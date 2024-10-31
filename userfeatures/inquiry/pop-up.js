document.getElementById('inquiry-form').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission

    // Get form data
    let formData = new FormData(this);

    // Send AJAX request
    fetch('userfeatures/inquiry/submit_inquiry.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text()) // Process the response from PHP
    .then(data => {
        // Assuming PHP returns "Inquiry submitted successfully!" on success
        if (data.includes("Inquiry submitted successfully!")) {
            Swal.fire({
                title: "Success!",
                text: "Inquiry Submitted Successfully",
                icon: "success",
                iconColor: "green",
                confirmButtonColor: "green" // Set the "OK" button background color to green
            });
            // Optional: clear form after success
            document.getElementById('inquiry-form').reset();
        } else {
            Swal.fire({
                title: "Error!",
                text: "There was an issue submitting your inquiry.",
                icon: "error"
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: "Error!",
            text: "An error occurred while submitting the inquiry.",
            icon: "error"
        });
        console.error('Error:', error);
    });
});
