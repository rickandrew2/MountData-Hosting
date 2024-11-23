<?php
// fetch_feed.php

// Include the database connection and login check
include '../../db_connection.php';

if ($loginStatus) {
    $user_id = $_SESSION['user_id'];

    // Query to get the user's reviews with mountain details
    $query = "SELECT r.review_id, r.user_id, r.rating, r.comment, r.review_date, r.review_photo, r.tags, 
                     m.name AS mountain_name, m.location AS mountain_location, m.difficulty_level, m.elevation, 
                     u.username, u.image_path 
              FROM reviews r
              JOIN mountains m ON r.mountain_id = m.mountain_id
              JOIN users u ON r.user_id = u.user_id
              WHERE r.user_id = ? 
              ORDER BY r.review_date DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any reviews were returned
    $reviewCount = $result->num_rows;
    if ($reviewCount > 0) {
        while ($row = $result->fetch_assoc()):
            // Retrieve each field
            $reviewId = $row['review_id'];
            $rating = $row['rating'];
            $comment = $row['comment'];
            $reviewDate = date("M d", strtotime($row['review_date']));
            $tags = $row['tags'];
            $tagArray = array_filter(explode(',', $tags));

            $baseReviewImagePath = '../../userfeatures/reviews/';
            $reviewPhotos = array_filter(explode(',', $row['review_photo']));

            $profilePicture = htmlspecialchars($row['image_path']) ?: '/images/profile_images/default.jpg';
            $username = htmlspecialchars($row['username']);
            $mountainName = htmlspecialchars($row['mountain_name']);
            $mountainLocation = htmlspecialchars($row['mountain_location']);
            $difficultyLevel = htmlspecialchars($row['difficulty_level']);
            $elevation = htmlspecialchars($row['elevation']);
?>

            <!-- Feed Section -->
            <div class="community-section" style="width: 100%; overflow-x: auto;">
                <div class="row align-items-center py-2">
                    <div class="col-auto">
                        <img src="<?= $profilePicture; ?>" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px;">
                    </div>
                    <div class="col">
                        <div class="d-flex flex-column">
                            <h6 class="m-0"><?= $username; ?></h6>
                            <small class="text-muted"><?= $reviewDate; ?> &bull; <span class="material-symbols-outlined" style="font-size: 18px;">hiking</span></small>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border: none; background: none; font-size: 1.5rem;">&#x2026;</button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick="confirmDeletion(<?= $reviewId; ?>)">Delete</a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <?php if (count($reviewPhotos) > 1): ?>
                            <div id="hikeCarousel<?= $reviewId; ?>" class="carousel slide mt-2" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <?php foreach ($reviewPhotos as $index => $photo): ?>
                                        <div class="carousel-item <?= $index === 0 ? 'active' : ''; ?>">
                                            <div class="img-container" style="width: 100%; height: 500px; overflow: hidden;">
                                                <img src="<?= htmlspecialchars($baseReviewImagePath . trim($photo)); ?>" class="d-block rounded" alt="Mountain Image <?= $index + 1; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#hikeCarousel<?= $reviewId; ?>" data-bs-slide="prev">
                                    <span class="material-symbols-outlined">arrow_left_alt</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#hikeCarousel<?= $reviewId; ?>" data-bs-slide="next">
                                    <span class="material-symbols-outlined">arrow_right_alt</span>
                                </button>
                            </div>
                        <?php elseif (count($reviewPhotos) === 1): ?>
                            <div class="img-container" style="width: 100%; height: 500px; overflow: hidden;">
                                <img src="<?= htmlspecialchars($baseReviewImagePath . trim($reviewPhotos[0])); ?>" class="d-block rounded" alt="Mountain Image" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        <?php else: ?>
                            <div style="display: none;"></div> <!-- This will hide the section if no images are available -->
                        <?php endif; ?>
                    </div>
                </div>


                <div class="row mt-3">
                    <div class="col">
                        <h3>Exploring Mount <?= $mountainName; ?></h3>
                        <p class="text-muted"><?= $mountainName; ?> Trail - <?= $mountainLocation; ?></p>
                        <div class="d-flex justify-content-start align-items-center mb-2">
                            <p class="mb-0 me-3">Difficulty Level: <strong><?= $difficultyLevel; ?></strong></p>
                            <p class="mb-0 me-3">Elev gain: <strong><?= $elevation; ?> m</strong></p>
                            <span class="me-2"> Rating: <?= str_repeat("⭐", $rating); ?></span>
                        </div>
                        <p class="mt-3"><?= htmlspecialchars($comment); ?></p>
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
                    </div>
                </div>
            </div>

        <?php endwhile; ?>
<?php
    } else {
        echo '<div class="no-bookmarks mt-5" style="text-align: center;">
            <span class="material-symbols-outlined" style="display: block; margin: 0 auto; font-size: 5rem;">hourglass_empty</span>
            <h3 class="mt-3">No Reviews Yet</h3>
            <p style="color: #8a8a8a;">You haven\'t added any reviews yet. Start sharing your experiences to build your feed!</p>
        </div>';
    }
} else {
    echo '<p>You must be logged in to view your feed.</p>';
}
?>

<script>
    function confirmDeletion(reviewId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Make an AJAX call to delete the review
                fetch('delete_review.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id=' + reviewId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'The comment has been deleted successfully.',
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                // Optionally refresh the page or remove the review from the DOM
                                window.location.reload(); // Refresh to see changes
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to delete the review.',
                                icon: 'error',
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    }
</script>