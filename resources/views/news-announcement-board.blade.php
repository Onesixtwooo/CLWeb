<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="News and Announcement Board - College of Engineering, {{ config('app.name', 'CLSU') }}">
    <title>News and Announcement Board - College of Engineering - {{ config('app.name', 'CLSU') }}</title>

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

        .news-board-hero {
            background: {{ $headerColor }};
            color: #ffffff;
            padding: 4rem 0 3rem;
            margin-top: 120px;
        }
        .news-board-hero .retro-section-title { color: #ffffff; }

        /* Override header colors */
        .engineering-top-header,
        .engineering-top-header::after {
            background: {{ $accentColor }} !important;
        }
        .engineering-header,
        .engineering-navbar {
            background: {{ $headerColor }} !important;
        }

        /* New card layout: banner + date tag + category + title + description */
        .news-board-card {
            display: block;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            text-decoration: none;
            color: inherit;
            transition: box-shadow 0.2s ease, transform 0.2s ease;
            position: relative;
        }
        .news-board-card:hover {
            box-shadow: 0 12px 32px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }
        /* Banner: whole section is one image, with date tag overlapping */
        .news-board-card-banner {
            position: relative;
            min-height: 280px;
            overflow: visible;
            background: {{ $headerColor }};
            z-index: 2;
        }
        .news-board-card-banner img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .news-board-banner-placeholder {
            position: absolute;
            inset: 0;
            background: {{ $headerColor }};
            color: rgba(255,255,255,0.8);
            font-size: 0.85rem;
            padding: 1.5rem;
            display: none;
            align-items: center;
            justify-content: center;
            text-align: center;
            flex-direction: column;
        }
        .news-board-banner-placeholder.banner-dark {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        }
        .news-board-date-tag {
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            background: {{ $headerColor }};
            color: #fff;
            padding: 0.5rem 1.25rem;
            font-size: 0.85rem;
            font-weight: 700;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            z-index: 10;
        }
        .news-board-card-body {
            padding: 2rem 1.25rem 1.25rem 1.25rem;
            position: relative;
            z-index: 1;
            margin-top: -1px;
        }
        .news-board-category {
            color: {{ $headerColor }};
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .news-board-card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
        .news-board-card-desc {
            font-size: 0.9rem;
            color: #6b7280;
            line-height: 1.5;
            margin: 0;
        }
        .news-board-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }
        @media (max-width: 991px) {
            .news-board-grid { grid-template-columns: 1fr; }
        }
        .news-board-card.pagination-hidden { display: none !important; }
        .news-board-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.35rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .news-board-pagination .page-link,
        .news-board-pagination .page-item span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.25rem;
            height: 2.25rem;
            padding: 0 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: {{ $headerColor }};
            background: #fff;
            border: 1px solid #dee2e6;
            text-decoration: none;
            transition: background 0.2s, border-color 0.2s, color 0.2s;
        }
        .news-board-pagination .page-link:hover {
            background: #f8f9fa;
            border-color: {{ $headerColor }};
            color: {{ $headerColor }};
        }
        .news-board-pagination .page-item.active .page-link,
        .news-board-pagination .page-item.active span {
            background: {{ $headerColor }};
            border-color: {{ $headerColor }};
            color: #fff;
        }
        .news-board-pagination .page-item.disabled .page-link,
        .news-board-pagination .page-item.disabled span {
            color: #adb5bd;
            pointer-events: none;
        }
    </style>
