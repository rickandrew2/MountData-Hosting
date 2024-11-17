$(document).ready(function() {
    // Function to load notifications
    function loadNotifications() {
        $.ajax({
            url: '/get_notifications.php',
            method: 'GET',
            success: function(data) {
                $('#notificationsContent').html(data);

                // Update notification count via AJAX
                $.ajax({
                    url: '/get_unread_count.php',
                    method: 'GET',
                    success: function(count) {
                        if (count > 0) {
                            $('.notification-count').text(count).show();
                        } else {
                            $('.notification-count').hide();
                        }
                    }
                });
            }
        });
    }

    // Function to update notification counts
    function updateNotificationCounts() {
        $.ajax({
            url: '/get_unread_count.php',
            method: 'GET',
            success: function(count) {
                if (count > 0) {
                    $('.notification-count, .profile-notification-count').text(count).show();
                } else {
                    $('.notification-count, .profile-notification-count').hide();
                }
            }
        });
    }

    // Load notifications when modal is opened
    $('#notificationsModal').on('show.bs.modal', function() {
        loadNotifications();

        // Mark notifications as read when modal is opened
        $.ajax({
            url: '/mark_notifications_read.php',
            method: 'POST',
            success: function() {
                // Hide both notification count badges after marking as read
                $('.notification-count, .profile-notification-count').hide();
            }
        });
    });

    // Update notification counts periodically (every 30 seconds)
    setInterval(updateNotificationCounts, 30000);
}); 