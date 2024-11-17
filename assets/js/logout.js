function confirmLogout(event) {
    event.preventDefault();
    Swal.fire({
      title: 'Are you sure?',
      text: "You will be logged out of your account",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#198754',  // Bootstrap green color
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, log out',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = '../../logout.php';
      }
    });
  }
