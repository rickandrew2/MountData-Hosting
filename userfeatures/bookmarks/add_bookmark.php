<?php
session_start();
include '../../db_connection.php';

header('Content-Type: application/json'); // Set content type to JSON

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$mountain_id = isset($_GET['mountain_id']) ? intval($_GET['mountain_id']) : 0;

if ($mountain_id === 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid mountain ID'
    ]);
    exit;
}

// Check if bookmark exists
$check_sql = "SELECT * FROM bookmarks WHERE user_id = ? AND mountain_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $user_id, $mountain_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    // Bookmark exists, so remove it
    $delete_sql = "DELETE FROM bookmarks WHERE user_id = ? AND mountain_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $user_id, $mountain_id);
    
    if ($delete_stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Bookmark removed',
            'isBookmarked' => false
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to remove bookmark'
        ]);
    }
    $delete_stmt->close();
} else {
    // Bookmark doesn't exist, so add it
    $insert_sql = "INSERT INTO bookmarks (user_id, mountain_id) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ii", $user_id, $mountain_id);
    
    if ($insert_stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Bookmark added',
            'isBookmarked' => true
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add bookmark'
        ]);
    }
    $insert_stmt->close();
}

$check_stmt->close();
$conn->close();
?>