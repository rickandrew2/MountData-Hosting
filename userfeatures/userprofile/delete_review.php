<?php
// delete_review.php
include '../../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reviewId = $_POST['id'];

    // Check if the user is logged in and has the right to delete
    session_start();
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        // Prepare the SQL statement
        $query = "DELETE FROM reviews WHERE review_id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $reviewId, $userId);
        $stmt->execute();

        // Check if the deletion was successful
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
