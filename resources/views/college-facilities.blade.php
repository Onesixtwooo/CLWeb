
@php
    $collegeName = $collegeName ?? 'College of Engineering';
    $collegeSlug = $collegeSlug ?? 'engineering';
    $collegeShortName = $collegeShortName ?? 'CEn';
    $collegeLogoUrl = $collegeLogoUrl ?? asset('images/logos/engineering.jpg');
    // Default green when appearance not yet set up
    $headerColor = ! empty($headerColor) && preg_match('/^#[0-9A-Fa-f]{6}$/', $headerColor) ? $headerColor : '#0d6e42';
    $accentColor = ! empty($accentColor) && preg_match('/^#[0-9A-Fa-f]{6}$/', $accentColor) ? $accentColor : '#0d2818';
    $sectionTitle = $sectionTitle ?? 'Facilities';
    $sectionDescription = $sectionDescription ?? '';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $sectionTitle }} - {{ $collegeName }} - {{ config('app.name', 'CLSU') }}">
    <title>{{ $sectionTitle }} - {{ $collegeName }} - {{ config('app.name', 'CLSU') }}</title>

    <!-- College appearance colors (set in admin Settings) -->
    <style>
        :root {
            --college-header-color: {{ $headerColor }};
            --college-accent-color: {{ $accentColor }};
        }
        .engineering-header-wrapper.engineering-header-scrolled .engineering-top-header::after {
            background: {{ $headerColor }} !important;
        }
        .engineering-header,
        .engineering-navbar {
            background: {{ $headerColor }} !important;
        }
        .engineering-loader-title {
            color: {{ $headerColor }} !important;
        }
        .engineering-loader-spinner {
            border-top-color: {{ $headerColor }} !important;
            border-color: {{ $headerColor }}33 !important;
        }
        .college-page .retro-button,
        .college-page .btn-primary.retro-button {
            background: {{ $headerColor }} !important;
            border-color: {{ $headerColor }} !important;
        }
        .college-page .retro-button:hover,
        .college-page .btn-primary.retro-button:hover {
            filter: brightness(0.92);
        }
        .college-page .retro-link,
        .college-page .link-button {
            color: {{ $headerColor }} !important;
        }
        .college-page .retro-link:hover,
        .college-page .link-button:hover {
            color: {{ $accentColor }} !important;
        }
        .facility-card {
            background: var(--bs-dark, #1a1a1a);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
        }
        .facility-card:hover {
            border-color: {{ $headerColor }};
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
            transform: translateY(-4px);
        }
        .facility-card-image {
            width: 100%;
            height: 240px;
            object-fit: cover;
            background: rgba(255,255,255,0.06);
        }
        .facility-card-body {
            padding: 1.5rem;
        }
        .facility-card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.75rem;
        }
        .facility-card-description {
            color: rgba(255,255,255,0.7);
            font-size: 0.9375rem;
            line-height: 1.6;
        }
        .facilities-header {
            background: linear-gradient(135deg, {{ $headerColor }} 0%, {{ $accentColor }} 100%);
            color: white;
            padding: 4rem 0 3rem;
            margin-top: 118px;
            margin-bottom: 0;
        }
        .facilities-header-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        .facilities-header-description {
            font-size: 1.125rem;
            opacity: 0.95;
            max-width: 800px;
            margin: 0 auto;
        }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: rgba(255,255,255,0.5);
        }
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }
    </style>

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
    <!-- Loader screen -->
    <div id="engineering-loader" class="engineering-loader" aria-hidden="false" aria-label="Loading {{ $collegeName }}">
        <div class="engineering-loader-inner">
            <img src="{{ $collegeLogoUrl }}" alt="{{ $collegeName }}" class="engineering-loader-logo">
            <p class="engineering-loader-title">{{ $collegeName }}</p>
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
                <a href="{{ route('college.show', ['college' => $collegeSlug]) }}" class="navbar-brand d-flex align-items-center gap-2 logo">
                    <div class="logo-box retro-badge">
                        <img src="{{ $collegeLogoUrl }}" alt="{{ $collegeName }} logo" class="logo-image">
                    </div>
                    <div class="logo-text d-flex flex-column">
                        <h2 class="retro-heading mb-0">
                            <span class="logo-full-text d-none d-md-inline">{{ strtoupper($collegeName) }}</span>
                            <span class="logo-short-text d-inline d-md-none">{{ $collegeShortName }}</span>
                        </h2>
                        <p class="retro-subtitle">
                            <span class="d-inline d-md-none">CLSU</span>
                            <span class="d-none d-md-inline">{{ $collegeName }}, Central Luzon State University</span>
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
                            <a href="{{ route('college.show', ['college' => $collegeSlug]) }}" class="nav-link">Home</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="departmentsDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                Departments
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="departmentsDropdown">
                                <li><a href="{{ route('college.show', ['college' => $collegeSlug]) }}#departments" class="dropdown-item">Agricultural &amp; Biosystems Engineering</a></li>
                                <li><a href="{{ route('college.show', ['college' => $collegeSlug]) }}#departments" class="dropdown-item">Civil Engineering</a></li>
                                <li><a href="{{ route('college.show', ['college' => $collegeSlug]) }}#departments" class="dropdown-item">Engineering Sciences</a></li>
                                @if($collegeSlug === 'engineering')
                                <li><a href="{{ route('department.it') }}" class="dropdown-item">Information Technology</a></li>
                                @endif
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="centersDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                Center &amp; Institutes
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="centersDropdown">
                                <li><a href="{{ route('college.show', ['college' => $collegeSlug]) }}#center-institutes" class="dropdown-item">ISI</a></li>
                                <li><a href="{{ route('college.show', ['college' => $collegeSlug]) }}#center-institutes" class="dropdown-item">PreDIC</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('college.show', ['college' => $collegeSlug]) }}#scholarships" class="nav-link">Scholarships</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('news.announcement.board', $collegeSlug) }}" class="nav-link">News &amp; Announcements</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="aboutEngineeringDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                About Us
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="aboutEngineeringDropdown">
                                <li><a href="{{ route('college.show', ['college' => $collegeSlug]) }}#aboutus" class="dropdown-item">Dean's Office</a></li>
                                <li><a href="{{ route('college.show', ['college' => $collegeSlug]) }}#aboutus" class="dropdown-item">Organizational Structure</a></li>
                                <li><a href="{{ route('college.show', ['college' => $collegeSlug]) }}#aboutus" class="dropdown-item">Manual of Operations</a></li>
                                <li><a href="{{ route('college.show', ['college' => $collegeSlug]) }}#aboutus" class="dropdown-item">Contact Us</a></li>
                                <li><a href="{{ route('college.downloads', ['college' => $collegeSlug]) }}" class="dropdown-item">Downloads</a></li>
                                @if($collegeSlug === 'engineering')
                                <li><a href="{{ route('college.engineering.faculty') }}" class="dropdown-item">Faculty</a></li>
                                <li><a href="{{ route('college.engineering.testimonials') }}" class="dropdown-item">Testimonials</a></li>
                                @endif
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
        <!-- Facilities Header -->
        <section class="facilities-header">
            <div class="container text-center">
                <h1 class="facilities-header-title">{{ $sectionTitle }}</h1>
                @if (!empty($sectionDescription))
                    <div class="facilities-header-description">
                        <div class="ql-editor p-0">
                        {!! $sectionDescription !!}
                    </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- Facilities Grid -->
        <section class="py-5" style="background: var(--bs-body-bg, #0f0f0f);">
            <div class="container pb-5">
                @if ($facilities->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">🏢</div>
                        <h3>No facilities listed yet</h3>
                        <p>Check back soon for facilities and resources information.</p>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach ($facilities as $facility)
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="facility-card">
                                    @if (!empty($facility->photo))
                                        <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($facility->photo, 'images') }}" alt="{{ $facility->name }}" class="facility-card-image">
                                    @else
                                        <div class="facility-card-image d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, {{ $headerColor }}20 0%, {{ $accentColor }}20 100%);">
                                            <svg width="64" height="64" fill="{{ $headerColor }}" opacity="0.3" viewBox="0 0 24 24">
                                                <rect x="4" y="2" width="16" height="20" rx="2" ry="2" fill="none" stroke="{{ $headerColor }}" stroke-width="1.5"></rect>
                                                <path d="M9 22v-4h6v4" fill="none" stroke="{{ $headerColor }}" stroke-width="1.5"></path>
                                                <circle cx="8" cy="6" r="0.5"></circle><circle cx="12" cy="6" r="0.5"></circle><circle cx="16" cy="6" r="0.5"></circle>
                                                <circle cx="8" cy="10" r="0.5"></circle><circle cx="12" cy="10" r="0.5"></circle><circle cx="16" cy="10" r="0.5"></circle>
                                                <circle cx="8" cy="14" r="0.5"></circle><circle cx="12" cy="14" r="0.5"></circle><circle cx="16" cy="14" r="0.5"></circle>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="facility-card-body">
                                        <h3 class="facility-card-title">{{ $facility->name }}</h3>
                                        @if (!empty($facility->description))
                                            <p class="facility-card-description">{{ Str::limit(strip_tags($facility->description), 150) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </main>

    <!-- Footer -->
    <!-- Footer -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    @include('includes.college-footer')

    <!-- Bootstrap 5 JS Bundle (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
