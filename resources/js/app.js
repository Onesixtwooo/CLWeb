import './bootstrap';

// Loading Screen
window.addEventListener('load', function () {
    const loadingScreen = document.getElementById('loadingScreen');
    if (loadingScreen) {
        // Add a small delay for smooth transition
        setTimeout(function () {
            loadingScreen.classList.add('hidden');
            // Remove from DOM after animation completes
            setTimeout(function () {
                loadingScreen.style.display = 'none';
            }, 500);
        }, 500);
    }
});

// Smooth Scrolling for Navigation Links (only for same-page hash links)
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        // Only handle pure hash links on the same page
        // Skip dropdown toggles, empty hashes, and dropdown items
        if (href !== '#' &&
            !this.classList.contains('dropdown-toggle') &&
            !this.classList.contains('dropdown-item')) {
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

                // Close any open Bootstrap navbar collapse on mobile
                const navbarCollapse = document.querySelector('.navbar-collapse.show');
                if (navbarCollapse) {
                    const collapseInstance = bootstrap.Collapse.getInstance(navbarCollapse);
                    collapseInstance?.hide();
                }
            }
        }
    });
});

// About Us dropdown: toggle on icon click (no hover)
const aboutDropdownIcon = document.querySelector('.nav-item.dropdown .dropdown-toggle-icon');
const aboutDropdownMenu = document.querySelector('.nav-item.dropdown .dropdown-menu');

if (aboutDropdownIcon && aboutDropdownMenu) {
    aboutDropdownIcon.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        aboutDropdownMenu.classList.toggle('show');
    });

    document.addEventListener('click', function (e) {
        if (!aboutDropdownMenu.contains(e.target) && !aboutDropdownIcon.contains(e.target)) {
            aboutDropdownMenu.classList.remove('show');
        }
    });
}

// Add scroll event listener for header effects
let lastScroll = 0;
const header = document.querySelector('.header');

if (header) {
    window.addEventListener('scroll', function () {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 100) {
            header.style.boxShadow = 'var(--shadow-sm)';
        } else {
            header.style.boxShadow = 'none';
        }

        lastScroll = currentScroll;
    });
}

// Intersection Observer for fade-in animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function (entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe all cards and sections
document.querySelectorAll('.program-card, .testimonial-card, .news-card, .news-card-modern, .facility-card').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
});

// Button click handlers
document.querySelectorAll('.btn-primary, .btn-secondary').forEach(btn => {
    btn.addEventListener('click', function (e) {
        // Add ripple effect
        const ripple = document.createElement('span');
        ripple.style.position = 'absolute';
        ripple.style.borderRadius = '50%';
        ripple.style.background = 'rgba(255, 255, 255, 0.5)';
        ripple.style.width = ripple.style.height = '20px';
        ripple.style.animation = 'ripple 0.6s ease-out';

        const rect = this.getBoundingClientRect();
        ripple.style.left = (e.clientX - rect.left - 10) + 'px';
        ripple.style.top = (e.clientY - rect.top - 10) + 'px';

        if (this.style.position !== 'absolute' && this.style.position !== 'fixed') {
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
        }

        this.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);
    });
});

// Add ripple animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);

// Add active state to navigation links based on scroll position
window.addEventListener('scroll', function () {
    const sections = document.querySelectorAll('section[id]');
    let currentSection = '';

    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;

        if (window.pageYOffset >= sectionTop - 200) {
            currentSection = section.getAttribute('id');
        }
    });

    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === '#' + currentSection) {
            link.classList.add('active');
        }
    });
});

// Lazy load images
const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            if (img.dataset.src) {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
            }
            observer.unobserve(img);
        }
    });
});

document.querySelectorAll('img[data-src]').forEach(img => {
    imageObserver.observe(img);
});

// Hero Slider
let heroCurrentSlide = 0;
const heroSlides = document.querySelectorAll('.hero-slide');
const heroTotalSlides = heroSlides.length;
let heroSlideInterval;

