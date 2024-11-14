<?php
include_once '../../check_login.php';
include '../../db_connection.php';
include 'fetch_reviews.php';

$mountain_id = isset($_GET['mountain_id']) ? intval($_GET['mountain_id']) : 0;
$currentPage = getCurrentPage();
$reviewsPerPage = 5;
$totalReviews = fetchTotalReviews($conn, $mountain_id);
$totalPages = ceil($totalReviews / $reviewsPerPage);
$offset = ($currentPage - 1) * $reviewsPerPage;

$reviews = fetchReviews($conn, $mountain_id, $reviewsPerPage, $offset);

if ($loginStatus) {
    displayReviews($reviews);
} else {
    displayLoginPrompt();
}

displayPagination($currentPage, $totalPages, $mountain_id);
?> 