<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'mountain_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $_SESSION['error_message'] = "Passwords do not match.";
        header('Location: reset_password.php?token=' . $token);
        exit;
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update the password and clear the reset token
    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ?");
    $stmt->bind_param('ss', $hashed_password, $token);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Password reset successful! You can now log in.";
        header('Location: /login/login.php' );
    } else {
        $_SESSION['error_message'] = "Password reset failed. Please try again.";
        header('Location: reset_password.php?token=' . $token);
    }
    $stmt->close();
}
$conn->close();
?>
