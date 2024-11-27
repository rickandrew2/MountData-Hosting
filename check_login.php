<?php
// check_login.php

// Only start session if one hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']); // Check if session 'user_id' exists
}

// Function to get user profile image path
function getUserImagePath() {
    // Ensure the correct path is returned based on the user session
    if (isLoggedIn()) {
        return '/images/profile_images/' . basename($_SESSION['image_path']); // Assuming image_path stores the image filename
    }
    return '/images/default-profile.png'; // Default image path if not logged in
}

// Function to get user's name
function getUserName() {
    if (isLoggedIn()) {
        // Add debug output to check what's actually in the session
        error_log("Session username: " . (isset($_SESSION['username']) ? $_SESSION['username'] : 'not set'));
        return isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest';
    }
    return 'Guest';
}

function getUserContactNumber() {
    if (isLoggedIn()) {
        // Add debug output to check what's actually in the session
        error_log("Session contact_number: " . (isset($_SESSION['contact_number']) ? $_SESSION['contact_number'] : 'not set'));
        return isset($_SESSION['contact_number']) ? htmlspecialchars($_SESSION['contact_number']) : '';
    }
    return '';
}

// Create a variable to store login status
$loginStatus = isLoggedIn();

// Embed user_id in JavaScript if logged in
$user_id = $loginStatus ? $_SESSION['user_id'] : 'null'; // Use 'null' if not logged in

// Use JSON_encode for safer JavaScript output
echo "<script>var userId = " . json_encode($user_id) . ";</script>";

// Preserve session across admin actions if needed
if (isset($_SESSION['admin_id']) && isset($_SESSION['user_id'])) {
    $_SESSION['preserve_session'] = true;
}

?>

<script>
    // Pass the image path to JavaScript

</script>