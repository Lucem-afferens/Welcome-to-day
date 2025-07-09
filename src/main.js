// Main JavaScript file for InviteWeb landing page

class InviteWebApp {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initAnimations();
        this.initCountdown();
        this.initSmoothScrolling();
        this.initIntersectionObserver();
    }

    setupEventListeners() {
        // Coming soon button
        const comingSoonBtn = document.getElementById('comingSoonBtn');
        if (comingSoonBtn) {
            comingSoonBtn.addEventListener('click', () => this.openModal());
        }

        // Close modal button
        const closeModalBtn = document.getElementById('closeModal');
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', () => this.closeModal());
        }

        // Modal backdrop click
        const modal = document.getElementById('comingSoonModal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeModal();
                }
            });
        }

        // Newsletter form
        const newsletterForm = document.getElementById('newsletterForm');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', (e) => this.handleNewsletterSubmit(e));
        }

        // Contact button
        const contactBtn = document.getElementById('contactBtn');
        if (contactBtn) {
            contactBtn.addEventListener('click', () => this.handleContactClick());
        }

        // Keyboard events
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeModal();
            }
        });

        // Window resize
        window.addEventListener('resize', () => this.handleResize());
    }

    initAnimations() {
        // Check if device is touch-based
        const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        
        // Parallax effect for background shapes (only on non-touch devices)
        if (!isTouchDevice) {
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const shapes = document.querySelectorAll('.bg-shape');
                
                shapes.forEach((shape, index) => {
                    const speed = 0.5 + (index * 0.1);
                    const yPos = -(scrolled * speed);
                    shape.style.transform = `translateY(${yPos}px)`;
                });
            });
        }

        // Floating cards animation
        const cards = document.querySelectorAll('.floating-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 2}s`;
        });

        // Button hover effects
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', () => {
                button.style.transform = 'translateY(-2px) scale(1.02)';
            });
            
            button.addEventListener('mouseleave', () => {
                button.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Service cards hover effect
        const serviceCards = document.querySelectorAll('.service-card');
        serviceCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) scale(1)';
            });
        });
    }

    initCountdown() {
        // Set target date (July 20, 2025)
        const targetDate = new Date('2025-07-20T00:00:00');
        
        const updateCountdown = () => {
            const now = new Date().getTime();
            const distance = targetDate.getTime() - now;

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Update DOM elements
            const daysEl = document.getElementById('days');
            const hoursEl = document.getElementById('hours');
            const minutesEl = document.getElementById('minutes');
            const secondsEl = document.getElementById('seconds');

            if (daysEl) daysEl.textContent = days.toString().padStart(2, '0');
            if (hoursEl) hoursEl.textContent = hours.toString().padStart(2, '0');
            if (minutesEl) minutesEl.textContent = minutes.toString().padStart(2, '0');
            if (secondsEl) secondsEl.textContent = seconds.toString().padStart(2, '0');

            // Add animation to countdown numbers
            [daysEl, hoursEl, minutesEl, secondsEl].forEach(el => {
                if (el) {
                    el.style.animation = 'pulse 0.5s ease';
                    setTimeout(() => {
                        el.style.animation = '';
                    }, 500);
                }
            });
        };

        // Update countdown every second
        updateCountdown();
        setInterval(updateCountdown, 1000);
    }

    initSmoothScrolling() {
        const navLinks = document.querySelectorAll('.nav__link, .footer__links a');
        
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('href');
                const targetSection = document.querySelector(targetId);
                
                if (targetSection) {
                    const headerHeight = document.querySelector('.header').offsetHeight;
                    const targetPosition = targetSection.offsetTop - headerHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    initIntersectionObserver() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('loaded');
                }
            });
        }, observerOptions);

        // Observe elements for animation
        const animatedElements = document.querySelectorAll('.about__item, .service-card, .contact__item');
        animatedElements.forEach(el => {
            el.classList.add('loading');
            observer.observe(el);
        });
    }

    openModal() {
        const modal = document.getElementById('comingSoonModal');
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            // Add entrance animation
            setTimeout(() => {
                modal.querySelector('.modal__content').style.transform = 'scale(1)';
            }, 100);
        }
    }

    closeModal() {
        const modal = document.getElementById('comingSoonModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
            
            // Reset modal content transform
            setTimeout(() => {
                modal.querySelector('.modal__content').style.transform = 'scale(0.9)';
            }, 300);
        }
    }

    handleNewsletterSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const emailInput = form.querySelector('input[type="email"]');
        const email = emailInput.value;

        if (this.validateEmail(email)) {
            // Simulate form submission
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Подписано!';
            submitBtn.style.background = 'linear-gradient(135deg, #4CAF50, #45a049)';
            
            // Reset form
            emailInput.value = '';
            
            // Show success message
            this.showNotification('Спасибо! Мы уведомим вас о запуске.', 'success');
            
            // Reset button after 3 seconds
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.style.background = '';
            }, 3000);
        } else {
            this.showNotification('Пожалуйста, введите корректный email.', 'error');
        }
    }

    handleContactClick() {
        // Simulate contact action
        this.showNotification('Скоро здесь будет форма обратной связи!', 'info');
        
        // Scroll to contact section
        const contactSection = document.getElementById('contact');
        if (contactSection) {
            contactSection.scrollIntoView({ behavior: 'smooth' });
        }
    }

    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification--${type}`;
        notification.innerHTML = `
            <div class="notification__content">
                <i class="fas fa-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
        `;

        // Add styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${this.getNotificationColor(type)};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            z-index: 3000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 300px;
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);

        // Remove after 5 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 5000);
    }

    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    getNotificationColor(type) {
        const colors = {
            success: 'linear-gradient(135deg, #4CAF50, #45a049)',
            error: 'linear-gradient(135deg, #f44336, #d32f2f)',
            info: 'linear-gradient(135deg, #2196F3, #1976D2)'
        };
        return colors[type] || colors.info;
    }

    handleResize() {
        // Recalculate floating cards positions on resize
        const cards = document.querySelectorAll('.floating-card');
        cards.forEach(card => {
            // Reset animation to recalculate positions
            card.style.animation = 'none';
            setTimeout(() => {
                card.style.animation = '';
            }, 10);
        });
    }

    // Utility method for adding loading states
    addLoadingState(element) {
        element.classList.add('loading');
        element.disabled = true;
    }

    removeLoadingState(element) {
        element.classList.remove('loading');
        element.disabled = false;
    }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new InviteWebApp();
});

// Add some additional interactive features
document.addEventListener('DOMContentLoaded', () => {
    // Check if device is touch-based
    const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    
    // Typewriter effect for hero title (по буквам, поочередно)
    const titleLines = document.querySelectorAll('.title-line');
    let lineIndex = 0;

    function typeLine(line, text, charIndex, callback) {
        if (charIndex === 0) {
            line.style.visibility = 'visible';
            line.classList.add('typewriter-in');
        }
        if (charIndex < text.length) {
            line.textContent += text.charAt(charIndex);
            // Более медленная анимация для всех строк
            const delay = lineIndex === 0 ? 80 : 70;
            setTimeout(() => typeLine(line, text, charIndex + 1, callback), delay);
        } else {
            // Удаляем анимационный класс после завершения анимации
            setTimeout(() => line.classList.remove('typewriter-in'), 500);
            if (callback) {
                // Меньше пауза между строками для плавности
                const pauseDelay = lineIndex === 0 ? 100 : 50;
                setTimeout(callback, pauseDelay);
            }
        }
    }

    function startTypewriter() {
        if (lineIndex < titleLines.length) {
            const line = titleLines[lineIndex];
            const text = line.getAttribute('data-text') || line.textContent;
            line.textContent = '';
            typeLine(line, text, 0, () => {
                lineIndex++;
                startTypewriter();
            });
        }
    }

    // Сохраняем оригинальный текст в data-text
    titleLines.forEach(line => {
        line.setAttribute('data-text', line.textContent);
        line.textContent = '';
        line.style.visibility = 'hidden';
    });

    startTypewriter();

    // Parallax effect for hero section (only on non-touch devices)
    if (!isTouchDevice) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.hero');
            const heroContent = document.querySelector('.hero__content');
            
            if (hero && heroContent) {
                const rate = scrolled * -0.5;
                heroContent.style.transform = `translateY(${rate}px)`;
            }
        });
    }

    // Add hover effect to service cards
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-10px) scale(1.02)';
            card.style.boxShadow = '0 20px 40px rgba(248, 180, 217, 0.3)';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0) scale(1)';
            card.style.boxShadow = '0 10px 30px rgba(248, 180, 217, 0.1)';
        });
    });

    // Add pulse animation to CTA button
    const ctaButton = document.querySelector('.btn--primary');
    if (ctaButton) {
        setInterval(() => {
            ctaButton.style.animation = 'pulse 2s ease-in-out';
            setTimeout(() => {
                ctaButton.style.animation = '';
            }, 2000);
        }, 5000);
    }
}); 