<?php
// fetch_upload_photos.php
if ($loginStatus) { // Assuming $loginStatus is true if logged in
    include 'db_connection.php'; // Include your database connection file

    // Fetch photos for the specific mountain_id from the database
    $query = "
        SELECT r.review_photo, r.review_date, u.username, u.image_path
        FROM reviews r
        INNER JOIN users u ON r.user_id = u.user_id
        WHERE r.mountain_id = ? AND r.review_photo IS NOT NULL
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $mountain_id);
    $stmt->execute();
    $result = $stmt->get_result();
?>

    <div class="container photos py-5">
        <div class="row mx-0">
            <?php if ($result->num_rows > 0): ?>
                <div class="col-12 mb-4 text-center">
                    <h2 class="mt-5">Trail Photos</h2>
                    <p>Click on a photo to view it full-screen.</p>
                </div>
                <div class="row g-4 justify-content-center">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                        // Split the review_photo by commas to get an array of photo paths
                        $photos = explode(',', $row['review_photo']);
                        ?>
                        <?php foreach ($photos as $photo): ?>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                                <div class="photo-item">
                                    <img src="userfeatures/reviews/<?php echo htmlspecialchars(trim($photo)); ?>"
                                        alt="Uploaded photo"
                                        class="img-fluid rounded shadow-sm w-100"
                                        onclick="openModal('<?php echo htmlspecialchars(trim($photo)); ?>', '<?php echo htmlspecialchars($row['username']); ?>', '<?php echo htmlspecialchars($row['review_date']); ?>', '<?php echo htmlspecialchars(trim(str_replace('../../', '', $row['image_path']))); ?>')">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="col-12 text-center mt-5">
                    <div class="no-reviews">
                        <span class="material-symbols-outlined d-block mx-auto" style="font-size: 5rem;">landscape_2_off</span>
                        <h3 class="mt-3">Upload Photos</h3>
                        <p class="text-muted">Share your experience by leaving a photo on any trail page. Your feedback helps others choose their next adventure!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>


    <!-- Modal for Full-Screen Image View -->
    <div class="modalWrapper modal" id="modalWrapper" style="display: none;">
        <div id="photoModal">
            <span class="close" onclick="closeModal()">&times;</span>
            <img class="modal-content" id="modalImage">
            <div id="modalOverlay">
                <img id="profilePic" class="rounded-circle" src="" alt="User Profile Picture">
                <span id="username"></span>
                <span id="uploadDate"></span>
            </div>
        </div>
    </div>

<?php
} else {
    // User is not logged in
    echo '
    <div class="login-prompt mt-5" style="text-align: center">
        <span class="material-symbols-outlined" style="display: block; margin: 0 auto; font-size: 5rem;">lock</span>
        <h3 class="mt-3">Please Log In</h3>
        <p style="color: #8a8a8a;">You need to log in to view and upload photos. <a href="login.php" style="color: #32CD32;">Log In</a></p>
    </div>';
}
?>