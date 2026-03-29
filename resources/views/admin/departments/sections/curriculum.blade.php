<input type="hidden" name="_curriculum_edit" value="1">
{{-- CURRICULUM EDITOR --}}
<div class="col-12 mb-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Curriculum (Sample Courses)</h4>
    </div>
    <p class="text-muted small">Update the curriculum section title and introductory details shown before the saved curriculum categories.</p>

    <div class="mb-3">
        <label for="curriculum_title" class="form-label">Section Title</label>
        <input
            type="text"
            name="curriculum_title"
            id="curriculum_title"
            class="form-control"
            value="{{ old('curriculum_title', $department->curriculum_title ?? 'Sample Courses') }}"
            placeholder="e.g., Sample Courses">
    </div>

    <div class="mb-4">
        <label for="curriculum_body" class="form-label">Section Details</label>
        <textarea
            name="curriculum_body"
            id="curriculum_body"
            class="form-control quill-editor"
            rows="5"
            placeholder="Add a short description for the curriculum section...">{{ old('curriculum_body', $department->curriculum_body ?? '') }}</textarea>
    </div>
</div>
