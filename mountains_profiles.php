<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- My CSS -->
    <link rel="stylesheet" href="dist/css/mountains_profiles.css" />

    <!-- Favicon -->
    <link rel="icon" href="images/logomount.png" type="image/png" />

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

    <title></title>

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
                                <a class="dropdown-item" href="systemfeatures/community/community.php"> <!-- Link to specific page -->
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

    <!-- Lightbox container -->
    <div id="lightbox" class="lightbox" onclick="closeLightbox()">
        <span class="close">&times;</span>
        <!-- Blurred background image -->
        <div id="lightbox-bg" class="lightbox-bg"></div>
        <!-- Full-screen image -->
        <img id="lightbox-img" class="lightbox-content">
    </div>

    <!-- MOUNTAIN CONTAINER -->

    <div class="col-lg-9 mountain-container mt-5" style="min-height: 100vh; padding-bottom: 10vh; margin-bottom: 50px;">
        <?php
        include_once 'check_login.php'; // Ensure login is checked
        include 'fetch_mountains_data.php'; // Include the new data-fetching file

        // Check if the mountain_id is provided in the URL
        if (isset($_GET['mountain_id']) && is_numeric($_GET['mountain_id'])) {
            $mountain_id = intval($_GET['mountain_id']); // Get the mountain ID from the URL and sanitize it
            echo "<script>var mountainId = $mountain_id;</script>";

            // Fetch mountain profile data
            $profileData = getMountainProfile($conn, $mountain_id);

            if ($profileData) {
                // Separate data for easy access
                $mountainData = $profileData['mountainData'];
                $ratingsData = $profileData['ratingsData'];

                // Extract mountain details
                $name = htmlspecialchars($mountainData['name']);
                $location = htmlspecialchars($mountainData['location']);
                $elevation = htmlspecialchars($mountainData['elevation']);
                $mountain_image = htmlspecialchars($mountainData['mountain_image']);
                $difficulty_level = htmlspecialchars($mountainData['difficulty_level']);
                $description = htmlspecialchars($mountainData['description']);
                $latitude = htmlspecialchars($mountainData['latitude']);
                $longitude = htmlspecialchars($mountainData['longitude']);
                $contact_numbers_array = explode(',', htmlspecialchars($mountainData['contact_number']));

                // Extract ratings details
                $averageRating = $ratingsData['averageRating'];
                $totalReviews = $ratingsData['totalReviews'];

                // Set the document title dynamically
                echo "<script>document.title = 'Mount $name';</script>";
                echo "<script>var mountainLatitude = $latitude; var mountainLongitude = $longitude;</script>";

                // Output HTML for the specific mountain
                echo "
                <div class='mountain-pic-header container m-0' style='position: relative; border-radius: 10px 10px 0 0; background-image: url(\"$mountain_image\"); background-size: cover; background-position: center; height: 300px;' onclick=\"openLightbox('$mountain_image')\">
                    <div class='mountain-content mb-3' style='position: absolute; bottom: 0; width: 100%; padding: 10px; color: white; border-radius: 0 0 10px 10px;'>
                        <div class='mountain-name-wrapper'>
                            <h3 class='mountain-name' style='align-self: flex-end; color: white;'>Mount $name</h3>
                        </div>
                        <div class='difficulty-n-rating d-flex'>
                            <h4>" . htmlspecialchars($difficulty_level) . " - <i class='fas fa-star fs-6' style='color: #32CD32'></i> $averageRating ($totalReviews)</h4>
                        </div>
                        <h5 style='text-decoration: underline; text-decoration-thickness: 2px; text-underline-offset: 4px;'>$location</h5>
                    </div>
                </div>

                <div class='d-flex justify-content-between mx-3'>
                    <div class='length mt-3 fs-3'>
                        <p class='info-label' style='line-height: 1.2; margin-bottom: 0; color: gray'>Difficulty</p>
                        <p class='info-value' style='line-height: 1.2; margin: 0; color: green;'>$difficulty_level</p>
                    </div>
                    <div class='elevation mt-3 fs-3'>
                        <p class='info-label' style='line-height: 1.2; margin-bottom: 0; color: gray;'>Elevation</p>
                        <p class='info-value' style='line-height: 1.2; margin: 0; color: green;'>$elevation m</p>
                    </div>
                    <div class='bookmark mt-3'>
                        <button class='bookmark-button' data-mountain-id='$mountain_id' aria-label='Toggle bookmark' onclick='toggleBookmark(this)'>
                            <span class='material-symbols-outlined'>bookmark_add</span>
                        </button>
                    </div>
                </div>

                <div class='description m-3'>
                    <p>$description</p>
                </div>
                ";

                // Display weather information for logged-in users
                if ($loginStatus) {
                    echo "
                    <div class='weather-info'></div>
                    <div class='container forecast-info overflow-auto'></div>
                    ";
                } else {
                    echo "<p class='mx-3'>Please log in to view weather information.</p>";
                }

                // Display contact numbers
                if (!empty($contact_numbers_array)) {
                    echo "<div class='contact-numbers m-3'><h5>Emergency Contact Numbers:</h5><ul class='contact-list'>";
                    foreach ($contact_numbers_array as $number) {
                        echo "<li><i class='fa-solid fa-phone'></i>" . htmlspecialchars(trim($number)) . "</li>";
                    }
                    echo "</ul></div>";
                }
            } else {
                echo "<p>Mountain not found.</p>";
            }
        } else {
            echo "<p>No valid mountain ID provided.</p>";
        }
        ?>


        <div class="tab-buttons d-flex justify-content-around section" style="cursor: pointer;">
            <div class="reviews-btn active" onclick="showSection('reviews')">
                <h5 style="text-align: center;">Reviews</h5>
            </div>
            <div class="photos-btn" onclick="showSection('photos')">
                <h5 style="text-align: center;">Photos</h5>
            </div>
        </div>

        <div class="content m-3 rating-container">

            <div class="reviews-content" id="reviews" style="display: block;"> <!-- Default visible -->
                <div class="row mb-3">
                    <div class="ratings col-lg-4 col-md-6">
                        <?php
                        require 'userfeatures/reviews/fetch_reviews.php';
                        // Fetch counts of ratings
                        $ratings = fetchRatings($conn, $mountain_id);
                        displayRatings($ratings);
                        $averageRating = calculateAverageRating($ratings);
                        ?>
                    </div>
                    <div class="total-rating col-lg-5 col-md-6" style="margin-top: -20px;">
                        <h1 style="font-size: 5rem; color: #006400;"><?php echo $averageRating; ?></h1>
                        <div><?php displayAverageStars($averageRating); ?></div>
                        <h5>(<?php echo $ratings['totalReviews']; ?>) <?php echo ($ratings['totalReviews'] == 1) ? 'Review' : 'Reviews'; ?></h5>
                    </div>
                    <div class="write-review col-md-12 col-lg-3 mb-1" style="text-align: center;">
                        <h5 id="writeReviewHeader" style="cursor: pointer;">Write Review</h5>
                    </div>
                </div>

                <div class="search-reviews" style="display: flex; align-items: center; position: relative; width: 100%;">
                    <i class="fas fa-search" style="position: absolute; left: 10px; color: black"></i>
                    <input type="text" placeholder="Search reviews..." class="search-bar" style="font-size: 1.2rem;">
                    <div class="filter-icon" style="position: absolute; right: 10px;">
                        <i class="fas fa-filter" style="color: black;"></i>
                    </div>
                </div>

                <!-- REVIEW COMMENTS SECTION -->
                <?php
                $totalReviews = fetchTotalReviews($conn, $mountain_id);
                $currentPage = getCurrentPage();
                $reviewsPerPage = 5;
                $totalPages = ceil($totalReviews / $reviewsPerPage);
                $offset = ($currentPage - 1) * $reviewsPerPage;

                if ($loginStatus) {
                    $reviews = fetchReviews($conn, $mountain_id, $reviewsPerPage, $offset);
                    displayReviews($reviews);
                } else {
                    displayLoginPrompt();
                }

                // Pagination
                displayPagination($currentPage, $totalPages, $mountain_id);

                // Close connection
                $conn->close();
                ?>
            </div>

            <div class="photos-content row" id="photos" style="display: none;">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="photos-details mx-3">
                            <h4>Photos of This Trail</h4>
                            <h5>Photos help others preview the trail. Upload photos about this trail to inspire others.</h5>
                        </div>
                        <div class="sort-by-container m-3">
                            <label for="sortBy">Sort by:</label>
                            <select id="sortBy">
                                <option value="all">All Trails</option>
                                <option value="popular">Most Popular</option>
                                <option value="recent">Most Recent</option>
                            </select>
                        </div>
                    </div>
                    <!-- Upload photos button with responsive margins -->
                    <div class="upload-photos col-12 col-md-12 col-lg-3" style="text-align: center;">
                        <h5 class="upload-photos-btn mt-4 mt-lg-0 mx-3" id="uploadBtn">Upload photos</h5>
                    </div>
                </div>
                <div class="container">
                    <hr>
                </div>
                <div>
                    <?php include 'userfeatures/reviews/fetch_upload_photos.php'; ?>
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


            <!-- Optional JavaScript; choose one of the two! -->
            <script src="mountains_profiles.js"></script>
            <!-- Option 1: Bootstrap Bundle with Popper -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

            <!--JQUERY-->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

            <script src="systemfeatures/search/search.js" defer></script>
            <script src="weather.js"></script>
            <script>
                const isLoggedIn = <?php echo json_encode($loginStatus); ?>; // Pass the PHP variable to JS
            </script>

</body>

</html>