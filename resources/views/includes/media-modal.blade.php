{{-- ── Media Picker Modal Overlay ── --}}
<div class="mp-backdrop" id="mpBackdrop" style="display: none;"></div>
<div class="mp-modal" id="mpModal" style="display: none;">
    <div class="mp-header">
        <h5>📸 Media Library</h5>
        <button class="mp-close" id="mpClose" type="button">✕</button>
    </div>
    <div class="mp-toolbar">
        <input type="text" class="mp-search" id="mpSearch" placeholder="Search images...">
        <select class="mp-folder-select" id="mpFolder">
            <option value="">All folders</option>
        </select>
        <button class="mp-upload-btn" id="mpUploadBtn" type="button">↑ Upload New</button>
        <input type="file" id="mpUploadInput" accept="image/*" multiple style="display:none;">
    </div>
    <div class="mp-body" id="mpBody">
        <div class="mp-dropzone" id="mpDropzone">
            <p>📁 Drop images here or <a href="#" id="mpDropzoneBrowse">browse</a> to upload</p>
        </div>
        <div class="mp-loading" id="mpLoading">Loading images...</div>
        <div class="mp-grid" id="mpGrid"></div>
        <div class="mp-empty" id="mpEmpty" style="display: none;">No images found.</div>
        <button class="mp-load-more" id="mpLoadMore" style="display: none;" type="button">Load more</button>
    </div>
    <div class="mp-footer">
        <span class="mp-selected-count" id="mpSelectedCount">No image selected</span>
        <button class="mp-insert-btn" id="mpInsertBtn" disabled type="button">Use Selected</button>
    </div>
</div>

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
        max-height: 120px; width: auto; object-fit: contain; border-radius: 8px; border: 1px solid var(--admin-border);
    }
    .mp-preview-remove {
        position: absolute; top: 4px; right: 4px; width: 22px; height: 22px;
        border-radius: 50%; border: none; background: rgba(239,68,68,0.9);
        color: #fff; font-size: 0.65rem; cursor: pointer; display: grid; place-items: center;
    }
</style>
@endpush

