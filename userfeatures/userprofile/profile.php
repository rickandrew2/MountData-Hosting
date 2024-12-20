<?php

include_once('../../check_login.php'); // This will check if the user is logged in
include('../../db_connection.php'); // Include the database connection

// Ensure user is logged in and session variables are set
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

//debug mode flag at the top
$debug = false; // Set to false in production

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- My CSS -->
    <link rel="stylesheet" href="../../dist/css/profile.css" />

    <!-- Favicon -->
    <link rel="icon" href="../../images/logomount.png" type="image/png" />

    <!--SWEET ALERT CDN-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.2/dist/sweetalert2.all.min.js"></script>

    <!--SweetAlert CSS-->
    <link href=" https://cdn.jsdelivr.net/npm/sweetalert2@11.14.2/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

    <!--BootStrap JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>

    <!--Icons and Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Favicon -->
    <link rel="icon" href="images/logomount.png" type="image/png" />

    <title>PROFILE</title>
</head>

<body>

    <!-- NAVIGATION BAR -->

    <nav class="navbar navbar-expand-lg navbar-container fs-5">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a class="navbar-brand" href="../../index.php">
                    <img src="/images/logomount-removebg-preview.png" alt="Logo" width="100" height="50" class="d-inline-block align-text-top" />
                </a>
            </div>
            <div class="search-container d-flex">
                <span class="material-symbols-outlined">search</span>
                <input type="text" placeholder="Search" class="search-bar" />
            </div>
            <!-- Add the result container here -->
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
                                <a class="dropdown-item" href="../../../systemfeatures/maps/maps.php"> <!-- Link to specific page -->
                                    <span class="dd-icon material-symbols-outlined">nearby</span>
                                    <span class="dd-text">Maps</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="../../systemfeatures/community/community.php"> <!-- Link to specific page -->
                                    <span class="dd-icon material-symbols-outlined">groups</span>
                                    <span class="dd-text">Community</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Bookmark Link without Dropdown -->
                    <li class="nav-item hideOnMobile">
                        <a class="nav-link" href="../bookmarks/bookmarks.php" id="navbarDropdown2" role="button" aria-expanded="false">
                            Bookmarks
                        </a>
                    </li>


                    <!-- Profile Picture or Login Link -->
                    <li class="nav-item nav-login hideOnMobile">
                        <?php if ($loginStatus): ?>
                            <a class="nav-link profilecon" id="profileDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img class="profilepic d-none" src="<?php echo htmlspecialchars(getUserImagePath()); ?>" alt="Profile Picture" width="40" height="40" class="rounded-circle">
                                <span class="username"><?php echo htmlspecialchars(getUserName()); ?></span>
                                <?php
                                include '../../get_notification_count.php';
                                $unread_count = getUnreadNotificationCount($_SESSION['user_id']);
                                if ($unread_count > 0) {
                                    echo "<span class='profile-notification-count'>$unread_count</span>";
                                }
                                ?>
                            </a>
                            <ul class="dropdown-menu profile-dropdown dropdown-menu-end" aria-labelledby="profileDropdown">
                                <li>
                                    <a class="dropdown-item" href="../../userfeatures/userprofile/profile.php">
                                        <span class="material-symbols-outlined">settings</span>
                                        Settings
                                    </a>
                                </li>
                                <li>
                                    <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#notificationsModal">
                                        <span class="material-symbols-outlined">notifications</span>
                                        Notifications
                                        <?php
                                        if ($unread_count > 0) {
                                            echo "<span class='notification-count'>$unread_count</span>";
                                        }
                                        ?>
                                    </button>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="confirmLogout(event)">
                                        <span class="material-symbols-outlined">logout</span>
                                        Logout
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

    <!-- PROFILE -->
    <?php

    // Check if the user is logged in
    if ($loginStatus) {
        $user_id = $_SESSION['user_id']; // Get the user ID from the session

        if ($debug) {
            echo "Current user_id: " . $user_id . "<br>";
        }

        // Query to fetch user data
        $query = "SELECT `username`, `email`, `created_at`, `image_path` FROM `users` WHERE `user_id` = ?";

        // Prepare statement
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);

        // Execute the statement
        $stmt->execute();

        // Fetch the result
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            // Extract user data
            $username = htmlspecialchars($user['username']);
            $_SESSION['username'] = $username; // Store username in session
            $email = htmlspecialchars($user['email']);
            $created_at = date('F j, Y', strtotime($user['created_at']));
            $image_path = htmlspecialchars($user['image_path']);

            if ($debug) {
                error_log("Username from database: " . $username);
                error_log("Session username: " . $_SESSION['username']);
                echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px; border: 1px solid #ccc;'>";
                echo "Debug Information:<br>";
                echo "Username from database: " . $username . "<br>";
                echo "Email: " . $email . "<br>";
                echo "Created at: " . $created_at . "<br>";
                echo "Image path: " . $image_path . "<br>";
                echo "Session username: " . $_SESSION['username'] . "<br>";
                echo "</div>";
            }
        } else {
            if ($debug) {
                echo "<div style='background: #ffe0e0; padding: 10px; margin: 10px; border: 1px solid #ffcccc;'>";
                echo "Error: User not found in database for ID: " . $user_id . "<br>";
                echo "SQL Query: " . $query . "<br>";
                echo "</div>";
            }
            // Handle case when user not found
            $username = "Guest";
            $email = "Not available";
            $created_at = "N/A";
            $image_path = "images/default.jpg";
        }

        // Close the statement
        $stmt->close();
    } else {
        if ($debug) {
            echo "<div style='background: #ffe0e0; padding: 10px; margin: 10px; border: 1px solid #ffcccc;'>";
            echo "User not logged in<br>";
            echo "</div>";
        }
        // Handle case when not logged in
        $username = "Guest";
        $email = "Not available";
        $created_at = "N/A";
        $image_path = "images/default.jpg";
    }

    // Close the database connection
    $conn->close();
    ?>

    <!-- PROFILE -->
    <div class="profile-and-feed container-fluid px-3 px-md-5 mt-5">
        <div class="row g-4" id="profileContainer">
            <!-- Left column: Profile Section -->
            <div class="col-12 col-md-4 edit-profile">
                <div class="profile-container p-3">
                    <div class="profile-pic-with-edit d-flex align-items-center">
                        <img src="<?php echo getUserImagePath(); ?>" alt="Profile Picture" class="profile-pic" style="width: 60px; height: 60px; border-radius: 50%; background-color: gray;" onclick="document.getElementById('fileInput').click();">
                        <input type="file" id="fileInput" accept="image/*" style="display: none;" onchange="uploadImage(event)">
                        <div class="tooltip-container ms-auto mx-3">
                            <span class="material-symbols-outlined ms-auto pen-icon" id="editIcon" style="cursor: pointer;">edit</span>
                            <span class="tooltip-text">Edit Profile</span>
                        </div>
                    </div>
                    <div class="profile-info mt-3">
                        <h1><?php echo $username; ?></h1>
                        <h6><?php echo $email; ?></h6>
                        <h6 style="color: #8a8a8a">Member since <?php echo $created_at; ?></h6>
                    </div>
                </div>
            </div>

            <!-- Right column: Content Section -->
            <div class="col-12 col-md-8 feed-profile" style="height: auto; padding: 15px; margin-bottom: 20px;">
                <div class="content feed-container p-3" style="border-bottom: 1px solid #8a8a8a;">
                    <h3 class="mt-3" style="text-align: center;">Feed</h3>
                </div>
                <div class="feed" style="overflow-y: auto; max-height: 80vh;"> <!-- Set max-height on feed instead -->
                    <?php include 'fetch_feed.php'; ?>
                </div>
            </div>
        </div>

        <!-- Edit Profile Container -->
        <div class="edit-profile-container d-none container-fluid px-3" id="editProfileContainer" style="max-width: 800px; width: 90%; height: auto; margin: 50px auto 0;">
            <div class="row g-0">
                <div class="row p-3">
                    <div class="d-flex" style="border-bottom: 1px solid black;">
                        <h4 class="fs-5">Edit Profile</h4>
                        <span class="ms-auto material-symbols-outlined fs-2">landscape</span>
                    </div>
                    <!-- Profile Image Column -->
                    <div class="col-lg-3 col-md-12 text-center text-lg-start" style="background-color: rgb(255, 255, 255);">
                        <div class="p-3">
                            <img src="<?php echo getUserImagePath(); ?>" alt="Profile Picture" style="width: 100px; height: 100px; border-radius: 50%; background-color: gray;" onclick="document.getElementById('fileInput').click();">
                            <input type="file" id="fileInput" accept="image/*" style="display: none;" onchange="uploadImage(event)">
                        </div>
                    </div>
                    <!-- Form Column -->
                    <div class="col-lg-6 col-md-12 mt-3 px-4">
                        <form method="POST" action="edit_profile.php" class="w-100">
                            <div class="mb-3">
                                <label for="editName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="editName" name="editName"
                                    value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="editEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="editEmail" name="editEmail"
                                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="editContact" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="editContact" name="editContact"
                                    value="<?php echo getUserContactNumber(); ?>">
                                <small class="text-muted">Philippine mobile number format (e.g., 09xxxxxxxxx or +639xxxxxxxxx)</small>
                            </div>
                            <div class="mb-3">
                                <label for="editPassword" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="editPassword" name="editPassword" placeholder="Enter new password">
                                <small class="text-muted">Minimum 5 characters, 1 uppercase letter, and 1 symbol</small>
                            </div>

                            <!-- Hidden inputs -->
                            <input type="hidden" name="originalUsername" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
                            <input type="hidden" name="originalEmail" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                            <input type="hidden" name="originalContact" value="<?php echo getUserContactNumber(); ?>">

                            <!-- Button group -->
                            <div class="button-group">
                                <button type="submit" class="btn" style="background-color: green; color: white;">Save Changes</button>
                                <button type="button" class="btn btn-secondary" id="cancelEdit">Cancel</button>
                            </div>
                        </form>
                    </div>
                    <!-- Account Settings Column -->
                    <div class="col-lg-3 col-md-12 mt-3 account-settings px-4">
                        <h4>Account Settings</h4>
                        <button class="delete-account-btn w-100" id="deleteAccountBtn">
                            <span class="material-symbols-outlined">delete</span>
                            <span class="btn-text">Delete Account</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--FOOTER-->
    <footer>
        <div class="container-fluid footer1 mt-5">
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


    <!-- Bootstrap JS (required for functionality) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!--BOOTSTRAP JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!--OWN JS-->
    <script src="profile.js"></script>
    <script src="../../script.js"></script>
    <script src="../../assets/js/logout.js"></script>
    <script src="../../assets/js/notification.js"></script>
    <!--JQUERY-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../systemfeatures/search/search.js" defer></script>

    <!--NOTIFICATIONS-->
    <!-- Notifications Modal -->
    <div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="notificationsModalLabel">Notifications</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="notificationsContent">
                        <!-- Notifications will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>