@php
    $isGallerySection = ($sectionSlug ?? '') === 'gallery';
    $isProjectSection = str_contains(strtolower($sectionName ?? ''), 'project');
@endphp

<div class="card mb-3 item-row border rounded-3 overflow-hidden shadow-sm" data-index="{{ $index }}">
    <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom-0 py-2">
        <span class="fw-600 small text-muted">Item #{{ $index + 1 }}</span>
        <button type="button" class="btn btn-link text-danger p-0 remove-item" title="Remove Item">
            <i class="bi bi-trash3"></i>
        </button>
    </div>
    <div class="card-body p-3">
        <div class="row g-3">
            <div class="col-md-2 text-center text-md-start">
                <label class="form-label small fw-700 d-block">
                    @if($isProjectSection)
                        Project Image
                    @else
                        {{ $layout === 'highlights' ? 'Icon' : 'Photo' }}
                    @endif
                </label>
                <div class="item-preview-container mb-2 text-center bg-light rounded d-flex align-items-center justify-content-center border" style="width: 80px; height: 80px; margin: 0 auto;">
                    @if (!empty($item['image']))
                        <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($item['image']) }}" class="item-preview img-fluid" style="max-height: 100%; object-fit: contain;">
                    @else
                        <i class="bi {{ $isProjectSection ? 'bi-kanban' : ($layout === 'highlights' ? 'bi-star' : 'bi-person') }} text-muted opacity-50 fs-2 item-placeholder"></i>
                    @endif
                </div>
                <input type="hidden" name="items[{{ $index }}][image]" class="item-image-input" value="{{ $item['image'] ?? '' }}">
                <input type="file" name="items[{{ $index }}][image_upload]" class="form-control form-control-sm" accept="image/*">
            </div>
            <div class="{{ $isGallerySection ? 'col-md-10' : 'col-md-5' }}">
                <label class="form-label small fw-700">
                    @if($isProjectSection)
                        Project Title
                    @elseif($isGallerySection)
                        Title / Caption
                    @else
                        {{ $layout === 'highlights' ? 'Title' : ($layout === 'testimonials' ? 'Name' : 'Full Name') }}
                    @endif
                </label>
                <input type="text" name="items[{{ $index }}][name]" class="form-control form-control-sm rounded-2" value="{{ $item['name'] ?? '' }}" placeholder="Enter text...">
            </div>
            @unless($isGallerySection)
                <div class="col-md-5">
                    <label class="form-label small fw-700">
                        @if($isProjectSection)
                            Subtitle / Status
                        @else
                            {{ $layout === 'grid' ? 'Role / Position' : ($layout === 'testimonials' ? 'Position' : 'Tag / Subtitle') }}
                        @endif
                    </label>
                    <input type="text" name="items[{{ $index }}][role]" class="form-control form-control-sm rounded-2" value="{{ $item['role'] ?? '' }}" placeholder="Enter text...">
                </div>
            @endunless
            <div class="col-12">
                <label class="form-label small fw-700">
                    @if($isProjectSection)
                        Project Details
                    @else
                        {{ $layout === 'testimonials' ? 'Testimonial Quote' : 'Description' }}
                    @endif
                </label>
                <textarea name="items[{{ $index }}][description]" class="form-control form-control-sm rounded-2 @if($isProjectSection) quill-editor @endif" rows="2" placeholder="Enter content...">{{ $item['description'] ?? '' }}</textarea>
            </div>
        </div>
    </div>
</div>
