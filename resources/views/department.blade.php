<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $department->name }} - {{ $collegeName }}, {{ config('app.name', 'CLSU') }}">
    <title>{{ $department->name }} - {{ config('app.name', 'CLSU') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Buttershine+Serif:wght@400;700&family=Libre+Franklin:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/college.css', 'resources/js/app.js'])

    @include('includes.college-css')

    <style>
        :root {
            --college-header-color: {{ $headerColor }};
            --college-accent-color: {{ $accentColor }};
            --college-header-gradient: {{ $headerColor }};
        }

        /* Dynamic college header styles */
        .engineering-top-header,
        .engineering-top-header::after {
            background: {{ $topHeaderColor ?? $accentColor }} !important;
        }
        .engineering-header,
        .engineering-navbar {
            background: {{ $headerColor }} !important;
        }

        .engineering-navbar {
            background: {{ $headerColor }};
        }

        .engineering-navbar .nav-link,
        .engineering-navbar .retro-heading,
        .engineering-navbar .retro-subtitle {
            color: #ffffff;
        }

        /* Keep navbar links white and background transparent on hover/click */
        .engineering-navbar .nav-link:hover,
        .engineering-navbar .nav-link:focus,
        .engineering-navbar .nav-link:active,
        .engineering-navbar .nav-link.show,
        .engineering-navbar .show > .nav-link {
            color: #ffffff;
            background-color: transparent !important;
        }

        .it-hero {
            position: relative;
            min-height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            padding: 5rem 0 4rem;
            overflow: hidden;
            @php
                $bannerSection = $department->getSection('overview');
                $bannerImages = $bannerSection['banner_images'] ?? [];
                $bannerImage = $bannerSection['banner_image'] ?? null;

                // Fallback to single image if array is empty but single image exists
                if (empty($bannerImages) && $bannerImage) {
                    $bannerImages[] = $bannerImage;
                }
            @endphp

            @if(empty($bannerImages))
                background: linear-gradient(135deg, {{ $headerColor }} 0%, {{ $accentColor }} 100%);
            @endif
        }

        /* Carousel Background */
        .it-hero-carousel {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
        
        .it-hero-carousel .carousel-item {
            height: 100vh; /* Approximate height cover */
            min-height: 60vh;
        }

        .it-hero-carousel .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .accreditation-logo-wrap {
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            padding: 2rem;
            position: relative;
        }
        .alumni-richtext {
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .alumni-richtext p:last-child {
            margin-bottom: 0;
        }
        .alumni-richtext img,
        .alumni-richtext iframe,
        .alumni-richtext video {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .alumni-richtext iframe {
            width: 100%;
            aspect-ratio: 16 / 9;
        }
        .alumni-richtext ul,
        .alumni-richtext ol {
            padding-left: 1.5rem;
            text-align: left;
        }
        .alumni-richtext table {
            display: block;
            width: 100%;
            overflow-x: auto;
        }
        .alumni-richtext pre {
            white-space: pre-wrap;
            word-break: break-word;
        }

        .it-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 1; /* Above carousel, below content */
            background: var(--college-header-gradient);
            @if(!empty($bannerImages))
            opacity: 0.55;
            @else
            opacity: 0.1;
            @endif
        }


        .it-hero-inner {
            position: relative;
            z-index: 2; /* Above overlay */
            max-width: 820px;
            margin: 0 auto;
            text-align: center;
        }

        .department-retro-hero {
            padding-top: 0 !important;
        }

        .department-retro-hero .hero-slider-container {
            height: min(calc(100vh - 118px), 760px);
            min-height: 60vh;
        }

        body.engineering-nav-hidden .department-retro-hero .hero-slider-container {
            height: min(calc(100vh - 2.5rem), 760px);
            min-height: 60vh;
        }

        .it-hero-kicker {
            font-size: 0.9rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #ffe4e6;
        }

        .it-hero-title {
            font-size: clamp(2.4rem, 4vw, 3.2rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1rem;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #ffffff;
        }

        .it-hero-subtitle {
            max-width: 640px;
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
            margin-left: auto;
            margin-right: auto;
        }

        .it-section-menu {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 35px; /* Height of sticky top bar */
            z-index: 50;
        }

        .it-section-menu-inner {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem 0;
        }

        .it-section-menu button,
        .it-section-menu a {
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            padding: 0.4rem 0.9rem;
            font-size: 0.85rem;
            font-weight: 600;
            background: #ffffff;
            color: #374151;
            text-decoration: none;
            transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
        }

        .it-section-menu a:hover {
            background: {{ $headerColor }};
            border-color: {{ $headerColor }};
            color: #ffffff;
        }

        /* Tabbed sections (show one at a time) */
        .it-tab-section {
            display: none;
        }

        .it-tab-section.is-active {
            display: block;
        }

        .it-section-menu a.is-active {
            background: {{ $headerColor }};
            border-color: {{ $headerColor }};
            color: #ffffff;
        }

        @media (max-width: 767.98px) {
            .it-section-menu {
                top: 35px;
            }

            .it-section-menu .container {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .it-section-menu-inner {
                flex-wrap: nowrap;
                justify-content: flex-start;
                gap: 0.45rem;
                padding: 0.7rem 0 0.8rem;
                overflow-x: auto;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }

            .it-section-menu-inner::-webkit-scrollbar {
                display: none;
            }

            .it-section-menu button,
            .it-section-menu a {
                flex: 0 0 auto;
                white-space: nowrap;
                padding: 0.38rem 0.8rem;
                font-size: 0.8rem;
            }
        }

        /* Footer socials (KEEP IN TOUCH) */
        .it-footer-social-title {
            font-weight: 800;
            letter-spacing: 1px;
            color: #ffffff;
            margin: 1.5rem 0 0.75rem;
        }

        .it-footer-social {
            display: flex;
            flex-wrap: wrap;
            gap: 0.85rem;
        }

        .it-footer-social a {
            width: 52px;
            height: 52px;
            border-radius: 999px;
            background: {{ $headerColor }};
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-weight: 900;
            font-size: 1.25rem;
            transition: transform 0.15s ease, background-color 0.15s ease;
        }

        .it-footer-social a:hover {
            background: {{ $accentColor }};
            transform: translateY(-1px);
        }

        /* IT Facilities galleries */
        .it-facilities-group + .it-facilities-group {
            margin-top: 2.25rem;
            padding-top: 2.25rem;
            border-top: 1px solid #e5e7eb;
        }
        .it-facilities-group-title {
            font-weight: 800;
            color: #111827;
            margin-bottom: 0.25rem;
        }
        .it-facilities-group-subtitle {
            color: #6b7280;
            margin-bottom: 1.25rem;
        }
        .it-facility-card {
            border: 0;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            background: #ffffff;
            height: 100%;
        }
        .it-facility-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .it-facility-card-body {
            padding: 0.9rem 1rem;
        }
        .it-facility-card-title {
            font-weight: 700;
            margin: 0 0 0.25rem 0;
            color: #111827;
        }
        .it-facility-card-text {
            margin: 0;
            font-size: 0.9rem;
            color: #6b7280;
        }
        .it-facilities-map {
            border: 2px dashed rgba(134, 9, 10, 0.35);
            border-radius: 0.75rem;
            background: linear-gradient(135deg, rgba(134, 9, 10, 0.06) 0%, rgba(179, 18, 20, 0.04) 100%);
            padding: 1.25rem;
        }
        .it-facilities-map .ratio {
            border-radius: 0.65rem;
            overflow: hidden;
            background: #fff;
            box-shadow: inset 0 0 0 1px #e5e7eb;
        }
        .it-facilities-map-placeholder {
            height: 100%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 1.25rem;
            color: #6b7280;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        .it-facilities-map-note {
            margin-top: 0.9rem;
            margin-bottom: 0;
            color: #6b7280;
            font-size: 0.9rem;
        }

        /* Faculty Card Styles */
        .faculty-card {
            background: {{ $headerColor }}; /* Body colored based on header */
            border-radius: 0;
            overflow: hidden;
            border: 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .faculty-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .faculty-card-photo-wrap {
            background: #ffffff;
            padding: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .faculty-card-photo {
            width: 100%;
            height: 180px;
            border-radius: 0;
            object-fit: contain;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.05);
            background-color: #f3f4f6;
        }
        .faculty-card-body { 
            padding: 1.5rem; 
            color: #ffffff;
            flex: 1;
            display: flex;
            flex-direction: column;
            text-align: left;
        }
        .faculty-card-name {
            font-weight: 700;
            color: #ffffff;
            font-size: 1.125rem;
            margin-bottom: 0.25rem;
            line-height: 1.2;
        }
        .faculty-card-position {
            color: rgba(255,255,255,0.9);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .faculty-card-dept {
            color: rgba(255,255,255,0.7);
            font-size: 0.8rem;
            margin-bottom: 0.25rem;
            line-height: 1.4;
        }
        .faculty-card-email {
            font-size: 0.8rem;
            margin-top: auto;
            padding-top: 0.5rem;
        }
        .faculty-card-email a { 
            color: rgba(255,255,255,0.8); 
            text-decoration: none; 
            transition: color 0.15s;
        }
        .faculty-card-email a:hover { 
            color: #ffffff; 
            text-decoration: underline; 
        }
        /* Alignment for Objectives and other lists */
        .objectives-list li {
            display: flex;
            align-items: start;
        }
        .objectives-number {
            color: {{ $headerColor }};
            font-weight: bold;
            margin-right: 0.5rem;
            flex-shrink: 0;
            line-height: inherit;
        }
        .objectives-content {
            flex-grow: 1;
        }
        .objectives-content p:first-child {
            margin-top: 0;
        }
        .objectives-content p:last-child {
            margin-bottom: 0;
        }
        .retro-section-text {
            color: #4b5563;
            font-size: 1.125rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        .text-center .retro-section-text p {
            text-align: center;
            margin-bottom: 1rem;
        }
        .text-center .retro-section-text img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            display: block;
            margin: 1rem auto;
        }
    </style>
</head>
<body class="retro-modern college-page" style="padding-top: 118px;">
    <div class="engineering-header-wrapper">
    @include('partials.college-top-header')
    <div class="engineering-nav-outer">
    <!-- Header reused from College of Engineering -->
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
                            <span class="d-none d-md-inline">{{ $department->name }}</span>
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
                            <a href="{{ route('college.show', ['college' => $collegeSlug]) }}#home" class="nav-link">College Home</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('college.show', ['college' => $collegeSlug]) }}#departments" class="nav-link">Departments</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('college.show', ['college' => $collegeSlug]) }}#explore" class="nav-link">Facilities</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    </div>
    </div>

    <main>
        <!-- Hero / Overview -->
        @if(isset($retroList) && $retroList->count() > 0)
        <section class="hero retro-hero department-retro-hero">
            <div class="hero-slider-container">
                <div class="hero-slider">
                    @foreach($retroList as $index => $item)
                        <div class="hero-slide {{ $index === 0 ? 'active' : '' }}">
                            <div class="hero-slide-bg">
                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($item->background_image ?: ($department->card_image ?: $collegeLogoUrl)) }}" alt="{{ $department->name }}">
                                <div class="hero-overlay"></div>
                            </div>
                            <div class="hero-slide-content">
                                <div class="hero-text">
                                    <span class="section-badge retro-label college-theme" style="{{ $headerColor ? 'background-color: ' . $headerColor . ' !important;' : '' }} padding: 0.5rem 1.25rem; {{ $item->stamp_size ? 'font-size: ' . $item->stamp_size . 'px;' : '' }}">
                                        {{ $item->stamp ? strtoupper($item->stamp) : strtoupper($collegeName) }}
                                    </span>
                                    <h1 class="retro-title" style="{{ $item->title_size ? 'font-size: ' . $item->title_size . 'px;' : '' }}">
                                        {{ $item->title ?: $department->name }}
                                    </h1>
                                    <div class="retro-description">
                                        {!! $item->description ?: ($department->details ?: 'Learn more about the department.') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($retroList->count() > 1)
                    <button class="hero-slider-arrow hero-prev" id="heroPrev">‹</button>
                    <button class="hero-slider-arrow hero-next" id="heroNext">›</button>
                @endif
            </div>
        </section>
        @else
        <section class="it-hero">
            @if(!empty($bannerImages))
                <div id="heroCarousel" class="carousel slide carousel-fade it-hero-carousel" data-bs-ride="carousel" data-bs-interval="5000">
                    <div class="carousel-inner h-100">
                        @foreach($bannerImages as $index => $img)
                            <div class="carousel-item h-100 {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($img) }}" alt="Hero Background {{ $index + 1 }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            <div class="container position-relative z-2 w-100 text-center">
                <div class="it-hero-inner py-5 mx-auto">
                    <h1 class="it-hero-title">
                       {{ $department->name }}
                    </h1>
                    @if($department->details)
                    <p class="it-hero-subtitle mb-4">
                        {{ $department->details }}
                    </p>
                    @endif
                </div>
            </div>
        </section>
        @endif

        <!-- Section Menu -->
        @php
            $overviewVisible = $department->overview_is_visible ?? true;
            $hasOverview = $overviewVisible && (
                          !empty($department->overview_body) || 
                          ($department->outcomes->count() > 0) ||
                          (!empty($department->graduate_outcomes))
            );
            
            $objectivesVisible = $department->objectives_is_visible ?? true;
            $hasObjectives = $objectivesVisible && ($department->objectives->count() > 0 || !empty($department->objectives_body) || $department->curricula->count() > 0);
            $hasFaculty = ($facultySectionVisible ?? true) && $faculty->count() > 0;
            $hasPrograms = $department->programs_is_visible && $department->programs->count() > 0;
            $hasAwards = $department->awards_is_visible && $awards->total() > 0;
            $hasResearch = $department->research_is_visible && $research->total() > 0;
            $hasExtension = $department->extension_is_visible && $extension->total() > 0;
            $hasTraining = $department->training_is_visible && $training->total() > 0;
            
            // Linkages
            $hasLinkages = ($department->linkages_is_visible ?? true) && !empty($department->linkages_body); 
            $hasFacilities = $department->facilities_is_visible && $facilities->total() > 0;
            $hasAlumni = $department->alumni_is_visible && $alumni->total() > 0;
            $hasMemberships = ($membershipSectionVisible ?? true) && isset($memberships) && $memberships->count() > 0;

            $anyButtonShown = $hasOverview || $hasObjectives || $hasFaculty || $hasPrograms || 
                               $hasAwards || $hasResearch || $hasExtension || $hasTraining ||
                               $hasLinkages || $hasFacilities || $hasAlumni || $hasMemberships;
        @endphp

        <!-- Section Menu -->
        @if($anyButtonShown)
        <section class="it-section-menu">
            <div class="container">
                <nav class="it-section-menu-inner" aria-label="{{ $department->name }} sections">
                    @if($hasOverview)
                        <a href="#program-overview" data-tab="program-overview">Overview</a>
                    @endif

                    @if($hasObjectives)
                        <a href="#objectives" data-tab="objectives">Objectives</a>
                    @endif

                    @if($hasFaculty)
                        <a href="#faculty" data-tab="faculty">Faculty</a>
                    @endif

                    @if($hasPrograms)
                        <a href="#programs" data-tab="programs">Programs</a>
                    @endif

                    @if($hasAwards)
                        <a href="#awards" data-tab="awards">Awards</a>
                    @endif

                    @if($hasResearch)
                        <a href="#research" data-tab="research">Research</a>
                    @endif

                    @if($hasExtension)
                        <a href="#extension" data-tab="extension">Extension</a>
                    @endif

                    @if($hasTraining)
                        <a href="#training" data-tab="training">Training</a>
                    @endif

                    @if($hasLinkages)
                        <a href="#linkages" data-tab="linkages">Linkages</a>
                    @endif

                    @if($hasFacilities)
                        <a href="#facilities" data-tab="facilities">Facilities</a>
                    @endif

                    @if($hasAlumni)
                        <a href="#alumni" data-tab="alumni">Alumni</a>
                    @endif

                    @if($hasMemberships)
                        <a href="#memberships" data-tab="memberships">Memberships</a>
                    @endif
                    
                    @if(($organizationsSectionVisible ?? true) && isset($organizations) && $organizations->isNotEmpty())
                        <a href="#organizations" data-tab="organizations">Organizations</a>
                    @endif
                </nav>
            </div>
        </section>
        @endif

        <!-- Overview Section -->
        @if($overviewVisible)
        <section id="program-overview" class="py-5 it-tab-section is-active">
            <div class="container">
                @php
                    // Use model attributes directly
                    $overviewTitle = $department->overview_title ?? 'Overview';
                    $overviewBody = $department->overview_body;
                    $graduateOutcomes = $department->graduate_outcomes;
                    $graduateOutcomesImage = $department->graduate_outcomes_image;
                    
                    $hasContent = !empty($overviewBody) || !empty($graduateOutcomes);
                @endphp

                @if(!$hasContent)
                    <div class="text-center py-5">
                        <span class="d-block display-1 mb-3">🚧</span>
                        <h3 class="fw-bold mb-3">This Page Is Coming Soon</h3>
                        <p class="lead text-muted mb-0">We’re still working on this section. Check back soon!</p>
                    </div>
                @else
                    {{-- Overview Body --}}
                    @if(!empty($overviewBody))
                    <div class="row align-items-start gy-4 mb-5">
                        <div class="col-lg-7">
                            <span class="section-badge retro-label mb-4 d-inline-block" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; letter-spacing: 2px; width: fit-content;">{{ $overviewTitle }}</span>
                            
                            @if(!empty($overviewBody))
                            <div class="retro-section-text mb-3" style="font-size: 1rem; line-height: 1.6;">
                                {!! $overviewBody !!}
                            </div>
                            @endif


                        </div>
                        <div class="col-lg-5">
                            <div class="ratio ratio-4x3 rounded overflow-hidden shadow-sm bg-light d-flex align-items-center justify-content-center">
                                @if(!empty($department->logo))
                                    <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($department->logo) }}" alt="{{ $department->name }} Logo" class="w-100 h-100" style="object-fit: contain; padding: 1rem;">
                                @else
                                    <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl(\App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp')) }}" alt="CLSU Logo" class="w-100 h-100" style="object-fit: contain; padding: 1rem;">
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Graduate Outcomes --}}
                    <div id="graduate-outcomes">
                        @if(isset($department->outcomes) && $department->outcomes->count() > 0)
                            <div class="mt-5 mb-4 d-flex flex-column align-items-center text-center">
                                 <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Graduate Outcomes</span>
                            </div>
                        <div class="row g-4 justify-content-center">
                            @foreach($department->outcomes as $outcome)
                                <div class="col-lg-4 col-md-6">
                                    <div class="card border-0 shadow-sm overflow-hidden h-100">
                                        <div class="ratio ratio-16x9">
                                            @if($outcome->image)
                                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($outcome->image) }}" class="card-img-top object-fit-cover" alt="{{ $outcome->title }}">
                                            @elseif(!empty($department->logo))
                                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($department->logo) }}" class="card-img-top object-fit-contain p-3" alt="{{ $outcome->title }}">
                                            @else
                                                <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl(\App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp')) }}" class="card-img-top object-fit-contain p-3" alt="{{ $outcome->title }}">
                                            @endif
                                        </div>
                                        <div class="card-body p-4 text-center">
                                            @if($outcome->title)
                                                <h4 class="fw-bold mb-3 text-dark">{{ $outcome->title }}</h4>
                                            @endif
                                            @if($outcome->description)
                                                <div class="retro-section-text text-start mt-3" style="font-size: 1rem; line-height: 1.6;">
                                                    {!! $outcome->description !!}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif(!empty($department->graduate_outcomes) && trim($department->graduate_outcomes) !== '')
                        <div class="row justify-content-center">
                            <div class="col-lg-6 col-md-8">
                                <div class="card border-0 shadow-sm overflow-hidden h-100">
                                    <div class="ratio ratio-16x9">
                                        @if(!empty($department->graduate_outcomes_image))
                                            <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($department->graduate_outcomes_image) }}" class="card-img-top object-fit-cover" alt="Graduate Outcomes">
                                        @elseif(!empty($department->logo))
                                            <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($department->logo) }}" class="card-img-top object-fit-contain p-3" alt="Graduate Outcomes">
                                        @else
                                            <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl(\App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp')) }}" class="card-img-top object-fit-contain p-3" alt="Graduate Outcomes">
                                        @endif
                                    </div>
                                    <div class="card-body p-4 text-center">
                                        @if(!empty($department->graduate_outcomes_title))
                                            <h4 class="fw-bold mb-3 text-dark">{{ $department->graduate_outcomes_title }}</h4>
                                        @else
                                             <span class="section-badge retro-label mb-3" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Graduate Outcomes</span>
                                        @endif
                                        <div class="retro-section-text text-start mt-3" style="font-size: 1rem; line-height: 1.6;">
                                            {!! $department->graduate_outcomes !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </section>
        @endif

        <!-- Objectives Section -->
        @if($hasObjectives)
        <section id="objectives" class="py-5 bg-light it-tab-section">
            <div class="container">
                <div class="section-header mb-4 d-flex flex-column align-items-center text-center">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Objectives</span>
                    <h2 class="retro-section-title mt-3">{{ $department->objectives_title ?? 'Objectives' }}</h2>
                    @if(!empty($department->objectives_body))
                        <div class="retro-section-text text-center w-100">
                            {!! $department->objectives_body !!}
                        </div>
                    @endif
                </div>
                <div class="row">
                    <div class="col-lg-10 mx-auto">
                        @if($department->objectives->count() > 0)
                            <ul class="list-unstyled mb-0 objectives-list" style="font-size: 1rem; line-height: 1.8;">
                                @foreach($department->objectives as $index => $objective)
                                    <li class="mb-3">
                                        <span class="objectives-number">{{ $index + 1 }}.</span>
                                        <div class="objectives-content">{!! $objective->content !!}</div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted text-center">No objectives available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- Faculty Section -->
        @if($hasFaculty)
        <section id="faculty" class="py-5 bg-light it-tab-section">
            <div class="container">
                <div class="section-header mb-4 d-flex flex-column align-items-center text-center">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Faculty</span>
                    <h2 class="retro-section-title mt-3">{{ $facultySectionTitle }}</h2>
                    <div class="retro-section-text text-center w-100">
                        {!! $facultySectionDescription !!}
                    </div>
                </div>

                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                    @forelse($faculty as $member)
                        <div class="col">
                            <div class="faculty-card h-100">
                                <div class="faculty-card-photo-wrap">
                                    @if($member->photo)
                                        <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($member->photo, 'images') }}" alt="{{ $member->name }}" class="faculty-card-photo">
                                    @else
                                        <div class="faculty-card-photo d-flex align-items-center justify-content-center text-muted small">No photo</div>
                                    @endif
                                </div>
                                <div class="faculty-card-body">
                                    <h5 class="faculty-card-name">{{ $member->name }}</h5>
                                    @if($member->position)
                                        <p class="faculty-card-position">{{ $member->position }}</p>
                                    @endif
                                    
                                    @if($member->department)
                                        <p class="faculty-card-dept">{{ $member->department }}</p>
                                    @endif
                                    
                                    @if($member->email)
                                        <p class="faculty-card-email mb-0"><a href="mailto:{{ $member->email }}">
                                            <i class="bi bi-envelope me-1"></i>{{ $member->email }}
                                        </a></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <p class="text-muted mb-0">No faculty members listed yet.</p>
                        </div>
                    @endforelse
                </div>


            </div>
        </section>
        @endif

        {{-- Programs Section --}}
        @if($hasPrograms)
        <section id="programs" class="py-5 it-tab-section">
            <div class="container-fluid">
                <div class="mt-4 mb-5 text-center">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">{{ $department->programs_title ?: 'Academic Programs' }}</span>
                </div>

                @if(!empty($department->programs_body))
                    <div class="row justify-content-center mb-4">
                        <div class="col-lg-10">
                            <div class="retro-section-text text-center">{!! $department->programs_body !!}</div>
                        </div>
                    </div>
                @endif
                
                <div class="row g-4 justify-content-center">
                    @foreach($department->programs as $program)
                        <div class="col-12">
                            <div class="card border-0 shadow-sm overflow-hidden h-100">
                                @if($program->image)
                                    <div class="row g-0">
                                        <div class="col-md-4 col-lg-3">
                                            <div class="h-100 position-relative" style="min-height: 250px;">
                                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($program->image) }}" class="position-absolute w-100 h-100 object-fit-cover" alt="{{ $program->title }}">
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-9">
                                            <div class="card-body p-4 p-lg-5">
                                                <h3 class="fw-bold mb-3 text-dark">{{ $program->title }}</h3>
                                                @if($program->description)
                                                    <div class="text-muted d-flex" style="font-size: 1.05rem; line-height: 1.7;">
                                                        <div>{!! $program->description !!}</div>
                                                    </div>
                                                @endif

                                                @if(!empty($program->numbered_content) && is_array($program->numbered_content))
                                                    <div class="mt-4">
                                                        @foreach($program->numbered_content as $content)
                                                            <div class="mb-4 text-muted" style="font-size: 1.05rem; line-height: 1.6;">
                                                                <div class="d-inline-block fw-bold text-center mb-2" 
                                                                     style="background: {{ $headerColor }}; 
                                                                            color: white; 
                                                                            padding: 0.5rem 1.25rem; 
                                                                            border-radius: 0; 
                                                                            text-transform: uppercase; 
                                                                            letter-spacing: 2px;
                                                                            font-family: 'Libre Franklin', sans-serif;
                                                                            font-size: 0.875rem;
                                                                            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);">
                                                                    {{ $content['label'] ?? '' }}
                                                                </div>
                                                                <div class="mt-2">{!! $content['text'] ?? '' !!}</div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="card-body p-4 p-lg-5 text-center">
                                        <h3 class="fw-bold mb-4 text-dark display-6" style="font-size: 2rem;">{{ $program->title }}</h3>
                                        @if($program->description)
                                            <div class="text-muted d-flex justify-content-center text-start" style="font-size: 1.1rem; line-height: 1.8; max-width: 1000px; margin: 0 auto;">
                                                <div>{!! $program->description !!}</div>
                                            </div>
                                        @endif

                                        @if(!empty($program->numbered_content) && is_array($program->numbered_content))
                                            <div class="mt-4 mx-auto text-start" style="max-width: 1000px;">
                                                @foreach($program->numbered_content as $content)
                                                    <div class="mb-4 text-muted" style="font-size: 1.1rem; line-height: 1.6;">
                                                        <div class="d-inline-block fw-bold text-center mb-2" 
                                                             style="background: {{ $headerColor }}; 
                                                                    color: white; 
                                                                    padding: 0.5rem 1.25rem; 
                                                                    border-radius: 0; 
                                                                    text-transform: uppercase; 
                                                                    letter-spacing: 2px;
                                                                    font-family: 'Libre Franklin', sans-serif;
                                                                    font-size: 0.875rem;
                                                                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);">
                                                            {{ $content['label'] ?? '' }}
                                                        </div>
                                                        <div class="mt-2">{!! $content['text'] ?? '' !!}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- Awards Section (placeholder) -->
        <!-- Awards Section -->
        @if($hasAwards)
            <section id="awards" class="py-5 bg-light it-tab-section">
            <div class="container">
                <div class="section-header mb-4 d-flex flex-column align-items-center text-center">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Awards</span>
                    <h2 class="retro-section-title mt-3">{{ $department->awards_title ?? 'Student & Faculty Awards' }}</h2>
                    <div class="retro-section-text mt-2">
                        {!! $department->awards_body ?? 'Recognition of excellence and achievements.' !!}
                    </div>
                </div>

                <div class="row g-4">
                    {{-- Awards are passed from controller as $awards (LengthAwarePaginator) --}}

                    @forelse($awards as $award)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="ratio ratio-16x9 bg-secondary bg-opacity-10">
                                    @if(!empty($award['image']))
                                        <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($award['image']) }}" class="object-fit-cover" alt="{{ $award['title'] }}">
                                    @elseif(!empty($department->logo))
                                        <div class="d-flex align-items-center justify-content-center h-100 bg-white">
                                            <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($department->logo) }}" class="img-fluid p-4" alt="Department Logo" style="max-height: 100%; object-fit: contain;">
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                            <div class="text-center">
                                                <i class="bi bi-trophy fs-3 mb-2 d-block"></i>
                                                <small>No Image</small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title fw-bold mb-2">{{ $award['title'] }}</h5>
                                    <p class="card-text text-muted small">{{ $award['description'] ?? 'No description available.' }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <p class="text-muted">No awards listed yet.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $awards->links() }}
                </div>
            </div>
        </section>
        @endif

        <!-- Research Section -->
        @if($hasResearch)
            <section id="research" class="py-5 it-tab-section">
            <div class="container">
                <div class="section-header mb-4 d-flex flex-column align-items-center text-center">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Research</span>
                    <h2 class="retro-section-title mt-3">{{ $department->research_title ?: 'Research Requests & Projects' }}</h2>
                    @if(!empty($department->research_body))
                        <div class="retro-section-text">{!! $department->research_body !!}</div>
                    @else
                        <p class="retro-section-text">Research initiatives and featured projects.</p>
                    @endif
                </div>

                <div class="row g-4">
                    {{-- Research items are passed from controller as $research (LengthAwarePaginator) --}}

                    @forelse($research as $item)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="ratio ratio-16x9 bg-secondary bg-opacity-10">
                                    @if(!empty($item['image']))
                                        <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($item['image']) }}" class="object-fit-cover" alt="{{ $item['title'] }}">
                                    @elseif(!empty($department->logo))
                                        <div class="d-flex align-items-center justify-content-center h-100 bg-white">
                                            <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($department->logo) }}" class="img-fluid p-4" alt="Department Logo" style="max-height: 100%; object-fit: contain;">
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                            <div class="text-center">
                                                <i class="bi bi-journal-text fs-3 mb-2 d-block"></i>
                                                <small>No Image</small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title fw-bold mb-1">{{ $item['title'] }}</h5>
                                    @if(!empty($item['completed_year']))
                                        <div class="text-secondary small mb-2">
                                            <i class="bi bi-calendar-check me-1"></i> {{ $item['completed_year'] }}
                                        </div>
                                    @endif
                                    <p class="card-text text-muted small">{{ $item['description'] ?? 'No description available.' }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <p class="text-muted">No research items listed yet.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $research->appends(request()->except('research_page'))->links() }}
                </div>
            </div>
        </section>
        @endif

        <!-- Extension Section -->
        @if($hasExtension)
            <section id="extension" class="py-5 bg-light it-tab-section">
                <div class="container">
                    <div class="section-header mb-5 d-flex flex-column align-items-center text-center">
                        <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">{{ $department->extension_title ?: 'Extension Services' }}</span>
                        @if(!empty($department->extension_body))
                            <div class="retro-section-text mt-4">{!! $department->extension_body !!}</div>
                        @endif
                    </div>

                    <div class="row g-4 d-flex justify-content-center">
                        @forelse($extension as $item)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 shadow-sm border-0 transition-hover">
                                    <div class="position-relative overflow-hidden" style="height: 200px;">
                                        @if(!empty($item['image']))
                                            <img src="{{ asset($item['image']) }}" class="card-img-top w-100 h-100 object-fit-cover" alt="{{ $item['title'] }}">
                                        @elseif(!empty($department->logo))
                                            <div class="d-flex align-items-center justify-content-center h-100 bg-white">
                                                <img src="{{ asset($department->logo) }}" class="img-fluid p-4" alt="Department Logo" style="max-height: 100%; object-fit: contain;">
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center justify-content-center h-100 bg-light text-muted">
                                                <i class="bi bi-people fs-1"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold mb-3">{{ $item['title'] }}</h5>
                                        <p class="card-text text-muted small">{{ $item['description'] ?? '' }}</p>
                                    </div>
                                    <div class="card-footer bg-white border-0 pb-3">
                                        <small class="text-muted"><i class="bi bi-calendar3 me-2"></i>{{ \Carbon\Carbon::parse($item['created_at'] ?? now())->format('M d, Y') }}</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <div class="p-5 bg-white rounded shadow-sm">
                                    <i class="bi bi-cone-striped fs-1 text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted">Content Coming Soon</h5>
                                    <p class="mb-0 text-muted small">We are currently updating our extension services.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $extension->appends(request()->except(['extension_page']))->links() }}
                    </div>
                </div>
            </section>
        @endif

        <!-- Training Section -->
        @if($hasTraining)
            <section id="training" class="py-5 it-tab-section">
                <div class="container">
                    <div class="section-header mb-5 d-flex flex-column align-items-center text-center">
                        <span class="section-badge retro-label" style="background: #f59e0b; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">{{ $department->training_title ?: 'Training & Workshops' }}</span>
                        @if(!empty($department->training_body))
                            <div class="retro-section-text mt-4">{!! $department->training_body !!}</div>
                        @endif
                    </div>

                    <div class="row g-4 d-flex justify-content-center">
                        @forelse($training as $item)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 shadow-sm border-0 transition-hover">
                                    <div class="position-relative overflow-hidden" style="height: 200px;">
                                        @if(!empty($item['image']))
                                            <img src="{{ asset($item['image']) }}" class="card-img-top w-100 h-100 object-fit-cover" alt="{{ $item['title'] }}">
                                        @elseif(!empty($department->logo))
                                            <div class="d-flex align-items-center justify-content-center h-100 bg-white">
                                                <img src="{{ asset($department->logo) }}" class="img-fluid p-4" alt="Department Logo" style="max-height: 100%; object-fit: contain;">
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center justify-content-center h-100 bg-light text-muted">
                                                <i class="bi bi-easel fs-1"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold mb-3">{{ $item['title'] }}</h5>
                                        <p class="card-text text-muted small">{{ $item['description'] ?? '' }}</p>
                                    </div>
                                    <div class="card-footer bg-white border-0 pb-3">
                                        <small class="text-muted"><i class="bi bi-calendar3 me-2"></i>{{ \Carbon\Carbon::parse($item['created_at'] ?? now())->format('M d, Y') }}</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <div class="p-5 bg-light rounded border border-light">
                                    <i class="bi bi-hourglass-split fs-1 text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted">Stay Tuned</h5>
                                    <p class="mb-0 text-muted small">Training programs will be announced shortly.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                         {{ $training->appends(request()->except(['training_page']))->links() }}
                    </div>
                </div>
            </section>
        @endif

        <!-- Linkages Section -->
        @if($hasLinkages)
        <section id="linkages" class="py-5 bg-light it-tab-section">
            <div class="container">
                <div class="section-header mb-4 d-flex flex-column align-items-center text-center">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">{{ $department->linkages_title ?? 'Linkages' }}</span>
                    @if(!empty($department->linkages_body))
                        <div class="retro-section-text mt-4">
                            {!! $department->linkages_body !!}
                        </div>
                    @endif
                </div>

                @php
                    $localLinkages = $department->linkages->where('type', 'local');
                    $intlLinkages = $department->linkages->where('type', 'international');
                @endphp

                @if($localLinkages->count() > 0)
                    <div class="linkage-group mb-5">
                        <h3 class="h4 fw-bold mb-4 border-start border-4 border-danger ps-3">Local Linkages</h3>
                        <ul class="list-unstyled mb-0">
                            @foreach($localLinkages as $linkage)
                                <li class="d-flex align-items-center py-2 border-bottom">
                                    <span class="me-2 text-muted">&bull;</span>
                                    @if($linkage->url)
                                        <a href="{{ $linkage->url }}" target="_blank" class="fw-bold text-decoration-none" style="color: {{ $headerColor }};">{{ $linkage->name }}</a>
                                    @else
                                        <span class="fw-bold">{{ $linkage->name }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($intlLinkages->count() > 0)
                    <div class="linkage-group">
                        <h3 class="h4 fw-bold mb-4 border-start border-4 border-danger ps-3">International Linkages</h3>
                        <ul class="list-unstyled mb-0">
                            @foreach($intlLinkages as $linkage)
                                <li class="d-flex align-items-center py-2 border-bottom">
                                    <span class="me-2 text-muted">&bull;</span>
                                    @if($linkage->url)
                                        <a href="{{ $linkage->url }}" target="_blank" class="fw-bold text-decoration-none" style="color: {{ $headerColor }};">{{ $linkage->name }}</a>
                                    @else
                                        <span class="fw-bold">{{ $linkage->name }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </section>
        @endif

        <!-- Facilities Section -->
        @if($hasFacilities)
            <section id="facilities" class="py-5 it-tab-section">
                <div class="container">
                    @php
                        $facilitiesSectionTitle = $department->facilities_title ?: 'Learning Environments';
                        $facilitiesSectionBody = $department->facilities_body ?: '';
                    @endphp
                    <div class="section-header mb-5 d-flex flex-column align-items-center text-center">
                        <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Facilities & Resources</span>
                        <h2 class="retro-section-title mt-3">{{ $facilitiesSectionTitle }}</h2>
                        @if(!empty($facilitiesSectionBody))
                            <div class="retro-section-text mt-2">
                                {!! $facilitiesSectionBody !!}
                            </div>
                        @endif
                    </div>

                    <div class="row g-4 d-flex justify-content-center">
                        @foreach($facilities as $item)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 shadow-sm border-0 transition-hover">
                                    <div class="position-relative overflow-hidden" style="height: 200px;">
                                        @if(!empty($item['image']))
                                            <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($item['image']) }}" class="card-img-top w-100 h-100 object-fit-cover" alt="{{ $item['title'] }}">
                                        @elseif(!empty($department->logo))
                                            <div class="d-flex align-items-center justify-content-center h-100 bg-white">
                                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($department->logo) }}" class="img-fluid p-4" alt="Department Logo" style="max-height: 100%; object-fit: contain;">
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center justify-content-center h-100 bg-light text-muted">
                                                <i class="bi bi-building fs-1"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold mb-3">{{ $item['title'] }}</h5>
                                        <div class="card-text text-muted small">{!! $item['description'] ?? '' !!}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $facilities->appends(request()->except(['facilities_page']))->links() }}
                    </div>
                </div>
            </section>
        @endif

        <!-- Alumni Section -->
        @if($hasAlumni)
            <section id="alumni" class="py-5 bg-light it-tab-section">
                <div class="container">
                    <div class="section-header mb-5 d-flex flex-column align-items-center text-center">
                        <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">{{ $department->alumni_title ?? 'Notable Alumni' }}</span>
                        <h2 class="retro-section-title mt-3">{{ $department->alumni_title ?? 'Success Stories' }}</h2>
                        <div class="retro-section-text">
                            {!! $department->alumni_body ?? 'Read some personal stories about our alumni.' !!}
                        </div>
                    </div>

                    <div class="row g-4 d-flex justify-content-center">
                        @foreach($alumni as $item)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 shadow-sm border-0 transition-hover">
                                    <div class="text-center mt-4 pt-2">
                                        <div class="position-relative d-inline-block">
                                            @if(!empty($item['image']))
                                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($item['image']) }}" class="rounded-circle object-fit-cover shadow-sm" alt="{{ $item['title'] }}" style="width: 150px; height: 150px; border: 4px solid #fff;">
                                            @else
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 150px; height: 150px; border: 4px solid #fff;">
                                                    <i class="bi bi-person-badge fs-1 text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body text-center">
                                        <h5 class="card-title fw-bold mb-1">{{ $item['title'] }}</h5>
                                        @if(!empty($item['year_graduated']))
                                            <div class="mb-3">
                                                <span class="d-inline-block fw-bold" style="color: {{ $headerColor }}; border: 1px solid {{ $headerColor }}; padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.8rem; font-family: 'Libre Franklin', sans-serif;">
                                                    {{ $item['year_graduated'] }}
                                                </span>
                                            </div>
                                        @else
                                            <div class="mb-3"></div>
                                        @endif
                                        <div class="card-text text-muted small alumni-richtext">{!! $item['description'] ?? '' !!}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $alumni->appends(request()->except(['alumni_page']))->links() }}
                    </div>
                </div>
            </section>
        @endif

        <!-- Memberships Section -->
        @if($hasMemberships)
            <section id="memberships" class="py-5 it-tab-section">
                <div class="container">
                    <div class="section-header mb-5 d-flex flex-column align-items-center text-center">
                        <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Professional Memberships</span>
                        <h2 class="retro-section-title mt-3">{{ $membershipSectionTitle ?? 'Affiliations & Memberships' }}</h2>
                        <div class="retro-section-text">{!! $membershipSectionDescription ?? 'Our department\'s active involvement in professional organizations.' !!}</div>
                    </div>

                    <div class="row g-4 d-flex justify-content-center">
                        @foreach($memberships as $membership)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 shadow-sm border-0 transition-hover">
                                    <div class="accreditation-logo-wrap text-center p-4 mt-3" style="height: 160px; display: flex; align-items: center; justify-content: center;">
                                        @if($membership->logo)
                                            <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($membership->logo) }}" alt="{{ $membership->organization }}" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                                        @else
                                            <div class="w-100 h-100 bg-light rounded d-flex align-items-center justify-content-center">
                                                <i class="bi bi-award fs-1 text-muted opacity-50"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body text-center">
                                        <h5 class="card-title fw-bold mb-1">{{ $membership->organization }}</h5>
                                        <div class="small fw-600 mb-3" style="color: {{ $headerColor }}">
                                            {{ $membership->membership_type }}
                                        </div>
                                        @if($membership->description)
                                            <p class="card-text text-muted small">{{ $membership->description }}</p>
                                        @endif
                                        @if($membership->valid_until)
                                            <div class="mt-3 pt-3 border-top">
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar-check me-1"></i>
                                                    Valid until: {{ $membership->valid_until->format('M Y') }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- Student Organizations Section -->
        @if(($organizationsSectionVisible ?? true) && isset($organizations) && $organizations->isNotEmpty())
            <section id="organizations" class="py-5 it-tab-section bg-light">
                <div class="container">
                    <div class="section-header mb-5 d-flex flex-column align-items-center text-center">
                        <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Student Life</span>
                        <h2 class="retro-section-title mt-3">{{ $organizationsSectionTitle ?? 'Student Organizations' }}</h2>
                        <div class="retro-section-text">
                            {!! $organizationsSectionDescription ?? ('Get involved with ' . $department->name . ' student organizations.') !!}
                        </div>
                    </div>

                    <div class="row g-4 d-flex justify-content-center">
                        @foreach($organizations as $index => $org)
                            <div class="col-lg-4 col-md-6 mb-4 dept-org-card {{ $index >= 3 ? 'd-none' : '' }}">
                                <a href="{{ route('college.organization.show', ['college' => $collegeSlug, 'organization' => $org]) }}"
                                   class="text-decoration-none h-100 d-block" style="color: inherit;">
                                <div class="card h-100 shadow-sm border-0 transition-hover" style="cursor: pointer;">
                                    <div class="accreditation-logo-wrap text-center p-4 mt-3" style="height: 160px; display: flex; align-items: center; justify-content: center;">
                                        @if($org->logo)
                                            <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($org->logo) }}" alt="{{ $org->acronym ?? $org->name }}" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                                        @elseif(!empty($department->logo))
                                            <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($department->logo) }}" alt="{{ $department->name }} Logo" class="img-fluid opacity-50" style="max-height: 100%; object-fit: contain; filter: grayscale(100%);">
                                        @else
                                            <div class="w-100 h-100 bg-white rounded border d-flex align-items-center justify-content-center">
                                                <i class="bi bi-people fs-1 text-muted opacity-50"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body text-center">
                                        <h5 class="card-title fw-bold mb-1">{{ $org->name }}</h5>
                                        @if($org->acronym)
                                            <div class="small fw-600 mb-3" style="color: {{ $headerColor }}">
                                                {{ $org->acronym }}
                                            </div>
                                        @else
                                            <div class="mb-3"></div>
                                        @endif
                                        
                                        @if($org->description)
                                            <p class="card-text text-muted small">{{ Str::limit(strip_tags($org->description), 100) }}</p>
                                        @endif
                                        
                                        @if($org->adviser)
                                            <div class="mt-3 pt-3 border-top">
                                                <small class="text-muted">
                                                    <i class="bi bi-person me-1"></i>
                                                    Adviser: {{ $org->adviser }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-footer bg-white border-0 pb-3 text-center">
                                        <small style="color: {{ $headerColor }}; font-weight: 600;">
                                            View Organization <i class="bi bi-arrow-right ms-1"></i>
                                        </small>
                                    </div>
                                </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    @if($organizations->count() > 3)
                        <div class="text-center mt-4">
                            <button class="btn rounded-pill px-4 py-2 fw-bold" id="show-more-dept-orgs-btn" onclick="toggleDeptOrgs()" style="border: 2px solid {{ $accentColor }}; color: {{ $accentColor }}; background: transparent; transition: all 0.3s ease;" onmouseover="this.style.background='{{ $accentColor }}'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='{{ $accentColor }}';">
                                Show More Organizations <i class="bi bi-chevron-down ms-1"></i>
                            </button>
                        </div>
                        <script>
                            function toggleDeptOrgs() {
                                const btn = document.getElementById('show-more-dept-orgs-btn');
                                const hiddenOrgs = document.querySelectorAll('.dept-org-card.d-none');
                                const shownOrgs = document.querySelectorAll('.dept-org-card.shown-org');

                                if (hiddenOrgs.length > 0) {
                                    hiddenOrgs.forEach(org => {
                                        org.classList.remove('d-none');
                                        org.classList.add('shown-org');
                                    });
                                    btn.innerHTML = 'Show Less <i class="bi bi-chevron-up ms-1"></i>';
                                } else {
                                    shownOrgs.forEach(org => {
                                        org.classList.add('d-none');
                                        org.classList.remove('shown-org');
                                    });
                                    btn.innerHTML = 'Show More Organizations <i class="bi bi-chevron-down ms-1"></i>';
                                }
                            }
                        </script>
                    @endif
                </div>
            </section>
        @endif

        <!-- Curriculum Snapshot -->
        @if($hasObjectives)
        <section id="curriculum" class="py-5">
            <div class="container">
                @php
                    $curriculum = $department->curricula ?? [];
                @endphp

                @if(collect($curriculum)->isEmpty())
                    <div class="text-center py-5">
                        <span class="d-block display-1 mb-3">🚧</span>
                        <h3 class="fw-bold mb-3">This Page Is Coming Soon</h3>
                        <p class="lead text-muted mb-0">We’re still working on this section. Check back soon!</p>
                    </div>
                @else
                    <div class="section-header mb-4 d-flex flex-column align-items-center text-center">
                        <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Curriculum</span>
                        <h2 class="retro-section-title mt-3">Sample Courses</h2>
                        <p class="retro-section-text">A selection of key courses students may take in the {{ $department->name }} programs.</p>
                    </div>

                    <div class="row gy-4">
                        @foreach($curriculum as $category)
                            <div class="col-md-6 col-lg-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold">{{ $category['title'] }}</h5>
                                        @if(!empty($category['courses']))
                                            <div class="courses-list mt-3">
                                                {!! $category['courses'] !!}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <p class="mb-1"><strong>Note:</strong> For the official and updated curriculum, please refer to the College of Engineering or the University Registrar.</p>
                    </div>
                @endif
            </div>
        </section>
        @endif
    </main>

    @include('includes.college-footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>

    <script>
        // Toggle sticky header state
        document.addEventListener('scroll', function() {
            const wrapper = document.querySelector('.engineering-header-wrapper');
            if (wrapper) {
                if (window.scrollY > 50) {
                    wrapper.classList.add('engineering-header-scrolled');
                } else {
                    wrapper.classList.remove('engineering-header-scrolled');
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const menu = document.querySelector('.it-section-menu-inner');
            const tabs = Array.from(document.querySelectorAll('.it-tab-section'));
            const graduateSection = document.getElementById('graduate-outcomes');
            const programsSection = document.getElementById('programs');
            const curriculumSection = document.getElementById('curriculum');

            function setActive(tabId) {
                tabs.forEach((section) => section.classList.remove('is-active'));
                menu.querySelectorAll('a').forEach((a) => a.classList.remove('is-active'));

                const target = document.getElementById(tabId);
                if (target) target.classList.add('is-active');

                // Handle linked sections:
                // - Overview: show Graduate Outcomes (if inside same tab), hide Curriculum
                // - Objectives: show Curriculum only
                // - Others: hide Graduate Outcomes and Curriculum
                if (tabId === 'program-overview') {
                    if (graduateSection) graduateSection.style.display = '';
                    if (curriculumSection) curriculumSection.style.display = 'none';
                } else if (tabId === 'objectives') {
                    if (graduateSection) graduateSection.style.display = 'none';
                    if (curriculumSection) curriculumSection.style.display = '';
                } else {
                    if (graduateSection) graduateSection.style.display = 'none';
                    if (curriculumSection) curriculumSection.style.display = 'none';
                }

                const activeLink = menu.querySelector(`a[data-tab="${tabId}"]`);
                if (activeLink) activeLink.classList.add('is-active');

                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }

                if (history.replaceState) {
                    history.replaceState(null, '', `#${tabId}`);
                }
            }

            // Linkage Modal Logic
            const linkageModalEl = document.getElementById('linkageModal');
            if (linkageModalEl) {
                const linkageModal = new bootstrap.Modal(linkageModalEl);
                const iframe = document.getElementById('linkageIframe');
                const loader = document.getElementById('linkageIframeLoader');
                const errorDiv = document.getElementById('linkageIframeError');
                const modalTitle = document.getElementById('linkageModalLabel');
                const externalLink = document.getElementById('linkageExternalLink');
                const errorExternalLink = document.getElementById('linkageErrorExternalLink');

                document.querySelectorAll('.view-linkage-website').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const url = this.getAttribute('data-url');
                        const name = this.getAttribute('data-name');
                        
                        modalTitle.textContent = name;
                        externalLink.href = url;
                        errorExternalLink.href = url;
                        
                        iframe.classList.add('d-none');
                        errorDiv.classList.add('d-none');
                        loader.classList.remove('d-none');
                        
                        iframe.src = url;
                        linkageModal.show();
                    });
                });

                iframe.addEventListener('load', function() {
                    if (this.src === '' || this.src.includes('about:blank')) return;
                    loader.classList.add('d-none');
                    iframe.classList.remove('d-none');
                });

                linkageModalEl.addEventListener('hidden.bs.modal', function () {
                    iframe.src = 'about:blank';
                });
            }

            // Default active link on load
            setActive((location.hash || '#program-overview').replace('#', '') || 'program-overview');

            menu.addEventListener('click', function (e) {
                const link = e.target.closest('a[data-tab]');
                if (!link) return;
                e.preventDefault();
                setActive(link.dataset.tab);
            });
        });
    </script>

    <!-- Linkage Iframe Modal -->
    <div class="modal fade" id="linkageModal" tabindex="-1" aria-labelledby="linkageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 90%;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                <div class="modal-header bg-dark text-white border-0 py-3">
                    <h5 class="modal-title fw-bold" id="linkageModalLabel">Partner Website</h5>
                    <div class="ms-auto d-flex align-items-center gap-3">
                        <a href="#" id="linkageExternalLink" target="_blank" class="text-white text-decoration-none small opacity-75">
                            Open in New Tab <i class="fas fa-external-link-alt ms-1"></i>
                        </a>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-0 bg-light" style="height: 80vh;">
                    <div id="linkageIframeContainer" class="w-100 h-100 position-relative">
                        <div id="linkageIframeLoader" class="position-absolute top-50 start-50 translate-middle text-center">
                            <div class="spinner-border text-danger" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted small">Connecting...</p>
                        </div>
                        <iframe id="linkageIframe" src="" class="w-100 h-100 border-0 d-none" style="background: white;"></iframe>
                        <div id="linkageIframeError" class="w-100 h-100 d-none flex-column align-items-center justify-content-center text-center p-4">
                            <i class="fas fa-shield-alt text-danger display-4 mb-3"></i>
                            <h4 class="fw-bold">Content Restricted</h4>
                            <p class="text-muted">This website prevents embedding. Please open it in a new tab instead.</p>
                            <a href="#" id="linkageErrorExternalLink" target="_blank" class="btn btn-danger rounded-pill px-4 mt-2">
                                Visit Website <i class="fas fa-external-link-alt ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

