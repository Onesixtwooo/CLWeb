<input type="hidden" name="_awards_edit" value="1">
{{-- AWARDS EDITOR --}}
<div class="col-12 mb-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? true) ? 'checked' : '' }}>
        <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
    </div>
    <small class="text-muted">Toggle to show or hide this section on the department page.</small>
</div>

<div class="col-12 mt-4">
    <h4 class="fw-bold mb-3">Student & Faculty Awards</h4>
    <p class="text-muted small">Manage awards and recognition for the department.</p>
    
    @php
        $awards = $content['items'] ?? [];
    @endphp

    <div id="awards-container">
        @forelse($awards as $index => $award)
            <div class="card mb-4 award-item border">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Award #{{ $index + 1 }}</span>
                    <button type="button" class="btn btn-danger btn-sm remove-award-btn">&times; Remove</button>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Award Title</label>
                                <input type="text" name="awards[{{ $index }}][title]" class="form-control" value="{{ $award['title'] ?? '' }}" placeholder="e.g., Best Capstone Project">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="awards[{{ $index }}][description]" class="form-control quill-editor" rows="3" placeholder="Brief description of the award...">{{ $award['description'] ?? '' }}</textarea>
                                <input type="hidden" name="awards[{{ $index }}][created_at]" value="{{ $award['created_at'] ?? now() }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Award Image</label>
                            @if(!empty($award['image']))
                                <div class="mb-2">
                                    <img src="{{ asset($award['image']) }}" alt="Award Image" class="img-fluid rounded border" style="max-height: 120px; width: 100%; object-fit: cover;">
                                    <input type="hidden" name="awards[{{ $index }}][existing_image]" value="{{ $award['image'] }}">
                                </div>
                            @endif
                            <input type="file" name="awards[{{ $index }}][image]" class="form-control form-control-sm" accept="image/*">
                            <small class="text-muted">Recommended: 16:9 aspect ratio</small>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty state handled by JS adding --}}
        @endforelse
    </div>

    <button type="button" id="add-award-btn" class="btn btn-outline-primary">
        + Add New Award
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('awards-container');
        const addBtn = document.getElementById('add-award-btn');
        // Calculate next index based on existing items
        const existingItems = container.querySelectorAll('.award-item');
        let awardIndex = existingItems.length > 0 ? existingItems.length : 0;

        addBtn.addEventListener('click', function() {
            const template = `
                <div class="card mb-4 award-item border">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <span class="fw-bold">New Award</span>
                        <button type="button" class="btn btn-danger btn-sm remove-award-btn">&times; Remove</button>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Award Title</label>
                                    <input type="text" name="awards[${awardIndex}][title]" class="form-control" placeholder="e.g., Best Capstone Project">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="awards[${awardIndex}][description]" class="form-control quill-editor" rows="3" placeholder="Brief description of the award..."></textarea>
                                    <input type="hidden" name="awards[${awardIndex}][created_at]" value="{{ now() }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Award Image</label>
                                <input type="file" name="awards[${awardIndex}][image]" class="form-control form-control-sm" accept="image/*">
                                <small class="text-muted">Recommended: 16:9 aspect ratio</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
            awardIndex++;
        });

        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-award-btn')) {
                if (confirm('Remove this award?')) {
                    e.target.closest('.award-item').remove();
                }
            }
        });
    });
</script>

@if(request()->get('action') === 'add')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const addBtn = document.getElementById('add-award-btn');
            if(addBtn) {
                addBtn.click();
                window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
            }
        }, 200);
    });
</script>
@endif
