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
            // Log the visit to mountain_log_history if user is logged in
            if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
                $last_visit_time = null; // Initialize the variable to avoid errors
    
                // Check if the last visit was over an hour ago
                $stmt = $conn->prepare("SELECT visit_time FROM mountain_log_history WHERE user_id = ? AND mountain_id = ? ORDER BY visit_time DESC LIMIT 1");
                $stmt->bind_param("ii", $user_id, $mountain_id);
                $stmt->execute();
                $stmt->bind_result($last_visit_time);
                $stmt->fetch();
                $stmt->close();
    
                // Only insert a new log entry if the last visit was over an hour ago
                if (!$last_visit_time || strtotime($last_visit_time) < strtotime('-1 hour')) {
                    $visit_time = date('Y-m-d H:i:s');
                    $stmt = $conn->prepare("INSERT INTO mountain_log_history (user_id, mountain_id, visit_time) VALUES (?, ?, ?)");
                    $stmt->bind_param("iis", $user_id, $mountain_id, $visit_time);
    
                    if (!$stmt->execute()) {
                        error_log("Failed to insert log: " . $stmt->error); // Log any error if insert fails
                    }
                    $stmt->close();
                }
            }
    
            $ratingsData = fetchRatingsData($conn, $mountain_id);
            return [
                'mountainData' => $mountainData,
                'ratingsData' => $ratingsData
            ];
        }
        return null;
    }
    
?>
