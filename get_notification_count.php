<?php
function getUnreadNotificationCount($user_id) {
    include 'db_connection.php';
    
    $sql = "SELECT COUNT(*) as unread_count 
            FROM notifications n
            WHERE n.user_id = ? 
            AND n.is_read = 0
            AND (
                (n.notification_type = 'like' AND n.triggered_by_user_id != ?) 
                OR n.notification_type = 'admin'
            )";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $unread_count = $row['unread_count'];
    
    $conn->close();
    return $unread_count;
}
?> 