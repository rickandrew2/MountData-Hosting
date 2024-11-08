let activeTab = document.querySelector('.active');

function showSection(section) {
    const reviewsContent = document.getElementById('reviews');
    const photosContent = document.getElementById('photos');

    // Update the active tab
    if (section === 'reviews') {
        reviewsContent.style.display = 'block';
        photosContent.style.display = 'none';
        activeTab.classList.remove('active');
        activeTab = document.querySelector('.reviews-btn');
        activeTab.classList.add('active');
    } else {
        reviewsContent.style.display = 'none';
        photosContent.style.display = 'block';
        activeTab.classList.remove('active');
        activeTab = document.querySelector('.photos-btn');
        activeTab.classList.add('active');
    }

    // You can remove the underline logic as it's no longer needed
}

// Initialize the active tab
activeTab.classList.add('active'); // Ensure the active class is set correctly on the first load

// Open the lightbox and display the full-screen image
function openLightbox(imageSrc) {
    document.getElementById('lightbox').style.display = 'block';
    document.getElementById('lightbox-img').src = imageSrc;
    document.getElementById('lightbox-bg').style.backgroundImage = `url(${imageSrc})`; // Set blurred background
}

// Close the lightbox
function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
}


document.getElementById('writeReviewHeader').addEventListener('click', function() {
    // Check if the user is logged in
    if (!userId) {
        // Show login prompt if not logged in
        Swal.fire({
            title: 'Login Required',
            text: 'You need to log in to submit a review.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Login',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#28a745', // Green login button
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to login page (you can adjust the URL accordingly)
                window.location.href = '/login.php';
            }
        });
    } else {
        // If user is logged in, show the review submission form
        Swal.fire({
            title: 'Submit Your Review',
            html: `
            <form id="reviewForm" style="text-align: left; padding: 10px;">
              <div style="margin-bottom: 10px;">
                <label for="rating" style="display: block; font-weight: bold;">Rating:</label>
                <div id="starRating" style="font-size: 24px; color: gray; margin-bottom: 15px;">
                    <div class="star-rating">
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                    </div>
                </div>
                <input type="hidden" id="ratingValue" name="ratingValue" value="5"> <!-- Default rating -->
              </div>

              <div style="margin-bottom: 15px;">
                <label for="comment" style="display: block; font-weight: bold;">Comment (required):</label>
                <textarea id="comment" name="comment" placeholder="Write your comment" required style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;"></textarea>
              </div>

              <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" id="cancelButton" class="swal2-cancel swal2-styled" style="background-color: #d33; color: white; padding: 10px 20px; border-radius: 5px;">Cancel</button>
                <button type="submit" class="swal2-confirm swal2-styled" style="background-color: #28a745;; color: white; padding: 10px 20px; border-radius: 5px;">Submit Review</button>
              </div>
            </form>
            `,
            showConfirmButton: false, // Hide the default confirm button
        });

        // Initialize star rating functionality
        document.querySelectorAll('#starRating span').forEach((star, index) => {
            star.addEventListener('click', () => {
                document.querySelectorAll('#starRating span').forEach(s => s.style.color = 'gray');
                for (let i = 0; i <= index; i++) {
                    document.querySelectorAll('#starRating span')[i].style.color = '#28a745';
                }
                document.getElementById('ratingValue').value = index + 1;
            });
        });

        // Cancel button functionality
        document.getElementById('cancelButton').addEventListener('click', function() {
            Swal.close();
        });

        // Handle form submission
        document.getElementById('reviewForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            formData.append('user_id', userId); // Assuming userId is defined in your script
            formData.append('mountain_id', mountainId); // Assuming mountainId is defined in your script

            fetch('userfeatures/reviews/write_review.php', { // Update to your actual PHP file path
                method: 'POST',
                body: formData
            }).then(response => response.text())
            .then(data => {
                Swal.fire({
                    title: 'Success!',
                    text: 'Your review has been submitted.',
                    icon: 'success',
                    confirmButtonText: 'Okay',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    Swal.close(); // Close the modal after submission
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'There was an issue uploading your review. Please try again later.',
                    icon: 'error',
                    confirmButtonText: 'Okay',
                    confirmButtonColor: '#d33' // Red color for the button
                });
            });
        });
    }
});

