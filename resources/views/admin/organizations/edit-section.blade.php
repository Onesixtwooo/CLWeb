@extends('admin.layout')

@section('title', "Edit {$sectionName} - {$organization->name}")

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12" style="background: #e3f2fd; padding: 2rem; border-radius: 15px; border-left: 5px solid #0d47a1;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 fw-800" style="color: #0d1b3e;">Edit {{ $sectionName }}</h1>
                    <p class="text-muted mb-0">{{ $organization->name }} — {{ $collegeName }}</p>
                </div>
                <a href="{{ route('admin.organizations.show-section', ['college' => $collegeSlug, 'organization' => $organization, 'section' => $sectionSlug]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    Back to Organization
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <form action="{{ route('admin.organizations.update-section', ['organization' => $organization, 'section' => $sectionSlug]) }}" method="POST" id="section-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-md-8">
                        <label for="title" class="form-label fw-700">Section Title</label>
                        <input type="text" name="title" id="title" class="form-control rounded-3" value="{{ old('title', $content['title'] ?? $sectionName) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="layout" class="form-label fw-700">Display Layout</label>
                        <select name="layout" id="layout-selector" class="form-select rounded-3">
                            <option value="grid" {{ ($content['layout'] ?? '') === 'grid' ? 'selected' : '' }}>Card Grid Layout</option>
                            <option value="split" {{ ($content['layout'] ?? '') === 'split' ? 'selected' : '' }}>Two-Column Split Layout</option>
                            <option value="testimonials" {{ ($content['layout'] ?? '') === 'testimonials' ? 'selected' : '' }}>Testimonial Card Layout</option>
                            <option value="highlights" {{ ($content['layout'] ?? '') === 'highlights' ? 'selected' : '' }}>Feature List / Highlight Section</option>
                        </select>
                    </div>

                    {{-- Section Layout Containers --}}
                    <div id="layout-fields-container" class="col-12 mt-3">
                        {{-- Items-based layouts (Grid, Testimonials, Highlights) --}}
                        <div id="items-layout-section" class="layout-group" style="display: none;">
                            <h5 class="fw-700 mb-3 px-1 text-secondary text-uppercase small ls-wide" id="items-title">Items List</h5>
                            <div id="items-container" class="row g-3">
                                @if (isset($content['items']) && is_array($content['items']))
                                    @foreach ($content['items'] as $index => $item)
                                        @include('admin.organizations.partials.item-row', [
                                            'index' => $index,
                                            'item' => $item,
                                            'layout' => $content['layout'] ?? 'grid',
                                            'sectionName' => $sectionName
                                        ])
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" id="add-item" class="btn btn-outline-primary btn-sm rounded-pill px-4 mt-3 shadow-sm">
                                <i class="bi bi-plus-lg me-1"></i> Add <span id="add-item-label">
                                    {{ str_contains(strtolower($sectionName ?? ''), 'project') ? 'Project' : 'Item' }}
                                </span>
                            </button>
                        </div>

                        {{-- Split layout (Body + Image) --}}
                        <div id="split-layout-section" class="layout-group" style="display: none;">
                            <div class="row g-4">
                                <div class="col-md-8">
                                    <label for="body" class="form-label fw-700">Content Description</label>
                                    <textarea name="body" id="body" rows="10" class="form-control quill-editor rounded-3">{{ old('body', $content['body'] ?? '') }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-700">Featured Image</label>
                                    <div class="item-preview-container mb-2 text-center bg-light rounded d-flex align-items-center justify-content-center border" style="width: 100%; height: 250px;" id="split-image-preview">
                                        @if (!empty($content['image']))
                                            <img src="{{ str_starts_with($content['image'], 'http') || str_starts_with($content['image'], '/') || str_starts_with($content['image'], 'media/') ? asset($content['image']) : asset('/storage/' . $content['image']) }}" class="img-fluid rounded shadow-sm" style="max-height: 100%; object-fit: contain;">
                                        @else
                                            <i class="bi bi-image text-muted opacity-50 fs-1"></i>
                                        @endif
                                    </div>
                                    <input type="hidden" name="image" id="split-image-input" value="{{ $content['image'] ?? '' }}">
                                    <input type="file" name="image_upload" id="split-image-upload" class="form-control rounded-pill px-3 shadow-sm" accept="image/*">
                                </div>
                            </div>
                        </div>

                        {{-- Default Body layout (used for split fallback or simple text) --}}
                        <div id="body-layout-section" class="layout-group" style="display: none;">
                            <label for="body_standalone" class="form-label fw-700">Section Content</label>
                            <textarea name="body_alt" id="body_alt" rows="10" class="form-control quill-editor rounded-3">{{ old('body', $content['body'] ?? '') }}</textarea>
                        </div>
                    </div>

                    {{-- Link faculty adviser specifically if it's the officers section --}}
                    @if ($sectionSlug === 'officers')
                        <div class="col-12 mt-4 p-4 bg-light rounded-4 border border-light-subtle shadow-sm">
                            <label for="adviser" class="form-label fw-800 mb-2">
                                <i class="bi bi-person-badge-fill me-2 text-primary"></i>Faculty Adviser
                            </label>
                            <select name="adviser" id="adviser" class="form-select rounded-3 border-secondary-subtle py-2 shadow-sm">
                                <option value="">-- No Adviser Selected --</option>
                                @foreach($faculty as $f)
                                    <option value="{{ $f->name }}" {{ old('adviser', $organization->adviser) === $f->name ? 'selected' : '' }}>
                                        {{ $f->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-12 mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-admin-primary px-5 rounded-pill shadow-sm">Save {{ $sectionName }}</button>
                        <a href="{{ route('admin.organizations.show-section', ['college' => $collegeSlug, 'organization' => $organization, 'section' => $sectionSlug]) }}" class="btn btn-light px-4 rounded-pill ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Templates for JS --}}
@if ($sectionSlug === 'officers')
<template id="item-template">
    <div class="card mb-3 item-row border rounded-3 overflow-hidden shadow-sm" data-index="__INDEX__">
        <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom-0 py-2">
            <span class="fw-600 small text-muted">Member #__NUMBER__</span>
            <button type="button" class="btn btn-link text-danger p-0 remove-item" title="Remove Member">
                <i class="bi bi-trash3"></i>
            </button>
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-2 text-center text-md-start">
                    <label class="form-label small fw-700 d-block">Photo</label>
                    <div class="item-preview-container mb-2 text-center bg-light rounded d-flex align-items-center justify-content-center border" style="width: 80px; height: 80px; margin: 0 auto;">
                        <i class="bi bi-person text-muted opacity-50 fs-2 item-placeholder"></i>
                    </div>
                    <input type="hidden" name="items[__INDEX__][image]" class="item-image-input">
                    <button type="button" class="btn btn-outline-primary btn-xs select-media w-100" style="font-size: 0.75rem;">Select</button>
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-700">Full Name</label>
                    <input type="text" name="items[__INDEX__][name]" class="form-control form-control-sm rounded-2" placeholder="Name">
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-700">Role / Position</label>
                    <input type="text" name="items[__INDEX__][role]" class="form-control form-control-sm rounded-2" placeholder="e.g. President">
                </div>
                <div class="col-12">
                    <label class="form-label small fw-700">Short Bio / Description (Optional)</label>
                    <textarea name="items[__INDEX__][description]" class="form-control form-control-sm rounded-2" rows="2" placeholder="Brief info about the member..."></textarea>
                </div>
            </div>
        </div>
    </div>
</template>
@elseif ($sectionSlug === 'activities')
<template id="item-template">
    <div class="card mb-3 item-row border rounded-3 overflow-hidden shadow-sm" data-index="__INDEX__">
        <div class="card-header bg-light d-flex justify-content-between align-items-center border-bottom-0 py-2">
            <span class="fw-600 small text-muted">Activity #__NUMBER__</span>
            <button type="button" class="btn btn-link text-danger p-0 remove-item" title="Remove Activity">
                <i class="bi bi-trash3"></i>
            </button>
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-700 d-block">Image</label>
                    <div class="item-preview-container mb-2 text-center bg-light rounded d-flex align-items-center justify-content-center border" style="width: 100%; height: 120px;">
                        <i class="bi bi-image text-muted opacity-50 fs-1 item-placeholder"></i>
                    </div>
                    <input type="hidden" name="items[__INDEX__][image]" class="item-image-input">
                    <button type="button" class="btn btn-outline-primary btn-xs select-media w-100" style="font-size: 0.75rem;">Select Image</button>
                </div>
                <div class="col-md-9">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-700">Activity Title</label>
                            <input type="text" name="items[__INDEX__][title]" class="form-control form-control-sm rounded-2" placeholder="Event title">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-700">Date</label>
                            <input type="text" name="items[__INDEX__][date]" class="form-control form-control-sm rounded-2" placeholder="e.g. Oct 2024">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-700">Description</label>
                            <textarea name="items[__INDEX__][description]" class="form-control form-control-sm rounded-2" rows="3" placeholder="Brief description of the activity..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
@elseif ($sectionSlug === 'gallery')
<template id="item-template">
    <div class="col-md-3 item-row" data-index="__INDEX__">
        <div class="card border rounded-3 overflow-hidden shadow-sm h-100">
            <div class="position-relative">
                <div class="item-preview-container text-center bg-light d-flex align-items-center justify-content-center border-bottom" style="aspect-ratio: 1/1;">
                    <i class="bi bi-image text-muted opacity-50 fs-1 item-placeholder"></i>
                </div>
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 remove-item" style="padding: 0px 5px;" title="Remove Photo">
                    &times;
                </button>
            </div>
            <div class="card-body p-2">
                <input type="hidden" name="items[__INDEX__][image]" class="item-image-input">
                <button type="button" class="btn btn-outline-primary btn-xs select-media w-100 mb-2" style="font-size: 0.75rem;">Select Photo</button>
                <input type="text" name="items[__INDEX__][caption]" class="form-control form-control-sm rounded-2" placeholder="Caption">
            </div>
        </div>
    </div>
</template>
@endif

<!-- Media Library Modal -->
@include('includes.media-modal')

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const layoutSelector = document.getElementById('layout-selector');
        const layoutGroups = document.querySelectorAll('.layout-group');
        const itemsContainer = document.getElementById('items-container');
        const addBtn = document.getElementById('add-item');
        const addItemLabel = document.getElementById('add-item-label');
        const sectionName = "{{ $sectionName }}".toLowerCase();
        const sectionSlug = "{{ $sectionSlug }}";
        const isProject = sectionName.includes('project');
        const isGallerySection = sectionSlug === 'gallery';

        // Layout Switcher
        function updateLayout() {
            const layout = layoutSelector.value;
            layoutGroups.forEach(group => group.style.display = 'none');
            
            if (layout === 'split') {
                document.getElementById('split-layout-section').style.display = 'block';
            } else if (layout === 'grid' || layout === 'testimonials' || layout === 'highlights') {
                document.getElementById('items-layout-section').style.display = 'block';
                
                // Update labels
                let label = 'Item';
                if (isProject) label = 'Project';
                else if (layout === 'testimonials') label = 'Testimonial';
                else if (layout === 'highlights') label = 'Feature';
                
                addItemLabel.textContent = label;
            } else {
                document.getElementById('body-layout-section').style.display = 'block';
            }
        }

        layoutSelector.addEventListener('change', updateLayout);
        updateLayout();

        // Items Logic
        if (addBtn && itemsContainer) {
            let index = itemsContainer.querySelectorAll('.item-row').length;
            
            addBtn.addEventListener('click', function() {
                const layout = layoutSelector.value;
                const number = index + 1;
                
                const html = `
                    <div class="card mb-3 item-row border rounded-3 overflow-hidden shadow-sm" data-index="${index}">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom-0 py-2">
                            <span class="fw-600 small text-muted">${isProject ? 'Project' : 'Item'} #${number}</span>
                            <button type="button" class="btn btn-link text-danger p-0 remove-item" title="Remove Item">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-2 text-center text-md-start">
                                    <label class="form-label small fw-700 d-block">${isProject ? 'Project Image' : (layout === 'highlights' ? 'Icon' : 'Photo')}</label>
                                    <div class="item-preview-container mb-2 text-center bg-light rounded d-flex align-items-center justify-content-center border" style="width: 80px; height: 80px; margin: 0 auto;">
                                        <i class="bi ${isProject ? 'bi-kanban' : (layout === 'highlights' ? 'bi-star' : 'bi-person')} text-muted opacity-50 fs-2 item-placeholder"></i>
                                    </div>
                                    <input type="hidden" name="items[${index}][image]" class="item-image-input">
                                    <button type="button" class="btn btn-outline-primary btn-xs select-media w-100" style="font-size: 0.75rem;">Select</button>
                                </div>
                                <div class="${isGallerySection ? 'col-md-10' : 'col-md-5'}">
                                    <label class="form-label small fw-700">${isProject ? 'Project Title' : (isGallerySection ? 'Title / Caption' : (layout === 'highlights' ? 'Title' : (layout === 'testimonials' ? 'Name' : 'Full Name')))}</label>
                                    <input type="text" name="items[${index}][name]" class="form-control form-control-sm rounded-2" placeholder="Enter text...">
                                </div>
                                ${isGallerySection ? '' : `
                                <div class="col-md-5">
                                    <label class="form-label small fw-700">${isProject ? 'Subtitle / Status' : (layout === 'grid' ? 'Role / Position' : (layout === 'testimonials' ? 'Position' : 'Tag / Subtitle'))}</label>
                                    <input type="text" name="items[${index}][role]" class="form-control form-control-sm rounded-2" placeholder="Enter text...">
                                </div>
                                `}
                                <div class="col-12">
                                    <label class="form-label small fw-700">${isProject ? 'Project Details' : (layout === 'testimonials' ? 'Testimonial Quote' : 'Description')}</label>
                                    <textarea name="items[${index}][description]" class="form-control form-control-sm rounded-2 ${isProject ? 'quill-editor' : ''}" rows="2" placeholder="Enter content..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html.trim();
                const newNode = tempDiv.firstChild;
                itemsContainer.appendChild(newNode);
                index++;
            });
        }

        // Split Layout Image logic
        const splitImageUpload = document.getElementById('split-image-upload');
        const splitImagePreview = document.getElementById('split-image-preview');
        const splitImageInput = document.getElementById('split-image-input');

        if (splitImageUpload) {
            splitImageUpload.addEventListener('change', function(event) {
                const file = event.target.files && event.target.files[0];
                if (!file) return;

                // Clear hidden input if a new file is uploaded
                if (splitImageInput) splitImageInput.value = '';

                const localUrl = URL.createObjectURL(file);
                if (splitImagePreview) {
                    splitImagePreview.innerHTML = `<img src="${localUrl}" class="img-fluid rounded shadow-sm" style="max-height: 100%; object-fit: contain;">`;
                }
            });
        }

        // Shared Logic for item actions
        if (itemsContainer) {
            itemsContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-item')) {
                    if (confirm('Are you sure you want to remove this item?')) {
                        e.target.closest('.item-row').remove();
                    }
                }
                
                if (e.target.closest('.select-media')) {
                    const row = e.target.closest('.item-row');
                    const input = row.querySelector('.item-image-input');
                    const preview = row.querySelector('.item-preview-container');
                    
                    if (typeof showMediaModal === 'function') {
                        showMediaModal(function(path, url) {
                            const displayUrl = url || (path.startsWith('/') ? path : '/' + path);
                            input.value = displayUrl;
                            preview.innerHTML = `<img src="${displayUrl}" class="item-preview img-fluid h-100 w-100 object-fit-cover shadow-sm rounded">`;
                        });
                    }
                }
            });
        }
    });
</script>
@endpush
