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
            $error = "That email is already taken. Please use another email.";
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

                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];


                $mail->isHTML(true);
                $mail->Subject = 'Registration Confirmation';

                // Styled HTML email body with updated verification link
                $mail->Body = "<html>
                
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
            text-align: center; /* Center the content */
        }
        .logo {
            width: 300px; /* Increased size of the logo */
            margin-bottom: 20px;
        }
        h2 {
            color: #28a745; /* Green color */
            font-size: 24px;
            margin-bottom: 10px;
        }
        .otp {
            font-size: 30px;
            font-weight: bold;
            color: #28a745; /* Green color */
            letter-spacing: 2px;
            margin-bottom: 15px;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
            color: #333;
        }
        .footer {
            font-size: 12px;
            color: #888;
            margin-top: 30px;
            text-align: center;
        }
        .cta {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 30px;
            background-color: #28a745; /* Green button */
            color: white; /* White text for the button */
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            text-transform: uppercase;
        }
        .cta:hover {
            background-color: #218838; /* Darker green on hover */
        }
    </style>
</head>

<body>
    <div class='container'>
        <img src='cid:logomount' alt='Tourism Website Logo' class='logo'>
        <h2>Registration Confirmation</h2>
        <p>Your OTP for registration is:</p>
        <p class='otp'>$otp</p>
        <p>This OTP is valid for 10 minutes.</p>
        <a href='http://localhost:3000/verify_register_otp.php' class='cta' style='color: white;'>Verify OTP</a>
    </div>
    <div class='footer'>
        <p>If you did not request this, please ignore this email.</p>
    </div>
</body>
</html>";


                // Embed the updated logo image (make sure the path is correct)
                $mail->addEmbeddedImage('images/logomount-removebg-preview.png', 'logomount', 'logomount-removebg-preview.png');

                $mail->send();

                // Redirect to the updated OTP verification page
                header("Location: http://localhost:3000/verify_register_otp.php");
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

    <!--SWEET ALERT CDN-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.2/dist/sweetalert2.all.min.js"></script>

    <!--SweetAlert CSS-->
    <link href=" https://cdn.jsdelivr.net/npm/sweetalert2@11.14.2/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" href="images/logomount.png" type="image/png" />

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
            height: 250px;
            /* Adjust height as needed */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo-container img {
            max-width: 125%;
            height: auto;
        }

        .form-container {
            min-height: 500px;
            /* Ensure the form container has a minimum height */
        }
    </style>

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
                                        <input type="text" 
                                               name="name" 
                                               class="form-control custom-input" 
                                               placeholder="Your Name" 
                                               pattern="[A-Za-z\s]+"
                                               title="Name can only contain letters and spaces"
                                               minlength="2"
                                               required>
                                        <small class="text-muted">Name must contain only letters and spaces</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control custom-input" placeholder="Email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="contact_number" class="form-label">Contact Number</label>
                                        <input type="tel" name="contact_number" class="form-control custom-input" 
                                               pattern="^(09|\+639)\d{9}$"
                                               placeholder="Enter Philippine mobile number (e.g., 09123456789)"
                                               title="Please enter a valid Philippine mobile number starting with 09 or +639" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group">
                                            <input type="password" name="password" id="password" class="form-control custom-input"
                                                placeholder="Min 5 chars, 1 uppercase & 1 symbol"
                                                pattern="^(?=.*[A-Z])(?=.*[!@#$%^&*?])[A-Za-z\d!@#$%^&*?]{5,}$"
                                                title="Password must be at least 5 characters and contain at least one uppercase letter and one symbol"
                                                required>
                                            <button type="button" class="btn btn-outline-secondary" style="height: 100%;" onclick="togglePasswordVisibility('password')">👁</button>
                                        </div>
                                        <small class="text-muted">Password must be at least 5 characters with 1 uppercase letter and 1 symbol</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm Password</label>
                                        <div class="input-group">
                                            <input type="password" name="confirm_password" id="confirm_password" class="form-control custom-input" placeholder="Confirm Password" required>
                                            <button type="button" class="btn btn-outline-secondary" style="height: 100%;" onclick="togglePasswordVisibility('confirm_password')">👁</button>
                                        </div>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input form-check-input-sm" id="terms" name="terms" required style="width: 15px; height: 15px;">
                                        <label class="form-check-label mx-2" for="terms">
                                            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a> and <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a>
                                        </label>
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

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Add your terms and conditions content here -->
                    <p>By using our service, you agree to follow these terms and conditions...</p>
                    <!-- Add more terms content as needed -->
                </div>
            </div>
        </div>
    </div>

    <!-- Privacy Policy Modal -->
    <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="privacyModalLabel">Privacy Policy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Add your privacy policy content here -->
                    <p>Your privacy is important to us. This policy explains how we collect and use your data...</p>
                    <!-- Add more privacy policy content as needed -->
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
    <script src="script.js"></script>

    <script>
        // Pass PHP variable to JavaScript
        const isLoggedIn = <?php echo json_encode($loginStatus); ?>;
    </script>
</body>

</html>