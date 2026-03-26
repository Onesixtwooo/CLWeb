<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $institute->name }} - {{ $collegeName }}, {{ config('app.name', 'CLSU') }}">
    <title>{{ $institute->name }} - {{ config('app.name', 'CLSU') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Buttershine+Serif:wght@400;700&family=Libre+Franklin:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/college.css', 'resources/js/app.js'])

    <style>
        :root {
            --college-header-color: {{ $headerColor }};
            --college-accent-color: {{ $accentColor }};
            --college-header-gradient: {{ $headerColor }};
        }

        /* Dynamic college header styles */
        .engineering-top-header {
            background: {{ $accentColor }} !important;
        }
        .engineering-header {
            background: {{ $headerColor }};
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
                $bannerSection = $institute->getSection('overview');
                $bannerImages = $bannerSection['banner_images'] ?? [];
                $bannerImage = $bannerSection['banner_image'] ?? null;

                // Fallback to single image if array is empty but single image exists
                if (empty($bannerImages) && $bannerImage) {
                    $bannerImages[] = $bannerImage;
                }
            @endphp

            @if(empty($bannerImages))
                background: {{ $headerColor }};
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
            object-fit: cover;
            object-position: center;
            width: 100%;
            height: 100%;
        }

        .it-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 1; /* Above carousel, below content */
            @if(!empty($bannerImages))
            background: linear-gradient(to right, rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.45));
            @else
            background: rgba(0, 0, 0, 0.1);
            @endif
        }

        .it-hero-inner {
            position: relative;
            z-index: 2; /* Above overlay */
            max-width: 820px;
            margin: 0 auto;
            text-align: center;
        }

        .it-hero-title {
            font-size: clamp(2.4rem, 4vw, 3.2rem);
            font-weight: 800;
            line-height: 1.1;
            color: #ffdd55;
            margin-bottom: 1rem;
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

        .it-section-menu a:hover, .it-section-menu a.is-active {
            background: {{ $headerColor }};
            border-color: {{ $headerColor }};
            color: #ffffff;
        }

        .it-tab-section {
            display: none;
        }

        .it-tab-section.is-active {
            display: block;
        }
    </style>
</head>
<body class="retro-modern college-page" style="padding-top: 130px;">
    <div class="engineering-header-wrapper">
    @include('partials.college-top-header')
    <div class="engineering-nav-outer">
    <header class="header engineering-header">
        <nav class="navbar navbar-expand-md navbar-dark engineering-navbar">
            <div class="container">
                <a href="{{ route('college.show', ['college' => $collegeSlug]) }}" class="navbar-brand d-flex align-items-center gap-2 logo">
                    <div class="logo-box retro-badge">
                        <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($collegeLogoUrl) }}" alt="{{ $collegeName }} logo" class="logo-image">
                    </div>
                    <div class="logo-text d-flex flex-column">
                        <h2 class="retro-heading mb-0">
                            <span class="logo-full-text d-none d-md-inline">{{ strtoupper($collegeName) }}</span>
                            <span class="logo-short-text d-inline d-md-none">{{ $collegeShortName }}</span>
                        </h2>
                        <p class="retro-subtitle">
                            <span class="d-inline d-md-none">CLSU</span>
                            <span class="d-none d-md-inline">{{ $institute->name }}</span>
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
                            <a href="{{ route('college.show', ['college' => $collegeSlug]) }}#institutes" class="nav-link">{{ $institutesSectionTitle ?? 'Institutes' }}</a>
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
                       {{ $institute->name }}
                    </h1>
                    @if($institute->details)
                    <p class="it-hero-subtitle mb-4">
                        {{ $institute->details }}
                    </p>
                    @endif
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a href="#program-overview" class="btn btn-light btn-sm">Explore {{ $institutesSectionTitle ?? 'Institute' }}</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section Menu -->
        @php
            // Remap Objectives -> Goals
            $goals = $institute->goals;
            $hasGoals = $goals->count() > 0;
            
            $hasHistory = !empty($institute->history);
            
            // Remap Faculty -> Staff
            $staff = $institute->staff;
            $hasStaff = $staff->count() > 0;

            $hasResearch = $institute->research_is_visible && $research->total() > 0;
            $hasExtension = $institute->extension_is_visible && $extension->total() > 0;
            $hasFacilities = $institute->facilities_is_visible && $facilities->total() > 0;
            
            // These might still be used or we hide them?
            // User asked for: Goals, History, Staff, Research, Extension, Facilities
            // Overview is likely still needed as the landing.
            // Programs, Awards, Training, Alumni - User didn't mention them. Hidden? 
            // Let's keep them if they have content, but prioritize user list.
            
            $hasOverview = !empty($institute->overview_body) || ($institute->outcomes->count() > 0);
            
            // Legacy sections check
            $hasPrograms = $institute->programs_is_visible && $institute->programs->count() > 0;
            $hasAwards = $institute->awards_is_visible && $awards->total() > 0;
            $hasTraining = $institute->training_is_visible && $training->total() > 0;
            $hasAlumni = $institute->alumni_is_visible && $alumni->total() > 0;

            $anyButtonShown = $hasOverview || $hasGoals || $hasHistory || $hasStaff || 
                               $hasResearch || $hasExtension || $hasFacilities ||
                               $hasPrograms || $hasAwards || $hasTraining || $hasAlumni;

            // Determine default active tab using priority order
            $activeTab = 'program-overview';
            $tabPriority = [
                'program-overview' => $hasOverview,
                'goals'            => $hasGoals,
                'history'          => $hasHistory,
                'staff'            => $hasStaff,
                'research'         => $hasResearch,
                'extension'        => $hasExtension,
                'facilities'       => $hasFacilities,
                'programs'         => $hasPrograms,
                'awards'           => $hasAwards,
                'training'         => $hasTraining,
                'alumni'           => $hasAlumni,
            ];

            foreach ($tabPriority as $tabId => $hasContent) {
                if ($hasContent) {
                    $activeTab = $tabId;
                    break;
                }
            }
        @endphp

        @if($anyButtonShown)
        <section class="it-section-menu">
            <div class="container">
                <nav class="it-section-menu-inner" aria-label="{{ $institutesSectionTitle ?? 'Institute' }} sections">
                    @if($hasOverview) <a href="#program-overview" data-tab="program-overview">Overview</a> @endif
                    {{-- User Requested Order: Goals, History, Staff, Research, Extension, Facilities --}}
                    @if($hasGoals) <a href="#goals" data-tab="goals">Goals</a> @endif
                    @if($hasHistory) <a href="#history" data-tab="history">History</a> @endif
                    @if($hasStaff) <a href="#staff" data-tab="staff">Staff</a> @endif
                    @if($hasResearch) <a href="#research" data-tab="research">Research</a> @endif
                    @if($hasExtension) <a href="#extension" data-tab="extension">Extension</a> @endif
                    @if($hasFacilities) <a href="#facilities" data-tab="facilities">Facilities</a> @endif
                    
                    {{-- Other sections --}}
                    @if($hasPrograms) <a href="#programs" data-tab="programs">Programs</a> @endif
                    @if($hasAwards) <a href="#awards" data-tab="awards">Awards</a> @endif
                    @if($hasTraining) <a href="#training" data-tab="training">Training</a> @endif
                    @if($hasAlumni) <a href="#alumni" data-tab="alumni">Alumni</a> @endif
                </nav>
            </div>
        </section>
        @endif

        <!-- Overview Section -->
        <section id="program-overview" class="py-5 it-tab-section {{ $activeTab === 'program-overview' ? 'is-active' : '' }}">
            <div class="container">
                @php
                    $overviewTitle = $institute->overview_title ?? 'Overview';
                    $overviewBody = $institute->overview_body;
                    $hasContent = !empty($overviewBody) || ($institute->outcomes->count() > 0);
                @endphp

                @if(!$anyButtonShown)
                    <div class="text-center py-5">
                        <span class="d-block display-1 mb-3">🚧</span>
                        <h3 class="fw-bold mb-3">This Page Is Coming Soon</h3>
                        <p class="lead text-muted mb-0">We’re still working on this section. Check back soon!</p>
                    </div>
                @elseif(!$hasContent)
                     <div class="text-center py-5">
                        <p class="text-muted fst-italic">No overview content available.</p>
                    </div>
                @else
                    @if(!empty($overviewBody))
                    <div class="row align-items-start gy-4 mb-5">
                        <div class="col-lg-7">
                            <span class="section-badge retro-label mb-4 d-inline-block" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; letter-spacing: 2px; width: fit-content;">{{ $overviewTitle }}</span>
                            <div class="retro-section-text mb-3" style="font-size: 1rem; line-height: 1.6;">
                                {!! nl2br(e($overviewBody)) !!}
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="ratio ratio-4x3 rounded overflow-hidden shadow-sm bg-light d-flex align-items-center justify-content-center">
                                @if(!empty($institute->logo))
                                    <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($institute->logo) }}" alt="{{ $institute->name }} Logo" class="w-100 h-100" style="object-fit: contain; padding: 1rem;">
                                @else
                                    <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl(\App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp')) }}" alt="CLSU Logo" class="w-100 h-100" style="object-fit: contain; padding: 1rem;">
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <div id="graduate-outcomes">
                        @if($institute->outcomes->count() > 0)
                            <div class="mt-5 mb-4 d-flex flex-column align-items-center text-center">
                                 <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Outcomes</span>
                            </div>
                        <div class="row g-4 justify-content-center">
                            @foreach($institute->outcomes as $outcome)
                                <div class="col-lg-4 col-md-6">
                                    <div class="card border-0 shadow-sm overflow-hidden h-100">
                                        <div class="ratio ratio-16x9">
                                            @if($outcome->image)
                                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($outcome->image) }}" class="card-img-top object-fit-cover" alt="{{ $outcome->title }}">
                                            @elseif(!empty($institute->logo))
                                                <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($institute->logo) }}" class="card-img-top object-fit-contain p-3" alt="{{ $outcome->title }}">
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
                                                    {!! nl2br(e($outcome->description)) !!}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                @endif
            </div>
        </section>

        <!-- Goals Section (formerly Objectives) -->
        <section id="goals" class="py-5 bg-light it-tab-section {{ $activeTab === 'goals' ? 'is-active' : '' }}">
            <div class="container">
                <div class="section-header mb-4 d-flex flex-column align-items-center text-center">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Goals</span>
                </div>
                <div class="row">
                    <div class="col-lg-10 mx-auto text-center">
                        @if($hasGoals)
                            <ul class="list-unstyled mb-0 d-inline-block text-start" style="font-size: 1rem; line-height: 1.8;">
                                @foreach($goals as $index => $goal)
                                    <li class="mb-3 d-flex align-items-start">
                                        <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0 me-3 shadow-sm" style="width: 32px; height: 32px; background: {{ $headerColor }}; color: white; font-weight: bold; border-radius: 4px;">{{ $index + 1 }}</span>
                                        <div>{{ $goal->content }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">No goals available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- History Section -->
        @if($hasHistory)
        <section id="history" class="py-5 it-tab-section {{ $activeTab === 'history' ? 'is-active' : '' }}">
            <div class="container">
                <div class="section-header mb-5 d-flex flex-column align-items-center text-center">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">History</span>
                </div>
                @if(!empty($institute->logo))
                <div class="row align-items-center gy-4">
                    <div class="col-lg-7">
                        <div class="retro-section-text text-start" style="font-size: 1rem; line-height: 1.8;">
                             {!! nl2br(e($institute->history)) !!}
                        </div>
                    </div>
                    <div class="col-lg-5">
                         <div class="ratio ratio-4x3 bg-light rounded shadow-sm border d-flex align-items-center justify-content-center overflow-hidden">
                            <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($institute->logo) }}" alt="Institute History" class="img-fluid p-4" style="max-height: 100%; object-fit: contain; opacity: 0.8;">
                         </div>
                    </div>
                </div>
                @else
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="retro-section-text" style="font-size: 1rem; line-height: 1.8;">
                             {!! nl2br(e($institute->history)) !!}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </section>
        @endif

        <!-- Staff Section -->
        @if($hasStaff)
        <section id="staff" class="py-5 bg-light it-tab-section {{ $activeTab === 'staff' ? 'is-active' : '' }}">
            <div class="container">
                <div class="section-header mb-4 d-flex flex-column align-items-center text-center">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Staff</span>
                </div>
                <div class="row g-4 justify-content-center">
                     @foreach($staff as $member)
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                                <div class="bg-white p-4 d-flex align-items-center justify-content-center">
                                    <div style="width: 120px; height: 120px;">
                                        @if($member->photo)
                                            <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($member->photo) }}" class="rounded-circle w-100 h-100 object-fit-cover shadow-sm border" alt="{{ $member->name }}">
                                        @elseif(!empty($institute->logo))
                                            <div class="rounded-circle w-100 h-100 bg-white d-flex align-items-center justify-content-center border shadow-sm p-3">
                                                <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($institute->logo) }}" class="img-fluid" style="max-height: 100%; object-fit: contain;" alt="{{ $institute->name }}">
                                            </div>
                                        @else
                                            <div class="rounded-circle w-100 h-100 bg-light d-flex align-items-center justify-content-center border">
                                                <i class="bi bi-person fs-1 text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body text-center d-flex flex-column justify-content-center p-3" style="background-color: {{ $headerColor }};">
                                    <h6 class="fw-bold mb-1 text-white">{{ $member->name }}</h6>
                                    <p class="small mb-0 text-white opacity-75">{{ $member->position }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- Programs Section --}}
        @if($hasPrograms)
        <section id="programs" class="py-5 it-tab-section {{ $activeTab === 'programs' ? 'is-active' : '' }}">
            <div class="container">
                <div class="mt-4 mb-5 text-center">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Programs</span>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach($institute->programs as $program)
                        <div class="col-lg-4 col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                @if($program->image)
                                    <div class="ratio ratio-16x9"><img src="{{ asset($program->image) }}" class="card-img-top object-fit-cover" alt="{{ $program->title }}"></div>
                                @endif
                                <div class="card-body p-4 {{ !$program->image ? 'text-center' : '' }}">
                                    <h4 class="fw-bold mb-3 text-dark">{{ $program->title }}</h4>
                                    @if($program->description) <div class="text-muted small mb-0">{!! nl2br(e($program->description)) !!}</div> @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        @if($hasAwards)
        <section id="awards" class="py-5 bg-light it-tab-section {{ $activeTab === 'awards' ? 'is-active' : '' }}">
            <div class="container">
                <div class="section-header mb-4 d-flex flex-column align-items-center text-center">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Awards</span>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach($awards as $award)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="ratio ratio-16x9 bg-secondary bg-opacity-10">
                                    @if(!empty($award['image'])) <img src="{{ asset($award['image']) }}" class="object-fit-cover" alt="{{ $award['title'] }}">
                                    @elseif(!empty($institute->logo)) <div class="d-flex align-items-center justify-content-center h-100 bg-white"><img src="{{ asset($institute->logo) }}" class="img-fluid p-4" style="max-height: 100%; object-fit: contain;"></div>
                                    @else <div class="d-flex align-items-center justify-content-center h-100 text-muted"><i class="bi bi-trophy fs-3"></i></div> @endif
                                </div>
                                <div class="card-body"><h5 class="card-title fw-bold mb-2">{{ $award['title'] }}</h5><p class="card-text text-muted small">{{ $award['description'] ?? '' }}</p></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 d-flex justify-content-center">{{ $awards->links() }}</div>
            </div>
        </section>
        @endif

        @if($hasResearch)
        <section id="research" class="py-5 it-tab-section {{ $activeTab === 'research' ? 'is-active' : '' }}">
            <div class="container">
                <div class="section-header mb-4 d-flex flex-column align-items-center text-center">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Research</span>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach($research as $item)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="ratio ratio-16x9 bg-secondary bg-opacity-10">
                                    @if(!empty($item->image)) <img src="{{ asset($item->image) }}" class="object-fit-cover" alt="{{ $item->title }}">
                                    @elseif(!empty($institute->logo)) <div class="d-flex align-items-center justify-content-center h-100 bg-white"><img src="{{ asset($institute->logo) }}" class="img-fluid p-4" style="max-height: 100%; object-fit: contain;"></div>
                                    @else <div class="d-flex align-items-center justify-content-center h-100 text-muted"><i class="bi bi-journal-text fs-3"></i></div> @endif
                                </div>
                                <div class="card-body"><h5 class="card-title fw-bold mb-2">{{ $item->title }}</h5><p class="card-text text-muted small">{{ $item->description ?? '' }}</p></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 d-flex justify-content-center">{{ $research->appends(request()->except('research_page'))->links() }}</div>
            </div>
        </section>
        @endif

        @if($hasExtension)
        <section id="extension" class="py-5 bg-light it-tab-section {{ $activeTab === 'extension' ? 'is-active' : '' }}">
            <div class="container">
                <div class="section-header mb-4 d-flex flex-column align-items-center text-center">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Extension</span>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach($extension as $item)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="ratio ratio-16x9 bg-secondary bg-opacity-10">
                                    @if(!empty($item->image)) <img src="{{ asset($item->image) }}" class="object-fit-cover" alt="{{ $item->title }}">
                                    @elseif(!empty($institute->logo)) <div class="d-flex align-items-center justify-content-center h-100 bg-white"><img src="{{ asset($institute->logo) }}" class="img-fluid p-4" style="max-height: 100%; object-fit: contain;"></div>
                                    @else <div class="d-flex align-items-center justify-content-center h-100 text-muted"><i class="bi bi-people fs-3"></i></div> @endif
                                </div>
                                <div class="card-body"><h5 class="card-title fw-bold mb-2">{{ $item->title }}</h5><p class="card-text text-muted small">{{ $item->description ?? '' }}</p></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 d-flex justify-content-center">{{ $extension->appends(request()->except('extension_page'))->links() }}</div>
            </div>
        </section>
        @endif

        @if($hasTraining)
        <section id="training" class="py-5 it-tab-section {{ $activeTab === 'training' ? 'is-active' : '' }}">
            <div class="container">
                <div class="section-header mb-4 d-flex flex-column align-items-center text-center">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Training</span>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach($training as $item)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="ratio ratio-16x9 bg-secondary bg-opacity-10">
                                    @if(!empty($item['image'])) <img src="{{ asset($item['image']) }}" class="object-fit-cover" alt="{{ $item['title'] }}">
                                    @elseif(!empty($institute->logo)) <div class="d-flex align-items-center justify-content-center h-100 bg-white"><img src="{{ asset($institute->logo) }}" class="img-fluid p-4" style="max-height: 100%; object-fit: contain;"></div>
                                    @else <div class="d-flex align-items-center justify-content-center h-100 text-muted"><i class="bi bi-easel fs-3"></i></div> @endif
                                </div>
                                <div class="card-body"><h5 class="card-title fw-bold mb-2">{{ $item['title'] }}</h5><p class="card-text text-muted small">{{ $item['description'] ?? '' }}</p></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 d-flex justify-content-center">{{ $training->appends(request()->except('training_page'))->links() }}</div>
            </div>
        </section>
        @endif

        @if($hasFacilities)
        <section id="facilities" class="py-5 it-tab-section {{ $activeTab === 'facilities' ? 'is-active' : '' }}">
            <div class="container">
                <div class="section-header mb-4 d-flex flex-column align-items-center text-center">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Facilities</span>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach($facilities as $item)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100 shadow-hover">
                                <div class="ratio ratio-16x9">
                                    @if(!empty($item->image)) <img src="{{ asset($item->image) }}" class="card-img-top object-fit-cover" alt="{{ $item->title }}">
                                    @elseif(!empty($institute->logo)) <div class="d-flex align-items-center justify-content-center h-100 bg-white p-3"><img src="{{ asset($institute->logo) }}" class="img-fluid" style="max-height: 100%; object-fit: contain;"></div>
                                    @else <div class="d-flex align-items-center justify-content-center h-100 bg-light"><i class="bi bi-building fs-1 text-muted"></i></div> @endif
                                </div>
                                <div class="card-body"><h5 class="card-title fw-bold mb-2">{{ $item->title }}</h5><p class="card-text text-muted small">{{ $item->description ?? '' }}</p></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 d-flex justify-content-center">{{ $facilities->appends(request()->except('facilities_page'))->links() }}</div>
            </div>
        </section>
        @endif

        @if($hasAlumni)
        <section id="alumni" class="py-5 bg-light it-tab-section {{ $activeTab === 'alumni' ? 'is-active' : '' }}">
            <div class="container">
                <div class="section-header mb-4 d-flex flex-column align-items-center text-center">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Alumni</span>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach($alumni as $item)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100 text-center p-4">
                                <div class="mb-3">
                                    @if(!empty($item['image'])) <img src="{{ asset($item['image']) }}" class="rounded-circle object-fit-cover shadow-sm" style="width: 120px; height: 120px; border: 4px solid #fff;">
                                    @else <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px;"><i class="bi bi-person fs-1 text-muted"></i></div> @endif
                                </div>
                                <h5 class="fw-bold mb-1">{{ $item['title'] }}</h5>
                                @if(!empty($item['year_graduated'])) <div class="mb-2"><span class="badge bg-secondary">{{ $item['year_graduated'] }}</span></div> @endif
                                <p class="text-muted small mb-0">{{ $item['description'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 d-flex justify-content-center">{{ $alumni->appends(request()->except('alumni_page'))->links() }}</div>
            </div>
        </section>
        @endif

    </main>

    @include('includes.college-footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const menu = document.querySelector('.it-section-menu-inner');
                const tabs = Array.from(document.querySelectorAll('.it-tab-section'));
                const defaultTab = '{{ $activeTab }}';

                // Get all valid tab IDs from the menu links if menu exists
                const validTabs = menu 
                    ? Array.from(menu.querySelectorAll('a[data-tab]')).map(a => a.getAttribute('data-tab')) 
                    : [];

                function setActive(tabId) {
                    let targetId = tabId;

                    // If we have a menu and the requested tab isn't in it, look for a valid fallback
                    // This handles cases where a hash points to a hidden/empty section (e.g. #program-overview when overview is empty)
                    if (validTabs.length > 0 && !validTabs.includes(targetId)) {
                         targetId = defaultTab;
                    }

                    tabs.forEach((section) => section.classList.remove('is-active'));
                    if(menu) menu.querySelectorAll('a').forEach((a) => a.classList.remove('is-active'));

                    const target = document.getElementById(targetId);
                    if (target) target.classList.add('is-active');

                    if(menu) {
                        const activeLink = menu.querySelector(`a[data-tab="${targetId}"]`);
                        if (activeLink) activeLink.classList.add('is-active');
                    }
                }

                // Initial load: Check hash, fallback to PHP calculated default
                let initialTab = (location.hash || '').replace('#', '');
                if (!initialTab) initialTab = defaultTab;
                
                setActive(initialTab);

                if(menu) {
                    menu.addEventListener('click', function (e) {
                        const link = e.target.closest('a[data-tab]');
                        if (!link) return;
                        e.preventDefault();
                        const newTab = link.dataset.tab;
                        setActive(newTab);
                        // Update URL hash without jumping
                        history.pushState(null, null, '#' + newTab);
                    });
                }
            });
        </script>
</body>
</html>

