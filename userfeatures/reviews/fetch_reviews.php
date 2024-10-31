<?php
// fetch_reviews.php
include 'check_login.php'; // Check if user is logged in
include 'db_connection.php'; // Include database connection

if (isset($_GET['mountain_id']) && is_numeric($_GET['mountain_id'])) {
    $mountain_id = intval($_GET['mountain_id']); // Sanitize mountain_id

    // Prepare and execute the query to fetch reviews
    $sql = "SELECT `user_name`, `review_date`, `rating`, `comment` 
            FROM `reviews` WHERE `mountain_id` = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $mountain_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = [
                'user_name' => htmlspecialchars($row['user_name']),
                'review_date' => htmlspecialchars($row['review_date']),
                'rating' => intval($row['rating']),
                'comment' => htmlspecialchars($row['comment']),
            ];
        }

        // Return JSON response
        echo json_encode($reviews);
    } else {
        echo json_encode(['error' => 'Error preparing the SQL statement.']);
    }
} else {
    echo json_encode(['error' => 'No valid mountain ID provided.']);
}

$conn->close();
?>