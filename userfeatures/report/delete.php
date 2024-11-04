<?php
include('../../db_connection.php'); // Connect to your database

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'])) {
    $reviewId = intval($_POST['review_id']);
    $sql = "DELETE FROM reviews WHERE review_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reviewId);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request";
}
?>
