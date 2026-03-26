<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $sectionTitle }} - {{ $collegeName }}, {{ config('app.name', 'CLSU') }}">
    <title>{{ $sectionTitle }} - {{ $collegeName }} - {{ config('app.name', 'CLSU') }}</title>

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
        .retro-section-text, .retro-section-text * {
            text-align: center !important;
            color: #ffffff !important;
            background-color: transparent !important;
            background: transparent !important;
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

        /* New card layout: banner + category + title + description */
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
        /* Banner */
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
        /* Removed .news-board-category, .news-board-card-title, .news-board-card-desc layout definitions */
        /* Removed .news-board-grid layout definitions */
        .news-board-card[data-page].pagination-hidden { display: none !important; }
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
                <h1 class="retro-section-title mt-3 mb-2">{{ $sectionTitle }}</h1>
                <div class="retro-section-text mx-auto mb-0">{!! $sectionDescription !!}</div>
            </div>
        </section>

        <!-- Scholarship Section -->
        <section id="scholarships" class="py-5">
            <div class="container">
                <div class="college-page">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="scholarships-grid" data-per-page="9">
                        @forelse($scholarshipItems as $item)
                        <a href="{{ route('college.scholarship.show', ['college' => $collegeSlug, 'slug' => \Illuminate\Support\Str::slug($item->title)]) }}" class="col news-board-card text-decoration-none">
                            <div class="news-board-card-banner">
                                <img src="{{ !empty($item->image) ? (\Illuminate\Support\Str::startsWith($item->image, 'http') ? $item->image : asset($item->image)) : $collegeLogoUrl }}" alt="{{ $item->title ?? 'Scholarship' }}" style="{{ empty($item->image) ? 'object-fit: contain; padding: 2rem; background: #f8f9fa;' : '' }}">
                                @if(!empty($item->created_at))
                                <span class="news-board-date-tag" style="position: absolute; bottom: -20px; left: 50%; transform: translateX(-50%); background: {{ $headerColor }}; color: #fff; padding: 0.5rem 1.25rem; font-size: 0.85rem; font-weight: 700; white-space: nowrap; box-shadow: 0 4px 12px rgba(0,0,0,0.25); z-index: 10;">
                                    {{ $item->created_at->format('F j, Y') }}
                                </span>
                                @endif
                            </div>
                            <div class="news-board-card-body p-4 pt-5" style="position: relative; z-index: 1; margin-top: -1px; padding-top: {{ !empty($item->created_at) ? '3.5rem !important' : '2.5rem' }};">
                                <p class="small fw-semibold text-uppercase mb-1" style="color: {{ $headerColor }}; letter-spacing: 0.05em;">Scholarship</p>
                                <h3 class="h6 fw-bold text-dark mb-1 lh-sm">{{ $item->title ?? 'No Title' }}</h3>
                                <p class="small text-secondary mb-0 lh-base">{{ Str::limit(strip_tags($item->description ?? ''), 150) }}</p>
                            </div>
                        </a>
                        @empty
                        <div class="col-12 text-center py-5" style="grid-column: 1 / -1;">
                            <p class="text-muted">No scholarships available at this time.</p>
                        </div>
                        @endforelse
                    </div>

                    @if($scholarshipItems->count() > 9)
                    <nav class="news-board-pagination" id="scholarships-pagination" aria-label="Scholarships pagination">
                        <ul class="pagination mb-0 flex-wrap justify-content-center gap-1">
                            <li class="page-item disabled" data-page="prev"><a class="page-link" href="#" data-go="prev">Previous</a></li>
                            @for($i = 1; $i <= ceil($scholarshipItems->count() / 9); $i++)
                                <li class="page-item {{ $i == 1 ? 'active' : '' }}" data-page="{{ $i }}"><a class="page-link" href="#" data-go="{{ $i }}">{{ $i }}</a></li>
                            @endfor
                            <li class="page-item" data-page="next"><a class="page-link" href="#" data-go="next">Next</a></li>
                        </ul>
                    </nav>
                    @endif
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
                var perPage = parseInt(grid.dataset.perPage || '9', 10);
                var totalPages = Math.max(1, Math.ceil(cards.length / perPage));
                var container = grid.closest('.container');
                var nav = container ? container.querySelector('.news-board-pagination') : null;
                if (!nav || cards.length === 0) return;
                var currentPage = 1;
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
