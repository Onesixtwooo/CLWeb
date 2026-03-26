@extends('layouts.app')

@section('content')
<style>
    :root {
        --dept-header-color: {{ $headerColor }};
        --dept-accent-color: {{ $accentColor }};
    }

    .dept-header {
        background: var(--dept-header-color);
        margin-top: 104px; /* Space for fixed CLSU header */
    }

    .dept-navbar {
        background: var(--dept-header-color);
    }

    .dept-navbar .nav-link,
    .dept-navbar .retro-heading,
    .dept-navbar .retro-subtitle {
        color: #ffffff;
    }

    .dept-navbar .nav-link:hover {
        color: #ffffff;
        background-color: transparent !important;
    }

    .dept-hero {
        position: relative;
        min-height: 60vh;
        display: flex;
        align-items: center;
        color: #ffffff;
        @php
            $bannerSection = $department->getSection('overview');
            $bannerImage = $bannerSection['banner_image'] ?? null;
        @endphp
        @if($bannerImage)
        background-image: url('{{ $bannerImage }}');
        background-size: cover;
        background-position: center;
        @else
        background: linear-gradient(135deg, {{ $headerColor }} 0%, {{ $accentColor }} 100%);
        @endif
        padding: 5rem 0 4rem;
    }

    .dept-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        @if($bannerImage)
        background: linear-gradient(to right, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.4));
        @else
        background: rgba(0, 0, 0, 0.1);
        @endif
    }

    .dept-hero-inner {
        position: relative;
        z-index: 1;
        max-width: 820px;
        margin: 0 auto;
        text-align: center;
    }

    .dept-hero-title {
        font-size: clamp(2.4rem, 4vw, 3.2rem);
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 1rem;
        text-decoration: none !important;
    }

    .section-badge {
        background: linear-gradient(135deg, {{ $headerColor }} 0%, {{ $accentColor }} 100%);
        color: #ffffff;
        padding: 0.5rem 1.25rem;
        display: inline-block;
        letter-spacing: 2px;
        text-transform: uppercase;
        font-weight: 700;
        font-size: 0.85rem;
    }
</style>

<!-- CLSU Fixed Header -->
@include('partials.clsu-header')

<!-- Header -->
<header class="header dept-header">
    <nav class="navbar navbar-expand-md navbar-dark dept-navbar">
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

<main>
    <!-- Hero Section -->
    <section class="dept-hero">
        <div class="container">
            <div class="dept-hero-inner py-5">
                <h1 class="dept-hero-title">{{ $department->name }}</h1>
                @if($department->details)
                <p class="mb-0" style="font-size: 1rem; color: rgba(255, 255, 255, 0.9);">
                    {{ $department->details }}
                </p>
                @endif
            </div>
        </div>
    </section>

    <!-- Overview Section -->
    @php
        $overviewSection = $department->getSection('overview');
    @endphp
    @if($overviewSection && !empty($overviewSection['body']))
    <section id="overview" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <span class="section-badge mb-4 d-inline-block">Overview</span>
                    <div class="content">
                        {!! nl2br(e($overviewSection['body'])) !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Faculty Section -->
    @if($faculty->count() > 0)
    <section id="faculty" class="py-5 bg-light">
        <div class="container">
            <span class="section-badge mb-4 d-inline-block">Faculty</span>
            <h2 class="retro-section-title mt-3 mb-4">Department Faculty</h2>
            
            <div class="row g-4">
                @foreach($faculty as $member)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        @if($member->photo_path)
                        <img src="{{ asset($member->photo_path) }}" alt="{{ $member->name }}" class="card-img-top" style="height: 250px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-1">{{ $member->name }}</h5>
                            @if($member->position)
                            <p class="mb-2 text-muted">{{ $member->position }}</p>
                            @endif
                            @if($member->bio)
                            <p class="mb-0 small">{{ Str::limit($member->bio, 100) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Placeholder for Future Sections -->
    <section class="py-5">
        <div class="container">
            <div class="text-center text-muted">
                <p class="mb-0">More department information coming soon.</p>
                <p class="small">Content sections can be managed through the admin panel.</p>
            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<footer id="contact" class="footer footer-rich" style="background: #121212; color: #fff;">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6">
                <h3>{{ strtoupper($collegeName) }}</h3>
                <p>Central Luzon State University</p>
                <ul class="list-unstyled mt-3">
                    <li class="mb-2">Science City of Munoz 3120, Nueva Ecija, Philippines</li>
                    <li class="mb-2"><a href="mailto:{{ $collegeEmail }}" class="text-white">{{ $collegeEmail }}</a></li>
                </ul>
            </div>
            <div class="col-md-6">
                <h4 class="mb-3">Quick Links</h4>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('college.show', ['college' => $collegeSlug]) }}" class="text-white">College Home</a></li>
                    <li class="mb-2"><a href="{{ route('college.faculty', ['college' => $collegeSlug]) }}" class="text-white">Faculty</a></li>
                    <li class="mb-2"><a href="{{ route('college.facilities', ['college' => $collegeSlug]) }}" class="text-white">Facilities</a></li>
                </ul>
            </div>
        </div>
        <div class="text-center mt-4 pt-4 border-top border-secondary">
            <p class="mb-0">&copy; {{ date('Y') }} Central Luzon State University. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
@endsection
