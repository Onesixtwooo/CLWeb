    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        // Loader handling
        (function() {
            const loader = document.getElementById('engineering-loader');
            if (!loader) return;

            function hideLoader() {
                loader.style.opacity = '0';
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 500);
            }

            // Hide after full load (fonts, images, etc.)
            window.addEventListener('load', hideLoader);

            // Handle browser back/forward cache
            window.addEventListener('pageshow', function(e) {
                if (e.persisted) hideLoader();
            });

            // Safety fallback: hide after 5s no matter what
            setTimeout(hideLoader, 5000);
        })();
            
            // Header Scroll Effect
            const headerWrapper = document.querySelector('.engineering-header-wrapper');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    headerWrapper.classList.add('engineering-header-scrolled');
                } else {
                    headerWrapper.classList.remove('engineering-header-scrolled');
                }
            });
            
            // FAQ Toggle
            const faqCards = document.querySelectorAll('.faq-card');
            faqCards.forEach(card => {
                const target = card.querySelector('.faq-card-header') || card;
                target.addEventListener('click', () => {
                   const isOpen = card.classList.contains('is-open');
                   
                   // Close all other cards
                   faqCards.forEach(c => {
                       c.classList.remove('is-open');
                       const icon = c.querySelector('.faq-toggle-icon');
                       if(icon) icon.textContent = '+';
                   });
                   
                   if(!isOpen) {
                       card.classList.add('is-open');
                       const icon = card.querySelector('.faq-toggle-icon');
                       if(icon) icon.textContent = '-';
                   }
                });
            });
            
            // Carousel Auto-play improvements (optional)
            const exploreSlider = document.getElementById('exploreSliderTrack');
            // Add custom slider logic if needed
        });
    </script>
