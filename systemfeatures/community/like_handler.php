<?php
// like_handler.php
include('../../db_connection.php');
session_start();

$userId = $_SESSION['user_id']; // Get logged in user's ID
$reviewId = $_POST['review_id']; // Review ID from AJAX request

// Check if the like already exists for this user and review
$checkLikeQuery = "SELECT * FROM likes WHERE user_id = '$userId' AND review_id = '$reviewId'";
$checkLikeResult = $conn->query($checkLikeQuery);

if ($checkLikeResult->num_rows > 0) {
    // The user has already liked this review, so remove the like
    $deleteLikeQuery = "DELETE FROM likes WHERE user_id = '$userId' AND review_id = '$reviewId'";
    $conn->query($deleteLikeQuery);

    // Remove the notification
    $deleteNotificationQuery = "DELETE FROM notifications 
                              WHERE user_id = '$userId' 
                              AND review_id = '$reviewId' 
                              AND notification_type = 'like'";
    $conn->query($deleteNotificationQuery);

    // Decrease the like count in the reviews table
    $updateLikeCountQuery = "UPDATE reviews SET like_count = like_count - 1 WHERE review_id = '$reviewId'";
    $conn->query($updateLikeCountQuery);

    echo json_encode(['status' => 'unliked', 'like_count' => getLikeCount($reviewId)]);
} else {
    // The user hasn't liked this review, so insert a new like
    $insertLikeQuery = "INSERT INTO likes (user_id, review_id, like_date) VALUES ('$userId', '$reviewId', NOW())";
    $conn->query($insertLikeQuery);

    // Get the user_id of the review owner to send them the notification
    $getReviewOwnerQuery = "SELECT user_id FROM reviews WHERE review_id = '$reviewId'";
    $reviewOwnerResult = $conn->query($getReviewOwnerQuery);
    $reviewOwner = $reviewOwnerResult->fetch_assoc();
    $receiverUserId = $reviewOwner['user_id'];

    // Create a notification for the like (sent to the review owner)
    $insertNotificationQuery = "INSERT INTO notifications 
                              (user_id, review_id, notification_type, is_read) 
                              VALUES ('$receiverUserId', '$reviewId', 'like', 0)";
    $conn->query($insertNotificationQuery);

    // Increase the like count in the reviews table
    $updateLikeCountQuery = "UPDATE reviews SET like_count = like_count + 1 WHERE review_id = '$reviewId'";
    $conn->query($updateLikeCountQuery);

    echo json_encode(['status' => 'liked', 'like_count' => getLikeCount($reviewId)]);
}

// Helper function to get the current like count for the review
function getLikeCount($reviewId) {
    global $conn;
    $result = $conn->query("SELECT like_count FROM reviews WHERE review_id = '$reviewId'");
    $row = $result->fetch_assoc();
    return $row['like_count'];
}
?>
