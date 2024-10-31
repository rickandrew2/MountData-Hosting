$(document).ready(function() {
    // Determine the URL based on the current path
    let url;

    if (window.location.pathname.includes('/userfeatures/userprofile')) {
        url = '../../systemfeatures/search/search.php'; // For profile.php in userprofile
    } else if (window.location.pathname.includes('/mountain_profiles.php')) {
        url = '../systemfeatures/search/search.php'; // For mountain_profiles.php
    } else if (window.location.pathname.includes('/systemfeatures/maps')) {
        url = '../search/search.php'; // For maps.php in systemfeatures/maps
    } else if (window.location.pathname.includes('/systemfeatures/community')) {
        url = '../search/search.php'; // For community.php
    } else {
        url = '../systemfeatures/search/search.php'; // Default path for other cases
    }

    // Listen for keyup events on the search bar
    $('.search-bar').on('keyup', function() {
        const query = $(this).val(); // Get the search query
        if (query.length > 0) { // If the query is not empty
            $.ajax({
                url: url, // Use the determined path here
                method: 'GET',
                data: { search: query }, // Pass the query as a GET parameter
                success: function(data) {
                    $('.search-results').html(data); // Display results in the .search-results div
                }
            });
        } else {
            $('.search-results').empty(); // Clear results when search bar is empty
        }
    });
});