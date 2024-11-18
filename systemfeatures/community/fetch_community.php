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
    <h2 class="text-center border-bottom pb-2">Latest Nearby</h2>
    <div class="search-container-com">
        <input type="text" class="search-input form-control" id="searchInput" placeholder="Search...(by #tags or name of mountains)" />
        <span class="material-symbols-outlined search-icon">search</span>
    </div>

    <div id="reviews-container">
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

            // Check if the current user has liked this review
            $currentUserId = $_SESSION['user_id'];
            $likeQuery = "SELECT * FROM likes WHERE user_id = '$currentUserId' AND review_id = '{$row['review_id']}'";
            $likeResult = $conn->query($likeQuery);
            $hasLiked = $likeResult->num_rows > 0;

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
                        <div class="dropdown">
                            <button class="btn" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="border: none; background: none; font-size: 1.5rem;">
                                &#x2026;
                            </button>
                            <!-- Dropdown menu -->
                            <ul class="dropdown-menu dropdown-review-options dropdown-menu-end">
                                <?php if ($userId == $currentUserId): ?>
                                    <li>
                                        <a class="dropdown-item delete-review-btn text-danger" href="#" onclick="confirmDeletion(<?= $reviewId ?>)">
                                            <i class="fas fa-trash-alt me-2"></i> Delete
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <a class="dropdown-item report-review-btn text-warning" href="#" onclick="confirmReport(<?= $reviewId ?>, <?= $currentUserId ?>)">
                                            <i class="fas fa-flag me-2"></i> Report
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
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
                        <!-- Like Button (inside the review loop) -->
                        <div class="d-flex align-items-center mt-3 like-container">
                            <svg class="icon like-button" data-review-id="<?= $reviewId; ?>" data-user-id="<?= $currentUserId; ?>" data-likes="<?= $hasLiked ? 'true' : 'false'; ?>" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path class="heart" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="none" stroke="currentColor" stroke-width="2" />
                                <path class="heart-filled" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="red" class="heart-filled" style="display: <?= $hasLiked ? 'block' : 'none'; ?>" />
                            </svg>
                            <span class="like-count mx-2" data-review-id="<?= $reviewId; ?>">
                                <?= $likeCount; ?> <?= ($likeCount <= 1) ? 'like' : 'likes' ?>
                            </span>
                        </div>
                        <hr>
                    </div>
                </div>
            </div> <!-- End of community-section -->

        <?php
        endwhile; // End of the while loop
        ?>
    </div>

<?php
else:
    echo "<p>No reviews found.</p>";
endif;

// Close the database connection
$conn->close();
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const reviewsContainer = document.getElementById('reviews-container');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value;

            fetch('search_reviews.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'query=' + encodeURIComponent(searchTerm)
                })
                .then(response => response.text())
                .then(data => {
                    reviewsContainer.innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        });
    });
</script>