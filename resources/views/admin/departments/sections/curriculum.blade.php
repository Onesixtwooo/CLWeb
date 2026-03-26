<input type="hidden" name="_curriculum_edit" value="1">
{{-- CURRICULUM EDITOR --}}
<div class="col-12 mb-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Curriculum (Sample Courses)</h4>
    </div>
    <p class="text-muted small">Add categories (e.g., "Core Programming") and list courses one per line.</p>
    
    @php
        $curriculum = $department->curricula ?? [];
    @endphp

    <div id="curriculum-container">
        @forelse($curriculum as $index => $category)
            <div class="card mb-3 curriculum-item">
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-2">
                        <button type="button" class="btn btn-danger btn-sm remove-curriculum-btn">&times; Remove</button>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category Title</label>
                        <input type="text" name="curriculum[{{ $index }}][title]" class="form-control" value="{{ $category['title'] ?? '' }}" placeholder="e.g., Core Programming">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Courses</label>
                        <textarea name="curriculum[{{ $index }}][courses]" class="form-control quill-editor" rows="10" placeholder="List courses here...">{{ is_array($category['courses']) ? implode("\n", $category['courses']) : ($category['courses'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty state --}}
        @endforelse
    </div>

    <button type="button" id="add-curriculum-btn" class="btn btn-outline-primary btn-sm">
        + Add Curriculum Category
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('curriculum-container');
        const addBtn = document.getElementById('add-curriculum-btn');
        let activeIndex = {{ count($curriculum ?? []) }};

        if (addBtn) {
            addBtn.addEventListener('click', function() {
                const template = `
                    <div class="card mb-3 curriculum-item">
                        <div class="card-body">
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-danger btn-sm remove-curriculum-btn">&times; Remove</button>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Category Title</label>
                                <input type="text" name="curriculum[${activeIndex}][title]" class="form-control" placeholder="e.g., Core Programming">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Courses</label>
                                <textarea name="curriculum[${activeIndex}][courses]" class="form-control quill-editor" rows="10" placeholder="List courses here..."></textarea>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', template);
                activeIndex++;
            });
        }

        if (container) {
            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-curriculum-btn')) {
                    if (confirm('Remove this category?')) {
                        e.target.closest('.curriculum-item').remove();
                    }
                }
            });
        }
    });
</script>
