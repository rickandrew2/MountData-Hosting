<?php
include('../../check_login.php');

include('../../db_connection.php');



// Get and sanitize form data
$name = htmlspecialchars(trim($_POST['name']));
$email = htmlspecialchars(trim($_POST['email']));
$subject = htmlspecialchars(trim($_POST['subject']));
$message = htmlspecialchars(trim($_POST['message']));

// Input validation
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
    exit;
}

// Check input lengths
if (strlen($name) > 100 || strlen($email) > 100 || strlen($subject) > 200 || strlen($message) > 1000) {
    echo json_encode(['status' => 'error', 'message' => 'Input exceeds maximum length']);
    exit;
}

// Get user_id only if user is logged in
$user_id = isLoggedIn() ? $_SESSION['user_id'] : null;
$status = 'pending'; // Default status

// Prepare the SQL insert statement
$sql = "INSERT INTO inquiries (user_id, name, email, subject, message, inquiry_date, status) VALUES (?, ?, ?, ?, ?, NOW(), ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    // Bind parameters
    $stmt->bind_param("isssss", $user_id, $name, $email, $subject, $message, $status);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Inquiry submitted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error preparing statement: ' . $conn->error]);
}

// Close the connection
$conn->close();
?>
