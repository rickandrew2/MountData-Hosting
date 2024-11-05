let map; // Main map variable
let modalMap; // Modal map variable

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
    // Check if the user is logged in
    if (!isLoggedIn) {
        // Display message for users who need to log in
        const messageContainer = document.getElementById("modalMapMessage");
        messageContainer.innerHTML = `
            <div class="no-bookmarks mt-5" style="text-align: center;">
                <span class="material-symbols-outlined" style="display: block; margin: 0 auto; font-size: 5rem;">login</span>
                <h3 class="mt-3">Please Log In</h3>
                <p style="color: #8a8a8a;">You need to log in to view the map of your bookmarked mountains. Please log in to access your favorites!</p>
            </div>`;

        // Hide the modal map since the user is not logged in
        document.getElementById("modalMap").style.display = "none";

        return; // Exit the function since the user is not logged in
    }

    // Initialize the modal map if it hasn't been initialized yet
    if (!modalMap) {
        const modalDefaultLocation = { lat: -34.397, lng: 150.644 };
        modalMap = new google.maps.Map(document.getElementById("modalMap"), {
            zoom: 8,
            center: modalDefaultLocation,
        });
    }
}


function handleMountainClick(image) {
    const lat = parseFloat(image.getAttribute('data-lat'));
    const lng = parseFloat(image.getAttribute('data-lng'));
    const location = { lat: lat, lng: lng };

    // Center the main map on the selected mountain
    map.setCenter(location);
    map.setZoom(10); // Adjust zoom level for main map

    // Add a marker for the selected mountain on the main map
    new google.maps.Marker({
        position: location,
        map: map,
        title: image.alt, // Use the mountain name as the title
    });

    // Check if the screen size is less than 768px before initializing/updating the modal map
    if (window.innerWidth < 768) {
        initModalMap(); // Initialize modal map if on small screen
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

    // Get the date filter from the selected radio button
    const dateFilter = getSelectedDateFilter();

    fetch(`fetch_bookmarks.php?minElevation=${minElevation}&maxElevation=${maxElevation}&difficulty=${difficulty}&date=${dateFilter}`)
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
