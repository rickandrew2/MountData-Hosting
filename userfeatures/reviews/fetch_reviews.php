<?php

// Function to fetch ratings
function fetchRatings($conn, $mountain_id) {
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

    return [
        'counts' => $ratingCounts,
        'totalReviews' => $totalReviews,
        'totalRatingSum' => $totalRatingSum
    ];
}   

// Function to display ratings// Function to display ratings

function displayRatings($ratings) {
    for ($rating = 1; $rating <= 5; $rating++) {
        $count = $ratings['counts'][$rating]; // Get the count for the current rating
        $stars = str_repeat('<i class="fas fa-star" style="color: #32CD32"></i>', $rating);
        echo '<h6>' . $stars . ' (' . $count . ')</h6>'; // Display the stars and count
    }
}



// Function to calculate average rating
function calculateAverageRating($ratings) {
    $totalReviews = $ratings['totalReviews'];
    $totalRatingSum = $ratings['totalRatingSum'];
    return $totalReviews > 0 ? round($totalRatingSum / $totalReviews, 1) : 0;
}

// Function to display average stars
function displayAverageStars($averageRating) {
    $averageStars = str_repeat('<i class="fas fa-star" style="color: #32CD32"></i>', round($averageRating));
    echo '<div>' . $averageStars . '</div>';
}

// Function to fetch total reviews
function fetchTotalReviews($conn, $mountain_id) {
    $totalReviewsQuery = "SELECT COUNT(*) AS total FROM reviews WHERE mountain_id = ?";
    $stmt = $conn->prepare($totalReviewsQuery);
    $stmt->bind_param('i', $mountain_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'];
}

// Function to get the current page
function getCurrentPage() {
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    return max(1, $currentPage); // Ensure the page is at least 1
}

// Function to fetch reviews
function fetchReviews($conn, $mountain_id, $reviewsPerPage, $offset) {
    $sql = "SELECT r.review_id, r.user_id, r.rating, r.comment, r.review_date, u.username, u.image_path, r.review_photo 
            FROM reviews r 
            JOIN users u ON r.user_id = u.user_id 
            WHERE r.mountain_id = ? 
            ORDER BY r.review_date DESC 
            LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iii', $mountain_id, $reviewsPerPage, $offset);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to display reviews
function displayReviews($result) {
    if ($result->num_rows > 0) {
        echo '<div class="reviews-comments-section">';
        while ($row = $result->fetch_assoc()) {
            displayReview($row);
        }
        echo '</div>';
    } else {
        displayNoReviewsMessage();
    }
}

// Function to display individual review
function displayReview($row) {
    $formattedDate = date("F j, Y", strtotime($row['review_date']));
    $stars = str_repeat('<i class="fas fa-star" style="color: #32CD32"></i>', $row['rating']);
    $currentUserId = $_SESSION['user_id'];

    echo '<div class="review-comment">';
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
    
    if ($row['user_id'] == $currentUserId) {
        echo '<li><a class="dropdown-item" href="#" onclick="confirmDeletion(' . $row['review_id'] . '); return false;">Delete</a></li>';
    } else {
        echo '<li><a class="dropdown-item" style="color: red;" href="#" onclick="confirmReport(' . $row['review_id'] . ', ' . $currentUserId . '); return false;">&#x26A0; Report</a></li>';
    }
    
    echo '</ul></div></div></div>';
    echo '<div class="container star-ratings mt-2">' . $stars . '</div>';
    echo '<div class="container" style="border-bottom: #8a8a8a solid 1px">';
    echo '<p class="comment-text fs-5" style="text-align: justify;">' . htmlspecialchars($row['comment']) . '</p>';
    
    if (!empty($row['review_photo'])) {
        displayReviewPhotos($row['review_photo']);
    }
    
    echo '</div>'; // End of review comment container
}

// Function to display review photos
function displayReviewPhotos($reviewPhoto) {
    $photos = explode(',', $reviewPhoto);
    $photoClass = count($photos) > 1 ? 'review-photos d-flex' : 'review-photos';

    echo '<div class="' . $photoClass . ' mt-2">';
    foreach ($photos as $photo) {
        echo '<img src="userfeatures/reviews/' . htmlspecialchars(trim($photo)) . '" alt="Review Photo" class="img-fluid rounded review-photo">';
    }
    echo '</div>';
}

// Function to display no reviews message
function displayNoReviewsMessage() {
    echo '
    <div class="no-reviews mt-5" style="text-align: center">
        <span class="material-symbols-outlined" style="display: block; margin: 0 auto; font-size: 5rem;">landscape_2_off</span>
        <h3 class="mt-3">Write reviews</h3>
        <p style="color: #8a8a8a;">Share your experience by leaving a review on any trail page. Your feedback helps others choose their next adventure!</p>
    </div>';
}

// Function to display login prompt
function displayLoginPrompt() {
    echo '
            <div class="login-prompt mt-5" style="text-align: center">
            <span class="material-symbols-outlined" style="display: block; margin: 0 auto; font-size: 5rem;">lock</span>
            <h3 class="mt-3">Please Log In</h3>
            <p style="color: #8a8a8a;">You need to log in to view and write reviews. <a href="login.php" style="color: #32CD32;">Log In</a></p>
            </div>';
}

// Function to display pagination
function displayPagination($currentPage, $totalPages, $mountain_id) {
    // Pagination
    echo '<div class="pagination" style="text-align: center; margin-top: 20px;">';

    // Display page numbers
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i === $currentPage) {
            echo '<strong style="margin: 0 5px;">' . $i . '</strong>'; // Current page
                } else {
                    echo '<a href="?mountain_id=' . $mountain_id . '&page=' . $i . '" style="margin: 0 5px; text-decoration: none; color: blue;">' . $i . '</a>';
                    }
                }
    
    // Display next page link if there's more
        if ($currentPage < $totalPages) {
            echo '<a href="?mountain_id=' . $mountain_id . '&page=' . ($currentPage + 1) . '" style="margin-left: 10px; text-decoration: none; color: blue;">&gt;</a>';
            }
        echo '</div>
        </div>';
}
?> 