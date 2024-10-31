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

    <title>Document</title>
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
                                <a class="dropdown-item" href="../maps/maps.php"> <!-- Link to specific page -->
                                    <span class="dd-icon material-symbols-outlined">nearby</span>
                                    <span class="dd-text">Maps</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="community.php"> <!-- Link to specific page -->
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

    <div class="container p-3">
        <div class="row mx-1 align-items-start">
            <?php

           // Prepare the query to fetch reviews, user image path, username, mountain name, location, difficulty level, elevation gain, and tags
            $sql = "SELECT r.review_id, r.user_id, r.mountain_id, r.rating, r.comment, r.review_date, r.review_photo, r.like_count, 
            r.tags, u.image_path, u.username, m.name, m.location, m.difficulty_level, m.elevation 
            FROM reviews r 
            JOIN users u ON r.user_id = u.user_id 
            JOIN mountains m ON r.mountain_id = m.mountain_id
            ORDER BY r.review_date DESC"; // Sort by review_date in descending order


            // Execute the query
            $result = $conn->query($sql);

            // Check if there are results
            if ($result->num_rows > 0):
            ?>

                <!-- Main content area with the heading outside of the loop -->
                <div class="container main-content col-lg-7 col-md-7 col-12 mt-2 p-4" style="height: auto;">
                    <h2 class="text-center border-bottom pb-2">Latest Nearby</h2>
                        <div class="search-container">
                            <input type="text" class="search-input" placeholder="Search...(by #tags or name of mountains)" />
                            <span class="material-symbols-outlined search-icon">search</span> <!-- Updated search icon -->
                        </div>

                    <?php
                        while ($row = $result->fetch_assoc()):
                            // Retrieve each field
                            $reviewId = $row['review_id'];
                            $userId = $row['user_id'];
                            $mountainId = $row['mountain_id'];
                            $rating = $row['rating'];
                            $comment = $row['comment'];
                            $reviewDate = date("M d", strtotime($row['review_date']));
                            $tags = $row['tags']; // Retrieve tags
                            // Convert tags to an array if they are stored as a comma-separated string
                            $tagArray = array_filter(explode(',', $tags)); // Split by delimiter (e.g., comma)


                            // Define the base path for review images
                            $baseReviewImagePath = '../../userfeatures/reviews/'; // Adjust the path as needed

                            // Retrieve review photo with updated directory
                            $reviewPhotos = array_filter(explode(',', $row['review_photo'])); // Split by delimiter (e.g., comma)

                            // Check if there are no photos
                            if (empty($reviewPhotos)) {
                                continue; // Skip this review if there's no image
                            }

                            $likeCount = $row['like_count'];
                            $profilePicture = htmlspecialchars($row['image_path']); // Get the user's profile picture
                            $username = htmlspecialchars($row['username']); // Get the username
                            $mountainName = htmlspecialchars($row['name']); // Get the mountain name
                            $mountainLocation = htmlspecialchars($row['location']); // Get the mountain location
                            $difficultyLevel = htmlspecialchars($row['difficulty_level']); // Get the difficulty level
                            $elevation = htmlspecialchars($row['elevation']); // Get the elevation gain

                    ?>

                        <!-- Community section -->
                        <div class="community-section">
                            <!-- User info row -->
                            <div class="row align-items-center py-2">
                                <div class="col-auto">
                                    <!-- Profile Picture -->
                                    <img src="<?= $profilePicture ?: '/images/profile_images/default.jpg'; ?>" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px;">
                                </div>
                                <div class="col">
                                    <!-- Name and Date Section -->
                                    <div class="d-flex flex-column">
                                        <h6 class="m-0"><?= $username; ?></h6> <!-- Display username -->
                                        <small class="text-muted"><?= $reviewDate; ?> &bull; <span class="material-symbols-outlined" style="font-size: 18px;">hiking</span></small>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <!-- Options Button -->
                                    <button class="btn" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="border: none; background: none; font-size: 1.5rem;">
                                        &#x2026;
                                    </button>
                                </div>
                            </div>

                            <!-- Carousel Container for Mountain Images -->
                            <div class="row">
                                <div class="col">
                                    <?php if (count($reviewPhotos) > 1): // Check if there are multiple images 
                                    ?>
                                        <div id="hikeCarousel<?= $reviewId; ?>" class="carousel slide mt-2" data-bs-ride="carousel">
                                            <div class="carousel-inner">
                                                <?php foreach ($reviewPhotos as $index => $photo): ?>
                                                    <div class="carousel-item <?= $index === 0 ? 'active' : ''; ?>">
                                                        <div class="img-container" style="width: 100%; height: 500px; overflow: hidden;">
                                                            <img src="<?= htmlspecialchars($baseReviewImagePath . trim($photo)); ?>"
                                                                class="d-block rounded"
                                                                alt="Mountain Image <?= $index + 1; ?>"
                                                                style="width: 100%; height: 100%; object-fit: cover;">
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>

                                            <!-- Carousel Controls (Arrows) -->
                                            <div class="carousel-container">
                                                <button class="carousel-control-prev" type="button" data-bs-target="#hikeCarousel<?= $reviewId; ?>" data-bs-slide="prev">
                                                    <span class="material-symbols-outlined" aria-hidden="true">arrow_left_alt</span>
                                                    <span class="visually-hidden">Previous</span>
                                                </button>
                                                <button class="carousel-control-next" type="button" data-bs-target="#hikeCarousel<?= $reviewId; ?>" data-bs-slide="next">
                                                    <span class="material-symbols-outlined" aria-hidden="true">arrow_right_alt</span>
                                                    <span class="visually-hidden">Next</span>
                                                </button>
                                            </div>
                                        </div>

                                    <?php else: // If there's only one photo, display it without a carousel 
                                    ?>  
                                         <div class="img-container" style="width: 100%; height: 500px; overflow: hidden;"> <!-- Set your desired height here -->
                                            <img src="<?= htmlspecialchars($baseReviewImagePath . trim($reviewPhotos[0])); ?>" 
                                            class="d-block rounded" 
                                            alt="Single Mountain Image"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <?php endif; ?>
                                </div>
                            </div>

                            <!-- Hike Information -->
                            <div class="row mt-3">
                                <div class="col">
                                    <h3> Exploring Mount <?= $mountainName; ?></h3> <!-- Dynamic mountain name -->
                                    <p class="text-muted"><?= $mountainName; ?> Trail - <?= $mountainLocation; ?></p> <!-- Dynamic mountain location -->

                                    <!-- Hike Details -->
                                    <div class="d-flex justify-content-start align-items-center mb-2">
                                    <p class="mb-0 me-3">Difficulty Level: <strong><?= $difficultyLevel; ?></strong></p>
                                    <p class="mb-0 me-3">Elev gain: <strong><?= $elevation; ?> m</strong></p>
                                        <span class="me-2"> Rating: <?= str_repeat("⭐", $rating); ?></span>
                                    </div>

                                    <!-- Caption -->
                                    <p class="mt-3">
                                        <?= htmlspecialchars($comment); ?>
                                    </p>

                                    <!-- Display Tags -->
                                    <div class="mt-2">
                                        <div class="tags-container mt-1">
                                            <?php 
                                            // Decode the JSON string into an array
                                            $tagsArray = json_decode($tags, true);
                                            // Ensure the tags are properly formatted and not empty
                                            if (!empty($tagsArray)): 
                                                foreach ($tagsArray as $tag): ?>
                                                    <span class="badge rounded-pill fs-6 me-1 mb-1 tag-badge" style="color: #3f8b22;">
                                                        <?= htmlspecialchars(trim($tag)); ?>
                                                    </span>
                                                <?php endforeach; 
                                            else: ?>
                                                <span class="badge rounded-pill fs-6 me-1 mb-1 tag-badge" style="color: #3f8b22;">No tags</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- Like Button -->
                                    <div class="d-flex align-items-center mt-3 like-container">
                                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                            <path class="heart" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="none" stroke="currentColor" stroke-width="2" />
                                            <path class="heart-filled" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="red" class="heart-filled" />
                                        </svg>
                                        <span class="ms-2 like-count"><?= $likeCount; ?> like<?= $likeCount != 1 ? 's' : ''; ?></span>
                                    </div>
                                    <hr>
                                </div>
                            </div>
                        </div> <!-- End of community-section -->

                    <?php
                    endwhile; // End of the while loop
                    ?>

                <?php
                else:
                    echo "<p>No reviews found.</p>";
                endif;

                // Close the database connection
                $conn->close();
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

                <!--OWN JS-->
                <script src="community.js"></script>

                <!--JQUERY-->
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="../../systemfeatures/search/search.js" defer></script>

</body>

</html>