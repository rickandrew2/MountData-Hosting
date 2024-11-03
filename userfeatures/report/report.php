<?php
// Include the database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);
var_dump($review_id, $user_id, $report_reason, $report_date);

include '../../db_connection.php';

// Get the POST data
$review_id = isset($_POST['review_id']) ? $_POST['review_id'] : '';
$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
$report_reason = isset($_POST['report_reason']) ? $_POST['report_reason'] : '';
$report_date = isset($_POST['report_date']) ? $_POST['report_date'] : '';

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO reports (review_id, user_id, report_reason, report_date) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $review_id, $user_id, $report_reason, $report_date);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Report submitted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
}

// Close connections
$stmt->close();
$conn->close();
?>