@push('scripts')
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
    var grid = document.getElementById('mpGrid');
    var loading = document.getElementById('mpLoading');
    var empty = document.getElementById('mpEmpty');
    var loadMore = document.getElementById('mpLoadMore');
    var selectedCount = document.getElementById('mpSelectedCount');
    var insertBtn = document.getElementById('mpInsertBtn');
    
    // Picked UI elements
    var pickedPreview = document.getElementById('mpPickedPreview');
    var pickedImg = document.getElementById('mpPickedImg');
    var pickedPath = document.getElementById('mpPickedPath');
    var pickedRemove = document.getElementById('mpPickedRemove');
    var openBtn = document.getElementById('openMediaPicker');

    var currentPage = 1;
    var searchTimeout = null;
    var selectedImages = []; // Changed to array
    var multiMode = false; // New variable
    var currentCallback = null;

    window.showMediaModal = function(callback, isMulti = false, defaultFolder = '') {
        currentCallback = callback;
        multiMode = !!isMulti;
        if (defaultFolder) folderSelect.value = defaultFolder;
        openModal();
    };

    function openModal() {
        backdrop.classList.add('show');
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        selectedImages = []; // Reset as array
        updateSelectionUI();
        loadMedia(1, true);
    }

    function closeModal() {
        backdrop.classList.remove('show');
        modal.classList.remove('show');
        document.body.style.overflow = '';
        currentCallback = null;
    }

    if (openBtn) {
        openBtn.addEventListener('click', openModal);
    }
    closeBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);

    function loadMedia(page, reset) {
        if (reset) { grid.innerHTML = ''; currentPage = 1; }
        loading.style.display = 'block';
        empty.style.display = 'none';
        loadMore.style.display = 'none';

        var params = new URLSearchParams({ page: page, search: searchInput.value, folder: folderSelect.value });
        fetch(apiUrl + '?' + params.toString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(r => r.json())
        .then(data => {
            loading.style.display = 'none';
            if (page === 1 && data.folders) {
                var cv = folderSelect.value;
                folderSelect.innerHTML = '<option value="">All folders</option>';
                data.folders.forEach(f => {
                    var opt = document.createElement('option');
                    opt.value = f; opt.textContent = f.replace('images/', '');
                    if (f === cv) opt.selected = true;
                    folderSelect.appendChild(opt);
                });
            }
            if (data.files.length === 0 && page === 1) { empty.style.display = 'block'; return; }
            data.files.forEach(file => {
                var el = document.createElement('div');
                el.className = 'mp-item';
                if (selectedImages.some(img => img.path === file.path)) el.classList.add('selected');
                el.dataset.path = file.path;
                el.innerHTML = `<img src="${file.url}" alt="${file.name}" loading="lazy"><span class="mp-item-name">${file.name}</span>`;
                el.addEventListener('click', () => selectItem(el, file));
                grid.appendChild(el);
            });
            currentPage = data.page;
            if (data.has_more) loadMore.style.display = 'block';
        })
        .catch(() => { loading.style.display = 'none'; });
    }

    function selectItem(el, file) {
        if (multiMode) {
            var index = selectedImages.findIndex(img => img.path === file.path);
            if (index > -1) {
                selectedImages.splice(index, 1);
                el.classList.remove('selected');
            } else {
                selectedImages.push({ url: file.url, path: file.path, name: file.name });
                el.classList.add('selected');
            }
        } else {
            grid.querySelectorAll('.mp-item.selected').forEach(s => s.classList.remove('selected'));
            selectedImages = [{ url: file.url, path: file.path, name: file.name }];
            el.classList.add('selected');
        }
        updateSelectionUI();
    }

    function updateSelectionUI() {
        if (multiMode) {
            selectedCount.textContent = selectedImages.length > 0 ? selectedImages.length + ' images selected' : 'No images selected';
        } else {
            selectedCount.textContent = selectedImages.length > 0 ? '1 image selected' : 'No image selected';
        }
        insertBtn.disabled = selectedImages.length === 0;
    }

    insertBtn.addEventListener('click', function() {
        if (selectedImages.length === 0) return;
        
        if (currentCallback) {
            if (multiMode) {
                // For multimode, pass the whole array
                currentCallback(selectedImages);
            } else {
                // For single mode, maintain compatibility with (path, url)
                currentCallback(selectedImages[0].path, selectedImages[0].url);
            }
        } else {
            // Fallback for accreditation views
            var img = selectedImages[0];
            if (pickedImg) pickedImg.src = img.url;
            if (pickedPath) pickedPath.value = img.path;
            if (pickedPreview) pickedPreview.style.display = 'block';
        }
        closeModal();
    });

    if (pickedRemove) {
        pickedRemove.addEventListener('click', function() {
            if (pickedImg) pickedImg.src = '';
            if (pickedPath) pickedPath.value = '';
            if (pickedPreview) pickedPreview.style.display = 'none';
        });
    }

    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadMedia(1, true), 300);
    });
    folderSelect.addEventListener('change', () => loadMedia(1, true));
    loadMore.addEventListener('click', () => loadMedia(currentPage + 1, false));

    uploadBtn.addEventListener('click', () => uploadInput.click());
    uploadInput.addEventListener('change', function() { if (this.files.length > 0) uploadFiles(this.files); });

    function uploadFiles(files) {
        var fd = new FormData();
        fd.append('folder', folderSelect.value || 'images/uploads');
        for (var i = 0; i < files.length; i++) fd.append('files[]', files[i]);
        fetch(uploadUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: fd,
        })
        .then(r => r.json())
        .then(data => {
            if (data.files && data.files.length > 0) {
                if (multiMode) {
                    data.files.forEach(f => {
                        if (!selectedImages.some(img => img.path === f.path)) {
                            selectedImages.push({ url: f.url, path: f.path, name: f.name });
                        }
                    });
                } else {
                    var f = data.files[0];
                    selectedImages = [{ url: f.url, path: f.path, name: f.name }];
                }
                updateSelectionUI();
            }
            loadMedia(1, true);
        });
    }
})();
</script>
@endpush
