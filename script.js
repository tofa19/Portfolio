document.addEventListener('DOMContentLoaded', () => {
    // 1. Set current year in the footer dynamically
    const currentYearSpan = document.getElementById('current-year');
    if (currentYearSpan) {
        currentYearSpan.textContent = new Date().getFullYear();
    }

    // Update copyright year
    document.getElementById('current-year').textContent = new Date().getFullYear();

    // 2. Smooth scrolling for all internal navigation links (those starting with '#')
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent default jump behavior

            // Scroll smoothly to the target section
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });

            // If mobile menu is open, close it after clicking a link
            const navMenu = document.querySelector('.nav-list');
            if (navMenu && navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
            }
        });
    });

    // 3. Mobile Navigation Toggle (Hamburger Menu)
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-list');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active'); // Toggle 'active' class to show/hide menu
        });
    }

    // Hamburger Menu Animation
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            this.classList.toggle('active');
            const sideMenu = document.getElementById('side-menu');
            if (sideMenu) {
                sideMenu.classList.toggle('active');
                
                // Add animation delay to each nav item
                const navItems = sideMenu.querySelectorAll('.nav-list li');
                navItems.forEach((item, index) => {
                    item.style.setProperty('--i', index + 1);
                });
            }
        });
    }

    // Optional: Highlight active navigation link based on scroll position
    // This function adds an 'active' class to the current section's nav link
    const sections = document.querySelectorAll('section[id]'); // Get all sections with an ID
    const navLinks = document.querySelectorAll('.nav-list a'); // Get all nav links

    function highlightNavLink() {
        let current = ''; // To store the ID of the current section in view

        // Loop through each section to determine which one is currently visible
        sections.forEach(section => {
            const sectionTop = section.offsetTop; // Top position of the section
            // Adjust offset to trigger 'active' state slightly before the section reaches the very top
            // This makes the active state change feel more natural as you scroll
            const sectionHeight = section.clientHeight;
            if (pageYOffset >= sectionTop - sectionHeight / 3) { // Adjust offset as needed
                current = section.getAttribute('id'); // Set current to the section's ID
            }
        });

        // Remove 'active' class from all links, then add it to the current one
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href').includes(current)) {
                link.classList.add('active');
            }
        });
    }

    // Add scroll event listener to update active link
    window.addEventListener('scroll', highlightNavLink);
    // Call it once on load to set the initial active link if page is scrolled
    highlightNavLink();

    // Scroll-based header animation
    let lastScroll = 0;
    const header = document.querySelector('.main-header');
    
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll <= 0) {
            header.classList.remove('scroll-up');
            return;
        }
        
        if (currentScroll > lastScroll && !header.classList.contains('scroll-down')) {
            header.classList.remove('scroll-up');
            header.classList.add('scroll-down');
        } else if (currentScroll < lastScroll && header.classList.contains('scroll-down')) {
            header.classList.remove('scroll-down');
            header.classList.add('scroll-up');
        }
        lastScroll = currentScroll;
    });

    // LLM Integration: Generate Professional Bio ✨
    const generateBioBtn = document.getElementById('generate-bio-btn');
    const originalBioP1 = document.getElementById('original-bio-p1');
    const originalBioP2 = document.getElementById('original-bio-p2');
    const llmBioOutput = document.getElementById('llm-bio-output');
    const loadingMessage = llmBioOutput ? llmBioOutput.querySelector('.loading-message') : null;

    if (generateBioBtn && originalBioP1 && originalBioP2 && llmBioOutput) {
        generateBioBtn.addEventListener('click', async () => {
            // Combine the text from the two original bio paragraphs
            const fullBioText = originalBioP1.textContent + "\n\n" + originalBioP2.textContent;

            // Define the prompt for the LLM to rephrase the bio
            const prompt = `Rephrase the following personal biography to be more concise and professional, suitable for a personal website's 'About Me' section. Focus on achievements, skills, and career goals, and keep it under 150 words. Do not include a greeting like "Hello!" or an introductory phrase. Just provide the rephrased bio:\n\n${fullBioText}`;

            // Show loading message and disable button during API call
            if (loadingMessage) {
                loadingMessage.style.display = 'block';
                // Clear any previous generated content but keep the loading message element
                llmBioOutput.innerHTML = '';
                llmBioOutput.appendChild(loadingMessage);
            }
            llmBioOutput.style.display = 'block'; // Ensure the output area is visible
            generateBioBtn.disabled = true; // Disable the button to prevent multiple clicks

            try {
                let chatHistory = [];
                chatHistory.push({ role: "user", parts: [{ text: prompt }] });
                const payload = { contents: chatHistory };
                // The API key will be automatically provided by the Canvas environment
                const apiKey = ""; 

                const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=${apiKey}`;

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(`API error: ${response.status} - ${errorData.error.message}`);
                }

                const result = await response.json();

                // Check if the response contains generated content
                if (result.candidates && result.candidates.length > 0 &&
                    result.candidates[0].content && result.candidates[0].content.parts &&
                    result.candidates[0].content.parts.length > 0) {
                    const generatedText = result.candidates[0].content.parts[0].text;
                    // Display the generated text and a 'Revert' button
                    llmBioOutput.innerHTML = `<p class="generated-text">${generatedText}</p><button class="btn btn-secondary btn-sm mt-2" onclick="revertBio()">Revert</button>`;
                } else {
                    // Handle cases where the response structure is unexpected or content is missing
                    llmBioOutput.innerHTML = '<p class="error-message">Could not generate bio. The AI response was empty or malformed. Please try again.</p>';
                }
            } catch (error) {
                console.error('Error generating bio:', error);
                // Display error message to the user
                llmBioOutput.innerHTML = `<p class="error-message">Error generating bio: ${error.message}. Please check console for details.</p>`;
            } finally {
                // Hide loading message and re-enable button regardless of success or failure
                if (loadingMessage) {
                    loadingMessage.style.display = 'none';
                }
                generateBioBtn.disabled = false;
            }
        });

        // Function to revert to original bio content
        window.revertBio = () => {
            llmBioOutput.innerHTML = ''; // Clear generated content
            llmBioOutput.style.display = 'none'; // Hide the output area
        };
    }

    // Sliding Menu Toggle with Overlay and Animation
    const menuToggle = document.getElementById('menu-toggle');
    const sideNav = document.getElementById('side-nav');
    const menuOverlay = document.getElementById('menu-overlay');
    const navItems = document.querySelectorAll('.nav-list li');

    function toggleMenu() {
        menuToggle.classList.toggle('active');
        sideNav.classList.toggle('active');
        menuOverlay.classList.toggle('active');
        
        // Animate nav items
        navItems.forEach((item, index) => {
            if (sideNav.classList.contains('active')) {
                item.style.opacity = '1';
                item.style.transform = 'translateX(0)';
                item.style.transitionDelay = `${index * 0.1}s`;
            } else {
                item.style.opacity = '0';
                item.style.transform = 'translateX(-20px)';
                item.style.transitionDelay = '0s';
            }
        });
    }

    // Toggle menu on button click
    menuToggle.addEventListener('click', toggleMenu);

    // Close menu when clicking overlay
    menuOverlay.addEventListener('click', toggleMenu);

    // Close menu when clicking menu items
    navItems.forEach(item => {
        item.addEventListener('click', toggleMenu);
    });

    // Harvard-style fullscreen menu open/close with top-down and bottom-up transitions

    // Get elements
    const menuBtn = document.getElementById('menu-toggle');
    const fullscreenMenu = document.querySelector('.fullscreen-menu');
    const closeBtn = document.querySelector('.fullscreen-menu-close');

    // Open menu
    menuBtn.addEventListener('click', () => {
        fullscreenMenu.classList.remove('closing');
        fullscreenMenu.classList.add('open');
        // Animate menu items in
        document.querySelectorAll('.fullscreen-menu-nav li').forEach((li, i) => {
            li.style.setProperty('--i', i);
        });
    });

    // Close menu
    closeBtn.addEventListener('click', () => {
        fullscreenMenu.classList.remove('open');
        fullscreenMenu.classList.add('closing');
        // After animation, hide menu
        setTimeout(() => {
            fullscreenMenu.classList.remove('closing');
        }, 500);
    });

    // Initialize AOS (Animate On Scroll)
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100,
        easing: 'ease-in-out'
    });

    // Fade in text elements as they become visible
    const fadeInElements = document.querySelectorAll('.fade-in');
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };

    const fadeInObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    fadeInElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        element.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
        fadeInObserver.observe(element);
    });

    // Updated Sliding Menu Toggle with Overlay and Close Button
    const overlay = document.getElementById('menu-overlay');
    const sideNavCloseBtn = document.querySelector('.side-nav .close-btn');

    menuBtn.addEventListener('click', () => {
        sideNav.classList.add('active');
        overlay.classList.add('active');
        document.body.classList.add('menu-open');
    });

    sideNavCloseBtn.addEventListener('click', () => {
        sideNav.classList.remove('active');
        overlay.classList.remove('active');
        document.body.classList.remove('menu-open');
    });

    overlay.addEventListener('click', () => {
        sideNav.classList.remove('active');
        overlay.classList.remove('active');
        document.body.classList.remove('menu-open');
    });
});
