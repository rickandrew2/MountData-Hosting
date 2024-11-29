document.getElementById('inquiry-form').addEventListener('submit', function(e) {
    e.preventDefault();

    // Check if form was submitted too quickly (likely a bot)
    if (window.formLoadTime && (Date.now() - window.formLoadTime < 3000)) {
        Swal.fire({
            title: "Error!",
            text: "Please take your time to fill out the form properly.",
            icon: "error"
        });
        return;
    }

    // Get form data
    let formData = new FormData(this);

    // Add reCAPTCHA response to form data
    const recaptchaResponse = grecaptcha.getResponse();
    if (!recaptchaResponse) {
        Swal.fire({
            title: "Error!",
            text: "Please complete the reCAPTCHA verification.",
            icon: "error"
        });
        return;
    }
    formData.append('g-recaptcha-response', recaptchaResponse);

    // Send AJAX request
    fetch('userfeatures/inquiry/submit_inquiry.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Server response:', text);
                throw new Error('Invalid JSON response from server');
            }
        });
    })
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                title: "Success!",
                text: data.message,
                icon: "success",
                iconColor: "green",
                confirmButtonColor: "green"
            });
            document.getElementById('inquiry-form').reset();
            grecaptcha.reset();
        } else {
            Swal.fire({
                title: "Error!",
                text: data.message || "An error occurred while submitting the inquiry.",
                icon: "error"
            });
        }
    })
    .catch(error => {
        console.error('Error details:', error);
        Swal.fire({
            title: "Error!",
            text: "An error occurred while submitting the inquiry. Please try again.",
            icon: "error"
        });
    });
});

// Record form load time
window.formLoadTime = Date.now();