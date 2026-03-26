<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $article->title }} - {{ $collegeName }}, {{ config('app.name', 'CLSU') }}">
    <title>{{ $article->title }} - {{ $collegeName }} - {{ config('app.name', 'CLSU') }}</title>

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
        
        /* Override header colors to ensure they match */
        /* Override header colors to ensure they match */
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
            width: 100%;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .article-banner img {
            width: 100%;
            height: auto;
            max-height: 500px;
            object-fit: contain;
            display: block;
        }
        .article-banner-placeholder {
            position: absolute;
            inset: 0;
            background: {{ $headerColor }};
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
            padding: 2rem;
            display: none;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .article-banner-placeholder.banner-dark {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        }
        .article-category {
            color: {{ $headerColor }};
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
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
        .article-body p:last-child { margin-bottom: 0; }
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
                    <a href="{{ route('news.announcement.board', $collegeSlug) }}">News &amp; Announcement Board</a>
                    <span aria-hidden="true"> / </span>
                    <a href="{{ route('news.announcement.board', $collegeSlug) }}#{{ $article->type === 'news' ? 'news' : 'announcements' }}">{{ ucfirst($article->type) }}</a>
                    <span aria-hidden="true"> / </span>
                    <span>{{ $article->title }}</span>
                </nav>
                <div class="news-detail-hero-title-wrap">
                    <h1 class="retro-section-title">{{ $article->title }}</h1>
                    <span class="news-detail-hero-underline" aria-hidden="true"></span>
                </div>
            </div>
        </section>

        <!-- Article -->
        <section class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <a href="{{ route('news.announcement.board', $collegeSlug) }}#{{ $article->type === 'news' ? 'news' : 'announcements' }}" class="article-back">&larr; Back to {{ $article->type === 'news' ? 'News' : 'Announcements' }}</a>


                        @php
                            $galleryImages = $article->images ?? ($article->banner ? [$article->banner] : []);
                        @endphp

                        @if(count($galleryImages) > 1)
                            <div id="articleCarousel" class="carousel slide mb-4 rounded overflow-hidden" data-bs-ride="carousel">
                                <div class="carousel-indicators">
                                    @foreach($galleryImages as $index => $image)
                                        <button type="button" data-bs-target="#articleCarousel" data-bs-slide-to="{{ $index }}" 
                                                class="{{ $index === 0 ? 'active' : '' }}" 
                                                aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                                aria-label="Slide {{ $index + 1 }}"></button>
                                    @endforeach
                                </div>
                                <div class="carousel-inner">
                                    @foreach($galleryImages as $index => $image)
                                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                            <div style="aspect-ratio: 16/9; max-height: 500px; width: 100%; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                                @php
                                                    $_imgSrc = \App\Providers\AppServiceProvider::resolveLogoUrl($image);
                                                @endphp
                                                <img src="{{ $_imgSrc }}" class="d-block" alt="{{ $article->title }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#articleCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: drop-shadow(0 0 2px rgba(0,0,0,0.5));"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#articleCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true" style="filter: drop-shadow(0 0 2px rgba(0,0,0,0.5));"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                        @elseif(!empty($galleryImages))
                            <div class="article-banner">
                                @php
                                    $_bannerSrc = \App\Providers\AppServiceProvider::resolveLogoUrl($galleryImages[0]);
                                @endphp
                                <img src="{{ $_bannerSrc }}" alt="{{ $article->title }}">
                            </div>
                        @elseif($collegeLogoUrl)
                            <div class="article-banner" style="display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                <img src="{{ $collegeLogoUrl }}" alt="Logo" style="object-fit: contain; width: 200px; height: 200px; opacity: 0.8;">
                            </div>
                        @else
                           {{-- No banner and no logo --}}
                        @endif

                        <p class="article-category">{{ $article->category ?? ucfirst($article->type) }}</p>
                        <h2 class="article-title">{{ $article->title }}</h2>
                        <div class="article-meta-divider"></div>
                        <div class="article-meta-row">
                            <div class="article-meta-author-date">
                                <span>{{ $article->author ?? $collegeName . ' Communications' }}</span>
                                <span class="sep">|</span>
                                <span>{{ $article->published_at->format('F j, Y') }}</span>
                            </div>
                            <div class="article-share">
                                <a href="#" onclick="window.print(); return false;" title="Print" aria-label="Print">🖨</a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer" title="Share on Facebook" aria-label="Share on Facebook">f</a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer" title="Share on LinkedIn" aria-label="Share on LinkedIn">in</a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}" target="_blank" rel="noopener noreferrer" title="Share on X (Twitter)" aria-label="Share on X">𝕏</a>
                            </div>
                        </div>
                        <div class="article-body">
                            @php
                                $articleBody = (string) ($article->body ?? '');
                                $bodyHasHtml = $articleBody !== strip_tags($articleBody);
                                $plainParagraphs = preg_split("/\R{2,}/", trim($articleBody)) ?: [];
                                $plainParagraphs = array_values(array_filter(array_map('trim', $plainParagraphs), static fn ($paragraph) => $paragraph !== ''));
                            @endphp

                            @if($bodyHasHtml)
                                {!! $articleBody !!}
                            @else
                                @foreach($plainParagraphs as $paragraph)
                                    <p>{!! nl2br(e($paragraph)) !!}</p>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <aside class="col-lg-4 mt-5 mt-lg-0">
                        @if($relatedArticles->count() > 0)
                        <div class="news-sidebar-box">
                            <h3 class="news-sidebar-title">{{ $article->type === 'news' ? 'Related News' : 'Related Announcements' }}</h3>
                            @foreach($relatedArticles as $related)
                            <a href="{{ route('news.announcement.detail', ['college' => $collegeSlug, 'slug' => $related->slug]) }}" class="news-sidebar-item">
                                <span class="news-sidebar-item-title">{{ $related->title }}</span>
                                <span class="news-sidebar-item-date">{{ $related->published_at->format('F j, Y') }}</span>
                            </a>
                            @endforeach
                        </div>
                        @endif
                        @if($recentArticles->count() > 0)
                        <div class="news-sidebar-box">
                            <h3 class="news-sidebar-title">{{ $article->type === 'news' ? 'Recent News' : 'Recent Announcements' }}</h3>
                            @foreach($recentArticles as $recent)
                            <a href="{{ route('news.announcement.detail', ['college' => $collegeSlug, 'slug' => $recent->slug]) }}" class="news-sidebar-item">
                                <span class="news-sidebar-item-title">{{ $recent->title }}</span>
                                <span class="news-sidebar-item-date">{{ $recent->published_at->format('F j, Y') }}</span>
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
