<?php
session_start();
include_once '../../db_connection.php';

$response = ['isBookmarked' => false];

if (isset($_SESSION['user_id']) && isset($_GET['mountain_id'])) {
    $user_id = $_SESSION['user_id'];
    $mountain_id = intval($_GET['mountain_id']);

    $stmt = $conn->prepare("SELECT bookmark_id FROM bookmarks WHERE user_id = ? AND mountain_id = ?");
    $stmt->bind_param("ii", $user_id, $mountain_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $response['isBookmarked'] = $result->num_rows > 0;
    
    $stmt->close();
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response); 