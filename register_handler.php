<?php
// Start session
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'mountain_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input
    $username = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $contact_number = htmlspecialchars(trim($_POST['contact_number']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Debugging line
    var_dump($username, $email, $contact_number, $password, $confirm_password);

    // First, check if email already exists
    $check_email = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check_email->bind_param('s', $email);
    $check_email->execute();
    $result = $check_email->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['register_error'] = "Email already exists. Please use a different email.";
        header('Location: register.php');
        exit;
    }
    $check_email->close();

    // Server-side email validation for Gmail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $email)) {
        $_SESSION['register_error'] = "Please enter a valid Gmail address.";
        header('Location: register.php');
        exit;
    }

    // Validate Philippine mobile number format
    if (!preg_match('/^(09|\+639)\d{9}$/', $contact_number)) {
        $_SESSION['register_error'] = "Please enter a valid Philippine mobile number.";
        header('Location: register.php');
        exit;
    }

    // Server-side password validation
    if (!preg_match('/^(?=.*[A-Z])(?=.*[!@#$%^&*?])[A-Za-z\d!@#$%^&*?]{5,}$/', $password)) {
        $_SESSION['register_error'] = "Password must be at least 5 characters long, with at least one uppercase letter and one symbol.";
        header('Location: register.php');
        exit;
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['register_error'] = "Passwords do not match.";
        header('Location: register.php');
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Set default image path
    $image_path = '/images/profile_images/user-icon.png';

    // Check if the image file exists
    if (!file_exists($image_path) || empty($image_path)) {
        // Set a default image if the file does not exist
        $image_path = '/images/profile_images/default-user-icon.png';
    }

    // Store the data in the database
    $stmt = $conn->prepare("INSERT INTO users (username, email, contact_number, password, image_path, status, created_at) VALUES (?, ?, ?, ?, ?, 'active', NOW())");
    $stmt->bind_param('sssss', $username, $email, $contact_number, $hashed_password, $image_path);

    if ($stmt->execute()) {
        $user_id = $conn->insert_id;

        // Set session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['image_path'] = $image_path;

        // Debug information
        error_log("User registered successfully - ID: $user_id, Username: $username");

        $_SESSION['register_success'] = "Registration successful! You can now log in.";
        header('Location: login.php');
        exit;
    } else {
        error_log("Registration failed: " . $stmt->error);
        $_SESSION['register_error'] = "Registration failed. Please try again.";
        header('Location: register.php');
        exit;
    }

    $stmt->close();
}

$conn->close();
