<?php
include_once ('../../check_login.php'); // This will check if the user is logged in
include ('../../db_connection.php'); // Include the database connection

// Initialize an array to hold validation errors
$errors = [];

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the posted data
    $newUsername = htmlspecialchars($_POST['editName']);
    $newEmail = htmlspecialchars($_POST['editEmail']);
    $newPassword = htmlspecialchars($_POST['editPassword']);
    $originalUsername = htmlspecialchars($_POST['originalUsername']);
    $originalEmail = htmlspecialchars($_POST['originalEmail']);

    // Check if the new username already exists, excluding the current user
    $checkUsernameQuery = "SELECT COUNT(*) FROM users WHERE username = ? AND user_id != ?";
    $stmt = $conn->prepare($checkUsernameQuery);
    $stmt->bind_param("si", $newUsername, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($usernameCount);
    $stmt->fetch();
    $stmt->close();

    // Check if the new email already exists, excluding the current user
    $checkEmailQuery = "SELECT COUNT(*) FROM users WHERE email = ? AND user_id != ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("si", $newEmail, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($emailCount);
    $stmt->fetch();
    $stmt->close();

    if ($usernameCount > 0) {
        // If the username already exists, redirect with an error message
        header("Location: profile.php?message=username_error");
        exit();
    } elseif ($emailCount > 0) {
        // If the email already exists, redirect with an error message
        header("Location: profile.php?message=email_error");
        exit();
    }

    // If no errors, proceed with the update
    if (empty($errors)) {
        // Initialize the update query
        $query = "UPDATE users SET username = ?, email = ?";
        $params = [$newUsername, $newEmail];

        // Check if a new password was provided
        if (!empty($newPassword)) {
            $query .= ", password = ?";
            $params[] = password_hash($newPassword, PASSWORD_DEFAULT); // Hash the password
        }

        $query .= " WHERE user_id = ?"; // Assuming you have a user_id field to identify the user
        $params[] = $_SESSION['user_id']; // Use the logged-in user's ID

        // Prepare and execute the statement
        if ($stmt = $conn->prepare($query)) {
            $stmt->execute($params);
            $stmt->close();
        }

        // Redirect with a success message
        header("Location: profile.php?message=success");
        exit();
    }
}
?>
