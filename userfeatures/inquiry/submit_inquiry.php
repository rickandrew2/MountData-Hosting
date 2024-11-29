<?php
// Prevent any output before JSON response
ob_start();

include('../../check_login.php');
include('../../db_connection.php');

// Verify reCAPTCHA first
$recaptcha_secret = "6LdUD18qAAAAAKdXud_mVA-MpUUwPu6stuY9ihXg";
$recaptcha_response = $_POST['g-recaptcha-response'];

$verify_url = "https://www.google.com/recaptcha/api/siteverify";
$data = [
    'secret' => $recaptcha_secret,
    'response' => $recaptcha_response
];

$options = [
    'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data)
    ]
];

$context = stream_context_create($options);
$verify_response = file_get_contents($verify_url, false, $context);
$response_data = json_decode($verify_response);

if (!$response_data->success) {
    // Clear any output buffers
    ob_clean();
    // Set JSON header
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Please complete the reCAPTCHA verification']);
    exit;
}

// Get and sanitize form data
$name = htmlspecialchars(trim($_POST['name']));
$email = htmlspecialchars(trim($_POST['email']));
$subject = htmlspecialchars(trim($_POST['subject']));
$message = htmlspecialchars(trim($_POST['message']));

// Input validation
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
    exit;
}

// Check input lengths
if (strlen($name) > 100 || strlen($email) > 100 || strlen($subject) > 200 || strlen($message) > 1000) {
    ob_clean();
    header('Content-Type: application/json');
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
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Inquiry submitted successfully!']);
    } else {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
    }

    // Close the statement
    $stmt->close();
} else {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Error preparing statement: ' . $conn->error]);
}

// Close the connection
$conn->close();
?>
