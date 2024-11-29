<?php
include '../../db_connection.php';

function fetchFilteredReviews($conn, $mountain_id, $searchQuery = '', $ratingFilter = '', $dateFilter = '') {
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
        $params[] = intval($ratingFilter);
        $types .= "i";
    }

    if (!empty($dateFilter)) {
        switch($dateFilter) {
            case '7':
                $sql .= " AND r.review_date >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)";
                break;
            case '30':
                $sql .= " AND r.review_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)";
                break;
        }
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
    $mountain_id = isset($_GET['mountain_id']) ? intval($_GET['mountain_id']) : '';
    $searchQuery = $_GET['search'] ?? '';
    $ratingFilter = isset($_GET['rating']) ? intval($_GET['rating']) : '';
    $dateFilter = $_GET['date'] ?? '';

    $reviews = fetchFilteredReviews($conn, $mountain_id, $searchQuery, $ratingFilter, $dateFilter);

    if (empty($reviews)) {
        echo json_encode(['status' => 'no_results']);
    } else {
        echo json_encode(['status' => 'success', 'reviews' => $reviews]);
    }
}

$conn->close();
?> 