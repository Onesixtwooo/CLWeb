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
    <meta name="description" content="Testimonials - {{ $collegeName }}, {{ config('app.name', 'CLSU') }}">
    <title>Testimonials - {{ $collegeName }} - {{ config('app.name', 'CLSU') }}</title>

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
        .testimonial-grid {
            align-items: stretch;
        }
        .testimonial-card {
            --testimonial-primary: {{ $headerColor }};
            --testimonial-accent: {{ $accentColor }};
            background:
                linear-gradient(160deg, color-mix(in srgb, var(--testimonial-primary) 78%, white 22%) 0%, var(--testimonial-primary) 52%, color-mix(in srgb, var(--testimonial-accent) 82%, black 18%) 100%);
            border-radius: 28px;
            overflow: hidden;
            border: 0;
            box-shadow: 0 22px 46px rgba(15, 23, 42, 0.18);
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            isolation: isolate;
        }
        .testimonial-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 28px 56px rgba(15, 23, 42, 0.24);
        }
        .testimonial-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.28), transparent 34%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.08), transparent 46%);
            pointer-events: none;
            z-index: -1;
        }
        .testimonial-body {
            padding: 2rem;
            min-height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1.1rem;
        }
        .author-photo {
            width: 74px;
            height: 74px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.22);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.26);
            flex-shrink: 0;
        }
        .author-info h4 {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 0.2rem;
            color: #ffffff;
            letter-spacing: 0.01em;
        }
        .author-info p {
            font-size: 0.92rem;
            color: rgba(255, 255, 255, 0.78);
            margin-bottom: 0.2rem;
        }
        .author-info p:last-child {
            margin-bottom: 0;
        }
        .author-photo-placeholder {
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
            font-weight: 800;
            font-size: 1.5rem;
        }
        @media (max-width: 767.98px) {
            .testimonial-body {
                padding: 1.6rem;
            }
            .author-photo {
                width: 64px;
                height: 64px;
            }
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
                <h1 class="display-3 fw-900 text-white mb-3">Our Voices</h1>
                <p class="lead text-white-50 mx-auto" style="max-width: 700px; font-size: 1.25rem;">Hear from our students, alumni, and faculty about their journey at the {{ $collegeName }}.</p>
            </div>
        </section>

        <!-- Content -->
        <section class="py-5 bg-white">
            <div class="container">
                @if($testimonials->isEmpty())
                    <div class="text-center py-5 my-5">
                        <div class="mb-4 opacity-25">
                            <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-muted">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </div>
                        <h2 class="h3 font-weight-bold text-dark mb-3">No testimonials shared yet.</h2>
                        <p class="text-muted mb-4">We are gathering stories from our community. Check back soon for inspiring journeys.</p>
                        <a href="{{ route('college.show', $collegeSlug) }}" class="btn rounded-pill px-5 py-3 fw-700 shadow-sm" style="background: {{ $headerColor }}; color: white; border: none;">Back to {{ $collegeShortName }} Home</a>
                    </div>
                @else
                    <div class="row g-4 testimonial-grid">
                        @foreach($testimonials as $testimonial)
                            <div class="col-md-6 col-lg-4">
                                <div class="testimonial-card">
                                    <div class="testimonial-body">
                                        <div class="testimonial-author">
                                            @if($testimonial->image)
                                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($testimonial->image) }}" alt="{{ $testimonial->title }}" class="author-photo">
                                            @else
                                                <div class="author-photo author-photo-placeholder d-flex align-items-center justify-content-center">
                                                    {{ substr($testimonial->title, 0, 1) }}
                                                </div>
                                            @endif
                                            <div class="author-info">
                                                <h4>{{ $testimonial->title }}</h4>
                                                <p>{{ $testimonial->department->name ?? $collegeName }}</p>
                                                @if($testimonial->year_graduated)
                                                    <p class="small">{{ $testimonial->year_graduated }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($testimonials->hasPages())
                        <div class="mt-5 d-flex justify-content-center">
                            {{ $testimonials->links() }}
                        </div>
                    @endif
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
