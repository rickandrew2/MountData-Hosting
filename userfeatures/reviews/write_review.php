<?php
// Prevent any output before the JSON response
ob_clean(); // Clear any output buffers
header('Content-Type: application/json'); // Set JSON content type

include_once ('../../check_login.php'); // This will check if the user is logged in
include ('../../db_connection.php'); // Include the database connection

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get the submitted user_id, mountain_id, rating, and comment
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
        $mountain_id = isset($_POST['mountain_id']) ? intval($_POST['mountain_id']) : null;
        $rating = isset($_POST['ratingValue']) ? intval($_POST['ratingValue']) : 0;
        $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

        if ($user_id && $mountain_id && $rating > 0 && !empty($comment)) {
            // Use the existing $conn from db_connection.php
            $stmt = $conn->prepare("INSERT INTO reviews (user_id, mountain_id, rating, comment, review_photo) VALUES (?, ?, ?, ?, NULL)");
            $stmt->bind_param("iiis", $user_id, $mountain_id, $rating, $comment);

            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Review submitted successfully!';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Database error: ' . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Invalid input. Please submit a valid rating and comment.';
        }
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['message'] = 'Server error: ' . $e->getMessage();
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method';
}

// Ensure no other output interferes with the JSON response
echo json_encode($response);
exit;
?>
