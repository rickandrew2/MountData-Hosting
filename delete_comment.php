<?php
session_start(); // Start the session to access $_SESSION['user_id']

// Include your database connection file
include('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Check if the comment ID is passed via GET request
if (isset($_GET['id'])) {
    $review_id = $_GET['id'];
    $currentUserId = $_SESSION['user_id'];

    // Prepare a query to fetch the review
    $sql = "SELECT user_id FROM reviews WHERE review_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $review_id);
    $stmt->execute();
    $stmt->bind_result($reviewUserId);
    $stmt->fetch();
    $stmt->close();

    // Check if the current user is the owner of the comment
    if ($reviewUserId == $currentUserId) {
        // Proceed to delete the comment
        $deleteSql = "DELETE FROM reviews WHERE review_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $review_id);

        if ($deleteStmt->execute()) {
            // Redirect to the reviews page without a success message
            header("Location: mountains_profiles.php?mountain_id=" . $_GET['mountain_id']);
        } else {
            // Redirect without an error message
            header("Location: mountains_profiles.php?mountain_id=" . $_GET['mountain_id']);
        }
        
        $deleteStmt->close();
        } else {
            // Redirect if the user is not authorized to delete the comment
            header("Location: mountains_profiles.php?mountain_id=" . $_GET['mountain_id']);
        }
        } else {
            // Redirect if no comment ID is provided
            header("Location: mountains_profiles.php?mountain_id=" . $_GET['mountain_id']);
        }
        
// Close the database connection
$conn->close();
?>
