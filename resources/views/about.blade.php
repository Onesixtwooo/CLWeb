 <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ config('app.name', 'Laravel') }} - About">
    <title>About - {{ config('app.name', '') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Buttershine+Serif:wght@400;700&family=Libre+Franklin:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="retro-modern">
    <!-- Header (same as welcome) -->
    <header class="header">
        <nav class="navbar navbar-expand-md navbar-light bg-white">
            <div class="container">
                <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center gap-2 logo">
                    <div class="logo-box retro-badge">
                        <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl(\App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp')) }}" alt="CLSU Logo" class="logo-image">
                    </div>
                    <div class="logo-text d-flex flex-column">
                        <h2 class="retro-heading mb-0">
                            <span class="logo-full-text d-none d-md-inline">CENTRAL LUZON STATE UNIVERSITY</span>
                            <span class="logo-short-text d-inline d-md-none">CLSU</span>
                        </h2>
                        <p class="retro-subtitle">
                            <span class="d-inline d-md-none">Science City of Muñoz</span>
                            <span class="d-none d-md-inline">Science City of Muñoz, 3120 Nueva Ecija, Philippines</span>
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
                            <a href="{{ url('/') }}#home" class="nav-link">Home</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('about') }}" class="nav-link active" aria-current="page">About</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/') }}#programs" class="nav-link">Programs</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/') }}#news" class="nav-link">News</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/') }}#contact" class="nav-link">Contact</a>
                        </li>
                        <li class="nav-item ms-md-3 mt-2 mt-md-0">
                            <div class="d-flex align-items-center gap-2 header-actions">
                                @if (Route::has('login'))
                                    @auth
                                        <a href="{{ url('/dashboard') }}" class="btn btn-success btn-sm">Dashboard</a>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-success btn-sm">Login</a>
                                        @if (Route::has('register'))
                                            <a href="{{ route('register') }}" class="btn btn-outline-success btn-sm">Register</a>
                                        @endif
                                    @endauth
                                @endif
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main style="padding-top: 80px;">
        <div class="about-subnav">
            <div class="container">
                <nav class="nav about-subnav-list">
                    <a class="nav-link" href="#about-university">About Us</a>
                    <a class="nav-link" href="#history">History</a>
                    <a class="nav-link" href="#brand-guidelines">Brand Guidelines</a>
                    <a class="nav-link" href="#campus-life">Campus Life</a>
                </nav>
            </div>
        </div>
        <!-- About the University -->
        <section id="about-university" class="py-5" style="background: #f8fbfd;">
            <div class="container">
                <div class="section-header">
                    <span class="section-badge retro-label">About the University</span>
                    <h1 class="retro-section-title">Central Luzon State University</h1>
                </div>

                <div class="row align-items-center gy-4">
                    <div class="col-lg-7">
                        <p class="text-muted">
                            The Central Luzon State University (CLSU), one of the renowned and prestigious state institutions of higher learning in the country,
                            straddles a 658-hectare campus in the Science City of Muñoz, Nueva Ecija, 150 kilometers north of Manila.
                        </p>
                        <p class="text-muted">
                            The lead agency of the Muñoz Science Community and the seat of Central Luzon Agriculture, Aquatic and Resources Research and Development Consortium (CLAARRDEC).
                        </p>
                        <p class="text-muted">
                            The university was designated by the Commission on Higher Education (CHED) – National Agriculture and Fisheries Education System (NAFES) as National University College of Agriculture (NUCA) and National University College of Fisheries (NUCF). Similarly, designated as CHED Center of Excellence (COE) in Agriculture, Agricultural Engineering, Biology, Fisheries, Teacher Education, and Veterinary Medicine - the most number of COEs in Central and Northern Luzon Regions. It is likewise designated as the Center of Research Excellence in Small Ruminants by the Philippine Council for Agriculture, Aquaculture, Forestry and Natural Resources Research and Development - Department of Science and Technology (PCAARRD-DOST). Also designated by the Department of Environment and Natural Resources (DENR) as the Regional Integrated Coastal Resources Management Center. Additionally, it was picked as the Model Agro-Tourism Site for Luzon.
                        </p>
                        <p class="text-muted">
                            CLSU stands out as the only comprehensive state university in the Philippines with the most number of curricular programs accredited by the Accrediting Agency of Chartered Colleges and Universities in the Philippines (AACCUP) with Level IV accreditation. The university is further declared Cultural Property of the Philippines with the code of PH-03-0027 due to its high historical, cultural, academic, and agricultural importance to the nation.
                        </p>
                        <p class="text-muted">
                            To date, CLSU remains as one of the premier institutions of agriculture in Southeast Asia known for its breakthrough researches in aquatic culture (pioneer in the sex reversal of tilapia), ruminant, crops, orchard, and water management, living through its vision of becoming “a world-class National Research University for science and technology in agriculture and allied fields.”
                        </p>
                    </div>
                    <div class="col-lg-5">
                        <div class="ratio ratio-4x3 rounded-4 overflow-hidden shadow-sm">
                            <img src="{{ asset('images/CLSU.jpg') }}" alt="CLSU Campus" class="w-100 h-100 object-fit-cover">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Institutional Profile -->
        <section id="institutional-profile" class="py-5">
            <div class="container">
                <div class="section-header">
                    <span class="section-badge retro-label">Institutional Profile</span>
                    <h2 class="retro-section-title">Institutional Profile</h2>
                </div>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <div class="institutional-card-image">
                                <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl(\App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp')) }}" alt="ISO 9001:2015 Certified">
                            </div>
                            <h3 class="h6 fw-bold mb-2">ISO 9001:2015 Certified</h3>
                            <p class="text-muted mb-0">Quality management system certified to international standards.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <div class="institutional-card-image">
                                <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl(\App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp')) }}" alt="Centers of Excellence">
                            </div>
                            <h3 class="h6 fw-bold mb-2">Centers of Excellence</h3>
                            <p class="text-muted mb-0">Recognized COEs in Agriculture, Engineering, Biology, Fisheries, Teacher Education, and Veterinary Medicine.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <div class="institutional-card-image">
                                <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl(\App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp')) }}" alt="Program Accreditation">
                            </div>
                            <h3 class="h6 fw-bold mb-2">Program Accreditation</h3>
                            <p class="text-muted mb-0">Most number of AACCUP Level IV accredited programs nationwide.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <div class="institutional-card-image">
                                <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl(\App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp')) }}" alt="Transparency Seal">
                            </div>
                            <h3 class="h6 fw-bold mb-2">Transparency Seal</h3>
                            <p class="text-muted mb-0">Committed to transparency and accountability in public service.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <div class="institutional-card-image">
                                <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl(\App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp')) }}" alt="Agro-Tourism Site">
                            </div>
                            <h3 class="h6 fw-bold mb-2">Agro-Tourism Site</h3>
                            <p class="text-muted mb-0">Model Agro-Tourism Site for Central Luzon.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mission, Vision and Philosophy -->
        <section id="mission" class="py-5" style="background: #f8fbfd;">
            <div class="container">
                <div class="section-header">
                    <span class="section-badge retro-label">Mission, Vision and Philosophy</span>
                    <h2 class="retro-section-title">Mission, Vision and Philosophy</h2>
                </div>

                <p class="text-muted">
                    Section 2 of Republic Act No. 4067 states \"The university shall primarily give professional and technical training in agriculture and mechanic arts besides providing advanced instruction and promoting research in literature, philosophy, the sciences, technology and arts.\"
                </p>
                <p class="text-muted">
                    Moreover, the provisions of Republic Act No. 8292 enabled its Board of Regents to expand its mandate and thus adopted the following:
                </p>

                <div class="row g-4 mt-3">
                    <div class="col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <h3 class="h6 fw-bold mb-2">Mission</h3>
                            <p class="text-muted mb-0">
                                CLSU shall develop globally competitive, work-ready, socially-responsible and empowered human resources who value life-long learning; and to generate, disseminate, and apply knowledge and technologies for poverty alleviation, environmental protection, and sustainable development.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <h3 class="h6 fw-bold mb-2">Vision</h3>
                            <p class="text-muted mb-0">CLSU as a world-class National Research University for science and technology in agriculture and allied fields.</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <h3 class="h6 fw-bold mb-2">Philosophy</h3>
                            <p class="text-muted mb-0">
                                The ultimate measure of the effectiveness of Central Luzon State University as an institution of higher learning is its contribution to and impact on the educational, economic, social, cultural, political and moral well-being and environmental consciousness of the peoples it serves.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CLSU Core Values and Principles -->
        <section id="core-values" class="py-5">
            <div class="container">
                <div class="section-header">
                    <span class="section-badge retro-label">CLSU Core Values and Principles</span>
                    <h2 class="retro-section-title">The Essence of CLSU</h2>
                </div>

                <p class="text-muted">We refer to the following as the “Essence of CLSU” to reflect and to further the values and principles that make CLSU great and unique.</p>

                <div class="row g-4 mt-3">
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <h3 class="h6 fw-bold mb-2">Quality and Excellence</h3>
                            <p class="text-muted mb-0">CLSU believes that the relentless pursuit of quality and excellence constitutes the foundational element of its existence.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <h3 class="h6 fw-bold mb-2">Innovativeness and Creativity</h3>
                            <p class="text-muted mb-0">CLSU considers research as the lifeblood of the institution to be further nurtured and cultivated as it provides the energy and dynamism in its quest to become a comprehensive research university.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <h3 class="h6 fw-bold mb-2">Inclusiveness and Stewardship</h3>
                            <p class="text-muted mb-0">CLSU supports and sustains an equitable community that will have access to the benefits of education and discovery.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <h3 class="h6 fw-bold mb-2">Transformative and Caring</h3>
                            <p class="text-muted mb-0">CLSU commits to provide a teaching and learning environment that provides opportunities for critical and analytical thinking, character building, skills training, and leadership training where adequate facilities and resources are available and accessible.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <h3 class="h6 fw-bold mb-2">Efficiency and Effectiveness</h3>
                            <p class="text-muted mb-0">CLSU demands and maintains that an efficient and professional administration and corporate organization is required to advance the University's vision and mission.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <h3 class="h6 fw-bold mb-2">Hard Work and Integrity</h3>
                            <p class="text-muted mb-0">CLSU practices the values of hard work and integrity as the cornerstones of performance and output for career development and professional growth.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <h3 class="h6 fw-bold mb-2">Transparency and Accountability</h3>
                            <p class="text-muted mb-0">CLSU operates under the principles of transparency and accountability where freedom, independence and autonomy is respected but balanced by shared management principles, openness and responsibility.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm border">
                            <h3 class="h6 fw-bold mb-2">Commitment to Public Service</h3>
                            <p class="text-muted mb-0">CLSU recognizes that its core purpose is to serve the interest of the people in Central Luzon, the country and the Asian region through the creation, dissemination and application of knowledge.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Educational Philosophy -->
        <section id="educational-philosophy" class="py-5" style="background: #f8fbfd;">
            <div class="container">
                <div class="section-header">
                    <span class="section-badge retro-label">The CLSU Educational Philosophy</span>
                    <h2 class="retro-section-title">Educational Philosophy</h2>
                </div>

                <div class="text-muted">
                    <p>
                        The Central Luzon State University is committed and dedicated to provide a holistic transformative education anchored on its mission statement and its institutional core values. As stated on its mission, the University shall develop globally competitive, work-ready, socially-responsible and empowered human resources who value life-long learning; and shall generate, disseminate, and apply knowledge and technologies for poverty alleviation, environmental protection and sustainable development. In consonance, the educational philosophy of the University is reflective of its teaching and learning environment.
                    </p>
                    <p>
                        Along with the curricular programs, the academic journey of learners revolves on three value-laden dimensions: creativeness and innovativeness, hard work and integrity, and inclusiveness and transformativeness.
                    </p>
                    <p>
                        With these, the University:
                    </p>
                    <p>
                        Provides a teaching and learning environment which harness creativity and innovativeness among learners. It advocates the development of individuals to become agents of change, innovators and leaders, imbued with an outward and forward-thinking perspectives in their respective fields. It further ensures the vital role of research in promoting quality and excellence. Thus, regular updating of curricular programs, empowerment of human capital, modernizing instructional and pedagogical resources, and equal opportunity for all, are always observed.
                    </p>
                    <p>
                        Adopts experiential learning on its programs along with the dynamic and continuous engagement between the faculty, the staff, the students and the community. The shared values of hard work and integrity puts forth in the discovery of new knowledge and in its application in the real-life contexts. Thus, enabling and preparing the learners to be effective and efficient navigators of the future.
                    </p>
                    <p>
                        Provides experiences that enable learners to discover the fulfillment of embracing diversity in the form of various academic collaborations at the local, regional and international levels. Students are guided to acknowledge and respect peoples and their cultures for inclusive societal transformation.
                    </p>
                </div>
            </div>
        </section>

        <!-- University Lifelong Learning Policy -->
        <section id="lifelong-learning" class="py-5">
            <div class="container">
                <div class="section-header">
                    <span class="section-badge retro-label">University Lifelong Learning Policy</span>
                    <h2 class="retro-section-title">Lifelong Learning Policy</h2>
                </div>

                <div class="text-muted">
                    <p>
                        The CLSU Lifelong Learning Policy Central Luzon State University (CLSU), as an institution of higher learning is committed and dedicated on providing lifelong learning opportunities and experiences among its stakeholders.
                    </p>
                    <p>
                        It also aims to enhance the quality of life through these learnings as an ultimate goal, to engender life qualities to ensue satisfaction and happiness are desired. Specifically, the university shall:
                    </p>
                    <p>
                        <strong>a.</strong> Ensure that benefits of formal, non-formal, and informal education, and discovery are made accessible, inclusive and equitable, to communities and stakeholders, regardless of ethnicity, religion, disability and gender.
                    </p>
                    <p>
                        <strong>b.</strong> Provide a teaching and learning environment that supports transformative opportunities for critical and analytical thinking, character building, skills training, and leadership training where adequate facilities and resources are available and accessible.
                    </p>
                </div>
            </div>
        </section>

        <!-- Quality Policy Statements -->
        <section id="quality-policy" class="py-5" style="background: #f8fbfd;">
            <div class="container">
                <div class="section-header">
                    <span class="section-badge retro-label">Quality Policy Statements</span>
                    <h2 class="retro-section-title">Quality Policy Statements</h2>
                </div>

                <div class="bg-white rounded-4 p-4 shadow-sm border">
                    <ul class="text-muted mb-3">
                        <li>Excellent service to humanity is our commitment.</li>
                        <li>We are committed to develop globally-competent and empowered human resources, and to generate knowledge and technologies for inclusive societal development.</li>
                        <li>We are dedicated to uphold CLSU's core values and principles, comply with statutory and regulatory standards and to continuously improve the effectiveness of our quality management system.</li>
                    </ul>
                    <p class="text-muted mb-0">
                        Mahalaga ang inyong tinig upang higit na mapahusay ang kalidad ng aming paglilingkod.
                    </p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer id="contact" class="footer footer-rich" style="background-image: linear-gradient(135deg, rgba(18, 18, 18, 0.9), rgba(18, 18, 18, 0.9)), url('{{ asset('images/CLSU.jpg') }}');">
            <div class="container">
                <div class="footer-grid footer-grid-rich">
                    <div class="footer-column footer-brand">
                        <div class="footer-logos">
                            <img src="{{ Vite::asset('resources/images/seal/1.png') }}" alt="Freedom of Information Seal">
                            <img src="{{ Vite::asset('resources/images/seal/2.png') }}" alt="Transparency Seal">
                            <img src="{{ Vite::asset('resources/images/seal/3.png') }}" alt="Philippines Seal" class="seal-transparent">
                        </div>
                        <h3>CENTRAL LUZON STATE UNIVERSITY</h3>
                        <p>Science City of Muñoz, Nueva Ecija, Philippines 3120</p>
                        <div class="footer-divider"></div>
                        <ul class="footer-contact">
                            <li>Central Luzon State University, Science City of Muñoz, Nueva Ecija, Philippines</li>
                            <li><a href="mailto:op@clsu.edu.ph">op@clsu.edu.ph</a></li>
                            <li><a href="tel:+63449408785">(044) 940 8785</a></li>
                        </ul>
                        <div class="footer-map">
                            <iframe
                                title="CLSU Map"
                                src="https://www.google.com/maps?q=Central%20Luzon%20State%20University%20Mu%C3%B1oz%20Nueva%20Ecija&output=embed"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div class="footer-social">
                            <a href="#" title="Facebook">f</a>
                            <a href="#" title="Twitter">𝕏</a>
                            <a href="#" title="Instagram">📷</a>
                            <a href="#" title="LinkedIn">in</a>
                            <a href="#" title="YouTube">▶</a>
                        </div>
                        <p class="footer-copy">&copy; Copyright {{ date('Y') }} Central Luzon State University All Rights Reserved</p>
                    </div>

                    <div class="footer-column">
                        <h3 class="footer-heading">OPPORTUNITIES</h3>
                        <ul class="footer-list">
                            <li><a href="#">Career</a></li>
                            <li><a href="#">Bid Opportunities</a></li>
                            <li><a href="#">Procurements</a></li>
                        </ul>

                        <h3 class="footer-heading mt-lg">E-SERVICES</h3>
                        <ul class="footer-list">
                            <li><a href="#">Downloads</a></li>
                            <li><a href="#">Publications</a></li>
                            <li><a href="#">Knowledge Sharing & Learning Resources</a></li>
                            <li><a href="#">AgriATM</a></li>
                            <li><a href="#">Kamalig Booking System</a></li>
                        </ul>
                    </div>

                    <div class="footer-column footer-feedback">
                        <h3 class="footer-heading">FEEDBACK AND GRIEVANCE DESK</h3>
                        <p>
                            Central Luzon State University values the voices of its students, faculty, staff, and the people it serves
                            and is committed to continuously improve its services. As part of our commitment to quality and excellence,
                            we encourage you to share your feedback, concerns, and suggestions.
                        </p>
                        <p>
                            To ensure your inputs are heard and addressed, we provide the following official channels for receiving
                            feedback and grievances.
                        </p>
                        <ul class="footer-contact">
                            <li><a href="mailto:feedback@clsu.edu.ph">feedback@clsu.edu.ph</a></li>
                            <li><a href="tel:+639537267511">+63 9537 267 511</a></li>
                            <li><a href="tel:+63449407030">(044) 940 7030</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </main>

    <!-- Bootstrap 5 JS bundle (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
</body>
</html>
