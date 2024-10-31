<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['otp_verified'])) {
    // Redirect to OTP verification if not verified
    header("Location: verify_otp.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $email = $_SESSION['reset_email'];

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update the password in the database
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);

    if ($stmt->execute()) {
        // Clear the session after password is changed
        unset($_SESSION['reset_email']);
        unset($_SESSION['otp_verified']);
        $success = "Your password has been updated successfully!";
    } else {
        $error = "Failed to update password.";
    }
}
?>

<!-- HTML part (form to reset password) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

     <!--Icons and Fonts-->
     <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .bg-image {
            background-image: url('/images/contact.png');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            margin-top: -7vh;
        }

        .img-fluid {
            max-width: 100%; /* Ensure the image does not exceed the container width */
            height: auto;    /* Maintain aspect ratio */
            max-height: 300px; /* Set a maximum height to control image size */
        }

        .form-container {
            display: flex;
            align-items: center; /* Center content vertically */
        }

        .form-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center; /* Center align form content */
            text-align: center; /* Center text within form */
        }
    </style>
</head>
<body>

<!-- NAVIGATION BAR -->

<?php
include 'check_login.php'; // This will check if the user is logged in
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



<div class="bg-image">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-8">
                <div class="form-container p-4">
                    <div class="row w-100">
                        <div class="col-md-5 d-flex justify-content-center align-items-center">
                            <img src="loginimage.jpg" alt="Reset Password" class="img-fluid">
                        </div>
                        <div class="col-md-7 form-section">
                            <h2 class="mb-4">Reset Password</h2>
                            <?php if (isset($success)): ?>
                                <div class="alert alert-success"><?= $success ?></div>
                            <?php elseif (isset($error)): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>
                            <form action="reset_password.php" method="POST">
                                <div class="mb-3 input-group">
                                    <input type="password" name="new_password" id="new_password" class="form-control" placeholder="New Password" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility()">👁</button>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                            </form>
                            <div class="text-center mt-3">
                                <p><a href="login.php">Back to Login</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById('new_password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        }
    </script>
</body>
</html>
