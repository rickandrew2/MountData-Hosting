<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in to view notifications";
    exit;
}

$user_id = $_SESSION['user_id'];

// Modified query to correctly join tables and get the liker's information
$sql = "SELECT n.notification_id, 
        n.notification_type, 
        n.is_read,
        n.created_at,
        n.review_id, 
        r.comment, 
        l.user_id as liker_id,
        u.username as liker_username,
        dr.comment as deleted_review_comment 
        FROM notifications n 
        LEFT JOIN reviews r ON n.review_id = r.review_id 
        LEFT JOIN likes l ON r.review_id = l.review_id 
        LEFT JOIN users u ON l.user_id = u.user_id 
        LEFT JOIN reviews dr ON n.review_id = dr.review_id 
        WHERE n.user_id = ? 
        AND (
            (n.notification_type = 'like' AND l.user_id != ?) -- Exclude self-likes
            OR n.notification_type = 'admin' -- Always show admin notifications
        )
        GROUP BY n.notification_id 
        ORDER BY n.created_at DESC 
        LIMIT 10";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $unreadClass = $row['is_read'] ? '' : 'unread';
        $timeAgo = time_elapsed_string($row['created_at']);
        
        if ($row['notification_type'] === 'like') {
            echo "<div class='notification-item {$unreadClass}'>";
            echo "<div class='notification-content'>";
            echo "<span class='material-symbols-outlined notification-icon'>favorite</span>";
            echo "<div class='notification-text'>";
            echo "<strong>{$row['liker_username']}</strong> liked your review";
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
            echo "<span class='material-symbols-outlined notification-icon'>admin_panel_settings</span>";
            echo "<div class='notification-text'>";
            echo "<strong>Admin Notification</strong><br>";
            echo "Your review has been deleted because it violated our community rules";
            if ($row['deleted_review_comment']) {
                $reviewPreview = strlen($row['deleted_review_comment']) > 50 ? 
                    substr($row['deleted_review_comment'], 0, 50) . "..." : 
                    $row['deleted_review_comment'];
                echo "<span class='review-text'>{$reviewPreview}</span>";
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