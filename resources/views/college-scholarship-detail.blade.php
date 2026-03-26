<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $scholarshipItem['title'] ?? 'Scholarship' }} - {{ $collegeName }}, {{ config('app.name', 'CLSU') }}">
    <title>{{ $scholarshipItem['title'] ?? 'Scholarship Details' }} - {{ $collegeName }} - {{ config('app.name', 'CLSU') }}</title>

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
        
        /* Override header colors */
        .engineering-top-header,
        .engineering-top-header::after {
            background: {{ $accentColor }} !important;
        }
        .engineering-header,
        .engineering-navbar {
            background: {{ $headerColor }} !important;
        }

        .scholarship-hero {
            background: {{ $headerColor }};
            color: #ffffff;
            padding: 3rem 0 2rem;
            margin-top: 120px;
        }
        .scholarship-hero .retro-section-title { color: #ffffff; font-size: 1.75rem; margin-bottom: 0; text-decoration: none; border: none; }
        .scholarship-hero-title-wrap { display: inline-block; }
        .scholarship-hero-underline {
            display: block;
            width: 66%;
            max-width: 420px;
            height: 3px;
            background: #eab308;
            margin-top: 0.75rem;
        }
        .scholarship-breadcrumb {
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .scholarship-breadcrumb a { color: rgba(255,255,255,0.9); text-decoration: none; }
        .scholarship-breadcrumb a:hover { color: #fff; text-decoration: underline; }
        .scholarship-breadcrumb span { color: rgba(255,255,255,0.7); }
        .scholarship-banner {
            position: relative;
            width: 100%;
            height: 320px;
            overflow: hidden;
            background: #f8f9fa;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .scholarship-banner img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
        .scholarship-category {
            color: {{ $headerColor }};
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .scholarship-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }
        .scholarship-meta-divider {
            height: 1px;
            background: #e5e7eb;
            margin-bottom: 1rem;
        }
        .scholarship-meta-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem 1.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: #4b5563;
        }
        .scholarship-body {
            font-size: 1rem;
            color: #374151;
            line-height: 1.75;
        }
        .scholarship-body p { margin-bottom: 1rem; }
        .scholarship-body ul, .scholarship-body ol { padding-left: 1.5rem; margin-bottom: 1rem; }
        .scholarship-body li { margin-bottom: 0.35rem; }
        .scholarship-body img { max-width: 100%; height: auto; border-radius: 0.375rem; }
        .scholarship-body a { color: inherit; text-decoration: underline; }
        /* Rich-text card content */
        .rich-card-body { font-size: 0.95rem; color: #374151; line-height: 1.75; }
        .rich-card-body p { margin-bottom: 0.75rem; }
        .rich-card-body ul, .rich-card-body ol { padding-left: 1.5rem; margin-bottom: 0.75rem; }
        .rich-card-body li { margin-bottom: 0.3rem; }
        .rich-card-body img { max-width: 100%; height: auto; border-radius: 0.375rem; }
        .rich-card-body strong { font-weight: 600; }
        .rich-card-body a { color: inherit; text-decoration: underline; }
        .scholarship-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: {{ $headerColor }};
            font-weight: 600;
            text-decoration: none;
            margin-bottom: 1.5rem;
        }
        .scholarship-back:hover { 
            color: {{ $headerColor }}; 
            filter: brightness(0.85);
        }
        .scholarship-detail-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .scholarship-detail-card h4 {
            font-size: 1rem;
            font-weight: 700;
            color: {{ $headerColor }};
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid {{ $headerColor }};
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .scholarship-detail-card .detail-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .scholarship-detail-card .detail-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.95rem;
            color: #374151;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }
        .scholarship-detail-card .detail-list li:last-child { border-bottom: none; }
        .scholarship-detail-card .detail-list li .bullet {
            flex-shrink: 0;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: {{ $headerColor }};
            margin-top: 0.5rem;
        }
        .scholarship-sidebar-box {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .scholarship-sidebar-title {
            font-size: 1rem;
            font-weight: 700;
            color: {{ $headerColor }};
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid {{ $headerColor }};
        }
        .scholarship-sidebar-item {
            display: block;
            padding: 0.6rem 0;
            border-bottom: 1px solid #f3f4f6;
            text-decoration: none;
            color: inherit;
            transition: color 0.2s;
        }
        .scholarship-sidebar-item:last-child { border-bottom: none; }
        .scholarship-sidebar-item:hover { color: {{ $headerColor }}; }
        .scholarship-sidebar-item-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #111827;
            line-height: 1.35;
            margin-bottom: 0.2rem;
        }
        .scholarship-sidebar-item-date {
            font-size: 0.8rem;
            color: #6b7280;
        }
        .scholarship-share {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .scholarship-share a {
            color: #4b5563;
            text-decoration: none;
            transition: color 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            font-size: 1rem;
            font-weight: 600;
        }
        .scholarship-share a:hover { color: {{ $headerColor }}; }
    </style>
</head>
<body class="retro-modern college-page">
    <!-- Header -->
    @include('includes.college-header')

    <main>
        <!-- Hero -->
        <section class="scholarship-hero">
            <div class="container">
                <nav class="scholarship-breadcrumb" aria-label="Breadcrumb">
                    <a href="{{ route('college.show', $collegeSlug) }}">Home</a>
                    <span aria-hidden="true"> / </span>
                    <a href="{{ route('college.show', $collegeSlug) }}#scholarships">Scholarships</a>
                    <span aria-hidden="true"> / </span>
                    <span>{{ $scholarshipItem['title'] ?? 'Scholarship Details' }}</span>
                </nav>
                <div class="scholarship-hero-title-wrap">
                    <h1 class="retro-section-title">{{ $scholarshipItem['title'] ?? 'Scholarship Details' }}</h1>
                    <span class="scholarship-hero-underline" aria-hidden="true"></span>
                </div>
            </div>
        </section>

        <!-- Content -->
        <section class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <a href="{{ route('college.show', $collegeSlug) }}#scholarships" class="scholarship-back">&larr; Back to Scholarships</a>

                        @if(!empty($scholarshipItem['image']))
                            <div class="scholarship-banner">
                                <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($scholarshipItem['image']) }}" alt="{{ $scholarshipItem['title'] ?? 'Scholarship Image' }}">
                            </div>
                        @elseif($collegeLogoUrl)
                            <div class="scholarship-banner" style="display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                <img src="{{ $collegeLogoUrl }}" alt="Logo" style="object-fit: contain; width: 200px; height: 200px; opacity: 0.8;">
                            </div>
                        @endif

                        <p class="scholarship-category">Scholarship Program</p>
                        <h2 class="scholarship-title">{{ $scholarshipItem['title'] ?? 'Scholarship Details' }}</h2>
                        <div class="scholarship-meta-divider"></div>
                        <div class="scholarship-meta-row">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span>{{ $collegeName }}</span>
                                @if(!empty($scholarshipItem['created_at']))
                                <span style="color: #9ca3af; user-select: none;">|</span>
                                <span>{{ \Carbon\Carbon::parse($scholarshipItem['created_at'])->format('F j, Y') }}</span>
                                @endif
                            </div>
                            <div class="scholarship-share">
                                <a href="#" onclick="window.print(); return false;" title="Print" aria-label="Print">🖨</a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer" title="Share on Facebook" aria-label="Share on Facebook">f</a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($scholarshipItem['title'] ?? 'Scholarship') }}" target="_blank" rel="noopener noreferrer" title="Share on X (Twitter)" aria-label="Share on X">𝕏</a>
                            </div>
                        </div>

                        <!-- Description -->
                        @if(!empty($scholarshipItem['description']))
                        <div class="scholarship-body mb-4">
                            {!! $scholarshipItem['description'] !!}
                        </div>
                        @endif

                        <!-- Qualifications -->
                        @if(!empty($scholarshipItem['qualifications']))
                        <div class="scholarship-detail-card">
                            <h4>📋 Qualifications</h4>
                            <div class="rich-card-body">{!! $scholarshipItem['qualifications'] !!}</div>
                        </div>
                        @endif

                        <!-- Requirements -->
                        @if(!empty($scholarshipItem['requirements']))
                        <div class="scholarship-detail-card">
                            <h4>📄 Requirements</h4>
                            <div class="rich-card-body">{!! $scholarshipItem['requirements'] !!}</div>
                        </div>
                        @endif

                        <!-- Application Process -->
                        @if(!empty($scholarshipItem['process']))
                        <div class="scholarship-detail-card">
                            <h4>🔄 Application Process</h4>
                            <div class="rich-card-body">{!! $scholarshipItem['process'] !!}</div>
                        </div>
                        @endif

                        <!-- Benefits -->
                        @if(!empty($scholarshipItem['benefits']))
                        <div class="scholarship-detail-card">
                            <h4>🎁 Benefits</h4>
                            <div class="rich-card-body">{!! $scholarshipItem['benefits'] !!}</div>
                        </div>
                        @endif
                    </div>

                    <aside class="col-lg-4 mt-5 mt-lg-0">
                        @if(count($otherScholarships) > 0)
                        <div class="scholarship-sidebar-box">
                            <h3 class="scholarship-sidebar-title">Other Scholarships</h3>
                            @foreach($otherScholarships as $index => $other)
                            <a href="{{ route('college.scholarship.show', ['college' => $collegeSlug, 'slug' => Str::slug($other['title'] ?? 'scholarship')]) }}" class="scholarship-sidebar-item">
                                <span class="scholarship-sidebar-item-title">{{ $other['title'] ?? 'Scholarship Program' }}</span>
                                @if(!empty($other['description']))
                                <span class="scholarship-sidebar-item-date">{{ Str::limit(strip_tags($other['description']), 60) }}</span>
                                @endif
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </aside>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('includes.college-footer')

    <!-- Includes: Scripts -->
    @include('includes.college-scripts')
</body>
</html>
