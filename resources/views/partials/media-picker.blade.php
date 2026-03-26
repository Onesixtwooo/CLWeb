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

    /* Upload dropzone inside modal */
    .mp-dropzone {
        border: 2px dashed var(--admin-border); border-radius: 10px;
        padding: 1.5rem; text-align: center; margin-bottom: 1rem;
        transition: all 0.2s; display: none;
    }
    .mp-dropzone.active { display: block; }
    .mp-dropzone.dragover { border-color: var(--admin-accent); background: rgba(var(--admin-accent-rgb, 22,163,74), 0.05); }
    .mp-dropzone p { margin: 0; font-size: 0.8125rem; color: var(--admin-text-muted); }

    /* Selected media previews */
    .mp-selected-previews {
        display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;
    }
    .mp-selected-preview {
        position: relative; width: 100px; height: 100px; border-radius: 8px;
        overflow: hidden; border: 1px solid var(--admin-border);
    }
    .mp-selected-preview img { width: 100%; height: 100%; object-fit: cover; }
    .mp-selected-preview .mp-remove-btn {
        position: absolute; top: 3px; right: 3px; width: 20px; height: 20px;
        border-radius: 50%; border: none; background: rgba(239,68,68,0.9);
        color: #fff; font-size: 0.6rem; cursor: pointer; display: grid; place-items: center;
    }

    /* ── Spinner ── */
    .mp-spinner {
        width: 40px; height: 40px;
        border: 3px solid rgba(var(--admin-accent-rgb, 22,163,74), 0.1);
        border-top-color: var(--admin-accent, #16a34a);
        border-radius: 50%;
        animation: mp-spin 0.8s linear infinite;
        margin: 0 auto 1rem;
    }
    @keyframes mp-spin {
        to { transform: rotate(360deg); }
    }

    /* ── Progress Bar ── */
    .mp-progress-container {
        width: 100%; max-width: 300px; margin: 1rem auto;
        background: #eee; border-radius: 10px; height: 8px; overflow: hidden;
        display: none;
    }
    .mp-progress-bar {
        height: 100%; background: var(--admin-accent, #16a34a); width: 0%;
        transition: width 0.2s ease;
    }
    .mp-progress-text {
        font-size: 0.75rem; color: var(--admin-text-muted); margin-top: 0.5rem;
        font-weight: 600;
    }
</style>
@endpush

{{-- ── Media Picker Modal ── --}}
<div class="mp-backdrop" id="mpBackdrop" style="display: none;"></div>
<div class="mp-modal" id="mpModal" style="display: none;">
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
        <div class="mp-loading" id="mpLoading">
            <div class="mp-spinner"></div>
            <div>Loading images...</div>
        </div>
        <div id="mpUploadStatus" style="display: none; text-align: center; padding: 2rem;">
            <div class="mp-spinner"></div>
            <div class="mp-progress-text" id="mpProgressText">Uploading: 0%</div>
            <div class="mp-progress-container" id="mpProgressContainer" style="display: block;">
                <div class="mp-progress-bar" id="mpProgressBar"></div>
            </div>
        </div>
        <div class="mp-grid" id="mpGrid"></div>
        <div class="mp-empty" id="mpEmpty" style="display: none;">No images found.</div>
        <button class="mp-load-more" id="mpLoadMore" style="display: none;">Load more</button>
    </div>
    <div class="mp-footer">
        <span class="mp-selected-count" id="mpSelectedCount">0 images selected</span>
        <button class="mp-insert-btn" id="mpInsertBtn" disabled>Insert Selected</button>
    </div>
</div>

<script>
(function() {
    var apiUrl = @json(route('admin.media.api.index'));
    var uploadUrl = @json(route('admin.media.api.upload'));
    var csrfToken = @json(csrf_token());

    // DOM
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

    // These should exist in the parent view
    var openBtn = document.getElementById('openMediaPicker');
    var previewsContainer = document.getElementById('mediaPickerPreviews');
    var selectedContainer = document.getElementById('mediaPickerSelected');
    var inputsContainer = document.getElementById('mediaPickerInputs');

    if (!openBtn || !previewsContainer || !selectedContainer || !inputsContainer) {
        console.warn('Media Picker: Required DOM elements (openMediaPicker, mediaPickerPreviews, mediaPickerSelected, mediaPickerInputs) not found in parent view.');
        return;
    }

    var currentPage = 1;
    var searchTimeout = null;
    var selectedImages = []; // [{url, path, name}]
    var showDropzone = false;

    // ── Open / Close ──
    function openModal() {
        backdrop.classList.add('show');
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
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

    // ── Load Media ──
    function loadMedia(page, reset) {
        if (reset) {
            grid.innerHTML = '';
            currentPage = 1;
            page = 1;
        }
        loading.style.display = 'block';
        empty.style.display = 'none';
        loadMore.style.display = 'none';
        grid.style.opacity = '0.4';
        grid.style.pointerEvents = 'none';

        var params = new URLSearchParams({
            page: page,
            search: searchInput.value,
            folder: folderSelect.value,
        });

        fetch(apiUrl + '?' + params.toString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            loading.style.display = 'none';
            grid.style.opacity = '1';
            grid.style.pointerEvents = 'auto';

            // Populate folder select on first load
            if (page === 1 && data.folders) {
                var currentVal = folderSelect.value;
                folderSelect.innerHTML = '<option value="">All folders</option>';
                data.folders.forEach(function(f) {
                    var opt = document.createElement('option');
                    opt.value = f;
                    opt.textContent = f.replace('images/', '');
                    if (f === currentVal) opt.selected = true;
                    folderSelect.appendChild(opt);
                });
            }

            if (data.files.length === 0 && page === 1) {
                empty.style.display = 'block';
                return;
            }

            data.files.forEach(function(file) {
                var el = document.createElement('div');
                el.className = 'mp-item';
                if (selectedImages.some(function(s) { return s.path === file.path; })) {
                    el.classList.add('selected');
                }
                el.dataset.path = file.path;
                el.dataset.url = file.url;
                el.dataset.name = file.name;
                el.innerHTML = '<img src="' + file.url + '" alt="' + file.name + '" loading="lazy">' +
                               '<span class="mp-item-name">' + file.name + '</span>';
                el.addEventListener('click', function() { toggleSelect(el, file); });
                grid.appendChild(el);
            });

            currentPage = data.page;
            if (data.has_more) {
                loadMore.style.display = 'block';
            }
        })
        .catch(function() { loading.style.display = 'none'; });
    }

    // ── Selection ──
    function toggleSelect(el, file) {
        var idx = selectedImages.findIndex(function(s) { return s.path === file.url; });
        if (idx > -1) {
            selectedImages.splice(idx, 1);
            el.classList.remove('selected');
        } else {
            // CRITICAL: We store file.url (the proxy URL) in our database field instead of file.path
            // This ensures resolveImageUrl skips local prefixing and handles it as a full external URL.
            selectedImages.push({ url: file.url, path: file.url, name: file.name });
            el.classList.add('selected');
        }
        updateSelectionUI();
    }

    function updateSelectionUI() {
        var n = selectedImages.length;
        selectedCount.textContent = n + ' image' + (n !== 1 ? 's' : '') + ' selected';
        insertBtn.disabled = n === 0;
    }

    // ── Insert ──
    insertBtn.addEventListener('click', function() {
        // Build previews + hidden inputs
        previewsContainer.innerHTML = '';
        inputsContainer.innerHTML = '';
        selectedImages.forEach(function(img, i) {
            // Preview
            var div = document.createElement('div');
            div.className = 'mp-selected-preview';
            div.innerHTML = '<img src="' + img.url + '" alt="">' +
                '<button type="button" class="mp-remove-btn" data-idx="' + i + '">✕</button>';
            previewsContainer.appendChild(div);

            // Hidden input
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'media_images[]';
            input.value = img.url; // Save the proxy URL
            inputsContainer.appendChild(input);
        });

        selectedContainer.style.display = selectedImages.length > 0 ? 'block' : 'none';

        // Attach remove handlers
        previewsContainer.querySelectorAll('.mp-remove-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var idx = parseInt(this.dataset.idx);
                selectedImages.splice(idx, 1);
                insertBtn.click(); // Re-render
            });
        });

        closeModal();
    });

    // ── Search ──
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() { loadMedia(1, true); }, 300);
    });

    // ── Folder filter ──
    folderSelect.addEventListener('change', function() { loadMedia(1, true); });

    // ── Load more ──
    loadMore.addEventListener('click', function() { loadMedia(currentPage + 1, false); });

    // ── Upload ──
    uploadBtn.addEventListener('click', function() {
        showDropzone = true;
        dropzone.classList.add('active');
        uploadInput.click();
    });
    dropzoneBrowse.addEventListener('click', function(e) { 
        e.preventDefault(); 
        uploadInput.click(); 
    });

    uploadInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            uploadFiles(this.files);
        }
    });

    // Drag & drop
    var mpBody = document.getElementById('mpBody');
    mpBody.addEventListener('dragover', function(e) { e.preventDefault(); dropzone.classList.add('active', 'dragover'); });
    mpBody.addEventListener('dragleave', function() { dropzone.classList.remove('dragover'); });
    mpBody.addEventListener('drop', function(e) {
        e.preventDefault();
        dropzone.classList.remove('dragover');
        if (e.dataTransfer.files.length > 0) uploadFiles(e.dataTransfer.files);
    });

    function uploadFiles(files) {
        var fd = new FormData();
        fd.append('folder', folderSelect.value || 'images/uploads');
        for (var i = 0; i < files.length; i++) fd.append('files[]', files[i]);

        var uploadStatus = document.getElementById('mpUploadStatus');
        var progressText = document.getElementById('mpProgressText');
        var progressBar = document.getElementById('mpProgressBar');
        
        dropzone.classList.remove('active');
        grid.style.display = 'none';
        uploadStatus.style.display = 'block';
        empty.style.display = 'none';
        loadMore.style.display = 'none';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', uploadUrl, true);
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                var percent = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percent + '%';
                progressText.textContent = 'Uploading: ' + percent + '%';
            }
        };

        xhr.onload = function() {
            uploadStatus.style.display = 'none';
            grid.style.display = 'grid';
            
            if (xhr.status >= 200 && xhr.status < 300) {
                var data = JSON.parse(xhr.responseText);
                showDropzone = false;
                uploadInput.value = '';

                if (data.files) {
                    data.files.forEach(function(f) {
                        if (!selectedImages.some(function(s) { return s.path === f.path; })) {
                            selectedImages.push({ url: f.url, path: f.path, name: f.name });
                        }
                    });
                    updateSelectionUI();
                }
                loadMedia(1, true);
            } else {
                uploadInput.value = ''; // Allow retry of same file
                alert('Upload failed. Please try again.');
            }
        };

        xhr.onerror = function() {
            uploadStatus.style.display = 'none';
            grid.style.display = 'grid';
            uploadInput.value = ''; // Allow retry of same file
            alert('An error occurred during upload.');
        };

        xhr.send(fd);
    }
})();
</script>
