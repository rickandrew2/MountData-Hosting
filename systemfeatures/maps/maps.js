// Initialize and add the map
let map; // Declare map variable globally

function initMap() {
    // Default location to center the map
    console.log("Map is initializing..."); // Debug line
    const defaultLocation = { lat: -34.397, lng: 150.644 };
    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 8,
        center: defaultLocation,
    });

    // Add click event listeners to mountain images
    const mountainImages = document.querySelectorAll('.mountain-pic');
    mountainImages.forEach((image) => {
        image.addEventListener('click', () => {
            const lat = parseFloat(image.getAttribute('data-lat'));
            const lng = parseFloat(image.getAttribute('data-lng'));
            const location = { lat: lat, lng: lng };

            // Center the map on the selected mountain
            map.setCenter(location);
            map.setZoom(10); // Optional: adjust zoom level

            // Add a marker for the selected mountain
            new google.maps.Marker({
                position: location,
                map: map,
                title: image.alt, // Use the mountain name as the title
            });
        });
    });
}



let mapVisible = false; // State to track if the map is visible

// Function to toggle map visibility
function toggleMap() {
    const mapContainer = document.getElementById("map-container");
    const mapToggleIcon = document.querySelector(".floating-map-icon span");

    // Ensure the elements exist
    if (!mapContainer || !mapToggleIcon) {
        console.error("Map container or toggle icon not found");
        return; // Stop execution if elements are not found
    }

    if (!mapVisible) {
        console.log("Showing map");
        mapContainer.style.display = "block"; // Show the map
        mapToggleIcon.innerHTML = "landscape"; // Change to mountain icon
        mapVisible = true;
    } else {
        console.log("Hiding map");
        mapContainer.style.display = "none"; // Hide the map
        mapToggleIcon.innerHTML = "map"; // Change back to map icon
        mapVisible = false;
    }
}

// Ensure map is hidden on smaller screens when resizing
window.addEventListener('resize', function () {
    const mapContainer = document.getElementById("map-container");
    const mapToggleIcon = document.querySelector(".floating-map-icon");

    // Ensure the elements exist
    if (!mapContainer || !mapToggleIcon) {
        console.error("Map container or toggle icon not found during resize");
        return; // Stop execution if elements are not found
    }

    if (window.innerWidth > 768) {
        console.log("Larger screen, show map");
        mapContainer.style.display = "block"; // Always show the map on larger screens
        mapToggleIcon.style.display = "none"; // Hide the toggle button
    } else {
        console.log("Smaller screen, hide map");
        mapContainer.style.display = mapVisible ? "block" : "none"; // Toggle based on visibility
        mapToggleIcon.style.display = "flex"; // Show the toggle button
    }
});

// Initial check to hide the map if the screen is small
window.addEventListener('DOMContentLoaded', function () {
    const mapContainer = document.getElementById("map-container");
    const mapToggleIcon = document.querySelector(".floating-map-icon");

    // Ensure the elements exist
    if (!mapContainer || !mapToggleIcon) {
        console.error("Map container or toggle icon not found during initial load");
        return; // Stop execution if elements are not found
    }

    if (window.innerWidth <= 575) {
        console.log("Initial load on smaller screen, hide map");
        mapContainer.style.display = "none"; // Hide the map
        mapToggleIcon.style.display = "flex"; // Show the toggle icon
    } else {
        console.log("Initial load on larger screen, show map");
        mapContainer.style.display = "block"; // Show the map
        mapToggleIcon.style.display = "none"; // Hide the toggle icon
    }
});



function checkLogin() {
    if (isLoggedIn) {
        // Redirect to community.php
        window.location.href = '../community/community.php';
    } else {
        // Show SweetAlert
        Swal.fire({
            title: 'Access Denied!',
            text: 'You need to log in to access the Community page.',
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#28a745', // Green button color
            background: '#f8f9fa', // Light background
            iconHtml: '<i class="fas fa-exclamation-triangle" style="font-size: 2em; color: #ffc107;"></i>', // Custom triangle icon
            padding: '20px', // Padding around the content
            backdrop: 'rgba(0, 0, 0, 0.5)', // Darker backdrop
            showClass: {
                popup: 'animate__animated animate__fadeInDown' // Animation for showing the popup
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp' // Animation for hiding the popup
            }
        });
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

function clearLocation() {
    const checkboxes = document.querySelectorAll('input[name="location"]');
    checkboxes.forEach(checkbox => checkbox.checked = false);
}

function filterMountains() {
    const minElevation = document.getElementById('minElevation').value || 0;
    const maxElevation = document.getElementById('maxElevation').value || 99999;
    const difficulty = document.querySelector('input[name="difficulty"]:checked')?.value || '';

    // Get selected locations
    const locationCheckboxes = document.querySelectorAll('input[name="location"]:checked');
    const locations = Array.from(locationCheckboxes).map(checkbox => checkbox.value).join(',');

    // Fetch with updated URL to include locations
    fetch(`fetch_mountains.php?minElevation=${minElevation}&maxElevation=${maxElevation}&difficulty=${difficulty}&locations=${locations}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('mountainList').innerHTML = data;
        });
}
