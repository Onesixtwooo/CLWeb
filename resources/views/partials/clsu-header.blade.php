<header class="header fixed-top bg-white border-bottom" style="z-index: 1040;">
    <nav class="navbar navbar-expand-md navbar-light bg-white">
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

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#clsuNavbar"
                    aria-controls="clsuNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="clsuNavbar">
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
