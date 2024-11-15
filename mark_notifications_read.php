<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$conn->close();
echo "success";
?> 