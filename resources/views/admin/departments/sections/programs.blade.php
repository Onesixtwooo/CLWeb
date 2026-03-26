<input type="hidden" name="_programs_edit" value="1">
{{-- PROGRAMS EDITOR --}}
<div class="col-12 mb-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($department->programs_is_visible ?? true) ? 'checked' : '' }}>
        <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
    </div>
    <small class="text-muted">Toggle to show or hide this section on the department page.</small>
</div>

<div class="col-12 mt-4">
    <h4 class="fw-bold mb-3">Programs</h4>
    <p class="text-muted small">Manage academic programs offered by the department.</p>
    
    @php
        $programs = $department->programs ?? [];
    @endphp

    <div id="programs-container">
        @forelse($programs as $index => $program)
            <div class="card mb-4 program-item border">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Program #{{ $index + 1 }}</span>
                    <button type="button" class="btn btn-danger btn-sm remove-program-btn">&times; Remove</button>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Program Title</label>
                                <input type="text" name="programs[{{ $index }}][title]" class="form-control" value="{{ $program->title ?? '' }}" placeholder="e.g., Bachelor of Science in Information Technology">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="programs[{{ $index }}][description]" class="form-control quill-editor" rows="3" placeholder="Brief description of the program...">{{ $program->description ?? '' }}</textarea>
                                <input type="hidden" name="programs[{{ $index }}][created_at]" value="{{ $program->created_at ?? now() }}">
                            </div>


                            <div class="mb-3">
                                <label class="form-label fw-bold">Additional Numbered Items</label>
                                <div class="numbered-items-container" data-program-index="{{ $index }}">
                                    @if(!empty($program->numbered_content) && is_array($program->numbered_content))
                                        @foreach($program->numbered_content as $nIndex => $content)
                                            <div class="input-group mb-2 numbered-item">
                                                <span class="input-group-text">Label</span>
                                                <input type="text" name="programs[{{ $index }}][numbered_content][{{ $nIndex }}][label]" class="form-control" style="max-width: 100px;" value="{{ $content['label'] ?? '' }}" placeholder="e.g. V">
                                                <span class="input-group-text">Text</span>
                                                <input type="text" name="programs[{{ $index }}][numbered_content][{{ $nIndex }}][text]" class="form-control" value="{{ $content['text'] ?? '' }}" placeholder="Content...">
                                                <button type="button" class="btn btn-outline-danger remove-numbered-item">&times;</button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary add-numbered-item-btn">+ Add Item</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty state handled by JS adding --}}
        @endforelse
    </div>

    <button type="button" id="add-program-btn" class="btn btn-outline-primary">
        + Add New Program
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('programs-container');
    const addBtn = document.getElementById('add-program-btn');
    let programIndex = {{ count($programs) }};



    // Validates if clicked element is part of a dynamically added program
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-program-btn')) {
            if(confirm('Are you sure you want to remove this program?')) {
                e.target.closest('.program-item').remove();
            }
        }

        // Handle adding numbered items
        if (e.target && e.target.classList.contains('add-numbered-item-btn')) {
            const container = e.target.previousElementSibling;
            const programIndex = container.getAttribute('data-program-index');
            // Calculate new index based on current count
            const newIndex = container.querySelectorAll('.numbered-item').length;
            
            const template = `
                <div class="input-group mb-2 numbered-item">
                    <span class="input-group-text">Label</span>
                    <input type="text" name="programs[${programIndex}][numbered_content][${newIndex}][label]" class="form-control" style="max-width: 100px;" placeholder="e.g. V">
                    <span class="input-group-text">Text</span>
                    <input type="text" name="programs[${programIndex}][numbered_content][${newIndex}][text]" class="form-control" placeholder="Content...">
                    <button type="button" class="btn btn-outline-danger remove-numbered-item">&times;</button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
        }

        // Handle removing numbered items
        if (e.target && e.target.classList.contains('remove-numbered-item')) {
            e.target.closest('.numbered-item').remove();
        }
    });

    addBtn.addEventListener('click', function() {
        const template = `
            <div class="card mb-4 program-item border">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span class="fw-bold">New Program</span>
                    <button type="button" class="btn btn-danger btn-sm remove-program-btn">&times; Remove</button>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Program Title</label>
                                <input type="text" name="programs[${programIndex}][title]" class="form-control" placeholder="e.g., Bachelor of Science in Information Technology">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="programs[${programIndex}][description]" class="form-control quill-editor" rows="3" placeholder="Brief description of the program..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Additional Numbered Items</label>
                                <div class="numbered-items-container" data-program-index="${programIndex}">
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary add-numbered-item-btn">+ Add Item</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', template);
        programIndex++;
    });
});
</script>
