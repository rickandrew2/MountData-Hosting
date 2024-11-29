let map; // Main map variable
let modalMap; // Modal map variable
let markers = []; // Array to store markers for main map
let modalMarkers = []; // Array to store markers for modal map

function initMap() {
    // Initialize the main map
    const defaultLocation = { lat: -34.397, lng: 150.644 };
    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 8,
        center: defaultLocation,
    });

    // Add click event listeners to mountain images
    const mountainImages = document.querySelectorAll('.mountain-pic');
    mountainImages.forEach((image) => {
        image.addEventListener('click', () => handleMountainClick(image));
    });
}

function initModalMap(isLoggedIn) {
    // Initialize the modal map if it hasn't been initialized yet
    if (!modalMap) {
        const modalDefaultLocation = { lat: -34.397, lng: 150.644 };
        modalMap = new google.maps.Map(document.getElementById("modalMap"), {
            zoom: 8,
            center: modalDefaultLocation,
        });
    }

    // Update visibility of elements based on login status
    const messageContainer = document.getElementById("modalMapMessage");
    const modalMapElement = document.getElementById("modalMap");

    if (!isLoggedIn) {
        messageContainer.innerHTML = `
            <div class="no-bookmarks mt-5" style="text-align: center;">
                <span class="material-symbols-outlined" style="display: block; margin: 0 auto; font-size: 5rem;">login</span>
                <h3 class="mt-3">Please Log In</h3>
                <p style="color: #8a8a8a;">You need to log in to view the map of your bookmarked mountains. Please log in to access your favorites!</p>
            </div>`;
        modalMapElement.style.display = "none";
    } else {
        messageContainer.innerHTML = '';
        modalMapElement.style.display = "block";
    }
}

function clearMarkers() {
    // Clear markers from the main map
    markers.forEach(marker => {
        marker.setMap(null);
    });
    markers = [];

    // Clear markers from the modal map
    modalMarkers.forEach(marker => {
        marker.setMap(null);
    });
    modalMarkers = [];
}

function handleMountainClick(image) {
    const lat = parseFloat(image.getAttribute('data-lat'));
    const lng = parseFloat(image.getAttribute('data-lng'));
    const location = { lat: lat, lng: lng };

    // Center the main map on the selected mountain
    map.setCenter(location);
    map.setZoom(10);

    // Create and store the marker for the main map
    const marker = new google.maps.Marker({
        position: location,
        map: map,
        title: image.alt,
    });
    markers.push(marker); // Add marker to array

    // Check if the screen size is less than 768px before initializing/updating the modal map
    if (window.innerWidth < 768) {
        initModalMap(true); // Pass true since we know the user is logged in at this point
        modalMap.setCenter(location); // Center the modal map on the selected mountain
        modalMap.setZoom(10); // Adjust zoom level for modal map

        // Add a marker for the selected mountain on the modal map
        new google.maps.Marker({
            position: location,
            map: modalMap, // Add marker to the modal map
            title: image.alt, // Use the mountain name as the title
        });

        // Open the modal
        openMapModal();
    }
}

function openMapModal() {
    document.getElementById('mapModal').style.display = 'block'; // Show the modal
}

function closeMapModal() {
    document.getElementById('mapModal').style.display = 'none'; // Hide the modal
}



// Close the modal if the user clicks anywhere outside of the modal
window.onclick = function(event) {
    const modal = document.getElementById('mapModal');
    if (event.target === modal) {
        closeMapModal();
    }
}


function toggleDropdown(id) {
    // Get all dropdowns
    const dropdowns = document.querySelectorAll('.dropdown-content');

    // Close all dropdowns except the one clicked
    dropdowns.forEach(dropdown => {
        if (dropdown.id !== id) {
            dropdown.style.display = 'none'; // Close other dropdowns
        }
    });

    // Toggle the clicked dropdown
    const dropdown = document.getElementById(id);
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}


function clearElevation() {
    document.getElementById('minElevation').value = '';
    document.getElementById('maxElevation').value = '';
}

function clearDifficulty() {
    const radios = document.getElementsByName('difficulty');
    radios.forEach(radio => radio.checked = false);
}