document.getElementById('uploadBtn').addEventListener('click', function() {
    // Check if the user is logged in
    if (!userId) {
        // Show login prompt if not logged in
        Swal.fire({
            title: 'Login Required',
            text: 'You need to log in to upload photos.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Login',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#28a745', // Green login button
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to login page (you can adjust the URL accordingly)
                window.location.href = '/login.php';
            }
        });
    } else {
        // If user is logged in, show the upload form
        Swal.fire({
            title: 'Submit Your Review',
            html: `
            <form id="reviewForm" enctype="multipart/form-data" style="text-align: left; padding: 10px;">
              <div style="margin-bottom: 10px;">
                <label for="rating" style="display: block; font-weight: bold;">Rating:</label>
                <div id="starRating" style="font-size: 24px; color: gray; margin-bottom: 15px;">
               <div id="starRating" class="star-rating">
                    <span class="star">★</span>
                    <span class="star">★</span>
                    <span class="star">★</span>
                    <span class="star">★</span>
                    <span class="star">★</span>
                </div>
                </div>
                <input type="hidden" id="ratingValue" name="ratingValue" value="5"> <!-- Default rating -->
              </div>

              <div style="margin-bottom: 15px;">
                <label for="comment" style="display: block; font-weight: bold;">Comment (required):</label>
                <textarea id="comment" name="comment" placeholder="Write your comment" required style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;"></textarea>
              </div>

              <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold;">Tags (optional):</label>
                <div id="tagsContainer" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 5px;">
                  <span class="tag" data-tag="#hiking">#hiking</span>
                  <span class="tag" data-tag="#nature">#nature</span>
                  <span class="tag" data-tag="#adventure">#adventure</span>
                  <span class="tag" data-tag="#photography">#photography</span>
                  <span class="tag" data-tag="#travel">#travel</span>
                  <span class="tag" data-tag="#mountains">#mountains</span>
                </div>
                <input type="hidden" id="selectedTags" name="tags" />
              </div>

              <div style="margin-bottom: 15px;">
                <label for="photoUpload" style="display: block; font-weight: bold;">Upload Photos:</label>
                <div id="dropzone" style="border: 2px dashed #ccc; padding: 20px; border-radius: 10px; background-color: #f9f9f9; text-align: center;">
                  <p style="color: #888;">Drag & Drop photos here or click to upload</p>
                  <input type="file" id="photoUpload" name="photoUpload[]" multiple accept="image/*" style="display: none;" />
                  <div id="preview" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;"></div>
                </div>
              </div>

              <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" id="cancelButton" class="swal2-cancel swal2-styled" style="background-color: #d33; color: white; padding: 10px 20px; border-radius: 5px;">Cancel</button>
                <button type="submit" class="swal2-confirm swal2-styled" style="background-color:#28a745;; color: white; padding: 10px 20px; border-radius: 5px;">Submit Review</button>
              </div>
            </form>
        `,
            didOpen: () => {
                // Initialize star rating
                document.querySelectorAll('#starRating span').forEach((star, index) => {
                    star.addEventListener('click', () => {
                        document.querySelectorAll('#starRating span').forEach(s => s.style.color = 'gray');
                        for (let i = 0; i <= index; i++) {
                            document.querySelectorAll('#starRating span')[i].style.color = '#28a745';
                        }
                        document.getElementById('ratingValue').value = index + 1;
                    });
                });

                // Tag selection functionality
                const tagsContainer = document.getElementById('tagsContainer');
                const selectedTagsInput = document.getElementById('selectedTags');
                const tags = [];

                tagsContainer.querySelectorAll('.tag').forEach(tag => {
                    tag.style.cursor = 'pointer'; // Cursor change for clickable tags
                    tag.style.padding = '8px 12px'; // Padding for tags
                    tag.style.borderRadius = '20px'; // Rounded corners
                    tag.style.border = '1px solid #ccc'; // Border style
                    tag.style.transition = 'background-color 0.3s, border-color 0.3s'; // Transition effects

                    tag.addEventListener('click', () => {
                        const tagValue = tag.getAttribute('data-tag');

                        // Toggle tag selection
                        if (tags.includes(tagValue)) {
                            tags.splice(tags.indexOf(tagValue), 1); // Remove if already selected
                            tag.style.backgroundColor = ''; // Reset background
                            tag.style.borderColor = '#ccc'; // Reset border color
                            tag.style.color = ''; // Reset text color
                        } else {
                            tags.push(tagValue); // Add if not selected
                            tag.style.backgroundColor = '#b2e0b2'; // Light Mint Green
                            tag.style.borderColor = '#388e3c'; // Darker Green
                            tag.style.color = 'white'; // White text
                        }

                        // Update the hidden input with selected tags
                        selectedTagsInput.value = JSON.stringify(tags);
                    });

                    // Hover effect for tags
                    tag.addEventListener('mouseover', () => {
                        if (!tags.includes(tagValue)) {
                            tag.style.backgroundColor = '#f0f8ff'; // Light background on hover
                        }
                    });

                    tag.addEventListener('mouseout', () => {
                        if (!tags.includes(tagValue)) {
                            tag.style.backgroundColor = ''; // Reset background
                        }
                    });
                });

                // Drag-and-drop functionality
                const dropzone = document.getElementById('dropzone');
                const photoUpload = document.getElementById('photoUpload');
                const preview = document.getElementById('preview');

                dropzone.addEventListener('click', () => photoUpload.click());

                dropzone.addEventListener('dragover', (event) => {
                    event.preventDefault();
                    dropzone.style.borderColor = '#3085d6'; // Highlight on drag over
                });

                dropzone.addEventListener('dragleave', () => {
                    dropzone.style.borderColor = '#ccc'; // Reset when dragging leaves
                });

                dropzone.addEventListener('drop', (event) => {
                    event.preventDefault();
                    dropzone.style.borderColor = '#ccc'; // Reset border
                    handleFiles(event.dataTransfer.files);
                });

                photoUpload.addEventListener('change', (event) => {
                    handleFiles(event.target.files);
                });

                function handleFiles(files) {
                    preview.innerHTML = ''; // Clear previous previews
                    Array.from(files).forEach((file) => {
                        if (!file.type.startsWith('image/')) return; // Skip non-image files

                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        img.style.maxWidth = '100px';
                        img.style.height = '100px';
                        img.style.objectFit = 'cover';
                        img.style.borderRadius = '5px';
                        preview.appendChild(img);
                    });
                }

                // Cancel button functionality
                document.getElementById('cancelButton').addEventListener('click', function() {
                    Swal.close();
                });
            },
            showConfirmButton: false, // Hide the default confirm button to use the form submit button instead
        });

        document.getElementById('reviewForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            formData.append('user_id', userId);
            formData.append('mountain_id', mountainId);

            fetch('userfeatures/reviews/upload_photo.php', {
                method: 'POST',
                body: formData
            }).then(response => response.text())
            .then(data => {
                Swal.fire({
                    title: 'Success!',
                    text: 'Your review has been submitted.',
                    icon: 'success',
                    confirmButtonText: 'Okay',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    // Reset the form and close the alert
                    document.getElementById('reviewForm').reset();
                    preview.innerHTML = ''; // Clear previews
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'There was an issue uploading your review. Please try again later.',
                    icon: 'error',
                    confirmButtonText: 'Okay',
                    confirmButtonColor: '#d33' // Red color for the button
                });
            });
        });
    }
});




