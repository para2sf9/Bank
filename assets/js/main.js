// PGi Bankings & Finance - Main JavaScript

document.addEventListener('DOMContentLoaded', function() {

    // Mobile menu toggle
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    const sidebar = document.querySelector('.sidebar');

    if (hamburger) {
        hamburger.addEventListener('click', function() {
            this.classList.toggle('active');
            if (navMenu) navMenu.classList.toggle('active');
            if (sidebar) sidebar.classList.toggle('open');
        });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Scroll reveal animation
    const revealElements = document.querySelectorAll(
        '.service-card, .loan-card, .stat-card, .feature-item, .section-header, .hero-content, .hero-card, .cta, .footer-grid > div'
    );

    const revealOnScroll = () => {
        const windowHeight = window.innerHeight;
        const elementVisible = 100;

        revealElements.forEach((element, index) => {
            const elementTop = element.getBoundingClientRect().top;

            if (elementTop < windowHeight - elementVisible) {
                element.classList.add('revealed');
                element.style.transitionDelay = (index * 0.05) + 's';
            }
        });
    };

    // Initial check
    revealOnScroll();

    // Check on scroll with throttling
    let scrollTimeout;
    window.addEventListener('scroll', () => {
        if (scrollTimeout) return;
        scrollTimeout = setTimeout(() => {
            revealOnScroll();
            scrollTimeout = null;
        }, 10);
    });

    // Navbar background on scroll
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.08)';
                navbar.style.background = 'rgba(255,255,255,0.98)';
            } else {
                navbar.style.boxShadow = 'none';
                navbar.style.background = 'rgba(255,255,255,0.95)';
            }
        });
    }

    // Parallax effect for hero
    const hero = document.querySelector('.hero');
    if (hero) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * 0.3;
            const heroContent = hero.querySelector('.hero-content');
            if (heroContent && scrolled < window.innerHeight) {
                heroContent.style.transform = 'translateY(' + rate + 'px)';
                heroContent.style.opacity = 1 - (scrolled / 700);
            }
        });
    }

    // Counter animation for stats
    const statNumbers = document.querySelectorAll('.stat-number');
    let countersAnimated = false;

    const animateCounters = () => {
        if (countersAnimated) return;

        const statsSection = document.querySelector('.stats');
        if (!statsSection) return;

        const rect = statsSection.getBoundingClientRect();
        if (rect.top < window.innerHeight - 100) {
            countersAnimated = true;

            statNumbers.forEach(stat => {
                const target = stat.textContent;
                const isNumber = /[0-9]/.test(target);
                if (!isNumber) return;

                const numericValue = parseFloat(target.replace(/[^0-9.]/g, ''));
                const suffix = target.replace(/[0-9.]/g, '');
                const duration = 2000;
                const startTime = performance.now();

                const updateCounter = (currentTime) => {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);

                    // Easing function
                    const easeOutQuart = 1 - Math.pow(1 - progress, 4);
                    const current = numericValue * easeOutQuart;

                    if (target.includes('.')) {
                        stat.textContent = current.toFixed(2) + suffix;
                    } else {
                        stat.textContent = Math.floor(current) + suffix;
                    }

                    if (progress < 1) {
                        requestAnimationFrame(updateCounter);
                    } else {
                        stat.textContent = target;
                    }
                };

                requestAnimationFrame(updateCounter);
            });
        }
    };

    window.addEventListener('scroll', () => {
        if (scrollTimeout) return;
        scrollTimeout = setTimeout(() => {
            animateCounters();
            scrollTimeout = null;
        }, 10);
    });

    // Add ripple effect to buttons
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255,255,255,0.4);
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                animation: rippleEffect 0.6s ease-out;
                pointer-events: none;
            `;

            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);

            setTimeout(() => ripple.remove(), 600);
        });
    });

    // Add ripple keyframe dynamically
    const style = document.createElement('style');
    style.textContent = `
        @keyframes rippleEffect {
            0% { transform: scale(0); opacity: 0.5; }
            100% { transform: scale(2.5); opacity: 0; }
        }
    `;
    document.head.appendChild(style);

});
