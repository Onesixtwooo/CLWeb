<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ config('app.name', 'Laravel') }} - Brand Guidelines">
    <title>Brand Guidelines - {{ config('app.name', '') }}</title>

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
    <!-- Header -->
    <header class="header">
        <nav class="navbar navbar-expand-md navbar-light bg-white">
            <div class="container">
                <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center gap-2 logo">
                    <div class="logo-box retro-badge">
                        <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl(\App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp')) }}" alt="CLSU Logo" class="logo-image">
                    </div>
                    <div class="logo-text d-flex flex-column">
                        <h2 class="retro-heading mb-0">
                            <span class="logo-full-text d-none d-md-inline">CENTRAL LUZON STATE UNIVERSITY</span>
                            <span class="logo-short-text d-inline d-md-none">CLSU</span>
                        </h2>
                        <p class="retro-subtitle">
                            <span class="d-inline d-md-none">Science City of Muñoz</span>
                            <span class="d-none d-md-inline">Science City of Muñoz, 3120 Nueva Ecija, Philippines</span>
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
                            <a href="{{ url('/') }}#home" class="nav-link">Home</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('about') }}" class="nav-link">About</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/') }}#programs" class="nav-link">Programs</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/') }}#news" class="nav-link">News</a>
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
        <!-- Brand Guidelines -->
        <section id="brand-guidelines" class="py-5" style="background: #f8fbfd;">
            <div class="container">
                <div class="section-header">
                    <span class="section-badge retro-label">Brand Guidelines</span>
                    <h1 class="retro-section-title">Brand Guidelines</h1>
                </div>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <h3 class="h6 fw-bold mb-2">University Seal</h3>
                            <p class="text-muted mb-0">The CLSU seal represents the university's heritage and commitment to excellence.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <h3 class="h6 fw-bold mb-2">Official Colors</h3>
                            <p class="text-muted mb-0">Primary green and accent tones reflect growth, sustainability, and leadership.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <h3 class="h6 fw-bold mb-2">Typography</h3>
                            <p class="text-muted mb-0">Consistent typography strengthens brand recognition across all platforms.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h3>{{ config('app.name', 'Laravel') }}</h3>
                    <p>Empowering students with transformative education that combines academic excellence with real-world impact.</p>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li><a href="{{ route('about') }}">About</a></li>
                        <li><a href="{{ route('history') }}">History</a></li>
                        <li><a href="{{ route('brand-guidelines') }}">Brand Guidelines</a></li>
                        <li><a href="{{ route('campus-life') }}">Campus Life</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS bundle (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
</body>
</html>