function confirmDeletion(reviewId) {
    // Use SweetAlert2 for the confirmation
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#28a745', // Green login button
        cancelButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleted!',
                text: 'The review has been deleted successfully.',
                icon: 'success',
                confirmButtonColor: '#28a745'
            }).then(() => {
                // Redirect to the delete_comment.php page with the review ID
                window.location.href = 'delete_comment.php?id=' + reviewId + '&mountain_id=' + mountainId;
            });

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
            xhr.open('POST', 'userfeatures/report/report.php', true);
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




// JavaScript to Handle Modal Functionality -->
function openModal(imagePath, username, uploadDate, uploaderImagePath) {
    document.getElementById("modalWrapper").style.display = "block"; // Show modalWrapper
    document.getElementById("photoModal").style.display = "block"; // Show photoModal
    document.getElementById("modalImage").src = "userfeatures/reviews/" + imagePath;
    document.getElementById("username").innerText = username;
    document.getElementById("uploadDate").innerText = "Uploaded on: " + uploadDate;
    
    // Set uploader's profile picture
    if (uploaderImagePath) {
        document.getElementById("profilePic").src = uploaderImagePath.startsWith('/') ? uploaderImagePath : '/' + uploaderImagePath;
    } else {
        document.getElementById("profilePic").src = "/images/default_profile.png"; // Default profile image
    }
}

