<?php
include_once('../../check_login.php'); // This will check if the user is logged in
include('../../db_connection.php'); // Include the database connection

// Initialize an array to hold validation errors
$errors = [];

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the posted data
    $newUsername = htmlspecialchars($_POST['editName']);
    $newEmail = htmlspecialchars($_POST['editEmail']);
    $newContact = htmlspecialchars($_POST['editContact']);
    $newPassword = htmlspecialchars($_POST['editPassword']);
    $originalUsername = htmlspecialchars($_POST['originalUsername']);
    $originalEmail = htmlspecialchars($_POST['originalEmail']);
    $originalContact = htmlspecialchars($_POST['originalContact']);

    // Validate email (Gmail only)
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $newEmail)) {
        header("Location: profile.php?message=invalid_email");
        exit();
    }

    // Validate contact number (Philippine format)
    if (!preg_match('/^(09|\+639)\d{9}$/', $newContact)) {
        header("Location: profile.php?message=invalid_contact");
        exit();
    }

    // Validate password if provided
    if (!empty($newPassword)) {
        if (!preg_match('/^(?=.*[A-Z])(?=.*[!@#$%^&*?])[A-Za-z\d!@#$%^&*?]{5,}$/', $newPassword)) {
            header("Location: profile.php?message=invalid_password");
            exit();
        }
    }

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

    // Validate email format (Gmail only)
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $newEmail)) {
        header("Location: profile.php?message=invalid_email");
        exit();
    }

    // Check if email already exists
    if ($emailCount > 0) {
        header("Location: profile.php?message=email_error");
        exit();
    }

    if ($usernameCount > 0) {
        // If the username already exists, redirect with an error message
        header("Location: profile.php?message=username_error");
        exit();
    }

    // If no errors, proceed with the update
    if (empty($errors)) {
        // Initialize the update query
        $query = "UPDATE users SET username = ?, email = ?, contact_number = ?";
        $params = [$newUsername, $newEmail, $newContact];
        $types = "sss";

        // Add password to update if provided
        if (!empty($newPassword)) {
            $query .= ", password = ?";
            $params[] = password_hash($newPassword, PASSWORD_DEFAULT);
            $types .= "s";
        }

        $query .= " WHERE user_id = ?";
        $params[] = $_SESSION['user_id'];
        $types .= "i";

        // Prepare and execute the statement
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                // Update session variables
                $_SESSION['username'] = $newUsername;
                $_SESSION['contact_number'] = $newContact; // Update contact number in session

                $stmt->close();
                header("Location: profile.php?message=success");
                exit();
            } else {
                $stmt->close();
                header("Location: profile.php?message=update_error");
                exit();
            }
        }
    }
}
