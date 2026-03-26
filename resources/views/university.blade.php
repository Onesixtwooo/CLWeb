<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Modern University Landing Page">
    <title>{{ config('app.name', 'University') }} – Modern</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Buttershine+Serif:wght@400;700&family=Libre+Franklin:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- App styles and scripts (for fonts, tokens, loading, etc.) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light text-dark">
    <!-- Loading Screen -->
    <div id="loadingScreen" class="loading-screen">
        <div class="loading-content">
            <div class="loading-logo">
                <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl(\App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp')) }}" alt="University Logo" class="loading-logo-image">
            </div>
            <div class="loading-spinner"></div>
            <p class="loading-text">Loading campus experience...</p>
        </div>
    </div>
    <!-- Minimal Navbar -->
    <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom sticky-top">
        <div class="container py-2">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <span class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                    <span class="text-white fw-bold">U</span>
                </span>
                <span class="fw-semibold">University</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#minimalNav"
                    aria-controls="minimalNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="minimalNav">
                <ul class="navbar-nav ms-auto align-items-md-center gap-md-2 mb-2 mb-md-0">
                    <li class="nav-item"><a class="nav-link" href="#programs">Programs</a></li>
                    <li class="nav-item"><a class="nav-link" href="#research">Research</a></li>
                    <li class="nav-item"><a class="nav-link" href="#campus">Campus Life</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item ms-md-3 mt-2 mt-md-0">
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="btn btn-dark btn-sm px-3">Student Portal</a>
                        @else
                            <a href="#" class="btn btn-dark btn-sm px-3">Student Portal</a>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <main>
        <section class="py-5 py-lg-6">
            <div class="container">
                <div class="row align-items-center gy-4">
                    <div class="col-lg-6">
                        <p class="text-uppercase text-muted small mb-2">Modern University</p>
                        <h1 class="display-4 fw-semibold mb-3">
                            A focused space for learning, research, and community.
                        </h1>
                        <p class="lead text-muted mb-4">
                            Build your future with programs designed for real-world impact, guided by faculty who care about your growth.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="#programs" class="btn btn-dark px-4">Explore Programs</a>
                            <a href="#contact" class="btn btn-outline-secondary px-4">Visit Campus</a>
                        </div>
                    </div>
                    <div class="col-lg-5 offset-lg-1">
                        <div class="bg-white rounded-4 shadow-sm p-4">
                            <div class="row text-center g-3">
                                <div class="col-4">
                                    <div class="fw-semibold fs-4">15k+</div>
                                    <div class="text-muted small">Students</div>
                                </div>
                                <div class="col-4">
                                    <div class="fw-semibold fs-4">120+</div>
                                    <div class="text-muted small">Programs</div>
                                </div>
                                <div class="col-4">
                                    <div class="fw-semibold fs-4">30+</div>
                                    <div class="text-muted small">Partners</div>
                                </div>
                            </div>
                            <hr class="my-4">
                            <p class="mb-1 fw-semibold">Applications for AY 2026–2027 are open.</p>
                            <p class="text-muted small mb-0">Submit your application online in minutes—no paper forms required.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Campus Images: light, minimal gallery -->
        <section class="py-4 py-md-5 border-top">
            <div class="container">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3 mb-md-4">
                    <div>
                        <p class="text-uppercase text-muted small mb-1">Campus glimpse</p>
                        <h2 class="h4 fw-semibold mb-0">A quick view of everyday spaces</h2>
                    </div>
                </div>

                <div class="row g-3 g-md-4 mb-3 mb-md-4">
                    <div class="col-md-4">
                        <div class="ratio ratio-4x3 rounded-4 overflow-hidden bg-light">
                            <img src="{{ asset('images/CLSU.jpg') }}" alt="Main entrance" class="w-100 h-100 object-fit-cover">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ratio ratio-4x3 rounded-4 overflow-hidden bg-light">
                            <img src="{{ asset('images/178935.jpg') }}" alt="Academic building" class="w-100 h-100 object-fit-cover">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ratio ratio-4x3 rounded-4 overflow-hidden bg-light">
                            <img src="{{ asset('images/wc1.jpg') }}" alt="Campus walkway" class="w-100 h-100 object-fit-cover">
                        </div>
                    </div>
                </div>

                <div class="row g-3 g-md-4">
                    <div class="col-md-6">
                        <div class="ratio ratio-16x9 rounded-4 overflow-hidden bg-light">
                            <img src="{{ asset('images/facilities/housing.jpg') }}" alt="Student housing" class="w-100 h-100 object-fit-cover">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ratio ratio-16x9 rounded-4 overflow-hidden bg-light">
                            <img src="{{ asset('images/Library-clean.webp') }}" alt="Library interior" class="w-100 h-100 object-fit-cover">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Programs -->
        <section id="programs" class="py-5 border-top">
            <div class="container">
                <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between mb-4">
                    <div>
                        <p class="text-uppercase text-muted small mb-1">Academics</p>
                        <h2 class="h3 fw-semibold mb-0">Focused programs across key disciplines</h2>
                    </div>
                    <a href="#" class="text-decoration-none small text-secondary mt-3 mt-md-0">View all programs</a>
                </div>

                <div class="row g-3 g-md-4">
                    <div class="col-md-4">
                        <div class="bg-white border rounded-4 p-3 p-md-4 h-100">
                            <p class="text-uppercase small text-muted mb-2">Undergraduate</p>
                            <h3 class="h5 fw-semibold mb-2">Liberal Arts &amp; Sciences</h3>
                            <p class="text-muted small mb-0">
                                Broad-based programs that build critical thinking, communication, and analytical skills.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-white border rounded-4 p-3 p-md-4 h-100">
                            <p class="text-uppercase small text-muted mb-2">Engineering</p>
                            <h3 class="h5 fw-semibold mb-2">Technology &amp; Innovation</h3>
                            <p class="text-muted small mb-0">
                                Applied engineering and computing programs focused on solving real-world problems.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-white border rounded-4 p-3 p-md-4 h-100">
                            <p class="text-uppercase small text-muted mb-2">Graduate</p>
                            <h3 class="h5 fw-semibold mb-2">Advanced Studies</h3>
                            <p class="text-muted small mb-0">
                                Research-driven master’s and doctoral programs for specialized fields.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Research & Campus Life -->
        <section id="research" class="py-5">
            <div class="container">
                <div class="row gy-4 align-items-center">
                    <div class="col-lg-6">
                        <p class="text-uppercase text-muted small mb-1">Research</p>
                        <h2 class="h3 fw-semibold mb-3">Research that stays close to real communities</h2>
                        <p class="text-muted mb-3">
                            Faculty and students work together on projects that address local challenges—from
                            climate resilience to digital inclusion.
                        </p>
                        <ul class="list-unstyled small text-muted mb-0">
                            <li class="mb-1">• Community-centered research labs</li>
                            <li class="mb-1">• Open data and transparent methods</li>
                            <li class="mb-1">• Cross-campus collaboration spaces</li>
                        </ul>
                    </div>
                    <div id="campus" class="col-lg-5 offset-lg-1">
                        <div class="bg-white border rounded-4 p-4 h-100">
                            <p class="text-uppercase text-muted small mb-2">Campus Life</p>
                            <p class="fw-semibold mb-2">Simple spaces made for connection.</p>
                            <p class="text-muted small mb-0">
                                Light-filled study halls, quiet outdoor corners, and a campus core designed for
                                walking, resting, and meeting with friends.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact / CTA -->
        <section id="contact" class="py-5 border-top">
            <div class="container">
                <div class="row gy-4 align-items-center">
                    <div class="col-lg-6">
                        <h2 class="h3 fw-semibold mb-2">Ready to see the campus?</h2>
                        <p class="text-muted mb-3">
                            Schedule a visit or talk with an admissions advisor. We’ll help you understand programs,
                            scholarships, and next steps.
                        </p>
                    </div>
                    <div class="col-lg-5 offset-lg-1">
                        <form class="bg-white border rounded-4 p-4">
                            <div class="mb-3">
                                <label for="visitName" class="form-label small text-muted">Full name</label>
                                <input type="text" id="visitName" class="form-control form-control-sm" placeholder="Alex Rivera">
                            </div>
                            <div class="mb-3">
                                <label for="visitEmail" class="form-label small text-muted">Email</label>
                                <input type="email" id="visitEmail" class="form-control form-control-sm" placeholder="you@example.com">
                            </div>
                            <div class="mb-3">
                                <label for="visitInterest" class="form-label small text-muted">Program interest</label>
                                <input type="text" id="visitInterest" class="form-control form-control-sm" placeholder="e.g., Computer Science">
                            </div>
                            <button type="submit" class="btn btn-dark btn-sm w-100">Request more information</button>
                        </form>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-top text-center text-muted small">
                    &copy; {{ date('Y') }} {{ config('app.name', 'University') }}. All rights reserved.
                </div>
            </div>
        </section>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
</body>
</html>

