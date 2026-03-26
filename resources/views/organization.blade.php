<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $organization->name }} - {{ $collegeName }}, {{ config('app.name', 'CLSU') }}">
    <title>{{ $organization->name }}{{ $organization->acronym ? ' (' . $organization->acronym . ')' : '' }} - {{ $department?->name ?? $collegeName }} - {{ config('app.name', 'CLSU') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Buttershine+Serif:wght@400;700&family=Libre+Franklin:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/college.css', 'resources/js/app.js'])

    @include('includes.college-css')

    <style>
        :root {
            --college-header-color: {{ $headerColor }};
            --college-accent-color: {{ $accentColor }};
            --college-header-gradient: {{ $headerColor }};
        }

        /* Reuse header styles from department page */
        .engineering-top-header,
        .engineering-header,
        .engineering-navbar {
            background: {{ $headerColor }} !important;
        }

        .engineering-navbar .nav-link { color: rgba(255, 255, 255, 0.85); }
        .engineering-navbar .nav-link:hover,
        .engineering-navbar .nav-link:focus,
        .engineering-navbar .nav-link:active,
        .engineering-navbar .nav-link.show,
        .engineering-navbar .show > .nav-link { color: #ffffff; background-color: transparent !important; }

        /* Hero - facilities style */
        .it-hero {
            padding: 15rem 0 8rem 0;
            color: white;
            text-align: center;
            background-size: cover;
            background-position: center;
        }
        .it-hero-title {
            font-size: clamp(2rem, 5vw, 4rem);
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.75rem;
        }


        /* Section Menu */
        .it-section-menu {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 35px;
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
        .it-section-menu a.is-active {
            background: {{ $headerColor }};
            border-color: {{ $headerColor }};
            color: #ffffff;
        }

        /* Tabbed sections */
        .it-tab-section { display: none; }
        .it-tab-section.is-active { display: block; }

        /* Org logo hero card */
        .org-hero-logo-wrap {
            display: flex; align-items: center; justify-content: center;
            background: #fff; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            padding: 1.5rem; width: 180px; height: 180px; margin: 0 auto 1.5rem;
        }
        .org-hero-logo { max-width: 100%; max-height: 100%; object-fit: contain; }
        .org-hero-badge {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 999px;
            padding: 0.25rem 0.9rem;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 0.75rem;
        }

        /* Members / Officers card */
        .member-card {
            background: {{ $headerColor }};
            border-radius: 12px;
            overflow: hidden;
            border: 0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .member-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        }
        .member-card-photo-wrap {
            background: #fff;
            padding: 2rem 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .member-card-photo {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255,255,255,0.2);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            background-color: #f3f4f6;
        }
        .member-card-body {
            padding: 1.5rem;
            color: #ffffff;
            flex: 1;
            display: flex;
            flex-direction: column;
            text-align: left;
        }
        .member-card-name {
            font-weight: 700;
            color: #ffffff;
            font-size: 1.125rem;
            margin-bottom: 0.25rem;
            line-height: 1.2;
        }
        .member-card-role {
            color: rgba(255,255,255,0.9);
            font-size: 0.875rem;
            margin-bottom: 0;
            font-weight: 500;
        }
        .member-section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.75rem;
            flex-wrap: wrap;
        }
        .member-section-count {
            background: color-mix(in srgb, {{ $headerColor }} 10%, white);
            color: {{ $headerColor }};
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.25rem 0.9rem;
            border-radius: 999px;
        }
        .member-section-line {
            flex: 1;
            height: 1px;
            min-width: 120px;
            background: color-mix(in srgb, {{ $headerColor }} 20%, transparent);
        }

        /* Section badge */
        .org-section-badge {
            display: inline-block;
            background: var(--college-header-gradient);
            color: #ffffff;
            padding: 0.5rem 1.25rem;
            letter-spacing: 2px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            font-family: 'Libre Franklin', sans-serif;
            width: fit-content;
        }

        /* Album hover effects */
        .group-hover-scale-110 {
            transition: transform 0.5s ease;
        }
        .group:hover .group-hover-scale-110 {
            transform: scale(1.1);
        }
        .hover-bg-light:hover {
            background-color: #f8f9fa;
        }
        .transition-all {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="retro-modern college-page">
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
                            <span class="logo-short-text d-inline d-md-none">{{ $collegeShortName ?? '' }}</span>
                        </h2>
                        <p class="retro-subtitle">
                            <span class="d-none d-md-inline">{{ $department?->name ?? $collegeName }}</span>
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
                        @php
                            $departmentLink = null;
                            if (! empty($department)) {
                                $departmentLink = route('college.department.show', ['college' => $collegeSlug, 'department' => $department]) . '#organizations';
                            }
                        @endphp
                        @if ($departmentLink)
                        <li class="nav-item">
                            <a href="{{ $departmentLink }}" class="nav-link">
                                {{ $department->name ?? 'Department' }}
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a href="{{ route('college.organizations', ['college' => $collegeSlug]) }}" class="nav-link">Organizations</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    </div>
    </div>

    <main>
        @php
            $heroLogoUrl = !empty($organization->logo)
                ? (str_starts_with($organization->logo, 'http') ? $organization->logo : asset($organization->logo))
                : null;
        @endphp
        <!-- Hero - facilities style -->
        <section class="it-hero" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ \App\Providers\AppServiceProvider::resolveImageUrl($heroLogoUrl ?? $collegeLogoUrl) }}'); background-size: cover; background-position: center;">
            <div class="container">
                @if ($department)
                    <h5 class="text-uppercase mb-3 fw-bold text-white text-decoration-underline" style="letter-spacing: 1px;">
                        {{ $department->name }}
                    </h5>
                @elseif (!empty($organization->acronym))
                    <h5 class="text-uppercase mb-3 fw-bold text-white text-decoration-underline" style="letter-spacing: 1px;">
                        {{ $organization->acronym }}
                    </h5>
                @endif

                <h1 class="it-hero-title">{{ $organization->name }}</h1>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item">
                            <a href="{{ route('college.show', ['college' => $collegeSlug]) }}" class="text-white text-decoration-none">Home</a>
                        </li>
                        @php
                            $departmentLink = null;
                            if (! empty($department)) {
                                $departmentLink = route('college.department.show', ['college' => $collegeSlug, 'department' => $department]) . '#organizations';
                            }
                        @endphp
                        @if ($departmentLink)
                            <li class="breadcrumb-item text-white-50">
                                <a href="{{ $departmentLink }}" class="text-white-50 text-decoration-none">{{ $department->name ?? 'Department' }}</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item text-white-50" aria-current="page">Organizations</li>
                        <li class="breadcrumb-item active text-white" aria-current="page">{{ $organization->name }}</li>
                    </ol>
                </nav>
            </div>
        </section>


        <!-- Section Menu -->
        <section class="it-section-menu">
            <div class="container">
                <nav class="it-section-menu-inner" aria-label="{{ $organization->name }} sections">
                    <a href="#overview" data-tab="overview" class="is-active">Overview</a>
                    
                    {{-- Standard Sections --}}
                    @if (!empty($sectionData['officers']['items']))
                        <a href="#officers" data-tab="officers">Members</a>
                    @endif
                    @if (!empty($sectionData['activities']['items']))
                        <a href="#activities" data-tab="activities">Activities</a>
                    @endif
                    @if (!empty($sectionData['gallery']['items']))
                        <a href="#gallery" data-tab="gallery">Gallery</a>
                    @endif

                    {{-- Dynamic/Custom Sections --}}
                    @foreach ($sectionData as $slug => $data)
                        @if (!in_array($slug, ['overview', 'activities', 'officers', 'gallery']))
                            @php
                                $hasContent = !empty($data['body']) || !empty($data['items']) || !empty($data['image']);
                            @endphp
                            @if ($hasContent)
                                <a href="#{{ $slug }}" data-tab="{{ $slug }}">{{ $data['title'] ?? Str::title(str_replace('-', ' ', $slug)) }}</a>
                            @endif
                        @endif
                    @endforeach
                </nav>
            </div>
        </section>

        <!-- Overview Section -->
        <section id="overview" class="py-5 it-tab-section is-active">
            <div class="container">
                <div class="section-header mb-5 d-flex flex-column align-items-center text-center">
                    <span class="org-section-badge">About</span>
                    <h2 class="retro-section-title mt-3">{{ $organization->name }}</h2>
                    @if ($department)
                        <p class="text-muted small mt-1">
                            <i class="bi bi-building me-1"></i>
                            {{ $department->name }} &mdash; {{ $collegeName }}
                        </p>
                    @endif
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        @if (!empty($organization->description))
                            <div class="ql-editor p-0" style="font-size: 1.05rem; line-height: 1.7; text-align: justify;">
                                {!! $organization->description !!}
                            </div>
                        @endif

                        @if (!empty($sectionData['overview']['body']))
                            <div class="retro-section-text" style="font-size: 1.05rem; line-height: 1.7;">
                                {!! $sectionData['overview']['body'] !!}
                            </div>
                        @endif

                        @if (empty($organization->description) && empty($sectionData['overview']['body']))
                            <div class="text-center py-5">
                                <span class="d-block display-1 mb-3">🚧</span>
                                <h3 class="fw-bold mb-3">This Page Is Coming Soon</h3>
                                <p class="lead text-muted mb-0">We're still working on this section. Check back soon!</p>
                            </div>
                        @endif
                    </div>

                    @php
                        $featureImage = !empty($sectionData['overview']['image']) 
                            ? $sectionData['overview']['image'] 
                            : (!empty($organization->logo) ? $organization->logo : null);
                        $isLogo = $featureImage === ($organization->logo ?? null);
                    @endphp

                    @if ($featureImage)
                    <div class="col-lg-4 mt-4 mt-lg-0">
                        <div class="bg-light rounded-3 p-4 d-flex align-items-center justify-content-center" style="min-height: 220px;">
                            @if ($isLogo)
                                <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($featureImage) }}" alt="{{ $organization->name }}" class="img-fluid" style="max-height: 200px; object-fit: contain;">
                            @else
                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($featureImage) }}" alt="{{ $organization->name }}" class="img-fluid" style="max-height: 200px; object-fit: contain;">
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Members Section -->
        @php
            $members = $sectionData['officers']['items'] ?? [];
            $organizationMemberFallbackLogo = \App\Providers\AppServiceProvider::resolveLogoUrl($organization->logo)
                ?: \App\Providers\AppServiceProvider::resolveLogoUrl($collegeLogoUrl);
        @endphp
        @if (!empty($members) || !empty($organization->adviser))
        <section id="officers" class="py-5 bg-light it-tab-section">
            <div class="container">
                {{-- Adviser Section --}}
                @if (!empty($organization->adviser))
                <div class="adviser-section mb-5">
                    <div class="member-section-header">
                        <h2 class="h4 fw-700 mb-0" style="color: {{ $headerColor }};">Faculty Adviser</h2>
                        <span class="member-section-count">1 member</span>
                        <div class="member-section-line"></div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-6 col-lg-4">
                            <div class="member-card adviser-card" style="border: 2px solid var(--college-accent-color);">
                                <div class="member-card-photo-wrap">
                                    @if ($adviserFaculty && !empty($adviserFaculty->photo))
                                        <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($adviserFaculty->photo) }}" alt="{{ $organization->adviser }}" class="member-card-photo">
                                    @elseif ($organizationMemberFallbackLogo)
                                        <img src="{{ $organizationMemberFallbackLogo }}" alt="{{ $organization->adviser }}" class="member-card-photo p-3 bg-white" style="object-fit: contain;">
                                    @else
                                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center border" style="width: 120px; height: 120px;">
                                            <i class="bi bi-person text-muted fs-1"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="member-card-body text-center">
                                    <h5 class="member-card-name">{{ $organization->adviser }}</h5>
                                    <p class="member-card-role mb-0 text-white fw-700">Faculty Adviser</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Members Section --}}
                @if (!empty($members))
                <div class="members-section {{ !empty($organization->adviser) ? 'mt-5 pt-5 border-top border-light-subtle' : '' }}">
                    <div class="member-section-header">
                        <h2 class="h4 fw-700 mb-0" style="color: {{ $headerColor }};">Officers &amp; Members</h2>
                        <span class="member-section-count">{{ count($members) }} {{ Str::plural('member', count($members)) }}</span>
                        <div class="member-section-line"></div>
                    </div>
                    <div class="row g-4">
                        @foreach ($members as $member)
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="member-card">
                                    <div class="member-card-photo-wrap">
                                        @if (!empty($member['image']))
                                            <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($member['image']) }}" alt="{{ $member['name'] ?? '' }}" class="member-card-photo">
                                        @elseif ($organizationMemberFallbackLogo)
                                            <img src="{{ $organizationMemberFallbackLogo }}" alt="{{ $member['name'] ?? 'Member' }}" class="member-card-photo p-3 bg-white" style="object-fit: contain;">
                                        @else
                                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center border" style="width: 120px; height: 120px;">
                                                <i class="bi bi-person text-muted fs-1"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="member-card-body">
                                        <h5 class="member-card-name">{{ $member['name'] ?? 'N/A' }}</h5>
                                        @if (!empty($member['role']))
                                            <p class="member-card-role">{{ $member['role'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </section>
        @endif

        <!-- Activities Section -->
        @php $activities = $sectionData['activities']['items'] ?? []; @endphp
        @if (!empty($activities))
        <section id="activities" class="py-5 it-tab-section">
            <div class="container">
                <div class="section-header mb-5 d-flex flex-column align-items-center text-center">
                    <span class="org-section-badge">Events</span>
                    <h2 class="retro-section-title mt-3">Activities &amp; Events</h2>
                    <p class="retro-section-text">Key events and activities organized by {{ $organization->name }}.</p>
                </div>
                <div class="row g-4">
                    @foreach ($activities as $activity)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                @if (!empty($activity['image']))
                                    <div class="ratio ratio-16x9">
                                        <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($activity['image']) }}" class="card-img-top object-fit-cover" alt="{{ $activity['title'] ?? '' }}">
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title fw-bold mb-2">{{ $activity['title'] ?? 'Activity' }}</h5>
                                    @if (!empty($activity['date']))
                                        <p class="small text-muted mb-3"><i class="bi bi-calendar3 me-1"></i>{{ $activity['date'] }}</p>
                                    @endif
                                    @if (!empty($activity['description']))
                                        <div class="card-text text-muted small ql-snow">
                                            <div class="ql-editor p-0">{!! $activity['description'] !!}</div>
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

        <!-- Gallery Section -->
        @php $gallery = $sectionData['gallery']['items'] ?? []; @endphp
        @if (!empty($gallery))
        <section id="gallery" class="py-5 bg-light it-tab-section">
            <div class="container">
                <div class="section-header mb-5 d-flex flex-column align-items-center text-center">
                    <span class="org-section-badge">Gallery</span>
                    <h2 class="retro-section-title mt-3">Photo Gallery</h2>
                </div>
                <div class="row g-3">
                    @foreach ($gallery as $index => $album)
                        @if (!empty($album['image']) || !empty($album['photos']))
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route('college.organization.album', ['college' => $collegeSlug, 'organization' => $organization, 'index' => $index]) }}" class="text-decoration-none group d-block h-100 p-2 rounded-3 hover-bg-light transition-all">
                                <div class="ratio ratio-1x1 rounded-4 overflow-hidden shadow-sm position-relative">
                                    @php
                                        $coverImage = !empty($album['image']) 
                                            ? $album['image'] 
                                            : (!empty($album['photos'][0]['image']) ? $album['photos'][0]['image'] : null);
                                    @endphp
                                    
                                    @if ($coverImage)
                                        <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($coverImage) }}" alt="{{ $album['title'] ?? '' }}" class="object-fit-cover w-100 h-100 transition-transform duration-500 group-hover-scale-110">
                                    @else
                                        <div class="bg-secondary-subtle d-flex align-items-center justify-content-center h-100 w-100">
                                            <i class="bi bi-images fs-2 text-muted opacity-50"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="position-absolute bottom-0 start-0 w-100 p-2" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                                        <span class="badge bg-dark bg-opacity-75 text-white border border-light border-opacity-25 rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                            <i class="bi bi-images me-1"></i> {{ count($album['photos'] ?? []) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-3 px-1 text-center">
                                    <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $album['title'] ?? ($album['name'] ?? 'Untitled Album') }}</h6>
                                    @if (!empty($album['description']))
                                        <div class="small text-muted mb-0 ql-snow" style="font-size: 0.8rem;">
                                            <div class="ql-editor p-0">{!! $album['description'] !!}</div>
                                        </div>
                                    @else
                                        <p class="small text-muted mb-0" style="font-size: 0.8rem;">View Album</p>
                                    @endif
                                </div>
                            </a>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- Custom Sections -->
        @foreach ($sectionData as $slug => $data)
            @if (!in_array($slug, ['overview', 'activities', 'officers', 'gallery']))
                @php 
                    $layout = $data['layout'] ?? 'body';
                    $items = $data['items'] ?? [];
                    $hasContent = !empty($data['body']) || !empty($items) || !empty($data['image']);
                @endphp
                
                @if ($hasContent)
                    <section id="{{ $slug }}" class="py-5 it-tab-section {{ $loop->even ? 'bg-light' : '' }}">
                        <div class="container">
                            <div class="section-header mb-5 d-flex flex-column align-items-center text-center">
                                <h2 class="retro-section-title mt-3">{{ $data['title'] ?? Str::title(str_replace('-', ' ', $slug)) }}</h2>
                            </div>

                            @if ($layout === 'split')
                                <div class="row g-4 align-items-center">
                                    <div class="col-md-7">
                                        <div class="section-content text-muted ql-snow">
                                            <div class="ql-editor" style="padding: 0;">{!! $data['body'] !!}</div>
                                        </div>
                                    </div>
                                    @if (!empty($data['image']))
                                        <div class="col-md-5">
                                            <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($data['image']) }}" class="img-fluid rounded-4 shadow-sm w-100" alt="{{ $data['title'] }}">
                                        </div>
                                    @endif
                                </div>
                            @elseif ($layout === 'testimonials')
                                <div class="row g-4 justify-content-center">
                                    @foreach ($items as $item)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="card h-100 border-0 shadow-sm p-4 text-center">
                                                <div class="mb-3">
                                                    @if (!empty($item['image']))
                                                        <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($item['image']) }}" class="rounded-circle shadow-sm object-fit-cover" style="width: 80px; height: 80px;" alt="{{ $item['name'] }}">
                                                    @else
                                                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                            <i class="bi bi-person text-muted fs-2"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <p class="fst-italic text-muted mb-3">"{{ $item['description'] ?? '' }}"</p>
                                                <h5 class="fw-bold mb-1">{{ $item['name'] ?? 'Anonymous' }}</h5>
                                                @if (!empty($item['role']))
                                                    <p class="small text-primary mb-0">{{ $item['role'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif ($layout === 'highlights')
                                <div class="row g-4">
                                    @foreach ($items as $item)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="d-flex align-items-start p-3 bg-white rounded-3 shadow-sm h-100 border border-light-subtle">
                                                <div class="me-3 mt-1">
                                                    @if (!empty($item['image']))
                                                        <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($item['image']) }}" style="width: 48px; height: 48px; object-fit: contain;" alt="{{ $item['name'] }}">
                                                    @else
                                                        <div class="bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                            <i class="bi bi-star-fill"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h5 class="fw-bold mb-2">{{ $item['name'] ?? 'Feature' }}</h5>
                                                    <p class="text-muted small mb-0">{{ $item['description'] ?? '' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif ($layout === 'grid')
                                <div class="row g-4">
                                    @foreach ($items as $item)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                                @if (!empty($item['image']))
                                                    <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($item['image']) }}" class="card-img-top object-fit-cover" style="height: 200px;" alt="{{ $item['name'] }}">
                                                @endif
                                                <div class="card-body">
                                                    <h5 class="card-title fw-bold mb-1">{{ $item['name'] ?? 'Item' }}</h5>
                                                    @if (!empty($item['role']))
                                                        <p class="small text-primary fw-600 mb-2">{{ $item['role'] }}</p>
                                                    @endif
                                                    <div class="card-text text-muted small ql-snow">
                                                        <div class="ql-editor p-0">{!! $item['description'] ?? '' !!}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else {{-- Default Body layout --}}
                                <div class="section-content text-muted ql-snow">
                                    <div class="ql-editor" style="padding: 0;">{!! $data['body'] ?? '' !!}</div>
                                </div>
                            @endif
                        </div>
                    </section>
                @endif
            @endif
        @endforeach
    </main>

    @include('includes.college-footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>

    <script>
        // Toggle sticky header
        document.addEventListener('scroll', function () {
            const wrapper = document.querySelector('.engineering-header-wrapper');
            if (wrapper) {
                wrapper.classList.toggle('engineering-header-scrolled', window.scrollY > 50);
            }
        });

        // Section tabs
        document.addEventListener('DOMContentLoaded', function () {
            const menu = document.querySelector('.it-section-menu-inner');
            const tabs = Array.from(document.querySelectorAll('.it-tab-section'));

            function setActive(tabId) {
                tabs.forEach(s => s.classList.remove('is-active'));
                menu.querySelectorAll('a').forEach(a => a.classList.remove('is-active'));
                const target = document.getElementById(tabId);
                if (target) target.classList.add('is-active');
                const activeLink = menu.querySelector(`a[data-tab="${tabId}"]`);
                if (activeLink) activeLink.classList.add('is-active');
                if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                if (history.replaceState) history.replaceState(null, '', `#${tabId}`);
            }

            setActive((location.hash || '#overview').replace('#', '') || 'overview');

            menu.addEventListener('click', function (e) {
                const link = e.target.closest('a[data-tab]');
                if (!link) return;
                e.preventDefault();
                setActive(link.dataset.tab);
            });
        });
    </script>
</body>
</html>