</head>
<body class="retro-modern college-page">
    <!-- Includes: Header -->
    @include('includes.college-header')

    <main>
        <!-- Hero -->
        <section class="news-board-hero">
            <div class="container text-center">
                <span class="section-badge retro-label" style="background: rgba(255,255,255,0.2); color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px;">Board</span>
                <h1 class="retro-section-title mt-3 mb-2">News and Announcement Board</h1>
                <p class="retro-section-text mx-auto mb-0" style="color: rgba(255,255,255,0.9);">Latest news and announcements from the College of Engineering, CLSU.</p>
            </div>
        </section>

        <!-- News Section -->
        <section id="news" class="py-5">
            <div class="container">
                <div class="section-header mb-4">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; text-transform: uppercase;">News</span>
                </div>
                <div class="college-page">
                    <div class="news-board-grid" id="news-grid" data-per-page="3">
                        @forelse($newsArticles as $article)
                        <a class="news-board-card" href="{{ route('news.announcement.detail', ['college' => $collegeSlug, 'slug' => $article->slug]) }}">
                            <div class="news-board-card-banner">
                                @php
                                    $bannerSrc = \App\Providers\AppServiceProvider::resolveLogoUrl($article->banner ?: $collegeLogoUrl);
                                    $bannerStyle = !$article->banner ? 'object-fit: contain; padding: 2rem; background: #f8f9fa;' : '';
                                @endphp
                                <img src="{{ $bannerSrc }}" alt="{{ $article->title }}" style="{{ $bannerStyle }}">
                                <span class="news-board-date-tag">{{ $article->published_at->format('F j, Y') }}</span>
                            </div>
                            <div class="news-board-card-body">
                                <p class="news-board-category">{{ $article->category ?? 'News' }}</p>
                                <h3 class="news-board-card-title">{{ $article->title }}</h3>
                                <p class="news-board-card-desc">{{ Str::limit(strip_tags($article->body), 150) }}</p>
                            </div>
                        </a>
                        @empty
                        <div class="col-12 text-center py-5">
                            <p class="text-muted">No news articles available at this time.</p>
                        </div>
                        @endforelse
                    </div>
                    <nav class="news-board-pagination" id="news-pagination" aria-label="News pagination">
                        <ul class="pagination mb-0 flex-wrap justify-content-center gap-1">
                            <li class="page-item disabled" data-page="prev"><a class="page-link" href="#" data-go="prev">Previous</a></li>
                            <li class="page-item" data-page="next"><a class="page-link" href="#" data-go="next">Next</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </section>

        <!-- Announcements Section -->
        <section id="announcements" class="py-5 bg-light">
            <div class="container">
                <div class="section-header mb-4">
                    <span class="section-badge retro-label" style="background: {{ $headerColor }}; color: #ffffff; padding: 0.5rem 1.25rem; display: inline-block; letter-spacing: 2px; text-transform: uppercase;">Announcements</span>
                </div>
                <div class="college-page">
                    <div class="news-board-grid" id="announcements-grid" data-per-page="3">
                        @forelse($announcements as $announcement)
                        <a class="news-board-card" href="{{ route($announcement->route_name, ['college' => $collegeSlug, 'slug' => $announcement->slug]) }}">
                            <div class="news-board-card-banner">
                                @php
                                    $annBannerSrc = \App\Providers\AppServiceProvider::resolveLogoUrl($announcement->banner ?: $collegeLogoUrl);
                                    $annBannerStyle = !$announcement->banner ? 'object-fit: contain; padding: 2rem; background: #f8f9fa;' : '';
                                @endphp
                                <img src="{{ $annBannerSrc }}" alt="{{ $announcement->title }}" style="{{ $annBannerStyle }}">
                                <span class="news-board-date-tag">{{ $announcement->published_at->format('F j, Y') }}</span>
                            </div>
                            <div class="news-board-card-body">
                                <p class="news-board-category">Announcement</p>
                                <h3 class="news-board-card-title">{{ $announcement->title }}</h3>
                                <p class="news-board-card-desc">{{ Str::limit(strip_tags($announcement->body), 150) }}</p>
                            </div>
                        </a>
                        @empty
                        <div class="col-12 text-center py-5">
                            <p class="text-muted">No announcements available at this time.</p>
                        </div>
                        @endforelse
                    </div>
                    <nav class="news-board-pagination" id="announcements-pagination" aria-label="Announcements pagination">
                        <ul class="pagination mb-0 flex-wrap justify-content-center gap-1">
                            <li class="page-item disabled" data-page="prev"><a class="page-link" href="#" data-go="prev">Previous</a></li>
                            <li class="page-item" data-page="next"><a class="page-link" href="#" data-go="next">Next</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('includes.college-footer')

    <!-- Includes: Scripts -->
    @include('includes.college-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function initPagination(grid) {
                var cards = grid.querySelectorAll('.news-board-card');
                var perPage = parseInt(grid.dataset.perPage || '3', 10);
                var totalPages = Math.max(1, Math.ceil(cards.length / perPage));
                var container = grid.closest('.container');
                var nav = container ? container.querySelector('.news-board-pagination') : null;
                if (!nav || cards.length === 0) return;
                var paginationList = nav.querySelector('.pagination');
                var prevItem = paginationList.querySelector('[data-page="prev"]');
                var nextItem = paginationList.querySelector('[data-page="next"]');
                var currentPage = 1;

                paginationList.querySelectorAll('.page-item[data-page]:not([data-page="prev"]):not([data-page="next"])').forEach(function (item) {
                    item.remove();
                });

                for (var page = 1; page <= totalPages; page++) {
                    var pageItem = document.createElement('li');
                    pageItem.className = 'page-item';
                    pageItem.dataset.page = String(page);

                    var pageLink = document.createElement('a');
                    pageLink.className = 'page-link';
                    pageLink.href = '#';
                    pageLink.dataset.go = String(page);
                    pageLink.textContent = String(page);

                    pageItem.appendChild(pageLink);
                    paginationList.insertBefore(pageItem, nextItem);
                }

                if (totalPages <= 1) {
                    nav.style.display = 'none';
                } else {
                    nav.style.display = '';
                }

                function showPage(page) {
                    currentPage = page;
                    var start = (page - 1) * perPage;
                    var end = start + perPage;
                    cards.forEach(function (card, i) {
                        if (i >= start && i < end) {
                            card.classList.remove('pagination-hidden');
                        } else {
                            card.classList.add('pagination-hidden');
                        }
                    });
                    nav.querySelectorAll('.page-item').forEach(function (item) {
                        var p = item.dataset.page;
                        item.classList.remove('active', 'disabled');
                        if (p === 'prev') {
                            if (page <= 1) item.classList.add('disabled');
                        } else if (p === 'next') {
                            if (page >= totalPages) item.classList.add('disabled');
                        } else {
                            var num = parseInt(p, 10);
                            if (num === page) item.classList.add('active');
                        }
                    });
                }
                nav.addEventListener('click', function (e) {
                    var link = e.target.closest('a[data-go]');
                    if (!link) return;
                    e.preventDefault();
                    var go = link.getAttribute('data-go');
                    if (go === 'next') {
                        if (currentPage < totalPages) showPage(currentPage + 1);
                    } else if (go === 'prev') {
                        if (currentPage > 1) showPage(currentPage - 1);
                    } else {
                        var num = parseInt(go, 10);
                        if (num >= 1 && num <= totalPages) showPage(num);
                    }
                });
                showPage(1);
            }
            document.querySelectorAll('.news-board-grid').forEach(initPagination);
        });
    </script>
</body>
</html>
