<?php
include_once '../../db_connection.php';
include 'fetch_reviews.php';

$mountain_id = isset($_GET['mountain_id']) ? intval($_GET['mountain_id']) : 0;
$ratings = fetchRatings($conn, $mountain_id);
$averageRating = calculateAverageRating($ratings);

ob_start();
displayRatings($ratings);
$ratingsHtml = ob_get_clean();

ob_start();
?>
<h1 style="font-size: 5rem; color: #006400;"><?php echo $averageRating; ?></h1>
<div><?php displayAverageStars($averageRating); ?></div>
<h5>(<?php echo $ratings['totalReviews']; ?>) <?php echo ($ratings['totalReviews'] == 1) ? 'Review' : 'Reviews'; ?></h5>
<?php
$totalRatingHtml = ob_get_clean();

header('Content-Type: application/json');
echo json_encode([
    'ratingsHtml' => $ratingsHtml,
    'totalRatingHtml' => $totalRatingHtml
]);
?> 