function closeModal() {
    document.getElementById("modalWrapper").style.display = "none"; // Hide modalWrapper
    document.getElementById("photoModal").style.display = "none"; // Hide photoModal
}

// Close the modal if the user clicks anywhere outside of the modal content
window.onclick = function(event) {
    const modalWrapper = document.getElementById("modalWrapper");
    const photoModal = document.getElementById("photoModal");
    if (event.target === modalWrapper) {
        closeModal();
    }
};

// Function to check for bookmarks in local storage
function checkBookmarks() {
    const bookmarkedMountains = JSON.parse(localStorage.getItem('bookmarkedMountains')) || [];
    
    bookmarkedMountains.forEach(mountainId => {
        const button = document.querySelector(`button[data-mountain-id="${mountainId}"]`);
        if (button) {
            button.classList.add('bookmarked'); // Add bookmarked class
            const icon = button.querySelector('span');
            icon.textContent = 'bookmark'; // Change icon to bookmarked state
        }
    });
}

// Call checkBookmarks on page load
document.addEventListener('DOMContentLoaded', checkBookmarks);

function toggleBookmark(button) {
    // Check if the user is logged in
    if (!isLoggedIn) {
        // Show SweetAlert if not logged in
        Swal.fire({
            title: 'Login Required',
            text: 'You need to log in to bookmark this item.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Login',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to login page if confirmed
                window.location.href = 'login.php'; // Change to your login URL
            }
        });
        return; // Exit the function if not logged in
    }

    const mountainId = button.getAttribute('data-mountain-id');
    const icon = button.querySelector('span');

    // Toggle bookmark state
    button.classList.toggle('bookmarked');

    const isBookmarked = button.classList.contains('bookmarked');
    
    // Update icon based on state
    icon.textContent = isBookmarked ? 'bookmark' : 'bookmark_add';
    
    // Update local storage
    let bookmarkedMountains = JSON.parse(localStorage.getItem('bookmarkedMountains')) || [];
    
    if (isBookmarked) {
        // Add mountainId to local storage if bookmarked
        if (!bookmarkedMountains.includes(mountainId)) {
            bookmarkedMountains.push(mountainId);
        }
    } else {
        // Remove mountainId from local storage if unbookmarked
        bookmarkedMountains = bookmarkedMountains.filter(id => id !== mountainId);
    }

    // Save updated bookmarks to local storage
    localStorage.setItem('bookmarkedMountains', JSON.stringify(bookmarkedMountains));

    // Send bookmark action to the server
    const action = isBookmarked ? 'add' : 'remove';
    const url = `userfeatures/bookmarks/add_bookmark.php?mountain_id=${mountainId}&action=${action}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log(data.message);
            } else {
                console.error(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}


// Prevent zooming with the mouse wheel or pinch gesture on mobile
document.addEventListener('wheel', function(event) {
    if (event.ctrlKey) {  // Zooming is typically triggered with the ctrl key + mouse wheel
        event.preventDefault();
    }
}, { passive: false });

// Prevent zoom on mobile (pinch zoom)
document.addEventListener('touchmove', function(event) {
    if (event.scale !== 1) {
        event.preventDefault(); // Prevent pinch zoom
    }
}, { passive: false });
