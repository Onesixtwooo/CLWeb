@extends('admin.layout')

@section('title', $collegeName)

@push('styles')
<style>
    .colleges-header-bar {
        background: var(--admin-surface);
        border-bottom: 1px solid var(--admin-border);
        padding: 0.875rem 1.5rem;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 1rem;
        margin: -2rem -2.25rem 1.5rem -2.25rem;
        padding: 1rem 2.25rem;
    }
    .colleges-header-title {
        font-weight: 700;
        font-size: 1.25rem;
        letter-spacing: -0.02em;
        color: var(--admin-text);
        margin: 0;
    }
    .colleges-header-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-left: auto;
    }
    .colleges-header-actions .btn-icon {
        width: 36px;
        height: 36px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        color: var(--admin-text-muted);
        border: 1px solid var(--admin-border);
        background: var(--admin-surface);
        transition: all 0.2s ease;
    }
    .colleges-header-actions .btn-icon:hover {
        color: var(--admin-accent);
        background: var(--admin-accent-soft);
        border-color: transparent;
    }
    .colleges-breadcrumb {
        font-size: 0.8125rem;
        color: var(--admin-text-muted);
    }
    .colleges-breadcrumb span:last-child { color: var(--admin-text); font-weight: 500; }
    .colleges-layout {
        display: flex;
        gap: 0;
        min-height: 480px;
    }
    .colleges-section-list {
        width: 280px;
        flex-shrink: 0;
        background: var(--admin-surface);
        border: 1px solid var(--admin-border);
        border-radius: var(--admin-radius-lg);
        overflow: hidden;
        margin-right: 1.5rem;
    }
    .colleges-section-list-header {
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid var(--admin-border);
        font-weight: 600;
        font-size: 0.8125rem;
        color: var(--admin-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .colleges-section-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        color: var(--admin-text);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9375rem;
        border-left: 3px solid transparent;
        transition: background 0.15s ease, border-color 0.15s ease;
    }
    .colleges-section-item-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        min-width: 0;
    }
    .colleges-section-status-dot {
        width: 0.625rem;
        height: 0.625rem;
        border-radius: 999px;
        flex-shrink: 0;
        box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.08);
    }
    .colleges-section-status-dot.is-visible {
        background: #198754;
    }
    .colleges-section-status-dot.is-hidden {
        background: #dc3545;
    }
    .colleges-section-item:hover {
        background: var(--admin-accent-soft);
        color: var(--admin-accent);
    }
    .colleges-section-item.active {
        background: var(--admin-accent-soft);
        color: var(--admin-accent);
        border-left-color: var(--admin-accent);
    }
    .colleges-detail {
        width: 100%;
        background: var(--admin-surface);
        border: 1px solid var(--admin-border);
        border-radius: var(--admin-radius-lg);
        overflow: hidden;
        padding: 1.75rem 2rem;
    }
    .colleges-detail-title {
        font-weight: 700;
        font-size: 1.5rem;
        letter-spacing: -0.02em;
        color: var(--admin-text);
        margin-bottom: 1.25rem;
    }
    .colleges-detail-body {
        color: var(--admin-text);
        line-height: 1.65;
    }
    .colleges-detail-body p { margin-bottom: 1rem; }
    .colleges-detail-body ul { margin-bottom: 1rem; padding-left: 1.5rem; }
    .colleges-detail-body li { margin-bottom: 0.35rem; }
    
    .stat-card {
        background: var(--admin-surface);
        border: 1px solid var(--admin-border);
        border-radius: var(--admin-radius);
        padding: 1rem;
        height: 100%;
    }
    .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--admin-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }
    .stat-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--admin-text);
    }

    /* Live Page Preview */
    .live-preview-toolbar {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: var(--admin-bg, #f7f8fa);
        border: 1px solid var(--admin-border);
        border-radius: var(--admin-radius-lg) var(--admin-radius-lg) 0 0;
        flex-wrap: wrap;
    }
    .live-preview-toolbar .viewport-btn {
        width: 34px;
        height: 34px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid var(--admin-border);
        background: var(--admin-surface);
        color: var(--admin-text-muted);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .live-preview-toolbar .viewport-btn:hover,
    .live-preview-toolbar .viewport-btn.active {
        background: var(--admin-accent-soft);
        color: var(--admin-accent);
        border-color: var(--admin-accent);
    }
    .live-preview-toolbar .toolbar-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-left: auto;
    }
    .live-preview-frame-wrapper {
        position: relative;
        border: 1px solid var(--admin-border);
        border-top: none;
        border-radius: 0 0 var(--admin-radius-lg) var(--admin-radius-lg);
        overflow: hidden;
        background: #e9ecef;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }
    .live-preview-frame-wrapper iframe {
        border: none;
        background: #fff;
        transition: width 0.3s ease, transform 0.3s ease;
        transform-origin: top center;
    }
    .live-preview-loading {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.85);
        z-index: 5;
        font-size: 0.875rem;
        color: var(--admin-text-muted);
        gap: 0.5rem;
    }
    .live-preview-loading .spinner-border {
        width: 1.25rem;
        height: 1.25rem;
        border-width: 2px;
    }
    .live-preview-section-link {
        border-bottom: 1px solid var(--admin-border);
    }

    /* Fullscreen mode */
    .live-preview-fullscreen {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: #fff;
        display: flex;
        flex-direction: column;
    }
    .live-preview-fullscreen .live-preview-toolbar {
        border-radius: 0;
        border-left: none;
        border-right: none;
        border-top: none;
    }
    .live-preview-fullscreen .live-preview-frame-wrapper {
        flex: 1;
        height: auto !important;
        border-radius: 0;
        border: none;
    }
</style>
@endpush

@section('content')
    {{-- Top header bar (college name, actions, breadcrumbs) --}}
    <div class="colleges-header-bar">
        <h1 class="colleges-header-title">{{ $collegeName }}</h1>
        <div class="colleges-header-actions">

            @if ($currentSection === 'departments')
                <!-- Edit Department Section Modal -->
                <div class="modal fade" id="editDeptSectionModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit department section</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST">
                                @csrf
                                <div class="modal-body">
                                    <input type="hidden" name="college" value="{{ $collegeSlug }}">
                                    <input type="hidden" name="section" value="{{ $currentSection }}">
                                    <input type="hidden" name="save_dept_section" value="1">
                                    <input type="hidden" name="edit_dept_index" value="{{ $selectedDepartment?->id ?? '' }}">
                                    <div class="mb-3">
                                        <label for="dept_section_content" class="form-label">Content</label>
                                        <textarea name="dept_section_content" id="dept_section_content" class="form-control quill-editor" rows="10">{{ $selectedDepartment?->details ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-admin-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            <span class="colleges-breadcrumb px-2">
                {{ $collegeName }} / {{ $content['title'] }}
            </span>
        </div>
    </div>

    <div class="colleges-layout">
        {{-- Left: section list (like Handbook, Workflow, etc.) --}}
        <aside class="colleges-section-list">
            <div class="colleges-section-list-header">Sections</div>
            <nav>
                <div class="live-preview-section-link">
                    <a href="{{ route('admin.colleges.show', ['college' => $collegeSlug, 'section' => 'live-page']) }}"
                       class="colleges-section-item {{ $currentSection === 'live-page' ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                        Live Page
                    </a>
                </div>
                @foreach ($sections as $slug => $name)
                    @php 
                        $secStatus = $sectionStatuses[$slug] ?? null; 
                        $isComplete = $completedSections[$slug] ?? true;
                        $isVisible = $secStatus ? (bool) $secStatus->is_visible : true;
                    @endphp
                    <a href="{{ route('admin.colleges.show', ['college' => $collegeSlug, 'section' => $sectionUrlSlugs[$slug] ?? $slug]) }}"
                       class="colleges-section-item {{ $currentSection === $slug ? 'active' : '' }}">
                        <span class="colleges-section-item-label">
                            {{ $slug === 'admissions' ? 'Admissions' : $name }}
                        </span>
                        <span style="display: flex; align-items: center; gap: 0.5rem;">
                            <span class="colleges-section-status-dot {{ $isVisible ? 'is-visible' : 'is-hidden' }}" title="{{ $isVisible ? 'Visible on Public Page' : 'Hidden from Public Page' }}" aria-label="{{ $isVisible ? 'Visible on Public Page' : 'Hidden from Public Page' }}"></span>
                            @if (!$isComplete && $slug !== 'appearance')
                                <span class="badge rounded-pill" style="background: rgba(239,68,68,0.15); color: #ef4444; font-size: 0.625rem; font-weight: 600; padding: 0.2em 0.5em;">Not set</span>
                            @endif
                        </span>
                    </a>
                @endforeach
            </nav>
        </aside>

        {{-- Right: detail content --}}
        <div style="flex: 1; min-width: 0;">
            @if ($currentSection === 'live-page')
                {{-- Live Page Preview --}}
                <div id="livePreviewContainer">
                <div class="live-preview-toolbar">
                    <span class="fw-600" style="font-size: 0.875rem; color: var(--admin-text);">Live Preview</span>
                    <div class="d-flex gap-1">
                        <button type="button" class="viewport-btn active" data-width="100%" title="Desktop">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                        </button>
                        <button type="button" class="viewport-btn" data-width="768px" title="Tablet">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                        </button>
                        <button type="button" class="viewport-btn" data-width="375px" title="Mobile">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                        </button>
                    </div>
                    <div class="toolbar-actions">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('livePreviewFrame').src = document.getElementById('livePreviewFrame').src;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 3px;"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                            Refresh
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="fullscreenBtn" title="Toggle fullscreen">
                            <svg id="fullscreenIcon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 3px;"><polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline><line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line></svg>
                            <span id="fullscreenLabel">Fullscreen</span>
                        </button>
                    </div>
                </div>
                <div class="live-preview-frame-wrapper" id="livePreviewWrapper" style="height: 700px;">
                    <div class="live-preview-loading" id="livePreviewLoading">
                        <div class="spinner-border text-secondary" role="status"></div>
                        Loading preview…
                    </div>
                    <iframe id="livePreviewFrame"
                            src="{{ route('college.show', ['college' => $collegeSlug]) }}"
                            style="width: 100%; height: 100%;"
                            onload="document.getElementById('livePreviewLoading').style.display='none';"></iframe>
                </div>
                </div>
                <script>
                (function() {
                    var btns = document.querySelectorAll('.viewport-btn');
                    var frame = document.getElementById('livePreviewFrame');
                    var wrapper = document.getElementById('livePreviewWrapper');
                    var container = document.getElementById('livePreviewContainer');
                    var fsBtn = document.getElementById('fullscreenBtn');
                    var fsIcon = document.getElementById('fullscreenIcon');
                    var fsLabel = document.getElementById('fullscreenLabel');
                    var isFullscreen = false;
                    var currentWidth = '100%';

                    function applyViewport(targetWidth) {
                        currentWidth = targetWidth;
                        if (targetWidth === '100%') {
                            frame.style.width = '100%';
                            frame.style.height = '100%';
                            frame.style.transform = 'none';
                            wrapper.style.overflow = 'hidden';
                        } else {
                            var pxWidth = parseInt(targetWidth);
                            var containerWidth = wrapper.clientWidth;
                            if (pxWidth > containerWidth) {
                                // Scale down to fit
                                var scale = containerWidth / pxWidth;
                                frame.style.width = pxWidth + 'px';
                                frame.style.height = (wrapper.clientHeight / scale) + 'px';
                                frame.style.transform = 'scale(' + scale + ')';
                                wrapper.style.overflow = 'hidden';
                            } else {
                                frame.style.width = pxWidth + 'px';
                                frame.style.height = '100%';
                                frame.style.transform = 'none';
                                wrapper.style.overflow = 'hidden';
                            }
                        }
                    }

                    btns.forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            btns.forEach(function(b) { b.classList.remove('active'); });
                            btn.classList.add('active');
                            applyViewport(btn.getAttribute('data-width'));
                        });
                    });

                    // Fullscreen toggle
                    fsBtn.addEventListener('click', function() {
                        isFullscreen = !isFullscreen;
                        if (isFullscreen) {
                            container.classList.add('live-preview-fullscreen');
                            document.body.style.overflow = 'hidden';
                            fsLabel.textContent = 'Exit';
                            fsIcon.innerHTML = '<polyline points="4 14 10 14 10 20"></polyline><polyline points="20 10 14 10 14 4"></polyline><line x1="14" y1="10" x2="21" y2="3"></line><line x1="3" y1="21" x2="10" y2="14"></line>';
                        } else {
                            container.classList.remove('live-preview-fullscreen');
                            document.body.style.overflow = '';
                            fsLabel.textContent = 'Fullscreen';
                            fsIcon.innerHTML = '<polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline><line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line>';
                        }
                        // Re-apply viewport after layout change
                        setTimeout(function() { applyViewport(currentWidth); }, 100);
                    });

                    // Handle Escape key to exit fullscreen
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape' && isFullscreen) {
                            fsBtn.click();
                        }
                    });

                    // Re-apply viewport on window resize
                    window.addEventListener('resize', function() {
                        applyViewport(currentWidth);
                    });
                })();
                </script>
            @endif

            <div class="colleges-detail" @if($currentSection === 'live-page') style="display:none;" @endif>
                @php
                    $currentSectionStatus = $sectionStatuses[$currentSection] ?? null;
                    $currentSectionVisible = $currentSectionStatus ? (bool) $currentSectionStatus->is_visible : true;
                @endphp
                @if ($currentSection === 'overview')
                    <div class="mb-3">
                        @if (!empty($collegeModel?->icon))
                            @php
                                preg_match('/[?&]id=([^&]+)/', $collegeModel->icon, $_iconM);
                                $_iconSrc = isset($_iconM[1])
                                    ? route('media.proxy.public', ['fileId' => $_iconM[1]])
                                    : asset($collegeModel->icon);
                            @endphp
                            <img src="{{ $_iconSrc }}" alt="{{ $collegeName }} icon" style="max-width: 120px; max-height: 120px; object-fit: contain;" class="rounded">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; color: #ccc; font-size: 0.875rem; text-align: center; padding: 1rem;">
                                No icon uploaded
                            </div>
                        @endif
                    </div>
                @endif
                {{-- All sections render their basic title and body inside this first colleges-detail container --}}
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <h2 class="colleges-detail-title mb-0">{{ $content['title'] }}</h2>
                    @if ($currentSection !== 'departments' && $currentSection !== 'explore' && $currentSection !== 'facilities' && $currentSection !== 'contact' && $currentSection !== 'training' && $currentSection !== 'downloads')
                        <a href="{{ route('admin.colleges.edit-section', ['college' => $collegeSlug, 'section' => $currentSection]) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil-square"></i> <span class="d-none d-md-inline">Edit section details</span></a>
                    @elseif ($currentSection === 'departments' || $currentSection === 'explore' || $currentSection === 'facilities' || $currentSection === 'extension' || $currentSection === 'training' || $currentSection === 'downloads' || $currentSection === 'faq' || $currentSection === 'scholarships')
                        <a href="{{ route('admin.colleges.edit-section', ['college' => $collegeSlug, 'section' => $currentSection]) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil-square"></i> <span class="d-none d-md-inline">Edit section details</span></a>
                    @endif
                </div>

                @if ($currentSection !== 'appearance')
                    <div class="mb-3">
                        <form method="POST" action="{{ route('admin.colleges.toggle-visibility', ['college' => $collegeSlug, 'section' => $currentSection]) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="badge border-0 {{ $currentSectionVisible ? 'bg-success' : 'bg-danger' }}" style="cursor: pointer;">
                                {{ $currentSectionVisible ? 'Visible on Public Page' : 'Hidden from Public Page' }}
                            </button>
                        </form>
                    </div>
                @endif

                <div class="colleges-detail-body mb-4">
                    @if ($currentSection === 'overview')
                        @php $contact = $content['contact_data'] ?? null; @endphp
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="stat-card">
                                    <div class="stat-label">Contact Email</div>
                                    <div class="stat-value">{{ $contact->email ?? 'Not set' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-card">
                                    <div class="stat-label">Phone Number</div>
                                    <div class="stat-value">{{ $contact->phone ?? 'Not set' }}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($currentSection === 'appearance')
                        <form method="POST" action="{{ route('admin.colleges.appearance.update', ['college' => $collegeSlug]) }}" class="row g-3" enctype="multipart/form-data">
                            @csrf
                            <div class="col-12">
                                <label class="form-label d-block mb-2">Logo</label>
                                <input type="file" name="admin_logo" id="admin_logo" accept="image/*" class="d-none">
                                <div class="d-flex flex-wrap align-items-center gap-3">
                                    <div id="admin-logo-zone" class="border rounded-3 bg-light d-flex align-items-center justify-content-center text-muted position-relative overflow-hidden"
                                         style="width: 120px; height: 120px; cursor: pointer;">
                                        <img id="admin-logo-preview"
                                             src="@if(!empty($content['adminLogoPath']))@php preg_match('/[?&]id=([^&]+)/', $content['adminLogoPath'], $_lm); echo isset($_lm[1]) ? route('media.proxy.public', ['fileId' => $_lm[1]]) : asset($content['adminLogoPath']); @endphp@endif"
                                             alt="Logo preview"
                                             class="w-100 h-100 position-absolute top-0 start-0 {{ !empty($content['adminLogoPath']) ? '' : 'd-none' }}"
                                             style="object-fit: cover;">
                                        <div id="admin-logo-placeholder" class="small text-center px-2 {{ !empty($content['adminLogoPath']) ? 'd-none' : '' }}">
                                            Drag &amp; drop<br>or click
                                        </div>
                                    </div>
                                    <div class="text-muted small">
                                        <div class="fw-600 text-dark">Upload logo</div>
                                        <div>PNG/JPG/WebP/GIF, max 2MB.</div>
                                        @if (!empty($content['adminLogoPath']))
                                            <div class="mt-1">Current: <code>{{ $content['adminLogoPath'] }}</code></div>
                                        @endif
                                    </div>
                                </div>
                                @error('admin_logo')
                                    <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="admin_header_color" class="form-label">Header color</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" name="admin_header_color" id="admin_header_color" class="form-control form-control-color p-1" style="width: 3rem; height: 2.5rem;" value="{{ old('admin_header_color', $content['headerColor']) }}" title="Choose header color">
                                    <input type="text" class="form-control" value="{{ old('admin_header_color', $content['headerColor']) }}" maxlength="7" pattern="#[0-9A-Fa-f]{6}" id="admin_header_color_hex" placeholder="#0d6e42" style="max-width: 8rem;">
                                </div>
                                @error('admin_header_color')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-admin-primary">
                                    Save appearance
                                </button>
                            </div>
                        </form>

                        <script>
                        (function () {
                            function syncColor(inputColor, inputHex) {
                                if (!inputColor || !inputHex) return;
                                inputColor.addEventListener('input', function () {
                                    inputHex.value = this.value;
                                });
                                inputHex.addEventListener('input', function () {
                                    var v = this.value;
                                    if (/^#[0-9A-Fa-f]{6}$/.test(v)) inputColor.value = v;
                                });
                            }
                            syncColor(document.getElementById('admin_header_color'), document.getElementById('admin_header_color_hex'));

                            var logoInput = document.getElementById('admin_logo');
                            var zone = document.getElementById('admin-logo-zone');
                            var img = document.getElementById('admin-logo-preview');
                            var ph = document.getElementById('admin-logo-placeholder');

                            function showFile(file) {
                                if (!file || !file.type || !file.type.startsWith('image/')) return;
                                var url = URL.createObjectURL(file);
                                img.onload = function () { URL.revokeObjectURL(url); };
                                img.src = url;
                                img.classList.remove('d-none');
                                ph.classList.add('d-none');
                            }

                            if (zone && logoInput) {
                                zone.addEventListener('click', function () { logoInput.click(); });
                                zone.addEventListener('dragover', function (e) { e.preventDefault(); zone.classList.add('border-admin'); });
                                zone.addEventListener('dragleave', function (e) { e.preventDefault(); zone.classList.remove('border-admin'); });
                                zone.addEventListener('drop', function (e) {
                                    e.preventDefault();
                                    zone.classList.remove('border-admin');
                                    var file = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
                                    if (file) {
                                        var dt = new DataTransfer();
                                        dt.items.add(file);
                                        logoInput.files = dt.files;
                                        showFile(file);
                                    }
                                });
                                logoInput.addEventListener('change', function () {
                                    var file = this.files && this.files[0];
                                    if (file) showFile(file);
                                });
                            }
                        })();
                        </script>
                    @elseif (!empty($content['plain']))
                        {!! nl2br(e($content['body'] ?? '')) !!}
                    @elseif (!empty($content['body']))
                        {!! $content['body'] !!}
                    @elseif ($currentSection === 'departments' || $currentSection === 'explore' || $currentSection === 'facilities')
                        <p class="text-muted">No description yet.</p>
                    @endif
                </div>

                {{-- Integrated Departments List --}}
                @if ($currentSection === 'departments')
                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h3 class="h5 mb-0 fw-600">Departments</h3>
                        <button type="button" class="btn btn-admin-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#addDepartmentForm"><i class="bi bi-folder-plus"></i> <span class="d-none d-md-inline">Add department</span></button>
                    </div>
                    
                    <div class="collapse mb-4" id="addDepartmentForm">
                        <div class="admin-card">
                            <div class="card-body p-4">
                                <h4 class="h6 fw-600 mb-3">Add new department</h4>
                                <form method="POST" enctype="multipart/form-data" class="row g-3">
                                    @csrf
                                    <input type="hidden" name="college" value="{{ $collegeSlug }}">
                                    <input type="hidden" name="section" value="{{ $currentSection }}">
                                    <div class="col-md-6">
                                        <label for="dept_name" class="form-label">Department name</label>
                                        <input type="text" name="dept_name" id="dept_name" class="form-control @error('dept_name') is-invalid @enderror" value="{{ old('dept_name') }}" required>
                                        @error('dept_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="dept_logo" class="form-label">Department logo</label>
                                        <input type="file" name="dept_logo" id="dept_logo" class="form-control @error('dept_logo') is-invalid @enderror" accept="image/*">
                                        <small class="text-muted">Optional. Upload a PNG, JPG, or SVG file.</small>
                                        @error('dept_logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="dept_details" class="form-label">Description</label>
                                        <textarea name="dept_details" id="dept_details" class="form-control quill-editor @error('dept_details') is-invalid @enderror" rows="3">{{ old('dept_details') }}</textarea>
                                        <small class="text-muted">Optional short description.</small>
                                        @error('dept_details')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-admin-primary"><i class="bi bi-folder-plus"></i> <span class="d-none d-md-inline">Add department</span></button>
                                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#addDepartmentForm">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="list-group">
                        <div class="list-group-item bg-light d-none d-md-block">
                            <div class="row align-items-center g-3">
                                <div class="col-auto" style="width: 60px;">
                                    <strong class="text-muted small">Logo</strong>
                                </div>
                                <div class="col">
                                    <strong class="text-muted small">Department name</strong>
                                </div>
                                <div class="col-12 col-md-auto">
                                    <strong class="text-muted small">Actions</strong>
                                </div>
                            </div>
                        </div>
                        @if ($departmentsList->isEmpty())
                            <div class="list-group-item">
                                <p class="text-muted small mb-0">No departments added yet. <a href="#addDepartmentForm" data-bs-toggle="collapse">Add one</a>.</p>
                            </div>
                        @else
                            @foreach ($departmentsList as $dept)
                                <div class="list-group-item">
                                    <div class="row align-items-center g-3">
                                        <div class="col-auto" style="width: 60px;">
                                            @if (!empty($dept->logo))
                                                <img src="{{ $dept->logo }}" alt="{{ $dept->name }}" class="img-fluid rounded" style="max-width: 50px; max-height: 50px; object-fit: contain;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; color: #ccc; font-size: 0.8rem;">No logo</div>
                                            @endif
                                        </div>
                                            <div class="col">
                                                <a href="{{ route('admin.colleges.show', ['college' => $collegeSlug, 'section' => $currentSection]) }}?department={{ $dept->id }}" class="text-decoration-none text-reset fw-500">{{ $dept->name }}</a>
                                        </div>
                                        <div class="col-12 col-md-auto">
                                            <div class="d-flex align-items-center gap-2 justify-content-start justify-content-md-end mt-2 mt-md-0">
                                                <a href="{{ route('admin.colleges.show-department', ['college' => $collegeSlug, 'department' => $dept, 'section' => 'overview']) }}" class="btn btn-sm btn-outline-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Open department" aria-label="Open department">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" data-bs-toggle="modal" data-bs-target="#editDepartment{{ $dept->id }}" title="Edit department" aria-label="Edit department">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                                        <path d="m15 5 4 4"></path>
                                                    </svg>
                                                </button>
                                            <form action="" method="POST" class="d-inline" onsubmit="return confirm('Remove this department?');">
                                                @csrf
                                                <input type="hidden" name="college" value="{{ $collegeSlug }}">
                                                <input type="hidden" name="section" value="{{ $currentSection }}">
                                                <input type="hidden" name="delete_dept" value="{{ $dept->id }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Delete department" aria-label="Delete department">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                                    </svg>
                                                </button>
                                            </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editDepartment{{ $dept->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit department</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-body">
                                                    <input type="hidden" name="college" value="{{ $collegeSlug }}">
                                                    <input type="hidden" name="section" value="{{ $currentSection }}">
                                                    <input type="hidden" name="edit_dept" value="{{ $dept->id }}">
                                                    <div class="mb-3">
                                                        <label for="edit_name_{{ $dept->id }}" class="form-label">Department name</label>
                                                        <input type="text" name="edit_name" id="edit_name_{{ $dept->id }}" class="form-control" value="{{ $dept->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_logo_{{ $dept->id }}" class="form-label">Department logo</label>
                                                        @if (!empty($dept->logo))
                                                            <div class="mb-2">
                                                                <img src="{{ $dept->logo }}" alt="{{ $dept->name }}" class="img-fluid rounded" style="max-width: 100px; max-height: 100px; object-fit: contain;">
                                                            </div>
                                                        @endif
                                                        <input type="file" name="edit_logo" id="edit_logo_{{ $dept->id }}" class="form-control" accept="image/*">
                                                        <small class="text-muted">Leave empty to keep the current logo.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-admin-primary">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endif
                
                @if ($currentSection === 'extension')
                    <div class="colleges-detail extension-activities-detail">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                            <h2 class="colleges-detail-title mb-0">Extension Activities</h2>
                            <div>
                                <a href="{{ route('admin.colleges.extensions.create', ['college' => $collegeSlug]) }}" class="btn btn-primary btn-sm me-1"><i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add activity</span></a>
                            </div>
                        </div>
                        <div class="colleges-detail-body">
                             @if($extensionList->isEmpty())
                                <p class="text-muted fst-italic">No extension activities added yet.</p>
                            @else
                                <div class="row g-4">
                                    @foreach($extensionList as $item)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="card h-100 shadow-sm border-admin-light">
                                                @php
                                                    $displayImage = \App\Providers\AppServiceProvider::resolveImageUrl(!empty($item->image) ? $item->image : ($collegeModel->icon ?? null));
                                                @endphp
                                                <div class="position-absolute top-0 end-0 d-flex gap-2 p-2" style="z-index: 2;">
                                                    <a href="{{ route('admin.colleges.extensions.edit', ['college' => $collegeSlug, 'extension' => $item->id]) }}" class="btn btn-sm btn-light border shadow-sm" title="Edit activity" aria-label="Edit activity">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('admin.colleges.extensions.destroy', ['college' => $collegeSlug, 'extension' => $item->id]) }}" method="POST" onsubmit="return confirm('Delete this extension activity?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-light border shadow-sm text-danger" title="Remove activity" aria-label="Remove activity">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                @if($displayImage)
                                                    <div class="ratio ratio-16x9 bg-light">
                                                        <img src="{{ $displayImage }}" class="card-img-top" alt="{{ $item->title ?? 'Extension' }}" style="object-fit: {{ !empty($item->image) ? 'cover' : 'contain' }}; padding: {{ !empty($item->image) ? '0' : '1.5rem' }};">
                                                    </div>
                                                @else
                                                     <div class="ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center border-bottom">
                                                        <span class="text-muted small">No Image</span>
                                                    </div>
                                                @endif
                                                <div class="card-body">
                                                    <h6 class="fw-bold mb-2">{{ $item->title ?? 'No Title' }}</h6>
                                                    @if(!empty($item->description))
                                                        <p class="card-text small text-muted">{{ Str::limit(strip_tags($item->description), 100) }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                
                @if ($currentSection === 'training')
                    <div class="colleges-detail">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                            <h2 class="colleges-detail-title mb-0">Training & Workshops</h2>
                            <div>
                                <a href="{{ route('admin.colleges.trainings.create', ['college' => $collegeSlug]) }}" class="btn btn-primary btn-sm me-1"><i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add activity</span></a>
                            </div>
                        </div>
                        <div class="colleges-detail-body">
                             @if($trainingList->isEmpty())
                                <p class="text-muted fst-italic">No training workshops added yet.</p>
                            @else
                                 <div class="row g-4">
                                    @foreach($trainingList as $item)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="card h-100 shadow-sm border-admin-light">
                                                @php
                                                    $displayImage = \App\Providers\AppServiceProvider::resolveImageUrl(!empty($item->image) ? $item->image : ($collegeModel->icon ?? null));
                                                @endphp
                                                <div class="position-absolute top-0 end-0 d-flex gap-2 p-2" style="z-index: 2;">
                                                    <a href="{{ route('admin.colleges.trainings.edit', ['college' => $collegeSlug, 'training' => $item]) }}" class="btn btn-sm btn-light border shadow-sm" title="Edit activity" aria-label="Edit activity">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('admin.colleges.trainings.destroy', ['college' => $collegeSlug, 'training' => $item]) }}" method="POST" onsubmit="return confirm('Delete this training activity?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-light border shadow-sm text-danger" title="Remove activity" aria-label="Remove activity">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                @if($displayImage)
                                                    <div class="ratio ratio-16x9 bg-light">
                                                        <img src="{{ $displayImage }}" class="card-img-top" alt="{{ $item->title ?? 'Training' }}" style="object-fit: {{ !empty($item->image) ? 'cover' : 'contain' }}; padding: {{ !empty($item->image) ? '0' : '1.5rem' }};">
                                                    </div>
                                                @else
                                                     <div class="ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center border-bottom">
                                                        <span class="text-muted small">No Image</span>
                                                    </div>
                                                @endif
                                                <div class="card-body">
                                                    <h6 class="fw-bold mb-2">{{ $item->title ?? 'No Title' }}</h6>
                                                    @if(!empty($item->description))
                                                        <p class="card-text small text-muted">{{ Str::limit(strip_tags($item->description), 100) }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($currentSection === 'downloads')
                    <div class="colleges-detail">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                            <h2 class="colleges-detail-title mb-0">File Resources</h2>
                            <div>
                                <a href="{{ route('admin.colleges.downloads.create', ['college' => $collegeSlug]) }}" class="btn btn-primary btn-sm me-1"><i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add file</span></a>
                                <a href="{{ route('admin.colleges.downloads.index', ['college' => $collegeSlug]) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-list-task"></i> <span class="d-none d-md-inline">Manage files</span></a>
                            </div>
                        </div>
                        <div class="colleges-detail-body">
                             @if($downloadList->isEmpty())
                                <p class="text-muted fst-italic">No downloadable resources added yet.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Title</th>
                                                <th>File</th>
                                                <th>Visibility</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($downloadList as $item)
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold">{{ $item->title }}</div>
                                                        @if(!empty($item->description))
                                                            <div class="text-muted small">{{ Str::limit(strip_tags($item->description), 100) }}</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div>{{ $item->file_name }}</div>
                                                        <div class="text-muted small">{{ number_format(($item->file_size ?? 0) / 1024, 1) }} KB</div>
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $item->is_visible ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ $item->is_visible ? 'Visible' : 'Hidden' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="{{ route('admin.colleges.downloads.edit', ['college' => $collegeSlug, 'download' => $item]) }}" class="btn btn-sm btn-link p-0 text-decoration-none small me-3">Edit item</a>
                                                        <a href="{{ route('college.downloads.file', ['college' => $collegeSlug, 'download' => $item]) }}" class="btn btn-sm btn-link p-0 text-decoration-none small" target="_blank" rel="noopener">Download</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif



                @if ($currentSection === 'contact')
                    <div class="colleges-detail">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                            <h2 class="colleges-detail-title mb-0">Contact Information</h2>
                            <a href="{{ route('admin.colleges.edit-section', ['college' => $collegeSlug, 'section' => 'contact']) }}" class="btn btn-outline-secondary btn-sm">Edit contact info</a>
                        </div>
                        <div class="colleges-detail-body">
                            @php $contact = $content['contact_data'] ?? null; @endphp
                            @if($contact)
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <h5 class="h6 text-muted mb-2">Email & Phone</h5>
                                        <p class="mb-1"><strong>Email:</strong> {{ $contact->email ?? 'Not set' }}</p>
                                        <p class="mb-0"><strong>Phone:</strong> {{ $contact->phone ?? 'Not set' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5 class="h6 text-muted mb-2">Address</h5>
                                        <p class="mb-0">{{ $contact->address ?? 'Not set' }}</p>
                                    </div>
                                    <div class="col-12">
                                        <h5 class="h6 text-muted mb-2">Social Media</h5>
                                        <div class="d-flex flex-wrap gap-3">
                                            @if($contact->facebook)
                                                <a href="{{ $contact->facebook }}" target="_blank" class="text-decoration-none">
                                                    <span class="badge bg-light text-dark border"><i class="me-1">f</i> Facebook</span>
                                                </a>
                                            @endif
                                            @if($contact->instagram)
                                                <a href="{{ $contact->instagram }}" target="_blank" class="text-decoration-none">
                                                    <span class="badge bg-light text-dark border">Instagram</span>
                                                </a>
                                            @endif
                                            @if(!empty($contact->custom_links))
                                                @foreach($contact->custom_links as $link)
                                                    @if(!empty($link))
                                                        <a href="{{ $link }}" target="_blank" class="text-decoration-none">
                                                            <span class="badge bg-light text-dark border">🌐 Custom Link</span>
                                                        </a>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if(empty($contact->facebook) && empty($contact->instagram) && empty($contact->custom_links))
                                                <span class="text-muted small">No social media links added.</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted">No contact information available.</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Retro Section Items --}}
            @if ($currentSection === 'overview')
                {{-- Retro Settings --}}
                <div class="colleges-detail mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="colleges-detail-title mb-0">Retro Settings</h2>
                    </div>
                    <div class="admin-card">
                        <div class="card-body p-4">
                            <form action="{{ route('admin.colleges.update-section', ['college' => $collegeSlug, 'section' => 'overview']) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_edit_mode" value="retro_settings">
                                
                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="show_primary_retro_btn" name="show_primary_retro_btn" {{ filter_var($content['show_primary_retro_btn'] ?? true, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="show_primary_retro_btn">
                                                Show "Explore Programs" Button
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="show_secondary_retro_btn" name="show_secondary_retro_btn" {{ filter_var($content['show_secondary_retro_btn'] ?? true, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="show_secondary_retro_btn">
                                                Show "{{ $collegeSlug === 'engineering' ? 'Meet the Faculty' : 'About Us' }}" Button
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-admin-primary btn-sm">Save Settings</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="colleges-detail mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="colleges-detail-title mb-0">Retro Section Items</h2>
                        @php
                            $retroCount = $retroList->count();
                        @endphp
                        @if ($retroCount < 4)
                            <a href="{{ route('admin.colleges.create-retro', ['college' => $collegeSlug]) }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add Item</span></a>
                        @else
                            <button class="btn btn-secondary btn-sm" disabled title="Maximum 4 items reached.">Max items reached</button>
                        @endif
                    </div>
                    
                    @if($retroList->count() > 0)
                        <div class="row g-3">
                        @foreach($retroList as $retro)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 shadow-sm border-admin-light">
                                    <div class="card-body">
                                        @if($retro->background_image)
                                            <div class="rounded mb-2 overflow-hidden" style="height: 150px;">
                                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($retro->background_image) }}" class="w-100 h-100 object-fit-cover" alt="Retro bg">
                                            </div>
                                        @endif
                                        <h5 class="card-title fw-bold text-dark">{{ $retro->title ?: 'No Title' }}</h5>
                                        <div class="mb-2">
                                            @if($retro->stamp)
                                                <span class="badge text-white" style="background-color: {{ $headerColor ?? '#f59e0b' }};">{{ $retro->stamp }}</span>
                                            @endif
                                        </div>
                                        <p class="card-text small text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($retro->description), 100) }}</p>
                                    </div>
                                    <div class="card-footer bg-transparent border-top-0 d-flex justify-content-end gap-2">
                                         <a href="{{ route('admin.colleges.edit-retro', ['college' => $collegeSlug, 'retro' => $retro->id]) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                                         <form action="{{ route('admin.colleges.update-section', ['college' => $collegeSlug, 'section' => 'overview']) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this retro item?');">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="_edit_mode" value="delete_retro">
                                            <input type="hidden" name="retro_id" value="{{ $retro->id }}">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Remove</button>
                                         </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    @else
                        <div class="colleges-detail-body">
                             <p class="text-muted mb-0">No retro items found.</p>
                             @if ($retroList->count() < 4)
                                 <p class="mt-2"><a href="{{ route('admin.colleges.create-retro', ['college' => $collegeSlug]) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Create your first item</span></a></p>
                             @endif
                        </div>
                    @endif
                </div>
            @endif

            {{-- Featured Video Section (separate colleges-detail div) --}}
            @if ($currentSection === 'overview')
                <div class="colleges-detail mt-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <h2 class="colleges-detail-title mb-0">Featured Video</h2>
                        <a href="{{ route('admin.colleges.edit-featured-video', ['college' => $collegeSlug]) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil-square"></i> <span class="d-none d-md-inline">Edit video</span></a>
                    </div>
                    <div class="colleges-detail-body">
                        @if ($videoData && $videoData->video_type && ($videoData->video_url || $videoData->video_file))
                            @if ($videoData->video_title)
                                <h3 class="fw-600 mb-2">{{ $videoData->video_title }}</h3>
                            @endif
                            @if ($videoData->video_description)
                                <div class="ql-editor p-0 text-muted mb-3" style="height: auto;">
                                    {!! html_entity_decode($videoData->video_description) !!}
                                </div>
                            @endif
                            
                            @if ($videoData->video_type === 'url' && $videoData->video_url)
                                {{-- URL Embed --}}
                                @php
                                    $url = $videoData->video_url;
                                    $embedUrl = null;
                                    
                                    // YouTube
                                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
                                        $embedUrl = 'https://www.youtube.com/embed/' . $matches[1];
                                    }
                                    // Vimeo
                                    elseif (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
                                        $embedUrl = 'https://player.vimeo.com/video/' . $matches[1];
                                    }
                                    // Direct URL (mp4, webm, etc.)
                                    elseif (preg_match('/\.(mp4|webm|ogg)$/i', $url)) {
                                        $embedUrl = 'direct';
                                    }
                                @endphp
                                
                                @if ($embedUrl === 'direct')
                                    <div class="ratio ratio-16x9 rounded overflow-hidden" style="max-width: 800px;">
                                        <video controls class="w-100 h-100">
                                            <source src="{{ $url }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                @elseif ($embedUrl)
                                    <div class="ratio ratio-16x9 rounded overflow-hidden" style="max-width: 800px;">
                                        <iframe src="{{ $embedUrl }}" allowfullscreen></iframe>
                                    </div>
                                @else
                                    <p class="text-muted">Unable to embed video. <a href="{{ $url }}" target="_blank">View video</a></p>
                                @endif
                            @elseif ($videoData->video_type === 'file' && $videoData->video_file)
                                {{-- Uploaded File --}}
                                <div class="ratio ratio-16x9 rounded overflow-hidden" style="max-width: 800px;">
                                    <video controls class="w-100 h-100">
                                        <source src="{{ $videoData->video_file }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            @endif
                        @else
                            <p class="text-muted mb-0">No featured video yet. <a href="{{ route('admin.colleges.edit-featured-video', ['college' => $collegeSlug]) }}">Add a video</a>.</p>
                        @endif
                    </div>
                </div>
            @endif


            @if ($currentSection === 'explore')
                    {{-- Explore items list as separate section --}}
                    <div class="colleges-detail mt-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <h2 class="colleges-detail-title mb-0">Explore Items</h2>
                            <button type="button" class="btn btn-admin-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#addExploreForm">Add explore item</button>
                        </div>
                        
                        <div class="collapse mb-4" id="addExploreForm">
                            <div class="admin-card">
                                <div class="card-body p-4">
                                    <h4 class="h6 fw-600 mb-3">Add new explore item</h4>
                                    <form method="POST" enctype="multipart/form-data" class="row g-3">
                                        @csrf
                                        <input type="hidden" name="college" value="{{ $collegeSlug }}">
                                        <input type="hidden" name="section" value="{{ $currentSection }}">
                                        <div class="col-md-6">
                                            <label for="explore_name" class="form-label">Item name</label>
                                            <input type="text" name="explore_name" id="explore_name" class="form-control @error('explore_name') is-invalid @enderror" value="{{ old('explore_name') }}" required>
                                            @error('explore_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="explore_image" class="form-label">Image</label>
                                            <input type="file" name="explore_image" id="explore_image" class="form-control @error('explore_image') is-invalid @enderror" accept="image/*">
                                            <small class="text-muted">Optional. Upload a PNG, JPG, or SVG file.</small>
                                            @error('explore_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label for="explore_description" class="form-label">Description</label>
                                            <textarea name="explore_description" id="explore_description" class="form-control quill-editor @error('explore_description') is-invalid @enderror" rows="3">{{ old('explore_description') }}</textarea>
                                            <small class="text-muted">Optional short description.</small>
                                            @error('explore_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-admin-primary">Add explore item</button>
                                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#addExploreForm">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="list-group">
                            <div class="list-group-item bg-light">
                                <div class="row align-items-center g-3">
                                    <div class="col-auto" style="width: 80px;">
                                        <strong class="text-muted small">Image</strong>
                                    </div>
                                    <div class="col">
                                        <strong class="text-muted small">Item name</strong>
                                    </div>
                                    <div class="col-auto" style="width: 150px;">
                                        <strong class="text-muted small">Actions</strong>
                                    </div>
                                </div>
                            </div>
                            @if (empty($exploreList) || count($exploreList) === 0)
                                <div class="list-group-item">
                                    <p class="text-muted small mb-0">No explore items added yet. <a href="#addExploreForm" data-bs-toggle="collapse">Add one</a>.</p>
                                </div>
                            @else
                                @foreach ($exploreList as $index => $item)
                                    <div class="list-group-item">
                                        <div class="row align-items-center g-3">
                                            <div class="col-auto" style="width: 80px;">
                                                @if (!empty($item['image']))
                                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="img-fluid rounded" style="max-width: 70px; max-height: 70px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; color: #ccc; font-size: 0.8rem;">No image</div>
                                                @endif
                                            </div>
                                            <div class="col">
                                                <div class="fw-600">{{ $item['name'] }}</div>
                                                @if (!empty($item['description']))
                                                    <small class="text-muted">{{ $item['description'] }}</small>
                                                @endif
                                            </div>
                                            <div class="col-auto" style="width: 150px;">
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editExplore{{ $index }}">Edit</button>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete this explore item?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="college" value="{{ $collegeSlug }}">
                                                        <input type="hidden" name="section" value="{{ $currentSection }}">
                                                        <input type="hidden" name="explore_index" value="{{ $index }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editExplore{{ $index }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit explore item</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="college" value="{{ $collegeSlug }}">
                                                        <input type="hidden" name="section" value="{{ $currentSection }}">
                                                        <input type="hidden" name="explore_index" value="{{ $index }}">
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Item name</label>
                                                                <input type="text" name="explore_name" class="form-control" value="{{ $item['name'] }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Image</label>
                                                                @if (!empty($item['image']))
                                                                    <div class="mb-2">
                                                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="img-fluid rounded" style="max-width: 150px;">
                                                                    </div>
                                                                @endif
                                                                <input type="file" name="explore_image" class="form-control" accept="image/*">
                                                                <small class="text-muted">Leave empty to keep current image.</small>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Description</label>
                                                                <textarea name="explore_description" class="form-control" rows="3">{{ $item['description'] ?? '' }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-admin-primary">Save changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
            @endif


            @if ($currentSection === 'facilities')
                <div class="colleges-detail mt-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h3 class="h5 mb-0 fw-600">Facility roster</h3>
                        <a href="{{ route('admin.facilities.create', ['college' => $collegeSlug]) }}" class="btn btn-admin-primary btn-sm"><i class="bi bi-building-plus"></i> <span class="d-none d-md-inline">Add facility</span></a>
                    </div>

                @if ($facilityList->isEmpty())
                    <p class="text-muted">No facilities in this college yet.</p>
                @else
                    <div class="row g-4">
                        @foreach ($facilityList as $facility)
                            <div class="col-6 col-sm-6 col-lg-4 col-xl-3">
                                <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                                    {{-- Facility Photo --}}
                                    <div class="position-relative" style="background: linear-gradient(135deg, #38b2ac 0%, #2d3748 100%); padding-top: 75%;">
                                        @if (!empty($facility->photo))
                                            <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($facility->photo, 'images') }}" alt="{{ $facility->name }}" class="position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;">
                                        @else
                                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                                                    <path d="M9 22v-4h6v4"></path>
                                                    <path d="M8 6h.01"></path><path d="M16 6h.01"></path>
                                                    <path d="M12 6h.01"></path>
                                                    <path d="M12 10h.01"></path>
                                                    <path d="M12 14h.01"></path>
                                                    <path d="M16 10h.01"></path>
                                                    <path d="M16 14h.01"></path>
                                                    <path d="M8 10h.01"></path>
                                                    <path d="M8 14h.01"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Facility Info --}}
                                    <div class="card-body p-3" style="background: #2d3748; color: white;">
                                        <h5 class="card-title mb-1 fw-600" style="font-size: 1rem;">{{ $facility->name }}</h5>
                                            <p class="card-text mb-2" style="font-size: 0.8rem; color: #a0aec0;">{{ Str::limit(strip_tags($facility->description), 100) }}</p>

                                        {{-- Action Buttons --}}
                                        <div class="d-flex gap-2 mt-3">
                                            <a href="{{ route('admin.facilities.edit', $facility) }}" class="btn btn-sm btn-outline-light" style="font-size: 0.75rem;" title="Edit"><i class="bi bi-pencil"></i></a>
                                            <form action="{{ route('admin.facilities.destroy', $facility) }}" method="POST"  class="" onsubmit="return confirm('Remove this facility?');">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="return_college" value="{{ $collegeSlug }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size: 0.75rem;" title="Delete"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                </div>
            @endif

            @if ($currentSection === 'institutes')
                <div class="colleges-detail mt-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h3 class="h5 mb-0 fw-600">{{ $sections['institutes'] ?? 'Institute' }} roster</h3>
                        <a href="{{ route('admin.institutes.create', ['college' => $collegeSlug]) }}" class="btn btn-admin-primary btn-sm">Add {{ strtolower($sections['institutes'] ?? 'institute') }}</a>
                    </div>

                @if ($instituteList->isEmpty())
                    <p class="text-muted">No {{ strtolower($sections['institutes'] ?? 'institutes') }} in this college yet.</p>
                @else
                    <div class="row g-4">
                        @foreach ($instituteList as $institute)
                            <div class="col-6 col-sm-6 col-lg-4 col-xl-3">
                                <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                                    {{-- Institute Photo --}}
                                    <div class="position-relative" style="background: linear-gradient(135deg, #38b2ac 0%, #2d3748 100%); padding-top: 75%;">
                                        @php
                                            $logoPath = null;
                                            if ($institute->logo) {
                                                $logoPath = \App\Providers\AppServiceProvider::resolveImageUrl($institute->logo);
                                            } elseif ($institute->photo) {
                                                $logoPath = \App\Providers\AppServiceProvider::resolveImageUrl($institute->photo, 'images');
                                            }
                                        @endphp
                                        @if ($logoPath)
                                            <img src="{{ $logoPath }}" alt="{{ $institute->name }}" class="position-absolute top-0 start-0 w-100 h-100" style="object-fit: contain; padding: 10px; background:white;">
                                        @else
                                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="12" cy="7" r="4"></circle>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Institute Info --}}
                                    <div class="card-body p-3" style="background: #2d3748; color: white;">
                                        <h5 class="card-title mb-1 fw-600" style="font-size: 1rem;">{{ $institute->name }}</h5>
                                            <p class="card-text mb-2" style="font-size: 0.8rem; color: #a0aec0;">{{ Str::limit(strip_tags($institute->description), 100) }}</p>

                                        {{-- Action Buttons --}}
                                        <div class="d-flex gap-2 mt-3">
                                            <a href="{{ route('admin.colleges.show-institute', ['college' => $collegeSlug, 'institute' => $institute->id]) }}" class="btn btn-sm btn-outline-success flex-fill" style="font-size: 0.75rem;">Open</a>
                                            <a href="{{ route('admin.institutes.edit', $institute) }}" class="btn btn-sm btn-outline-light" style="font-size: 0.75rem;" title="Edit"><i class="bi bi-pencil"></i></a>
                                            <form action="{{ route('admin.institutes.destroy', $institute) }}" method="POST"  class="" onsubmit="return confirm('Remove this institute?');">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="return_college" value="{{ $collegeSlug }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size: 0.75rem;" title="Delete"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                </div>
            @endif

            @if ($currentSection === 'faq')

                <div class="colleges-detail mt-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 class="colleges-detail-title mb-0">Frequently Asked Questions</h2>
                        <a href="{{ route('admin.faqs.create-college', ['college' => $collegeSlug]) }}" class="btn btn-admin-primary btn-sm">Add FAQ</a>
                    </div>

                    @if ($faqList->isEmpty())
                        <p class="text-muted">No FAQs for this college yet.</p>
                    @else
                        <div class="accordion" id="faqAccordion">
                            @foreach ($faqList as $faq)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $faq->id }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $faq->id }}">
                                            <div class="d-flex align-items-center gap-2 w-100 me-3">
                                                <span>{{ $faq->question }}</span>
                                                @if(!$faq->is_visible)
                                                    <span class="badge bg-secondary" style="font-size: 0.7rem;">Hidden</span>
                                                @else
                                                    <span class="badge bg-success" style="font-size: 0.7rem;">Visible</span>
                                                @endif
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            <div class="mb-3 faq-richtext">{!! $faq->answer !!}</div>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                                <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" onsubmit="return confirm('Delete this FAQ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="return_college" value="{{ $collegeSlug }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            @if ($currentSection === 'faculty')
                <div class="colleges-detail mt-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h3 class="h5 mb-0 fw-600">Faculty roster</h3>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.faculty.create-college', ['college' => $collegeSlug]) }}" class="btn btn-admin-primary btn-sm"><i class="bi bi-person-plus"></i> <span class="d-none d-md-inline">Add faculty member</span></a>
                            
                            {{-- Optional: Dropdown or list of institutes to add staff to --}}
                            @php
                                $institutes = \App\Models\CollegeInstitute::where('college_slug', $collegeSlug)->get();
                            @endphp
                            @if($institutes->isNotEmpty())
                                <div class="dropdown">
                                    <button class="btn btn-outline-admin-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-person-badge"></i> <span class="d-none d-md-inline">Add Staff</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li><a class="dropdown-item" href="{{ route('admin.institute-staff.create', ['return_college' => $collegeSlug]) }}">Not assigned</a></li>
                                        @if($institutes->isNotEmpty())
                                            <li><hr class="dropdown-divider"></li>
                                        @endif
                                        @foreach($institutes as $inst)
                                            <li><a class="dropdown-item" href="{{ route('admin.institute-staff.create', ['institute' => $inst->id, 'return_college' => $collegeSlug]) }}">{{ $inst->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                
                @if ($facultyList->isEmpty())
                    <p class="text-muted">No faculty members in this college yet.</p>
                @else
                    @php
                        $grouped = $facultyList->groupBy(fn($m) => $m->department ?: 'Unassigned');
                        $sortedGroups = $grouped->sortKeys();
                    @endphp
                    @foreach ($sortedGroups as $deptName => $members)
                        <div class="mb-4">
                            <h5 class="fw-700 mb-3 pb-2 border-bottom" style="font-size: 1rem; color: var(--admin-text);">
                                {{ $deptName }}
                                <span class="badge bg-secondary ms-2" style="font-size: 0.7rem; font-weight: 500;">{{ $members->count() }}</span>
                            </h5>
                            <div class="row g-4">
                                @foreach ($members as $member)
                                    <div class="col-6 col-sm-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                                            {{-- Faculty Photo --}}
                                            <div class="position-relative" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding-top: 100%;">
                                                @if (!empty($member->photo))
                                                    <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($member->photo, 'images') }}" alt="{{ $member->name }}" class="position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;">
                                                @else
                                                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                                            <circle cx="12" cy="7" r="4"></circle>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Faculty Info --}}
                                            <div class="card-body p-3" style="background: #2d3748; color: white;">
                                                <h5 class="card-title mb-1 fw-600" style="font-size: 1rem;">{{ $member->name }}</h5>
                                                <p class="card-text mb-1" style="font-size: 0.875rem; color: #cbd5e0;">{{ $member->position ?? 'Faculty Member' }}</p>
                                                @if (!empty($member->email))
                                                    <p class="card-text mb-2" style="font-size: 0.75rem; color: #a0aec0; word-break: break-word;">{{ $member->email }}</p>
                                                @endif

                                                {{-- Action Buttons --}}
                                                <div class="d-flex gap-2 mt-3">
                                                    @if(get_class($member) === \App\Models\InstituteStaff::class)
                                                        <a href="{{ route('admin.institute-staff.edit', ['institute_staff' => $member->id, 'return_college' => $collegeSlug]) }}" class="btn btn-sm btn-outline-light" style="font-size: 0.75rem;" title="Edit"><i class="bi bi-pencil"></i></a>
                                                        <form action="{{ route('admin.institute-staff.destroy', ['institute_staff' => $member->id, 'return_college' => $collegeSlug]) }}" method="POST"  class="" onsubmit="return confirm('Remove this staff member?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size: 0.75rem;" title="Delete"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    @else
                                                        <a href="{{ route('admin.faculty.edit', $member) }}" class="btn btn-sm btn-outline-light" style="font-size: 0.75rem;" title="Edit"><i class="bi bi-pencil"></i></a>
                                                        <form action="{{ route('admin.faculty.destroy', $member) }}" method="POST"  class="" onsubmit="return confirm('Remove this faculty member?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size: 0.75rem;" title="Delete"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
                </div>
            @endif



            @if ($currentSection === 'accreditation')
                <div class="colleges-detail mt-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 class="colleges-detail-title mb-0">Accreditation Status</h2>
                        <a href="{{ route('admin.accreditations.create', ['college' => $collegeSlug]) }}" class="btn btn-admin-primary btn-sm"><i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add Accreditation Record</span></a>
                    </div>

                    @if ($accreditationList->isEmpty())
                        <p class="text-muted">No accreditation records for this college yet.</p>
                    @else
                        <div class="table-responsive bg-white rounded shadow-sm">
                            <table class="table table-hover align-middle mb-0 table-accreditation">
                                <thead class="table-light">
                                    <tr>
                                        <th>Agency</th>
                                        <th>Level / Status</th>
                                        <th>Program Scope</th>
                                        <th>Valid Until</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($accreditationList as $acc)
                                        <tr>
                                            <td><span class="fw-600">{{ $acc->agency }}</span></td>
                                            <td><span class="badge bg-info text-dark">{{ $acc->level }}</span></td>
                                            <td>
                                                @if($acc->program)
                                                    <span class="text-primary small fw-600">{{ $acc->program->title }}</span>
                                                @else
                                                    <span class="text-muted small italic">Entire College</span>
                                                @endif
                                            </td>
                                            <td class="small">{{ $acc->valid_until ? $acc->valid_until->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                @if(!$acc->is_visible)
                                                    <span class="badge bg-secondary">Hidden</span>
                                                @else
                                                    <span class="badge bg-success">Visible</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="{{ route('admin.accreditations.edit', $acc) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                                    <form action="{{ route('admin.accreditations.destroy', $acc) }}" method="POST" onsubmit="return confirm('Delete this record?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="return_college" value="{{ $collegeSlug }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

            @if ($currentSection === 'membership')
                <div class="colleges-detail mt-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 class="colleges-detail-title mb-0">Professional Memberships</h2>
                        <a href="{{ route('admin.memberships.create', ['college' => $collegeSlug]) }}" class="btn btn-admin-primary btn-sm"><i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add Membership Record</span></a>
                    </div>

                    @if ($membershipList->isEmpty())
                        <p class="text-muted">No professional memberships for this college yet.</p>
                    @else
                        <div class="table-responsive bg-white rounded shadow-sm">
                            <table class="table table-hover align-middle mb-0 table-memberships">
                                <thead class="table-light">
                                    <tr>
                                        <th>Organization</th>
                                        <th>Type</th>
                                        <th>Scope</th>
                                        <th>Valid Until</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($membershipList as $membership)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($membership->logo)
                                                        @php
                                                            $logoUrl = \App\Providers\AppServiceProvider::resolveLogoUrl($membership->logo);
                                                        @endphp
                                                        <img src="{{ $logoUrl }}" alt="" style="height: 24px; width: 24px; object-fit: contain;">
                                                    @endif
                                                    <span class="fw-600">{{ $membership->organization }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $membership->membership_type }}</td>
                                            <td>
                                                @if($membership->department)
                                                    <span class="text-primary small fw-600">{{ $membership->department->name }}</span>
                                                @else
                                                    <span class="text-muted small italic">Entire College</span>
                                                @endif
                                            </td>
                                            <td class="small">{{ $membership->valid_until ? $membership->valid_until->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                @if(!$membership->is_visible)
                                                    <span class="badge bg-secondary">Hidden</span>
                                                @else
                                                    <span class="badge bg-success">Visible</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="{{ route('admin.memberships.edit', $membership) }}?return_college={{ $collegeSlug }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                                    <form action="{{ route('admin.memberships.destroy', $membership) }}" method="POST" onsubmit="return confirm('Delete this record?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="return_college" value="{{ $collegeSlug }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

            @if ($currentSection === 'organizations')
                <div class="colleges-detail mt-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 class="colleges-detail-title mb-0">Student Organizations</h2>
                        <a href="{{ route('admin.organizations.create', ['college' => $collegeSlug]) }}" class="btn btn-admin-primary btn-sm"><i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add Organization Record</span></a>
                    </div>

                    @if ($organizationList->isEmpty())
                        <p class="text-muted">No student organizations for this college yet.</p>
                    @else
                        <div class="table-responsive bg-white rounded shadow-sm">
                            <table class="table table-hover align-middle mb-0 table-organizations">
                                <thead class="table-light">
                                    <tr>
                                        <th>Organization Info</th>
                                        <th>Scope</th>
                                        <th>Adviser</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($organizationList as $org)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    @php
                                                        $logoUrl = \App\Providers\AppServiceProvider::resolveLogoUrl($org->logo)
                                                            ?: \App\Providers\AppServiceProvider::resolveLogoUrl('images/colleges/main.webp');
                                                    @endphp
                                                    <img src="{{ $logoUrl }}" alt="{{ $org->name }}" class="rounded shadow-sm bg-white" style="height: 40px; width: 40px; object-fit: contain; padding: 4px;">
                                                    <div>
                                                        <div class="fw-600">
                                                            {{ $org->name }}
                                                            @if($org->acronym)
                                                                <span class="text-muted small">({{ $org->acronym }})</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($org->department)
                                                    <span class="text-primary small fw-600">{{ $org->department->name }}</span>
                                                @else
                                                    <span class="text-muted small italic">Entire College</span>
                                                @endif
                                            </td>
                                            <td class="small">{{ $org->adviser ?? 'N/A' }}</td>
                                            <td>
                                                @if(!$org->is_visible)
                                                    <span class="badge bg-secondary">Hidden</span>
                                                @else
                                                    <span class="badge bg-success">Visible</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="{{ route('admin.organizations.show', ['college' => $collegeSlug, 'organization' => $org]) }}" class="btn btn-sm btn-outline-success" title="View &amp; edit sections">Open</a>
                                                    <a href="{{ route('admin.organizations.edit-scoped', ['college' => $collegeSlug, 'organization' => $org]) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                                    <form action="{{ route('admin.organizations.destroy', $org) }}" method="POST" onsubmit="return confirm('Delete this organization?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="return_college" value="{{ $collegeSlug }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
@push('styles')
<style>
    .faq-richtext {
        overflow-wrap: anywhere;
        word-break: break-word;
    }
    .faq-richtext p:last-child {
        margin-bottom: 0 !important;
    }
    .faq-richtext img,
    .faq-richtext iframe,
    .faq-richtext video {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }
    .faq-richtext iframe {
        width: 100%;
        aspect-ratio: 16 / 9;
    }
    .faq-richtext table {
        display: block;
        width: 100%;
        overflow-x: auto;
    }
    .faq-richtext ul,
    .faq-richtext ol {
        padding-left: 1.5rem;
    }
    .faq-richtext pre {
        white-space: pre-wrap;
        word-break: break-word;
    }

    /* Responsive Adjustments for Mobile View */
    @media (max-width: 991.98px) {
        .colleges-layout {
            flex-direction: column !important;
            gap: 1.5rem !important;
        }
        .colleges-section-list {
            width: 100% !important;
            margin-right: 0 !important;
            margin-bottom: 1.5rem !important;
        }
        .colleges-header-bar {
            flex-direction: column !important;
            align-items: flex-start !important;
            padding: 1rem !important;
            margin: -1rem -1.25rem 1.5rem -1.25rem !important;
        }
        .colleges-header-actions {
            margin-left: 0 !important;
            width: 100% !important;
            flex-wrap: wrap !important;
            gap: 0.5rem !important;
            margin-top: 0.5rem !important;
        }
        .colleges-breadcrumb {
            width: 100% !important;
            padding: 0.5rem 0 !important;
            display: block !important;
        }
    }


    /* ── Responsive Tables to Cards for Mobile ── */
    @media (max-width: 991.98px) {
        .colleges-detail {
            padding: 1.25rem !important; /* Slightly reduce padding on mobile detail container */
        }
        .table-responsive {
            border: none !important;
            overflow-x: visible !important;
            background: none !important;
            box-shadow: none !important;
        }
        .table {
            display: block !important;
            width: 100% !important;
            border: none !important;
        }
        .table thead {
            display: none !important; /* Hide header rows */
        }
        .table tbody {
            display: block !important;
            width: 100% !important;
        }
        .table tbody tr {
            display: flex !important;
            flex-direction: column !important;
            background: var(--admin-surface) !important;
            border: 1px solid var(--admin-border) !important;
            border-radius: 12px !important;
            margin-bottom: 1rem !important;
            padding: 1.25rem !important;
            box-shadow: var(--admin-shadow) !important;
            width: 100% !important;
        }
        .table tbody td {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 100% !important;
            padding: 0.625rem 0 !important;
            border: none !important;
            border-bottom: 1px solid rgba(0,0,0,0.03) !important;
            text-align: right !important;
        }
        .table tbody td:last-child {
            border-bottom: none !important;
            padding-top: 1rem !important;
            justify-content: flex-start !important;
        }
        .table tbody td::before {
            font-weight: 600 !important;
            color: var(--admin-text-muted) !important;
            font-size: 0.8125rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            text-align: left !important;
            margin-right: 1rem !important;
        }

        /* Title Card Styling for first cell */
        .table tbody td:nth-of-type(1) {
            justify-content: flex-start !important;
            font-size: 1.1rem !important;
            font-weight: 700 !important;
            border-bottom: 1px solid var(--admin-border) !important;
            padding-bottom: 0.75rem !important;
            margin-bottom: 0.25rem !important;
        }
        
        .table-accreditation td:nth-of-type(1)::before { content: ''; }
        .table-accreditation td:nth-of-type(2)::before { content: 'Level'; }
        .table-accreditation td:nth-of-type(3)::before { content: 'Scope'; }
        .table-accreditation td:nth-of-type(4)::before { content: 'Valid Until'; }
        .table-accreditation td:nth-of-type(5)::before { content: 'Status'; }
        .table-accreditation td:nth-of-type(6)::before { content: ''; }

        .table-memberships td:nth-of-type(1)::before { content: ''; }
        .table-memberships td:nth-of-type(2)::before { content: 'Type'; }
        .table-memberships td:nth-of-type(3)::before { content: 'Scope'; }
        .table-memberships td:nth-of-type(4)::before { content: 'Valid Until'; }
        .table-memberships td:nth-of-type(5)::before { content: 'Status'; }
        .table-memberships td:nth-of-type(6)::before { content: ''; }

        .table-organizations td:nth-of-type(1)::before { content: ''; }
        .table-organizations td:nth-of-type(2)::before { content: 'Scope'; }
        .table-organizations td:nth-of-type(3)::before { content: 'Adviser'; }
        .table-organizations td:nth-of-type(4)::before { content: 'Status'; }
        .table-organizations td:nth-of-type(5)::before { content: ''; }

        .table tbody td .d-flex.justify-content-end,
        .table tbody td .d-flex.justify-content-start {
            justify-content: flex-start !important;
            width: 100% !important;
            flex-wrap: wrap !important;
        }
    }

</style>
@endpush
@endsection
