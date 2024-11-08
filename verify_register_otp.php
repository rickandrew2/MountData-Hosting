<?php

include 'check_login.php'; // This will check if the user is logged in
include 'db_connection.php'; // Ensure this file contains your database connection code


if (!isset($_SESSION['otp']) || !isset($_SESSION['temp_user'])) {
    // Redirect to registration page if session data is not set
    header("Location: register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_otp = $_POST['otp'];

    if ($entered_otp == $_SESSION['otp']) {
        // OTP is correct, insert the new user into the database
        $name = $_SESSION['temp_user']['name'];
        $email = $_SESSION['temp_user']['email'];
        $password = $_SESSION['temp_user']['password'];

        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            // Clear session data after successful registration
            unset($_SESSION['otp']);
            unset($_SESSION['temp_user']);
            $_SESSION['success'] = "Registration successful! You can now log in.";

            header("Location: login.php");
            exit();
        } else {
            $error = "Failed to register user.";
        }
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!-- HTML for OTP verification -->
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

    <!--SWEET ALERT CDN-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.2/dist/sweetalert2.all.min.js"></script>

    <!--SweetAlert CSS-->
    <link href=" https://cdn.jsdelivr.net/npm/sweetalert2@11.14.2/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" href="images/logomount.png" type="image/png" />

    <style>
        .bg-image {
            background-image: url('backgroundimage.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            margin-top: -8vh;
        }

        .form-container {
            display: flex;
            align-items: center;
            /* Center content vertically */
        }

        .form-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            /* Center align form content */
            text-align: center;
            /* Center text within form */
        }

        .img-fluid {
            max-width: 100%;
            /* Ensure the image does not exceed the container width */
            height: auto;
            /* Maintain aspect ratio */
            max-height: 300px;
            /* Set a maximum height to control image size */
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
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div class="col-md-8">
                    <div class="form-container p-4">
                        <div class="row w-100">
                            <div class="col-md-5 d-flex justify-content-center align-items-center">
                                <img src="/images/logomount-removebg-preview.png" alt="Logo" class="img-fluid login-img">
                            </div>
                            <div class="col-md-7 form-section">
                                <h2 class="mb-4">Verify OTP</h2>
                                <?php if (isset($error)): ?>
                                    <div class="alert alert-danger"><?= $error ?></div>
                                <?php endif; ?>
                                <form action="verify_register_otp.php" method="POST">
                                    <div class="mb-3">
                                        <label for="otp" class="form-label">Enter the OTP sent to your email:</label>
                                        <input type="text" name="otp" class="form-control" placeholder="OTP" required>
                                    </div>
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </form>
                                <div class="text-center mt-3">
                                    <p><a href="register.php">Back to Registration</a></p>
                                </div>
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
    <!--JQUERY-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="systemfeatures/search/search.js" defer></script>

    <script src="script.js"></script>

    <script>
        // Pass PHP variable to JavaScript
        const isLoggedIn = <?php echo json_encode($loginStatus); ?>;
    </script>

</body>

</html>