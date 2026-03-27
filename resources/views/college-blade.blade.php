@php
    $collegeName = $collegeName ?? 'College';
    $collegeSlug = $collegeSlug ?? 'college';
    $collegeShortName = $collegeShortName ?? 'College';
    $collegeLogoUrl = $collegeLogoUrl ?? asset('images/logo_placeholder.png');
    // Default green when appearance not yet set up
    $headerColor = ! empty($headerColor) && preg_match('/^#[0-9A-Fa-f]{6}$/', $headerColor) ? $headerColor : '#0d6e42';
    $accentColor = ! empty($accentColor) && preg_match('/^#[0-9A-Fa-f]{6}$/', $accentColor) ? $accentColor : '#0d2818';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $collegeName }} - {{ config('app.name', 'CLSU') }}">
    <title>{{ $collegeName }} - {{ config('app.name', 'CLSU') }}</title>

    <!-- Includes: CSS & Fonts -->
    @include('includes.college-css')
    <style>
        .explore-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            align-items: stretch;
        }

        /* About Section Carousel Styles */
        .college-about-media .carousel {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .college-about-media .carousel-item.active img {
            transform: scale(1.02);
        }

        .college-about-media .carousel-control-prev,
        .college-about-media .carousel-control-next {
            width: 5%;
            opacity: 0.8;
        }

        .college-about-media .carousel-control-prev:hover,
        .college-about-media .carousel-control-next:hover {
            opacity: 1;
        }

        .college-about-media .carousel-indicators {
            bottom: 15px;
        }

        .college-about-media .carousel-indicators button {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin: 0 4px;
            background-color: rgba(255, 255, 255, 0.7);
            border: none;
        }

        .college-about-media .carousel-indicators button.active {
            background-color: #ffffff;
        }



        /* Accreditation Preview Styles */
        .accreditation-pill {
            background: #ffffff;
            padding: 0.6rem 1.25rem;
            border-radius: 99px;
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            min-width: 200px;
        }
        .accreditation-pill:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.06) !important;
            border-color: {{ $headerColor }}44;
        }
        .pill-logo {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pill-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .pill-content {
            display: flex;
            flex-direction: column;
            text-align: left;
            line-height: 1.2;
        }
        .pill-agency {
            font-weight: 800;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .pill-level {
            font-weight: 600;
            font-size: 0.75rem;
            color: #4a5568;
        }

        /* Org Hover Card Styles */
        .org-hover-card {
            width: 140px;
            height: 140px;
            cursor: pointer;
        }
        .org-hover-card .org-logo-wrap {
            transition: all 0.3s ease;
        }
        .org-hover-card:hover .org-logo-wrap {
            opacity: 0.15;
            transform: scale(0.95);
        }
        .org-hover-card .org-overlay {
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(2px);
            opacity: 0;
            transition: all 0.3s ease;
        }
        .org-hover-card:hover .org-overlay {
            opacity: 1;
        }

        /* Keep department cards in a manual slider with no auto-scroll animation. */
        .programs-slider {
            overflow: hidden;
            position: relative;
            padding: 0 3rem;
        }
        @media (max-width: 767.98px) {
            .programs-slider {
                padding: 0 1.5rem;
            }
        }
        #programsKnowSliderTrack {
            display: flex;
            gap: 1.5rem;
            overflow-x: auto;
            overflow-y: hidden;
            scroll-snap-type: x mandatory;
            scroll-behavior: auto;
            padding: 1rem 0;
            -ms-overflow-style: none; /* IE and Edge */
            scrollbar-width: none; /* Firefox */
            user-select: none;
            cursor: default;
        }
        #programsKnowSliderTrack::-webkit-scrollbar { 
            display: none; 
            width: 0;
            height: 0;
            background: transparent;
        }
        #programsKnowSliderTrack .program-card {
            flex: 0 0 300px;
            max-width: 85vw;
            scroll-snap-align: center;
        }
        @media (max-width: 767.98px) {
            #programsKnowSliderTrack .program-card {
                flex: 0 0 100%;
                max-width: none;
            }
        }
        .programs-know-prev,
        .programs-know-next {
            position: absolute;
            display: grid !important;
            top: 50%;
            transform: translateY(-50%);
            z-index: 5;
        }
        .programs-know-prev {
            left: 0.25rem;
        }
        .programs-know-next {
            right: 0.25rem;
        }

        @media (max-width: 767.98px) {
            /* Removed .about-header responsive layout definitions */

            .explore-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="retro-modern college-page">
    <!-- Includes: Header -->
    @include('includes.college-header')

    <main>
        <!-- Hero -->
        <section id="home" class="hero retro-hero">
            <div class="hero-slider-container">
            <div class="hero-slider">
                @if(isset($retroList) && $retroList->count() > 0)
                    @foreach($retroList as $index => $item)
                    <div class="hero-slide {{ $index === 0 ? 'active' : '' }}">
                        <div class="hero-slide-bg">
                            <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($item->background_image ?: $adminDefaultHeroUrl) }}" alt="{{ $collegeName }}">
                            <div class="hero-overlay"></div>
                        </div>
                        <div class="hero-slide-content">
                            <div class="hero-text">
                                <span class="section-badge retro-label college-theme" style="{{ $headerColor ? 'background-color: ' . $headerColor . ' !important;' : '' }} padding: 0.5rem 1.25rem; {{ $item->stamp_size ? 'font-size: ' . $item->stamp_size . 'px;' : '' }}">{{ $item->stamp ? strtoupper($item->stamp) : strtoupper($collegeName) }}</span>
                                <h1 class="retro-title" style="{{ $item->title_size ? 'font-size: ' . $item->title_size . 'px;' : '' }}">{{ $item->title ?: 'Welcome to ' . $collegeName }}</h1>
                                <div class="retro-description">{!! $item->description ? $item->description : 'Quality education and research in line with the University and national thrusts.' !!}</div>
                                <div class="hero-buttons">
                                    @if($showPrimaryRetroBtn ?? true)
                                        <button class="btn btn-primary retro-button">Explore Programs</button>
                                    @endif
                                    
                                    @if($showSecondaryRetroBtn ?? true)
                                        <a href="{{ route('college.faculty', $collegeSlug) }}" class="btn btn-secondary retro-button-outline">Meet the Faculty</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="hero-slide active">
                        <div class="hero-slide-bg">
                            <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl((isset($retro) && $retro->background_image) ? $retro->background_image : $adminDefaultHeroUrl) }}" alt="{{ $collegeName }}">
                            <div class="hero-overlay"></div>
                        </div>
                        <div class="hero-slide-content">
                            <div class="hero-text">
                                <!-- No buttons when no items configured -->
                            </div>
                        </div>
                    </div>
                @endif
                </div>

            <!-- Hero Slider Navigation Arrows -->
            <button class="hero-slider-arrow hero-prev" id="heroPrev">‹</button>
            <button class="hero-slider-arrow hero-next" id="heroNext">›</button>
            </div>
        </section>

        @php
            $overviewVisible = !isset($overviewSection) || $overviewSection->is_visible;
            $departmentsVisible = !isset($departmentsSection) || $departmentsSection->is_visible;
            $organizationsVisible = isset($organizationsSection) ? (bool) $organizationsSection->is_visible : true;
            $facilitiesVisible = !isset($facilitiesSection) || $facilitiesSection->is_visible;
            $accreditationVisible = isset($accreditationSection) ? (bool) $accreditationSection->is_visible : true;
            $membershipVisible = isset($membershipSection) ? (bool) $membershipSection->is_visible : true;
        @endphp

        <!-- About -->
        @if($overviewVisible)
        <section id="about" class="testimonials college-about">
            <div class="container">
                <div class="row align-items-center gy-4">
                    <div class="col-lg-6">
                        <div class="about-header text-center text-lg-start">
                            <span class="section-badge retro-label college-theme d-block d-lg-inline-block mx-auto mx-lg-0" style="padding: 0.5rem 1.25rem; letter-spacing: 2px; width: fit-content;">About the College</span>
                            <h2 class="retro-section-title mt-3">{{ $collegeName }}</h2>
                        </div>
                        <p class="retro-section-text">
                            @if(isset($overviewSection) && !empty($overviewSection->body))
                                {!! $overviewSection->body !!}
                            @else
                                {{ $collegeName }} is one of the colleges in the Central Luzon State University, committed to quality education and research in line with the University and national thrusts.
                            @endif
                        </p>
                    </div>
                    <div class="col-lg-6">
                        <div class="college-about-media">
                            @php
                                $aboutImages = $collegeModel?->about_images ?? [];
                                if (!is_array($aboutImages)) {
                                    $aboutImages = json_decode($aboutImages, true) ?? [];
                                }
                            @endphp

                            @if(!empty($aboutImages))
                                <div id="about-carousel" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        @foreach($aboutImages as $index => $imageUrl)
                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                <img src="{{ $imageUrl }}" alt="{{ $collegeName }} - Image {{ $index + 1 }}" class="d-block w-100 rounded">
                                            </div>
                                        @endforeach
                                    </div>

                                    @if(count($aboutImages) > 1)
                                        <button class="carousel-control-prev" type="button" data-bs-target="#about-carousel" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#about-carousel" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>

                                        <div class="carousel-indicators">
                                            @foreach($aboutImages as $index => $imageUrl)
                                                <button type="button" data-bs-target="#about-carousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @else
                                <img src="{{ $collegeLogoUrl }}" alt="{{ $collegeName }}">
                            @endif
                        </div>
                    </div>
                </div>

                @if($organizationsVisible && isset($organizationPreview) && $organizationPreview->isNotEmpty())
                <div id="organizations" class="mt-5 pt-5 border-top">
                    <div class="section-header d-flex flex-column align-items-center mb-4">
                        <span class="section-badge retro-label college-theme" style="padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; width: fit-content; text-transform: uppercase;">STUDENT ORGANIZATIONS</span>

                        @if(isset($organizationsSection) && !empty($organizationsSection->body))
                            <div class="retro-section-text text-center mx-auto mt-3" style="max-width: 800px;">
                                {!! $organizationsSection->body !!}
                            </div>
                        @else
                            <p class="retro-section-text text-center mx-auto mt-3" style="max-width: 800px;">Get involved with {{ $collegeName }} student organizations.</p>
                        @endif
                    </div>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        @foreach($organizationPreview as $index => $org)
                            @php
                                // Link directly to the organization detail page (fallback to ID if acronym is missing)
                                $orgRouteKey = $org->acronym ?: $org->id;
                                $orgLink = route('college.organization.show', ['college' => $collegeSlug, 'organization' => $orgRouteKey]);
                            @endphp
                            <a href="{{ $orgLink }}" class="bg-white rounded shadow-sm border transition-hover position-relative overflow-hidden org-item org-hover-card {{ $index >= 3 ? 'd-none' : '' }}" style="text-decoration: none;">
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center org-logo-wrap p-3">
                                    @if($org->logo)
                                        @php
                                            $logoUrl = \App\Providers\AppServiceProvider::resolveLogoUrl($org->logo);
                                        @endphp
                                        <img src="{{ $logoUrl }}" alt="{{ $org->acronym ?? $org->name }}" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                                    @elseif($collegeLogoUrl)
                                        <img src="{{ $collegeLogoUrl }}" alt="{{ $collegeName }} Logo" class="img-fluid opacity-50" style="max-height: 100%; object-fit: contain; filter: grayscale(100%);">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 60px; height: 60px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="{{ $headerColor }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="org-overlay position-absolute w-100 h-100 d-flex flex-column align-items-center justify-content-center text-center p-2" style="top: 0; left: 0; color: white;">
                                    <span class="fw-bold lh-1 mb-1" style="font-size: 0.95rem;">{{ $org->acronym ?? $org->name }}</span>
                                    @if($org->acronym && $org->name !== $org->acronym)
                                        <span class="small lh-1 mt-1" style="font-size: 0.70rem; color: #e2e8f0;">{{ Str::limit($org->name, 45) }}</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                    @if($organizationPreview->count() > 3)
                        <div class="text-center mt-4">
                            <button class="btn rounded-pill px-4 py-2 fw-bold" id="show-more-orgs-btn" onclick="toggleOrgs()" style="border: 2px solid {{ $accentColor }}; color: {{ $accentColor }}; background: transparent; transition: all 0.3s ease;" onmouseover="this.style.background='{{ $accentColor }}'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='{{ $accentColor }}';">
                                Show More Organizations <i class="bi bi-chevron-down ms-1"></i>
                            </button>
                        </div>
                        <script>
                            function toggleOrgs() {
                                const btn = document.getElementById('show-more-orgs-btn');
                                const hiddenOrgs = document.querySelectorAll('.org-item.d-none');
                                const shownOrgs = document.querySelectorAll('.org-item.shown-org');

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
                @endif
            </div>
        </section>
        @endif

        <!-- Departments -->
    @if($departmentsVisible && $departments->isNotEmpty())
    <section id="programs" class="programs py-5">
        <div class="container">
            <div class="section-header">
                <span class="section-badge retro-label college-theme" style="padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; align-self: center; width: fit-content;">Get to Know</span>
                <h2 class="retro-section-title">Departments of the College</h2>
                <p class="retro-section-text">Explore the departments that shape industry-ready graduates.</p>
            </div>

            @php
                $departmentCount = $departments->count();
            @endphp
            
            @if($departmentCount > 4)
                {{-- Slider layout for more than 4 departments --}}
                <div class="programs-slider">
                    <div class="programs-slider-track" id="programsKnowSliderTrack">
                        @foreach ($departments as $department)
                            @php
                                $deptOverview = $department->getSection('overview');
                                $cardImage = \App\Providers\AppServiceProvider::resolveLogoUrl($deptOverview['card_image'] ?? $collegeLogoUrl);
                            @endphp
                            <div class="program-card retro-card" style="background-image: url('{{ $cardImage }}'); background-size: cover; background-position: center;">
                                <div class="program-card-overlay" style="background: rgba(0, 0, 0, 0.7);"></div>
                                <div class="retro-card-border"></div>
                                <h3 class="retro-card-title" style="position: relative; z-index: 2; color: #fff;">{{ $department->name }}</h3>
                                <p class="retro-card-text" style="position: relative; z-index: 2; color: #f0f0f0;">{{ Str::limit(strip_tags($department->details ?? ''), 150) ?: 'Explore this department to learn more about its programs and opportunities.' }}</p>
                                <a href="{{ route('college.department.show', ['college' => $collegeSlug, 'department' => $department]) }}" class="link-button retro-link" style="position: relative; z-index: 2;">Learn more →</a>
                            </div>
                        @endforeach
                    </div>
                    <button class="programs-slider-arrow programs-know-prev" id="programsKnowPrev">‹</button>
                    <button class="programs-slider-arrow programs-know-next" id="programsKnowNext">›</button>
                </div>
            @else
                {{-- Grid layout for 4 or fewer departments --}}
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @foreach ($departments as $department)
                        @php
                            $deptOverview = $department->getSection('overview');
                            $cardImage = \App\Providers\AppServiceProvider::resolveLogoUrl($deptOverview['card_image'] ?? $collegeLogoUrl);
                        @endphp
                        <div class="col">
                        <div class="program-card retro-card" style="background-image: url('{{ $cardImage }}'); background-size: cover; background-position: center;">
                            <div class="program-card-overlay" style="background: rgba(0, 0, 0, 0.7);"></div>
                            <div class="retro-card-border"></div>
                            <h3 class="retro-card-title" style="position: relative; z-index: 2; color: #fff;">{{ $department->name }}</h3>
                            <p class="retro-card-text" style="position: relative; z-index: 2; color: #f0f0f0;">{{ Str::limit(strip_tags($department->details ?? ''), 150) ?: 'Explore this department to learn more about its programs and opportunities.' }}</p>
                            <a href="{{ route('college.department.show', ['college' => $collegeSlug, 'department' => $department]) }}" class="link-button retro-link" style="position: relative; z-index: 2;">Learn more →</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
    @endif

    <!-- Explore Laboratories and Facilities -->
    @if($facilitiesVisible && $facilities->isNotEmpty())
    <section id="explore" class="programs">
        <div class="container">
            <div class="section-header">
                <span class="section-badge retro-label college-theme" style="padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; align-self: center; width: fit-content;">Explore</span>
                <h2 class="retro-section-title">{{ !empty($facilitiesSection->title) ? $facilitiesSection->title : 'Laboratories and Facilities of the College' }}</h2>
                <p class="retro-section-text text-center mx-auto">{{ !empty($facilitiesSection->body) ? strip_tags($facilitiesSection->body) : 'Specialized spaces that support instruction, research, and practical training.' }}</p>
            </div>

            <div class="explore-grid">
                @foreach ($facilities as $facility)
                    @php
                        $facilityImage = \App\Providers\AppServiceProvider::resolveLogoUrl($facility->photo ?: $collegeLogoUrl);
                    @endphp
                    <div class="program-card retro-card">
                        <div class="program-card-overlay" style="background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('{{ $facilityImage }}'); background-size: cover; background-position: center;"></div>
                        <div class="retro-card-border"></div>
                        <h3 class="retro-card-title" style="position: relative; z-index: 2; color: #fff;">
                            <a href="{{ route('college.facility.show', ['college' => $collegeSlug, 'facility' => $facility]) }}" style="color: #fff; text-decoration: none;" class="stretched-link">
                                {{ $facility->name }}
                            </a>
                        </h3>
                        <p class="retro-card-text" style="position: relative; z-index: 2; color: #f0f0f0;">{{ Str::limit(strip_tags($facility->description ?? ''), 150) ?: 'Explore this facility to learn more about its resources and capabilities.' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Featured Video Section -->
    @if($featuredVideo && $featuredVideo->is_visible && $featuredVideo->video_type && ($featuredVideo->video_url || $featuredVideo->video_file))
    <section id="featured-video" class="py-5">
        <div class="container">
            <div class="section-header text-center mb-4">
                <span class="section-badge retro-label college-theme" style="padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; align-self: center; width: fit-content;">Watch</span>
                <h2 class="retro-section-title mt-3">{{ $featuredVideo->video_title ?: 'Featured Video' }}</h2>
                @if($featuredVideo->video_description)
                <p class="retro-section-text mx-auto mt-2">{{ $featuredVideo->video_description }}</p>
                @else
                <p class="retro-section-text mx-auto mt-2">Highlights from {{ $collegeName }}—programs, facilities, and community.</p>
                @endif
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    @if($featuredVideo->video_type === 'url' && $featuredVideo->video_url)
                        {{-- URL Embed --}}
                        @php
                            $url = $featuredVideo->video_url;
                            $embedUrl = null;
                            
                            // YouTube
                            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
                                $embedUrl = 'https://www.youtube.com/embed/' . $matches[1];
                            }
                            // Vimeo
                            elseif (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
                                $embedUrl = 'https://player.vimeo.com/video/' . $matches[1];
                            }
                            // Direct URL (mp4, webm, etc.)
                            elseif (preg_match('/\.(mp4|webm|ogg)$/i', $url)) {
                                $embedUrl = 'direct';
                            }
                        @endphp
                        
                        @if ($embedUrl === 'direct')
                            <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow-sm college-featured-video">
                                <video controls class="w-100 h-100">
                                    <source src="{{ $url }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        @elseif ($embedUrl)
                            <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow-sm college-featured-video">
                                <iframe src="{{ $embedUrl }}" title="{{ $featuredVideo->video_title ?: 'Featured Video' }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                            </div>
                        @else
                            <div class="text-center p-5 bg-light rounded-3">
                                <p class="text-muted mb-2">Unable to embed video.</p>
                                <a href="{{ $url }}" target="_blank" class="btn btn-primary retro-button">View Video</a>
                            </div>
                        @endif
                    @elseif($featuredVideo->video_type === 'file' && $featuredVideo->video_file)
                        {{-- Uploaded File --}}
                        <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow-sm college-featured-video">
                            <video controls class="w-100 h-100">
                                <source src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($featuredVideo->video_file) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Extension Section -->
    @if($extensions->isNotEmpty() && (!isset($extensionSection) || $extensionSection->is_visible))
    <section id="extension" class="programs py-5 bg-light">
        <div class="container">
            <div class="section-header text-center mb-4">
                <span class="section-badge retro-label college-theme" style="padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; align-self: center; width: fit-content;">Extension</span>
                <h2 class="retro-section-title mt-3">{{ $extensionSection->title ?? 'Extension Activities' }}</h2>
                <div class="retro-section-text mx-auto mt-2" style="max-width: 800px;">{!! $extensionSection->body ?? '<p>Community engagement and outreach programs.</p>' !!}</div>
            </div>

            <div class="row g-4 justify-content-center">
                @foreach($extensions as $item)
                    <div class="col-md-6 col-lg-4">
                        <div class="program-card retro-card h-100" style="min-height: 250px;">
                             @php
                                $bgImage = \App\Providers\AppServiceProvider::resolveLogoUrl(!empty($item->image) ? $item->image : $collegeLogoUrl);
                            @endphp
                            <div class="program-card-overlay" style="background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8)), url('{{ $bgImage }}'); background-size: cover; background-position: center;"></div>
                            <div class="retro-card-border"></div>
                            <div class="d-flex flex-column h-100 justify-content-end p-4" style="position: relative; z-index: 2;">
                                <h3 class="retro-card-title text-white mb-2">{{ $item->title ?? 'Extension Activity' }}</h3>
                                <p class="retro-card-text text-white-50 small mb-0">{{ Str::limit(strip_tags($item->description ?? ''), 120) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Training Section -->
    @if($trainings->isNotEmpty() && (!isset($trainingSection) || $trainingSection->is_visible))
    <section id="training" class="programs py-5">
        <div class="container">
            <div class="section-header text-center mb-4">
                <span class="section-badge retro-label college-theme" style="padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; align-self: center; width: fit-content;">Training</span>
                <h2 class="retro-section-title mt-3">{{ $trainingSection->title ?? 'Trainings & Workshops' }}</h2>
                <div class="retro-section-text mx-auto mt-2" style="max-width: 800px;">{!! $trainingSection->body ?? '<p>Capacity building and skills development.</p>' !!}</div>
            </div>

            <div class="row g-4 justify-content-center">
                 @foreach($trainings as $training)
                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('college.training.show', ['college' => $collegeSlug, 'slug' => \Illuminate\Support\Str::slug($training->title)]) }}" class="text-decoration-none">
                            <div class="program-card retro-card h-100" style="min-height: 250px; cursor: pointer;">
                                 @php
                                    $bgImage = \App\Providers\AppServiceProvider::resolveLogoUrl(!empty($training->image) ? $training->image : $collegeLogoUrl);
                                @endphp
                                <div class="program-card-overlay" style="background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.85)), url('{{ $bgImage }}'); background-size: cover; background-position: center;"></div>
                                <div class="retro-card-border"></div>
                                <div class="d-flex flex-column h-100 justify-content-end p-4" style="position: relative; z-index: 2;">
                                    <h3 class="retro-card-title text-white mb-2">{{ $training->title ?? 'Training Workshop' }}</h3>
                                    <p class="retro-card-text text-white-50 small mb-3">{{ Str::limit(strip_tags($training->description ?? ''), 120) }}</p>
                                    <span class="text-white-50 small" style="opacity: 0.7;">View details →</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('college.training', $collegeSlug) }}" class="link-button retro-link mx-auto">See all training programs →</a>
            </div>
        </div>
    </section>
    @endif

    <!-- Admissions Section -->
    @if($admissionsSection && $admissionsSection->is_visible)
    <section id="admissions" class="programs py-5 bg-light">
        <div class="container">
            <div class="section-header text-center mb-4">
                <span class="section-badge retro-label college-theme" style="padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; align-self: center; width: fit-content;">Admissions</span>
                <h2 class="retro-section-title mt-3">{{ $admissionsSection->title ?? 'Admissions' }}</h2>
                <div class="retro-section-text mx-auto mt-2 text-center" style="max-width: 800px;">
                    {!! $admissionsSection->body !!}
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Scholarships Section --}}
    @if(isset($scholarships) && $scholarships->isNotEmpty() && (!isset($scholarshipsSection) || $scholarshipsSection->is_visible))
    <section id="scholarships" class="programs py-5 bg-light">
        <div class="container">
            <div class="section-header text-center mb-4">
                <span class="section-badge retro-label college-theme" style="padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; align-self: center; width: fit-content;">{{ $scholarshipsSection->title ?? 'Scholarships' }}</span>
                <h2 class="retro-section-title mt-3">{{ $scholarshipsSection->title ?? 'Scholarships' }}</h2>
                <p class="retro-section-text mx-auto mt-2">{!! $scholarshipsSection->body ?? 'Scholarship programs and opportunities for students.' !!}</p>
            </div>

            <div class="row g-4 justify-content-center">
                @foreach($scholarships as $scholarship)
                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('college.scholarship.show', ['college' => $collegeSlug, 'slug' => Str::slug($scholarship->title)]) }}" class="text-decoration-none">
                            <div class="program-card retro-card h-100" style="min-height: 280px; cursor: pointer;">
                                @php
                                    $bgImage = \App\Providers\AppServiceProvider::resolveLogoUrl(!empty($scholarship->image) ? $scholarship->image : $collegeLogoUrl);
                                @endphp
                                <div class="program-card-overlay" style="background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.85)), url('{{ $bgImage }}'); background-size: cover; background-position: center;"></div>
                                <div class="retro-card-border"></div>
                                <div class="d-flex flex-column h-100 justify-content-end p-4" style="position: relative; z-index: 2;">
                                    <h3 class="retro-card-title text-white mb-2">{{ $scholarship->title ?? 'Scholarship Program' }}</h3>
                                    <p class="retro-card-text text-white-50 small mb-3">{{ Str::limit(strip_tags($scholarship->description ?? ''), 120) }}</p>
                                    <span class="text-white-50 small" style="opacity: 0.7;">View details →</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('college.scholarships', $collegeSlug) }}" class="link-button retro-link mx-auto">See all scholarship programs →</a>
            </div>
        </div>
    </section>
    @endif

    <!-- Institutes Section -->
    @if(isset($institutes) && $institutes->isNotEmpty() && (!isset($institutesSection) || $institutesSection->is_visible))
    <section id="institutes" class="programs py-5 bg-light">
        <div class="container">
            <div class="section-header text-center mb-4">
                <span class="section-badge retro-label college-theme" style="padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; align-self: center; width: fit-content;">{{ $institutesSection->title ?? 'Institutes' }}</span>
                <h2 class="retro-section-title mt-3">{{ $institutesSection->title ?? 'Institutes' }}</h2>
                <div class="retro-section-text mx-auto mt-2" style="max-width: 800px;">{!! $institutesSection->body ?? '<p>Research centers and specialized institutes.</p>' !!}</div>
            </div>

            <div class="row g-4 justify-content-center">
                @foreach($institutes as $institute)
                    <div class="col-md-6 col-lg-4">
                        <div class="program-card retro-card h-100" style="min-height: 250px;">
                            @php
                                $bgImage = $collegeLogoUrl;
                                $targetImg = $institute->card_image ?: ($institute->logo ?: ('images/' . $institute->photo));
                                if ($targetImg) {
                                    $bgImage = \App\Providers\AppServiceProvider::resolveLogoUrl($targetImg);
                                }
                            @endphp
                            <style>
                                .program-card .program-card-overlay {
                                    transition: background-color 0.3s ease;
                                }
                                .program-card:hover .program-card-overlay {
                                    background-color: rgba(0, 0, 0, 0.7) !important; /* Darken on hover */
                                    background-blend-mode: overlay;
                                }
                                .program-card .institute-content {
                                    opacity: 0;
                                    transform: translateY(20px);
                                    transition: all 0.3s ease;
                                }
                                .program-card:hover .institute-content {
                                    opacity: 1;
                                    transform: translateY(0);
                                }
                            </style>
                            <div class="program-card-overlay" style="background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('{{ $bgImage }}'); background-size: cover, contain; background-repeat: no-repeat, no-repeat; background-position: center;"></div>
                            <div class="retro-card-border"></div>
                            <div class="institute-content d-flex flex-column h-100 justify-content-end p-4" style="position: relative; z-index: 2;">
                                <h3 class="retro-card-title text-white mb-2">
                                    <a href="{{ route('college.institute.show', ['college' => $collegeSlug, 'sectionSlug' => Str::slug($institutesSection?->title ?? 'Institutes'), 'institute' => $institute->id]) }}" class="text-white text-decoration-none stretched-link">
                                        {{ $institute->name }}
                                    </a>
                                </h3>
                                <p class="retro-card-text text-white-50 small mb-0">{{ Str::limit($institute->description ?? '', 120) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Testimonials Section -->
    @if(isset($testimonialPreview) && $testimonialPreview->isNotEmpty())
    <section id="testimonials" class="py-5" style="background: #ffffff;">
        <div class="container">
            <div class="section-header text-center mb-5">
                <span class="section-badge retro-label college-theme" style="padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; align-self: center; width: fit-content;">Testimonials</span>
                <h2 class="retro-section-title mt-3">What They Say</h2>
                <p class="retro-section-text mx-auto mt-2">Voices from our {{ $collegeShortName }} community.</p>
            </div>

            <div class="row g-4">
                @foreach($testimonialPreview as $testimonial)
                    <div class="col-md-4">
                        <div class="testimonial-card p-4 rounded-4 shadow-sm border-0 h-100" style="background: #f8f9fa;">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                @if($testimonial->image)
                                    <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($testimonial->image) }}" alt="" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 50px; height: 50px;">
                                        {{ substr($testimonial->title, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <h5 class="mb-0 fw-700" style="font-size: 1rem;">{{ $testimonial->title }}</h5>
                                    <small class="text-muted">{{ $testimonial->department->name ?? 'CLSU' }}</small>
                                </div>
                            </div>
                            <div class="mb-0 text-dark" style="font-style: italic; line-height: 1.6; font-size: 0.95rem;">{!! $testimonial->description !!}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('college.testimonials', $collegeSlug) }}" class="btn retro-button-outline" style="border-color: {{ $headerColor }}; color: {{ $headerColor }};">View All Stories →</a>
            </div>
        </div>
    </section>
    @endif

    <!-- Accreditation Section -->
    @if(($accreditationVisible && isset($accreditationPreview) && $accreditationPreview->isNotEmpty()) || ($membershipVisible && isset($membershipPreview) && $membershipPreview->isNotEmpty()))
    <section id="accreditation" class="py-5" style="background: #eff2f0;">
        <div class="container text-center">
            <div class="section-header mb-5">
                <span class="section-badge retro-label college-theme" style="padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; align-self: center; width: fit-content; text-transform: uppercase;">
                    {{ $accreditationSection->title ?? 'Accreditation & Recognition' }}
                </span>
                <h2 class="retro-section-title mt-3">
                    {{ isset($accreditationSection->meta['hero_title']) ? $accreditationSection->meta['hero_title'] : 'Commitment' }}
                </h2>
            </div>
            
            <div class="row g-4 justify-content-center mt-4 mb-5 text-start">
                @if($accreditationVisible && isset($accreditationPreview) && $accreditationPreview->isNotEmpty())
                    <div class="col-lg-{{ (isset($membershipPreview) && $membershipPreview->isNotEmpty()) ? '6' : '10' }}">
                        <div class="bg-white p-4 h-100" style="border: 2px solid #e2e8f0; border-top: 4px solid {{ $headerColor }}; box-shadow: 4px 4px 0 rgba(0,0,0,0.05);">
                            <h5 class="text-uppercase fw-bold mb-4 d-flex align-items-center gap-2" style="font-size: 0.95rem; letter-spacing: 1px; color: #2d3748;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $headerColor }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M12 15v5s3 2 5 2 5-2 5-2v-5"/><path d="M12 15V3a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v12s3 2 5 2 5-2 5-2Z"/><path d="M22 15s-3-2-5-2-5 2-5 2"/></svg>
                                Accreditations
                            </h5>
                            <div class="d-flex flex-column gap-3">
                                @foreach($accreditationPreview as $acc)
                                    <div class="d-flex align-items-center gap-3 p-3 bg-white" style="border: 1px solid #cbd5e1; box-shadow: 2px 2px 0 rgba(0,0,0,0.05);">
                                        <div class="d-flex align-items-center justify-content-center bg-white" style="width: 50px; height: 50px; border: 1px solid #e2e8f0; padding: 5px;">
                                            @if($acc->logo)
                                                @php $logoUrl = \App\Providers\AppServiceProvider::resolveLogoUrl($acc->logo); @endphp
                                                <img src="{{ $logoUrl }}" alt="{{ $acc->agency }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                            @else
                                                <img src="{{ asset('images/accreditation_placeholder.webp') }}" alt="" class="opacity-50" style="max-width: 100%;">
                                            @endif
                                        </div>
                                        <div class="text-start">
                                            <span class="d-block fw-bold text-uppercase" style="color: {{ $headerColor }}; font-size: 0.95rem; letter-spacing: 0.5px;" title="{{ $acc->agency }}">{{ $acc->agency_acronym }}</span>
                                            <span class="d-inline-block mt-1 px-2 py-1 bg-light text-muted fw-600" style="font-size: 0.7rem; letter-spacing: 0.5px; border: 1px solid #e2e8f0;">{{ $acc->level }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if($membershipVisible && isset($membershipPreview) && $membershipPreview->isNotEmpty())
                    <div class="col-lg-{{ ($accreditationVisible && isset($accreditationPreview) && $accreditationPreview->isNotEmpty()) ? '6' : '10' }}">
                        <div class="bg-white p-4 h-100" style="border: 2px solid #e2e8f0; border-top: 4px solid {{ $headerColor }}; box-shadow: 4px 4px 0 rgba(0,0,0,0.05);">
                            <h5 class="text-uppercase fw-bold mb-4 d-flex align-items-center gap-2" style="font-size: 0.95rem; letter-spacing: 1px; color: #2d3748;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $headerColor }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                Memberships
                            </h5>
                            <div class="d-flex flex-column gap-3">
                                @foreach($membershipPreview as $membership)
                                    <div class="d-flex align-items-center gap-3 p-3 bg-white" style="border: 1px solid #cbd5e1; border-left: 4px solid {{ $headerColor }} !important; box-shadow: 2px 2px 0 rgba(0,0,0,0.05);">
                                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-light" style="width: 45px; height: 45px; border: 1px solid #e2e8f0;">
                                            @if($membership->logo)
                                                @php $logoUrl = \App\Providers\AppServiceProvider::resolveLogoUrl($membership->logo); @endphp
                                                <img src="{{ $logoUrl }}" alt="" style="height: 24px; width: 24px; object-fit: contain;">
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $headerColor }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-dark text-uppercase" style="font-size: 0.9rem; letter-spacing: 0.5px;">{{ $membership->organization }}</h6>
                                            <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                                                @if(!empty($membership->membership_type))
                                                    <span class="text-uppercase fw-600 px-2 py-1" style="background: {{ $headerColor }}10; color: {{ $headerColor }}; font-size: 0.65rem; letter-spacing: 0.5px; border: 1px solid {{ $headerColor }}30;">{{ $membership->membership_type }}</span>
                                                @endif
                                                @if($membership->department)
                                                    <span class="text-secondary fw-600 px-2 py-1 bg-light" style="font-size: 0.65rem; letter-spacing: 0.25px; border: 1px solid #e2e8f0;">{{ $membership->department->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <a href="{{ route('college.accreditation', $collegeSlug) }}" class="link-button retro-link mx-auto">Learn about our standards →</a>
        </div>
    </section>
    @endif

    <!-- News and Announcement Board Section -->
    <section id="news-announcement-board" class="news news-announcement-board py-5">
        <div class="container">
            <div class="section-header text-center mb-4">
                <span class="section-badge retro-label college-theme" style="padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; align-self: center; width: fit-content;">Board</span>
                <h2 class="retro-section-title mt-3">News and Announcement Board</h2>
                <p class="retro-section-text mx-auto mt-2">Latest news and announcements from {{ $collegeName }}.</p>
            </div>

            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-3 mt-4">
                @forelse ($articles as $article)
                    <div class="col">
                    <a class="event-card rounded-3" href="{{ route($article->route_name, ['college' => $collegeSlug, 'slug' => $article->slug]) }}">
                        <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($article->banner ?: $collegeLogoUrl) }}" alt="{{ $article->title }}" style="{{ !$article->banner ? 'object-fit: contain; padding: 2rem; background: #f8f9fa;' : '' }}">
                        <div class="event-date-badge">
                            <span class="month">{{ $article->published_at->format('M') }}</span>
                            <span class="day">{{ $article->published_at->format('d') }}</span>
                        </div>
                        <div class="event-card-content">
                            <span class="event-card-tag {{ strtolower($article->type) }}">{{ ucfirst($article->type) }}</span>
                            <p class="event-card-title">{{ $article->title }}</p>
                        </div>
                    </a>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">No news or announcements available at this time.</p>
                    </div>
                @endforelse
            </div>

            <div class="events-link text-center mt-4">
                @if($collegeSlug === 'engineering')
                <a href="{{ route('news.announcement.board', $collegeSlug) }}">View All News &amp; Announcements →</a>
                @else
                <a href="{{ route('news.announcement.board', $collegeSlug) }}">View All News &amp; Announcements →</a>
                @endif
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    @if($faqs->isNotEmpty())
    <section id="faq" class="py-5 faq-section bg-light">
        <div class="container">
            <div class="text-center faq-intro mb-5">
                <span class="section-badge retro-label college-theme" style="padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; align-self: center; width: fit-content;">FAQ</span>
                <h2 class="retro-section-title mt-3">{{ $faqSection->title ?? 'Frequently Asked Questions' }}</h2>
                @if(isset($faqSection) && !empty($faqSection->body))
                    <div class="testimonials-subtitle">{!! $faqSection->body !!}</div>
                @else
                    <p class="testimonials-subtitle">Quick answers to common questions about {{ $collegeName }}.</p>
                @endif
            </div>

            <div class="faq-grid">
                @forelse ($faqs as $faq)
                    <div class="faq-card rounded-3 shadow-sm">
                        <div class="faq-card-header d-flex align-items-center gap-3">
                            <div class="faq-card-icon mb-0">
                                🔔
                            </div>
                            <h5 class="faq-card-title mb-0">{{ $faq->question }}</h5>
                        </div>
                        <div class="faq-card-body">
                            <div class="faq-card-text">
                                {!! $faq->answer !!}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">No FAQs available at this time.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    @endif

    </main>

    <!-- Footer -->
    @include('includes.college-footer', ['department' => null, 'institute' => null])

    <!-- Includes: Scripts -->
    @include('includes.college-scripts')
</body>
</html>
