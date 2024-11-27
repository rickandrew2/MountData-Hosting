<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to upload photos'
    ]);
    exit;
}

// Include database connection
require_once '../../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare variables
    $user_id = $_POST['user_id']; // Replace with dynamic value
    $mountain_id = $_POST['mountain_id']; // Replace with dynamic value
    $rating = $_POST['ratingValue'];
    $comment = $_POST['comment'];
    $tags = $_POST['tags']; // Get the tags from the POST request
    $review_date = date('Y-m-d H:i:s');
    $review_photos = [];

    // Handle file uploads
    if (isset($_FILES['photoUpload']) && $_FILES['photoUpload']['error'][0] !== UPLOAD_ERR_NO_FILE) {
        $uploadDir = 'reviews_images'; // Specify your upload directory

        // Create upload directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Create directory with proper permissions
        }

        foreach ($_FILES['photoUpload']['name'] as $key => $name) {
            if ($_FILES['photoUpload']['error'][$key] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['photoUpload']['tmp_name'][$key];
                $fileName = basename($name);
                $filePath = $uploadDir . '/' . $fileName; // Ensure the path includes a directory separator

                // Move uploaded file to the specified directory
                if (move_uploaded_file($tmpName, $filePath)) {
                    $review_photos[] = $filePath; // Store file path
                } else {
                    echo "Failed to upload file: " . $fileName;
                }
            } else {
                echo "Error uploading file: " . $name;
            }
        }
    }

    // Convert photo paths to a comma-separated string (or adjust as needed)
    $review_photos_string = implode(',', $review_photos);

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("INSERT INTO reviews (user_id, mountain_id, rating, comment, review_date, review_photo, tags) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssss", $user_id, $mountain_id, $rating, $comment, $review_date, $review_photos_string, $tags);

    if ($stmt->execute()) {
        echo "Review submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
