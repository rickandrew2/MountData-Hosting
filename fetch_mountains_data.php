<?php
    include_once 'check_login.php';

    function fetchMountainData($conn, $mountain_id) {
        $sql = "SELECT `mountain_id`, `name`, `location`, `elevation`, `mountain_image`, `difficulty_level`, `description`, `latitude`, `longitude`, `contact_number`
                FROM `mountains` WHERE `mountain_id` = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $mountain_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }
        return null;
    }

    function fetchRatingsData($conn, $mountain_id) {
        $ratingsQuery = "SELECT rating, COUNT(*) as count FROM reviews WHERE mountain_id = ? GROUP BY rating";
        $stmt = $conn->prepare($ratingsQuery);
        $stmt->bind_param('i', $mountain_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        $totalReviews = 0;
        $totalRatingSum = 0;

        while ($row = $result->fetch_assoc()) {
            $ratingCounts[$row['rating']] = $row['count'];
            $totalReviews += $row['count'];
            $totalRatingSum += $row['rating'] * $row['count'];
        }

        $averageRating = $totalReviews > 0 ? round($totalRatingSum / $totalReviews, 1) : 0;
        return ['averageRating' => $averageRating, 'totalReviews' => $totalReviews];
    }

    function getMountainProfile($conn, $mountain_id) {
        $mountainData = fetchMountainData($conn, $mountain_id);
        if ($mountainData) {
            $ratingsData = fetchRatingsData($conn, $mountain_id);
            return [
                'mountainData' => $mountainData,
                'ratingsData' => $ratingsData
            ];
        }
        return null;
    }
?>
