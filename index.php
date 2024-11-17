<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  
  <title>MountData</title>

  <!-- My CSS -->
  <link rel="stylesheet" href="dist/css/main.css" />

  <!-- Favicon -->
  <link rel="icon" href="images/logomount.png" type="image/png" />

  <!--SWEET ALERT CDN-->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.2/dist/sweetalert2.all.min.js"></script>

  <!--SweetAlert CSS-->
  <link href=" https://cdn.jsdelivr.net/npm/sweetalert2@11.14.2/dist/sweetalert2.min.css" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

  <!-- Bootstrap JS -->

  <!--Icons and Fonts-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>

  <!--NAVIGATION BAR-->
  <?php
  include 'check_login.php'; // This will check if the user is logged in
  ?>

  <nav class="navbar navbar-expand-lg navbar-container fs-5">
    <div class="container-fluid">
      <!-- Logo -->
      <a class="navbar-brand" href="#">
        <img
          src="/images/logomount-removebg-preview.png"
          alt="Logo"
          width="100"
          height="50"
          class="d-inline-block align-text-top" />
      </a>

      <!-- Toggler for mobile -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navbar Links -->
      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ms-auto">

          <!-- First Dropdown Link -->
          <li class="nav-item dropdown hideOnMobile">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown1" role="button" data-bs-toggle="dropdown" aria-expanded="false"> Explore
            </a>
            <!-- Dropdown for Explore -->
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown1">
              <li>
                <a class="dropdown-item" id="mapsLink" href="systemfeatures/maps/maps.php">
                  <span class="dd-icon material-symbols-outlined">nearby</span>
                  <span class="dd-text">Maps</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item" id="communityLink" href="#" onclick="checkLogin()">
                  <span class="dd-icon material-symbols-outlined">groups</span>
                  <span class="dd-text">Community</span>
                </a>
              </li>
            </ul>
          </li>

          <!-- Bookmark Link without Dropdown -->
          <li class="nav-item hideOnMobile">
            <a class="nav-link" href="userfeatures/bookmarks/bookmarks.php" id="navbarDropdown2" role="button" aria-expanded="false">
              Bookmarks
            </a>
          </li>

          <!-- Profile Picture or Login Link -->
          <li class="nav-item nav-login hideOnMobile">
            <?php if ($loginStatus): ?>
              <a class="nav-link profilecon" id="profileDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img class="profilepic d-none" src="<?php echo htmlspecialchars(getUserImagePath()); ?>" alt="Profile Picture" width="40" height="40" class="rounded-circle">
                <span class="username"><?php echo htmlspecialchars(getUserName()); ?></span>
                <?php
                include 'get_notification_count.php';
                $unread_count = getUnreadNotificationCount($_SESSION['user_id']);
                if ($unread_count > 0) {
                    echo "<span class='profile-notification-count'>$unread_count</span>";
                }
                ?>
              </a>
              <ul class="dropdown-menu profile-dropdown dropdown-menu-end" aria-labelledby="profileDropdown">
                <li>
                  <a class="dropdown-item" href="userfeatures/userprofile/profile.php">
                    <span class="material-symbols-outlined">settings</span>
                    Settings
                  </a>
                </li>
                <li>
                  <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#notificationsModal">
                    <span class="material-symbols-outlined">notifications</span>
                    Notifications
                    <?php
                    if ($unread_count > 0) {
                        echo "<span class='notification-count'>$unread_count</span>";
                    }
                    ?>
                  </button>
                </li>
                <li>
                  <a class="dropdown-item" href="logout.php">
                    <span class="material-symbols-outlined">logout</span>
                    Logout
                  </a>
                </li>
              </ul>
            <?php else: ?>
              <a class="nav-link navlog" href="login.php">Login</a>
            <?php endif; ?>
          </li>
        </ul>
      </div>
    </div>
  </nav>







  <!--HEADER-->
  <header class="hero container-fluid d-flex align-items-center justify-content-center">
    <div class="search">
      <h1>Search it up!</h1>
      <div class="search-container">
        <span class="material-symbols-outlined">search</span>
        <input type="text" placeholder="Search by name" class="search-bar" />
      </div>
      <h3>Explore Nearby Details</h3>
      <div class="search-results"></div> <!-- Results will be displayed here -->
    </div>
  </header>

  <!--BLOG SECTION-->
  <div class="container my-5 ">

    <!-- Blog Section Header -->
    <div class="row mb-4">
      <div class="col text-center">
        <h2 class="blog-section-title">Latest Blog Posts</h2>
        <p class="blog-section-subtitle">Discover ways to connect with nature responsibly</p>
      </div>
    </div>

    <!-- Blog Cards -->
    <div class="row g-5 mx-2">
      <div class="col-md-4">
        <div class="blog card h-200">
          <div style="height: 300px; overflow: hidden;">
            <img src="/images/blog1pic.png" class="card-img-top h-100 w-100" style="object-fit: contain;" alt="..." />
          </div>
          <div class="card-body">
            <h5 class="card-title text-truncate">Life on Land</h5>
            <p class="card-text" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
              Protect, restore and promote sustainable use of terrestrial ecosystems,
              sustainably manage forests, combat desertification,
              and halt and reverse land degradation and halt biodiversity loss
            </p>
            <a href="https://stairwaytosdg.eu/en/sdgs/15-life-on-land" class="btn read-more-btn" style="color: white;">Read More</a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="blog card h-200">
          <div style="height: 300px; overflow: hidden;">
            <img src="/images/blog2pic.png" class="card-img-top h-100 w-100" style="object-fit: contain;" alt="..." />
          </div>
          <div class="card-body">
            <h5 class="card-title text-truncate">Why SDG 15 Matters</h5>
            <p class="card-text" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
              SDG 15 is dedicated to the preservation and sustainable management of terrestrial ecosystems.
              This goal highlights the urgent need to combat land degradation, halt biodiversity loss,
              and promote sustainable land use to maintain the health and vitality of our planet's land-based
              environments..
            </p>
            <a href="https://www.teamfit.eu/en/sdg-goal-15-life-on-land/" class="btn read-more-btn" style="color: white;">Read More</a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="blog card h-200">
          <div style="height: 300px; overflow: hidden;">
            <img src="/images/blog3pic.png" class="card-img-top h-100 w-100" style="object-fit: cover;" alt="..." />
          </div>
          <div class="card-body">
            <h5 class="card-title text-truncate">Overnight at Mt. Sembrano (A Guideline)</h5>
            <p class="card-text" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
              This was my first hike this year, and since we were planning to climb
              Mt. Pulag, we decided to climb this mountain
              first to prepare ourselves for it. Thus, an overnight.
            </p>
            <a href="https://ditokayshellan.com/2020/03/10/overnight-at-mt-sembrano-a-guideline/" class="btn read-more-btn" style="color: white;">Read More</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!--MOUNTAIN CAROUSEL SECTION-->
  <div class="carousel">

    <div class="list">

      <?php
      // Include the database connection file
      include 'db_connection.php'; // Ensure this file contains your database connection code

      // Fetch all mountains from the database
      $sql = "SELECT `mountain_id`, `name`, `location`, `elevation`, `mountain_image`, `difficulty_level`, `description` FROM `mountains`";
      $result = $conn->query($sql);

      // Check if there are any mountains
      if ($result && $result->num_rows > 0) {
        // Loop through the results and generate HTML for each mountain
        while ($row = $result->fetch_assoc()) {
          $mountain_id = htmlspecialchars($row['mountain_id']);
          $name = htmlspecialchars($row['name']);
          $location = htmlspecialchars($row['location']);
          $elevation = htmlspecialchars($row['elevation']);
          $mountain_image = htmlspecialchars($row['mountain_image']);
          $difficulty_level = htmlspecialchars($row['difficulty_level']);
          $description = htmlspecialchars($row['description']);

          // Output HTML for each mountain
          echo "
              <div class='item' style='background-image: url($mountain_image);'>
                  <div class='content'>
                      <div class='title'>MOUNT</div>
                      <div class='name'>$name</div>
                      <div class='des' style='text-align: justify'>$description</div>
                      <div class='btn'>
                          <a href='mountains_profiles.php?mountain_id=$mountain_id'>
                              <button>See More</button>
                          </a>
                          <button class='quick-tip'>Quick Tip</button>
                      </div>
                  </div>
              </div>
              ";
        }
      } else {
        echo "<p>No mountains found.</p>";
      }

      // Close the database connection
      $conn->close();
      ?>



    </div>

    <!--next prev button-->
    <div class="arrows">
      <button class="prev">
        < </button>
          <button class="next">></button>
    </div>


    <!-- time running -->
    <div class="timeRunning"></div>

  </div>

  <!-- Inquiry Form -->
  <div class="container contact my-5">
    <div class="row d-flex align-items-center">
      <!-- Image Container -->
      <div class="col-12 col-md-6 image-container">
        <img src="images/contact.png" alt="" class="img-fluid">
      </div>

      <!-- Inquiry Form -->
      <div class="col col-md-6 mt-6 d-flex flex-column justify-content-center px-5">
        <h2 class="text-center mb-4 mt-4" style="color: green;">Contact Us</h2>
        <form id="inquiry-form"> <!-- Removed action and method -->
          <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
          </div>

          <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <select class="form-select" id="subject" name="subject" required>
              <option value="" disabled selected>Select a subject</option>
              <option value="general_inquiry" class="subject-dropdown">General Inquiry</option>
              <option value="feedback_suggestions" class="subject-dropdown">Feedback or Suggestions</option>
              <option value="account_issues" class="subject-dropdown">Account Issues</option>
              <option value="collaboration_opportunities" class="subject-dropdown">Collaboration Opportunities</option>
              <option value="other" class="subject-dropdown">Other</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" name="message" rows="4" placeholder="Type your message here" required></textarea>
          </div>

          <button type="submit" id="sbmit" class="btn" style="background-color: green; color: white;">Submit</button>
        </form>
      </div>
    </div>
  </div>

  <!--FOOTER-->
  <footer>
    <div class="container-fluid footer1">
      <div class="row p-5 text-center text-md-start">
        <div class="col-12 col-md-4 mb-4">
          <span class="ftr-icon material-symbols-outlined">photo_camera</span>
          <h3 class="mt-3 fs-2 footer-title">Share Your Journey</h3>
          <h3 class="mt-3 fs-4 footer1-des" class="text-center text-md-justify">Connect with fellow adventurers and share your experiences. Tag us in your photos to inspire others!</h3>
        </div>
        <div class="col-12 col-md-4 mb-4">
          <span class="ftr-icon material-symbols-outlined">landscape</span>
          <h3 class="mt-3 fs-2 footer-title">Adventure Awaits</h3>
          <h3 class="mt-3 fs-4 footer1-des" class="text-center text-md-justify">Every adventure brings a new experience. Discover breathtaking trails, hidden gems, and the beauty of nature with us.</h3>
        </div>
        <div class="col-12 col-md-4 mb-4">
          <span class="ftr-icon material-symbols-outlined">explore</span>
          <h3 class="mt-3 fs-2 footer-title">Explore Responsibly</h3>
          <h3 class="mt-3 fs-4 footer1-des" class="text-center text-md-justify">We believe in responsible exploration. Follow our guidelines to leave minimal impact and preserve the beauty of nature.</h3>
        </div>
      </div>
    </div>

    <!--FOOTER 2-->
    <div class="container-fluid footer2">
      <div class="row d-flex justify-content-between mx-5">
        <div class="col-lg-6 mb-4">
          <h3 class="mt-5 text-align: text-center mb-5 footer-title">Members of the team</h3>
          <div class="row d-flex align-items-center">
            <div class="col-4 text-center">
              <div class="img-container">
                <img src="/images/nathanpic.jpg" alt="Icon" class="align-self-center img-fluid" />
              </div>
              <p class="mt-2 member-name">Macalinao, Nathaniel H.</p>
            </div>
            <div class="col-4 text-center">
              <div class="img-container">
                <img src="/images/rickpic.jpg" alt="Icon" class="img-fluid" />
              </div>
              <p class="mt-2 member-name">Macapagal, Rick Andrew M.</p>
            </div>
            <div class="col-4 text-center">
              <div class="img-container">
                <img src="/images/vonpic.jpg" alt="Icon" class="img-fluid" />
              </div>
              <p class="mt-2 member-name">Morales, Von Razel S.</p>
            </div>
          </div>
        </div>
        <div class="col-lg-6 mb-4">
          <h3 class="mt-5 text-align: text-center mb-5 footer-title">Connect with us</h3>
          <div class="social-icons d-flex justify-content-center">
            <a href="https://www.facebook.com/RickAndrew.06" target="_blank" class="me-2">
              <i class="fab fa-facebook fa-lg"></i>
            </a>
            <a href="mailto:example@gmail.com" target="_blank" class="me-2">
              <i class="fas fa-envelope fa-lg"></i>
            </a>
            <a href="https://twitter.com" target="_blank" class="me-2">
              <i class="fab fa-twitter fa-lg"></i>
            </a>
            <a href="https://www.linkedin.com" target="_blank" class="me-2">
              <i class="fab fa-linkedin fa-lg"></i>
            </a>
            <a href="https://www.instagram.com" target="_blank">
              <i class="fab fa-instagram fa-lg"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </footer>


  <!--BOOTSTRAP JS-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

  <!--OWN.JS-->
  <script src="app.js"></script>
  <script src="script.js" defer></script>
  <script src="userfeatures/inquiry/pop-up.js"></script>

  <!--JQUERY-->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>
    $(document).ready(function() {
      $('.search-bar').on('keyup', function() {
        const query = $(this).val();
        if (query.length > 0) {
          $.ajax({
            url: 'systemfeatures/search/search.php', // Update the path here
            method: 'GET',
            data: {
              search: query
            },
            success: function(data) {
              $('.search-results').html(data);
            }
          });
        } else {
          $('.search-results').empty();
        }
      });
    });

    const isLoggedIn = <?php echo json_encode($loginStatus); ?>; // Pass the PHP variable to JS
  </script>

  <!-- Notifications Modal -->
  <div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="notificationsModalLabel">Notifications</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="notificationsContent">
            <!-- Notifications will be loaded here -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
  $(document).ready(function() {
    // Function to load notifications
    function loadNotifications() {
      $.ajax({
        url: 'get_notifications.php',
        method: 'GET',
        success: function(data) {
          $('#notificationsContent').html(data);
          
          // Update notification count via AJAX
          $.ajax({
            url: 'get_unread_count.php',
            method: 'GET',
            success: function(count) {
              if (count > 0) {
                $('.notification-count').text(count).show();
              } else {
                $('.notification-count').hide();
              }
            }
          });
        }
      });
    }

    // Function to update notification counts
    function updateNotificationCounts() {
      $.ajax({
        url: 'get_unread_count.php',
        method: 'GET',
        success: function(count) {
          if (count > 0) {
            $('.notification-count, .profile-notification-count').text(count).show();
          } else {
            $('.notification-count, .profile-notification-count').hide();
          }
        }
      });
    }

    // Load notifications when modal is opened
    $('#notificationsModal').on('show.bs.modal', function () {
      loadNotifications();
      
      // Mark notifications as read when modal is opened
      $.ajax({
        url: 'mark_notifications_read.php',
        method: 'POST',
        success: function() {
          // Hide both notification count badges after marking as read
          $('.notification-count, .profile-notification-count').hide();
        }
      });
    });

    // Update notification counts periodically (every 30 seconds)
    setInterval(updateNotificationCounts, 30000);
  });
  </script>

  <!-- Chat Widget -->
  <div class="chat-widget">
    <div class="chat-button" id="chatButton">
      <span class="material-symbols-outlined">chat</span>
    </div>
    <div class="chat-container" id="chatContainer">
      <div class="chat-header">
        <h4>MountData Assistant</h4>
        <button class="close-chat" id="closeChat">×</button>
      </div>
      <div class="chat-messages" id="chatMessages">
        <div class="message bot">
          Hello! 👋 I'm your MountData assistant. How can I help you today?
        </div>
        <div class="faq-suggestions">
          <button class="faq-button" data-question="How do I create an account?">How do I create an account? 🔐</button>
          <button class="faq-button" data-question="How do I use the map?">How do I use the map? 🗺️</button>
          <button class="faq-button" data-question="Tell me about safety guidelines">Safety Guidelines ⚠️</button>
          <button class="faq-button" data-question="How do I check weather conditions?">Weather Information 🌤️</button>
          <button class="faq-button" data-question="How do I submit an inquiry?">Submit an Inquiry 📧</button>
          <button class="faq-button" data-question="How do I view mountain profiles?">Mountain Profiles 🏔️</button>
        </div>
        <div class="typing-indicator" style="display: none;">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
      <div class="chat-input">
        <input type="text" id="userInput" placeholder="Type your message...">
        <button id="sendMessage">
          <span class="material-symbols-outlined">send</span>
        </button>
      </div>
    </div>
  </div>

  <script src="systemfeatures/chatbot/chatbot.js"></script>

</body>

</html>