function showHeroSlide(index) {
    heroSlides.forEach(slide => slide.classList.remove('active'));
    if (heroSlides[index]) {
        heroSlides[index].classList.add('active');
    }
    heroCurrentSlide = index;
}

function nextHeroSlide() {
    const next = (heroCurrentSlide + 1) % heroTotalSlides;
    showHeroSlide(next);
}

function prevHeroSlide() {
    const prev = (heroCurrentSlide - 1 + heroTotalSlides) % heroTotalSlides;
    showHeroSlide(prev);
}

function startHeroSlider() {
    heroSlideInterval = setInterval(nextHeroSlide, 6000); // 6 seconds
}

function stopHeroSlider() {
    clearInterval(heroSlideInterval);
}

// Initialize hero slider
if (heroSlides.length > 0) {
    showHeroSlide(0);
    startHeroSlider();

    const heroPrevBtn = document.getElementById('heroPrev');
    const heroNextBtn = document.getElementById('heroNext');

    if (heroPrevBtn) {
        heroPrevBtn.addEventListener('click', () => {
            stopHeroSlider();
            prevHeroSlide();
            startHeroSlider();
        });
    }

    if (heroNextBtn) {
        heroNextBtn.addEventListener('click', () => {
            stopHeroSlider();
            nextHeroSlide();
            startHeroSlider();
        });
    }

    // Pause on hover
    const heroSliderContainer = document.querySelector('.hero-slider-container');
    if (heroSliderContainer) {
        heroSliderContainer.addEventListener('mouseenter', stopHeroSlider);
        heroSliderContainer.addEventListener('mouseleave', startHeroSlider);
    }
}

// About School Slider
let currentSlide = 0;
const slides = document.querySelectorAll('.about-slide');
const dots = document.querySelectorAll('.dot');
const totalSlides = slides.length;
let slideInterval;

function showSlide(index) {
    // Remove active class from all slides and dots
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));

    // Add active class to current slide and dot
    if (slides[index]) {
        slides[index].classList.add('active');
    }
    if (dots[index]) {
        dots[index].classList.add('active');
    }

    currentSlide = index;
}

function nextSlide() {
    const next = (currentSlide + 1) % totalSlides;
    showSlide(next);
}

function prevSlide() {
    const prev = (currentSlide - 1 + totalSlides) % totalSlides;
    showSlide(prev);
}

function startSlider() {
    slideInterval = setInterval(nextSlide, 6000); // 6 seconds
}

function stopSlider() {
    clearInterval(slideInterval);
}

// Initialize slider
if (slides.length > 0) {
    showSlide(0);
    startSlider();

    // Navigation arrows
    const prevBtn = document.getElementById('prevSlide');
    const nextBtn = document.getElementById('nextSlide');

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            stopSlider();
            prevSlide();
            startSlider();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            stopSlider();
            nextSlide();
            startSlider();
        });
    }

    // Dot navigation
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            stopSlider();
            showSlide(index);
            startSlider();
        });
    });

    // Pause on hover
    const sliderContainer = document.querySelector('.about-slider-container');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', stopSlider);
        sliderContainer.addEventListener('mouseleave', startSlider);
    }
}

// Rotating image for Generated Technologies slide
const rotatingImage = document.querySelector('[data-rotate-images]');
if (rotatingImage) {
    const images = rotatingImage.dataset.rotateImages
        .split(',')
        .map(src => src.trim())
        .filter(Boolean);
    const intervalMs = Number(rotatingImage.dataset.rotateInterval) || 3500;
    let imageIndex = 0;

    if (images.length > 1) {
        setInterval(() => {
            imageIndex = (imageIndex + 1) % images.length;
            rotatingImage.src = images[imageIndex];
        }, intervalMs);
    }
}

// Programs Offered slider - click navigation only
const programsTrack = document.getElementById('programsSliderTrack');
const programsPrev = document.getElementById('programsPrev');
const programsNext = document.getElementById('programsNext');
const programsDots = document.getElementById('programsDots');

