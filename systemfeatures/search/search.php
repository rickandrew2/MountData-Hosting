<?php
include '../../db_connection.php'; // Include your database connection file

if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT `mountain_id`, `name`, `location`, `elevation`, `mountain_image`, `difficulty_level`, `description` FROM `mountains` WHERE `name` LIKE '%$search%'";
    $result = $conn->query($sql);

    // Link to mountains_profiles.php from search.php
    $linkUrl = '../../mountains_profiles.php'; // Correct path to reach mountains_profiles.php

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<a href='" . $linkUrl . "?mountain_id=" . urlencode($row['mountain_id']) . "' class='result-item-a m-auto' style='text-decoration:none; color:inherit;'>";
            echo "<div class='result-item'>";
            echo "<span class='material-symbols-outlined'>location_on</span>";
            echo "<p class='mountain-name'>" . htmlspecialchars($row['name']) . "</p>";
            echo "<p class='location mx-2'>Location: " . htmlspecialchars($row['location']) . "</p>";
            echo "<p class='elevation'>Elevation: " . htmlspecialchars($row['elevation']) . " m</p>";
            echo "</div>";
            echo "</a>"; // Closing anchor tag
        }
    } else {
        echo "<p class='mt-3' style='text-align: center;'>No results found</p>";
    }
}

// Close the connection if you want, but it might already be handled in your db_connection.php
$conn->close();
?>
