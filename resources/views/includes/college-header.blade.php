    <style>
    .engineering-top-header {
        background: #009639 !important;
    }
    @media (max-width: 767px) {
        .logo-full-text { font-size: 1rem !important; line-height: 1.2; display: block; }
        .logo-text p { font-size: 0.8rem !important; margin-bottom: 0 !important; }
        .logo-box { width: 40px !important; height: 40px !important; padding: 0.4rem !important; }
        .engineering-navbar { padding: 0.5rem 0.75rem !important; }
    }
    @media (max-width: 576px) {
        .logo-full-text { font-size: 0.9rem !important; }
        .retro-subtitle { font-size: 0.75rem !important; }
    }
    @media (max-width: 480px) {
        .logo-full-text {
            font-size: 0.82rem !important;
            line-height: 1.15;
            max-width: 12rem;
            white-space: normal;
            word-break: break-word;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
    }
    </style>

<!-- Loader screen -->
    <div id="engineering-loader" class="engineering-loader" aria-hidden="false" aria-label="Loading {{ $collegeName }}">
        <div class="engineering-loader-inner d-flex flex-column align-items-center text-center p-4 gap-3">
            <img src="{{ $collegeLogoUrl }}" alt="{{ $collegeName }}" class="engineering-loader-logo">
            <p class="engineering-loader-title mb-0">{{ $collegeName }}</p>
            <p class="engineering-loader-subtitle mb-0">Central Luzon State University</p>
            <div class="engineering-loader-spinner"></div>
        </div>
    </div>

    <!-- Fixed header wrapper: top bar + main nav so both stay visible -->
    <div class="engineering-header-wrapper">
        <!-- Top header bar (main contact bar above main header) -->
        @include('partials.college-top-header')

        <!-- Main header (hides on scroll; top bar above remains) -->
        <div class="engineering-nav-outer">
        <header class="header engineering-header">
        <nav class="navbar navbar-expand-md navbar-dark engineering-navbar">
            <div class="container">
                <div class="w-100 d-flex align-items-center justify-content-between gap-2">
                    <a href="{{ route('college.show', $collegeSlug ?? 'engineering') }}" class="navbar-brand d-flex align-items-center gap-2 logo">
                        <div class="logo-box retro-badge">
                            <img src="{{ $collegeLogoUrl }}" alt="{{ $collegeName }}" class="logo-image">
                        </div>
                        <div class="logo-text d-flex flex-column">
                            <h2 class="retro-heading mb-0">
                                <span class="logo-full-text">{{ strtoupper($collegeName) }}</span>
                            </h2>
                            <p class="retro-subtitle">
                                <span class="d-inline d-md-none">CLSU</span>
                                <span class="d-none d-md-inline">Central Luzon State University</span>
                            </p>
                        </div>
                    </a>

                    <button class="navbar-toggler ms-0 ms-md-3 order-md-2" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                            aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-md-0 align-items-md-center">
                        <li class="nav-item">
                            <a href="{{ route('college.show', $collegeSlug ?? 'engineering') }}" class="nav-link">Home</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="aboutDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                About
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
                                <li><a href="{{ route('college.show', $collegeSlug ?? 'engineering') }}#about" class="dropdown-item">About the College</a></li>
                                <li><a href="{{ route('college.faculty', $collegeSlug ?? 'engineering') }}" class="dropdown-item">Faculty & Staff</a></li>
                                <li><a href="{{ route('college.testimonials', $collegeSlug ?? 'engineering') }}" class="dropdown-item">Testimonials</a></li>
                                @if(!isset($accreditationSection) || $accreditationSection->is_visible)
                                    <li><a href="{{ route('college.accreditation', $collegeSlug ?? 'engineering') }}" class="dropdown-item">Accreditation</a></li>
                                @endif
                                @if(!isset($downloadsSection) || $downloadsSection->is_visible)
                                    <li><a href="{{ route('college.downloads', $collegeSlug ?? 'engineering') }}" class="dropdown-item">Downloads</a></li>
                                @endif
                                @if((!isset($organizationsSection) || $organizationsSection->is_visible) && (!isset($organizationPreview) || $organizationPreview->isNotEmpty()))
                                    <li><a href="{{ route('college.organizations', $collegeSlug ?? 'engineering') }}" class="dropdown-item">Student Organizations</a></li>
                                @endif
                            </ul>
                        </li>
                        @if(!isset($departmentsSection) || $departmentsSection->is_visible)
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" id="departmentsDropdown" role="button"
                                   data-bs-toggle="dropdown" aria-expanded="false">
                                    Departments
                                </a>
                                @php
                                    $headerDepartments = $departments ?? \App\Models\CollegeDepartment::where('college_slug', $collegeSlug ?? 'engineering')->orderBy('sort_order')->orderBy('name')->get();
                                @endphp
                                <ul class="dropdown-menu" aria-labelledby="departmentsDropdown">
                                    @forelse($headerDepartments as $department)
                                        <li><a href="{{ route('college.department.show', ['college' => $collegeSlug ?? 'engineering', 'department' => $department]) }}" class="dropdown-item">{{ $department->name }}</a></li>
                                    @empty
                                        <li><a href="#" class="dropdown-item text-muted">No departments available</a></li>
                                    @endforelse
                                </ul>
                            </li>
                        @endif
                        @if(isset($scholarshipsSection) && $scholarshipsSection && $scholarshipsSection->is_visible && isset($scholarships) && $scholarships->isNotEmpty())
                        <li class="nav-item">
                            <a href="{{ route('college.show', $collegeSlug ?? 'engineering') }}#scholarships" class="nav-link">Scholarships</a>
                        </li>
                        @endif
                        @if(isset($institutes) && $institutes->isNotEmpty() && (!isset($institutesSection) || $institutesSection->is_visible))
                        <li class="nav-item">
                            <a href="{{ route('college.show', $collegeSlug ?? 'engineering') }}#institutes" class="nav-link">Institutes</a>
                        </li>
                        @endif
                        @if(isset($extensionSection) && $extensionSection->is_visible && !empty($extensionSection->items))
                        <li class="nav-item">
                            <a href="{{ route('college.show', $collegeSlug ?? 'engineering') }}#extension" class="nav-link">Extension</a>
                        </li>
                        @endif
                        @if(isset($trainingSection) && $trainingSection->is_visible && !empty($trainingSection->items))
                        <li class="nav-item">
                            <a href="{{ route('college.show', $collegeSlug ?? 'engineering') }}#training" class="nav-link">Training</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
        </header>
        </div>
    </div>
