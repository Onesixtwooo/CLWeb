<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $album['title'] ?? ($album['name'] ?? 'Album') }} - {{ $organization->name }} - {{ config('app.name', 'CLSU') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Buttershine+Serif:wght@400;700&family=Libre+Franklin:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/college.css', 'resources/js/app.js'])
    @include('includes.college-css')

    <style>
        :root {
            --college-header-color: {{ $headerColor }};
            --college-accent-color: {{ $accentColor }};
            --college-header-gradient: {{ $headerColor }};
        }
        .engineering-top-header, .engineering-header, .engineering-navbar { background: {{ $headerColor }} !important; }
        .it-hero { padding: 8rem 0 4rem 0; color: white; text-align: center; background: var(--college-header-gradient); }
        .back-btn { 
            display: inline-flex; align-items: center; gap: 0.5rem; color: white; 
            text-decoration: none; font-weight: 600; margin-bottom: 2rem;
            transition: opacity 0.2s;
        }
        .back-btn:hover { opacity: 0.8; color: white; }
        .album-photo-card {
            border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            transition: transform 0.3s ease; cursor: pointer;
        }
        .album-photo-card:hover { transform: translateY(-5px); }
        .album-photo-frame {
            height: 240px;
            background: #f8f9fa;
            overflow: hidden;
        }
        .modal-body img { cursor: zoom-out; }
    </style>
</head>
<body class="retro-modern college-page">
    <div class="engineering-header-wrapper">
    @include('partials.college-top-header')
    <div class="engineering-nav-outer">
    <header class="header engineering-header">
        <nav class="navbar navbar-expand-md navbar-dark engineering-navbar">
            <div class="container">
                <a href="{{ route('college.show', ['college' => $collegeSlug]) }}" class="navbar-brand d-flex align-items-center gap-2 logo">
                    <div class="logo-box retro-badge">
                        <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($collegeLogoUrl) }}" alt="{{ $collegeName }} logo" class="logo-image">
                    </div>
                    <div class="logo-text d-flex flex-column">
                        <h2 class="retro-heading mb-0">
                            <span class="logo-full-text d-none d-md-inline">{{ strtoupper($collegeName) }}</span>
                            <span class="logo-short-text d-inline d-md-none">{{ strtoupper($collegeName) }}</span>
                        </h2>
                        <p class="retro-subtitle">
                            <span class="d-none d-md-inline">{{ $department?->name ?? $collegeName }}</span>
                        </p>
                    </div>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-md-0 align-items-md-center">
                        <li class="nav-item">
                            <a href="{{ route('college.show', ['college' => $collegeSlug]) }}#home" class="nav-link">College Home</a>
                        </li>
                        @php
                            $departmentLink = null;
                            if (! empty($department)) {
                                $departmentLink = route('college.department.show', ['college' => $collegeSlug, 'department' => $department]) . '#organizations';
                            }
                        @endphp
                        @if ($departmentLink)
                        <li class="nav-item">
                            <a href="{{ $departmentLink }}" class="nav-link">
                                {{ $department->name ?? 'Department' }}
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a href="{{ route('college.organizations', ['college' => $collegeSlug]) }}" class="nav-link">Organizations</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    </div>
    </div>

    <main>
        <section class="it-hero" style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), var(--college-header-gradient);">
            <div class="container text-start">
                <a href="{{ route('college.organization.show', ['college' => $collegeSlug, 'organization' => $organization]) }}#gallery" class="back-btn">
                    <i class="bi bi-arrow-left"></i> Back to Organization
                </a>
                <div class="d-flex flex-column gap-2">
                    <h6 class="text-uppercase mb-0 fw-bold opacity-75" style="letter-spacing: 2px;">{{ $organization->name }}</h6>
                    <h1 class="display-4 fw-bold mb-2">{{ $album['title'] ?? ($album['name'] ?? 'Untitled Album') }}</h1>
                    @if(!empty($album['description']))
                        <p class="lead opacity-75 max-w-2xl">{{ $album['description'] }}</p>
                    @endif
                </div>
            </div>
        </section>

        <section class="py-5 bg-white border-top">
            <div class="container">
                <div class="row g-4">
                    @php $photos = $album['photos'] ?? []; @endphp
                    @forelse ($photos as $photo)
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="album-photo-card bg-light" data-bs-toggle="modal" data-bs-target="#photoModal" 
                                 data-photo-url="{{ \App\Providers\AppServiceProvider::resolveImageUrl($photo['image']) }}" 
                                 data-photo-caption="{{ $photo['caption'] ?? '' }}">
                                <div class="album-photo-frame">
                                    <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($photo['image']) }}" alt="{{ $photo['caption'] ?? '' }}" class="object-fit-cover w-100 h-100">
                                </div>
                                @if(!empty($photo['caption']))
                                    <div class="p-3 bg-white border-top text-center">
                                        <p class="small text-dark fw-semibold mb-0 text-truncate" title="{{ $photo['caption'] }}">{{ $photo['caption'] }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-12 py-5 text-center my-5">
                            <div class="mb-4">
                                <i class="bi bi-images display-1 text-muted opacity-25"></i>
                            </div>
                            <h3 class="fw-bold text-muted">No photos in this album yet</h3>
                            <p class="text-muted">Stay tuned as we add more memories.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    <!-- Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0 p-3 position-absolute top-0 end-0 z-3">
                    <button type="button" class="btn-close btn-close-white shadow-none bg-dark rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 text-center" data-bs-dismiss="modal">
                    <img id="modalPhotoImg" src="" class="img-fluid rounded shadow-lg mx-auto d-block" style="max-height: 90vh; width: auto;">
                    <div id="modalPhotoCaption" class="text-white mt-3 p-3 bg-dark bg-opacity-75 rounded-pill d-inline-block px-4 lead fw-semibold shadow"></div>
                </div>
            </div>
        </div>
    </div>

    @include('includes.college-footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle sticky header
            window.addEventListener('scroll', function () {
                const wrapper = document.querySelector('.engineering-header-wrapper');
                if (wrapper) {
                    wrapper.classList.toggle('engineering-header-scrolled', window.scrollY > 50);
                }
            });

            const photoModal = document.getElementById('photoModal');
            if (photoModal) {
                photoModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const photoUrl = button.getAttribute('data-photo-url');
                    const photoCaption = button.getAttribute('data-photo-caption');
                    
                    const modalImg = photoModal.querySelector('#modalPhotoImg');
                    const modalCaption = photoModal.querySelector('#modalPhotoCaption');
                    
                    modalImg.src = photoUrl;
                    if (photoCaption) {
                        modalCaption.textContent = photoCaption;
                        modalCaption.style.display = 'inline-block';
                    } else {
                        modalCaption.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>
