<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Information Technology Department - College of Engineering, {{ config('app.name', 'CLSU') }}">
    <title>Information Technology Department - {{ config('app.name', 'CLSU') }}</title>

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
        /* Reuse College of Engineering header styles */
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
            color: #ffffff;
            background-image: url('{{ asset('images/js/app.js') }}');
            background-size: cover;
            background-position: center;
            padding: 5rem 0 4rem;
        }

        .it-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, rgba(134, 9, 10, 0.88), rgba(134, 9, 10, 0.55));
        }

        .it-hero-inner {
            position: relative;
            z-index: 1;
            max-width: 820px;
            margin: 0 auto;
            text-align: center;
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
            top: 0;
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
    </style>
</head>
<body class="retro-modern college-page">
    <!-- Header reused from College of Engineering -->
    <header class="header engineering-header">
        <nav class="navbar navbar-expand-md navbar-dark engineering-navbar">
            <div class="container">
                <a href="{{ route('college.engineering') }}" class="navbar-brand d-flex align-items-center gap-2 logo">
                    <div class="logo-box retro-badge">
                        <img src="{{ asset('images/logos/engineering.jpg') }}" alt="College of Engineering logo" class="logo-image">
                    </div>
                    <div class="logo-text d-flex flex-column">
                        <h2 class="retro-heading mb-0">
                            <span class="logo-full-text d-none d-md-inline">COLLEGE OF ENGINEERING</span>
                            <span class="logo-short-text d-inline d-md-none">CEn</span>
                        </h2>
                        <p class="retro-subtitle">
                            <span class="d-inline d-md-none">CLSU</span>
                            <span class="d-none d-md-inline">Information Technology Department</span>
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
                            <a href="{{ route('college.engineering') }}#home" class="nav-link">College Home</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('college.engineering') }}#programs" class="nav-link">Departments</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('college.engineering') }}#explore" class="nav-link">Facilities</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('college.engineering') }}#news-announcement-board" class="nav-link">News &amp; Announcements</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <!-- Hero / Overview -->
        <section class="it-hero">
            <div class="container">
                <div class="it-hero-inner py-5">
                   
                    <h1 class="it-hero-title">
                       Department of Information Technology
                    </h1>
                    <p class="it-hero-subtitle mb-4">
                        Pursue your undergraduate IT degree at CLSU’s College of Engineering. A forward-thinking
                        curriculum and industry-standard laboratories equip you with the skills and experience needed
                        to become a tech leader of tomorrow.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a href="#program-overview" class="btn btn-light btn-sm">Explore the Program</a>
                        <a href="#curriculum" class="btn btn-outline-light btn-sm">View Sample Courses</a>
                    </div>
                </div>
            </div>
        </section>

        @php
            $objectivesVisible = $department->objectives_is_visible ?? true;
            $programsVisible = $department->programs_is_visible ?? true;
            $awardsVisible = $department->awards_is_visible ?? true;
            $researchVisible = $department->research_is_visible ?? true;
            $extensionVisible = $department->extension_is_visible ?? true;
            $trainingVisible = $department->training_is_visible ?? true;
            $linkagesVisible = $department->linkages_is_visible ?? true;
            $facilitiesVisible = $department->facilities_is_visible ?? true;
            $alumniVisible = $department->alumni_is_visible ?? true;
        @endphp

        <!-- Section Menu -->
        <section class="it-section-menu">
            <div class="container">
                <nav class="it-section-menu-inner" aria-label="IT department sections">
                    <a href="#program-overview" data-tab="program-overview">Overview</a>
                    @if($objectivesVisible)
                        <a href="#objectives" data-tab="objectives">Objectives</a>
                    @endif
                    @if($facultySectionVisible ?? true)
                        <a href="#faculty" data-tab="faculty">Faculty</a>
                    @endif
                    @if($programsVisible)
                        <a href="#programs" data-tab="programs">Programs</a>
                    @endif
                    @if($awardsVisible)
                        <a href="#awards" data-tab="awards">Awards</a>
                    @endif
                    @if($researchVisible)
                        <a href="#research" data-tab="research">Research</a>
                    @endif
                    @if($extensionVisible)
                        <a href="#extension" data-tab="extension">Extension</a>
                    @endif
                    @if($trainingVisible)
                        <a href="#training" data-tab="training">Training</a>
                    @endif
                    @if($linkagesVisible)
                        <a href="#linkages" data-tab="linkages">Linkages</a>
                    @endif
                    @if($facilitiesVisible)
                        <a href="#facilities" data-tab="facilities">Facilities</a>
                    @endif
                    @if($alumniVisible)
                        <a href="#alumni" data-tab="alumni">Alumni</a>
                    @endif
                </nav>
            </div>
        </section>

        <!-- Overview Section -->
        <section id="program-overview" class="py-5 it-tab-section is-active">
            <div class="container">
                <div class="row align-items-start gy-4">
                    <div class="col-lg-7">
                        <span class="section-badge retro-label mb-4 d-inline-block" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; letter-spacing: 2px; width: fit-content;">Overview</span>
                        <p class="retro-section-text mb-3" style="font-size: 1rem; line-height: 1.6;">
                            As IT shapes the future of global development, CLSU's Department of Information Technology (DIT) prepares you to be tech-forward. With programs aligned with industry standards and outcomes-based education, the DIT carries CLSU's standard of achievement as a recognized center of engineering excellence.
                        </p>
                        <p class="retro-section-text mb-3" style="font-size: 1rem; line-height: 1.6;">
                            DIT's curriculum, crafted by industry experts, emphasizes mastering the latest tools and languages in IT. Our courses will develop your analytical mind, tackling real-world challenges through hands-on laboratory experiences and industry applications.
                        </p>
                        <p class="retro-section-text mb-4" style="font-size: 1rem; line-height: 1.6;">
                            CLSU's College of Engineering is a recognized Center of Excellence. We ensure you graduate prepared for any field in information technology. Join the university's legacy of high-caliber professionals today and become an innovator in tech!
                        </p>
                    
                    </div>
                    <div class="col-lg-5">
                        <div class="ratio ratio-4x3 rounded overflow-hidden shadow-sm">
                            <img src="{{ asset('images/CLSU.jpg') }}" alt="Information Technology Department" class="w-100 h-100" style="object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Objectives Section -->
        @if($objectivesVisible)
        <section id="objectives" class="py-5 bg-light it-tab-section">
            <div class="container">
                <div class="section-header mb-4">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Objectives</span>
                 
                </div>
                <div class="row">
                    <div class="col-lg-10 mx-auto">
                        <ul class="list-unstyled mb-0" style="font-size: 1rem; line-height: 1.8;">
                            <li class="mb-3">
                                <strong style="color: {{ $headerColor }};">1.</strong> Provide relevant and quality education in information and communications technology (ICT).
                            </li>
                            <li class="mb-3">
                                <strong style="color: {{ $headerColor }};">2.</strong> Generate innovative technologies and systems that upholds the global and national initiatives of bridging the digital divide in the diverse range of human experiences, in every sector of the society and the economy.
                            </li>
                            <li class="mb-0">
                                <strong style="color: {{ $headerColor }};">3.</strong> Provide technological expertise in the field of information technology for the country, the region and beyond.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- Program Description Section -->
        @if($programsVisible)
        <section id="programs" class="py-5 it-tab-section">
            <div class="container">
                <div class="section-header mb-4">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Program Description</span>
                    <h2 class="retro-section-title mt-3">Bachelor of Science in Information Technology</h2>
                </div>
                <div class="row">
                    <div class="col-lg-10 mx-auto">
                        <p class="retro-section-text" style="font-size: 1rem; line-height: 1.8;">
                            The Bachelor of Science in Information Technology (BSIT) program equips students with the expertise to plan, create, install, customize, operate, manage, and maintain information technologies that address the computing needs of organizations. Throughout the program, students develop critical skills essential for the selection, development, application, integration, and management of computing technologies.
                        </p>
                        <p class="retro-section-text" style="font-size: 1rem; line-height: 1.8;">
                            By engaging in problem-solving activities and research-driven projects, students learn to assess emerging technologies, identify effective solutions for organizational challenges, and stay at the forefront of IT innovations. Graduates of the program are well-prepared for a variety of roles, including software engineer, web and applications developer, systems administrator, database administrator, network administrator, networks engineer, IT auditor, systems analyst, computer programmer, IT manager, and multimedia specialist.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- Faculty Section -->
        @if($facultySectionVisible ?? true)
        <section id="faculty" class="py-5 bg-light it-tab-section">
            <div class="container">
                <div class="section-header mb-4">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Faculty</span>
                    <h2 class="retro-section-title mt-3">{{ $facultySectionTitle ?? 'Department Faculty' }}</h2>
                    <div class="retro-section-text">{!! $facultySectionDescription ?? 'Meet the faculty members who support instruction, research, and extension services in Information Technology.' !!}</div>
                </div>

                <div class="row g-4">
                    @forelse(($faculty ?? collect()) as $member)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold mb-1">{{ $member->name }}</h5>
                                    <p class="mb-2 text-muted">{{ $member->position ?? 'Faculty Member' }}</p>
                                    @if(!empty($member->email))
                                        <p class="mb-0">{{ $member->email }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted mb-0">No faculty members listed yet.</p>
                        </div>
                    @endforelse
                </div>


            </div>
        </section>
        @endif

        <!-- Awards Section (placeholder) -->
        @if($awardsVisible)
        <section id="awards" class="py-5 bg-light it-tab-section">
            <div class="container">
                <div class="section-header mb-4">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Awards</span>
                    <h2 class="retro-section-title mt-3">Awards</h2>
                    <p class="retro-section-text">Content coming soon.</p>
                </div>
            </div>
        </section>
        @endif

        <!-- Research Section (placeholder) -->
        @if($researchVisible)
        <section id="research" class="py-5 it-tab-section">
            <div class="container">
                <div class="section-header mb-4">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Research</span>
                    <h2 class="retro-section-title mt-3">Research</h2>
                    <p class="retro-section-text">Content coming soon.</p>
                </div>
            </div>
        </section>
        @endif

        <!-- Extension Section (placeholder) -->
        @if($extensionVisible)
        <section id="extension" class="py-5 bg-light it-tab-section">
            <div class="container">
                <div class="section-header mb-4">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Extension</span>
                    <h2 class="retro-section-title mt-3">Extension</h2>
                    <p class="retro-section-text">Content coming soon.</p>
                </div>
            </div>
        </section>
        @endif

        <!-- Training Section (placeholder) -->
        @if($trainingVisible)
        <section id="training" class="py-5 it-tab-section">
            <div class="container">
                <div class="section-header mb-4">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Training</span>
                    <h2 class="retro-section-title mt-3">Training</h2>
                    <p class="retro-section-text">Content coming soon.</p>
                </div>
            </div>
        </section>
        @endif

        <!-- Linkages Section (placeholder) -->
        @if($linkagesVisible)
        <section id="linkages" class="py-5 bg-light it-tab-section">
            <div class="container">
                <div class="section-header mb-4">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Linkages</span>
                    <h2 class="retro-section-title mt-3">Linkages</h2>
                    <p class="retro-section-text">Content coming soon.</p>
                </div>
            </div>
        </section>
        @endif

        <!-- Facilities Section (placeholder) -->
        @if($facilitiesVisible)
        <section id="facilities" class="py-5 it-tab-section">
            <div class="container">
                <div class="section-header mb-4">
                    <div class="text-center">
                        <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Facilities</span>
                    </div>

                    <p class="retro-section-text">Explore selected learning spaces and laboratories used for instruction, hands-on activities, and student projects.</p>
                </div>

                <div class="it-facilities-group">
                    <h3 class="it-facilities-group-title">Computer Laboratories</h3>
                    <p class="it-facilities-group-subtitle">Workstations and software tools for programming, databases, and systems development.</p>
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-4">
                            <div class="it-facility-card">
                                <div class="ratio ratio-4x3">
                                    <img src="{{ asset('images/CLSU.jpg') }}" alt="Computer Laboratory - Workstations">
                                </div>
                                <div class="it-facility-card-body">
                                    <p class="it-facility-card-title">Lab Workstations</p>
                                    <p class="it-facility-card-text">Programming and software development exercises.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="it-facility-card">
                                <div class="ratio ratio-4x3">
                                    <img src="{{ asset('images/Library-clean.webp') }}" alt="Computer Laboratory - Learning Space">
                                </div>
                                <div class="it-facility-card-body">
                                    <p class="it-facility-card-title">Learning Space</p>
                                    <p class="it-facility-card-text">Collaborative activities and lab-based discussions.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="it-facility-card">
                                <div class="ratio ratio-4x3">
                                    <img src="{{ asset('images/infirmary.jpg') }}" alt="Computer Laboratory - Hands-on Sessions">
                                </div>
                                <div class="it-facility-card-body">
                                    <p class="it-facility-card-title">Hands-on Sessions</p>
                                    <p class="it-facility-card-text">Guided laboratory work and practical assessments.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="it-facilities-group">
                    <h3 class="it-facilities-group-title">Networking &amp; Cybersecurity Lab</h3>
                    <p class="it-facilities-group-subtitle">Network configuration, routing/switching, and secure systems practice.</p>
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-4">
                            <div class="it-facility-card">
                                <div class="ratio ratio-4x3">
                                    <img src="{{ asset('images/wc1.jpg') }}" alt="Networking Lab - Equipment">
                                </div>
                                <div class="it-facility-card-body">
                                    <p class="it-facility-card-title">Network Equipment</p>
                                    <p class="it-facility-card-text">Switching, routing, and structured cabling practice.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="it-facility-card">
                                <div class="ratio ratio-4x3">
                                    <img src="{{ asset('images/res.jpg') }}" alt="Networking Lab - Simulation">
                                </div>
                                <div class="it-facility-card-body">
                                    <p class="it-facility-card-title">Network Simulation</p>
                                    <p class="it-facility-card-text">Topologies, protocols, and troubleshooting scenarios.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="it-facility-card">
                                <div class="ratio ratio-4x3">
                                    <img src="{{ asset('images/178935.jpg') }}" alt="Cybersecurity Lab - Secure Systems">
                                </div>
                                <div class="it-facility-card-body">
                                    <p class="it-facility-card-title">Secure Systems</p>
                                    <p class="it-facility-card-text">Security labs, monitoring, and defensive exercises.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="it-facilities-group">
                    <h3 class="it-facilities-group-title">Multimedia &amp; Innovation Studio</h3>
                    <p class="it-facilities-group-subtitle">Creative production, UI/UX prototyping, and student innovation projects.</p>
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-4">
                            <div class="it-facility-card">
                                <div class="ratio ratio-4x3">
                                    <img src="{{ asset('images/vfkbvgtk801439.jpeg') }}" alt="Multimedia Studio - Editing">
                                </div>
                                <div class="it-facility-card-body">
                                    <p class="it-facility-card-title">Editing &amp; Production</p>
                                    <p class="it-facility-card-text">Multimedia creation and project documentation.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="it-facility-card">
                                <div class="ratio ratio-4x3">
                                    <img src="{{ asset('images/CLSU.jpg') }}" alt="Innovation Studio - Prototyping">
                                </div>
                                <div class="it-facility-card-body">
                                    <p class="it-facility-card-title">Prototyping</p>
                                    <p class="it-facility-card-text">UI/UX mockups, demos, and product pitches.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="it-facility-card">
                                <div class="ratio ratio-4x3">
                                    <img src="{{ asset('images/Library-clean.webp') }}" alt="Innovation Studio - Collaboration">
                                </div>
                                <div class="it-facility-card-body">
                                    <p class="it-facility-card-title">Collaboration Area</p>
                                    <p class="it-facility-card-text">Teamwork spaces for capstone and research groups.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="it-facilities-group">
                    <h3 class="it-facilities-group-title">Facilities Map / Floor Plan</h3>
                    <p class="it-facilities-group-subtitle">Placeholder for the IT laboratory layout or building floor plan.</p>
                    <div class="it-facilities-map">
                        <div class="ratio ratio-16x9">
                            <!-- Replace this placeholder with an actual image -->
                            <!-- Example: <img src="{{ asset('images/it/facilities/it-floor-plan.png') }}" alt="IT Facilities Floor Plan" class="w-100 h-100" style="object-fit: contain; background: #fff;"> -->
                            <div class="it-facilities-map-placeholder">
                                MAP / FLOOR PLAN PLACEHOLDER
                                <br>
                                <small class="fw-semibold">Add an image like <code>public/images/it/facilities/it-floor-plan.png</code></small>
                            </div>
                        </div>
                        <p class="it-facilities-map-note">You can upload a campus map, building directory, or floor plan image and replace the placeholder above.</p>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="mb-0"><strong>Tip:</strong> Replace the placeholder images with actual IT laboratory photos (recommended folder: <code>public/images/it/facilities/</code>).</p>
                </div>
            </div>
        </section>
        @endif

        <!-- Alumni Section (placeholder) -->
        @if($alumniVisible)
        <section id="alumni" class="py-5 bg-light it-tab-section">
            <div class="container">
                <div class="section-header mb-4">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Alumni</span>
                    <h2 class="retro-section-title mt-3">Alumni</h2>
                    <p class="retro-section-text">Content coming soon.</p>
                </div>
            </div>
        </section>
        @endif

        <!-- Graduate Outcomes -->
        <section id="graduate-outcomes" class="py-5 bg-light">
            <div class="container">
                <div class="section-header mb-4">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Graduate Outcomes</span>
                    <h2 class="retro-section-title mt-3">Expected Graduate Attributes</h2>
                </div>

                <div class="row gy-4">
                    <div class="col-md-4">
                        <div class="program-card retro-card h-100">
                            <div class="program-card-overlay"></div>
                            <div class="retro-card-border"></div>
                            <h3 class="retro-card-title">Technical Proficiency</h3>
                            <p class="retro-card-text">Able to design, develop, deploy, and maintain software and IT solutions using current tools and technologies.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="program-card retro-card h-100">
                            <div class="program-card-overlay"></div>
                            <div class="retro-card-border"></div>
                            <h3 class="retro-card-title">Problem Solving</h3>
                            <p class="retro-card-text">Applies analytical and critical thinking skills to address real-world computing problems ethically and responsibly.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="program-card retro-card h-100">
                            <div class="program-card-overlay"></div>
                            <div class="retro-card-border"></div>
                            <h3 class="retro-card-title">Professionalism</h3>
                            <p class="retro-card-text">Demonstrates teamwork, communication, and lifelong learning in diverse and multidisciplinary environments.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Curriculum Snapshot -->
        <section id="curriculum" class="py-5">
            <div class="container">
                <div class="section-header mb-4">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content;">Curriculum</span>
                    <h2 class="retro-section-title mt-3">Sample Courses</h2>
                    <p class="retro-section-text">A selection of key courses students may take in the BSIT program.</p>
                </div>

                <div class="row gy-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">Core Programming</h5>
                                <ul class="mb-0">
                                    <li>Introduction to Programming</li>
                                    <li>Object-Oriented Programming</li>
                                    <li>Data Structures and Algorithms</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">Systems & Infrastructure</h5>
                                <ul class="mb-0">
                                    <li>Computer Networks</li>
                                    <li>Operating Systems</li>
                                    <li>Systems Administration</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">Applications & Practice</h5>
                                <ul class="mb-0">
                                    <li>Web and Mobile Development</li>
                                    <li>Database Systems</li>
                                    <li>Capstone Project / Internship</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="mb-1"><strong>Note:</strong> For the official and updated curriculum, please refer to the College of Engineering or the University Registrar.</p>
                </div>
            </div>
        </section>
    </main>

    <footer id="contact" class="footer footer-rich" style="background-image: linear-gradient(135deg, rgba(18, 18, 18, 0.9), rgba(18, 18, 18, 0.9)), url('{{ asset('images/CLSU.jpg') }}');">
        <div class="container">
            <div class="footer-grid footer-grid-rich">
                <div class="footer-column footer-brand">
                    <div class="footer-logos">
                        <img src="{{ asset('images/logos/engineering.jpg') }}" alt="College of Engineering logo">
                    </div>
                    <h3>COLLEGE OF ENGINEERING</h3>
                    <p>Central Luzon State University</p>
                    <div class="footer-divider"></div>
                    <p>The College of Engineering aims to provide relevant and quality training for students in engineering and related fields consistent with the national development thrust.</p>
                    <ul class="footer-contact">
                        <li>College of Engineering, Central Luzon State University, Science City of Munoz 3120, Nueva Ecija, Philippines</li>
                        <li><a href="tel:0444567208">044-456-7208</a></li>
                        <li><a href="mailto:cen@clsu.edu.ph">cen@clsu.edu.ph</a></li>
                    </ul>

                    <div class="it-footer-social-title">KEEP IN TOUCH</div>
                    <div class="it-footer-social">
                        <a href="#" title="Facebook" aria-label="Facebook">f</a>
                        <a href="#" title="X" aria-label="X">𝕏</a>
                        <a href="#" title="YouTube" aria-label="YouTube">▶</a>
                        <a href="#" title="LinkedIn" aria-label="LinkedIn">in</a>
                        <a href="#" title="RSS" aria-label="RSS">⌁</a>
                        <a href="#" title="Spotify" aria-label="Spotify">⦿</a>
                    </div>
                </div>

                <div class="footer-column">
                    <h3 class="footer-heading">QUICK LINKS</h3>
                    <ul class="footer-list">
                        <li><a href="https://www.clsu.edu.ph" target="_blank" rel="noopener noreferrer">CLSU Main Website</a></li>
                        <li><a href="https://www.clsu.edu.ph/ovpaa/" target="_blank" rel="noopener noreferrer">CLSU OVPAA</a></li>
                        <li><a href="https://www.clsu.edu.ph/colleges/" target="_blank" rel="noopener noreferrer">CLSU Colleges</a></li>
                        <li><a href="https://portal.clsu.edu.ph" target="_blank" rel="noopener noreferrer">Student Portal</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>

    <script>
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
                // - Overview: show Program Description + Graduate Outcomes, hide Curriculum
                // - Objectives: show Curriculum only
                // - Others: hide Program Description, Graduate Outcomes, and Curriculum
                if (tabId === 'program-overview') {
                    if (programsSection) programsSection.classList.add('is-active');
                    if (graduateSection) graduateSection.style.display = '';
                    if (curriculumSection) curriculumSection.style.display = 'none';
                } else if (tabId === 'objectives') {
                    if (programsSection) programsSection.classList.remove('is-active');
                    if (graduateSection) graduateSection.style.display = 'none';
                    if (curriculumSection) curriculumSection.style.display = '';
                } else {
                    if (programsSection) programsSection.classList.remove('is-active');
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
</body>
</html>


