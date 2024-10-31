<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';


include 'check_login.php'; // This will check if the user is logged in
include 'db_connection.php'; // Include your database connection


if (!isset($_SESSION['reset_email'])) {
    // Redirect to forgot password page if email is not set in session
    header("Location: forgot_password.php");
    exit();
}

// Function to send OTP email
function sendOtpEmail($email, $otp) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tourismwebiste@gmail.com'; // Your Gmail address
        $mail->Password = 'rvco kfzi dfns hrbk'; // Your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('tourismwebiste@gmail.com', 'Tourism Website');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Password Reset';
        $mail->Body = "<p>Your OTP for password reset is: <strong>$otp</strong></p>
                       <p>This OTP is valid for 10 minutes.</p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false; // Handle sending failure if needed
    }
}

// Handle OTP submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['otp'])) {
        $otp = $_POST['otp'];
        $email = $_SESSION['reset_email'];

        // Fetch the OTP and expiry from the database
        $stmt = $conn->prepare("SELECT reset_token_hash, reset_token_expires_at FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Check if OTP is correct and not expired
        if ($user && password_verify($otp, $user['reset_token_hash'])) {
            if (new DateTime() < new DateTime($user['reset_token_expires_at'])) {
                // OTP is correct and not expired, redirect to reset password page
                $_SESSION['otp_verified'] = true;
                header("Location: reset_password.php");
                exit();
            } else {
                $error = "OTP has expired.";
            }
        } else {
            $error = "Invalid OTP.";
        }
    } elseif (isset($_POST['resend_otp'])) {
        // Generate new OTP
        $otp = rand(100000, 999999);
        $hashed_otp = password_hash($otp, PASSWORD_DEFAULT);
        $email = $_SESSION['reset_email'];

        // Update OTP in the database
        $stmt = $conn->prepare("UPDATE users SET reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ?");
        $expiry_time = (new DateTime())->modify('+10 minutes')->format('Y-m-d H:i:s');
        $stmt->bind_param("ssi", $hashed_otp, $expiry_time, $email);
        $stmt->execute();

        // Send the new OTP via email
        if (sendOtpEmail($email, $otp)) {
            $success = "A new OTP has been sent to your email.";
        } else {
            $error = "Failed to send OTP. Please try again.";
        }
    }
}
?>

<!-- HTML part (form to input OTP) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

    <!--Icons and Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<!-- NAVIGATION BAR -->

