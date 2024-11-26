<?php
include('../../db_connection.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $currentUserId = $_SESSION['user_id'];

    // Check if email exists for any other user
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND user_id != ?");
    $stmt->bind_param("si", $email, $currentUserId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode(['exists' => $count > 0]);
} 