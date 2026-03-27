<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('app.name', 'CLSU CMS') }}</title>
    @vite(['resources/css/app.css'])
    {{-- Fonts are loaded via app.css to avoid duplication --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --bs-primary: {{ $adminHeaderColor ?? '#198754' }};
            --admin-bg: #e8f5e9;
            --admin-surface: #ffffff;
            --admin-sidebar-bg: #3b3131;
            --admin-sidebar-hover: rgba(255,255,255,0.08);
            --admin-sidebar-active: rgba(255,255,255,0.12);
            --admin-sidebar-border: rgba(255,255,255,0.06);
            --admin-accent: {{ $adminHeaderColor ?? '#198754' }};
            --admin-accent-hover: {{ $adminHeaderColor ?? '#157347' }};
            --admin-accent-soft: {{ ($adminHeaderColor ?? '#198754') . '1e' }};
            --admin-header-bg: {{ $adminHeaderColor ?? '#0d6e42' }};
            --admin-gradient: {{ $adminHeaderColor ?? '#0d6e42' }};
            --admin-gradient-subtle: linear-gradient(180deg, {{ ($adminHeaderColor ?? '#0d6e42') . '0f' }} 0%, transparent 100%);
            --admin-text: #0f172a;
            --admin-text-muted: #64748b;
            --admin-border: {{ ($adminHeaderColor ?? '#d4edda') . '33' }};
            --admin-radius: 12px;
            --admin-radius-lg: 16px;
            --admin-shadow: 0 1px 3px rgba(0,0,0,0.05);
            --admin-shadow-hover: 0 4px 12px rgba(0,0,0,0.08);
        }
        * { -webkit-font-smoothing: antialiased; }

        /* ── Global Page Loading Screen ── */
        #page-loader {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1.25rem;
            transition: opacity 0.35s ease, visibility 0.35s ease;
        }
        #page-loader.loader-hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        #page-loader .loader-brand {
            color: #0f172a;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            opacity: 0.9;
        }
        #page-loader .loader-spinner {
            width: 44px;
            height: 44px;
            border: 3.5px solid #f1f5f9;
            border-top-color: #198754;
            border-radius: 50%;
            animation: spin 0.75s linear infinite;
        }
        #page-loader .loader-dots {
            display: flex;
            gap: 6px;
        }
        #page-loader .loader-dots span {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #e2e8f0;
            animation: dot-pulse 1.2s ease-in-out infinite;
        }
        #page-loader .loader-dots span:nth-child(2) { animation-delay: .2s; }
        #page-loader .loader-dots span:nth-child(3) { animation-delay: .4s; }
        @keyframes spin   { to { transform: rotate(360deg); } }
        @keyframes dot-pulse {
            0%, 80%, 100% { background: #e2e8f0; transform: scale(0.85); }
            40%            { background: #198754;               transform: scale(1.2);  }
        }

        body {
            font-family: "acumin-pro", "Acumin Pro", system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
            background: var(--admin-bg);
            color: var(--admin-text);
            min-height: 100vh;
            font-size: 0.9375rem;
        }
        .admin-navbar {
            background: var(--admin-header-bg) !important;
            border-bottom: 1px solid rgba(0,0,0,0.15);
            padding: 0.875rem 1.5rem;
            box-shadow: var(--admin-shadow);
        }
        .admin-navbar .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: #fff;
            letter-spacing: -0.02em;
        }
        .admin-navbar .nav-link {
            color: rgba(255,255,255,0.9);
            font-weight: 500;
            padding: 0.5rem 0.875rem;
            border-radius: 8px;
            transition: color 0.2s ease, background 0.2s ease;
        }
        .admin-navbar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.15);
        }
        .admin-navbar .btn-link.nav-link { border: 0; color: rgba(255,255,255,0.9); }
        .admin-navbar .btn-link.nav-link:hover { color: #fff; }
        .admin-navbar .navbar-toggler-white { border-color: rgba(255,255,255,0.5); }
        .admin-navbar .navbar-toggler-white .navbar-toggler-icon { filter: invert(1); }
        .admin-header-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(255,255,255,0.2);
            color: #fff;
            text-decoration: none;
            flex-shrink: 0;
            transition: background 0.2s ease;
        }
        .admin-header-logo:hover { background: rgba(255,255,255,0.3); color: #fff; }
        .admin-header-logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 8px;
        }
        .admin-header-logo-placeholder {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        .admin-layout-body {
            min-height: calc(100vh - 65px);
        }
        .admin-sidebar {
            min-height: calc(100vh - 65px);
            background: var(--admin-sidebar-bg);
            width: 260px;
            padding: 1rem 0.75rem;
            border-right: 1px solid var(--admin-sidebar-border);
            transition: width 0.2s ease, padding 0.2s ease;
            display: flex;
            flex-direction: column;
        }
        .admin-sidebar > .nav {
            flex: 1;
            min-height: 0;
        }
        .admin-sidebar-footer {
            flex-shrink: 0;
            padding-top: 0.75rem;
            margin-top: 0.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .admin-sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            font-weight: 500;
            padding: 0.75rem 1rem;
            margin-bottom: 2px;
            border-radius: 10px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .admin-sidebar .nav-link:hover {
            color: #fff;
            background: var(--admin-sidebar-hover);
        }
        .admin-sidebar .nav-link.active {
            color: #fff;
            background: var(--admin-sidebar-active);
            box-shadow: 0 0 0 1px rgba(255,255,255,0.1);
        }
        .admin-sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: {{ $adminHeaderColor ?? '#20c997' }};
            border-radius: 0 3px 3px 0;
        }
        .admin-sidebar .nav-item { position: relative; }
        .admin-sidebar .nav-link svg {
            flex-shrink: 0;
            opacity: 0.85;
        }
        .admin-sidebar-logout-btn:hover {
            color: #fff !important;
            background: var(--admin-sidebar-hover);
            border-radius: 10px;
        }
        .admin-main {
            flex: 1;
            padding: 2rem 2.25rem;
            background: var(--admin-gradient-subtle);
        }
        body.admin-sidebar-collapsed .admin-sidebar {
            width: 0 !important;
            padding: 0 !important;
            overflow: hidden !important;
            border-right: 0 !important;
        }
        body.admin-sidebar-collapsed .admin-sidebar * { display: none !important; }
        @media (max-width: 991.98px) {
            .admin-sidebar {
                position: fixed;
                top: 65px;
                bottom: 0;
                z-index: 1040;
                height: auto;
                box-shadow: 4px 0 15px rgba(0,0,0,0.3);
            }
        }
        .admin-sidebar-toggle {
            border: 0;
            background: rgba(255,255,255,0.12);
            color: #fff;
            border-radius: 10px;
            padding: 0.5rem 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            font-weight: 700;
        }
        .admin-sidebar-toggle:hover { background: rgba(255,255,255,0.18); color: #fff; }
        .admin-sidebar-toggle svg { opacity: 0.95; }
        .admin-page-title {
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: -0.02em;
            color: var(--admin-text);
            margin-bottom: 1.5rem;
        }
        .admin-card {
            background: var(--admin-surface);
            border: 1px solid var(--admin-border);
            border-radius: var(--admin-radius-lg);
            box-shadow: var(--admin-shadow);
            overflow: hidden;
            transition: box-shadow 0.2s ease;
        }
        .admin-card:hover { box-shadow: var(--admin-shadow-hover); }
        .admin-card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--admin-border);
            font-weight: 600;
            font-size: 0.9375rem;
            color: var(--admin-text);
        }
        .admin-alert {
            border-radius: 10px;
            border: 0;
        }
        .btn-admin-primary, .btn-primary {
            background: var(--admin-gradient);
            border: 0;
            color: #fff !important;
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            border-radius: 10px;
            transition: filter 0.2s ease, transform 0.15s ease;
        }
        .btn-admin-primary:hover, .btn-primary:hover {
            filter: brightness(1.08);
            color: #fff !important;
            transform: translateY(-1px);
        }
        .form-control, .form-select {
            border-radius: 10px;
            border-color: var(--admin-border);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 3px var(--admin-accent-soft);
        }
        .admin-table tbody tr { transition: background 0.15s ease; }
        .admin-table tbody tr:hover { background: var(--admin-accent-soft); }
        .faculty-photo-zone.border-admin { border-color: var(--admin-accent) !important; background: var(--admin-accent-soft) !important; }
        /* Global Search */
        .admin-search-wrapper {
            position: relative;
            flex: 1;
            max-width: 420px;
            margin: 0 1rem;
        }
        .admin-search-input {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 10px;
            padding: 0.45rem 0.75rem 0.45rem 2.25rem;
            color: #fff;
            font-size: 0.8125rem;
            width: 100%;
            transition: all 0.2s ease;
        }
        .admin-search-input::placeholder { color: rgba(255,255,255,0.6); }
        .admin-search-input:focus {
            background: rgba(255,255,255,0.22);
            border-color: rgba(255,255,255,0.4);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255,255,255,0.1);
        }
        .admin-search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.6);
            pointer-events: none;
        }
        .admin-search-dropdown {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: var(--admin-surface);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
            max-height: 420px;
            overflow-y: auto;
            z-index: 9999;
            display: none;
        }
        .admin-search-dropdown.show { display: block; }
        .admin-search-result {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 1rem;
            text-decoration: none;
            color: var(--admin-text);
            transition: background 0.12s ease;
            border-bottom: 1px solid rgba(0,0,0,0.04);
        }
        .admin-search-result:last-child { border-bottom: none; }
        .admin-search-result:hover, .admin-search-result.active {
            background: var(--admin-accent-soft);
            color: var(--admin-text);
        }
        .admin-search-result-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: var(--admin-accent-soft);
            color: var(--admin-accent);
        }
        .admin-search-result-title { font-weight: 500; font-size: 0.8125rem; }
        .admin-search-result-sub { font-size: 0.6875rem; color: var(--admin-text-muted); }
        .admin-search-type {
            font-size: 0.625rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--admin-text-muted);
            padding: 0.5rem 1rem 0.25rem;
        }
        .admin-search-empty {
            padding: 1.5rem 1rem;
            text-align: center;
            color: var(--admin-text-muted);
            font-size: 0.8125rem;
        }

        /* Global Loading Overlay */
        #adminLoadingOverlay {
            position: fixed;
            inset: 0;
            z-index: 100000;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            animation: fadeInOverlay 0.3s ease;
        }
        @keyframes fadeInOverlay { from { opacity: 0; } to { opacity: 1; } }
        
        .loading-spinner-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
            margin-bottom: 1.5rem;
        }
        .loading-spinner {
            position: absolute;
            inset: 0;
            border: 3px solid var(--admin-accent-soft);
            border-top-color: var(--admin-accent);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        
        .loading-logo {
            position: absolute;
            inset: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse { 0%, 100% { transform: scale(0.9); opacity: 0.7; } 50% { transform: scale(1.1); opacity: 1; } }
        
        #loadingMessage {
            font-weight: 600;
            color: var(--admin-text);
            font-size: 1.125rem;
            max-width: 300px;
        }
        #loadingSubMessage {
            color: var(--admin-text-muted);
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        /* ── Rich Text Editor Global Styles ── */
        .tox-tinymce { border-radius: 10px !important; border-color: var(--admin-border) !important; font-family: inherit; }
    </style>
    @stack('styles')
</head>
<body>
    {{-- Global Page Loading Screen --}}
    <div id="page-loader" aria-hidden="true">
        <div class="loader-spinner"></div>
    </div>
    <nav class="navbar navbar-expand-lg admin-navbar">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center gap-2 me-2">
                <a href="{{ route('admin.dashboard') }}" class="admin-header-logo" aria-label="Admin home">
                    @if(!empty($adminLogoUrl))
                        <img src="{{ $adminLogoUrl }}" alt="" class="admin-header-logo-img">
                    @else
                        <span class="admin-header-logo-placeholder">Logo</span>
                    @endif
                </a>
                <button type="button" class="admin-sidebar-toggle" id="adminSidebarToggle" aria-controls="adminSidebar" aria-expanded="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                <span class="d-none d-lg-inline">
                    @php
                        $adminUser = auth()->user();
                        $adminCollegeName = null;
                        $adminDepartmentName = null;
                        
                        if ($adminUser && method_exists($adminUser, 'isBoundedToCollege') && $adminUser->isBoundedToCollege()) {
                            $slug = $adminUser->college_slug;
                            $adminCollegeName = \App\Http\Controllers\Admin\CollegeController::getColleges()[$slug] ?? $slug;
                            
                            // Show department name if user is bounded to department
                            if (method_exists($adminUser, 'isBoundedToDepartment') && $adminUser->isBoundedToDepartment()) {
                                $adminDepartmentName = $adminUser->department;
                            }
                        }
                    @endphp
                    {{ $adminDepartmentName ? $adminDepartmentName . ' Admin' : ($adminCollegeName ? $adminCollegeName . ' Admin' : 'CLSU - CIS') }}
                </span>
                </button>
            </div>
            {{-- Global Search --}}
            <div class="admin-search-wrapper d-none d-md-block">
                <svg class="admin-search-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="admin-search-input" id="adminSearchInput" placeholder="Search articles, faculty, colleges…" autocomplete="off">
                <div class="admin-search-dropdown" id="adminSearchDropdown"></div>
            </div>
            <button class="navbar-toggler navbar-toggler-white" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <li class="nav-item">
                        <button type="button" class="nav-link d-flex align-items-center gap-1 border-0 bg-transparent" id="livePreviewBtn" title="Live Preview">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                            Live Preview
                        </button>
                    </li>

                    {{-- Live Preview Modal --}}
                    <div id="livePreviewModal" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,0.65);backdrop-filter:blur(4px);animation:lpFadeIn 0.2s ease;">
                        <div style="position:absolute;inset:2rem;background:#fff;border-radius:16px;box-shadow:0 24px 80px rgba(0,0,0,0.4);display:flex;flex-direction:column;overflow:hidden;">
                            {{-- Modal Header --}}
                            <div style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;background:#0d6e42;flex-shrink:0;">
                                <div style="display:flex;gap:6px;align-items:center;">
                                    <span style="width:12px;height:12px;border-radius:50%;background:#ff5f57;display:inline-block;"></span>
                                    <span style="width:12px;height:12px;border-radius:50%;background:#febc2e;display:inline-block;"></span>
                                    <span style="width:12px;height:12px;border-radius:50%;background:#28c840;display:inline-block;"></span>
                                </div>
                                <div style="flex:1;display:flex;align-items:center;background:rgba(255,255,255,0.15);border-radius:8px;padding:0.3rem 0.75rem;gap:0.5rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                    <span style="font-size:0.75rem;color:rgba(255,255,255,0.85);font-family:monospace;" id="livePreviewUrl">{{ url('/') }}</span>
                                </div>
                                <a href="{{ url('/') }}" target="_blank" style="color:rgba(255,255,255,0.8);text-decoration:none;font-size:0.75rem;display:flex;align-items:center;gap:4px;white-space:nowrap;" title="Open in new tab">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                    Open
                                </a>
                                <button id="livePreviewClose" type="button" style="border:0;background:rgba(255,255,255,0.15);color:#fff;border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;" title="Close preview">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>
                            {{-- iframe --}}
                            <iframe id="livePreviewFrame" src="" style="flex:1;border:0;width:100%;background:#f8f9fa;" title="Live Preview"></iframe>
                        </div>
                    </div>
                    <style>
                        @keyframes lpFadeIn { from { opacity: 0; } to { opacity: 1; } }
                    </style>
                    <script>
                    (function() {
                        var btn   = document.getElementById('livePreviewBtn');
                        var modal = document.getElementById('livePreviewModal');
                        var close = document.getElementById('livePreviewClose');
                        var frame = document.getElementById('livePreviewFrame');
                        var loaded = false;
                        if (!btn || !modal) return;
                        btn.addEventListener('click', function() {
                            modal.style.display = 'block';
                            var previewUrl = window.livePreviewUrl || '{{ url('/') }}';
                            frame.src = previewUrl;
                        });
                        close.addEventListener('click', function() { modal.style.display = 'none'; });
                        modal.addEventListener('click', function(e) { if (e.target === modal) modal.style.display = 'none'; });
                        document.addEventListener('keydown', function(e) { if (e.key === 'Escape') modal.style.display = 'none'; });
                    })();
                    </script>
                    <li class="nav-item d-md-none">
                        <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm rounded-pill px-3">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="d-flex admin-layout-body position-relative">
        <aside class="admin-sidebar" id="adminSidebar">
            <ul class="nav flex-column">
                @if (auth()->user() && method_exists(auth()->user(), 'isBoundedToDepartment') && auth()->user()->isBoundedToDepartment())
                    {{-- Department users only see their department dashboard --}}
                    @php
                        $deptUser = auth()->user();
                        $departmentRouteKey = $deptUser->getDepartmentRouteKey($deptUser->college_slug);
                    @endphp
                    @if ($departmentRouteKey !== null)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.colleges.show-department') ? 'active' : '' }}" 
                               href="{{ route('admin.colleges.show-department', ['college' => $deptUser->college_slug, 'department' => $departmentRouteKey, 'section' => 'overview']) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                Department Dashboard
                            </a>
                        </li>
                    @endif
                @else
                    {{-- Regular users see full navigation --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}" href="{{ route('admin.articles.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            Articles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}" href="{{ route('admin.announcements.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 2v7.31"/><path d="M14 9.3V1.99"/><path d="M8.5 2h7"/><path d="M14 9.3a6 6 0 1 1-4 0"/><path d="M5.52 16h12.96"/></svg>
                            Announcements
                        </a>
                    </li>
                    <li class="nav-item">
                        @php
                            $navUser = auth()->user();
                            $isCollegeAdmin = $navUser && $navUser->isAdmin() && !$navUser->isSuperAdmin() && $navUser->college_slug;
                            $collegesLabel = $isCollegeAdmin ? 'Details' : 'Colleges';
                            $collegesRoute = $isCollegeAdmin 
                                ? route('admin.colleges.show', ['college' => $navUser->college_slug])
                                : route('admin.colleges.index');
                        @endphp
                        <a class="nav-link {{ request()->routeIs('admin.colleges.*') ? 'active' : '' }}" href="{{ $collegesRoute }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 0 1 3-3h14a3 3 0 0 1 3 3v9a3 3 0 0 1-3 3H5a3 3 0 0 1-3-3V9z"/><path d="M6 6h.01"/><path d="M10 6h.01"/><path d="M14 6h.01"/></svg>
                            {{ $collegesLabel }}
                        </a>
                    </li>
                    @if ($isCollegeAdmin)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.colleges.scholarships.*') ? 'active' : '' }}" href="{{ route('admin.colleges.scholarships.index', ['college' => $navUser->college_slug]) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.1 2.7 3 6 3s6-1.9 6-3v-5"/></svg>
                            Scholarships
                        </a>
                    </li>
                    @elseif ($navUser && $navUser->isSuperAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.scholarships.*') ? 'active' : '' }}" href="{{ route('admin.scholarships.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.1 2.7 3 6 3s6-1.9 6-3v-5"/></svg>
                            Scholarships
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06 a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                            Settings
                        </a>
                    </li>
                    @if(\App\Models\Setting::get('facebook_integration_enabled_' . auth()->user()?->college_slug, '1') == '1')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.facebook.*') ? 'active' : '' }}" href="{{ route('admin.facebook.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                            Facebook
                        </a>
                    </li>
                    @endif
                    @if (auth()->user()?->isSuperAdmin() || auth()->user()?->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            Users
                        </a>
                    </li>
                    @endif
                @endif
            </ul>
            <div class="admin-sidebar-footer">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="admin-sidebar-logout-btn nav-link border-0 bg-transparent w-100 text-start d-flex align-items-center gap-2" style="padding: 0.75rem 1rem; font-weight: 500; color: rgba(255,255,255,0.75); margin: 0; border-radius: 10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>
        <main class="admin-main w-100">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show admin-alert" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show admin-alert" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        (function () {
            var btn = document.getElementById('adminSidebarToggle');
            if (!btn) return;
            var key = 'admin.sidebar.collapsed';
            function setCollapsed(collapsed) {
                document.body.classList.toggle('admin-sidebar-collapsed', collapsed);
                btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                try { localStorage.setItem(key, collapsed ? '1' : '0'); } catch (e) {}
            }
            try {
                if (window.innerWidth <= 991) {
                    setCollapsed(true);
                } else if (localStorage.getItem(key) === '1') {
                    setCollapsed(true);
                } else {
                    setCollapsed(false);
                }
            } catch (e) {}
            btn.addEventListener('click', function () {
                setCollapsed(!document.body.classList.contains('admin-sidebar-collapsed'));
            });
        })();
    </script>
    @stack('scripts')
    <script>
    (function() {
        var input = document.getElementById('adminSearchInput');
        var dropdown = document.getElementById('adminSearchDropdown');
        if (!input || !dropdown) return;
        var timer = null;
        var activeIdx = -1;

        var iconMap = {
            'file-text': '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
            'bell': '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
            'calendar': '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
            'user': '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
            'layers': '<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>',
            'building': '<rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><line x1="8" y1="6" x2="8" y2="6.01"/><line x1="16" y1="6" x2="16" y2="6.01"/><line x1="12" y1="6" x2="12" y2="6.01"/><line x1="8" y1="10" x2="8" y2="10.01"/><line x1="16" y1="10" x2="16" y2="10.01"/><line x1="12" y1="10" x2="12" y2="10.01"/><line x1="8" y1="14" x2="8" y2="14.01"/><line x1="16" y1="14" x2="16" y2="14.01"/><line x1="12" y1="14" x2="12" y2="14.01"/>',
            'home': '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>'
        };

        function makeSvg(icon) {
            return '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + (iconMap[icon] || iconMap['file-text']) + '</svg>';
        }

        function doSearch() {
            var q = input.value.trim();
            if (q.length < 2) { dropdown.classList.remove('show'); return; }
            fetch('{{ route("admin.search") }}?q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                activeIdx = -1;
                if (!data.results || data.results.length === 0) {
                    dropdown.innerHTML = '<div class="admin-search-empty">No results for "' + q + '"</div>';
                    dropdown.classList.add('show');
                    return;
                }
                var html = '';
                var lastType = '';
                data.results.forEach(function(r) {
                    if (r.type !== lastType) {
                        html += '<div class="admin-search-type">' + r.type + 's</div>';
                        lastType = r.type;
                    }
                    html += '<a href="' + r.url + '" class="admin-search-result">';
                    html += '<div class="admin-search-result-icon">' + makeSvg(r.icon) + '</div>';
                    html += '<div><div class="admin-search-result-title">' + r.title + '</div>';
                    if (r.subtitle) html += '<div class="admin-search-result-sub">' + r.subtitle + '</div>';
                    html += '</div></a>';
                });
                dropdown.innerHTML = html;
                dropdown.classList.add('show');
            })
            .catch(function() { dropdown.classList.remove('show'); });
        }

        input.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(doSearch, 300);
        });

        input.addEventListener('keydown', function(e) {
            var items = dropdown.querySelectorAll('.admin-search-result');
            if (!items.length) return;
            if (e.key === 'ArrowDown') { e.preventDefault(); activeIdx = Math.min(activeIdx + 1, items.length - 1); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); activeIdx = Math.max(activeIdx - 1, 0); }
            else if (e.key === 'Enter' && activeIdx >= 0) { e.preventDefault(); items[activeIdx].click(); return; }
            else if (e.key === 'Escape') { dropdown.classList.remove('show'); input.blur(); return; }
            else return;
            items.forEach(function(it, i) { it.classList.toggle('active', i === activeIdx); });
            if (items[activeIdx]) items[activeIdx].scrollIntoView({ block: 'nearest' });
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.admin-search-wrapper')) dropdown.classList.remove('show');
        });

        input.addEventListener('focus', function() {
            if (input.value.trim().length >= 2 && dropdown.innerHTML) dropdown.classList.add('show');
        });
    })();
    </script>
    <div id="adminLoadingOverlay">
        <div class="loading-spinner-wrapper">
            <div class="loading-spinner"></div>
            <div class="loading-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--admin-accent)"><path d="M12 2v8"/><path d="m4.93 4.93 5.66 5.66"/><path d="M2 12h8"/><path d="m4.93 19.07 5.66-5.66"/><path d="M12 22v-8"/><path d="m19.07 19.07-5.66-5.66"/><path d="M22 12h-8"/><path d="m19.07 4.93-5.66 5.66"/></svg>
            </div>
        </div>
        <div id="loadingMessage">Processing...</div>
        <div id="loadingSubMessage">Please wait, this may take a moment.</div>
    </div>
    <script>
    window.showAdminLoading = function(message, subMessage) {
        const overlay = document.getElementById('adminLoadingOverlay');
        const msgEl = document.getElementById('loadingMessage');
        const subMsgEl = document.getElementById('loadingSubMessage');
        if (overlay) {
            if (message) msgEl.textContent = message;
            if (subMessage) subMsgEl.textContent = subMessage;
            overlay.style.display = 'flex';
        }
    };
    window.hideAdminLoading = function() {
        const overlay = document.getElementById('adminLoadingOverlay');
        if (overlay) overlay.style.display = 'none';
    };
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
    // ── Global Editor Auto-Init ──
    // Any <textarea> with class "quill-editor" is auto-converted to TinyMCE for backwards compatibility.
    (function() {
        function initTinyMCE(selectorOrElement) {
            tinymce.init({
                target: (typeof selectorOrElement === 'string') ? null : selectorOrElement,
                selector: (typeof selectorOrElement === 'string') ? selectorOrElement : null,
                promotion: false,
                branding: false,
                plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
                toolbar: 'undo redo | blocks | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link image | removeformat | help',
                setup: function (editor) {
                    editor.on('change keyup', function () {
                        editor.save();
                    });
                }
            });
        }
        
        // Init existing
        initTinyMCE('textarea.quill-editor');

        // Watch for dynamic additions
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { 
                        if (node.tagName === 'TEXTAREA' && node.classList.contains('quill-editor')) {
                            initTinyMCE(node);
                        }
                        node.querySelectorAll('textarea.quill-editor').forEach(function(el) {
                            initTinyMCE(el);
                        });
                    }
                });
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    })();
    </script>

    <script>
    /* ── Global Page Loader ── */
    (function () {
        var loader = document.getElementById('page-loader');

        function showLoader() {
            if (loader) loader.classList.remove('loader-hidden');
        }
        function hideLoader() {
            if (loader) loader.classList.add('loader-hidden');
        }

        // Hide after full load (fonts, images, etc.)
        window.addEventListener('load', hideLoader);

        // Handle browser back/forward cache
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) hideLoader();
        });

        // Safety fallback: hide after 5 s no matter what
        setTimeout(hideLoader, 5000);

        // Show loader on any internal navigation link click
        document.addEventListener('click', function (e) {
            var link = e.target.closest('a[href]');
            if (!link) return;
            // Skip: new tab, external, hash-only, javascript:, ctrl/cmd+click
            if (
                e.ctrlKey || e.metaKey || e.shiftKey ||
                link.target === '_blank' ||
                link.href.startsWith('javascript') ||
                link.href.startsWith('mailto') ||
                link.getAttribute('href') === '#' ||
                (link.getAttribute('href') || '').startsWith('#')
            ) return;
            // Only same origin
            if (link.hostname && link.hostname !== window.location.hostname) return;
            showLoader();
        });

        // Show loader on any form submission
        document.addEventListener('submit', function (e) {
            var form = e.target;
            // Skip AJAX forms (those that prevent default via their own listeners are fine — they'll just flash the loader briefly)
            if (form.dataset.noLoader) return;
            showLoader();
        });
    })();
    </script>
</body>
</html>
