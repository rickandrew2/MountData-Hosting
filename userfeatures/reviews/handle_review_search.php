<?php
include '../../db_connection.php';

function fetchFilteredReviews($conn, $mountain_id, $searchQuery = '', $ratingFilter = '') {
    $sql = "SELECT r.review_id, r.user_id, r.rating, r.comment, r.review_date, r.review_photo, 
            u.username, u.image_path 
            FROM reviews r 
            JOIN users u ON r.user_id = u.user_id 
            WHERE r.mountain_id = ?";
    
    $params = [$mountain_id];
    $types = "i";

    if (!empty($searchQuery)) {
        $sql .= " AND r.comment LIKE ?";
        $params[] = "%$searchQuery%";
        $types .= "s";
    }

    if (!empty($ratingFilter)) {
        $sql .= " AND r.rating = ?";
        $params[] = $ratingFilter;
        $types .= "i";
    }

    $sql .= " ORDER BY r.review_date DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }

    return $reviews;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $mountain_id = $_GET['mountain_id'] ?? '';
    $searchQuery = $_GET['search'] ?? '';
    $ratingFilter = $_GET['rating'] ?? '';

    $reviews = fetchFilteredReviews($conn, $mountain_id, $searchQuery, $ratingFilter);

    if (empty($reviews)) {
        echo json_encode(['status' => 'no_results']);
    } else {
        echo json_encode(['status' => 'success', 'reviews' => $reviews]);
    }
}

$conn->close();
?> 