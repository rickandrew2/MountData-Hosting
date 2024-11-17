<?php

// Include database connection
include '../../db_connection.php';
require '../../check_login.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- My CSS -->
    <link rel="stylesheet" href="../../dist/css/community.css" />

    <!-- Favicon -->
    <link rel="icon" href="../../images/logomount.png" type="image/png" />

    <!--SWEET ALERT CDN-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.2/dist/sweetalert2.all.min.js"></script>

    <!--SweetAlert CSS-->
    <link href=" https://cdn.jsdelivr.net/npm/sweetalert2@11.14.2/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

    <!-- Bootstrap JS -->

    <!--Icons and Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <title>Community</title>
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
                        <ul class="dropdown-menu" aria-labelledby="   navbarDropdown1">
                            <li>
                                <a class="dropdown-item" href="../maps/maps.php"> <!-- Link to specific page -->
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
                        <a class="nav-link" href="../../userfeatures/bookmarks/bookmarks.php" id="navbarDropdown2" role="button" aria-expanded="false">
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
                                    <a class="dropdown-item" href="logout.php">
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

    <div class="container p-3">
        <div class="row mx-1 align-items-start">
            <?php
            include('fetch_community.php');
            ?>
        </div> <!-- End of main content area -->

        <!-- Secondary column -->
        <div class="container second-column col-lg-4 col-md-5 col-12 mt-2 p-4">
            <h3 class="text-center mb-4">User Tips</h3>
            <p class="mt-3 text-center">Essential Tips for Outdoor Enthusiasts</p>
            <ul class="list-unstyled mt-3">
                <li class="tip-item">
                    <span class="material-symbols-outlined icon">local_drink</span>
                    <div class="tip-content">
                        <strong>Stay Hydrated:</strong> Always carry enough water, especially on hot days.
                    </div>
                </li>
                <li class="tip-item">
                    <span class="material-symbols-outlined icon">hiking</span>
                    <div class="tip-content">
                        <strong>Wear Proper Footwear:</strong> Invest in good hiking boots for support and traction.
                    </div>
                </li>
                <li class="tip-item">
                    <span class="material-symbols-outlined icon">remove_circle</span>
                    <div class="tip-content">
                        <strong>Leave No Trace:</strong> Pack out what you pack in to keep nature pristine.
                    </div>
                </li>
                <li class="tip-item">
                    <span class="material-symbols-outlined icon">cloud</span>
                    <div class="tip-content">
                        <strong>Check Weather Conditions:</strong> Be aware of the weather forecast before heading out.
                    </div>
                </li>
                <li class="tip-item">
                    <span class="material-symbols-outlined icon">pets</span>
                    <div class="tip-content">
                        <strong>Respect Wildlife:</strong> Observe animals from a distance and do not feed them.
                    </div>
                </li>
            </ul>

            <hr class="my-4" style="border-top: 2px solid white;">

            <h3 class="text-center mb-4">Nature Conservation</h3>
            <p class="mt-3 text-center">Help protect our beautiful landscapes by following these conservation tips:</p>
            <ul class="list-unstyled mt-2">
                <li class="tip-item">
                    <span class="material-symbols-outlined icon">straight</span>
                    <div class="tip-content">
                        <strong>Stay on Trails:</strong> Prevent soil erosion and protect plant life by sticking to marked paths.
                    </div>
                </li>
                <li class="tip-item">
                    <span class="material-symbols-outlined icon">check_circle</span>
                    <div class="tip-content">
                        <strong>Remove Invasive Species:</strong> Help maintain biodiversity by reporting and removing non-native plants.
                    </div>
                </li>
                <li class="tip-item">
                    <span class="material-symbols-outlined icon">cleaning_services</span>
                    <div class="tip-content">
                        <strong>Participate in Clean-ups:</strong> Join local conservation groups for trail maintenance and clean-up efforts.
                    </div>
                </li>
                <li class="tip-item">
                    <span class="material-symbols-outlined icon">volunteer_activism</span>
                    <div class="tip-content">
                        <strong>Support Conservation Organizations:</strong> Consider donating or volunteering to organizations that protect natural areas.
                    </div>
                </li>
            </ul>
        </div>
    </div>
    </div>


    <!-- Bootstrap CSS and JS (Include these if not already included) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!--JQUERY-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!--OWN JS-->
    <script src="community.js"></script>
    <script src="../../systemfeatures/search/search.js" defer></script>
    <script src="../../assets/js/notification.js"></script>

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