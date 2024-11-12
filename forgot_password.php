<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

include 'check_login.php'; // This will check if the user is logged in
include 'db_connection.php'; // Include database connection




if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email exists, generate OTP
        $otp = rand(100000, 999999); // 6-digit OTP
        $expiry = date("Y-m-d H:i:s", time() + 60 * 10); // Expire in 10 minutes

        // Store OTP in the database
        $stmt = $conn->prepare("UPDATE users SET reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ?");
        $stmt->bind_param("sss", password_hash($otp, PASSWORD_DEFAULT), $expiry, $email);
        $stmt->execute();

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

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for Password Reset';
            $mail->Body = "<p>Your OTP for password reset is: <strong>$otp</strong></p>
                           <p>This OTP is valid for 10 minutes.</p>";

            $mail->send();

            // Store email in session for use in OTP verification
            $_SESSION['reset_email'] = $email;

            // Redirect to the OTP verification page
            header("Location: verify_otp.php");
            exit();
        } catch (Exception $e) {
            $error = "Failed to send OTP. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $error = "Email does not exist.";
    }
}
?>

<!-- HTML part (form to input email for password reset) -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

    <!--Icons and Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

     <!--SWEET ALERT CDN-->
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.2/dist/sweetalert2.all.min.js"></script>
     
     <!--SweetAlert CSS-->
     <link href=" https://cdn.jsdelivr.net/npm/sweetalert2@11.14.2/dist/sweetalert2.min.css" rel="stylesheet">

     <!-- Favicon -->
    <link rel="icon" href="images/logomount.png" type="image/png"/>

</head>

<body>

    <!-- NAVIGATION BAR -->
    <?php
    include_once 'check_login.php'; // This will check if the user is logged in
    include 'db_connection.php'; // Ensure this file contains your database connection code
    ?>

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
                                <a class="dropdown-item" id="communityLink" href="#" onclick="checkLogin()">
                                    <span class="dd-icon material-symbols-outlined">groups</span>
                                    <span class="dd-text">Community</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Bookmark Link without Dropdown -->
                    <li class="nav-item hideOnMobile">
                        <a class="nav-link" href="userfeatures/bookmarks/bookmarks.php" id="navbarDropdown2" role="button" aria-expanded="false">
                            Bookmarks
                        </a>
                    </li>


                    <!-- Profile Picture or Login Link -->
                    <li class="nav-item nav-login hideOnMobile">
                        <?php if ($loginStatus): ?>
                            <a class="nav-link dropdown-toggle profilecon" id="profileDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img class="profilepic d-none" src="<?php echo htmlspecialchars(getUserImagePath()); ?>" alt="Profile Picture" width="40" height="40" class="rounded-circle">
                                <span class="username"><?php echo htmlspecialchars(getUserName()); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                <li>
                                    <a class="dropdown-item dd-item-login dd-text" href="userfeatures/userprofile/profile.php">
                                        <span class="dd-icon material-symbols-outlined">settings</span>
                                        <span class="dd-text">Settings</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item dd-item-login dd-text" href="logout.php">
                                        <span class="dd-icon material-symbols-outlined">logout</span>
                                        <span class="dd-text">Logout</span>
                                    </a>
                                </li>
                            </ul>
                        <?php else: ?>
                            <a class="nav-link navlog" href="login.php">Login</a>
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
                            <div class="col-md-5 d-flex justify-content-center align-items-center">
                                <img src="images/logomount-removebg-preview.png" alt="Forgot Password" class="img-fluid" style="height: auto; width: 600px;">
                            </div>
                            <!-- Form Section -->
                            <div class="col-md-7">
                                <h2 class="mb-4 text-center">Forgot Password</h2>
                                <?php if (isset($error)): ?>
                                    <div class="alert alert-danger"><?= $error ?></div>
                                <?php endif; ?>
                                <form action="forgot_password.php" method="POST">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Enter your email:</label>
                                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">Submit</button>
                                </form>
                                <p class="mt-3 text-center"><a href="login.php">Back to Login</a></p>
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
    <script src="script.js"></script>

    <script>
        // Pass PHP variable to JavaScript
        const isLoggedIn = <?php echo json_encode($loginStatus); ?>;
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>