// Function to clear the bookmark date selection
function clearBookmarkDate() {
    const radios = document.getElementsByName('bookmarkDate');
    radios.forEach(radio => radio.checked = false);
}

function getSelectedDateFilter() {
    const radios = document.getElementsByName('bookmarkDate');
    for (let radio of radios) {
        if (radio.checked) {
            return radio.value; // return the value of the selected radio button
        }
    }
    return ''; // return empty if no radio button is selected
}

function filterMountains() {
    const minElevation = document.getElementById('minElevation').value || 0;
    const maxElevation = document.getElementById('maxElevation').value || 99999;
    const difficulty = document.querySelector('input[name="difficulty"]:checked')?.value || '';
    const dateFilter = getSelectedDateFilter();

    // Show loading state
    Swal.fire({
        title: 'Filtering...',
        text: 'Please wait while we find your bookmarked trails',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(`fetch_bookmarks.php?minElevation=${minElevation}&maxElevation=${maxElevation}&difficulty=${difficulty}&date=${dateFilter}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('mountainList').innerHTML = data;

            // Reattach click event listeners
            const mountainImages = document.querySelectorAll('.mountain-pic');
            mountainImages.forEach((image) => {
                image.addEventListener('click', () => handleMountainClick(image));
            });

            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Filters Applied!',
                text: 'Your bookmarks have been updated',
                timer: 1500,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });

            // Close any open dropdowns
            const dropdowns = document.querySelectorAll('.dropdown-content');
            dropdowns.forEach(dropdown => {
                dropdown.style.display = 'none';
            });
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while filtering your bookmarks',
                timer: 1500,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        });
}

function checkLogin() {
    if (isLoggedIn) {
        // Redirect to community.php
        window.location.href = '../../systemfeatures/community/community.php';
    } else {
        // Show SweetAlert
        Swal.fire({
            title: 'Access Denied!',
            text: 'You need to log in to access the Community page.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: "green",
            cancelButtonColor: "#d33",
            confirmButtonText: "Login",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.value) {
                // Redirect to the login page
                window.location.href = "login.php"; // Change to your login page
            }
        });
        buttonsStyling: false // Disable default styling for buttons
    }
}

// Fix the event listener error by checking if elements exist
document.addEventListener('DOMContentLoaded', function() {
    const searchContainer = document.querySelector('.search-container');
    const searchToggle = document.querySelector('.mobile-search-toggle');
    
    if (searchContainer && searchToggle) { // Check if elements exist
        searchToggle.addEventListener('click', function() {
            searchContainer.classList.toggle('active');
            const navbar = document.querySelector('.navbar-container');
            if (navbar) {
                navbar.classList.toggle('mobile-search-active');
            }
        });

        // Close search when clicking outside
        document.addEventListener('click', function(e) {
            if (searchContainer && searchToggle) {
                if (!searchContainer.contains(e.target) && !searchToggle.contains(e.target)) {
                    searchContainer.classList.remove('active');
                    const navbar = document.querySelector('.navbar-container');
                    if (navbar) {
                        navbar.classList.remove('mobile-search-active');
                    }
                }
            }
        });
    }
});

function clearAllFilters() {
    // Clear elevation inputs
    document.querySelectorAll('#minElevation, #maxElevation').forEach(input => {
        input.value = '';
    });

    // Clear difficulty radio buttons
    document.querySelectorAll('input[name="difficulty"]').forEach(radio => {
        radio.checked = false;
    });

    // Clear location checkboxes
    document.querySelectorAll('input[name="location"]').forEach(checkbox => {
        checkbox.checked = false;
    });

    filterMountains();
}

function searchBookmarks() {
    const searchTerm = document.getElementById('mountainSearchBar').value.toLowerCase();

    // Fetch bookmarks with the search term
    fetch(`fetch_bookmarks.php?search=${encodeURIComponent(searchTerm)}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('mountainList').innerHTML = data;

            // Reattach click event listeners to the new mountain images
            const mountainImages = document.querySelectorAll('.mountain-pic');
            mountainImages.forEach((image) => {
                image.addEventListener('click', () => handleMountainClick(image));
            });
        })
        .catch(error => {
            console.error('Search failed:', error);
        });
}