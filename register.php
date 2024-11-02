<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

include 'check_login.php'; // This will check if the user is logged in
include 'db_connection.php'; // Include your database connection



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email is already taken
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Email already exists
            $error = "This email is already taken. Please use another email.";
        } else {
            // Generate OTP
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['temp_user'] = [
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT) // Hash password for later use
            ];

            // Send OTP via email
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
                $mail->Subject = 'Registration Confirmation';
                $mail->Body = "<p>Your OTP for registration is: <strong>$otp</strong></p>
                               <p>This OTP is valid for 10 minutes.</p>";

                $mail->send();

                // Redirect to OTP verification page
                header("Location: verify_register_otp.php");
                exit();
            } catch (Exception $e) {
                $error = "Failed to send OTP. Error: {$e->getMessage()}";
            }
        }
    }
}
?>

<!-- HTML part -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

    <!--Icons and Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script>
        function togglePasswordVisibility(id) {
            var passwordInput = document.getElementById(id);
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        }
    </script>

    <style>
        .logo-container {
            height: 250px; /* Adjust height as needed */
            display : flex;
            justify-content: center;
            align-items: center;
        }

        .logo-container img {
            max-width: 125%;
            height: auto;
        }

        .form-container {
            min-height: 500px; /* Ensure the form container has a minimum height */
        }
    </style>

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
            <span class="material-symbols-outlined">search</span>
            <input type="text" placeholder="Search" class="search-bar" />
        </div>
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

              <!-- Bookmark Link without Dropdown -->
            <li class="nav-item hideOnMobile">
                <a class="nav-link" href="userfeatures/userprofile/bookmarks.php" id="navbarDropdown2" role="button" aria-expanded="false">
                    Bookmarks
                </a>
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
                <div class="form-container p-4 d-flex flex-column align-items-center mt-5 mb-5">
                    <div class="row w-100 align-items-center">
                        <div class="col-md-5 logo-container"> <!-- Centering the logo with height -->
                            <img src="images/logomount-removebg-preview.png" alt="logo" class="img-fluid"> <!-- Centered logo -->
                        </div>
                        <div class="col-md-7">
                            <h2 class="mb-4 text-center">Create your free account</h2>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>
                            <form action="register.php" method="POST">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control custom-input" placeholder="Your Name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control custom-input" placeholder="Email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password" class="form-control custom-input" placeholder="Password" required>
                                        <button type="button" class="btn btn-outline-secondary" style="height: 100%;" onclick="togglePasswordVisibility('password')">👁</button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <div class="input-group">
                                        <input type="password" name="confirm_password" id="confirm_password" class="form-control custom-input" placeholder="Confirm Password" required>
                                        <button type="button" class="btn btn-outline-secondary" style="height: 100%;" onclick="togglePasswordVisibility('confirm_password')">👁</button>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success w-100">Register</button>
                            </form>
                            <p class="mt-3 text-center"><a href="login.php">Already have an account? Log in</a></p>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
