document.addEventListener('DOMContentLoaded', function() {
    
    // Header scroll interaction - mimics Alta.com
    function handleHeaderScroll() {
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        var header = document.querySelector('.site-header');
        
        if (scrollTop > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }
    
    // Run on page load
    handleHeaderScroll();
    
    // Run on scroll
    window.addEventListener('scroll', handleHeaderScroll);
    
    // Mobile menu toggle
    var menuToggle = document.querySelector('.main-navigation-toggle');
    var mainNav = document.querySelector('.main-navigation');
    
    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
        });
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        if (mainNav && !e.target.closest('.main-navigation, .main-navigation-toggle')) {
            mainNav.classList.remove('active');
        }
    });
    
}); 