if (programsTrack) {
    const cards = Array.from(programsTrack.children);

    // Get the scroll position of a card relative to the track
    const getCardScrollPosition = (cardIndex) => {
        if (cardIndex < 0 || cardIndex >= cards.length) return 0;
        const card = cards[cardIndex];
        return card.offsetLeft - programsTrack.offsetLeft;
    };

    // Calculate how many cards are visible
    const getVisibleCount = () => {
        if (cards.length === 0) return 1;
        const firstCard = cards[0];
        const cardWidth = firstCard.offsetWidth;
        const gap = 24; // 1.5rem gap from CSS
        const cardWithGap = cardWidth + gap;
        return Math.max(1, Math.floor(programsTrack.clientWidth / cardWithGap));
    };

    const getPageCount = () => {
        const visible = getVisibleCount();
        return Math.max(1, Math.ceil(cards.length / visible));
    };

    const buildDots = () => {
        if (!programsDots) return;
        programsDots.innerHTML = '';
        const pageCount = getPageCount();
        for (let i = 0; i < pageCount; i += 1) {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.setAttribute('aria-label', `Go to page ${i + 1}`);
            dot.addEventListener('click', () => {
                const visible = getVisibleCount();
                const targetCardIndex = Math.min(i * visible, cards.length - 1);
                const scrollPosition = getCardScrollPosition(targetCardIndex);
                programsTrack.scrollTo({ left: scrollPosition, behavior: 'smooth' });
            });
            programsDots.appendChild(dot);
        }
    };

    const updateDots = () => {
        if (!programsDots) return;
        const dots = Array.from(programsDots.children);
        if (dots.length === 0) return;

        const visible = getVisibleCount();
        const scrollLeft = programsTrack.scrollLeft;
        let currentPage = 0;

        // Find which page we're on based on scroll position
        for (let i = 0; i < cards.length; i += visible) {
            const cardPosition = getCardScrollPosition(i);
            if (scrollLeft >= cardPosition - 50) { // 50px threshold for page detection
                currentPage = Math.floor(i / visible);
            }
        }

        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentPage);
        });
    };

    const scrollByPage = (direction) => {
        const visible = getVisibleCount();
        const currentScroll = programsTrack.scrollLeft;

        // Find the current card index based on scroll position
        let currentCardIndex = 0;
        for (let i = 0; i < cards.length; i++) {
            const cardPosition = getCardScrollPosition(i);
            if (cardPosition <= currentScroll + 50) {
                currentCardIndex = i;
            } else {
                break;
            }
        }

        // Calculate target card index
        const targetCardIndex = Math.max(0, Math.min(
            currentCardIndex + (direction * visible),
            cards.length - 1
        ));

        const targetPosition = getCardScrollPosition(targetCardIndex);

        programsTrack.scrollTo({
            left: targetPosition,
            behavior: 'smooth'
        });
    };

    // Update dots on scroll
    programsTrack.addEventListener('scroll', () => {
        window.requestAnimationFrame(updateDots);
    });

    // Rebuild dots on resize
    window.addEventListener('resize', () => {
        buildDots();
        updateDots();
    });

    // Navigation buttons
    programsPrev?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        scrollByPage(-1);
    });

    programsNext?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        scrollByPage(1);
    });

    // Initialize
    buildDots();
    updateDots();
}

// News Slider - click navigation only
const newsTrack = document.getElementById('newsSliderTrack');
const newsPrev = document.getElementById('newsPrev');
const newsNext = document.getElementById('newsNext');

