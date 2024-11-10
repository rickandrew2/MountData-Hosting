<?php
include '../../db_connection.php'; // Make sure to include database connection

$searchTerm = $_POST['query'] ?? '';


// Modify the SQL query to include the search term
$sql = "
    SELECT r.*, 
           m.name AS mountainName, 
           m.location, 
           m.difficulty_level, 
           m.elevation, 
           u.username, 
           u.image_path 
    FROM reviews r 
    JOIN mountains m ON r.mountain_id = m.mountain_id 
    JOIN users u ON r.user_id = u.user_id
    WHERE m.name LIKE ? OR r.tags LIKE ?
";


$stmt = $conn->prepare($sql);
$searchTermWildcard = '%' . $searchTerm . '%';
$stmt->bind_param('ss', $searchTermWildcard, $searchTermWildcard);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()):
        // Retrieve each field
        $reviewId = $row['review_id'];
        $userId = $row['user_id'];
        $mountainId = $row['mountain_id'];
        $rating = $row['rating'];
        $comment = $row['comment'];
        $reviewDate = date("M d", strtotime($row['review_date']));
        $tags = $row['tags']; // Retrieve tags
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
        $profilePicture = isset($row['image_path']) ? htmlspecialchars($row['image_path']) : '/images/profile_images/default.jpg';
        $username = htmlspecialchars($row['username']);
        $mountainName = htmlspecialchars($row['mountainName']);
        $mountainLocation = isset($row['location']) ? htmlspecialchars($row['location']) : 'Unknown Location';
        $difficultyLevel = isset($row['difficulty_level']) ? htmlspecialchars($row['difficulty_level']) : 'N/A';
        $elevation = isset($row['elevation']) ? htmlspecialchars($row['elevation']) : 'N/A';

?>

        <!-- Community section -->
        <div class="community-section-container">
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
                    <?php if (count($reviewPhotos) > 1): // Check if there are multiple images ?>
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
                    <?php else: // If there's only one photo, display it without a carousel ?>
                        <div class="img-container" style="width: 100%; height: 500px; overflow: hidden;">
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
                    <p class="mt-3"><?= htmlspecialchars($comment); ?></p>

                    <!-- Display Tags -->
                    <div class="mt-2">
                        <div class="tags-container mt-1">
                            <?php 
                            // Ensure the tags are properly formatted and not empty
                            if (!empty($tagArray)): 
                                foreach ($tagArray as $tag): ?>
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
                            <path class="heart-filled" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="currentColor" />
                        </svg>
                        <span class="like-count ms-1"><?= $likeCount; ?></span>
                    </div>
                </div>
            </div>
        </div>

<?php
    endwhile;
} else {
    echo '<div class="no-results mt-5" style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 500px; text-align: center;">
        <span class="material-symbols-outlined" style="font-size: 5rem;">search_off</span>
        <h3 class="mt-3">No Results Found</h3>
        <p style="color: #8a8a8a;">We couldn’t find any reviews that match your search. Please try different keywords or check back later!</p>
        <p style="color: #8a8a8a;">In the meantime, try looking for other community reviews on different trails!</p>
        <span class="material-symbols-outlined" style="font-size: 2rem; color: #8a8a8a;">arrow_downward</span>
      </div>';
}


$stmt->close();
$conn->close();
?>

