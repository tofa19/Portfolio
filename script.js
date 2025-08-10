document.addEventListener('DOMContentLoaded', () => {

    // --- Initialize AOS (Animate on Scroll) ---
    AOS.init({
        duration: 800, // values from 0 to 3000, with step 50ms
        once: true,     // whether animation should happen only once - while scrolling down
        offset: 100,    // offset (in px) from the original trigger point
    });

    // --- Fullscreen Menu Toggle ---
    const menuLinks = document.querySelectorAll('.fullscreen-menu-nav a');

    // Function to open the menu
    document.getElementById('menu-open-btn').onclick = function () {
        document.getElementById('fullscreen-menu').classList.add('active');
    };

    // Function to close the menu
    document.getElementById('menu-close-btn').onclick = function () {
        document.getElementById('fullscreen-menu').classList.remove('active');
    };

    // Add event listener to each menu link to close the menu on click
    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            document.getElementById('fullscreen-menu').classList.remove('active');
            document.body.style.overflow = '';
        });
    });

    // --- Smooth Scrolling for Anchor Links ---
    // This is handled by CSS 'scroll-behavior: smooth' but can be enhanced with JS if needed.
    // The current implementation is simple and effective.

    // --- Set Current Year in Footer ---
    const currentYearSpan = document.getElementById('current-year');
    if (currentYearSpan) {
        currentYearSpan.textContent = new Date().getFullYear();
    }

    // --- Search Box Expand from Menu Section ---
    const searchBtn = document.getElementById('search-btn');
    const searchBar = document.getElementById('header-search-bar');
    const headerActions = document.querySelector('.header-actions');
    const headerContainer = document.querySelector('.header-container');

    searchBtn.onclick = function(e) {
        e.preventDefault();
        document.getElementById('header-search-bar').classList.add('active');
        document.querySelector('.header-search-input').focus();
    };

    document.getElementById('header-search-close').onclick = function() {
        document.getElementById('header-search-bar').classList.remove('active');
    };

});
