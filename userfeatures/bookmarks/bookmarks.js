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
        });
}
