<?php
// Database connection
$servername = "localhost";  
$username = "root";        
$password = "";        
$dbname = "mountain_db";     

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get and sanitize form data
$name = htmlspecialchars(trim($_POST['name']));
$email = htmlspecialchars(trim($_POST['email']));
$subject = htmlspecialchars(trim($_POST['subject']));
$message = htmlspecialchars(trim($_POST['message']));
$user_id = 1; // Assuming you get the user_id from session or another method
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
