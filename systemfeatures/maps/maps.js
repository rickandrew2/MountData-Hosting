let map; // Main map variable
let modalMap; // Modal map variable
let markers = []; // Array to store markers
let modalMarkers = []; // Array to store modal markers

function initMap() {
    // Initialize the main map with custom styles
    const defaultLocation = { lat: -34.397, lng: 150.644 };
    const mapOptions = {
        zoom: 8,
        center: defaultLocation,
        styles: [
            {
                featureType: 'water',
                elementType: 'geometry',
                stylers: [{ color: '#b3e5fc' }] // Light blue for water
            },
            {
                featureType: 'landscape',
                elementType: 'geometry',
                stylers: [{ color: '#d7f9d7' }] // Light green for landscape
            },
            {
                featureType: 'road',
                elementType: 'geometry',
                stylers: [{ color: '#e0e0e0' }] // Light gray for roads
            },
            {
                featureType: 'road',
                elementType: 'labels',
                stylers: [{ visibility: 'on' }] // Show road labels
            },
            {
                featureType: 'poi',
                elementType: 'geometry',
                stylers: [{ visibility: 'off' }] // Hide points of interest
            },
            {
                featureType: 'administrative',
                elementType: 'geometry',
                stylers: [{ visibility: 'off' }] // Hide administrative areas
            }
        ]
    };

    map = new google.maps.Map(document.getElementById("map"), mapOptions);

    // Add click event listeners to mountain images
    const mountainImages = document.querySelectorAll('.mountain-pic');
    mountainImages.forEach((image) => {
        image.addEventListener('click', () => handleMountainClick(image));
    });
}

function initModalMap() {
    // Initialize the modal map if it hasn't been initialized yet
    if (!modalMap) {
        const modalDefaultLocation = { lat: -34.397, lng: 150.644 };
        const modalMapOptions = {
            zoom: 8,
            center: modalDefaultLocation,
            styles: [
                {
                    featureType: 'water',
                    elementType: 'geometry',
                    stylers: [{ color: '#b3e5fc' }] // Light blue for water
                },
                {
                    featureType: 'landscape',
                    elementType: 'geometry',
                    stylers: [{ color: '#d7f9d7' }] // Light green for landscape
                },
                {
                    featureType: 'road',
                    elementType: 'geometry',
                    stylers: [{ color: '#e0e0e0' }] // Light gray for roads
                },
                {
                    featureType: 'road',
                    elementType: 'labels',
                    stylers: [{ visibility: 'on' }] // Show road labels
                },
                {
                    featureType: 'poi',
                    elementType: 'geometry',
                    stylers: [{ visibility: 'off' }] // Hide points of interest
                },
                {
                    featureType: 'administrative',
                    elementType: 'geometry',
                    stylers: [{ visibility: 'off' }] // Hide administrative areas
                }
            ]
        };

        modalMap = new google.maps.Map(document.getElementById("modalMap"), modalMapOptions);
    }
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
        initModalMap();
        modalMap.setCenter(location);
        modalMap.setZoom(10);

        // Create and store the marker for the modal map
        const modalMarker = new google.maps.Marker({
            position: location,
            map: modalMap,
            title: image.alt,
        });
        modalMarkers.push(modalMarker); // Add modal marker to array

        openMapModal();
    }
}

function openMapModal() {
    document.getElementById('mapModal').style.display = 'block';
}

function closeMapModal() {
    document.getElementById('mapModal').style.display = 'none';
}

// Close the modal if the user clicks anywhere outside of the modal
window.onclick = function(event) {
    const modal = document.getElementById('mapModal');
    if (event.target === modal) {
        closeMapModal();
    }
}


function showMountain() {
    document.getElementById('mountainColumn').style.display = 'block'; // Show the mountain column
    document.getElementById('mapColumn').style.display = 'none'; // Hide the map column
    document.getElementById('showMapButton').style.display = 'block'; // Show the map button
    document.getElementById('showMountainButton').style.display = 'none'; // Hide the mountain button
}








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

    // Get selected locations as an array
    const locationCheckboxes = document.querySelectorAll('input[name="location"]:checked');
    const locations = Array.from(locationCheckboxes).map(checkbox => checkbox.value);

    // Convert locations array to URL parameters
    const locationParams = locations.map(loc => `locations[]=${encodeURIComponent(loc)}`).join('&');

    // Fetch with updated URL to include locations array
    fetch(`fetch_mountains.php?minElevation=${minElevation}&maxElevation=${maxElevation}&difficulty=${difficulty}&${locationParams}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('mountainList').innerHTML = data;

            // Reattach click event listeners to the new mountain images
            const mountainImages = document.querySelectorAll('.mountain-pic');
            mountainImages.forEach((image) => {
                image.addEventListener('click', () => handleMountainClick(image));
            });
        });
}

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

// Add new function to clear markers
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
