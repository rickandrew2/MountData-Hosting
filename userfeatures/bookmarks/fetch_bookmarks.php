<?php
// Start the session to access session variables
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection and other dependencies
include('../../db_connection.php'); 

// Fetch bookmarked mountains for the logged-in user
$userId = $_SESSION['user_id'];
$bookmarkedMountainsQuery = "SELECT `bookmark_id`, `mountain_id`, `bookmark_date` FROM `bookmarks` WHERE `user_id` = ?";
$stmt = $conn->prepare($bookmarkedMountainsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$bookmarkedMountains = $stmt->get_result();
$mountains = [];
while ($row = $bookmarkedMountains->fetch_assoc()) {
    $mountains[] = $row; // Include bookmark_date
}

// If there are no bookmarked mountains, output a message
if (empty($mountains)) {
    echo '<div class="no-bookmarks mt-5" style="text-align: center;">
        <span class="material-symbols-outlined" style="display: block; margin: 0 auto; font-size: 5rem;">explore</span>
        <h3 class="mt-3">No Bookmarks Yet</h3>
        <p style="color: #8a8a8a;">You have not bookmarked any mountains. Explore and add some to your favorites!</p>
    </div>';
    exit;
}

// Prepare to fetch mountain details based on bookmarked IDs
$minElevation = isset($_GET['minElevation']) ? (int)$_GET['minElevation'] : 0;
$maxElevation = isset($_GET['maxElevation']) ? (int)$_GET['maxElevation'] : 99999;
$difficulty = isset($_GET['difficulty']) ? $conn->real_escape_string($_GET['difficulty']) : ''; // Escape user input for security
$dateFilter = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : ''; // Get the date parameter

// Build the SQL query to fetch mountain details for bookmarked mountains
$mountainIds = implode(',', array_map('intval', array_column($mountains, 'mountain_id'))); // Ensure all IDs are integers for the query
$sql = "SELECT mountain_id, name, location, elevation, mountain_image, difficulty_level, description, latitude, longitude 
        FROM mountains 
        WHERE mountain_id IN ($mountainIds) 
        AND elevation BETWEEN $minElevation AND $maxElevation";

// Add filtering by bookmark date
if (!empty($dateFilter)) {
    switch ($dateFilter) {
        case 'last7days':
            $sql .= " AND EXISTS (SELECT 1 FROM bookmarks WHERE bookmarks.mountain_id = mountains.mountain_id AND bookmarks.user_id = ? AND bookmark_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY))";
            break;
        case 'last30days':
            $sql .= " AND EXISTS (SELECT 1 FROM bookmarks WHERE bookmarks.mountain_id = mountains.mountain_id AND bookmarks.user_id = ? AND bookmark_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY))";
            break;
        case 'last90days':
            $sql .= " AND EXISTS (SELECT 1 FROM bookmarks WHERE bookmarks.mountain_id = mountains.mountain_id AND bookmarks.user_id = ? AND bookmark_date >= DATE_SUB(CURDATE(), INTERVAL 90 DAY))";
            break;
    }
}

// Prepare and bind parameters for the final SQL query
$stmt = $conn->prepare($sql);
$params = [];

// Bind user ID if a date filter exists
if (!empty($dateFilter)) {
    $params[] = $userId; // Include user ID for the EXISTS clause
}

// Bind difficulty if it exists
if (!empty($difficulty)) {
    // Ensure the difficulty is used in the query, if you plan to filter by it
    $sql .= " AND difficulty_level = ?"; // Add difficulty filter to the SQL
    $params[] = $difficulty;
}

// Bind parameters if necessary
if ($params) {
    $stmt->bind_param(str_repeat('i', count($params)), ...$params); // Adjust the type according to your parameter types
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<li class='list-group-item'>";
        echo "<div class='mountains'>";
        echo "<div class='mountain-container'>";
        echo "<img class='mountain-pic mt-2' src='../../" . $row["mountain_image"] . "' alt='" . $row["name"] . "' data-lat='" . $row["latitude"] . "' data-lng='" . $row["longitude"] . "'>";
        echo "</div>";
        echo "<a href='../../mountains_profiles.php?mountain_id=" . $row["mountain_id"] . "' class='icon-link'>";
        echo "<span class='material-symbols-outlined travel-icon' id='bookmark-" . $row["mountain_id"] . "'>travel_explore</span>";
        echo "</a>";
        echo "<a href='../../mountains_profiles.php?mountain_id=" . $row["mountain_id"] . "' class='mountain-link'>";
        echo "<h5 class='mountain-title'>" . "Mount " . $row["name"] . "</h5>";
        echo "</a>";
        echo "<div class='about-mountain'>";
        echo "<p class='location'>" . $row["location"] . "</p>";
        echo "<p class='elevation'>Elevation: " . $row["elevation"] . "</p>";
        echo "<p class='difficulty-level'>Difficulty Level: " . $row["difficulty_level"] . "</p>";
        echo "<p class='description' style='text-align: justify;'>" . $row["description"] . "</p>";
        echo "</div></div></li>";
    }
} else {
    echo '<div class="no-bookmarks mt-5" style="text-align: center;">
    <span class="material-symbols-outlined" style="display: block; margin: 0 auto; font-size: 5rem;">explore</span>
    <h3 class="mt-3">No Mountains Found</h3>
    <p style="color: #8a8a8a;">No mountains match your criteria. Try adjusting your filters!</p>
    </div>';    
}

$stmt->close();
$conn->close();
?>
