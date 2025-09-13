// Scroll Animation Handler
document.addEventListener('DOMContentLoaded', () => {
    // Setup Intersection Observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            // Add 'in-view' class when element is visible
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                // Once the animation has played, no need to watch it anymore
                observer.unobserve(entry.target);
            }
        });
    }, {
        root: null, // Use viewport as root
        rootMargin: '0px 0px -100px 0px', // Slightly above the bottom of viewport
        threshold: 0.1 // Trigger when 10% of the element is visible
    });

    // Get all elements with animation classes
    const animatedElements = document.querySelectorAll(
        '.scroll-fade-up, .scroll-fade-in, .scroll-fade-right, .scroll-fade-left, .scroll-scale-up'
    );

    // Observe each element
    animatedElements.forEach(el => {
        observer.observe(el);
    });
});