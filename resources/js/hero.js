/**
 * Hero Slideshow Functionality
 * Handles automatic slide transitions and manual navigation
 */

document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('hero-carousel');
    if (!carousel) return;

    const slides = carousel.querySelectorAll('[data-slide]');
    const indicators = carousel.querySelectorAll('[data-slide-to]');
    
    let currentSlide = 0;
    let slideInterval;
    const slideDelay = 5000; // 5 seconds

    // Initialize slideshow
    function initializeSlideshow() {
        if (slides.length <= 1) return;
        
        // Set up slide navigation
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => goToSlide(index));
        });

        // Start automatic slideshow
        startAutoSlide();
        
        // Pause on hover
        carousel.addEventListener('mouseenter', stopAutoSlide);
        carousel.addEventListener('mouseleave', startAutoSlide);
    }

    // Go to specific slide
    function goToSlide(index) {
        if (index === currentSlide || index >= slides.length) return;
        
        // Hide current slide
        slides[currentSlide].classList.remove('opacity-100');
        slides[currentSlide].classList.add('opacity-0');
        
        // Update current indicator
        if (indicators[currentSlide]) {
            indicators[currentSlide].classList.remove('bg-white', 'scale-125');
            indicators[currentSlide].classList.add('bg-white/50');
        }
        
        // Show new slide
        currentSlide = index;
        slides[currentSlide].classList.remove('opacity-0');
        slides[currentSlide].classList.add('opacity-100');
        
        // Update new indicator
        if (indicators[currentSlide]) {
            indicators[currentSlide].classList.remove('bg-white/50');
            indicators[currentSlide].classList.add('bg-white', 'scale-125');
        }
    }

    // Go to next slide
    function nextSlide() {
        const next = (currentSlide + 1) % slides.length;
        goToSlide(next);
    }

    // Start automatic slideshow
    function startAutoSlide() {
        if (slides.length <= 1) return;
        
        stopAutoSlide(); // Clear any existing interval
        slideInterval = setInterval(nextSlide, slideDelay);
    }

    // Stop automatic slideshow
    function stopAutoSlide() {
        if (slideInterval) {
            clearInterval(slideInterval);
            slideInterval = null;
        }
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (!carousel.matches(':hover')) return;
        
        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            const prev = currentSlide === 0 ? slides.length - 1 : currentSlide - 1;
            goToSlide(prev);
        } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            nextSlide();
        }
    });

    // Touch/swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;

    carousel.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    carousel.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });

    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;

        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                // Swiped left - next slide
                nextSlide();
            } else {
                // Swiped right - previous slide
                const prev = currentSlide === 0 ? slides.length - 1 : currentSlide - 1;
                goToSlide(prev);
            }
        }
    }

    // Intersection Observer for performance
    const observerOptions = {
        root: null,
        threshold: 0.1
    };

    const carouselObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                startAutoSlide();
            } else {
                stopAutoSlide();
            }
        });
    }, observerOptions);

    carouselObserver.observe(carousel);

    // Initialize everything
    initializeSlideshow();

    // Cleanup on page unload
    window.addEventListener('beforeunload', stopAutoSlide);
});
