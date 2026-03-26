@php
    $collegeName = $collegeName ?? 'College';
    $collegeSlug = $collegeSlug ?? 'college';
    $collegeShortName = $collegeShortName ?? 'College';
    $collegeLogoUrl = $collegeLogoUrl ?? asset('images/colleges/main.webp');
    $headerColor = !empty($headerColor) && preg_match('/^#[0-9A-Fa-f]{6}$/', $headerColor) ? $headerColor : '#0d6e42';
    $accentColor = !empty($accentColor) && preg_match('/^#[0-9A-Fa-f]{6}$/', $accentColor) ? $accentColor : '#0d2818';
    $collegeEmail = $collegeEmail ?? $collegeSlug . '@clsu.edu.ph';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Accreditation - {{ $collegeName }}, {{ config('app.name', 'CLSU') }}">
    <title>Accreditation - {{ $collegeName }} - {{ config('app.name', 'CLSU') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Buttershine+Serif:wght@400;700&family=Libre+Franklin:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Styles -->
    @include('includes.college-css')
    <style>
        .accreditation-hero-description,
        .accreditation-hero-description *,
        .accreditation-hero-description p,
        .accreditation-hero-description span,
        .accreditation-hero-description div {
            color: #fff !important;
        }
        .accreditation-card {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
            height: 100%;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            display: flex;
            flex-direction: column;
        }
        .accreditation-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: {{ $headerColor }}33;
        }
        .agency-header {
            background: {{ $headerColor }}0a;
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid {{ $headerColor }}14;
        }
        .agency-badge {
            display: inline-block;
            background: {{ $headerColor }};
            color: white;
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            font-weight: 800;
            font-size: 0.65rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .accreditation-logo-wrap {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            padding: 1rem;
            position: relative;
        }
        .accreditation-logo {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.05));
            transition: transform 0.3s ease;
        }
        .accreditation-card:hover .accreditation-logo {
            transform: scale(1.05);
        }
        /* Removed .accreditation-body layout definitions */
        /* Removed .level-tag and .program-name layout definitions */
        /* Removed .validity-info layout definitions */
        .status-active {
            background: #e6fffa;
            color: #2c7a7b;
        }
    </style>
</head>
<body class="retro-modern">

    <!-- Includes: Header -->
    @include('includes.college-header')

    <main>
        <!-- Title -->
        <section class="py-5 mt-5 pt-5" style="background: {{ $headerColor }};">
            <div class="container text-center py-5">
                <h1 class="display-3 fw-900 text-white mb-3">{{ $heroTitle }}</h1>
                <div class="lead mx-auto accreditation-hero-description" style="max-width: 700px; font-size: 1.25rem;">{!! $heroDescription !!}</div>
            </div>
        </section>

        <!-- Content -->
        <section class="py-5 bg-white">
            <div class="container">
                @if($accreditations->isEmpty())
                    <div class="text-center py-5 my-5">
                        <div class="mb-4 opacity-25">
                            <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-muted">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                            </svg>
                        </div>
                        <h2 class="h3 font-weight-bold text-dark mb-3">Accreditation status is currently being updated.</h2>
                        <p class="text-muted mb-4">We are currently documenting our program recognitions. Please check back soon.</p>
                        <a href="{{ route('college.show', $collegeSlug) }}" class="btn rounded-pill px-5 py-3 fw-700 shadow-sm" style="background: {{ $headerColor }}; color: white; border: none;">Back to {{ $collegeShortName }} Home</a>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($accreditations as $acc)
                            <div class="col-md-6 col-lg-4">
                                <div class="accreditation-card">
                                    <div class="agency-header">
                                        <div class="agency-badge" title="{{ $acc->agency }}">{{ $acc->agency_acronym }}</div>
                                    </div>
                                    <div class="accreditation-logo-wrap">
                                        @if($acc->logo)
                                            @php
                                                $logoUrl = \App\Providers\AppServiceProvider::resolveLogoUrl($acc->logo);
                                            @endphp
                                            <img src="{{ $logoUrl }}" alt="{{ $acc->agency }}" class="accreditation-logo">
                                        @else
                                            <img src="{{ asset('images/accreditation_placeholder.webp') }}" alt="Accreditation Placeholder" class="accreditation-logo opacity-50">
                                        @endif
                                    </div>
                                    <div class="p-4 flex-grow-1 d-flex flex-column">
                                        <div class="fs-4 fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">{{ $acc->level }}</div>
                                        <div class="fw-semibold small mb-4" style="color: {{ $headerColor }};">
                                            @if($acc->program)
                                                {{ $acc->program->title }}
                                            @endif
                                        </div>
                                        
                                        @if($acc->description)
                                            <p class="small text-muted mb-3">{{ $acc->description }}</p>
                                        @endif

                                        <div class="d-flex align-items-center gap-2 text-secondary border-top pt-3 mt-auto small">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                            <span>
                                                @if($acc->valid_until)
                                                    Valid until {{ $acc->valid_until->format('F Y') }}
                                                @else
                                                    Full Accreditation Status
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if(isset($membershipSection) && $membershipSection->is_visible && !$memberships->isEmpty())
                    <div class="mt-5 pt-5 mb-5 border-top">
                        @php
                            $membershipTitle = $membershipSection?->title ?? 'Professional Memberships';
                            $membershipBody = $membershipSection?->body ?? 'We are proud members of national and international organizations that promote academic excellence and professional development in our fields.';
                        @endphp
                        <div class="row align-items-center mb-5">
                            <div class="col-lg-6">
                                <h2 class="display-5 fw-900 text-dark mb-3">{{ $membershipTitle }}</h2>
                                @if(!empty(trim($membershipBody)))
                                    <p class="lead text-muted">{!! $membershipBody !!}</p>
                                @endif
                            </div>
                        </div>

                        <div class="row g-4">
                            @foreach($memberships as $membership)
                                <div class="col-md-6 col-lg-4">
                                    <div class="accreditation-card">
                                        <div class="agency-header">
                                            <div class="agency-badge" title="Membership Type">
                                                {{ mb_strtoupper($membership->membership_type ?: 'MEMBER') }}
                                            </div>
                                        </div>
                                        <div class="accreditation-logo-wrap">
                                            @if($membership->logo)
                                                @php
                                                    $logoUrl = \App\Providers\AppServiceProvider::resolveLogoUrl($membership->logo);
                                                @endphp
                                                <img src="{{ $logoUrl }}" alt="{{ $membership->organization }}" class="accreditation-logo">
                                            @else
                                                <div class="w-100 h-100 bg-light rounded d-flex align-items-center justify-content-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="{{ $headerColor }}" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="opacity-50"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="p-4 flex-grow-1 d-flex flex-column">
                                            <div class="fs-5 fw-bold text-dark mb-1 lh-sm">{{ $membership->organization }}</div>
                                            <div class="fw-semibold small mb-3" style="color: {{ $headerColor }};">
                                                @if($membership->department)
                                                    {{ $membership->department->name }}
                                                @endif
                                            </div>
                                            
                                            @if($membership->description)
                                                <p class="small text-muted mb-3">{{ $membership->description }}</p>
                                            @endif

                                            <div class="d-flex align-items-center gap-2 text-secondary border-top pt-3 mt-auto small">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                                <span>Active Professional Network</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('includes.college-footer')

    <!-- Includes: Scripts -->
    @include('includes.college-scripts')
</body>
</html>
