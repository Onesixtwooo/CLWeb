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
    <meta name="description" content="Faculty - {{ $collegeName }}, {{ config('app.name', 'CLSU') }}">
    <title>Faculty - {{ $collegeName }} - {{ config('app.name', 'CLSU') }}</title>

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
        .faculty-card {
            background: #ffffff;
            border-radius: 0;
            overflow: hidden;
            border: 1px solid color-mix(in srgb, {{ $headerColor }} 14%, #e2e8f0 86%);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
        }
        .faculty-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .faculty-card-photo-wrap {
            background: #ffffff;
            width: 100%;
            border-bottom: 1px solid color-mix(in srgb, {{ $headerColor }} 18%, #e2e8f0 82%);
        }
        .faculty-card-photo {
            width: 100%;
            height: 220px;
            border-radius: 0;
            object-fit: cover;
            border: 0;
            box-shadow: none;
            background-color: #f3f4f6;
            display: block;
        }
        .faculty-card-body { 
            padding: 1.5rem; 
            color: #ffffff;
            flex: 1;
            display: flex;
            flex-direction: column;
            background: linear-gradient(
                180deg,
                color-mix(in srgb, {{ $headerColor }} 92%, #ffffff 8%) 0%,
                color-mix(in srgb, {{ $headerColor }} 78%, #0f172a 22%) 100%
            );
            border-top: 1px solid color-mix(in srgb, {{ $headerColor }} 24%, #ffffff 76%);
            text-align: center; /* Center text matches the reference roughly, or keep left? Reference has centered photo but left text. Let's try mixed. */
            text-align: left; /* Reset to left based on reference image text alignment */
        }
        .faculty-card-name {
            font-weight: 700;
            color: #ffffff;
            font-size: 1.125rem;
            margin-bottom: 0.25rem;
            line-height: 1.2;
        }
        .faculty-card-position {
            color: rgba(255,255,255,0.9);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .faculty-card-dept {
            color: rgba(255,255,255,0.7);
            font-size: 0.8rem;
            margin-bottom: 0.25rem;
            line-height: 1.4;
        }
        .faculty-card-email {
            font-size: 0.8rem;
            margin-top: auto; /* Push to bottom if needed */
            padding-top: 0.5rem;
        }
        .faculty-card-email a { 
            color: rgba(255,255,255,0.8); 
            text-decoration: none; 
            transition: color 0.15s;
        }
        .faculty-card-email a:hover { 
            color: #ffffff; 
            text-decoration: underline; 
        }
        @media (max-width: 767.98px) {
            .faculty-page-title {
                padding-top: 1.5rem !important;
                padding-bottom: 1.5rem !important;
            }
            .faculty-page-title .retro-section-title {
                font-size: clamp(2.1rem, 10vw, 3rem);
                line-height: 0.95;
            }
            .faculty-page-title .text-white {
                max-width: 18rem;
            }
            .faculty-group-header {
                align-items: flex-start !important;
                flex-wrap: wrap;
                gap: 0.75rem !important;
                margin-bottom: 1.25rem !important;
            }
            .faculty-group-title {
                width: 100%;
                font-size: 1.1rem;
                line-height: 1.2;
            }
            .faculty-group-divider {
                display: none;
            }
            .faculty-card-photo {
                height: 200px;
            }
            .faculty-card-body {
                padding: 1.2rem 1rem 1.1rem;
            }
        }
    </style>
</head>
<body class="retro-modern college-page">


    <!-- Includes: Header -->
    @include('includes.college-header')

    <main>
        <!-- Page title -->
        <section class="py-4 faculty-page-title" style="background: {{ $headerColor }};">
            <div class="container">
                <h1 class="retro-section-title text-white mb-0">{{ $sectionTitle }}</h1>
                <div class="text-white mb-0 mt-2">{!! $sectionDescription !!}</div>
            </div>
        </section>

        <!-- Faculty grid -->
        <section class="py-5" style="background: var(--bs-body-bg, #0f0f0f);">
            <div class="container">
                @if($faculty->isEmpty())
                    <div class="text-center py-5">
                        <p class="h4 mb-2" style="color: #c44;">Looks like there isn't one yet—our roster is still warming up.</p>
                        <p class="h4 mb-4" style="color: #b85454;">Check back soon, or head to the college home for more.</p>
                        <a href="{{ route('college.show', ['college' => $collegeSlug]) }}" class="btn rounded-pill px-4 py-3" style="background: {{ $headerColor }}; color: #fff; border: none;">Back to {{ $collegeName }}</a>
                    </div>
                @else
                    @php
                        $grouped = $faculty->groupBy(fn($m) => $m->department ?: 'Unassigned');
                        $sortedGroups = $grouped->sortKeys();
                    @endphp
                    @foreach ($sortedGroups as $deptName => $members)
                        @php
                            $displayDeptName = in_array($deptName, ['Unassigned', 'Unassigned Staff'], true) ? 'Staff' : $deptName;
                        @endphp
                        <div class="mb-5">
                            <div class="d-flex align-items-center gap-3 mb-4 faculty-group-header">
                                <h2 class="h4 fw-700 mb-0 faculty-group-title" style="color: {{ $headerColor }};">{{ $displayDeptName }}</h2>
                                <span class="badge rounded-pill px-3 py-1" style="background: {{ $headerColor }}1a; color: {{ $headerColor }}; font-size: 0.8rem; font-weight: 500;">{{ $members->count() }} {{ Str::plural('member', $members->count()) }}</span>
                                <div class="flex-grow-1 faculty-group-divider" style="height: 1px; background: {{ $headerColor }}33;"></div>
                            </div>
                            <div class="row g-4">
                                @foreach ($members as $member)
                                    <div class="col-sm-6 col-md-4 col-lg-3">
                                        <div class="faculty-card h-100">
                                            <div class="faculty-card-photo-wrap">
                                                @if($member->photo)
                                                    <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($member->photo, 'images') }}" alt="{{ e($member->name) }}" class="faculty-card-photo">
                                                @else
                                                    <img src="{{ $collegeLogoUrl }}" alt="{{ e($member->name) }}" class="faculty-card-photo" style="object-fit: contain; padding: 1rem; background: #ffffff;">
                                                @endif
                                            </div>
                                            <div class="faculty-card-body">
                                                <h3 class="faculty-card-name h6 mb-0">{{ $member->name }}</h3>
                                                @if($member->position)
                                                    <p class="faculty-card-position mb-0">{{ $member->position }}</p>
                                                @endif
                                                @if($member->email)
                                                    <p class="faculty-card-email mb-0"><a href="mailto:{{ e($member->email) }}">{{ $member->email }}</a></p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </section>
    </main>

    <!-- Footer -->
    <!-- Footer -->
    @include('includes.college-footer')

    <!-- Includes: Scripts -->
    @include('includes.college-scripts')
</body>
</html>