<nav class="navbar navbar-expand-lg navbar-container fs-5">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <a class="navbar-brand" href="index.php">
                <img src="/images/logomount-removebg-preview.png" alt="Logo" width="100" height="50" class="d-inline-block align-text-top" />
            </a>
        </div>
        <div class="search-container d-flex">
            <span id="search-icon" class="material-symbols-outlined">search</span>
            <input type="text" placeholder="Search" class="search-bar" />
        </div>
  
         <div class="search-results mt-2"></div> <!-- This will hold the search results -->

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto">
                
            <!-- First Dropdown Link -->
            <li class="nav-item dropdown hideOnMobile">
                  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown1" role="button" data-bs-toggle="dropdown" aria-expanded="false"> Explore
                  </a>
                  <!-- Dropdown for Explore -->
                  <ul class="dropdown-menu" aria-labelledby="navbarDropdown1">
                      <li>
                          <a class="dropdown-item" href="systemfeatures/maps/maps.php"> <!-- Link to specific page -->
                              <span class="dd-icon material-symbols-outlined">nearby</span>
                              <span class="dd-text">Maps</span>
                          </a>
                      </li>
                      <li>
                          <a class="dropdown-item" href="community.html"> <!-- Link to specific page -->
                              <span class="dd-icon material-symbols-outlined">groups</span>
                              <span class="dd-text">Community</span>
                          </a>
                      </li>
                  </ul>
              </li>

                 <!-- Second Dropdown Link -->
              <li class="nav-item dropdown hideOnMobile">
                  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      Saved
                  </a>
                  <!-- Dropdown for Saved -->
                  <ul class="dropdown-menu" aria-labelledby="navbarDropdown2">
                      <li>
                          <a class="dropdown-item" href="bookmarks.html"> <!-- Link to specific page -->
                              <span class="dd-icon material-symbols-outlined">bookmarks</span>
                              <span class="dd-text">Bookmarks</span>
                          </a>
                      </li>
                      <li>
                          <a class="dropdown-item" href="favorites.html"> <!-- Link to specific page -->
                              <span class="dd-icon material-symbols-outlined">favorite</span>
                              <span class="dd-text">Favorites</span>
                          </a>
                      </li>
                  </ul>
              </li>


                <li class="nav-item nav-login hideOnMobile mx-5">
                    <?php if ($loginStatus): ?>
                        <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo htmlspecialchars(getUserImagePath()); ?>" alt="Profile Picture" width="40" height="40" class="rounded-circle">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li>
                                <a class="dropdown-item" href="profile.php">Settings</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="logout.php">Logout</a>
                            </li>
                        </ul>
                    <?php else: ?>
                        <a class="nav-link" href="login.php">Login</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="bg-image" style="background-image: url('/images/login-bg.jpg'); background-size: cover; background-position: center;">
    <div class="container-fluid">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-8 d-flex justify-content-center">
                <div class="form-container p-4 d-flex align-items-center">
                    <div class="row w-100">
                        <!-- Image Section -->
                        <div class="col-md-5 logo-container">
                            <img src="images/logomount-removebg-preview.png" alt="Verify OTP" class="img-fluid">
                        </div>
                        <!-- Form Section -->
                        <div class="col-md-7">
                            <h2 class="mb-4 text-center">Verify OTP</h2>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>
                            <?php if (isset($success)): ?>
                                <div class="alert alert-success"><?= $success ?></div>
                            <?php endif; ?>
                            <form action="verify_otp.php" method="POST">
                                <div class="mb-3">
                                    <label for="otp" class="form-label">Enter the OTP sent to your email:</label>
                                    <input type="text" name="otp" class="form-control" placeholder="OTP" required>
                                </div>
                                <button type="submit" class="btn btn-success w-100">Submit</button>
                            </form>
                            <form action="verify_otp.php" method="POST" class="mt-3">
                                <button type="submit" name="resend_otp" class="btn btn-secondary w-100">Resend OTP</button>
                            </form>
                            <p class="mt-3 text-center"><a href="forgot_password.php">Back to Forgot Password</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--FOOTER-->
<footer>
    <div class="container-fluid footer1">
      <div class="row p-5 text-center text-md-start">
        <div class="col-12 col-md-4 mb-4">
          <span class="ftr-icon material-symbols-outlined">photo_camera</span>
          <h3 class="mt-3 fs-2 footer-title">Share Your Journey</h3>
          <h3 class="mt-3 fs-4 footer1-des" style="text-align: justify;">Connect with fellow adventurers and share your experiences. Tag us in your photos to inspire others!</h3>
        </div>
        <div class="col-12 col-md-4 mb-4">
          <span class="ftr-icon material-symbols-outlined">landscape</span>
          <h3 class="mt-3 fs-2 footer-title">Adventure Awaits</h3>
          <h3 class="mt-3 fs-4 footer1-des" style="text-align: justify;">Every adventure brings a new experience. Discover breathtaking trails, hidden gems, and the beauty of nature with us.</h3>
        </div>
        <div class="col-12 col-md-4 mb-4">
          <span class="ftr-icon material-symbols-outlined">explore</span>
          <h3 class="mt-3 fs-2 footer-title">Explore Responsibly</h3>
          <h3 class="mt-3 fs-4 footer1-des" style="text-align: justify;"> We believe in responsible exploration. Follow our guidelines to leave minimal impact and preserve the beauty of nature.</h3>
        </div>
      </div>
    </div>
</footer>

<!--JQUERY-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
<script src="systemfeatures/search/search.js" defer></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
