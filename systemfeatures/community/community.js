document.addEventListener('DOMContentLoaded', () => {
    const likeContainers = document.querySelectorAll('.like-container');

    likeContainers.forEach(container => {
        const icon = container.querySelector('.icon'); // Select the icon element

        container.addEventListener('click', () => {
            icon.classList.toggle('liked'); // Toggle the liked class on the icon
            // Logic to update the like count can go here
        });
    });
});

function confirmDeletion(reviewId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            // Send AJAX request to delete_comment.php
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../../userfeatures/report/delete.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'The post has been deleted successfully.',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            location.reload(); // Reload to update the page after deletion
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to delete the comment.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                }
            };
            xhr.send('review_id=' + encodeURIComponent(reviewId)); // Send the reviewId to delete_comment.php
        }
    });
}


function confirmReport(reviewId, userId) {
    // Use SweetAlert2 for the confirmation
    Swal.fire({
        title: 'Report Review',
        text: 'Select a reason for reporting this review:',
        icon: 'warning',
        input: 'select',
        inputOptions: {
            'Inappropriate Content': 'Inappropriate Content',
            'Spam': 'Spam',
            'Offensive Language': 'Offensive Language',
            'Other': 'Other'
        },
        inputPlaceholder: 'Choose a reason',
        showCancelButton: true,
        confirmButtonText: 'Report',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            const reportReason = result.value; // Get the selected value

            if (!reportReason) {
                // If no reason is selected, show an error message
                Swal.fire({
                    title: 'Error!',
                    text: 'You must select a reason for reporting.',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
                return; // Exit the function if no reason is selected
            }

            // Create an AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../../userfeatures/report/report.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            // Define the data to be sent
            const data = 'review_id=' + encodeURIComponent(reviewId) +
                         '&user_id=' + encodeURIComponent(userId) +
                         '&report_reason=' + encodeURIComponent(reportReason) + // Use the selected reason
                         '&report_date=' + encodeURIComponent(new Date().toISOString());

            // Handle the response from the server
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        Swal.fire({
                            title: 'Reported!',
                            text: 'The review has been reported successfully.',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        });
                        // Optionally, you can refresh the page or update the UI here
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'There was an error reporting the review: ' + xhr.responseText,
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                }
            };

            // Send the request
            xhr.send(data);
        }
    });
}

$(document).ready(function () {
    // Handle like button click
    $(".like-button").click(function () {
        var reviewId = $(this).data("review-id");
        var userId = $(this).data("user-id");
        var hasLiked = $(this).data("likes") === 'true';

        // Send AJAX request to like_handler.php
        $.ajax({
            url: "like_handler.php", // Adjust the path as needed
            type: "POST",
            data: {
                review_id: reviewId
            },
            success: function (response) {
                var data = JSON.parse(response);
                
                if (data.status === 'liked') {
                    // Update the like button to filled state
                    $(".like-button[data-review-id='" + reviewId + "'] .heart").hide();
                    $(".like-button[data-review-id='" + reviewId + "'] .heart-filled").show();
                    $(this).data("likes", 'true');
                } else {
                    // Update the like button to unfilled state
                    $(".like-button[data-review-id='" + reviewId + "'] .heart").show();
                    $(".like-button[data-review-id='" + reviewId + "'] .heart-filled").hide();
                    $(this).data("likes", 'false');
                }

                // Update the like count on the page
                $(".like-count[data-review-id='" + reviewId + "']").text(data.like_count);
            }
        });
    });
});