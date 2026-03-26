<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ config('app.name', 'Laravel') }} - History">
    <title>History - {{ config('app.name', '') }}</title>

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
        <!-- History -->
        <section id="history" class="py-5">
            <div class="container">
                <div class="section-header">
                    <span class="section-badge retro-label">History</span>
                    <h1 class="retro-section-title">History</h1>
                </div>
                <div class="row align-items-center gy-4">
                    <div class="col-lg-7">
                        <p class="text-muted">
                            CLSU has grown into one of the most respected state universities in the Philippines, recognized for
                            its leadership in agriculture, fisheries, and allied fields. Its academic programs and research
                            initiatives continue to shape regional and national development.
                        </p>
                        <p class="text-muted">
                            Through decades of instruction, research, and extension work, the university has expanded its reach
                            and impact while staying grounded in service to the community and the nation.
                        </p>
                        <p class="text-muted">
                            The Central Luzon State University (CLSU), one of the renowned and prestigious state institutions of higher learning in the country,
                            straddles a 658-hectare campus in the Science City of Muñoz, Nueva Ecija, 150 kilometers north of Manila.
                        </p>
                        <p class="text-muted">
                            The lead agency of the Muñoz Science Community and the seat of Central Luzon Agriculture, Aquatic and Resources Research and Development Consortium (CLAARRDEC).
                        </p>
                        <p class="text-muted">
                            The university was designated by the Commission on Higher Education (CHED) – National Agriculture and Fisheries Education System (NAFES) as National University College of Agriculture (NUCA) and National University College of Fisheries (NUCF). Similarly, designated as CHED Center of Excellence (COE) in Agriculture, Agricultural Engineering, Biology, Fisheries, Teacher Education, and Veterinary Medicine - the most number of COEs in Central and Northern Luzon Regions.
                        </p>
                    </div>
                    <div class="col-lg-5">
                        <div class="ratio ratio-4x3 rounded-4 overflow-hidden shadow-sm">
                            <img src="{{ asset('images/178935.jpg') }}" alt="CLSU grounds" class="w-100 h-100 object-fit-cover">
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
