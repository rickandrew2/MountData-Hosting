document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const filterIcon = document.getElementById('filter-icon');
    let currentRatingFilter = '';
    
    // Get the user ID from the global variable set in check_login.php
    const currentUserId = userId; // This comes from check_login.php's "var userId = <?php echo $user_id; ?>"

    // Debounce function to limit API calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Function to fetch and display reviews
    function fetchReviews(searchQuery = '', rating = '') {
        const queryParams = new URLSearchParams({
            mountain_id: mountainId,
            search: searchQuery,
            rating: rating
        });

        fetch(`userfeatures/reviews/handle_review_search.php?${queryParams}`)
            .then(response => response.json())
            .then(data => {
                const reviewsContainer = document.querySelector('.reviews-comments-section');
                
                if (data.status === 'no_results') {
                    reviewsContainer.innerHTML = `
                        <div class="no-results mt-5" style="text-align: center">
                            <span class="material-symbols-outlined" style="display: block; margin: 0 auto; font-size: 5rem;">search_off</span>
                            <h3 class="mt-3">No Reviews Found</h3>
                            <p style="color: #8a8a8a;">Try adjusting your search or filter criteria.</p>
                        </div>`;
                } else {
                    reviewsContainer.innerHTML = data.reviews.map(review => {
                        return generateReviewHTML(review);
                    }).join('');
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Function to generate HTML for a single review
    function generateReviewHTML(review) {
        const stars = '<i class="fas fa-star" style="color: #32CD32"></i>'.repeat(review.rating);
        const formattedDate = new Date(review.review_date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        // Convert both IDs to numbers for reliable comparison
        const reviewUserId = parseInt(review.user_id);
        const loggedInUserId = parseInt(currentUserId);

        console.log('Review User ID:', reviewUserId); // Debug
        console.log('Logged In User ID:', loggedInUserId); // Debug
        console.log('Are they equal?:', reviewUserId === loggedInUserId); // Debug

        return `
            <div class="review-comment">
                <div class="container d-flex" style="margin-top: 20px;">
                    <img src="${review.image_path}" alt="Profile Picture" style="width: 70px; height: 70px; border-radius: 50%; background-color: gray;">
                    <div class="name-n-date mx-3 mt-2">
                        <h5>${review.username}</h5>
                        <h6>${formattedDate}</h6>
                    </div>
                    <div class="dropdown ms-auto mt-2">
                        <button class="btn btn-sm" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="border: none; background: none; padding: 0; font-size: clamp(1rem, 2vw, 1.5rem);">
                            &#x2026;
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            ${reviewUserId === loggedInUserId ? 
                                `<li><a class="dropdown-item small" href="#" onclick="confirmDeletion(${review.review_id}); return false;">Delete</a></li>` :
                                `<li><a class="dropdown-item small" style="color: red;" href="#" onclick="confirmReport(${review.review_id}, ${currentUserId}); return false;">⚠ Report</a></li>`
                            }
                        </ul>
                    </div>
                </div>
                <div class="container star-ratings mt-2">${stars}</div>
                <div class="container" style="border-bottom: #8a8a8a solid 1px">
                    <p class="comment-text fs-5" style="text-align: justify;">${review.comment}</p>
                    ${review.review_photo ? generatePhotoHTML(review.review_photo) : ''}
                </div>
            </div>`;
    }

   // Function to generate HTML for review photos
function generatePhotoHTML(photos) {
    const photoArray = photos.split(',');
    
    return `
        <div class="review-photos-container mt-2" style="overflow-x: auto; white-space: nowrap;">
            <div class="d-flex">
                ${photoArray.map(photo => `
                    <div class="review-photo-wrapper me-2" style="display: inline-block;">
                        <img src="userfeatures/reviews/${photo.trim()}" 
                             alt="Review Photo" 
                             class="review-photo img-fluid rounded" 
                             style="cursor: pointer; transition: transform 0.2s; height: 100%; width: 100%; max-height: 200px; max-width: 250px;"
                             onclick="viewFullImage('userfeatures/reviews/${photo.trim()}')">
                    </div>
                `).join('')}
            </div>
        </div>`;
}

    // Search input handler
    searchInput.addEventListener('input', debounce(function(e) {
        fetchReviews(e.target.value, currentRatingFilter);
    }, 300));

    // Filter icon click handler
    document.querySelector('.filter-icon').addEventListener('click', function() {
        Swal.fire({
            title: 'Filter Reviews by Rating',
            html: `
                <div style="display: flex; flex-direction: column; gap: 15px; margin: 20px 0;">
                    ${[5,4,3,2,1].map(num => `
                        <div style="display: flex; align-items: center; justify-content: center; gap: 10px; cursor: pointer;">
                            <input type="radio" name="rating" value="${num}" id="rating${num}" 
                                ${currentRatingFilter === num.toString() ? 'checked' : ''}>
                            <label for="rating${num}" style="margin: 0; cursor: pointer;">
                                ${'<i class="fas fa-star" style="color: #32CD32"></i>'.repeat(num)}
                            </label>
                        </div>
                    `).join('')}
                </div>`,
            showCancelButton: true,
            confirmButtonText: 'Apply',
            cancelButtonText: 'Clear Filter',
            confirmButtonColor: '#32CD32',
            cancelButtonColor: '#dc3545',
            showCloseButton: true,
            preConfirm: () => {
                const selectedRating = document.querySelector('input[name="rating"]:checked')?.value;
                return selectedRating || '';
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                currentRatingFilter = result.value;
                fetchReviews(searchInput.value, currentRatingFilter);
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                currentRatingFilter = '';
                fetchReviews(searchInput.value, '');
            }
        });
    });

    document.getElementById('timeFilter').addEventListener('change', function() {
        const timeFilter = this.value;
        const searchQuery = document.getElementById('search-input').value;
        const mountainId = window.mountainId; // Make sure this is defined in your PHP

        // Update the URL to include the correct path to handle_review_search.php
        const queryParams = new URLSearchParams({
            mountain_id: mountainId,
            search: searchQuery,
            time: timeFilter
        });

        fetch(`userfeatures/reviews/handle_review_search.php?${queryParams}`)
            .then(response => response.json())
            .then(data => {
                const reviewsContainer = document.querySelector('.reviews-comments-section');
                
                if (data.status === 'no_results') {
                    reviewsContainer.innerHTML = `
                        <div class="no-results mt-5" style="text-align: center">
                            <span class="material-symbols-outlined" style="display: block; margin: 0 auto; font-size: 5rem;">search_off</span>
                            <h3 class="mt-3">No Reviews Found</h3>
                            <p style="color: #8a8a8a;">Try adjusting your search or filter criteria.</p>
                        </div>`;
                } else {
                    reviewsContainer.innerHTML = data.reviews.map(review => {
                        return generateReviewHTML(review);
                    }).join('');
                }
            })
            .catch(error => console.error('Error:', error));
    });
});


function viewFullImage(imageSrc) {
    const popup = document.getElementById('imagePopup');
    const popupImage = document.getElementById('imagePopupContent');
    const closeButton = document.querySelector('.image-popup-close');

    popupImage.src = imageSrc;
    popup.classList.add('active');

    closeButton.onclick = function() {
        popup.classList.remove('active');
    };

    window.onclick = function(event) {
        if (event.target === popup) {
            popup.classList.remove('active');
        }
    };
}

