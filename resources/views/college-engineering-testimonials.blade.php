<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Testimonials - College of Engineering, {{ config('app.name', 'CLSU') }}">
    <title>Testimonials - College of Engineering - {{ config('app.name', 'CLSU') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Buttershine+Serif:wght@400;700&family=Libre+Franklin:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/college.css', 'resources/js/app.js'])
</head>
<body class="retro-modern college-page">
    <!-- Loader screen: College of Engineering -->
    <div id="engineering-loader" class="engineering-loader" aria-hidden="false" aria-label="Loading College of Engineering">
        <div class="engineering-loader-inner">
            <img src="{{ asset('images/logos/engineering.jpg') }}" alt="College of Engineering" class="engineering-loader-logo">
            <p class="engineering-loader-title">College of Engineering</p>
            <p class="engineering-loader-subtitle">Central Luzon State University</p>
            <div class="engineering-loader-spinner"></div>
        </div>
    </div>

    <!-- Fixed header wrapper: top bar + main nav so both stay visible -->
    <div class="engineering-header-wrapper">
        <!-- Top header bar (main contact bar above main header) -->
        <div class="engineering-top-header">
            <div class="container">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 engineering-top-header-inner">
                    <div class="d-flex flex-wrap align-items-center gap-3 gap-md-4">
                        <a href="{{ url('/') }}" class="engineering-top-header-clsu d-flex align-items-center flex-shrink-0" aria-label="Back to CLSU main">
                            @php
                                $globalLogoPath = \App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp');
                                $globalLogoUrl = \App\Providers\AppServiceProvider::resolveLogoUrl($globalLogoPath);
                            @endphp
                            <img src="{{ $globalLogoUrl }}" alt="Central Luzon State University" class="engineering-top-header-clsu-img">
                        </a>
                        <a href="https://www.google.com/maps?q=Central+Luzon+State+University+Mu%C3%B1oz+Nueva+Ecija" target="_blank" rel="noopener noreferrer" class="engineering-top-header-link d-flex align-items-center gap-1" aria-label="Location">
                            <span class="engineering-top-header-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            </span>
                            <span class="d-none d-md-inline">Science City of Muñoz</span>
                        </a>
                        <a href="mailto:op@clsu.edu.ph" class="engineering-top-header-link d-flex align-items-center gap-1">
                            <span class="engineering-top-header-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </span>
                            op@clsu.edu.ph
                        </a>
                        <a href="tel:+63449408785" class="engineering-top-header-link d-flex align-items-center gap-1">
                            <span class="engineering-top-header-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            </span>
                            (044) 940 8785
                        </a>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="#" class="engineering-top-header-social" title="Facebook" aria-label="Facebook">f</a>
                        <a href="#" class="engineering-top-header-social" title="Twitter" aria-label="Twitter">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="#" class="engineering-top-header-social" title="Instagram" aria-label="Instagram">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        </a>
                        <a href="#" class="engineering-top-header-social" title="LinkedIn" aria-label="LinkedIn">in</a>
                        <a href="#" class="engineering-top-header-social" title="YouTube" aria-label="YouTube">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main header (hides on scroll; top bar above remains) -->
        <div class="engineering-nav-outer">
        <header class="header engineering-header">
        <nav class="navbar navbar-expand-md navbar-dark engineering-navbar">
            <div class="container">
                <a href="{{ route('college.engineering') }}" class="navbar-brand d-flex align-items-center gap-2 logo">
                    <div class="logo-box retro-badge">
                        <img src="{{ asset('images/logos/engineering.jpg') }}" alt="College of Engineering logo" class="logo-image">
                    </div>
                    <div class="logo-text d-flex flex-column">
                        <h2 class="retro-heading mb-0">
                            <span class="logo-full-text d-none d-md-inline">COLLLEGE OF ENGINEERING</span>
                            <span class="logo-short-text d-inline d-md-none">CEn</span>
                        </h2>
                        <p class="retro-subtitle">
                            <span class="d-inline d-md-none">CLSU</span>
                            <span class="d-none d-md-inline">College of Engineering, Central Luzon State University</span>
                        </p>
                    </div>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-md-0 align-items-md-center">
                        <li class="nav-item">
                            <a href="{{ route('college.engineering') }}" class="nav-link">Home</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="departmentsDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                Departments
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="departmentsDropdown">
                                <li><a href="{{ route('college.engineering') }}#departments" class="dropdown-item">Agricultural &amp; Biosystems Engineering</a></li>
                                <li><a href="{{ route('college.engineering') }}#departments" class="dropdown-item">Civil Engineering</a></li>
                                <li><a href="{{ route('college.engineering') }}#departments" class="dropdown-item">Engineering Sciences</a></li>
                                <li><a href="{{ route('department.it') }}" class="dropdown-item">Information Technology</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="centersDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                Center & Institutes
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="centersDropdown">
                                <li><a href="{{ route('college.engineering') }}#center-institutes" class="dropdown-item">ISI</a></li>
                                <li><a href="{{ route('college.engineering') }}#center-institutes" class="dropdown-item">PreDIC</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('college.engineering') }}#scholarships" class="nav-link">Scholarships</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('news.announcement.board', 'engineering') }}" class="nav-link">News &amp; Announcements</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="aboutEngineeringDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                About Us
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="aboutEngineeringDropdown">
                                <li><a href="{{ route('college.engineering') }}#aboutus" class="dropdown-item">Dean's Office</a></li>
                                <li><a href="{{ route('college.engineering') }}#aboutus" class="dropdown-item">Organizational Structure</a></li>
                                <li><a href="{{ route('college.engineering') }}#aboutus" class="dropdown-item">Manual of Operations</a></li>
                                <li><a href="{{ route('college.engineering') }}#aboutus" class="dropdown-item">Contact Us</a></li>
                                <li><a href="{{ route('college.downloads', 'engineering') }}" class="dropdown-item">Downloads</a></li>
                                <li><a href="{{ route('college.engineering.faculty') }}" class="dropdown-item">Faculty</a></li>
                                <li><a href="{{ route('college.engineering.testimonials') }}" class="dropdown-item">Testimonials</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        </header>
        </div>
    </div>

    <main>
        <!-- Page title -->
        <section class="py-4" style="background: linear-gradient(135deg, #86090a 0%, #b31214 100%); margin-top: 118px;">
            <div class="container">
                <h1 class="retro-section-title text-white mb-0">Testimonials</h1>
                <p class="text-white-50 mb-0 mt-2">What our community says</p>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section id="testimonials" class="testimonials-section py-5">
            <div class="container position-relative">
                <div class="testimonials-header text-center mb-4">
                    <h2 class="retro-section-title mt-3 text-white">What Our Community Says</h2>
                    <p class="testimonials-subtitle text-white-50">Stories from students, alumni, and partners of the College of Engineering.</p>
                </div>

                <div class="testimonial-layout position-relative">
                    <span class="testimonial-quote-mark testimonial-quote-open" aria-hidden="true">"</span>
                    <span class="testimonial-quote-mark testimonial-quote-close" aria-hidden="true">"</span>

                    <div class="testimonial-slider">
                        <div class="testimonial-slide active">
                            <div class="row align-items-stretch g-3 testimonial-row">
                                <div class="col-lg-5">
                                    <div class="testimonial-image-card rounded-3 overflow-hidden">
                                        <img src="{{ asset('images/facilities/res.jpg') }}" alt="College of Engineering" class="w-100 h-100 object-fit-cover">
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="testimonial-quote-card rounded-3">
                                        <blockquote class="testimonial-quote">"Success, to me, is more than professional achievements—it's about character, knowledge, skills, and values. The college and university played a significant role in shaping who I am today, influencing how I work with colleagues, face challenges, and approach projects. I owe a big part of my personal and professional growth to the college. It's not just about becoming a successful..."</blockquote>
                                        <div class="testimonial-attribution">
                                            <p class="testimonial-name mb-1">Engr. Lorem Ipsum</p>
                                            <p class="testimonial-role mb-1">Electrical Engineer</p>
                                            <p class="testimonial-degree mb-0">Bachelor of Science in Electrical Engineering</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-slide">
                            <div class="row align-items-stretch g-3 testimonial-row">
                                <div class="col-lg-5">
                                    <div class="testimonial-image-card rounded-3 overflow-hidden">
                                        <img src="{{ asset('images/facilities/agrimuseum.jpg') }}" alt="College of Engineering" class="w-100 h-100 object-fit-cover">
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="testimonial-quote-card rounded-3">
                                        <blockquote class="testimonial-quote">"The hands-on projects and supportive mentors prepared me for industry challenges. The college gave me the foundation to excel as an engineer."</blockquote>
                                        <div class="testimonial-attribution">
                                            <p class="testimonial-name mb-1">Engr. Lorem Ipsum</p>
                                            <p class="testimonial-role mb-0">Alumnus</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-slide">
                            <div class="row align-items-stretch g-3 testimonial-row">
                                <div class="col-lg-5">
                                    <div class="testimonial-image-card rounded-3 overflow-hidden">
                                        <img src="{{ asset('images/Library-clean.webp') }}" alt="College of Engineering" class="w-100 h-100 object-fit-cover">
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="testimonial-quote-card rounded-3">
                                        <blockquote class="testimonial-quote">"The college's research culture encourages innovation and collaboration. I'm proud to be part of a community that values both theory and practice."</blockquote>
                                        <div class="testimonial-attribution">
                                            <p class="testimonial-name mb-1">Dr. Lorem Ipsum</p>
                                            <p class="testimonial-role mb-0">Faculty Research Adviser</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-dots text-center my-4">
                    <button type="button" class="testimonial-dot active" aria-label="Go to testimonial 1" data-index="0"></button>
                    <button type="button" class="testimonial-dot" aria-label="Go to testimonial 2" data-index="1"></button>
                    <button type="button" class="testimonial-dot" aria-label="Go to testimonial 3" data-index="2"></button>
                </div>
                <div class="text-center">
                    <a href="{{ route('college.engineering') }}#programs" class="btn testimonial-cta-btn rounded-pill px-4 py-3">BE ONE OF US!</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('includes.college-footer')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var testimonialSlides = document.querySelectorAll('.testimonial-slide');
            var testimonialDots = document.querySelectorAll('.testimonial-dot');
            if (testimonialSlides.length && testimonialDots.length) {
                testimonialDots.forEach(function (dot, i) {
                    dot.addEventListener('click', function () {
                        testimonialSlides.forEach(function (s) { s.classList.remove('active'); });
                        testimonialDots.forEach(function (d) { d.classList.remove('active'); });
                        testimonialSlides[i].classList.add('active');
                        dot.classList.add('active');
                    });
                });
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
    <script>
        (function () {
            var loader = document.getElementById('engineering-loader');
            if (!loader) return;
            function hideLoader() {
                loader.classList.add('engineering-loader-hidden');
                loader.setAttribute('aria-hidden', 'true');
                setTimeout(function () {
                    loader.style.display = 'none';
                }, 500);
            }
            if (document.readyState === 'complete') {
                setTimeout(hideLoader, 400);
            } else {
                window.addEventListener('load', function () {
                    setTimeout(hideLoader, 400);
                });
            }
        })();
    </script>
    <script>
        (function () {
            var wrapper = document.querySelector('.engineering-header-wrapper');
            var body = document.body;
            if (!wrapper) return;
            var lastScrollY = window.scrollY || 0;
            var scrollThreshold = 80;
            function onScroll() {
                var scrollY = window.scrollY || 0;
                if (scrollY > scrollThreshold) {
                    if (scrollY > lastScrollY) {
                        wrapper.classList.add('engineering-header-scrolled');
                        body.classList.add('engineering-nav-hidden');
                    } else {
                        wrapper.classList.remove('engineering-header-scrolled');
                        body.classList.remove('engineering-nav-hidden');
                    }
                } else {
                    wrapper.classList.remove('engineering-header-scrolled');
                    body.classList.remove('engineering-nav-hidden');
                }
                lastScrollY = scrollY;
            }
            window.addEventListener('scroll', onScroll, { passive: true });
        })();
    </script>
</body>
</html>
