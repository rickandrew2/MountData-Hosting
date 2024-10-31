<?php
// Start session to retrieve the logged-in user's information
session_start();

// Check if the user is logged in (you can add your own login session validation)
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Include database connection
include('db_connection.php');

// Fetch user's current information from the database (example: username, email)
$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Handle the update form submission
if (isset($_POST['update'])) {
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_password = mysqli_real_escape_string($conn, $_POST['password']);

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update user data in the database
    $update_query = "UPDATE users SET username='$new_username', email='$new_email', password='$hashed_password' WHERE username='$username'";
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['username'] = $new_username; // Update session username
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Update Profile</h2>
            <!-- Display success or error messages -->
            <?php if (isset($success_message)) { echo "<p class='success-message'>$success_message</p>"; } ?>
            <?php if (isset($error_message)) { echo "<p class='error-message'>$error_message</p>"; } ?>
            
            <form action="update_profile.php" method="POST">
                <!-- Pre-fill with current user info -->
                <input type="text" name="username" value="<?php echo $user['username']; ?>" required>
                <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
                <input type="password" name="password" placeholder="New Password" required>
                <button type="submit" name="update">Update Profile</button>
            </form>
            <!-- Back button -->
            <div class="register-link">
                <a href="profile.php">Back to Profile</a>
            </div>
        </div>
    </div>
</body>
</html>
