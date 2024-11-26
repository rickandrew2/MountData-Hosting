$(document).ready(function() {
    // Adjust container visibility with proper transitions
    $('#editIcon').click(function() {
        $('#profileContainer').fadeOut(300, function() {
            $('#editProfileContainer').removeClass('d-none').hide().fadeIn(300);
        });
    });

    // Handle window resize for responsive adjustments
    $(window).resize(function() {
        adjustContainerSpacing();
    });

    // Initial call to adjust spacing
    adjustContainerSpacing();

    // Function to adjust container spacing based on screen size
    function adjustContainerSpacing() {
        if (window.innerWidth <= 768) { // Mobile breakpoint
            $('.profile-and-feed').addClass('mobile-spacing');
            $('.edit-profile-container').addClass('mobile-spacing');
        } else {
            $('.profile-and-feed').removeClass('mobile-spacing');
            $('.edit-profile-container').removeClass('mobile-spacing');
        }
    }

    const form = document.querySelector("form");
    const cancelBtn = document.getElementById("cancelEdit");
    let originalName = document.getElementById("editName").value;
    let originalEmail = document.getElementById("editEmail").value;
    let originalContact = document.getElementById("editContact").value;

    // Function to get the query parameter from the URL
    function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    // Function to remove query parameters and update URL
    function removeQueryParams() {
        const url = window.location.pathname;
        window.history.replaceState({}, document.title, url);
    }

    // Check for message in the URL
    const message = getQueryParam('message');

    if (message === "success") {
        Swal.fire({
            title: "Profile Updated Successfully",
            icon: 'success',
            confirmButtonText: 'Okay',
            confirmButtonColor: "green"
        }).then(() => {
            // Remove query parameters after showing the alert
            removeQueryParams();
        });
    } else if (message === "username_error") {
        Swal.fire({
            title: "Username already exists. Please choose another.",
            icon: 'error',
            confirmButtonText: 'Okay',
            confirmButtonColor: "green"
        });
    } else if (message === "email_error") {
        Swal.fire({
            title: "Email Already Exists",
            text: "This email address is already registered. Please use a different email.",
            icon: 'error',
            confirmButtonText: 'Okay',
            confirmButtonColor: "green"
        });
    } else if (message === "invalid_email") {
        Swal.fire({
            title: "Invalid Email Format",
            text: "Please enter a valid Gmail address (example@gmail.com)",
            icon: 'error',
            confirmButtonText: 'Okay',
            confirmButtonColor: "green"
        });
    } else if (message === "invalid_contact") {
        Swal.fire({
            title: "Invalid contact number format",
            text: "Please enter a valid Philippine mobile number (e.g., 09xxxxxxxxx or +639xxxxxxxxx)",
            icon: 'error',
            confirmButtonText: 'Okay',
            confirmButtonColor: "green"
        });
    } else if (message === "invalid_password") {
        Swal.fire({
            title: "Invalid Password Format",
            text: "Password must be at least 5 characters long, contain at least one uppercase letter and one symbol (!@#$%^&*?)",
            icon: 'error',
            confirmButtonText: 'Okay',
            confirmButtonColor: "green"
        });
    }

    cancelBtn.addEventListener("click", function () {
        // Redirect smoothly to profile.php
        window.location.href = "profile.php";
    });

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const newName = document.getElementById("editName").value;
        const newEmail = document.getElementById("editEmail").value;
        const newContact = document.getElementById("editContact").value;
        const newPassword = document.getElementById("editPassword").value;

        // First check email format
        const emailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
        if (!emailRegex.test(newEmail)) {
            Swal.fire({
                title: "Invalid Email Format",
                text: "Please enter a valid Gmail address (example@gmail.com)",
                icon: 'error',
                confirmButtonText: 'Okay',
                confirmButtonColor: "green"
            });
            return;
        }

        // Check contact number format (Philippine format)
        const contactRegex = /^(09|\+639)\d{9}$/;
        if (!contactRegex.test(newContact)) {
            Swal.fire({
                title: "Invalid Contact Number Format",
                text: "Please enter a valid Philippine mobile number (e.g., 09xxxxxxxxx or +639xxxxxxxxx)",
                icon: 'error',
                confirmButtonText: 'Okay',
                confirmButtonColor: "green"
            });
            return;
        }

        // Password validation regex
        const passwordRegex = /^(?=.*[A-Z])(?=.*[!@#$%^&*?])[A-Za-z\d!@#$%^&*?]{5,}$/;

        // If password field is not empty, validate it
        if (newPassword && !passwordRegex.test(newPassword)) {
            Swal.fire({
                title: "Invalid Password Format",
                text: "Password must be at least 5 characters long, contain at least one uppercase letter and one symbol (!@#$%^&*?)",
                icon: 'error',
                confirmButtonText: 'Okay',
                confirmButtonColor: "green"
            });
            return;
        }

        // Check if email exists (if it's different from original email)
        if (newEmail !== originalEmail) {
            // Make AJAX call to check email existence
            $.ajax({
                url: 'check_email.php',
                type: 'POST',
                data: { email: newEmail },
                success: function(response) {
                    if (response.exists) {
                        Swal.fire({
                            title: "Email Already Exists",
                            text: "This email address is already registered. Please use a different email.",
                            icon: 'error',
                            confirmButtonText: 'Okay',
                            confirmButtonColor: "green"
                        });
                    } else {
                        // If email has changed, show confirmation dialog
                        Swal.fire({
                            title: 'Confirm Email Change',
                            text: "Are you sure you want to change your email address?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: 'green',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, change it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Something went wrong. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        } else {
            // Check if any other changes were made
            if (newName === originalName && 
                newEmail === originalEmail && 
                newContact === originalContact) {
                // No changes made, redirect smoothly
                window.location.href = "profile.php";
            } else {
                // Other changes were made, submit the form
                form.submit();
            }
        }
    });
});


function uploadImage(event) {
    const fileInput = event.target;
    const formData = new FormData();
    const file = fileInput.files[0];

    if (file) {
        formData.append('profile_image', file);

        // Send an AJAX request to upload the profile picture
        fetch('update_profile_picture.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.text())
        .then(data => {
            if (data.includes("Profile picture updated successfully")) {
                Swal.fire({
                    icon: 'success',
                    title: 'Profile Updated',
                    text: 'Your profile picture has been updated successfully!',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok',
                    background: '#f0f8ff', // Light background for a refreshing feel
                    backdrop: `
                        rgba(0, 0, 0, 0.5)
                        url('../../../images/login-bg.jpg') // Add a mountain-themed backdrop image
                        center top
                        no-repeat
                    `,
                    customClass: {
                        title: 'swal-title', // Custom class for the title
                        content: 'swal-content', // Custom class for the content
                        confirmButton: 'swal-confirm-button' // Custom class for the button
                    },
                    footer: '<span style=\"color: gray;\">Thank you for updating your profile!</span>',
                    willClose: () => {
                        // Reload the page to reflect the updated profile picture
                        location.reload();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: data, // Display the error message from the server
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ok'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while updating the profile picture. Please try again later.',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ok'
            });
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'No File Selected',
            text: 'Please choose an image file to upload.',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ok'
        });
    }
}

document.getElementById('deleteAccountBtn').addEventListener('click', function() {
    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone. All your data will be permanently deleted.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete my account',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Make AJAX call to delete account
            $.ajax({
                url: 'delete_account.php',
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Account Deleted',
                            text: 'Your account has been successfully deleted.',
                            icon: 'success',
                            confirmButtonColor: '#3085d6'
                        }).then(() => {
                            window.location.href = '../../login.php';
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message || 'Failed to delete account. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Something went wrong. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        }
    });
});

