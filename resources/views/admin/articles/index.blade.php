@extends('admin.layout')

@section('title', 'Articles')

@push('styles')
<style>
    /* Article Live Preview */
    .article-preview-toolbar {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: var(--admin-bg, #f7f8fa);
        border: 1px solid var(--admin-border);
        border-radius: var(--admin-radius-lg) var(--admin-radius-lg) 0 0;
        flex-wrap: wrap;
    }
    .article-preview-toolbar .viewport-btn {
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
    .article-preview-toolbar .viewport-btn:hover,
    .article-preview-toolbar .viewport-btn.active {
        background: var(--admin-accent-soft);
        color: var(--admin-accent);
        border-color: var(--admin-accent);
    }
    .article-preview-toolbar .toolbar-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-left: auto;
    }
    .article-preview-frame-wrapper {
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
    .article-preview-frame-wrapper iframe {
        border: none;
        background: #fff;
        transition: width 0.3s ease, transform 0.3s ease;
        transform-origin: top center;
    }
    .article-preview-loading {
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
    .article-preview-loading .spinner-border {
        width: 1.25rem;
        height: 1.25rem;
        border-width: 2px;
    }
    .article-preview-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 300px;
        color: var(--admin-text-muted);
        font-size: 0.9375rem;
        text-align: center;
        border: 1px solid var(--admin-border);
        border-radius: var(--admin-radius-lg);
        background: var(--admin-surface);
    }
    /* Fullscreen mode */
    .article-preview-fullscreen {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: #fff;
        display: flex;
        flex-direction: column;
    }
    .article-preview-fullscreen .article-preview-toolbar {
        border-radius: 0;
        border-left: none;
        border-right: none;
        border-top: none;
    }
    .article-preview-fullscreen .article-preview-frame-wrapper {
        flex: 1;
        height: auto !important;
        border-radius: 0;
        border: none;
    }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <h1 class="admin-page-title mb-0">Articles</h1>
        <a href="{{ route('admin.articles.create') }}" class="btn btn-admin-primary">New Article</a>
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search articles..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">All types</option>
                        <option value="news" {{ request('type') === 'news' ? 'selected' : '' }}>News</option>
                        <option value="announcement" {{ request('type') === 'announcement' ? 'selected' : '' }}>Announcement</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-secondary w-100">Filter</button>
                </div>
            </form>

            @if ($articles->isEmpty())
                <div class="py-5 text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px; background: var(--admin-accent-soft);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--admin-accent)" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </div>
                    <p class="text-muted mb-2">No articles found.</p>
                    <a href="{{ route('admin.articles.create') }}" class="btn btn-admin-primary btn-sm">Create one</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 admin-table">
                        <thead>
                            <tr class="text-muted small text-uppercase" style="letter-spacing: 0.05em;">
                                <th class="fw-600 border-0 py-3 px-4">Title</th>
                                <th class="fw-600 border-0 py-3 px-4" style="width: 110px;">Type</th>
                                @if (auth()->user()?->isSuperAdmin())
                                    <th class="fw-600 border-0 py-3 px-4" style="width: 140px;">Department</th>
                                @endif
                                <th class="fw-600 border-0 py-3 px-4" style="width: 140px;">Author</th>
                                <th class="fw-600 border-0 py-3 px-4" style="width: 120px;">Date</th>
                                <th class="fw-600 border-0 py-3 px-4 text-end" style="width: 180px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($articles as $article)
                                @php
                                    $previewCollege = $article->college_slug
                                        ?: (auth()->user()?->college_slug ?: null);
                                    if (!$previewCollege) {
                                        $allColleges = \App\Http\Controllers\Admin\CollegeController::getColleges();
                                        $previewCollege = array_key_first($allColleges);
                                    }
                                    $previewUrl = $previewCollege && $article->slug
                                        ? route('news.announcement.detail', ['college' => $previewCollege, 'slug' => $article->slug])
                                        : null;
                                @endphp
                                <tr>
                                    <td class="py-3 px-4 fw-500">{{ Str::limit($article->title, 45) }}</td>
                                    <td class="py-3 px-4"><span class="badge rounded-pill px-2 py-1" style="background: var(--admin-accent-soft); color: var(--admin-accent); font-weight: 500;">{{ $article->type }}</span></td>
                                    @if (auth()->user()?->isSuperAdmin())
                                        <td class="py-3 px-4 text-muted small">
                                            {{ $article->college_slug ? (\App\Http\Controllers\Admin\CollegeController::getColleges()[$article->college_slug] ?? $article->college_slug) : '—' }}
                                        </td>
                                    @endif
                                    <td class="py-3 px-4 text-muted small">{{ $article->author ?? '—' }}</td>
                                    <td class="py-3 px-4 text-muted small">{{ $article->date_formatted }}</td>
                                    <td class="py-3 px-4 text-end">
                                        @if ($previewUrl)
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 article-preview-btn" data-preview-url="{{ $previewUrl }}" data-preview-title="{{ Str::limit($article->title, 50) }}">Preview</button>
                                        @endif
                                        @if(auth()->user()?->canManageArticle($article))
                                            <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Edit</a>
                                            <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this article?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center py-3 border-top">
                    {{ $articles->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Live Preview Panel --}}
    <div class="mt-4" id="articlePreviewContainer" style="display: none;">
        <div class="article-preview-toolbar">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--admin-text-muted);"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            <span class="fw-600" style="font-size: 0.875rem; color: var(--admin-text);">Live Preview</span>
            <span id="articlePreviewTitle" class="text-muted small" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"></span>
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
                <button type="button" class="btn btn-outline-secondary btn-sm" id="articleRefreshBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 3px;"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                    Refresh
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="articleFullscreenBtn" title="Toggle fullscreen">
                    <svg id="articleFsIcon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 3px;"><polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline><line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line></svg>
                    <span id="articleFsLabel">Fullscreen</span>
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="articleCloseBtn" title="Close preview">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
        </div>
        <div class="article-preview-frame-wrapper" id="articlePreviewWrapper" style="height: 700px;">
            <div class="article-preview-loading" id="articlePreviewLoading" style="display: none;">
                <div class="spinner-border text-secondary" role="status"></div>
                Loading preview…
            </div>
            <iframe id="articlePreviewFrame" src="about:blank" style="width: 100%; height: 100%;"></iframe>
        </div>
    </div>

    <script>
    (function() {
        var container = document.getElementById('articlePreviewContainer');
        var wrapper = document.getElementById('articlePreviewWrapper');
        var frame = document.getElementById('articlePreviewFrame');
        var loading = document.getElementById('articlePreviewLoading');
        var titleEl = document.getElementById('articlePreviewTitle');
        var btns = container.querySelectorAll('.viewport-btn');
        var fsBtn = document.getElementById('articleFullscreenBtn');
        var fsIcon = document.getElementById('articleFsIcon');
        var fsLabel = document.getElementById('articleFsLabel');
        var closeBtn = document.getElementById('articleCloseBtn');
        var refreshBtn = document.getElementById('articleRefreshBtn');
        var isFullscreen = false;
        var currentWidth = '100%';

        function applyViewport(targetWidth) {
            currentWidth = targetWidth;
            if (targetWidth === '100%') {
                frame.style.width = '100%';
                frame.style.height = '100%';
                frame.style.transform = 'none';
            } else {
                var pxWidth = parseInt(targetWidth);
                var containerWidth = wrapper.clientWidth;
                if (pxWidth > containerWidth) {
                    var scale = containerWidth / pxWidth;
                    frame.style.width = pxWidth + 'px';
                    frame.style.height = (wrapper.clientHeight / scale) + 'px';
                    frame.style.transform = 'scale(' + scale + ')';
                } else {
                    frame.style.width = pxWidth + 'px';
                    frame.style.height = '100%';
                    frame.style.transform = 'none';
                }
            }
        }

        // Preview buttons on each article row
        document.querySelectorAll('.article-preview-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var url = this.getAttribute('data-preview-url');
                var title = this.getAttribute('data-preview-title');
                loading.style.display = 'flex';
                container.style.display = 'block';
                titleEl.textContent = '— ' + title;
                frame.onload = function() { loading.style.display = 'none'; };
                frame.src = url;
                // Reset viewport
                btns.forEach(function(b) { b.classList.remove('active'); });
                btns[0].classList.add('active');
                currentWidth = '100%';
                applyViewport('100%');
                // Scroll to preview
                container.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        // Viewport toggles
        btns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                btns.forEach(function(b) { b.classList.remove('active'); });
                btn.classList.add('active');
                applyViewport(btn.getAttribute('data-width'));
            });
        });

        // Refresh
        refreshBtn.addEventListener('click', function() {
            loading.style.display = 'flex';
            frame.onload = function() { loading.style.display = 'none'; };
            frame.src = frame.src;
        });

        // Close
        closeBtn.addEventListener('click', function() {
            if (isFullscreen) fsBtn.click();
            container.style.display = 'none';
            frame.src = 'about:blank';
        });

        // Fullscreen toggle
        fsBtn.addEventListener('click', function() {
            isFullscreen = !isFullscreen;
            if (isFullscreen) {
                container.classList.add('article-preview-fullscreen');
                document.body.style.overflow = 'hidden';
                fsLabel.textContent = 'Exit';
                fsIcon.innerHTML = '<polyline points="4 14 10 14 10 20"></polyline><polyline points="20 10 14 10 14 4"></polyline><line x1="14" y1="10" x2="21" y2="3"></line><line x1="3" y1="21" x2="10" y2="14"></line>';
            } else {
                container.classList.remove('article-preview-fullscreen');
                document.body.style.overflow = '';
                fsLabel.textContent = 'Fullscreen';
                fsIcon.innerHTML = '<polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline><line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line>';
            }
            setTimeout(function() { applyViewport(currentWidth); }, 100);
        });

        // Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isFullscreen) fsBtn.click();
        });

        // Resize
        window.addEventListener('resize', function() {
            if (container.style.display !== 'none') applyViewport(currentWidth);
        });
    })();
    </script>
@endsection
