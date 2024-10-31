
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- My CSS -->
        <link rel="stylesheet" href="dist/css/mountains_profiles.css"/>

         <!-- Favicon -->
        <link rel="icon" href="images/logomount.png" type="image/png" />

        <!--SWEET ALERT CDN-->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.2/dist/sweetalert2.all.min.js"></script>
        
        <!--SweetAlert CSS-->
        <link href=" https://cdn.jsdelivr.net/npm/sweetalert2@11.14.2/dist/sweetalert2.min.css" rel="stylesheet">
    
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"/>
    
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
        
        include_once 'check_login.php'; // Use include_once to prevent redeclaration
        

    // Check if the mountain_id is provided in the URL
    if (isset($_GET['mountain_id']) && is_numeric($_GET['mountain_id'])) {
        $mountain_id = intval($_GET['mountain_id']); // Get the mountain ID from the URL and sanitize it
        echo "<script>var mountainId = $mountain_id;</script>";

         // Prepare and execute the query to fetch data for the specific mountain
                $sql = "SELECT `mountain_id`, `name`, `location`, `elevation`, `mountain_image`, `difficulty_level`, `description`, `latitude`, `longitude`, `contact_number`
                FROM `mountains` WHERE `mountain_id` = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
            $stmt->bind_param("i", $mountain_id); // Bind the mountain ID parameter
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if the mountain exists
            if ($result->num_rows > 0) {
                // Fetch the mountain data
                $row = $result->fetch_assoc();
                $name = htmlspecialchars($row['name']); // Escape output data for security
                $location = htmlspecialchars($row['location']);
                $elevation = htmlspecialchars($row['elevation']);
                $mountain_image = htmlspecialchars($row['mountain_image']);
                $difficulty_level = htmlspecialchars($row['difficulty_level']);
                $description = htmlspecialchars($row['description']);
                $latitude = htmlspecialchars($row['latitude']);
                $longitude = htmlspecialchars($row['longitude']);
                $contact_numbers = htmlspecialchars($row['contact_number']); // Fetch contact number

                // Split the contact numbers into an array
                $contact_numbers_array = explode(',', $contact_numbers);

                 // Set the document title dynamically
                echo "<script>document.title = 'Mount $name';</script>";
                echo "<script>
                var mountainLatitude = $latitude;
                var mountainLongitude = $longitude;
                </script>";

                 // Fetch counts of ratings
                $ratingsQuery = "SELECT rating, COUNT(*) as count FROM reviews WHERE mountain_id = ? GROUP BY rating";
                $stmt = $conn->prepare($ratingsQuery);
                $stmt->bind_param('i', $mountain_id);
                $stmt->execute();
                $ratingsResult = $stmt->get_result();
                
                $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
                $totalReviews = 0;
                $totalRatingSum = 0;

                while ($row = $ratingsResult->fetch_assoc()) {
                    $ratingCounts[$row['rating']] = $row['count'];
                    $totalReviews += $row['count'];
                    $totalRatingSum += $row['rating'] * $row['count'];
                }

                // Calculate average rating
                $averageRating = $totalReviews > 0 ? round($totalRatingSum / $totalReviews, 1) : 0;

                // Output HTML for the specific mountain
                echo "
                <div class='mountain-pic-header container m-0' style='position: relative; border-radius: 10px 10px 0 0; background-image: url(\"$mountain_image\") !important; background-size: cover; background-position: center; height: 300px;' onclick=\"openLightbox('$mountain_image')\">
                    <div class='mountain-content mb-3' style='position: absolute; bottom: 0; width: 100%; padding: 10px; color: white; border-radius: 0 0 10px 10px;'>
                        <div class='mountain-name-wrapper'>
                            <h3 class='mountain-name' style='align-self: flex-end; color: white;'>Mount $name</h3>
                        </div>
                        <div class='difficulty-n-rating d-flex'>
                            <h4>
                                " . htmlspecialchars($difficulty_level) . " - <i class='fas fa-star fs-6' style='color: #32CD32'></i> " . $averageRating . " (" . $totalReviews . ")
                            </h4>
                        </div>
                        <h5 style='text-decoration: underline; text-decoration-thickness: 2px; text-underline-offset: 4px;'>$location</h5>
                    </div>
                </div>

                <div class='d-flex justify-content-between mx-3'>
                    <div class='length mt-3 fs-3'>
                        <p class='info-label' style='line-height: 1.2; margin-bottom: 0; color: gray'>Difficulty</p>
                        <p class='info-value' style='line-height: 1.2; margin: 0; color: green;'>$difficulty_level</p> <!-- Replace with actual length if available -->
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
                    <p>
                        $description
                    </p>
                </div>
                               
                ";


                // Display weather information only for logged-in users
                if ($loginStatus) {
                    echo "
                    <div class='weather-info'>
                        <!-- Current weather information will be inserted here by JavaScript -->
                    </div>
                    <div class='container forecast-info overflow-auto'>
                        <!-- 5-day forecast information will be inserted here by JavaScript -->
                    </div>
                    ";
                } else {
                    echo "<p class=mx-3>Please log in to view weather information.</p>";
                }

                // Display contact numbers
                if (!empty($contact_numbers_array)) {
                    echo "<div class='contact-numbers m-3'>";
                    echo "<h5>Emergency Contact Numbers:</h5>";
                    echo "<ul class='contact-list'>";
                    foreach ($contact_numbers_array as $number) {
                        echo "<li><i class='fa-solid fa-phone'></i>" . htmlspecialchars(trim($number)) . "</li>"; // Display each contact number with an icon
                    }
                    echo "</ul>";
                    echo "</div>";
                }
     
            } else {
                echo "<p>Mountain not found.</p>";
            }
            // Close the statement
            $stmt->close();
        } else {
            echo "<p>Error preparing the SQL statement.</p>";
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
    <div class="reviews-content" id="reviews" style="display: block;">  <!-- Default visible -->
        <div class="row mb-3">
            <div class="ratings col-lg-4 col-md-6">
                <?php
                // Fetch counts of ratings
                $ratingsQuery = "SELECT rating, COUNT(*) as count FROM reviews WHERE mountain_id = ? GROUP BY rating";
                $stmt = $conn->prepare($ratingsQuery);
                $stmt->bind_param('i', $mountain_id);
                $stmt->execute();
                $ratingsResult = $stmt->get_result();
                
                $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
                $totalReviews = 0;
                $totalRatingSum = 0;

                while ($row = $ratingsResult->fetch_assoc()) {
                    $ratingCounts[$row['rating']] = $row['count'];
                    $totalReviews += $row['count'];
                    $totalRatingSum += $row['rating'] * $row['count'];
                }

                // Display ratings with fixed star icons
                for ($i = 5; $i >= 1; $i--) {
                    $stars = str_repeat('<i class="fas fa-star" style="color: #32CD32"></i>', $i); // Fixed star icons
                    echo '<h6>' . $stars . ' (' . $ratingCounts[$i] . ')</h6>';
                }

                // Calculate average rating
                $averageRating = $totalReviews > 0 ? round($totalRatingSum / $totalReviews, 1) : 0;
                ?>
            </div>
            <div class="total-rating col-lg-5 col-md-6"  style="margin-top: -20px;">
                <h1 style="font-size: 5rem; color: #006400;"><?php echo $averageRating; ?></h1>
                <?php
                // Generate star icons based on average rating
                $averageStars = str_repeat('<i class="fas fa-star" style="color: #32CD32"></i>', round($averageRating)); // Rounded to nearest whole number
                echo '<div>' . $averageStars . '</div>'; // Display stars
                ?>
                <h5>(<?php echo $totalReviews; ?>) <?php echo ($totalReviews == 1) ? 'Review' : 'Reviews'; ?></h5>
            </div>
            <div class="write-review col-md-12 col-lg-3 mb-1" style="text-align: center;">
                <h5 id="writeReviewHeader" style="cursor: pointer;">Write Review</h5>
            </div>
        </div>
        <div class="search-reviews" style="display: flex; align-items: center; position: relative; width: 100%;">
            <!-- Search icon on the left -->
            <i class="fas fa-search" style="position: absolute; left: 10px; color: black"></i>
            
            <!-- Input field -->
            <input type="text" placeholder="Search reviews..." class="search-bar" style="font-size: 1.2rem;">
        
            <!-- Filter icon on the right -->
            <div class="filter-icon" style="position: absolute; right: 10px;">
                <i class="fas fa-filter" style="color: black;"></i>
            </div>
        </div>
            
            <!--REVIEW COMMENTS SECTION-->
            <?php
        
            // Get mountain_id from the URL
            $mountain_id = isset($_GET['mountain_id']) ? (int)$_GET['mountain_id'] : 0;

            // Reviews per page
            $reviewsPerPage = 5;

            // Get the total number of reviews for the specific mountain
            $totalReviewsQuery = "SELECT COUNT(*) AS total FROM reviews WHERE mountain_id = ?";
            $stmt = $conn->prepare($totalReviewsQuery);
            $stmt->bind_param('i', $mountain_id);
            $stmt->execute();
            $totalReviewsResult = $stmt->get_result();
            $totalReviews = $totalReviewsResult->fetch_assoc()['total'];

            // Calculate total pages
            $totalPages = ceil($totalReviews / $reviewsPerPage);

            // Get the current page from the URL, default to 1
            $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

            // Ensure the current page is within the valid range
            if ($current_page < 1) {
                $current_page = 1;
            } elseif ($current_page > $totalPages) {
                $current_page = $totalPages;
            }

            // Calculate the offset for the query
            $offset = ($current_page - 1) * $reviewsPerPage;

            // Fetch reviews for the current page for the specific mountain
            $sql = "SELECT r.review_id, r.user_id, r.rating, r.comment, r.review_date, u.username, u.image_path, r.review_photo 
            FROM reviews r 
            JOIN users u ON r.user_id = u.user_id 
            WHERE r.mountain_id = ? 
            ORDER BY r.review_date DESC 
            LIMIT ? OFFSET ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iii', $mountain_id, $reviewsPerPage, $offset);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if the user is logged in using $loginStatus
            if ($loginStatus) { // Assuming $loginStatus is true if logged in
            // Check if there are results
            if ($result->num_rows > 0) {
            echo '<div class="reviews-comments-section">';

            // Loop through each review and display it
            while ($row = $result->fetch_assoc()) {
                // Fetch and format the review details
                $formattedDate = date("F j, Y", strtotime($row['review_date']));
                $stars = str_repeat('<i class="fas fa-star" style="color: #32CD32"></i>', $row['rating']);
                $currentUserId = $_SESSION['user_id'];

                echo '<div class="review-comment">'; // Start a new comment container
                echo '<div class="container d-flex" style="margin-top: 20px;">';
                echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="Profile Picture" style="width: 70px; height: 70px; border-radius: 50%; background-color: gray;">';
                echo '<div class="name-n-date mx-3 mt-2">';
                echo '<h5>' . htmlspecialchars($row['username']) . '</h5>';
                echo '<h6>' . $formattedDate . '</h6>';
                echo '</div>';
                echo '<div class="dropdown ms-auto mt-2">';
                echo '<button class="btn" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="border: none; background: none; padding: 0; font-size: 1.5rem;">';
                echo '&#x2026;</button>'; // Three-dot icon
                echo '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';

                // Show delete option only if the comment is from the current user
                if ($row['user_id'] == $currentUserId) {
                    echo '<li><a class="dropdown-item" href="#" onclick="confirmDeletion(' . $row['review_id'] . '); return false;">Delete</a></li>';
                } else {
                    echo '<li class="dropdown-item" style="color: red;">&#x26A0; Report</li>';
                }

                echo '</ul></div></div></div>';
                echo '<div class="container star-ratings mt-2">' . $stars . '</div>';
                echo '<div class="container" style="border-bottom: #8a8a8a solid 1px">';

                // Display the comment text
                echo '<p class="comment-text fs-5" style="text-align: justify;">' . htmlspecialchars($row['comment']) . '</p>';

                // Check if there's a review photo and display it
                if (!empty($row['review_photo'])) {
                    // Split the review_photo by commas if multiple photos exist
                    $photos = explode(',', $row['review_photo']);
                    
                    // Only set display: flex if there are multiple photos
                    $photoClass = count($photos) > 1 ? 'review-photos d-flex' : 'review-photos';
                
                    echo '<div class="' . $photoClass . ' mt-2">';
                
                    foreach ($photos as $photo) {
                        echo '<img src="userfeatures/reviews/' . htmlspecialchars(trim($photo)) . '" alt="Review Photo" class="img-fluid rounded review-photo" >';
                    }
                    echo '</div>';
                }

                echo '</div>'; // End of review comment container
            }

            } else {
            echo '
            <div class="no-reviews mt-5" style="text-align: center">
                <span class="material-symbols-outlined" style="display: block; margin: 0 auto; font-size: 5rem;">landscape_2_off</span>
                <h3 class="mt-3">Write reviews</h3>
                <p style="color: #8a8a8a;">Share your experience by leaving a review on any trail page. Your feedback helps others choose their next adventure!</p>
            </div>';
            }
            } else {
            // User is not logged in
            echo '
            <div class="login-prompt mt-5" style="text-align: center">
            <span class="material-symbols-outlined" style="display: block; margin: 0 auto; font-size: 5rem;">lock</span>
            <h3 class="mt-3">Please Log In</h3>
            <p style="color: #8a8a8a;">You need to log in to view and write reviews. <a href="login.php" style="color: #32CD32;">Log In</a></p>
            </div>';
            }
            // Pagination
            echo '<div class="pagination" style="text-align: center; margin-top: 20px;">';

            // Display page numbers
            for ($i = 1; $i <= $totalPages; $i++) {
                if ($i === $current_page) {
                    echo '<strong style="margin: 0 5px;">' . $i . '</strong>'; // Current page
                } else {
                    echo '<a href="?mountain_id=' . $mountain_id . '&page=' . $i . '" style="margin: 0 5px; text-decoration: none; color: blue;">' . $i . '</a>';
                }
            }

            // Display next page link if there's more
            if ($current_page < $totalPages) {
                echo '<a href="?mountain_id=' . $mountain_id . '&page=' . ($current_page + 1) . '" style="margin-left: 10px; text-decoration: none; color: blue;">&gt;</a>';
            }
            echo '</div>';

            // Close connection
            $conn->close();
            ?>
        </div>
    </div>

        <div class="photos-content row" id="photos" style="display: none;">
            <!-- Initially hidden -->
            <div class="row">
                <div class="col-lg-9 m-0">
                    <div class="photos-details">
                        <h4>Photos of This Trail</h4>
                        <h5>Photos help others preview the trail. Upload photos about this trail to inspire others.</h5>
                    </div>
                    <div class="sort-by-container">
                        <label for="sortBy">Sort by:</label>
                        <select id="sortBy">
                            <option value="all">All Trails</option>
                            <option value="popular">Most Popular</option>
                            <option value="recent">Most Recent</option>
                        </select>
                    </div>
                </div>
                <div class="upload-photos col-lg-3" style="text-align: center;">
                    <h5 class="upload-photos-btn mt-sm-4 mt-lg-0" id="uploadBtn">Upload photos</h5>
                </div>
            </div>
            <?php
            if ($loginStatus) { // Assuming $loginStatus is true if logged in
                include 'db_connection.php'; // Include your database connection file
            
                // Fetch photos for the specific mountain_id from the database
                $query = "
                    SELECT r.review_photo, r.review_date, u.username, u.image_path
                    FROM reviews r
                    INNER JOIN users u ON r.user_id = u.user_id
                    WHERE r.mountain_id = ? AND r.review_photo IS NOT NULL
                ";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('i', $mountain_id);
                $stmt->execute();
                $result = $stmt->get_result();
                ?>
            
                <div class="container photos py-5">
                    <div class="row">
                        <?php if ($result->num_rows > 0): ?>
                            <div class="col-12 mb-4 text-center" style="border-top: #8a8a8a solid 1px">
                                <h2 class="mt-5">Trail Photos</h2>
                                <p>Click on a photo to view it full-screen.</p>
                            </div>
                            <div class="row g-4 justify-content-center"> <!-- Added justify-content-center for horizontal centering -->
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <?php 
                                    // Split the review_photo by commas to get an array of photo paths
                                    $photos = explode(',', $row['review_photo']); 
                                    ?>
                                    <?php foreach ($photos as $photo): ?>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mx-2">
                                            <div class="photo-item">
                                                <img src="userfeatures/reviews/<?php echo htmlspecialchars(trim($photo)); ?>" 
                                                    alt="Uploaded photo" 
                                                    class="img-fluid rounded shadow-sm"
                                                    onclick="openModal('<?php echo htmlspecialchars(trim($photo)); ?>', '<?php echo htmlspecialchars($row['username']); ?>', '<?php echo htmlspecialchars($row['review_date']); ?>', '<?php echo htmlspecialchars(trim(str_replace('../../', '', $row['image_path']))); ?>')">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <!-- Placeholder for no uploaded photos -->
                            <div class="col-12 text-center mt-5">
                                <div class="no-reviews">
                                    <span class="material-symbols-outlined d-block mx-auto" style="font-size: 5rem;">landscape_2_off</span>
                                    <h3 class="mt-3">Upload Photos</h3>
                                    <p class="text-muted">Share your experience by leaving a photo on any trail page. Your feedback helps others choose their next adventure!</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

           <!-- Modal for Full-Screen Image View -->
            <div class="modalWrapper modal" id="modalWrapper" style="display: none;">
                <div id="photoModal">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <img class="modal-content" id="modalImage">
                    <div id="modalOverlay">
                        <img id="profilePic" class="rounded-circle" src="" alt="User Profile Picture">
                        <span id="username"></span> 
                        <span id="uploadDate"></span>
                    </div>
                </div>
            </div>
            <?php
            } else {
                // User is not logged in
                echo '
                <div class="login-prompt mt-5" style="text-align: center">
                    <span class="material-symbols-outlined" style="display: block; margin: 0 auto; font-size: 5rem;">lock</span>
                    <h3 class="mt-3">Please Log In</h3>
                    <p style="color: #8a8a8a;">You need to log in to view and upload photos. <a href="login.php" style="color: #32CD32;">Log In</a></p>
                </div>';
            }
            ?>
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

        
    <!-- Optional JavaScript; choose one of the two! -->
    <script src="mountains_profiles.js"></script>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
    <!--JQUERY-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
    <script src="systemfeatures/search/search.js" defer></script>
    <script src="weather.js"></script>
    <script>
        const isLoggedIn = <?php echo json_encode($loginStatus); ?>; // Pass the PHP variable to JS
    </script>   

  </body>
</html>