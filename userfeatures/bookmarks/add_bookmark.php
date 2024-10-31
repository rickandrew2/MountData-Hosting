<?php
include_once '../../db_connection.php';
include_once '../../check_login.php'; // Assuming this file sets the logged-in user's ID in $user_id

// Check if the user is logged in and has a valid mountain_id
if (isset($_SESSION['user_id']) && isset($_GET['mountain_id']) && is_numeric($_GET['mountain_id'])) {
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID
    $mountain_id = intval($_GET['mountain_id']); // Sanitize mountain_id

    // Prepare SQL statement to check if the bookmark already exists
    $checkSql = "SELECT `bookmark_id` FROM `bookmarks` WHERE `user_id` = ? AND `mountain_id` = ?";
    $stmt = $conn->prepare($checkSql);
    
    if ($stmt) {
        $stmt->bind_param("ii", $user_id, $mountain_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if bookmark exists
        if ($result->num_rows > 0) {
            // Bookmark exists, proceed to remove it
            $deleteSql = "DELETE FROM `bookmarks` WHERE `user_id` = ? AND `mountain_id` = ?";
            $deleteStmt = $conn->prepare($deleteSql);
            if ($deleteStmt) {
                $deleteStmt->bind_param("ii", $user_id, $mountain_id);
                if ($deleteStmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Bookmark removed.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error removing bookmark.']);
                }
                $deleteStmt->close();
            }
        } else {
            // Bookmark does not exist, proceed to add it
            $insertSql = "INSERT INTO `bookmarks` (`user_id`, `mountain_id`, `bookmark_date`) VALUES (?, ?, NOW())";
            $insertStmt = $conn->prepare($insertSql);
            if ($insertStmt) {
                $insertStmt->bind_param("ii", $user_id, $mountain_id);
                if ($insertStmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Bookmark added.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error adding bookmark.']);
                }
                $insertStmt->close();
            }
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error preparing the SQL statement.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}

$conn->close(); // Close the database connection
?>