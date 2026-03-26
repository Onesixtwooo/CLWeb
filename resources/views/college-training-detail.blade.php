<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $trainingItem->title ?? 'Training' }} - {{ $collegeName }}, {{ config('app.name', 'CLSU') }}">
    <title>{{ $trainingItem->title ?? 'Training Details' }} - {{ $collegeName }} - {{ config('app.name', 'CLSU') }}</title>

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

        .news-detail-hero {
            background: {{ $headerColor }};
            color: #ffffff;
            padding: 3rem 0 2rem;
            margin-top: 120px;
        }
        .news-detail-hero .retro-section-title { color: #ffffff; font-size: 1.75rem; margin-bottom: 0; text-decoration: none; border: none; }
        .news-detail-hero-title-wrap { display: inline-block; }
        .news-detail-hero-underline {
            display: block;
            width: 66%;
            max-width: 420px;
            height: 3px;
            background: #eab308;
            margin-top: 0.75rem;
        }
        .news-detail-breadcrumb {
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .news-detail-breadcrumb a { color: rgba(255,255,255,0.9); text-decoration: none; }
        .news-detail-breadcrumb a:hover { color: #fff; text-decoration: underline; }
        .news-detail-breadcrumb span { color: rgba(255,255,255,0.7); }
        .article-banner {
            position: relative;
            min-height: 320px;
            overflow: hidden;
            background: {{ $headerColor }};
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
        .article-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .article-category {
            color: {{ $headerColor }};
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .article-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }
        .article-meta-divider {
            height: 1px;
            background: #e5e7eb;
            margin-bottom: 1rem;
        }
        .article-meta-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem 1.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: #4b5563;
        }
        .article-meta-author-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .article-meta-author-date span.sep {
            color: #9ca3af;
            user-select: none;
        }
        .article-share {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .article-share a {
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
        .article-share a:hover { color: {{ $headerColor }}; }
        .article-share a[title="Print"] { font-size: 1.1rem; }
        .article-body {
            font-size: 1rem;
            color: #374151;
            line-height: 1.75;
        }
        .article-body p { margin-bottom: 1rem; }
        .article-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: {{ $headerColor }};
            font-weight: 600;
            text-decoration: none;
            margin-bottom: 1.5rem;
        }
        .article-back:hover { 
            color: {{ $headerColor }}; 
            filter: brightness(0.85);
        }
        .news-sidebar-box {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .news-sidebar-title {
            font-size: 1rem;
            font-weight: 700;
            color: {{ $headerColor }};
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid {{ $headerColor }};
        }
        .news-sidebar-item {
            display: block;
            padding: 0.6rem 0;
            border-bottom: 1px solid #f3f4f6;
            text-decoration: none;
            color: inherit;
            transition: color 0.2s;
        }
        .news-sidebar-item:last-child { border-bottom: none; }
        .news-sidebar-item:hover { color: {{ $headerColor }}; }
        .news-sidebar-item-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #111827;
            line-height: 1.35;
            margin-bottom: 0.2rem;
        }
        .news-sidebar-item-date {
            font-size: 0.8rem;
            color: #6b7280;
        }
    </style>
</head>
<body class="retro-modern college-page">
    <!-- Header -->
    @include('includes.college-header')

    <main>
        <!-- Hero -->
        <section class="news-detail-hero">
            <div class="container">
                <nav class="news-detail-breadcrumb" aria-label="Breadcrumb">
                    <a href="{{ route('college.show', $collegeSlug) }}">Home</a>
                    <span aria-hidden="true"> / </span>
                    <a href="{{ route('college.training', $collegeSlug) }}">Training &amp; Workshops</a>
                    <span aria-hidden="true"> / </span>
                    <span>{{ $trainingItem->title ?? 'Training Details' }}</span>
                </nav>
                <div class="news-detail-hero-title-wrap">
                    <h1 class="retro-section-title">{{ $trainingItem->title ?? 'Training Details' }}</h1>
                    <span class="news-detail-hero-underline" aria-hidden="true"></span>
                </div>
            </div>
        </section>

        <!-- Article -->
        <section class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <a href="{{ route('college.training', $collegeSlug) }}" class="article-back">&larr; Back to Trainings &amp; Workshops</a>

                        @if(!empty($trainingItem->image))
                            <div class="article-banner">
                                <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($trainingItem->image) }}" alt="{{ $trainingItem->title ?? 'Training Image' }}">
                            </div>
                        @elseif($collegeLogoUrl)
                            <div class="article-banner" style="display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                <img src="{{ $collegeLogoUrl }}" alt="Logo" style="object-fit: contain; width: 200px; height: 200px; opacity: 0.8;">
                            </div>
                        @endif

                        <p class="article-category">Workshop</p>
                        <h2 class="article-title">{{ $trainingItem->title ?? 'Training Details' }}</h2>
                        <div class="article-meta-divider"></div>
                        <div class="article-meta-row">
                            <div class="article-meta-author-date">
                                <span>{{ $collegeName }}</span>
                                @if(!empty($trainingItem->created_at))
                                <span class="sep">|</span>
                                <span>{{ $trainingItem->created_at->format('F j, Y') }}</span>
                                @endif
                            </div>
                            <div class="article-share">
                                <a href="#" onclick="window.print(); return false;" title="Print" aria-label="Print">🖨</a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer" title="Share on Facebook" aria-label="Share on Facebook">f</a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer" title="Share on LinkedIn" aria-label="Share on LinkedIn">in</a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($trainingItem->title ?? 'Training') }}" target="_blank" rel="noopener noreferrer" title="Share on X (Twitter)" aria-label="Share on X">𝕏</a>
                            </div>
                        </div>
                        <div class="article-body">
                            {!! $trainingItem->description !!}
                        </div>
                    </div>
                    <aside class="col-lg-4 mt-5 mt-lg-0">
                        @if($recentTrainings->isNotEmpty())
                        <div class="news-sidebar-box">
                            <h3 class="news-sidebar-title">Other Trainings &amp; Workshops</h3>
                            @foreach($recentTrainings as $recent)
                            <a href="{{ route('college.training.show', ['college' => $collegeSlug, 'slug' => \Illuminate\Support\Str::slug($recent->title)]) }}" class="news-sidebar-item">
                                <span class="news-sidebar-item-title">{{ $recent->title ?? 'Training Program' }}</span>
                                @if(!empty($recent->created_at))
                                <span class="news-sidebar-item-date">{{ $recent->created_at->format('F j, Y') }}</span>
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
