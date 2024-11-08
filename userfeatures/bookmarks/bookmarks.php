<?php
// Move the login check and database connection to the top of the file
include_once('../../check_login.php'); // This will check if the user is logged in
include('../../db_connection.php'); // Include the database connection
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- My CSS -->
    <link rel="stylesheet" href="../../dist/css/maps.css" />

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

    <!-- Favicon -->
    <link rel="icon" href="../../images/logomount.png" type="image/png" />

    <title>Bookmarks</title>
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
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown1">
                            <li>
                                <a class="dropdown-item" href="../../systemfeatures/maps/maps.php"> <!-- Link to specific page -->
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
                        <a class="nav-link" href="bookmarks.php" id="navbarDropdown2" role="button" aria-expanded="false">
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
                                    <a class="dropdown-item dd-item-login dd-text" href="../../userfeatures/userprofile/profile.php">
                                        <span class="dd-icon material-symbols-outlined">settings</span>
                                        <span class="dd-text">Settings</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item dd-item-login dd-text" href="../../logout.php">
                                        <span class="dd-icon material-symbols-outlined">logout</span>
                                        <span class="dd-text">Logout</span>
                                    </a>
                                </li>
                            </ul>
                        <?php else: ?>
                            <a class="nav-link navlog" href="../../login.php">Login</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!--MOUNTAINS WITH MAPS-->

    <div class="container-fluid mt-3">
        <div class="filter-row mt-2 mb-2">
            <div class="sort-by-row container-fluid d-flex position-relative">
                <h5 class="mx-2 p- mt-2" style="flex-shrink: 0;">Filter by:</h5>

                <!-- Elevation Dropdown -->
                <div class="dropdown-container mx-2 position-relative" style="flex-shrink: 0;">
                    <h5 class="p-1 d-flex align-items-center justify-content-center" style="border: solid 1px green; border-radius: 10px; cursor: pointer;" onclick="toggleDropdown('elevationDropdown')">
                        Elevation <span class="material-symbols-outlined ms-2">keyboard_arrow_down</span>
                    </h5>
                    <div id="elevationDropdown" class="dropdown-content" style="display:none; position: absolute; top: 100%; left: 0; background-color: white; border: solid 1px green; padding: 10px; border-radius: 10px; z-index: 10; width: 300px;">
                        <label for="minElevation">Min Elevation:</label>
                        <input type="number" id="minElevation" class="form-control mb-2" placeholder="Min">

                        <label for="maxElevation">Max Elevation:</label>
                        <input type="number" id="maxElevation" class="form-control mb-3" placeholder="Max">

                        <div class="border-top" style="border-top: solid black 1px; margin-top: 10px; padding-top: 10px;">
                            <div class="d-flex justify-content-between">
                                <button class="btn" onclick="clearElevation()">Clear</button>
                                <button class="btn btn-success" onclick="filterMountains()">See Trails</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Difficulty Level Dropdown -->
                <div class="dropdown-container mx-2 position-relative" style="flex-shrink: 0;">
                    <h5 class="p-1 d-flex align-items-center justify-content-center" onclick="toggleDropdown('difficultyDropdown')" style="border: solid 1px green; border-radius: 10px; cursor: pointer;">
                        Difficulty Level <span class="material-symbols-outlined">keyboard_arrow_down</span>
                    </h5>
                    <div id="difficultyDropdown" class="dropdown-content" style="display:none; position: absolute; top: 100%; left: 0; background-color: white; border: solid 1px green; padding: 10px; border-radius: 10px; z-index: 10; width: 300px;">
                        <label class="d-block fs-5"><input type="radio" name="difficulty" value="easy"> Easy</label>
                        <label class="d-block fs-5"><input type="radio" name="difficulty" value="moderate"> Moderate</label>
                        <label class="d-block fs-5"><input type="radio" name="difficulty" value="challenging"> Challenging</label>

                        <div class="border-top" style="border-top: solid black 1px; margin-top: 10px; padding-top: 10px;">
                            <div class="d-flex justify-content-between">
                                <button class="btn" onclick="clearDifficulty()">Clear</button>
                                <button class="btn btn-success" onclick="filterMountains()">See Trails</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bookmark Date Dropdown -->
                <div class="dropdown-container mx-2 position-relative" style="flex-shrink: 0;">
                    <h5 class="p-1 d-flex align-items-center justify-content-center" onclick="toggleDropdown('bookmarkDateDropdown')" style="border: solid 1px green; border-radius: 10px; cursor: pointer;">
                        Bookmark Date <span class="material-symbols-outlined">keyboard_arrow_down</span>
                    </h5>
                    <div id="bookmarkDateDropdown" class="dropdown-content" style="display:none; position: absolute; top: 100%; left: 0; background-color: white; border: solid 1px green; padding: 10px; border-radius: 10px; z-index: 10; width: 300px;">
                        <label class="d-block fs-5"><input type="radio" name="bookmarkDate" value="last7days"> Last 7 Days</label>
                        <label class="d-block fs-5"><input type="radio" name="bookmarkDate" value="last30days"> Last 30 Days</label>
                        <label class="d-block fs-5"><input type="radio" name="bookmarkDate" value="last90days"> Last 90 Days</label>
                        <div class="border-top" style="border-top: solid black 1px; margin-top: 10px; padding-top: 10px;">
                            <div class="d-flex justify-content-between">
                                <button class="btn" onclick="clearBookmarkDate()">Clear</button>
                                <button class="btn btn-success" onclick="filterMountains()">See Trails</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-3">
            <!-- Mobile Filter Button -->
            <div class="d-flex justify-content-center mb-3 d-md-none">
                <button class="btn btn-success filters d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <span class="material-symbols-outlined me-2">filter_alt</span>
                    <span>Filter Trails</span>
                </button>
            </div>
        </div>

        <!-- Filter Modal -->
        <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filterModalLabel">Filter Options</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="minElevation" class="form-label">Min Elevation:</label>
                            <input type="number" id="minElevation" class="form-control" placeholder="Min">
                        </div>
                        <div class="mb-3">
                            <label for="maxElevation" class="form-label">Max Elevation:</label>
                            <input type="number" id="maxElevation" class="form-control" placeholder="Max">
                        </div>
                        <div class="mb-3">
                            <h6>Difficulty Level:</h6>
                            <label class="d-block"><input type="radio" name="difficulty" value="easy"> Easy</label>
                            <label class="d-block"><input type="radio" name="difficulty" value="moderate"> Moderate</label>
                            <label class="d-block"><input type="radio" name="difficulty" value="challenging"> Challenging</label>
                        </div>
                        <div class="mb-3">
                            <h6>Bookmark Date: </h6>
                            <label class="d-block"><input type="radio" name="bookmarkDate" value="last7days"> Last 7 Days</label>
                            <label class="d-block"><input type="radio" name="bookmarkDate" value="last30days"> Last 30 Days</label>
                            <label class="d-block"><input type="radio" name="bookmarkDate" value="last90days"> Last 90 Days</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" onclick="filterMountains()">Apply Filters</button>
                    </div>
                </div>
            </div>
        </div>



        <div class="row">
            <div class="col-md-3 mountain-column" id="mountainColumn">
                <h2 class="mountain-heading mb-3" style="text-align: center;">Mountains
                    <span class="material-symbols-outlined icon-landscape"> landscape </span>
                </h2>
                <ul id="mountainList" class="list-group">
                    <?php include 'fetch_bookmarks.php'; ?>
                </ul>
            </div>
            <div class="col-md-9 map-container d-md-block" id="mapColumn">
                <div id="map" style="width: 100%; height: 100%;"></div> <!-- Keep original map -->
            </div>
        </div>

        <!-- Floating Map Button (show map in modal) -->
        <div class="floating-map-button d-md-none" id="showMapButton" onclick="openMapModal()">
            <span class="material-symbols-outlined">map</span> Map
        </div>

        <!-- Modal for the Map -->
        <div id="mapModal" class="modal-map">
            <div class="modal-content">
                <span class="close" onclick="closeMapModal()">&times;</span>
                <!-- Placeholder for the message -->
                <h3 class="map-label">Mountain Location Map</h3> 
                <div id="modalMapMessage"></div>
                <div id="modalMap" style="width: 100%; height: 400px;"></div>
            </div>
        </div>

        <!-- Floating Mountain Button (go back to mountain view) -->
        <div class="floating-map-button d-md-none" id="showMountainButton" style="display: none;" onclick="closeMapModal()">
            <span class="material-symbols-outlined">landscape</span> Mountains
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

        <!-- Google Maps JavaScript API -->
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqm58SgYCVN-CdOxefv0BPG_PTJ75yINM&callback=initMap" async defer></script>

        <!--OWN JS-->
        <script src="profiles.js"></script>
        <script src="bookmarks.js"></script>
        <script src="script.js"></script>

        <script src="../../systemfeatures/search/search.js"></script>

        <!--BOOTSTRAP JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

        <!--JQUERY-->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="../../systemfeatures/search/search.js" defer></script>

        <script>
            const isLoggedIn = <?php echo json_encode(isset($_SESSION['user_id'])); ?>; // true or false based on session
            initModalMap(isLoggedIn);
        </script>

</body>

</html>