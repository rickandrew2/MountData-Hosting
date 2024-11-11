<?php
session_start();
include('../../db_connection.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Start transaction
    $conn->begin_transaction();

    // Get user's image path before deletion (to delete the file)
    $stmt = $conn->prepare("SELECT image_path FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    // Delete the user's profile picture if it exists and is not the default image
    if ($user && $user['image_path'] && $user['image_path'] != 'images/default.jpg') {
        $image_path = '../../' . $user['image_path'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // Delete user from database
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        // Commit transaction
        $conn->commit();
        
        // Clear all session data
        session_destroy();
        
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Failed to delete user");
    }
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
