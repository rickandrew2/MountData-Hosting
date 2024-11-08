
document.addEventListener("DOMContentLoaded", function () {
  // constants
  const body = document.querySelector("body"),
      loader = document.querySelector(".loader-wrap"),
      links = document.querySelectorAll('a[href="#"]'),
      nav = document.querySelector("header nav"),
      navToggle = document.querySelector("header nav .navbar-toggler"),
      searchInput = document.querySelector("header nav .search-input"), // Search input
      navNavigationBar = document.querySelector("header nav .navigation-bar"),
      navNavigationBarLi = document.querySelectorAll("header nav .navigation-bar li"),
      headerText = document.querySelector("header .text"),
      headerSection = document.querySelector("header"),
      aboutSection = document.querySelector(".about-us"),
      recipeSection = document.querySelector(".recipes"),
      menuSection = document.querySelector(".menu"),
      fixedImageSection = document.querySelector(".fixed-image"),
      footerSection = document.querySelector("footer"),
      dotOne = document.querySelector(".dots .one"),
      dotTwo = document.querySelector(".dots .two"),
      dotThree = document.querySelector(".dots .three"),
      dots = document.querySelectorAll(".dots > div"),
      logoImage = document.querySelector("header nav .logo img"),
      svgDown = document.querySelector("header .arrow-down"),
      svgUp = document.querySelector(".copyright .arrow-up"),
      menuImgs = document.querySelectorAll(".menu .menu-image-container img"),
      boxModel = document.querySelector(".menu .box-model"),
      menuImageContainer = document.querySelector(".menu-image-container"),
      boxModelArrow = document.querySelector(".menu .box-model .arrow"),
      boxModelImage = document.querySelector(".menu .box-model img"),
      pageTitle = document.querySelector("title");

  // prevent links click hash
  if (links) {
      links.forEach(link =>
          link.addEventListener("click", function (e) {
              e.preventDefault();
          })
      );
  }

  // Toggle search input on hamburger button click
  if (navToggle && searchInput) {
      navToggle.addEventListener("click", function () {
          if (searchInput.style.display === "none" || searchInput.style.display === "") {
              searchInput.style.display = "block"; // Show search input
          } else {
              searchInput.style.display = "none"; // Hide search input
          }
      });
  }

  // Show active navigation bar li
  if (navNavigationBarLi) {
      navNavigationBarLi.forEach(li =>
          li.addEventListener("click", () => {
              const arr = Array.from(li.parentElement.children);
              arr.forEach(li => li.classList.remove("active"));
              li.classList.add("active");
          })
      );
  }
});


// Define an array of responsible hiking tips
const hikingTips = [
    "Leave no trace: Pack out all trash and dispose of it properly.",
    "Stay on marked trails to avoid damaging vegetation.",
    "Respect wildlife: Observe from a distance and never feed animals.",
    "Carry reusable water bottles to reduce plastic waste.",
    "Be mindful of noise and keep the environment peaceful.",
    "Use biodegradable products to protect the ecosystem.",
    "Plan ahead and prepare: Know the regulations and terrain.",
    "Share the trail: Yield to uphill hikers and be courteous to others.",
    "Avoid damaging or removing natural features like rocks or plants."
  ];
  
  // Add event listener for "Quick Tip" buttons
  document.addEventListener('DOMContentLoaded', () => {
    const quickTipButtons = document.querySelectorAll('.quick-tip');
  
    quickTipButtons.forEach(button => {
      button.addEventListener('click', () => {
        // Get a random hiking tip
        const randomTip = hikingTips[Math.floor(Math.random() * hikingTips.length)];
        
        // Display the tip using a customized SweetAlert
        Swal.fire({
            title: '🌿 Quick Tip for Responsible Hiking 🌿',
            text: randomTip,
            icon: 'info',
            background: 'white', // Light green background
            color: '#2f855a', // Dark green text
            confirmButtonColor: '#38a169', // Green confirm button
            confirmButtonText: 'Got it!',
            padding: '2em',
            customClass: {
                popup: 'popup-class', // Add custom class for extra styling if needed
                title: 'title-class', // Custom title class for styling
                confirmButton: 'confirm-button-class'
            },
            buttonsStyling: false // Disable default styling to allow custom styles
        });            
      });
    });
  });


  function checkLogin() {
    if (isLoggedIn) {
        // Redirect to community.php
        window.location.href = '../systemfeatures/community/community.php';
    } else {
        // Show SweetAlert
        Swal.fire({
            title: 'Access Denied!',
            text: 'You need to log in to access the Community page.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: "green",
            cancelButtonColor: "#d33",
            confirmButtonText: "Login",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.value) {
                // Redirect to the login page
                window.location.href = "login.php"; // Change to your login page
            }
        });
        buttonsStyling: false // Disable default styling for buttons
    }
}


// Prevent zooming with the mouse wheel or pinch gesture on mobile
document.addEventListener('wheel', function(event) {
    if (event.ctrlKey) {  // Zooming is typically triggered with the ctrl key + mouse wheel
        event.preventDefault();
    }
}, { passive: false });

// Prevent zoom on mobile (pinch zoom)
document.addEventListener('touchmove', function(event) {
    if (event.scale !== 1) {
        event.preventDefault(); // Prevent pinch zoom
    }
}, { passive: false });
