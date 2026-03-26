<input type="hidden" name="_training_edit" value="1">
{{-- TRAINING EDITOR --}}
<div class="col-12 mb-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? false) ? 'checked' : '' }}>
        <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
    </div>
    <small class="text-muted">Toggle to show or hide this section on the department page.</small>
</div>

<div class="col-12 mt-4">
    <h4 class="fw-bold mb-3">Training & Workshops</h4>
    <p class="text-muted small">Manage training programs and workshops offered.</p>
    
    @php
        $training = $content['items'] ?? [];
    @endphp

    <div id="training-container">
        @forelse($training as $index => $item)
            <div class="card mb-4 training-item border">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Training #{{ $index + 1 }}</span>
                    <button type="button" class="btn btn-danger btn-sm remove-training-btn">&times; Remove</button>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Training Title</label>
                                <input type="text" name="training[{{ $index }}][title]" class="form-control" value="{{ $item['title'] ?? '' }}" placeholder="e.g., Advanced Web Development Workshop">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="training[{{ $index }}][description]" class="form-control quill-editor" rows="3" placeholder="Brief description of the training...">{{ $item['description'] ?? '' }}</textarea>
                                <input type="hidden" name="training[{{ $index }}][created_at]" value="{{ $item['created_at'] ?? now() }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Featured Image</label>
                            @if(!empty($item['image']))
                                <div class="mb-2">
                                    <img src="{{ asset($item['image']) }}" alt="Training Image" class="img-fluid rounded border" style="max-height: 120px; width: 100%; object-fit: cover;">
                                    <input type="hidden" name="training[{{ $index }}][existing_image]" value="{{ $item['image'] }}">
                                </div>
                            @endif
                            <input type="file" name="training[{{ $index }}][image]" class="form-control form-control-sm" accept="image/*">
                            <small class="text-muted">Recommended: 16:9 aspect ratio</small>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty state handled by JS --}}
        @endforelse
    </div>

    <button type="button" id="add-training-btn" class="btn btn-outline-primary">
        + Add New Training
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('training-container');
        const addBtn = document.getElementById('add-training-btn');
        const existingItems = container.querySelectorAll('.training-item');
        let trainingIndex = existingItems.length > 0 ? existingItems.length : 0;

        addBtn.addEventListener('click', function() {
            const template = `
                <div class="card mb-4 training-item border">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <span class="fw-bold">New Training</span>
                        <button type="button" class="btn btn-danger btn-sm remove-training-btn">&times; Remove</button>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Training Title</label>
                                    <input type="text" name="training[${trainingIndex}][title]" class="form-control" placeholder="e.g., Advanced Web Development Workshop">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="training[${trainingIndex}][description]" class="form-control quill-editor" rows="3" placeholder="Brief description of the training..."></textarea>
                                    <input type="hidden" name="training[${trainingIndex}][created_at]" value="{{ now() }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Featured Image</label>
                                <input type="file" name="training[${trainingIndex}][image]" class="form-control form-control-sm" accept="image/*">
                                <small class="text-muted">Recommended: 16:9 aspect ratio</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
            trainingIndex++;
        });

        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-training-btn')) {
                if (confirm('Remove this training program?')) {
                    e.target.closest('.training-item').remove();
                }
            }
        });
    });
</script>

@if(request()->get('action') === 'add')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const addBtn = document.getElementById('add-training-btn');
            if(addBtn) {
                addBtn.click();
                window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
            }
        }, 200);
    });
</script>
@endif