if (newsTrack) {
    const cards = Array.from(newsTrack.children);

    // Get the scroll position of a card relative to the track
    const getCardScrollPosition = (cardIndex) => {
        if (cardIndex < 0 || cardIndex >= cards.length) return 0;
        const card = cards[cardIndex];
        return card.offsetLeft - newsTrack.offsetLeft;
    };

    // Calculate how many cards are visible
    const getVisibleCount = () => {
        if (cards.length === 0) return 1;
        const firstCard = cards[0];
        const cardWidth = firstCard.offsetWidth;
        const gap = 24; // 1.5rem gap from CSS
        const cardWithGap = cardWidth + gap;
        return Math.max(1, Math.floor(newsTrack.clientWidth / cardWithGap));
    };

    const scrollByPage = (direction) => {
        const visible = getVisibleCount();
        const currentScroll = newsTrack.scrollLeft;

        // Find the current card index based on scroll position
        let currentCardIndex = 0;
        for (let i = 0; i < cards.length; i++) {
            const cardPosition = getCardScrollPosition(i);
            if (cardPosition <= currentScroll + 50) {
                currentCardIndex = i;
            } else {
                break;
            }
        }

        // Calculate target card index
        const targetCardIndex = Math.max(0, Math.min(
            currentCardIndex + (direction * visible),
            cards.length - 1
        ));

        const targetPosition = getCardScrollPosition(targetCardIndex);

        newsTrack.scrollTo({
            left: targetPosition,
            behavior: 'smooth'
        });
    };

    // Navigation buttons
    newsPrev?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        scrollByPage(-1);
    });

    newsNext?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        scrollByPage(1);
    });
}

// GIF Slider with transitions
const gifSliderTrack = document.getElementById('gifSliderTrack');
const gifSlides = document.querySelectorAll('.gif-slide');
const gifPrev = document.getElementById('gifPrev');
const gifNext = document.getElementById('gifNext');
const gifDots = document.getElementById('gifDots');

if (gifSliderTrack && gifSlides.length > 0) {
    let currentGifSlide = 0;
    let gifSlideInterval;

    // Build dots
    const buildGifDots = () => {
        if (!gifDots) return;
        gifDots.innerHTML = '';
        gifSlides.forEach((_, index) => {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.setAttribute('aria-label', `Go to slide ${index + 1}`);
            dot.addEventListener('click', () => {
                showGifSlide(index);
            });
            gifDots.appendChild(dot);
        });
    };

    // Show specific slide
    const showGifSlide = (index) => {
        gifSlides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
        });

        // Update dots
        const dots = Array.from(gifDots.children);
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });

        currentGifSlide = index;
    };

    // Next slide
    const nextGifSlide = () => {
        const next = (currentGifSlide + 1) % gifSlides.length;
        showGifSlide(next);
    };

    // Previous slide
    const prevGifSlide = () => {
        const prev = (currentGifSlide - 1 + gifSlides.length) % gifSlides.length;
        showGifSlide(prev);
    };

    // Start auto-play
    const startGifAutoPlay = () => {
        gifSlideInterval = setInterval(nextGifSlide, 5000); // 5 seconds
    };

    // Stop auto-play
    const stopGifAutoPlay = () => {
        clearInterval(gifSlideInterval);
    };

    // Initialize
    buildGifDots();
    showGifSlide(0);
    startGifAutoPlay();

    // Navigation buttons
    gifPrev?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        stopGifAutoPlay();
        prevGifSlide();
        startGifAutoPlay();
    });

    gifNext?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        stopGifAutoPlay();
        nextGifSlide();
        startGifAutoPlay();
    });

    // Pause on hover
    gifSliderTrack?.addEventListener('mouseenter', stopGifAutoPlay);
    gifSliderTrack?.addEventListener('mouseleave', startGifAutoPlay);
}

// Announcements are now in a static card grid (no slider)

// Explore slider (College of Engineering)
const exploreSliderTrack = document.getElementById('exploreSliderTrack');
const explorePrev = document.getElementById('explorePrev');
const exploreNext = document.getElementById('exploreNext');

