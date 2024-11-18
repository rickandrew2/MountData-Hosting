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
                            location.reload();
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
            xhr.send('review_id=' + encodeURIComponent(reviewId));
        }
    });
}

function confirmReport(reviewId, userId) {
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
            const reportReason = result.value;

            if (!reportReason) {
                Swal.fire({
                    title: 'Error!',
                    text: 'You must select a reason for reporting.',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../../userfeatures/report/report.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            const data = 'review_id=' + encodeURIComponent(reviewId) +
                        '&user_id=' + encodeURIComponent(userId) +
                        '&report_reason=' + encodeURIComponent(reportReason) +
                        '&report_date=' + encodeURIComponent(new Date().toISOString());

            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        Swal.fire({
                            title: 'Reported!',
                            text: 'The review has been reported successfully.',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        });
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

            xhr.send(data);
        }
    });
}

$(document).ready(function () {
    // Use event delegation for like button clicks
    $(document).on('click', '.like-button', function() {
        var reviewId = $(this).data("review-id");
        var userId = $(this).data("user-id");
        var hasLiked = $(this).data("likes") === 'true';
        var $likeButton = $(this);

        // Send AJAX request to like_handler.php
        $.ajax({
            url: "like_handler.php",
            type: "POST",
            data: {
                review_id: reviewId
            },
            success: function(response) {
                var data = JSON.parse(response);
                
                if (data.status === 'liked') {
                    // Update the like button to filled state
                    $likeButton.find('.heart').hide();
                    $likeButton.find('.heart-filled').show();
                    $likeButton.data("likes", 'true');
                } else {
                    // Update the like button to unfilled state
                    $likeButton.find('.heart').show();
                    $likeButton.find('.heart-filled').hide();
                    $likeButton.data("likes", 'false');
                }

                // Update the like count
                $(".like-count[data-review-id='" + reviewId + "']").text(
                    `${data.like_count} ${data.like_count <= 1 ? 'like' : 'likes'}`
                );
            }
        });
    });
});

// Initialize Bootstrap dropdowns
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });
});