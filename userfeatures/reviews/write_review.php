<?php
// Turn off error reporting and display
error_reporting(0);
ini_set('display_errors', 0);

// Start fresh session without outputting anything
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear ALL previous output and buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Set JSON headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Check if user is logged in without including check_login.php
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to submit a review'
    ]);
    exit;
}

// Include only the database connection
require_once('../../db_connection.php');
require_once('profanity_filter.php');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get the submitted data
    $user_id = $_SESSION['user_id']; // Use session user_id instead of POST
    $mountain_id = isset($_POST['mountain_id']) ? intval($_POST['mountain_id']) : null;
    $rating = isset($_POST['ratingValue']) ? intval($_POST['ratingValue']) : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    // Validate inputs
    if (!$user_id || !$mountain_id || $rating < 1 || empty($comment)) {
        throw new Exception('Invalid input. Please submit a valid rating and comment.');
    }

    // Check for profanity
    if (containsProfanity($comment)) {
        throw new Exception('Your review contains inappropriate language. Please revise your comment.');
    }

    // Filter the comment
    $filtered_comment = filterProfanity($comment);

    // Prepare and execute the query
    $stmt = $conn->prepare("INSERT INTO reviews (user_id, mountain_id, rating, comment, review_photo) VALUES (?, ?, ?, ?, NULL)");
    $stmt->bind_param("iiis", $user_id, $mountain_id, $rating, $filtered_comment);

    if (!$stmt->execute()) {
        throw new Exception('Database error: ' . $stmt->error);
    }

    $stmt->close();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Review submitted successfully!'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

// End the script
exit;
?>
