document.addEventListener('DOMContentLoaded', () => {

    // --- Image Carousel Logic ---

    // Array of image sources for the carousel
    const slides = [
        'images/1.webp',
        'images/2.webp',
        'images/3.webp',
        'images/4.webp',
        'images/5.webp',
        'images/6.webp',
        'images/7.webp',
        'images/8.webp',
    ];

    let currentSlideIndex = 0;
    const carouselImage = document.querySelector('.carousel-slide img');
    const dots = document.querySelectorAll('.dot');

    // Function to show a specific slide
    function showSlide(index) {
        // Check for valid index
        if (index >= slides.length) {
            currentSlideIndex = 0;
        } else if (index < 0) {
            currentSlideIndex = slides.length - 1;
        } else {
            currentSlideIndex = index;
        }

        // Change the image source
        carouselImage.src = slides[currentSlideIndex];

        // Update the active state of the dots
        dots.forEach((dot, i) => {
            if (i === currentSlideIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    // Function to show the next slide
    function nextSlide() {
        showSlide(currentSlideIndex + 1);
    }

    // Set an interval to automatically change the slide every 3 seconds (3000ms)
    setInterval(nextSlide, 3000);

    // Add click event listeners to the dots
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showSlide(index);
        });
    });

    // Initialize the first slide
    showSlide(currentSlideIndex);

});
