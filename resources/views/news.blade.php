<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ config('app.name', 'Laravel') }} - News Updates">
    <title>News Updates - {{ config('app.name', '') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Buttershine+Serif:wght@400;700&family=Libre+Franklin:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="retro-modern">
    <!-- Header (same as about) -->
    <header class="header">
        <nav class="navbar navbar-expand-md navbar-light bg-white">
            <div class="container">
                <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center gap-2 logo">
                    <div class="logo-box retro-badge">
                        <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl(\App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp')) }}" alt="CLSU Logo" class="logo-image">
                    </div>
                    <div class="logo-text d-flex flex-column">
                        <h2 class="retro-heading mb-0">CENTRAL LUZON STATE UNIVERSITY</h2>
                        <p class="retro-subtitle">Science City of Muñoz, 3120 Nueva Ecija, Philippines</p>
                    </div>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-md-0 align-items-md-center">
                        <li class="nav-item">
                            <a href="{{ url('/') }}#home" class="nav-link">Home</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('about') }}" class="nav-link">About</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/') }}#programs" class="nav-link">Programs</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('news') }}" class="nav-link active" aria-current="page">News</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/') }}#contact" class="nav-link">Contact</a>
                        </li>
                        <li class="nav-item ms-md-3 mt-2 mt-md-0">
                            <div class="d-flex align-items-center gap-2 header-actions">
                                @if (Route::has('login'))
                                    @auth
                                        <a href="{{ url('/dashboard') }}" class="btn btn-success btn-sm">Dashboard</a>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-success btn-sm">Login</a>
                                        @if (Route::has('register'))
                                            <a href="{{ route('register') }}" class="btn btn-outline-success btn-sm">Register</a>
                                        @endif
                                    @endauth
                                @endif
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main style="padding-top: 80px;">
        <section class="news-hero">
            <div class="news-hero-overlay"></div>
            <div class="container">
                <h1 class="news-hero-title">NEWS AND UPDATES</h1>
            </div>
        </section>

        <!-- Latest News Section -->
        <section id="news" class="news news-full">
            <div class="container-fluid px-4 px-lg-5">
                <div class="news-bulletin-layout">
                    <div class="news-section-modern news-list-panel">
                        <div class="news-strip">
                            <div class="news-strip-label">NEWS AND UPDATES</div>
                            <div class="news-strip-bar">
                                <h2 class="news-strip-title">LATEST NEWS</h2>
                                <a href="#" class="read-more-btn-modern">READ MORE</a>
                            </div>
                        </div>
                        <div class="news-list">
                            <article class="announcement-item-modern">
                                <div class="announcement-date-block">
                                    <div class="date-month">JAN</div>
                                    <div class="date-day">22</div>
                                    <div class="date-year">2026</div>
                                </div>
                                <div class="announcement-content">
                                    <h3 class="announcement-title">Innovative Farming Technology Transforms Local Agriculture Practices</h3>
                                    <p class="announcement-snippet">Innovative technology is changing local farming methods. Farmers can now increase efficiency and sustainability with modern tools and data-driven decisions.</p>
                                </div>
                            </article>
                            <article class="announcement-item-modern">
                                <div class="announcement-date-block">
                                    <div class="date-month">JAN</div>
                                    <div class="date-day">19</div>
                                    <div class="date-year">2026</div>
                                </div>
                                <div class="announcement-content">
                                    <h3 class="announcement-title">Cultural Festival Highlights Diversity Through Food and Performances</h3>
                                    <p class="announcement-snippet">A vibrant cultural festival will celebrate diversity with food and performances. Local cultures will be showcased throughout the event.</p>
                                </div>
                            </article>
                            <article class="announcement-item-modern">
                                <div class="announcement-date-block">
                                    <div class="date-month">JAN</div>
                                    <div class="date-day">16</div>
                                    <div class="date-year">2026</div>
                                </div>
                                <div class="announcement-content">
                                    <h3 class="announcement-title">Regional Sports League Welcomes New Teams and Opportunities</h3>
                                    <p class="announcement-snippet">This initiative aims to promote sports participation and community engagement, providing a platform for athletes to showcase their talents.</p>
                                </div>
                            </article>
                        </div>
                    </div>

                    <aside class="announcements-section-modern news-bulletin">
                        <div class="section-header-modern">
                            <h2 class="section-title-modern">BULLETIN</h2>
                            <a href="#" class="read-more-btn-modern">READ MORE</a>
                        </div>
                        <div class="announcements-grid-modern">
                            <article class="announcement-item-modern">
                                <div class="announcement-date-block">
                                    <div class="date-month">JAN</div>
                                    <div class="date-day">20</div>
                                    <div class="date-year">2026</div>
                                </div>
                                <div class="announcement-content">
                                    <h3 class="announcement-title">Enrollment Advisory for Second Semester AY 2025–2026</h3>
                                </div>
                            </article>
                        <article class="announcement-item-modern">
                            <div class="announcement-date-block">
                                <div class="date-month">JAN</div>
                                <div class="date-day">20</div>
                                <div class="date-year">2026</div>
                            </div>
                            <div class="announcement-content">
                                <h3 class="announcement-title">Enrollment Advisory for Second Semester AY 2025–2026</h3>
                                <p class="announcement-snippet">Please review the updated enrollment schedule, requirements, and important reminders for all colleges and programs.</p>
                            </div>
                        </article>

                        <article class="announcement-item-modern">
                            <div class="announcement-date-block">
                                <div class="date-month">JAN</div>
                                <div class="date-day">18</div>
                                <div class="date-year">2026</div>
                            </div>
                            <div class="announcement-content">
                                <h3 class="announcement-title">Scholarship Application Window Now Open</h3>
                            </div>
                        </article>

                        <article class="announcement-item-modern">
                            <div class="announcement-date-block">
                                <div class="date-month">JAN</div>
                                <div class="date-day">15</div>
                                <div class="date-year">2026</div>
                            </div>
                            <div class="announcement-content">
                                <h3 class="announcement-title">Campus Safety &amp; ID Policy Reminder</h3>
                            </div>
                        </article>

                        <article class="announcement-item-modern">
                            <div class="announcement-date-block">
                                <div class="date-month">JAN</div>
                                <div class="date-day">12</div>
                                <div class="date-year">2026</div>
                            </div>
                            <div class="announcement-content">
                                <h3 class="announcement-title">Library Extended Hours (Exam Week)</h3>
                            </div>
                        </article>

                        <article class="announcement-item-modern">
                            <div class="announcement-date-block">
                                <div class="date-month">JAN</div>
                                <div class="date-day">10</div>
                                <div class="date-year">2026</div>
                            </div>
                            <div class="announcement-content">
                                <h3 class="announcement-title">Student Organizations Fair – Call for Booths</h3>
                            </div>
                        </article>

                        <article class="announcement-item-modern">
                            <div class="announcement-date-block">
                                <div class="date-month">JAN</div>
                                <div class="date-day">05</div>
                                <div class="date-year">2026</div>
                            </div>
                            <div class="announcement-content">
                                <h3 class="announcement-title">Campus Facilities Maintenance Schedule</h3>
                            </div>
                        </article>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer id="contact" class="footer footer-rich" style="background-image: linear-gradient(135deg, rgba(18, 18, 18, 0.9), rgba(18, 18, 18, 0.9)), url('{{ asset('images/CLSU.jpg') }}');">
        <div class="container">
            <div class="footer-grid footer-grid-rich">
                <div class="footer-column footer-brand">
                    <div class="footer-logos">
                        <img src="{{ Vite::asset('resources/images/seal/1.png') }}" alt="Freedom of Information Seal">
                        <img src="{{ Vite::asset('resources/images/seal/2.png') }}" alt="Transparency Seal">
                        <img src="{{ Vite::asset('resources/images/seal/3.png') }}" alt="Philippines Seal" class="seal-transparent">
                    </div>
                    <h3>CENTRAL LUZON STATE UNIVERSITY</h3>
                    <p>Science City of Muñoz, Nueva Ecija, Philippines 3120</p>
                    <div class="footer-divider"></div>
                    <ul class="footer-contact">
                        <li>Central Luzon State University, Science City of Muñoz, Nueva Ecija, Philippines</li>
                        <li><a href="mailto:op@clsu.edu.ph">op@clsu.edu.ph</a></li>
                        <li><a href="tel:+63449408785">(044) 940 8785</a></li>
                    </ul>
                    <div class="footer-map">
                        <iframe
                            title="CLSU Map"
                            src="https://www.google.com/maps?q=Central%20Luzon%20State%20University%20Mu%C3%B1oz%20Nueva%20Ecija&output=embed"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <div class="footer-social">
                        <a href="#" title="Facebook">f</a>
                        <a href="#" title="Twitter">𝕏</a>
                        <a href="#" title="Instagram">📷</a>
                        <a href="#" title="LinkedIn">in</a>
                        <a href="#" title="YouTube">▶</a>
                    </div>
                    <p class="footer-copy">&copy; Copyright {{ date('Y') }} Central Luzon State University All Rights Reserved</p>
                </div>

                <div class="footer-column">
                    <h3 class="footer-heading">OPPORTUNITIES</h3>
                    <ul class="footer-list">
                        <li><a href="#">Career</a></li>
                        <li><a href="#">Bid Opportunities</a></li>
                        <li><a href="#">Procurements</a></li>
                    </ul>

                    <h3 class="footer-heading mt-lg">E-SERVICES</h3>
                    <ul class="footer-list">
                        <li><a href="#">Downloads</a></li>
                        <li><a href="#">Publications</a></li>
                        <li><a href="#">Knowledge Sharing &amp; Learning Resources</a></li>
                        <li><a href="#">AgriATM</a></li>
                        <li><a href="#">Kamalig Booking System</a></li>
                    </ul>
                </div>

                <div class="footer-column footer-feedback">
                    <h3 class="footer-heading">FEEDBACK AND GRIEVANCE DESK</h3>
                    <p>
                        Central Luzon State University values the voices of its students, faculty, staff, and the people it serves
                        and is committed to continuously improve its services. As part of our commitment to quality and excellence,
                        we encourage you to share your feedback, concerns, and suggestions.
                    </p>
                    <p>
                        To ensure your inputs are heard and addressed, we provide the following official channels for receiving
                        feedback and grievances.
                    </p>
                    <ul class="footer-contact">
                        <li><a href="mailto:feedback@clsu.edu.ph">feedback@clsu.edu.ph</a></li>
                        <li><a href="tel:+639537267511">+63 9537 267 511</a></li>
                        <li><a href="tel:+63449407030">(044) 940 7030</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS bundle (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
</body>
</html>
