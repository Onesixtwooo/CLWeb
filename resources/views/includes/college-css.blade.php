    <!-- College appearance colors (set in admin Settings) -->
    <style>
        :root {
            --college-header: {{ $headerColor }};
            --college-accent: {{ $accentColor }};
        }
        .engineering-header-wrapper.engineering-header-scrolled .engineering-top-header::after {
            background: {{ $accentColor }} !important;
        }
        .engineering-header,
        .engineering-navbar {
            background: {{ $headerColor }} !important;
        }
        .engineering-loader-title {
            color: {{ $headerColor }} !important;
        }
        .engineering-loader-spinner {
            border-top-color: {{ $headerColor }} !important;
            border-color: {{ $headerColor }}33 !important;
        }
        .college-page .retro-button,
        .college-page .btn-primary.retro-button {
            background: {{ $headerColor }} !important;
            border-color: {{ $headerColor }} !important;
        }
        .college-page .retro-button:hover,
        .college-page .btn-primary.retro-button:hover {
            background: {{ $headerColor }} !important;
            border-color: {{ $headerColor }} !important;
            filter: none !important;
        }
        .college-page .retro-link,
        .college-page .link-button,
        .college-page .event-card-tag.news,
        .college-page .event-card-tag.announcement,
        .college-page .news-card-category,
        .college-page .faq-card-title,
        .college-page a.retro-link {
            color: {{ $headerColor }} !important;
        }
        .college-page .retro-link:hover,
        .college-page .link-button:hover {
            color: {{ $accentColor }} !important;
        }
        .college-page .retro-card-border,
        .college-page .program-card:hover .retro-card-border {
            border-color: {{ $headerColor }} !important;
        }
        .college-page .section-badge.retro-label.college-theme {
            background: {{ $headerColor }} !important;
            color: #fff !important;
        }
        .college-page .news-section-modern:hover::before {
            border-color: {{ $headerColor }}73 !important;
        }
        .college-page .news-card-modern:hover .news-card-title {
            color: {{ $headerColor }} !important;
        }
        .college-page .faq-card:hover,
        .college-page .faq-card.is-open {
            border-color: {{ $headerColor }}40 !important;
        }
        .college-page .faq-card.is-open .faq-card-title {
            color: {{ $headerColor }} !important;
        }
        .college-page .hero-overlay {
            background: {{ $headerColor }}8c !important;
        }
        .college-page .btn-success.retro-button,
        .college-page .hero-buttons .btn-primary {
            background: {{ $headerColor }} !important;
            border-color: {{ $headerColor }} !important;
        }
        .college-page .btn-success.retro-button:hover,
        .college-page .hero-buttons .btn-primary:hover {
            background: {{ $headerColor }} !important;
            border-color: {{ $headerColor }} !important;
            filter: none !important;
        }

        /* Responsive Rich Text (Quill) */
        .ql-editor {
            height: auto !important;
            padding: 0 !important;
            overflow-y: visible !important;
        }
        .ql-editor img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 1rem 0;
        }
        .ql-editor iframe {
            max-width: 100%;
            width: 100%;
            aspect-ratio: 16 / 9;
            border-radius: 8px;
            margin: 1rem 0;
        }
        .ql-editor p {
            margin-bottom: 1rem;
        }
        .ql-editor ul, .ql-editor ol {
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }
    </style>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Buttershine+Serif:wght@400;700&family=Libre+Franklin:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        :root {
            --college-header-color: {{ $headerColor }};
            --college-accent-color: {{ $accentColor }};
            --college-header-gradient: {{ $headerColor }};
        }
    </style>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/college.css', 'resources/js/app.js'])
