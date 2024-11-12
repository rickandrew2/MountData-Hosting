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
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Debugging line
    var_dump($name, $email, $password, $confirm_password); // Check received data

    // Server-side email validation for Gmail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $email)) {
        $_SESSION['register_error'] = "Please enter a valid Gmail address.";
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

    // Set the image path to a variable
    $image_path = '/images/profile_images/user-icon.png';

    // Check if the image file exists
    if (!file_exists($image_path) || empty($image_path)) {
        // Set a default image if the file does not exist
        $image_path = '/images/profile_images/default-user-icon.png';
    }

    // Store the data in the database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, image_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $name, $email, $hashed_password, $image_path);

    if ($stmt->execute()) {
        // Fetch the last inserted ID for the user
        $user_id = $conn->insert_id;

        // Set session variables, including image_path
        $_SESSION['user_id'] = $user_id;
        $_SESSION['image_path'] = $image_path; // Store image path in session

        $_SESSION['register_success'] = "Registration successful! You can now log in.";
        header('Location: login.php');
        exit; // Ensure you exit after redirection
    } else {
        // Debugging output
        echo "Error: " . $stmt->error;
        $_SESSION['register_error'] = "Registration failed. Please try again.";
        header('Location: register.php');
        exit; // Ensure you exit after redirection
    }

    $stmt->close();
}

$conn->close();
