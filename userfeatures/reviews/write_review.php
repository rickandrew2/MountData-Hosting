<?php
// write_review.php
include_once ('../../check_login.php'); // This will check if the user is logged in
include ('../../db_connection.php'); // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the submitted user_id, mountain_id, rating, and comment
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
    $mountain_id = isset($_POST['mountain_id']) ? intval($_POST['mountain_id']) : null;
    $rating = isset($_POST['ratingValue']) ? intval($_POST['ratingValue']) : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    // Validate required inputs
    if ($user_id && $mountain_id && $rating > 0 && !empty($comment)) {
        // Use the existing $conn from db_connection.php
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, mountain_id, rating, comment, review_photo) VALUES (?, ?, ?, ?, NULL)");
        $stmt->bind_param("iiis", $user_id, $mountain_id, $rating, $comment);

        if ($stmt->execute()) {
            echo "Review submitted successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        // Handle validation error (missing or invalid input)
        echo "Invalid input. Please submit a valid rating and comment.";
    }
} else {
    // If request is not POST, return an error
    echo "Invalid request method.";
}
?>
