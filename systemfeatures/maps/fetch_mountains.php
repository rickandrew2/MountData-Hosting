<?php
include '../../db_connection.php'; 

$minElevation = isset($_GET['minElevation']) ? (int)$_GET['minElevation'] : 0;
$maxElevation = isset($_GET['maxElevation']) ? (int)$_GET['maxElevation'] : null;
$difficulty = isset($_GET['difficulty']) ? $_GET['difficulty'] : '';
$locations = isset($_GET['locations']) ? (is_array($_GET['locations']) ? $_GET['locations'] : explode(',', $_GET['locations'])) : [];
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build the base SQL query with only minElevation
$sql = "SELECT mountain_id, name, location, elevation, mountain_image, difficulty_level, description, latitude, longitude 
        FROM mountains 
        WHERE elevation >= ?";

$params = [$minElevation];
$types = "i"; // Start with one integer parameter

// Add maxElevation condition only if it's provided
if ($maxElevation !== null) {
    $sql .= " AND elevation <= ?";
    $params[] = $maxElevation;
    $types .= "i";
}

// Add difficulty filter if specified
if (!empty($difficulty)) {
    $sql .= " AND difficulty_level = ?";
    $params[] = $difficulty;
    $types .= "s";
}

// Add search filter if specified
if (!empty($search)) {
    $sql .= " AND name LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= "s";
}

// If the locations array is not empty, process it
if (!empty($locations)) {
    $locationConditions = [];

    // Create the LIKE conditions for each location
    foreach ($locations as $location) {
        $locationConditions[] = "location LIKE ?";
        $params[] = '%' . trim($location) . '%';
        $types .= "s";
    }

    // Append to the SQL query
    if (!empty($locationConditions)) {
        $sql .= " AND (" . implode(" OR ", $locationConditions) . ")";
    }
}

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

// Bind the parameters dynamically
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

$linkUrl = '../../mountains_profiles.php';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<li class='list-group-item'>";
        echo "<div class='mountains'>";
        echo "<div class='mountain-container'>";

        echo "<img class='mountain-pic mt-2' src='../../" . $row["mountain_image"] . "' alt='" . $row["name"] . "' data-lat='" . $row["latitude"] . "' data-lng='" . $row["longitude"] . "'>";
        
        echo "</div>";

        // Add anchor around the travel-icon
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
