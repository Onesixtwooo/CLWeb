@if ($editMode !== 'add_facility' && $editMode !== 'edit_facility')
    <input type="hidden" name="_facilities_edit" value="1">
    {{-- FACILITIES SECTION DETAILS --}}
    <div class="col-12 mb-3">
        <label for="title" class="form-label">Facilities Section Title</label>
        <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $content['title'] ?? 'Facilities') }}" placeholder="e.g., Facilities">
    </div>

    <div class="col-12 mb-3">
        <label for="body" class="form-label">Introductory Text</label>
        <textarea name="body" id="body" class="form-control quill-editor" rows="4" placeholder="Brief introduction about your department's facilities...">{{ old('body', $content['body'] ?? '') }}</textarea>
    </div>

    <div class="col-12 mb-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
        </div>
    </div>

@elseif ($editMode === 'add_facility' || $editMode === 'edit_facility')
    @if ($editMode === 'add_facility')
        <input type="hidden" name="_add_facility_edit" value="1">
    @elseif ($editMode === 'edit_facility')
        <input type="hidden" name="_edit_facility_edit" value="1">
        <input type="hidden" name="facility_id" value="{{ $facility->id ?? '' }}">
    @endif
    {{-- FACILITY FORM --}}
    <div class="col-12">
        <h4 class="fw-bold mb-3">{{ $editMode === 'add_facility' ? 'Add New Facility' : 'Edit Facility: ' . ($facility->title ?? '') }}</h4>
        <div class="card border">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Facility Title</label>
                            <input type="text" name="title" class="form-control" value="{{ $editMode === 'edit_facility' ? ($facility->title ?? '') : '' }}" required placeholder="e.g., Computer Laboratory 1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description (Optional)</label>
                            <textarea name="description" class="form-control quill-editor" rows="3" placeholder="Brief description of the facility...">{{ $editMode === 'edit_facility' ? ($facility->description ?? '') : '' }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Facility Image (Optional)</label>
                        @if($editMode === 'edit_facility' && !empty($facility->image))
                            <div class="mb-2">
                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($facility->image) }}" alt="Facility Image" class="img-fluid rounded border" style="max-height: 150px; width: 100%; object-fit: cover;">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                        <small class="text-muted">Recommended: 16:9 aspect ratio</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endif
