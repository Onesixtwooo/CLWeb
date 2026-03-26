<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ config('app.name', 'CLSU') }} - Programs Offered - Colleges of Central Luzon State University">
    <title>Programs Offered - {{ config('app.name', 'CLSU') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Buttershine+Serif:wght@400;700&family=Libre+Franklin:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .page-hero {
            background: linear-gradient(135deg, #43a047 0%, #009639 50%, #006b28 100%);
            color: #fff;
            padding: 3rem 0 2.5rem;
            text-align: center;
            margin-top: 80px; /* clear fixed header */
        }
        .page-hero h1 { font-size: clamp(1.75rem, 4vw, 2.25rem); font-weight: 800; margin-bottom: 0.5rem; }
        .page-hero p { opacity: 0.95; margin: 0; }
        .programs-offered-card { transition: box-shadow 0.2s ease; overflow: hidden; height: 100%; display: flex; flex-direction: column; }
        .programs-offered-card .card-body { flex: 1; display: flex; flex-direction: column; }
        .programs-offered-card .card-body > p:last-child { margin-top: auto !important; }
        .programs-offered-card:hover { box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.12) !important; }
        .programs-offered-contact ul li + li { margin-top: 0.25rem; }
        .college-card-img-wrap {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            background: #e5e7eb;
            overflow: hidden;
        }
        .college-card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .college-card-img-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #d1d5db 0%, #e5e7eb 100%);
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 600;
            text-align: center;
            padding: 1rem;
        }
        .college-card-img-placeholder span.icon { font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.7; }
        .college-card-img-placeholder small { font-weight: 400; font-size: 0.75rem; margin-top: 0.25rem; color: #9ca3af; }
    </style>
</head>
<body class="retro-modern">
    <!-- Header -->
    <header class="header">
        <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom">
            <div class="container">
                <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center gap-2 logo">
                    <div class="logo-box retro-badge">
                        @php
                            $globalLogoPath = \App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp');
                            $globalLogoUrl = \App\Providers\AppServiceProvider::resolveLogoUrl($globalLogoPath);
                        @endphp
                        <img src="{{ $globalLogoUrl }}" alt="CLSU Logo" class="logo-image">
                    </div>
                    <div class="logo-text d-flex flex-column">
                        <h2 class="retro-heading mb-0">
                            <span class="logo-full-text d-none d-md-inline">CENTRAL LUZON STATE UNIVERSITY</span>
                            <span class="logo-short-text d-inline d-md-none">CLSU</span>
                        </h2>
                        <p class="retro-subtitle mb-0">
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
                            <a href="{{ url('/') }}" class="nav-link">Home</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="{{ url('/about') }}" class="nav-link dropdown-toggle" id="aboutDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">About Us</a>
                            <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
                                <li><a href="{{ route('history') }}" class="dropdown-item">History</a></li>
                                <li><a href="{{ route('brand-guidelines') }}" class="dropdown-item">Brand Guidelines</a></li>
                                <li><a href="{{ route('campus-life') }}" class="dropdown-item">Campus Life</a></li>
                                <li><a href="{{ route('offices') }}" class="dropdown-item">Offices</a></li>
                                <li><a href="{{ route('university-officials') }}" class="dropdown-item">University Officials</a></li>
                                <li><a href="{{ route('organizational-structure') }}" class="dropdown-item">Organizational Structure</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#contact" class="nav-link">Contact</a>
                        </li>
                        @if (Route::has('login'))
                            <li class="nav-item ms-md-3 mt-2 mt-md-0">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="btn btn-success btn-sm">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-success btn-sm">Login</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="btn btn-outline-success btn-sm">Register</a>
                                    @endif
                                @endauth
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <!-- Hero -->
        <section class="page-hero">
            <div class="container">
                <h1>Programs Offered</h1>
                <p>Colleges of Central Luzon State University</p>
            </div>
        </section>

        <!-- Colleges -->
        <section id="programs-offered" class="py-5">
            <div class="container">
                <div class="row g-4">
                    @foreach($colleges as $college)
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm programs-offered-card">
                            <div class="college-card-img-wrap">
                                <img src="{{ $college->logo_url }}" alt="{{ $college->name }} Logo" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div class="card-body p-4">
                                <h2 class="h4 fw-bold text-success mb-3">{{ $college->name }}</h2>
                                <p class="text-secondary mb-3">{{ $college->overview_preview }}</p>
                                <div class="programs-offered-contact">
                                    <strong>Contact Information</strong>
                                    <ul class="list-unstyled mb-0 mt-1">
                                        @if($college->contact_email)
                                            <li><a href="mailto:{{ $college->contact_email }}">{{ $college->contact_email }}</a></li>
                                        @else
                                            <li><span class="text-muted">No email available</span></li>
                                        @endif
                                    </ul>
                                </div>
                                <p class="mb-0 mt-3"><a href="{{ route('college.show', $college->slug) }}" class="btn btn-success btn-sm">Visit</a></p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
</body>
</html>
