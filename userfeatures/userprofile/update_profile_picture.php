<?php

include '../../db_connection.php'; // Ensure correct database connection
include '../../check_login.php'; // Ensure user is logged in

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
    $user_id = $_SESSION['user_id']; // Ensure user is logged in
    $target_dir = __DIR__ . "/../../images/profile_images/";
    $target_file = $target_dir . basename($_FILES['profile_image']['name']);
    $target_file_relative = "/../../images/profile_images/" . basename($_FILES['profile_image']['name']);
    
    // Basic file checks
    $check = getimagesize($_FILES['profile_image']['tmp_name']);
    if ($check === false) {
        echo "Error: File is not an image.";
        exit;
    }

    if ($_FILES['profile_image']['size'] > 5000000) { // Allow up to 5 MB
        echo "Error: File is too large.";
        exit;
    }

    // Move the file and update the database
    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
        $sql = "UPDATE users SET image_path = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $target_file_relative, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['image_path'] = $target_file_relative;
            echo "Profile picture updated successfully.";
        } else {
            echo "Error updating profile picture.";
        }
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "No file uploaded.";
}
?>
