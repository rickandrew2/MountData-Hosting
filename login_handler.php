<?php
session_start();
include 'db_connection.php'; // Include your database connection



// Check if POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username_or_email = trim($_POST['username']); // Sanitize input
    $password = $_POST['password'];
    $recaptchaResponse = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : ''; // Ensure reCAPTCHA response is set

    // Verify Google reCAPTCHA
    $secretKey = '6LfbrV4qAAAAAF72PNEOVbKvMXa7YRDr9EVyrGn9'; // Replace with your secret key
    $recaptchaURL = "https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}";

    // Make the reCAPTCHA request and handle potential errors
    $response = @file_get_contents($recaptchaURL); // Suppress error with @, but handle response below

    if ($response === FALSE) {
        // Handle potential errors in reCAPTCHA request
        $_SESSION['login_error'] = "Unable to verify reCAPTCHA. Please try again later.";
        header("Location: login.php");
        exit();
    }

    $responseKeys = json_decode($response, true);

    // If reCAPTCHA fails, return an error
    if (intval($responseKeys["success"]) !== 1) {
        $_SESSION['login_error'] = "Please complete the reCAPTCHA.";
        header("Location: login.php");
        exit();
    }

    // Prepare the SQL statement to select user data including image_path and status
    if ($stmt = $conn->prepare("SELECT user_id, username, password, image_path, status FROM users WHERE username = ? OR email = ?")) {
        $stmt->bind_param('ss', $username_or_email, $username_or_email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if a user with that username/email exists
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Check if user is banned
            if ($user['status'] === 'banned') {
                $_SESSION['login_error'] = 'banned';
                header("Location: login.php");
                exit();
            }

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Update last_login timestamp
                $update_stmt = $conn->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?");
                $update_stmt->bind_param('i', $user['user_id']);
                $update_stmt->execute();
                $update_stmt->close();

                // Password is correct, set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['image_path'] = $user['image_path'];
                $_SESSION['login_success'] = "Login successful! Welcome " . $user['username'] . ".";
                header("Location: index.php");
                exit();
            } else {
                // Password is incorrect
                $_SESSION['login_error'] = "Incorrect password. Please try again.";
                header("Location: login.php");
                exit();
            }
        } else {
            // Username or email does not exist
            $_SESSION['login_error'] = "Username or email does not exist.";
            header("Location: login.php");
            exit();
        }

        $stmt->close();
    } else {
        // Handle error preparing SQL statement
        $_SESSION['login_error'] = "Database error. Please try again later.";
        header("Location: login.php");
        exit();
    }

    $conn->close(); // Always close the database connection
}
?>
