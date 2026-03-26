@extends('admin.layout')

@section('title', $organization->name)

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
    .colleges-layout {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        min-height: 480px;
    }
    .colleges-section-list {
        width: 100%;
        flex-shrink: 0;
        background: var(--admin-surface);
        border: 1px solid var(--admin-border);
        border-radius: var(--admin-radius-lg);
        overflow: hidden;
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
        display: block;
        padding: 0.75rem 1.25rem;
        color: var(--admin-text);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9375rem;
        border-left: 3px solid transparent;
        transition: background 0.15s ease, border-color 0.15s ease;
    }
    @media (min-width: 768px) {
        .colleges-layout {
            flex-direction: row;
            gap: 0;
        }
        .colleges-section-list {
            width: 240px;
            margin-right: 1.5rem;
        }
    }
    .colleges-section-item:hover {
        background: var(--admin-accent-soft);
        color: var(--admin-accent);
    }
    .colleges-section-item.active {
        background: var(--admin-primary-light);
        color: var(--admin-primary);
        font-weight: 600;
        box-shadow: inset 3px 0 0 var(--admin-primary);
    }
    
    /* Item Management Styles */
    .group-hover-show {
        opacity: 0;
        transition: all 0.2s ease;
        pointer-events: none;
    }
    .group:hover .group-hover-show {
        opacity: 1;
        pointer-events: auto;
    }
    .item-card-actions {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        z-index: 10;
        display: flex;
        gap: 4px;
        background: rgba(255, 255, 255, 0.95);
        padding: 4px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border: 1px solid rgba(0,0,0,0.05);
    }
    .btn-xs-custom {
        padding: 2px 6px;
        font-size: 0.7rem;
    }
    .btn-item-action {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.75rem;
        font-weight: 600;
        font-size: 0.75rem;
        border-radius: 6px;
    }
    .btn-item-action i {
        font-size: 0.875rem;
    }
    .sortable-photo-card {
        cursor: grab;
    }
    .sortable-photo-card.dragging {
        opacity: 0.55;
        transform: scale(0.98);
    }
    .drag-handle {
        cursor: grab;
    }
    .colleges-detail {
        flex: 1;
        min-width: 0;
        background: var(--admin-surface);
        border: 1px solid var(--admin-border);
        border-radius: var(--admin-radius-lg);
        overflow: hidden;
        padding: 1.25rem 1rem;
    }
    @media (min-width: 768px) {
        .colleges-detail {
            padding: 1.75rem 2rem;
        }
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
    .media-upload-zone {
        border: 2px dashed var(--admin-border);
        border-radius: var(--admin-radius-lg);
        padding: 3.5rem 2rem;
        text-align: center;
        transition: all 0.2s ease;
        background: var(--admin-background);
        cursor: pointer;
    }
    .media-upload-zone:hover, .media-upload-zone.dragover {
        border-color: var(--admin-primary);
        background: rgba(13, 110, 253, 0.05);
    }
</style>
@endpush

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.colleges.show', ['college' => $collegeSlug, 'section' => 'organizations']) }}" class="btn btn-outline-secondary btn-sm mb-3" title="Back to Organizations">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Back
        </a>

        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">{{ $organization->name }}</h1>
                <p class="text-muted small mb-0">
                    {{ $collegeName }}
                    @if ($organization->department)
                        / <span class="text-primary fw-600">{{ $organization->department->name }}</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('admin.organizations.edit-scoped', ['college' => $collegeSlug, 'organization' => $organization]) }}" class="btn btn-outline-secondary btn-sm" title="Edit Organization Info">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
            </a>
        </div>
    </div>

    <div class="colleges-layout">
        {{-- Left: section list --}}
        @include('admin.organizations.partials.sections-sidebar')

        {{-- Right: detail content --}}
        <div style="flex: 1; min-width: 0;">
            <div class="colleges-detail">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <h2 class="colleges-detail-title mb-0">{{ $sectionContent['title'] }}</h2>
                    <div class="d-flex gap-2">
                        @if ($currentSection !== 'overview')
                            @php
                                $itemLabel = 'Item';
                                if (str_contains(strtolower($sectionContent['title']), 'project')) $itemLabel = 'Project';
                                elseif ($currentSection === 'officers') $itemLabel = 'Member';
                                elseif ($currentSection === 'activities') $itemLabel = 'Activity';
                                elseif ($currentSection === 'gallery') $itemLabel = 'Photo';
                            @endphp
                            @if ($currentSection === 'gallery')
                                @php
                                    $inAlbum = isset($activeAlbumIndex) && $activeAlbumIndex !== null && isset($sectionContent['items'][$activeAlbumIndex]);
                                    $albumIndex = $activeAlbumIndex;
                                @endphp
                                @if ($inAlbum)
                                    <a href="{{ route('admin.organizations.show-section', ['college' => $collegeSlug, 'organization' => $organization, 'section' => 'gallery']) }}" class="btn btn-sm btn-outline-secondary shadow-sm rounded-pill px-3 me-2">
                                        <i class="bi bi-arrow-left"></i> <span class="d-none d-md-inline">Back to Albums</span>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-primary shadow-sm rounded-pill px-3" onclick="openBatchMediaModal(this, {{ $albumIndex }})">
                                        <i class="bi bi-images"></i> <span class="d-none d-md-inline">Batch Upload</span>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm rounded-pill px-3" onclick="openItemModal('add', null, null, {{ $albumIndex }})">
                                        <i class="bi bi-plus-lg"></i> <span class="d-none d-md-inline">Add {{ $itemLabel }}
                                    </span></button>
                                @else
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm rounded-pill px-3" onclick="openItemModal('add')">
                                        <i class="bi bi-plus-lg"></i> <span class="d-none d-md-inline">Add Album
                                    </span></button>
                                @endif
                            @else
                                @if ($currentSection === 'officers')
                                    <button type="button" class="btn btn-sm btn-outline-success shadow-sm rounded-pill px-3 me-2" onclick="openAdviserModal()">
                                        <i class="bi bi-person-plus-fill"></i> <span class="d-none d-md-inline">Add Adviser</span>
                                    </button>
                                @endif
                                <button type="button" class="btn btn-sm btn-primary shadow-sm rounded-pill px-3" onclick="openItemModal('add')">
                                    <i class="bi bi-plus-lg"></i> <span class="d-none d-md-inline">Add {{ $itemLabel }}
                                </span></button>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="colleges-detail-body">
                    @if ($currentSection === 'overview')
                        <div class="row g-4">
                            {{-- Left Column: Logo and Key Details --}}
                            <div class="col-lg-4 col-md-5">
                                {{-- Organization logo --}}
                                <div class="mb-4">
                                    @if (!empty($organization->logo))
                                        <div class="d-inline-block">
                                            <img src="{{ str_starts_with($organization->logo, 'http') ? $organization->logo : asset($organization->logo) }}"
                                                 alt="{{ $organization->name }} logo"
                                                 style="max-width: 100%; height: auto; max-height: 200px; object-fit: contain;" class="rounded-4 border shadow-sm p-2 bg-white">
                                        </div>
                                    @else
                                        <div class="bg-light rounded-4 d-flex align-items-center justify-content-center border"
                                             style="width: 140px; height: 140px; color: #ccc; font-size: 0.875rem; text-align: center; padding: 1rem;">
                                            No logo uploaded
                                        </div>
                                    @endif
                                </div>

                                {{-- Key details --}}
                                <div class="d-flex flex-column gap-4">
                                    <div>
                                        <label class="form-label text-muted small fw-700 text-uppercase mb-1">Organization Name</label>
                                        <p class="mb-0 fw-600 fs-5 lh-sm">{{ $organization->name }}</p>
                                    </div>
                                    @if ($organization->acronym)
                                    <div>
                                        <label class="form-label text-muted small fw-700 text-uppercase mb-1">Acronym</label>
                                        <p class="mb-0 fs-5 text-primary fw-700">{{ $organization->acronym }}</p>
                                    </div>
                                    @endif
                                    <div>
                                        <label class="form-label text-muted small fw-700 text-uppercase mb-1">Main Adviser</label>
                                        <p class="mb-0">{{ $organization->adviser ?: 'Not Specified' }}</p>
                                    </div>
                                    @if ($organization->department)
                                    <div>
                                        <label class="form-label text-muted small fw-700 text-uppercase mb-1">Department</label>
                                        <p class="mb-0">
                                            <a href="{{ route('admin.colleges.show-department', ['college' => $collegeSlug, 'department' => $organization->department, 'section' => 'overview']) }}"
                                               class="text-decoration-none fw-600">{{ $organization->department->name }}</a>
                                        </p>
                                    </div>
                                    @endif
                                    <div>
                                        <label class="form-label text-muted small fw-700 text-uppercase mb-1">Visibility</label>
                                        <p class="mb-0">
                                            @if ($organization->is_visible)
                                                <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3">Visible on Public Page</span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle px-3">Hidden from Public Page</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Column: Description --}}
                            <div class="col-lg-8 col-md-7 border-start-md ps-lg-4">
                                @php
                                    $hasContent = !empty($sectionContent['body']) || 
                                                  !empty($sectionContent['image']) || 
                                                  !empty($sectionContent['items']);
                                @endphp

                                @if ($hasContent)
                                    @include('admin.organizations.partials.generic-section')
                                @elseif (!empty($organization->description))
                                    <div>
                                        <label class="form-label text-muted small fw-700 text-uppercase mb-2">Detailed Description</label>
                                        <div class="p-4 bg-light rounded-4 ql-snow border border-light-subtle shadow-sm" style="min-height: 300px;">
                                            <div class="ql-editor" style="padding: 0;">{!! $organization->description !!}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="h-100 d-flex flex-column align-items-center justify-content-center py-5 text-center bg-light rounded-4 border border-dashed">
                                        <i class="bi bi-text-paragraph fs-1 text-muted opacity-25 d-block mb-2"></i>
                                        <p class="text-muted fst-italic mb-0">No description added yet. Click "Edit section details" to add content.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                    @elseif ($currentSection === 'officers')
                        @include('admin.organizations.partials.officers-section')

                    @elseif ($currentSection === 'activities')
                        @include('admin.organizations.partials.activities-section')

                    @elseif ($currentSection === 'gallery')
                        @include('admin.organizations.partials.gallery-section')

                    @else
                        @include('admin.organizations.partials.generic-section')
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('admin.organizations.partials.item-modal')

    <!-- Batch Upload Modal -->
    <div class="modal fade" id="batchUploadModal" tabindex="-1" aria-labelledby="batchUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-700" id="batchUploadModalLabel">Batch Upload Photos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="batchUploadForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body py-4">
                        <div id="batch-upload-zone" class="media-upload-zone">
                            <i class="bi bi-cloud-arrow-up-fill fs-1 text-primary mb-2 d-block"></i>
                            <h6 class="fw-600 mb-1">Drag & Drop images here</h6>
                            <p class="text-muted small mb-3">or click to browse from device</p>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3">Browse Files</button>
                            <input type="file" name="images[]" id="batch-upload-input" class="d-none" accept="image/*" multiple>
                        </div>
                        <div id="batch-upload-preview" class="mt-3 row g-2" style="max-height: 200px; overflow-y: auto;">
                            <!-- File previews -->
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" id="batch-upload-submit" disabled>Upload 0 Files</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Adviser Modal -->
    <div class="modal fade" id="adviserModal" tabindex="-1" aria-labelledby="adviserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form action="{{ route('admin.organizations.update-section', ['organization' => $organization, 'section' => 'officers']) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="title" value="{{ $sectionContent['title'] ?? 'Members' }}">
                    <input type="hidden" name="layout" value="{{ $sectionContent['layout'] ?? 'grid' }}">
                    
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-800" id="adviserModalLabel">Set Main Adviser</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="adviser-select" class="form-label fw-600">Select Adviser</label>
                            <select name="adviser" id="adviser-select" class="form-select rounded-3 shadow-sm">
                                <option value="">-- No Adviser --</option>
                                @foreach ($faculty as $f)
                                    <option value="{{ $f->name }}" {{ $organization->adviser === $f->name ? 'selected' : '' }}>{{ $f->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted mt-1 d-block">
                                Listing faculty from {{ $organization->department ? $organization->department->name : 'the entire college' }}.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Save Adviser</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@include('includes.media-modal')
@push('scripts')

<script>
    window.livePreviewUrl = "{{ route('college.organization.show', ['college' => $collegeSlug, 'organization' => $organization]) }}";
    @if ($currentSection !== 'overview')
        window.livePreviewUrl += "?section={{ $currentSection }}";
    @endif
    @if (isset($activeAlbumIndex) && $activeAlbumIndex !== null)
        window.livePreviewUrl += (window.livePreviewUrl.includes('?') ? '&' : '?') + "album={{ $activeAlbumIndex }}";
    @endif

    const itemModal = new bootstrap.Modal(document.getElementById('itemModal'));
    const itemModalElement = document.getElementById('itemModal');
    const itemForm = document.getElementById('itemForm');
    const itemModalLabel = document.getElementById('itemModalLabel');
    const methodContainer = document.getElementById('method-container');
    
    const currentSection = "{{ $currentSection }}";
    const sectionTitle = "{{ $sectionContent['title'] }}";
    const layout = "{{ $sectionContent['layout'] ?? '' }}";
    
    const inAlbumView = {!! json_encode(isset($activeAlbumIndex) && $activeAlbumIndex !== null) !!};
    const activeAlbumIndex = {!! json_encode($activeAlbumIndex ?? null) !!};

    function ensureItemModalEditor() {
        const textarea = document.getElementById('item-modal-description');
        if (!textarea || typeof tinymce === 'undefined') {
            return;
        }

        const existingEditor = tinymce.get('item-modal-description');
        if (existingEditor) {
            existingEditor.remove();
        }

        tinymce.init({
            selector: '#item-modal-description',
            promotion: false,
            branding: false,
            menubar: false,
            height: 260,
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
            toolbar: 'undo redo | blocks | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link image | removeformat | help',
            setup: function (editor) {
                editor.on('init', function () {
                    editor.setContent(textarea.value || '');
                });

                editor.on('change keyup', function () {
                    editor.save();
                });
            }
        });
    }

    function openAdviserModal() {
        const modal = new bootstrap.Modal(document.getElementById('adviserModal'));
        modal.show();
    }

    function setItemModalDescription(html = '') {
        const textarea = document.getElementById('item-modal-description');
        textarea.value = html || '';

        const editor = typeof tinymce !== 'undefined' ? tinymce.get('item-modal-description') : null;
        if (editor) {
            editor.setContent(html || '');
        }
    }

    itemModalElement.addEventListener('shown.bs.modal', function () {
        ensureItemModalEditor();
    });

    itemModalElement.addEventListener('hidden.bs.modal', function () {
        if (typeof tinymce === 'undefined') {
            return;
        }

        const editor = tinymce.get('item-modal-description');
        if (editor) {
            editor.save();
            editor.remove();
        }
    });
    
    function openItemModal(mode, data = null, index = null, albumIndex = null) {
        // Reset form
        itemForm.reset();
        document.getElementById('item-modal-image').value = '';
        document.getElementById('item-modal-upload').value = '';
        setItemModalDescription('');
        document.getElementById('item-modal-preview').innerHTML = '<i class="bi bi-image text-muted opacity-50 fs-1"></i>';
        methodContainer.innerHTML = '';
        
        // Configuration base on section/layout
        let itemLabel = 'Item';
        if (sectionTitle.toLowerCase().includes('project')) itemLabel = 'Project';
        else if (currentSection === 'officers') itemLabel = 'Member';
        else if (currentSection === 'activities') itemLabel = 'Activity';
        else if (currentSection === 'gallery') {
            itemLabel = (albumIndex !== null || inAlbumView) ? 'Photo' : 'Album';
        }

        const isGalleryPhoto = currentSection === 'gallery' && itemLabel === 'Photo';
        const isGalleryAlbum = currentSection === 'gallery' && itemLabel === 'Album';
        
        // Adjust fields visibility and labels
        document.getElementById('field-date-container').style.display = (currentSection === 'activities') ? 'block' : 'none';
        document.getElementById('field-caption-container').style.display = isGalleryPhoto ? 'block' : 'none';
        document.getElementById('field-name-container').style.display = isGalleryPhoto ? 'none' : 'block';
        document.getElementById('field-role-container').style.display = (isGalleryPhoto || isGalleryAlbum) ? 'none' : 'block';
        document.getElementById('field-description-container').style.display = isGalleryPhoto ? 'none' : 'block';
        document.getElementById('field-visible-container').style.display = (currentSection === 'activities') ? 'block' : 'none';
        document.getElementById('item-modal-visible').checked = true;
        
        // Specialized labels
        document.getElementById('label-name').textContent = itemLabel + (itemLabel === 'Album' ? ' Title' : ' Name');
        document.getElementById('label-role').textContent = (currentSection === 'officers') ? 'Position/Role' : (currentSection === 'activities' ? 'Status/Tag' : 'Subtitle/Role');
        document.getElementById('label-description').textContent = itemLabel === 'Album' ? 'Album Description (optional)' : 'Description';

        let urlSuffix = '';
        if (albumIndex !== null) urlSuffix = '?album=' + albumIndex;
        else if (inAlbumView && activeAlbumIndex !== null) urlSuffix = '?album=' + activeAlbumIndex;
        
        if (mode === 'add') {
            itemModalLabel.textContent = 'Add New ' + itemLabel;
            itemForm.action = "{{ route('admin.organizations.store-item', ['organization' => $organization, 'section' => $currentSection]) }}" + urlSuffix;
            document.getElementById('item-modal-submit').textContent = 'Create ' + itemLabel;
        } else {
            itemModalLabel.textContent = 'Edit ' + itemLabel;
            let baseUrl = "{{ route('admin.organizations.update-item', ['organization' => $organization, 'section' => $currentSection, 'index' => 'INDEX']) }}";
            itemForm.action = baseUrl.replace('INDEX', index) + urlSuffix;
            methodContainer.innerHTML = '@method("PUT")';
            document.getElementById('item-modal-submit').textContent = 'Save Changes';
            
            // Populate data
            document.getElementById('item-modal-name').value = data.name || data.title || '';
            document.getElementById('item-modal-role').value = data.role || '';
            document.getElementById('item-modal-date').value = data.date || '';
            document.getElementById('item-modal-caption').value = data.caption || '';
            document.getElementById('item-modal-visible').checked = (data.is_visible !== false && data.is_visible !== 0 && data.is_visible !== '0');
            setItemModalDescription(data.description || '');
            
            if (data.image) {
                document.getElementById('item-modal-image').value = data.image;
                document.getElementById('item-modal-preview').innerHTML = `<img src="${data.image.startsWith('http') ? data.image : '{{ asset("") }}' + data.image.replace(/^\//, '')}" class="img-fluid rounded h-100 w-100 object-fit-cover">`;
            }
        }
        
        itemModal.show();
    }

    const batchUploadModal = new bootstrap.Modal(document.getElementById("batchUploadModal"));
    let allFiles = []; 

    function openBatchMediaModal(btn, albumIndex = null) {
        let urlSuffix = "";
        if (albumIndex !== null) urlSuffix = "?album=" + albumIndex;
        else if (inAlbumView && activeAlbumIndex !== null) urlSuffix = "?album=" + activeAlbumIndex;

        document.getElementById("batchUploadForm").action = `{{ route("admin.organizations.store-batch-items", ["organization" => $organization, "section" => $currentSection]) }}` + urlSuffix;
        
        allFiles = []; 
        updatePreview();
        if (document.getElementById("batch-upload-input")) {
            document.getElementById("batch-upload-input").value = "";
        }
        
        batchUploadModal.show();
    }

    const dropZone = document.getElementById("batch-upload-zone");
    const fileInput = document.getElementById("batch-upload-input");
    const previewContainer = document.getElementById("batch-upload-preview");
    const submitBtn = document.getElementById("batch-upload-submit");

    if (dropZone) {
        dropZone.addEventListener("click", () => fileInput.click());

        ["dragenter", "dragover"].forEach(e => {
            dropZone.addEventListener(e, (ev) => { ev.preventDefault(); dropZone.classList.add("dragover"); });
        });
        ["dragleave", "drop"].forEach(e => {
            dropZone.addEventListener(e, (ev) => { ev.preventDefault(); dropZone.classList.remove("dragover"); });
        });

        dropZone.addEventListener("drop", (ev) => {
            ev.preventDefault();
            const files = ev.dataTransfer.files;
            addFilesToBatch(files);
        });

        if (fileInput) {
            fileInput.addEventListener("change", (ev) => {
                addFilesToBatch(ev.target.files);
            });
        }
    }

    function addFilesToBatch(files) {
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                allFiles.push(file);
            }
        });
        updatePreview();
    }

    window.removeBatchFile = function(index) {
        allFiles.splice(index, 1);
        updatePreview();
    };

    function updatePreview() {
        if (!previewContainer) return;
        previewContainer.innerHTML = "";
        
        if (allFiles.length === 0) {
            submitBtn.disabled = true;
            submitBtn.textContent = "Upload 0 Files";
            if (fileInput) fileInput.value = "";
            return;
        }
        submitBtn.disabled = false;
        submitBtn.textContent = `Upload ${allFiles.length} File` + (allFiles.length > 1 ? "s" : "");

        const dt = new DataTransfer();
        allFiles.forEach(file => dt.items.add(file));
        if (fileInput) {
            fileInput.files = dt.files;
        }

        allFiles.forEach((file, index) => {
            const div = document.createElement("div");
            div.className = "col-4";
            div.innerHTML = `
                <div class="border rounded p-1 text-center small position-relative" style="aspect-ratio: 1/1;">
                    <img src="${URL.createObjectURL(file)}" class="img-fluid rounded h-100 w-100 object-fit-cover">
                    <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-1 p-0 d-flex align-items-center justify-content-center" style="width: 18px; height: 18px; font-size: 10px;" onclick="removeBatchFile(${index})">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            `;
            previewContainer.appendChild(div);
        });
    }

    const itemModalSelectBtn = document.getElementById('item-modal-select-btn');
    if (itemModalSelectBtn) {
        itemModalSelectBtn.addEventListener('click', function() {
            if (typeof showMediaModal === 'function') {
                let defaultFolder = currentSection === 'gallery' ? 'images/gallery' : '';
                showMediaModal(function(path, url) {
                    const displayUrl = url || (path.startsWith('/') ? path : '/' + path);
                    document.getElementById('item-modal-upload').value = '';
                    document.getElementById('item-modal-image').value = displayUrl;
                    document.getElementById('item-modal-preview').innerHTML = `<img src="${displayUrl}" class="img-fluid rounded h-100 w-100 object-fit-cover">`;
                }, false, defaultFolder);
            }
        });
    }

    const itemModalUpload = document.getElementById('item-modal-upload');
    if (itemModalUpload) {
        itemModalUpload.addEventListener('change', function(event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;

            document.getElementById('item-modal-image').value = '';

            const localUrl = URL.createObjectURL(file);
            document.getElementById('item-modal-preview').innerHTML = `<img src="${localUrl}" class="img-fluid rounded h-100 w-100 object-fit-cover">`;
        });
    }

    const albumPhotoGrid = document.getElementById('album-photo-grid');
    if (albumPhotoGrid) {
        let draggedItem = null;

        const getDragAfterElement = (container, y) => {
            const draggableItems = [...container.querySelectorAll('.album-photo-item:not(.dragging)')];

            return draggableItems.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;

                if (offset < 0 && offset > closest.offset) {
                    return { offset, element: child };
                }

                return closest;
            }, { offset: Number.NEGATIVE_INFINITY, element: null }).element;
        };

        albumPhotoGrid.querySelectorAll('.album-photo-item').forEach((item) => {
            item.addEventListener('dragstart', () => {
                draggedItem = item;
                item.classList.add('dragging');
            });

            item.addEventListener('dragend', async () => {
                item.classList.remove('dragging');

                const order = [...albumPhotoGrid.querySelectorAll('.album-photo-item')].map((node) => node.dataset.index);
                draggedItem = null;

                try {
                    const response = await fetch(albumPhotoGrid.dataset.reorderUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ order }),
                    });

                    if (!response.ok) {
                        window.location.reload();
                    } else {
                        window.location.reload();
                    }
                } catch (error) {
                    window.location.reload();
                }
            });
        });

        albumPhotoGrid.addEventListener('dragover', (event) => {
            event.preventDefault();
            if (!draggedItem) return;

            const afterElement = getDragAfterElement(albumPhotoGrid, event.clientY);
            if (afterElement == null) {
                albumPhotoGrid.appendChild(draggedItem);
            } else {
                albumPhotoGrid.insertBefore(draggedItem, afterElement);
            }
        });
    }

    // Reuse existing addSectionModal logic or keep separate? 
    // The user didn't ask to change how sections are added.
</script>

@endpush
