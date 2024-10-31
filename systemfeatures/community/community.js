document.addEventListener('DOMContentLoaded', () => {
    const likeContainers = document.querySelectorAll('.like-container');

    likeContainers.forEach(container => {
        const icon = container.querySelector('.icon'); // Select the icon element

        container.addEventListener('click', () => {
            icon.classList.toggle('liked'); // Toggle the liked class on the icon
            // Logic to update the like count can go here
        });
    });
});