if (exploreSliderTrack) {
    // Clone cards for seamless infinite loop
    const cards = exploreSliderTrack.querySelectorAll('.program-card');
    if (cards.length > 0) {
        cards.forEach(card => {
            const clone = card.cloneNode(true);
            exploreSliderTrack.appendChild(clone);
        });
    }

    const scrollExploreBy = (direction) => {
        const card = exploreSliderTrack.querySelector('.program-card');
        const cardWidth = card ? card.offsetWidth : 320;
        const gap = parseFloat(getComputedStyle(exploreSliderTrack).columnGap || '0') || 0;
        exploreSliderTrack.scrollBy({
            left: direction * (cardWidth + gap),
            behavior: 'smooth'
        });
    };

    let exploreAutoScrollId;
    const startExploreAutoScroll = () => {
        stopExploreAutoScroll();
        exploreSliderTrack.classList.add('is-auto-scrolling');
        const originalCardCount = cards.length;

        exploreAutoScrollId = setInterval(() => {
            exploreSliderTrack.scrollLeft += 1;

            // Calculate reset point based on card width for precision
            const card = exploreSliderTrack.querySelector('.program-card');
            const cardWidth = card ? card.offsetWidth : 320;
            const gap = parseFloat(getComputedStyle(exploreSliderTrack).columnGap || '0') || 0;
            const resetPoint = (cardWidth + gap) * originalCardCount;

            if (exploreSliderTrack.scrollLeft >= resetPoint) {
                exploreSliderTrack.scrollLeft -= resetPoint;
            }
        }, 20);
    };

    const stopExploreAutoScroll = () => {
        if (exploreAutoScrollId) {
            clearInterval(exploreAutoScrollId);
        }
        exploreSliderTrack.classList.remove('is-auto-scrolling');
    };

    explorePrev?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        stopExploreAutoScroll();
        scrollExploreBy(-1);
    });

    exploreNext?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        stopExploreAutoScroll();
        scrollExploreBy(1);
    });

    exploreSliderTrack.addEventListener('mouseenter', stopExploreAutoScroll);
    exploreSliderTrack.addEventListener('mouseleave', startExploreAutoScroll);
    startExploreAutoScroll();
}

// Programs slider for "Get to Know" section
const programsKnowSliderTrack = document.getElementById('programsKnowSliderTrack');
const programsKnowPrev = document.getElementById('programsKnowPrev');
const programsKnowNext = document.getElementById('programsKnowNext');

if (programsKnowSliderTrack) {
    programsKnowSliderTrack.classList.remove('is-auto-scrolling');
    programsKnowSliderTrack.scrollLeft = 0;

    const scrollProgramsKnowBy = (direction) => {
        const card = programsKnowSliderTrack.querySelector('.program-card');
        const cardWidth = card ? card.offsetWidth : 320;
        const gap = parseFloat(getComputedStyle(programsKnowSliderTrack).columnGap || '0') || 0;

        programsKnowSliderTrack.scrollBy({
            left: direction * (cardWidth + gap),
            behavior: 'auto'
        });
    };

    programsKnowPrev?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        scrollProgramsKnowBy(-1);
    });

    programsKnowNext?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        scrollProgramsKnowBy(1);
    });
}


// Testimonials slider (College of Engineering)
const testimonialsTrack = document.getElementById('testimonialsTrack');
const testimonialsPrev = document.getElementById('testimonialsPrev');
const testimonialsNext = document.getElementById('testimonialsNext');

if (testimonialsTrack) {
    const scrollTestimonialsBy = (direction) => {
        const card = testimonialsTrack.querySelector('.testimonial-card');
        const cardWidth = card ? card.offsetWidth : 300;
        const gap = parseFloat(getComputedStyle(testimonialsTrack).columnGap || '0') || 0;
        testimonialsTrack.scrollBy({
            left: direction * (cardWidth + gap),
            behavior: 'smooth'
        });
    };

    testimonialsPrev?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        scrollTestimonialsBy(-1);
    });

    testimonialsNext?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        scrollTestimonialsBy(1);
    });
}

// College of Engineering navbar: dropdowns are click-only (Bootstrap data-bs-toggle).
// No hover open/close — removed so menu stays open when moving to items.
