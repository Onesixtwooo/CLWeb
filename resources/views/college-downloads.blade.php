@php
    $collegeName = $collegeName ?? 'College';
    $collegeSlug = $collegeSlug ?? 'college';
    $collegeShortName = $collegeShortName ?? 'College';
    $collegeLogoUrl = $collegeLogoUrl ?? asset('images/colleges/main.webp');
    $headerColor = !empty($headerColor) && preg_match('/^#[0-9A-Fa-f]{6}$/', $headerColor) ? $headerColor : '#0d6e42';
    $accentColor = !empty($accentColor) && preg_match('/^#[0-9A-Fa-f]{6}$/', $accentColor) ? $accentColor : '#0d2818';
    $sectionTitle = $sectionTitle ?? 'Downloads';
    $sectionDescription = $sectionDescription ?? '';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Downloads - {{ $collegeName }} - {{ config('app.name', 'CLSU') }}">
    <title>Downloads - {{ $collegeName }} - {{ config('app.name', 'CLSU') }}</title>

    @include('includes.college-css')

    <style>
        .downloads-hero {
            background: {{ $headerColor }};
            color: #fff;
            padding: 2rem 0 1.5rem;
            margin-top: 118px;
        }
        .downloads-shell {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            gap: 2rem;
            align-items: start;
        }
        .downloads-sidebar {
            background: #fff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 14px;
            padding: 1.4rem 1.1rem;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.05);
            position: sticky;
            top: 140px;
        }
        .downloads-sidebar-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 1rem;
        }
        .downloads-nav {
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
        }
        .downloads-nav-link {
            display: block;
            text-decoration: none;
            color: #1e293b;
            padding: 0.9rem 1rem;
            border-radius: 12px;
            border: 1px solid transparent;
            background: #f8fafc;
            font-weight: 600;
            line-height: 1.35;
            transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
        }
        .downloads-nav-link:hover,
        .downloads-nav-link:focus {
            background: {{ $headerColor }};
            color: #fff;
            border-color: {{ $headerColor }};
            transform: translateX(2px);
        }
        .downloads-panel {
            background: #fff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        }
        .downloads-panel-top {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
            background: #fcfcfd;
        }
        .downloads-panel-note {
            margin: 0;
            color: #475569;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        .downloads-panel-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0.2rem 0 0;
        }
        .downloads-table-wrap {
            padding: 1.25rem;
        }
        .downloads-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid rgba(15, 23, 42, 0.08);
        }
        .downloads-table thead th {
            background: #1f2933;
            color: #fff;
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 1rem 0.85rem;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }
        .downloads-table thead th:last-child {
            border-right: none;
        }
        .downloads-table tbody td {
            padding: 1.1rem 0.85rem;
            vertical-align: middle;
            border-top: 1px solid rgba(15, 23, 42, 0.08);
            border-right: 1px solid rgba(15, 23, 42, 0.08);
            color: #1f2937;
        }
        .downloads-table tbody td:last-child {
            border-right: none;
        }
        .downloads-file-title {
            font-size: 0.98rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.3rem;
        }
        .downloads-file-name {
            color: #64748b;
            font-size: 0.88rem;
            line-height: 1.45;
        }
        .downloads-file-type {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 58px;
            padding: 0.35rem 0.6rem;
            border-radius: 999px;
            background: {{ $headerColor }}14;
            color: {{ $headerColor }};
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .downloads-meta-cell {
            color: #334155;
            font-size: 0.92rem;
            white-space: nowrap;
        }
        .downloads-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 150px;
            text-decoration: none;
            border: none;
            border-radius: 999px;
            background: {{ $headerColor }};
            color: #fff;
            padding: 0.75rem 1rem;
            font-weight: 700;
            font-size: 0.95rem;
        }
        .downloads-action:hover,
        .downloads-action:focus {
            color: #fff;
            filter: brightness(0.95);
        }
        .downloads-empty {
            text-align: center;
            padding: 3rem 1.25rem;
            color: #6b7280;
        }
        @media (max-width: 991.98px) {
            .downloads-shell {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            .downloads-sidebar {
                position: static;
            }
        }
        @media (max-width: 767.98px) {
            .downloads-hero {
                padding: 1.6rem 0 1.2rem;
            }
            .downloads-table-wrap {
                padding: 0;
            }
            .downloads-table,
            .downloads-table thead,
            .downloads-table tbody,
            .downloads-table th,
            .downloads-table td,
            .downloads-table tr {
                display: block;
                width: 100%;
            }
            .downloads-table thead {
                display: none;
            }
            .downloads-table {
                border: none;
            }
            .downloads-table tbody tr {
                border-top: 1px solid rgba(15, 23, 42, 0.08);
                padding: 1rem;
            }
            .downloads-table tbody td {
                border: none;
                padding: 0.35rem 0;
            }
            .downloads-meta-cell {
                white-space: normal;
            }
            .downloads-action {
                width: 100%;
                margin-top: 0.4rem;
            }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/css/college.css', 'resources/js/app.js'])
</head>
<body class="retro-modern college-page">
    @include('includes.college-header')

    <main>
        <section class="downloads-hero">
            <div class="container text-center">
                <h1 class="display-5 fw-900 mb-2">{{ $sectionTitle }}</h1>
                @if (!empty(trim($sectionDescription)))
                    <div class="mx-auto" style="max-width: 760px; font-size: 1rem; line-height: 1.6;">{!! $sectionDescription !!}</div>
                @endif
            </div>
        </section>

        <section class="py-5 bg-white">
            <div class="container pb-4">
                @if ($downloads->isEmpty())
                    <div class="downloads-empty">
                        <h3 class="h4 mb-2">No files available yet</h3>
                        <p class="mb-0">Please check back later for downloadable resources from {{ $collegeShortName }}.</p>
                    </div>
                @else
                    <div class="downloads-shell">
                        <aside class="downloads-sidebar">
                            <h2 class="downloads-sidebar-title">Select file</h2>
                            <nav class="downloads-nav" aria-label="Downloads navigation">
                                @foreach ($downloads as $download)
                                    <a href="#download-{{ $download->id }}" class="downloads-nav-link">{{ $download->title }}</a>
                                @endforeach
                            </nav>
                        </aside>

                        <section class="downloads-panel">
                            <div class="downloads-panel-top">
                                <p class="downloads-panel-note">
                                    Browse downloadable forms and reference files for {{ $collegeShortName }}.
                                </p>
                                <h2 class="downloads-panel-title">Files List</h2>
                            </div>

                            <div class="downloads-table-wrap">
                                <table class="downloads-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 44%;">Filename</th>
                                            <th style="width: 14%;">Type</th>
                                            <th style="width: 14%;">File Size</th>
                                            <th style="width: 14%;">Date Added</th>
                                            <th style="width: 14%;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($downloads as $download)
                                            @php
                                                $ext = strtoupper(pathinfo($download->file_name, PATHINFO_EXTENSION));
                                                $sizeKb = max(1, (int) ceil(($download->file_size ?? 0) / 1024));
                                            @endphp
                                            <tr id="download-{{ $download->id }}">
                                                <td>
                                                    <div class="downloads-file-title">{{ $download->title }}</div>
                                                    <div class="downloads-file-name">{{ $download->file_name }}</div>
                                                    @if ($download->description)
                                                        <div class="downloads-file-name mt-2">{{ \Illuminate\Support\Str::limit(strip_tags($download->description), 110) }}</div>
                                                    @endif
                                                </td>
                                                <td class="downloads-meta-cell">
                                                    <span class="downloads-file-type">{{ $ext ?: 'FILE' }}</span>
                                                </td>
                                                <td class="downloads-meta-cell">{{ number_format($sizeKb) }} KB</td>
                                                <td class="downloads-meta-cell">{{ optional($download->created_at)->format('M. d, Y') }}</td>
                                                <td>
                                                    <a href="{{ route('college.downloads.file', ['college' => $collegeSlug, 'download' => $download]) }}" class="downloads-action">
                                                        Download
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                @endif
            </div>
        </section>
    </main>

    @include('includes.college-footer')
    @include('includes.college-scripts')
</body>
</html>
