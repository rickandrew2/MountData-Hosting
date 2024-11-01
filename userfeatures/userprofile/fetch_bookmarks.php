<?php
// Check if user is logged in
include_once('../../check_login.php'); 

// Include the database connection
include('../../db_connection.php'); 

// Fetch bookmarked mountains for the logged-in user
$userId = $_SESSION['user_id']; // Assuming user ID is stored in session
$bookmarkedMountainsQuery = "SELECT `bookmark_id`, `mountain_id` FROM `bookmarks` WHERE `user_id` = ?";
$stmt = $conn->prepare($bookmarkedMountainsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$bookmarkedMountains = $stmt->get_result();
$mountains = [];
while ($row = $bookmarkedMountains->fetch_assoc()) {
    $mountains[] = $row['mountain_id'];
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

// Build the SQL query to fetch mountain details for bookmarked mountains
$mountainIds = implode(',', array_map('intval', $mountains)); // Ensure all IDs are integers for the query
$sql = "SELECT mountain_id, name, location, elevation, mountain_image, difficulty_level, description, latitude, longitude 
        FROM mountains 
        WHERE mountain_id IN ($mountainIds) 
        AND elevation BETWEEN $minElevation AND $maxElevation";

if (!empty($difficulty)) {
    $sql .= " AND difficulty_level = '$difficulty'";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<li class='list-group-item'>";
        echo "<div class='mountains'>";
        echo "<div class='mountain-container'>";
        
        // Wrap the image in an anchor tag
        echo "<a href='../../mountains_profiles.php?mountain_id=" . $row["mountain_id"] . "'>";
        echo "<img class='mountain-pic mt-2' src='../../" . $row["mountain_image"] . "' alt='" . $row["name"] . "' data-lat='" . $row["latitude"] . "' data-lng='" . $row["longitude"] . "'>";
        echo "</a>"; // Close the anchor tag
        
        echo "</div>";
        echo "<span class='material-symbols-outlined bookmark-icon' id='bookmark-" . $row["mountain_id"] . "'>bookmark_border</span>";
        echo "<h5 style='margin-top: 10px;'>" . "Mount " . $row["name"] . "</h5>";
        echo "<div class='about-mountain'>";
        echo "<p class='location'>" . $row["location"] . "</p>";
        echo "<p class='elevation'>Elevation: " . $row["elevation"] . "</p>";
        echo "<p class='difficulty-level'>Difficulty Level: " . $row["difficulty_level"] . "</p>";
        echo "<p class='description' style='text-align: justify;'>" . $row["description"] . "</p>";
        echo "</div></div></li>";
    }
} else {
    echo "<li class='list-group-item'>No mountains found.</li>";
}

$conn->close();
?>
