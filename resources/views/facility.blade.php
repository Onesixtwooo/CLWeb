@php
    $collegeName = $collegeName ?? 'College of Engineering';
    $collegeSlug = $collegeSlug ?? 'engineering';
    $collegeShortName = $collegeShortName ?? 'CEn';
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
    <meta name="description" content="{{ $facility->name }} - {{ $collegeName }} - {{ config('app.name', 'CLSU') }}">
    <title>{{ $facility->name }} - {{ $collegeName }}</title>

    @include('includes.college-css')

    <style>
        :root {
            --college-header-color: {{ $headerColor }};
            --college-accent-color: {{ $accentColor }};
            --college-header-gradient: {{ $headerColor }};
            
            --primary: {{ $headerColor }};
            --primary-dark: {{ $accentColor }};
            --secondary: #e0a70d; /* CLSU Gold */
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
    </style>
</head>
<body class="antialiased college-page">

    <!-- Header -->
    @include('includes.college-header', [
        'collegeName' => $collegeName,
        'collegeShortName' => $collegeShortName,
        'collegeLogoUrl' => $collegeLogoUrl,
        'headerColor' => $headerColor,
        'collegeEmail' => $collegeEmail,
        'collegePhone' => $collegePhone,
        'collegeContact' => $collegeContact
    ])

    <!-- Facility Hero Section -->
    <section class="facility-hero" style="background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ $facility->photo ? \App\Providers\AppServiceProvider::resolveImageUrl($facility->photo, 'images') : asset('images/college-hero.jpg') }}'); background-size: cover; background-position: center; padding: 15rem 0 8rem 0; color: white; text-align: center;">
        <div class="container">
            @if($facility->department_name)
                <h5 class="text-uppercase mb-3 fw-bold text-white text-decoration-underline" style="letter-spacing: 1px;">{{ $facility->department_name }}</h5>
            @endif
            <h1 class="display-3 fw-bold mb-3">{{ $facility->name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="{{ route('college.show', $college) }}" class="text-white text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item text-white-50" aria-current="page">Facilities</li>
                    <li class="breadcrumb-item active text-white" aria-current="page">{{ $facility->name }}</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Facility Details Section -->
    <section class="facility-details py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-5">
                            <h2 class="mb-4 text-dark fw-bold border-bottom pb-3">Facility Overview</h2>
                            <div class="lead text-muted mb-4">
                                {!! $facility->description !!}
                            </div>
                            


                            @if($facility->images->isNotEmpty())
                                <hr class="my-5">
                                <h3 class="mb-4 text-dark fw-bold">Image Gallery</h3>
                                <div class="row g-3">
                                    @foreach($facility->images as $image)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="ratio ratio-4x3 rounded overflow-hidden shadow-sm">
                                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($image->image_path, 'images') }}" 
                                                     class="img-fluid object-fit-cover w-100 h-100" 
                                                     alt="{{ $image->caption ?? 'Facility Image' }}"
                                                     style="cursor: pointer;"
                                                     onclick="window.open(this.src, '_blank')">
                                            </div>
                                            @if($image->caption)
                                                <small class="text-muted d-block mt-1 text-center">{{ $image->caption }}</small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Other Facilities -->
    <section class="gallery-highlights py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark">Other Facilities</h2>
                <p class="text-muted">Explore distinct facilities at {{ $collegeShortName }}</p>
                <div class="mx-auto mt-3" style="width: 60px; height: 4px; background: var(--primary);"></div>
            </div>

            <div class="row g-4">
                @forelse($otherFacilities as $other)
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-radius: 12px; transition: transform 0.3s ease;">
                        <a href="{{ route('college.facility.show', ['college' => $college, 'facility' => $other]) }}" class="text-decoration-none">
                            <div class="ratio ratio-4x3">
                                <img src="{{ $other->photo ? \App\Providers\AppServiceProvider::resolveImageUrl($other->photo, 'images') : $collegeLogoUrl }}" class="card-img-top object-fit-cover" alt="{{ $other->name }}">
                            </div>
                            <div class="card-body text-center bg-white">
                                <h6 class="card-title fw-bold text-dark mb-0 text-truncate">{{ $other->name }}</h6>
                            </div>
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-4">
                    <p class="text-muted">No other facilities available to display.</p>
                </div>
                @endforelse
            </div>
            
            <div class="text-center mt-5">
                 <a href="{{ route('college.show', $college) }}#explore" class="btn btn-primary px-4 py-2" style="background-color: {{ $headerColor }}; border-color: {{ $headerColor }};">View All Facilities</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('includes.college-footer')


    <!-- Scripts -->
    @include('includes.college-scripts')
</body>
</html>
