<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ config('app.name', 'Laravel') }} - Coming Soon">
    <title>{{ config('app.name', 'Laravel') }} - Coming Soon</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Buttershine+Serif:wght@400;700&family=Libre+Franklin:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .coming-soon-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--background, #f8f9fa);
        }

        .coming-soon-content {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            padding: 2rem;
        }

        .coming-soon-logo {
            width: min(280px, 70vw);
            height: auto;
        }

        .coming-soon-title {
            font-family: "Libre Franklin", sans-serif;
            font-weight: 700;
            letter-spacing: 1px;
            color: var(--foreground, #1b1b1b);
            text-transform: uppercase;
            margin: 0;
        }

        .coming-soon-subtitle {
            color: var(--text-muted, #6b7280);
            margin: 0;
        }
    </style>
</head>
<body class="retro-modern">
    <!-- Header (Bootstrap navbar) -->
    <header class="header">
        <nav class="navbar navbar-expand-md navbar-light bg-white">
            <div class="container">
                <!-- Logo / Brand -->
                <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center gap-2 logo">
                    <div class="logo-box retro-badge">
                        <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl(\App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp')) }}" alt="CLSU Logo" class="logo-image">
                    </div>
                    <div class="logo-text d-flex flex-column">
                        <h2 class="retro-heading mb-0">CENTRAL LUZON STATE UNIVERSITY</h2>
                        <p class="retro-subtitle">Science City of Muñoz, 3120 Nueva Ecija, Philippines</p>
                    </div>
                </a>

                <!-- Navbar toggler -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation links + auth buttons -->
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-md-0 align-items-md-center">
                        <li class="nav-item">
                            <a href="#home" class="nav-link">Home</a>
                        </li>

                        <li class="nav-item dropdown">
                            <a href="{{ url('/about') }}" class="nav-link">
                                About Us <span class="dropdown-toggle-icon">▼</span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
                                <li><a href="{{ url('/about') }}#history" class="dropdown-item">History</a></li>
                                <li><a href="{{ url('/about') }}#brand-guidelines" class="dropdown-item">Brand Guidelines</a></li>
                                <li><a href="{{ url('/about') }}#campus-life" class="dropdown-item">Campus Life</a></li>
                                <li><a href="{{ url('/about') }}#offices" class="dropdown-item">Offices</a></li>
                                <li><a href="{{ url('/about') }}#university-officials" class="dropdown-item">University Officials</a></li>
                                <li><a href="{{ url('/about') }}#organizational-structure" class="dropdown-item">Organizational Structure</a></li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a href="#academic" class="nav-link dropdown-toggle" id="academicDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                Academic Affairs
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="academicDropdown">
                                <li><a href="#academic" class="dropdown-item">Academic Affairs</a></li>
                                <li><a href="#research" class="dropdown-item">Research &amp; Extension</a></li>
                                <li><a href="#business" class="dropdown-item">Business Affairs</a></li>
                                <li><a href="#administrative" class="dropdown-item">Administrative Services</a></li>
                                <li><a href="#international" class="dropdown-item">International Affairs</a></li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="#news" class="nav-link">News Updates</a>
                        </li>
                        <li class="nav-item">
                            <a href="#contact" class="nav-link">Contact</a>
                        </li>

                        <!-- Auth buttons -->
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

    <main class="coming-soon-page">
        <div class="coming-soon-content">
            <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl(\App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp')) }}" alt="CLSU Logo" class="coming-soon-logo">
            <h1 class="coming-soon-title">Central Luzon State University</h1>
            <p class="coming-soon-subtitle">Coming Soon</p>
        </div>
    </main>
</body>
</html>
