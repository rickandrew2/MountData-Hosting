<?php
// Include the database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../db_connection.php';

// Get the POST data
$review_id = isset($_POST['review_id']) ? $_POST['review_id'] : '';
$reporter_id = isset($_POST['reporter_id']) ? $_POST['reporter_id'] : '';
$report_reason = isset($_POST['report_reason']) ? $_POST['report_reason'] : '';

try {
    // First check if this user has already reported this review
    $check_query = $conn->prepare("SELECT report_id FROM reports WHERE review_id = ? AND reporter_id = ?");
    $check_query->bind_param("ii", $review_id, $reporter_id);
    $check_query->execute();
    $result = $check_query->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'You have already reported this review'
        ]);
        exit;
    }

    // Get the review owner's ID from the reviews table
    $review_query = $conn->prepare("SELECT user_id FROM reviews WHERE review_id = ?");
    $review_query->bind_param("i", $review_id);
    $review_query->execute();
    $result = $review_query->get_result();
    $review_data = $result->fetch_assoc();
    
    if (!$review_data) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Review not found'
        ]);
        exit;
    }
    
    $review_owner_id = $review_data['user_id'];

    // Insert the new report
    $stmt = $conn->prepare("INSERT INTO reports (review_id, review_owner_id, reporter_id, report_reason, report_date, status) 
                          VALUES (?, ?, ?, ?, NOW(), 'To Be Reviewed')");
    $stmt->bind_param("iiis", $review_id, $review_owner_id, $reporter_id, $report_reason);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Report submitted successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error submitting report: ' . $stmt->error
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'An unexpected error occurred: ' . $e->getMessage()
    ]);
}

// Close connections
if (isset($check_query)) $check_query->close();
if (isset($review_query)) $review_query->close();
if (isset($stmt)) $stmt->close();
$conn->close();
?>
