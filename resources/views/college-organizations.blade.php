
@php
    $collegeName = $collegeName ?? 'College of Engineering';
    $collegeSlug = $collegeSlug ?? 'engineering';
    $collegeShortName = $collegeShortName ?? 'CEn';
    $collegeLogoUrl = $collegeLogoUrl ?? asset('images/logos/engineering.jpg');
    // Default green when appearance not yet set up
    $headerColor = ! empty($headerColor) && preg_match('/^#[0-9A-Fa-f]{6}$/', $headerColor) ? $headerColor : '#0d6e42';
    $accentColor = ! empty($accentColor) && preg_match('/^#[0-9A-Fa-f]{6}$/', $accentColor) ? $accentColor : '#0d2818';
    $sectionTitle = $sectionTitle ?? 'Student Organizations';
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

    @include('includes.college-css')

    <style>
        .org-card {
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
        }
        .org-card:hover {
            border-color: {{ $headerColor }};
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            transform: translateY(-4px);
        }
        .org-card-logo-wrapper {
            width: 100%;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            padding: 1.5rem;
        }
        .org-card-logo {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .org-card-body {
            padding: 1.25rem 1.5rem 1.5rem;
            border-top: 1px solid rgba(0,0,0,0.06);
        }
        .org-card-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }
        .org-card-acronym {
            font-size: 0.8125rem;
            color: {{ $headerColor }};
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        .org-card-description {
            color: #4a5568;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        .org-card-adviser {
            font-size: 0.8rem;
            color: #718096;
            margin-top: 0.75rem;
        }
        .org-card-scope {
            display: inline-block;
            font-size: 0.75rem;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            background: {{ $headerColor }}22;
            color: {{ $headerColor }};
            border: 1px solid {{ $headerColor }}44;
            margin-top: 0.5rem;
        }
        .orgs-header {
            background: {{ $headerColor }};
            color: white;
            padding: 4rem 0 3rem;
            margin-top: 118px;
            margin-bottom: 0;
        }
        .orgs-header-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        .orgs-header-description {
            font-size: 1.125rem;
            opacity: 0.95;
            max-width: 800px;
            margin: 0 auto;
        }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #718096;
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
    <!-- Includes: Header -->
    @include('includes.college-header')

    <main>
        <!-- Organizations Header -->
        <section class="orgs-header">
            <div class="container text-center">
                <h1 class="orgs-header-title">{{ $sectionTitle }}</h1>
                @if (!empty($sectionDescription))
                    <div class="orgs-header-description">
                        {!! $sectionDescription !!}
                    </div>
                @endif
            </div>
        </section>

        <!-- Organizations Grid -->
        <section class="py-5 bg-white">
            <div class="container pb-5">
                @if ($organizations->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">🏛️</div>
                        <h3>No organizations listed yet</h3>
                        <p>Check back soon for student organizations information.</p>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach ($organizations as $org)
                            @php
                                // Ensure we generate a usable route key even when acronym is missing.
                                $orgRouteKey = $org->acronym ?: $org->id;
                            @endphp
                            <div class="col-12 col-md-6 col-lg-4">
                                <a href="{{ route('college.organization.show', ['college' => $collegeSlug, 'organization' => $orgRouteKey]) }}"
                                   class="text-decoration-none d-block h-100">
                                <div class="org-card" style="cursor: pointer;">
                                    <div class="org-card-logo-wrapper">
                                        @if (!empty($org->logo))
                                            <img src="{{ str_starts_with($org->logo, 'http') ? $org->logo : asset($org->logo) }}"
                                                 alt="{{ $org->name }}" class="org-card-logo">
                                        @else
                                            <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($collegeLogoUrl) }}"
                                                 alt="{{ $collegeName }} Logo"
                                                 class="org-card-logo opacity-50"
                                                 style="filter: grayscale(100%);">
                                        @endif
                                    </div>
                                    <div class="org-card-body">
                                        <h3 class="org-card-title">{{ $org->name }}</h3>
                                        @if (!empty($org->acronym))
                                            <div class="org-card-acronym">{{ $org->acronym }}</div>
                                        @endif
                                        @if (!empty($org->description))
                                            <p class="org-card-description">{{ Str::limit(strip_tags($org->description), 150) }}</p>
                                        @endif
                                        @if (!empty($org->adviser))
                                            <div class="org-card-adviser">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px; opacity: 0.6;">
                                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="9" cy="7" r="4"></circle>
                                                </svg>
                                                Adviser: {{ $org->adviser }}
                                            </div>
                                        @endif
                                        @if ($org->department)
                                            <div class="org-card-scope">{{ $org->department->name }}</div>
                                        @endif
                                    </div>
                                </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </main>

    <!-- Footer -->
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
