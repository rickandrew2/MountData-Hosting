<?php
// session.php
session_start(); // Start the session

// Function to check if the user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['username']); // Check if the username session variable is set
}

// Function to get user image path
function getUserImagePath() {
    return isUserLoggedIn() ? $_SESSION['image_path'] : '/images/default-profile.png'; // Default image path
}
?>