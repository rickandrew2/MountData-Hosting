<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in to view notifications";
    exit;
}

$user_id = $_SESSION['user_id'];

// Modified query to get distinct likes and prevent duplicates
$sql = "SELECT 
    n.notification_id,
    n.review_id,
    n.notification_type,
    n.message,
    MIN(n.is_read) as is_read,
    MAX(n.created_at) as latest_created_at,
    r.comment,
    GROUP_CONCAT(DISTINCT u.username) as likers,
    COUNT(DISTINCT n.triggered_by_user_id) as liker_count
    FROM (
        SELECT DISTINCT notification_id, review_id, notification_type, message, is_read, created_at, triggered_by_user_id, user_id
        FROM notifications
        WHERE user_id = ?
        AND (
            (notification_type = 'like' AND triggered_by_user_id != ?)
            OR notification_type = 'admin'
        )
    ) n 
    LEFT JOIN reviews r ON n.review_id = r.review_id 
    LEFT JOIN users u ON n.triggered_by_user_id = u.user_id 
    GROUP BY 
        CASE 
            WHEN n.notification_type = 'like' THEN n.review_id 
            ELSE n.notification_id
        END,
        n.notification_type,
        n.message
    ORDER BY latest_created_at DESC 
    LIMIT 10";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $unreadClass = $row['is_read'] ? '' : 'unread';
        $timeAgo = time_elapsed_string($row['latest_created_at']);
        
        if ($row['notification_type'] === 'like') {
            echo "<div class='notification-item {$unreadClass}'>";
            echo "<div class='notification-content'>";
            echo "<span class='material-symbols-outlined notification-icon'>favorite</span>";
            echo "<div class='notification-text'>";
            
            // Format likers list
            $likers = explode(',', $row['likers']);
            if ($row['liker_count'] <= 2) {
                echo "<strong>" . implode(' and ', $likers) . "</strong>";
            } else {
                echo "<strong>" . $likers[0] . ", " . $likers[1] . " and " . ($row['liker_count'] - 2) . " others</strong>";
            }
            
            echo " liked your review";
            if ($row['comment']) {
                $reviewPreview = strlen($row['comment']) > 50 ? 
                    substr($row['comment'], 0, 50) . "..." : 
                    $row['comment'];
                echo ": \"<span class='review-text'>{$reviewPreview}</span>\"";
            }
            echo "</div>";
            echo "</div>";
        } else if ($row['notification_type'] === 'admin') {
            echo "<div class='notification-item {$unreadClass} admin-notification'>";
            echo "<div class='notification-content'>";
            echo "<span class='material-symbols-outlined notification-icon' style='color: #ff0000;'>admin_panel_settings</span>";
            echo "<div class='notification-text'>";
            echo "<strong>Admin Notification</strong><br>";
            if ($row['message']) {
                echo "<p class='mb-1'>" . htmlspecialchars($row['message']) . "</p>";
            } else {
                // Fallback message if no specific message is stored
                echo "<p class='mb-1'>Your review has been deleted because it violated our community rules.</p>";
            }
            echo "</div>";
            echo "</div>";
        }
        
        echo "<span class='notification-time'>{$timeAgo}</span>";
        echo "</div>"; // Close notification-item
    }
} else {
    echo "<div class='notification-item text-center'>";
    echo "<p class='mb-0'>No notifications yet</p>";
    echo "</div>";
}

// Helper function to format time
function time_elapsed_string($datetime) {
    // Set timezone to your local timezone
    date_default_timezone_set('Asia/Manila'); // Adjust this to your timezone
    
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    // For debugging
    // echo "Now: " . $now->format('Y-m-d H:i:s') . "<br>";
    // echo "Ago: " . $ago->format('Y-m-d H:i:s') . "<br>";

    if ($diff->y > 0) {
        return $ago->format('M j, Y');
    } else if ($diff->m > 0) {
        return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    } else if ($diff->d > 7) {
        return $ago->format('M j');
    } else if ($diff->d > 0) {
        return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    } else if ($diff->h > 0) {
        return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    } else if ($diff->i > 0) {
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    } else {
        return 'Just now';
    }
}

$conn->close();
?> 