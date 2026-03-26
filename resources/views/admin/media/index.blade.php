@extends('admin.layout')

@section('title', 'Media Library')

@push('styles')
<style>
    /* ── Media grid ─────────────────────────────────────────── */
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
    }
    .media-item {
        background: var(--admin-surface);
        border: 1px solid var(--admin-border);
        border-radius: 12px;
        overflow: hidden;
        transition: box-shadow 0.2s ease, transform 0.15s ease;
        cursor: pointer;
        position: relative;
    }
    .media-item:hover {
        box-shadow: var(--admin-shadow-hover);
        transform: translateY(-2px);
    }
    .media-thumb {
        width: 100%;
        height: 130px;
        object-fit: cover;
        background: #f1f5f9;
        display: block;
    }
    .media-file-icon {
        width: 100%;
        height: 130px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: var(--admin-text-muted);
    }
    .media-info { padding: 0.625rem 0.75rem; }
    .media-name {
        font-size: 0.75rem;
        font-weight: 500;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: var(--admin-text);
    }
    .media-meta { font-size: 0.6875rem; color: var(--admin-text-muted); margin-top: 0.2rem; }

    /* ── Action buttons ──────────────────────────────────────── */
    .media-actions { position: absolute; top: 6px; right: 6px; display: none; gap: 4px; }
    .media-item:hover .media-actions { display: flex; }
    .media-action-btn {
        width: 28px; height: 28px; padding: 0;
        border-radius: 6px; border: none;
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 0;
        transition: background 0.15s ease;
    }
    .media-action-copy  { background: rgba(255,255,255,0.92); color: var(--admin-accent); }
    .media-action-copy:hover  { background: #fff; }
    .media-action-delete { background: rgba(255,255,255,0.92); color: #ef4444; }
    .media-action-delete:hover { background: #fff; }

    /* ── Sidebar folder links ────────────────────────────────── */
    .media-folder-card {
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--admin-text);
        border: 1px solid transparent;
        transition: all 0.15s ease;
        text-decoration: none;
    }
    .media-folder-card:hover  { background: var(--admin-accent-soft); color: var(--admin-text); }
    .media-folder-card.active { background: var(--admin-accent-soft); border-color: var(--admin-accent); color: var(--admin-accent); }

    /* ── Upload zone ─────────────────────────────────────────── */
    .media-upload-zone {
        border: 2px dashed var(--admin-border);
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        transition: border-color 0.2s ease, background 0.2s ease;
        cursor: pointer;
    }
    .media-upload-zone:hover,
    .media-upload-zone.dragover {
        border-color: var(--admin-accent);
        background: var(--admin-accent-soft);
    }

    /* ── Toast ───────────────────────────────────────────────── */
    .toast-copied {
        position: fixed;
        bottom: 1.5rem; left: 50%;
        transform: translateX(-50%) translateY(60px);
        background: #0f172a; color: #fff;
        padding: 0.5rem 1.25rem;
        border-radius: 10px;
        font-size: 0.8125rem;
        z-index: 99999; opacity: 0;
        transition: all 0.3s ease;
        pointer-events: none;
    }
    .toast-copied.show { opacity: 1; transform: translateX(-50%) translateY(0); }

    /* ── Folder card grid ────────────────────────────────────── */
    .folder-card-big {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        padding: 1.25rem 1rem;
        border-radius: 12px;
        border: 1px solid var(--admin-border);
        background: var(--admin-surface);
        transition: box-shadow 0.2s ease, transform 0.15s ease, border-color 0.15s ease;
        text-align: center;
    }
    .folder-card-big:hover {
        box-shadow: var(--admin-shadow-hover);
        transform: translateY(-2px);
        border-color: var(--admin-accent);
    }
    .folder-card-icon  { color: #fbbc04; }
    .folder-card-icon.local { color: #4285f4; }
    .folder-card-name  { font-size: 0.8125rem; font-weight: 600; color: var(--admin-text); word-break: break-word; }
    .folder-card-meta  { font-size: 0.6875rem; color: var(--admin-text-muted); }

    /* ── Source tabs ─────────────────────────────────────────── */
    .source-tabs {
        display: flex;
        gap: 0;
        border-bottom: 2px solid var(--admin-border);
        margin-bottom: 1.25rem;
    }
    .source-tab-btn {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.55rem 1.1rem;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--admin-text-muted);
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        cursor: pointer;
        transition: color 0.15s, border-color 0.15s;
        text-decoration: none;
    }
    .source-tab-btn:hover { color: var(--admin-text); }
    .source-tab-btn.active {
        color: var(--admin-accent);
        border-bottom-color: var(--admin-accent);
    }
    .tab-badge {
        font-size: 0.65rem;
        font-weight: 600;
        background: var(--admin-accent-soft);
        color: var(--admin-accent);
        border-radius: 10px;
        padding: 1px 7px;
    }
    .source-tab-btn.active .tab-badge { background: var(--admin-accent); color: #fff; }

    /* ── Sidebar section label ───────────────────────────────── */
    .sidebar-section-label {
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--admin-text-muted);
        padding: 0.5rem 0.75rem 0.2rem;
    }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 class="admin-page-title mb-0">Media Library</h1>
            <p class="text-muted small mb-0 mt-1">
                {{ $totalFiles }} files total &mdash;
                <span>{{ $localTotal }} local</span> &middot;
                <span>{{ $gdriveTotal }} on Google Drive</span>
            </p>
        </div>
    </div>

    <div class="row g-4">
        {{-- ── Sidebar ─────────────────────────────────────────────────────── --}}
        <div class="col-lg-3">
            <div class="admin-card">
                <div class="admin-card-header">Folders</div>
                <div class="card-body p-2">

                    {{-- All Files (resets to grid) --}}
                    <a href="{{ route('admin.media.index') }}" class="media-folder-card {{ !$currentFolder && !$search ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        All Folders
                    </a>

                    {{-- ── LOCAL ── --}}
                    <div class="sidebar-section-label mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        Local Server
                    </div>
                    @foreach ($localFolders as $dir)
                        <a href="{{ route('admin.media.index', ['folder' => $dir, 'source' => 'local']) }}"
                           class="media-folder-card {{ ($currentFolder === $dir && $source === 'local') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                            <div>
                                <div>{{ basename($dir) }}</div>
                                <div class="text-muted" style="font-size:0.6rem;font-weight:400;">{{ $localFolderStats[$dir]['count'] ?? 0 }} files</div>
                            </div>
                        </a>
                    @endforeach

                    {{-- ── GOOGLE DRIVE ── --}}
                    <div class="sidebar-section-label mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
                        Google Drive
                    </div>
                    @foreach ($gdriveFolders as $dir)
                        <a href="{{ route('admin.media.index', ['folder' => $dir, 'source' => 'gdrive']) }}"
                           class="media-folder-card {{ ($currentFolder === $dir && $source === 'gdrive') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                            <div>
                                <div>{{ basename($dir) }}</div>
                                <div class="text-muted" style="font-size:0.6rem;font-weight:400;">{{ $gdriveFolderStats[$dir]['count'] ?? 0 }} files</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Upload --}}
            <div class="admin-card mt-3">
                <div class="admin-card-header">Upload</div>
                <div class="card-body p-3">
                    <form method="POST" action="{{ route('admin.media.upload') }}" enctype="multipart/form-data" id="uploadForm">
                        @csrf
                        <input type="hidden" name="source" value="{{ $source }}">

                        <div class="mb-2">
                            <label class="form-label small fw-semibold mb-1">Destination</label>
                            <select name="folder" class="form-select form-select-sm">
                                @if ($source === 'local')
                                    <optgroup label="Local Server">
                                        @foreach ($localFolders as $dir)
                                            <option value="{{ $dir }}" {{ ($currentFolder ?: 'images/uploads') === $dir ? 'selected' : '' }}>{{ basename($dir) }}</option>
                                        @endforeach
                                    </optgroup>
                                @else
                                    <optgroup label="Google Drive">
                                        @foreach ($gdriveFolders as $dir)
                                            <option value="{{ $dir }}" {{ ($currentFolder ?: 'images/uploads') === $dir ? 'selected' : '' }}>{{ basename($dir) }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                        </div>

                        <div class="media-upload-zone" id="uploadZone" onclick="document.getElementById('fileInput').click();">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--admin-text-muted)" stroke-width="2" class="mb-2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <div class="text-muted small">Drop files here or click to browse</div>
                            <div class="text-muted small mt-1" id="selectedCount"></div>
                        </div>
                        <input type="file" name="files[]" multiple id="fileInput" class="d-none" accept="image/*,video/*,.pdf">
                        <button type="submit" class="btn btn-admin-primary btn-sm w-100 mt-2" id="uploadBtn" disabled>Upload</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Main content area ──────────────────────────────────────────── --}}
        <div class="col-lg-9">

            @if ($showFolderGrid)
                {{-- ── Folder grid (no folder selected) ──────────────────── --}}

                {{-- Source tabs --}}
                <div class="source-tabs">
                    <a href="{{ route('admin.media.index', ['source' => 'local']) }}"
                       class="source-tab-btn {{ $source === 'local' ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        Local Server
                        <span class="tab-badge">{{ $localTotal }}</span>
                    </a>
                    <a href="{{ route('admin.media.index', ['source' => 'gdrive']) }}"
                       class="source-tab-btn {{ $source === 'gdrive' ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
                        Google Drive
                        <span class="tab-badge">{{ $gdriveTotal }}</span>
                    </a>
                </div>

                @if ($source === 'local')
                    <div class="text-muted small mb-3">{{ count($localFolders) }} folders &middot; {{ $localTotal }} files total</div>
                    <div class="row g-3">
                        @forelse ($localFolders as $dir)
                            @php $count = $localFolderStats[$dir]['count'] ?? 0; @endphp
                            <div class="col-6 col-md-4 col-xl-3">
                                <a href="{{ route('admin.media.index', ['folder' => $dir, 'source' => 'local']) }}" class="folder-card-big text-decoration-none">
                                    <div class="folder-card-icon local">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M20 6H12l-2-2H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2z"/></svg>
                                    </div>
                                    <div class="folder-card-name">{{ basename($dir) }}</div>
                                    <div class="folder-card-meta">{{ $count }} {{ $count === 1 ? 'file' : 'files' }}</div>
                                </a>
                            </div>
                        @empty
                            <div class="col-12"><p class="text-muted small">No local folders found.</p></div>
                        @endforelse
                    </div>
                @else
                    <div class="text-muted small mb-3">{{ count($gdriveFolders) }} folders &middot; {{ $gdriveTotal }} files total</div>
                    <div class="row g-3">
                        @forelse ($gdriveFolders as $dir)
                            @php $count = $gdriveFolderStats[$dir]['count'] ?? 0; @endphp
                            <div class="col-6 col-md-4 col-xl-3">
                                <a href="{{ route('admin.media.index', ['folder' => $dir, 'source' => 'gdrive']) }}" class="folder-card-big text-decoration-none">
                                    <div class="folder-card-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M20 6H12l-2-2H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2z"/></svg>
                                    </div>
                                    <div class="folder-card-name">{{ basename($dir) }}</div>
                                    <div class="folder-card-meta">{{ $count }} {{ $count === 1 ? 'file' : 'files' }}</div>
                                </a>
                            </div>
                        @empty
                            <div class="col-12"><p class="text-muted small">No Google Drive folders found.</p></div>
                        @endforelse
                    </div>
                @endif

            @else
                {{-- ── File listing (inside a folder or search results) ──── --}}

                {{-- Source tabs --}}
                <div class="source-tabs">
                    <a href="{{ route('admin.media.index', array_merge(request()->only(['folder', 'search']), ['source' => 'local'])) }}"
                       class="source-tab-btn {{ $source === 'local' ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        Local Server
                        <span class="tab-badge">{{ $localTotal }}</span>
                    </a>
                    <a href="{{ route('admin.media.index', array_merge(request()->only(['folder', 'search']), ['source' => 'gdrive'])) }}"
                       class="source-tab-btn {{ $source === 'gdrive' ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
                        Google Drive
                        <span class="tab-badge">{{ $gdriveTotal }}</span>
                    </a>
                </div>

                {{-- Breadcrumb + Search --}}
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.media.index', ['source' => $source]) }}" class="btn btn-sm btn-outline-secondary px-2 py-1" style="font-size:.8rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                            All Folders
                        </a>
                        @if ($currentFolder)
                            <span class="text-muted" style="font-size:.8rem;">/</span>
                            <span class="fw-semibold" style="font-size:.85rem;">{{ basename($currentFolder) }}</span>
                        @endif
                        @if ($search)
                            <span class="badge bg-secondary">Search: {{ $search }}</span>
                        @endif
                    </div>
                    <form method="GET" class="d-flex gap-2">
                        @if ($currentFolder)
                            <input type="hidden" name="folder" value="{{ $currentFolder }}">
                        @endif
                        <input type="hidden" name="source" value="{{ $source }}">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search files…" value="{{ $search }}" style="max-width: 220px;">
                        <button type="submit" class="btn btn-outline-secondary btn-sm">Search</button>
                        @if ($search)
                            <a href="{{ route('admin.media.index', ['folder' => $currentFolder, 'source' => $source]) }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                        @endif
                    </form>
                </div>

                @if ($files->isEmpty())
                    <div class="admin-card">
                        <div class="card-body p-5 text-center">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;background:var(--admin-accent-soft);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--admin-accent)" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </div>
                            <p class="text-muted mb-0">{{ $search ? 'No files match your search.' : 'No media files in this folder.' }}</p>
                        </div>
                    </div>
                @else
                    <div class="media-grid">
                        @foreach ($files as $file)
                            <div class="media-item">
                                <div class="media-actions">
                                    <button type="button" class="media-action-btn media-action-copy" title="Copy URL" onclick="copyUrl('{{ $file['url'] }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.media.destroy') }}" onsubmit="return confirm('Delete this file?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="path" value="{{ $file['path'] }}">
                                        <input type="hidden" name="source" value="{{ $file['source'] }}">
                                        <button type="submit" class="media-action-btn media-action-delete" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </form>
                                </div>

                                @if ($file['is_image'])
                                    <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}" class="media-thumb" loading="lazy">
                                @else
                                    <div class="media-file-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </div>
                                @endif
                                <div class="media-info">
                                    <div class="media-name" title="{{ $file['name'] }}">{{ $file['name'] }}</div>
                                    <div class="media-meta">{{ $file['size_human'] }} · {{ $file['modified_human'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $files->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    <div class="toast-copied" id="toastCopied">URL copied to clipboard!</div>

    <script>
    // File input
    var fileInput    = document.getElementById('fileInput');
    var uploadBtn    = document.getElementById('uploadBtn');
    var selectedCount = document.getElementById('selectedCount');
    fileInput.addEventListener('change', function() {
        var count = fileInput.files.length;
        uploadBtn.disabled = count === 0;
        selectedCount.textContent = count > 0 ? count + ' file(s) selected' : '';
    });

    // Drag & drop
    var uploadZone = document.getElementById('uploadZone');
    ['dragenter', 'dragover'].forEach(function(e) {
        uploadZone.addEventListener(e, function(ev) { ev.preventDefault(); uploadZone.classList.add('dragover'); });
    });
    ['dragleave', 'drop'].forEach(function(e) {
        uploadZone.addEventListener(e, function(ev) { ev.preventDefault(); uploadZone.classList.remove('dragover'); });
    });
    uploadZone.addEventListener('drop', function(ev) {
        fileInput.files = ev.dataTransfer.files;
        fileInput.dispatchEvent(new Event('change'));
    });

    // Copy URL
    function copyUrl(url) {
        navigator.clipboard.writeText(url).then(function() {
            var toast = document.getElementById('toastCopied');
            toast.classList.add('show');
            setTimeout(function() { toast.classList.remove('show'); }, 2000);
        });
    }
    </script>
@endsection
