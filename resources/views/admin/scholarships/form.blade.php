@extends('admin.layout')

@php
    $isGlobal = ($college === '_global');
    $routePrefix = $isGlobal ? 'admin.scholarships' : 'admin.colleges.scholarships';
    $routeParams = $isGlobal ? [] : ['college' => $college];
@endphp

@section('title', ($scholarship ? 'Edit' : 'New') . ' Scholarship - ' . ($isGlobal ? 'Global' : $collegeName))

@push('styles')
<style>
    /* ── Media Picker Modal ── */
    .mp-backdrop {
        position: fixed; inset: 0; z-index: 9998;
        background: rgba(0,0,0,0.5); backdrop-filter: blur(3px);
        display: none; opacity: 0; transition: opacity 0.2s ease;
    }
    .mp-backdrop.show { display: block; opacity: 1; }
    .mp-modal {
        position: fixed; z-index: 9999;
        top: 50%; left: 50%; transform: translate(-50%, -50%);
        width: min(960px, 95vw); max-height: 85vh;
        background: var(--admin-surface, #fff);
        border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        display: none; flex-direction: column; overflow: hidden;
    }
    .mp-modal.show { display: flex; }
    .mp-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 1rem 1.25rem; border-bottom: 1px solid var(--admin-border, #e5e7eb);
    }
    .mp-header h5 { margin: 0; font-size: 1rem; font-weight: 600; }
    .mp-close {
        width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--admin-border);
        background: none; cursor: pointer; display: grid; place-items: center;
        color: var(--admin-text-muted); transition: all 0.15s;
    }
    .mp-close:hover { background: rgba(239,68,68,0.08); color: #ef4444; }
    .mp-toolbar {
        display: flex; align-items: center; gap: 0.5rem;
        padding: 0.75rem 1.25rem; border-bottom: 1px solid var(--admin-border, #e5e7eb);
        flex-wrap: wrap;
    }
    .mp-search {
        flex: 1; min-width: 160px; padding: 0.4rem 0.75rem; border-radius: 8px;
        border: 1px solid var(--admin-border); font-size: 0.8125rem;
        outline: none; background: var(--admin-bg, #f7f8fa);
    }
    .mp-search:focus { border-color: var(--admin-accent); box-shadow: 0 0 0 2px rgba(var(--admin-accent-rgb, 22,163,74), 0.15); }
    .mp-folder-select {
        padding: 0.4rem 0.75rem; border-radius: 8px;
        border: 1px solid var(--admin-border); font-size: 0.8125rem;
        background: var(--admin-bg); cursor: pointer;
    }
    .mp-upload-btn {
        padding: 0.4rem 0.85rem; border-radius: 8px; border: none;
        background: var(--admin-accent, #16a34a); color: #fff; font-size: 0.8125rem;
        font-weight: 500; cursor: pointer; transition: opacity 0.15s;
    }
    .mp-upload-btn:hover { opacity: 0.9; }
    .mp-body {
        flex: 1; overflow-y: auto; padding: 1rem 1.25rem; min-height: 0;
    }
    .mp-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 0.75rem;
    }
    .mp-item {
        position: relative; aspect-ratio: 1; border-radius: 10px; overflow: hidden;
        border: 2px solid transparent; cursor: pointer; transition: all 0.15s;
        background: #f3f4f6;
    }
    .mp-item:hover { border-color: var(--admin-accent, #16a34a); transform: translateY(-1px); }
    .mp-item.selected { border-color: var(--admin-accent, #16a34a); box-shadow: 0 0 0 3px rgba(var(--admin-accent-rgb, 22,163,74), 0.2); }
    .mp-item.selected::after {
        content: '✓'; position: absolute; top: 6px; right: 6px; width: 22px; height: 22px;
        background: var(--admin-accent, #16a34a); color: #fff; border-radius: 50%;
        display: grid; place-items: center; font-size: 0.65rem; font-weight: 700;
    }
    .mp-item img { width: 100%; height: 100%; object-fit: cover; }
    .mp-item-name {
        position: absolute; bottom: 0; left: 0; right: 0; padding: 4px 6px;
        background: linear-gradient(transparent, rgba(0,0,0,0.7));
        color: #fff; font-size: 0.6rem; overflow: hidden; text-overflow: ellipsis;
        white-space: nowrap;
    }
    .mp-empty {
        text-align: center; padding: 3rem; color: var(--admin-text-muted);
    }
    .mp-loading { text-align: center; padding: 2rem; color: var(--admin-text-muted); }
    .mp-load-more {
        display: block; margin: 1rem auto 0; padding: 0.4rem 1.5rem; border-radius: 8px;
        border: 1px solid var(--admin-border); background: none; cursor: pointer;
        font-size: 0.8125rem; color: var(--admin-text-muted);
    }
    .mp-load-more:hover { background: var(--admin-bg); }
    .mp-footer {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.75rem 1.25rem; border-top: 1px solid var(--admin-border);
    }
    .mp-selected-count { font-size: 0.8125rem; color: var(--admin-text-muted); }
    .mp-insert-btn {
        padding: 0.5rem 1.25rem; border-radius: 8px; border: none;
        background: var(--admin-accent, #16a34a); color: #fff; font-weight: 600;
        font-size: 0.875rem; cursor: pointer; transition: opacity 0.15s;
    }
    .mp-insert-btn:hover { opacity: 0.9; }
    .mp-insert-btn:disabled { opacity: 0.4; cursor: not-allowed; }
    .mp-dropzone {
        border: 2px dashed var(--admin-border); border-radius: 10px;
        padding: 1.5rem; text-align: center; margin-bottom: 1rem;
        transition: all 0.2s; display: none;
    }
    .mp-dropzone.active { display: block; }
    .mp-dropzone.dragover { border-color: var(--admin-accent); background: rgba(var(--admin-accent-rgb, 22,163,74), 0.05); }
    .mp-dropzone p { margin: 0; font-size: 0.8125rem; color: var(--admin-text-muted); }
    .mp-preview-wrap {
        position: relative; display: inline-block; margin-top: 0.5rem;
    }
    .mp-preview-wrap img {
        max-height: 180px; width: 100%; object-fit: cover; border-radius: 8px; border: 1px solid var(--admin-border);
    }
    .mp-preview-remove {
        position: absolute; top: 4px; right: 4px; width: 22px; height: 22px;
        border-radius: 50%; border: none; background: rgba(239,68,68,0.9);
        color: #fff; font-size: 0.65rem; cursor: pointer; display: grid; place-items: center;
    }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="admin-page-title mb-1">{{ $scholarship ? 'Edit Scholarship' : 'New Scholarship' }}</h1>
            <p class="text-muted small mb-0">{{ $isGlobal ? 'Global (appears on all college pages)' : $collegeName }}</p>
        </div>
        <a href="{{ route($routePrefix . '.index', $routeParams) }}" class="btn btn-outline-secondary">Back to Scholarships</a>
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST"
                  action="{{ $scholarship
                      ? route($routePrefix . '.update', array_merge($routeParams, ['scholarship' => $scholarship->id]))
                      : route($routePrefix . '.store', $routeParams) }}"
                  enctype="multipart/form-data">
                @csrf
                @if ($scholarship)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Scholarship Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $scholarship?->title ?? '') }}" placeholder="e.g., Academic Excellence Scholarship" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control quill-editor @error('description') is-invalid @enderror" rows="3"
                                      placeholder="Brief overview of this scholarship...">{{ old('description', $scholarship?->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="qualifications" class="form-label">Qualifications</label>
                            <textarea name="qualifications" id="qualifications" class="form-control quill-editor @error('qualifications') is-invalid @enderror" rows="3"
                                      placeholder="Who is eligible for this scholarship...">{{ old('qualifications', $scholarship?->qualifications ?? '') }}</textarea>
                            <small class="text-muted">Use new lines for each qualification.</small>
                            @error('qualifications')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="requirements" class="form-label">Requirements</label>
                            <textarea name="requirements" id="requirements" class="form-control quill-editor @error('requirements') is-invalid @enderror" rows="3"
                                      placeholder="Documents or materials needed...">{{ old('requirements', $scholarship?->requirements ?? '') }}</textarea>
                            <small class="text-muted">Use new lines for each requirement.</small>
                            @error('requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="process" class="form-label">Application Process</label>
                            <textarea name="process" id="process" class="form-control quill-editor @error('process') is-invalid @enderror" rows="3"
                                      placeholder="Step-by-step application process...">{{ old('process', $scholarship?->process ?? '') }}</textarea>
                            <small class="text-muted">Use new lines for each step.</small>
                            @error('process')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="benefits" class="form-label">Benefits</label>
                            <textarea name="benefits" id="benefits" class="form-control quill-editor @error('benefits') is-invalid @enderror" rows="3"
                                      placeholder="What the scholar receives...">{{ old('benefits', $scholarship?->benefits ?? '') }}</textarea>
                            <small class="text-muted">Use new lines for each benefit.</small>
                            @error('benefits')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Featured Image</label>

                        {{-- Current image --}}
                        @if ($scholarship?->image)
                            <div class="mb-2" id="currentImageWrap">
                                <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($scholarship->image) }}" alt="Scholarship Image" class="img-fluid rounded border" style="max-height: 180px; width: 100%; object-fit: cover;">
                                <div class="form-text small text-muted">Current image</div>
                            </div>
                        @endif

                        {{-- Media library picked preview --}}
                        <div id="mpPickedPreview" style="display: none;">
                            <div class="mp-preview-wrap">
                                <img id="mpPickedImg" src="" alt="Selected from library">
                                <button type="button" class="mp-preview-remove" id="mpPickedRemove">✕</button>
                            </div>
                            <div class="form-text small text-muted">Selected from Media Library</div>
                        </div>
                        <input type="hidden" name="media_image" id="mpPickedPath" value="">

                        <div class="d-flex flex-column gap-2 mt-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="openMediaPicker">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                Media Library
                            </button>
                            <span class="text-muted small">or upload directly:</span>
                            <input type="file" name="image" class="form-control form-control-sm @error('image') is-invalid @enderror" accept="image/*">
                        </div>
                        <small class="text-muted">Recommended: 16:9 aspect ratio. Max 2MB.</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <hr class="my-2">
                        <button type="submit" class="btn btn-admin-primary">
                            {{ $scholarship ? 'Save Changes' : 'Create Scholarship' }}
                        </button>
                        <a href="{{ route($routePrefix . '.index', $routeParams) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Media Picker Modal ── --}}
    <div class="mp-backdrop" id="mpBackdrop"></div>
    <div class="mp-modal" id="mpModal">
        <div class="mp-header">
            <h5>📸 Media Library</h5>
            <button class="mp-close" id="mpClose">✕</button>
        </div>
        <div class="mp-toolbar">
            <input type="text" class="mp-search" id="mpSearch" placeholder="Search images...">
            <select class="mp-folder-select" id="mpFolder">
                <option value="">All folders</option>
            </select>
            <button class="mp-upload-btn" id="mpUploadBtn">↑ Upload New</button>
            <input type="file" id="mpUploadInput" accept="image/*" multiple style="display:none;">
        </div>
        <div class="mp-body" id="mpBody">
            <div class="mp-dropzone" id="mpDropzone">
                <p>📁 Drop images here or <a href="#" id="mpDropzoneBrowse">browse</a> to upload</p>
            </div>
            <div class="mp-loading" id="mpLoading">Loading images...</div>
            <div class="mp-grid" id="mpGrid"></div>
            <div class="mp-empty" id="mpEmpty" style="display: none;">No images found.</div>
            <button class="mp-load-more" id="mpLoadMore" style="display: none;">Load more</button>
        </div>
        <div class="mp-footer">
            <span class="mp-selected-count" id="mpSelectedCount">No image selected</span>
            <button class="mp-insert-btn" id="mpInsertBtn" disabled>Use Selected</button>
        </div>
    </div>

    <script>
    (function() {
        var apiUrl = @json(route('admin.media.api.index'));
        var uploadUrl = @json(route('admin.media.api.upload'));
        var csrfToken = @json(csrf_token());

        var backdrop = document.getElementById('mpBackdrop');
        var modal = document.getElementById('mpModal');
        var closeBtn = document.getElementById('mpClose');
        var searchInput = document.getElementById('mpSearch');
        var folderSelect = document.getElementById('mpFolder');
        var uploadBtn = document.getElementById('mpUploadBtn');
        var uploadInput = document.getElementById('mpUploadInput');
        var dropzone = document.getElementById('mpDropzone');
        var dropzoneBrowse = document.getElementById('mpDropzoneBrowse');
        var grid = document.getElementById('mpGrid');
        var loading = document.getElementById('mpLoading');
        var empty = document.getElementById('mpEmpty');
        var loadMore = document.getElementById('mpLoadMore');
        var selectedCount = document.getElementById('mpSelectedCount');
        var insertBtn = document.getElementById('mpInsertBtn');
        var openBtn = document.getElementById('openMediaPicker');
        var pickedPreview = document.getElementById('mpPickedPreview');
        var pickedImg = document.getElementById('mpPickedImg');
        var pickedPath = document.getElementById('mpPickedPath');
        var pickedRemove = document.getElementById('mpPickedRemove');

        var currentPage = 1;
        var searchTimeout = null;
        var selectedImage = null; // Single selection: {url, path, name}
        var showDropzone = false;

        function openModal() {
            backdrop.classList.add('show');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            selectedImage = null;
            updateSelectionUI();
            loadMedia(1, true);
        }
        function closeModal() {
            backdrop.classList.remove('show');
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }

        openBtn.addEventListener('click', openModal);
        closeBtn.addEventListener('click', closeModal);
        backdrop.addEventListener('click', closeModal);
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal(); });

        function loadMedia(page, reset) {
            if (reset) { grid.innerHTML = ''; currentPage = 1; page = 1; }
            loading.style.display = 'block';
            empty.style.display = 'none';
            loadMore.style.display = 'none';

            var params = new URLSearchParams({ page: page, search: searchInput.value, folder: folderSelect.value });
            fetch(apiUrl + '?' + params.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                loading.style.display = 'none';
                if (page === 1 && data.folders) {
                    var cv = folderSelect.value;
                    folderSelect.innerHTML = '<option value="">All folders</option>';
                    data.folders.forEach(function(f) {
                        var opt = document.createElement('option');
                        opt.value = f; opt.textContent = f.replace('images/', '');
                        if (f === cv) opt.selected = true;
                        folderSelect.appendChild(opt);
                    });
                }
                if (data.files.length === 0 && page === 1) { empty.style.display = 'block'; return; }
                data.files.forEach(function(file) {
                    var el = document.createElement('div');
                    el.className = 'mp-item';
                    if (selectedImage && selectedImage.path === file.path) el.classList.add('selected');
                    el.dataset.path = file.path;
                    el.innerHTML = '<img src="' + file.url + '" alt="' + file.name + '" loading="lazy"><span class="mp-item-name">' + file.name + '</span>';
                    el.addEventListener('click', function() { selectItem(el, file); });
                    grid.appendChild(el);
                });
                currentPage = data.page;
                if (data.has_more) loadMore.style.display = 'block';
            })
            .catch(function() { loading.style.display = 'none'; });
        }

        // Single selection
        function selectItem(el, file) {
            if (selectedImage && selectedImage.path === file.path) {
                selectedImage = null;
                el.classList.remove('selected');
            } else {
                grid.querySelectorAll('.mp-item.selected').forEach(function(s) { s.classList.remove('selected'); });
                selectedImage = { url: file.url, path: file.path, name: file.name };
                el.classList.add('selected');
            }
            updateSelectionUI();
        }

        function updateSelectionUI() {
            selectedCount.textContent = selectedImage ? '1 image selected' : 'No image selected';
            insertBtn.disabled = !selectedImage;
        }

        insertBtn.addEventListener('click', function() {
            if (!selectedImage) return;
            pickedImg.src = selectedImage.url;
            pickedPath.value = selectedImage.path;
            pickedPreview.style.display = 'block';
            closeModal();
        });

        pickedRemove.addEventListener('click', function() {
            pickedImg.src = '';
            pickedPath.value = '';
            pickedPreview.style.display = 'none';
        });

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() { loadMedia(1, true); }, 300);
        });
        folderSelect.addEventListener('change', function() { loadMedia(1, true); });
        loadMore.addEventListener('click', function() { loadMedia(currentPage + 1, false); });

        uploadBtn.addEventListener('click', function() {
            showDropzone = !showDropzone;
            dropzone.classList.toggle('active', showDropzone);
            if (showDropzone) uploadInput.click();
        });
        dropzoneBrowse.addEventListener('click', function(e) { e.preventDefault(); uploadInput.click(); });
        uploadInput.addEventListener('change', function() { if (this.files.length > 0) uploadFiles(this.files); });

        var mpBody = document.getElementById('mpBody');
        mpBody.addEventListener('dragover', function(e) { e.preventDefault(); dropzone.classList.add('active', 'dragover'); });
        mpBody.addEventListener('dragleave', function() { dropzone.classList.remove('dragover'); });
        mpBody.addEventListener('drop', function(e) {
            e.preventDefault(); dropzone.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) uploadFiles(e.dataTransfer.files);
        });

        function uploadFiles(files) {
            var fd = new FormData();
            fd.append('folder', folderSelect.value || 'images/uploads');
            for (var i = 0; i < files.length; i++) fd.append('files[]', files[i]);
            dropzone.innerHTML = '<p>⏳ Uploading...</p>';
            dropzone.classList.add('active');
            fetch(uploadUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd,
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                dropzone.innerHTML = '<p>📁 Drop images here or <a href="#" id="mpDropzoneBrowse">browse</a> to upload</p>';
                dropzone.classList.remove('active'); showDropzone = false; uploadInput.value = '';
                document.getElementById('mpDropzoneBrowse')?.addEventListener('click', function(e) { e.preventDefault(); uploadInput.click(); });
                if (data.files && data.files.length > 0) {
                    var f = data.files[0];
                    selectedImage = { url: f.url, path: f.path, name: f.name };
                    updateSelectionUI();
                }
                loadMedia(1, true);
            })
            .catch(function() {
                dropzone.innerHTML = '<p style="color:#ef4444;">Upload failed. Try again.</p>';
                setTimeout(function() {
                    dropzone.innerHTML = '<p>📁 Drop images here or <a href="#">browse</a> to upload</p>';
                    dropzone.classList.remove('active');
                }, 2000);
            });
        }
    })();
    </script>

@push('scripts')
<script>
(function() {
    // Wait for Quill to be ready, then attach a SINGLE form submit listener
    // that syncs ALL quill editors at once (fixes the multiple-editor sync bug).
    var form = document.querySelector('form[enctype="multipart/form-data"]');
    if (!form) return;

    form.addEventListener('submit', function() {
        // Find every hidden textarea that has been quill-initialized
        form.querySelectorAll('textarea[data-quill-initialized]').forEach(function(textarea) {
            // The quill container is the next sibling div after the hidden textarea
            var container = textarea.nextElementSibling;
            if (!container) return;
            var qlRoot = container.querySelector('.ql-editor');
            if (!qlRoot) return;
            var html = qlRoot.innerHTML;
            textarea.value = (html === '<p><br></p>') ? '' : html;
            textarea.removeAttribute('disabled'); // ensure it submits
        });
    }, true); // capture phase so it fires before any other submit listeners
})();
</script>
@endpush
@endsection
