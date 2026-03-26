<input type="hidden" name="_extension_edit" value="1">
{{-- EXTENSION EDITOR --}}
<div class="col-12 mb-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? false) ? 'checked' : '' }}>
        <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
    </div>
    <small class="text-muted">Toggle to show or hide this section on the department page.</small>
</div>

<div class="col-12 mt-4">
    <h4 class="fw-bold mb-3">Extension Activities</h4>
    <p class="text-muted small">Manage extension programs and community engagement activities.</p>
    
    @php
        $extension = $content['items'] ?? [];
    @endphp

    <div id="extension-container">
        @forelse($extension as $index => $item)
            <div class="card mb-4 extension-item border">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Extension #{{ $index + 1 }}</span>
                    <button type="button" class="btn btn-danger btn-sm remove-extension-btn">&times; Remove</button>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Activity Title</label>
                                <input type="text" name="extension[{{ $index }}][title]" class="form-control" value="{{ $item['title'] ?? '' }}" placeholder="e.g., Community Livelihood Program">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="extension[{{ $index }}][description]" class="form-control quill-editor" rows="3" placeholder="Brief description of the activity...">{{ $item['description'] ?? '' }}</textarea>
                                <input type="hidden" name="extension[{{ $index }}][created_at]" value="{{ $item['created_at'] ?? now() }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Featured Image</label>
                            @if(!empty($item['image']))
                                <div class="mb-2">
                                    <img src="{{ asset($item['image']) }}" alt="Extension Image" class="img-fluid rounded border" style="max-height: 120px; width: 100%; object-fit: cover;">
                                    <input type="hidden" name="extension[{{ $index }}][existing_image]" value="{{ $item['image'] }}">
                                </div>
                            @endif
                            <input type="file" name="extension[{{ $index }}][image]" class="form-control form-control-sm" accept="image/*">
                            <small class="text-muted">Recommended: 16:9 aspect ratio</small>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty state handled by JS --}}
        @endforelse
    </div>

    <button type="button" id="add-extension-btn" class="btn btn-outline-primary">
        + Add New Extension Activity
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('extension-container');
        const addBtn = document.getElementById('add-extension-btn');
        const existingItems = container.querySelectorAll('.extension-item');
        let extensionIndex = existingItems.length > 0 ? existingItems.length : 0;

        addBtn.addEventListener('click', function() {
            const template = `
                <div class="card mb-4 extension-item border">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <span class="fw-bold">New Extension Activity</span>
                        <button type="button" class="btn btn-danger btn-sm remove-extension-btn">&times; Remove</button>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Activity Title</label>
                                    <input type="text" name="extension[${extensionIndex}][title]" class="form-control" placeholder="e.g., Community Livelihood Program">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="extension[${extensionIndex}][description]" class="form-control quill-editor" rows="3" placeholder="Brief description of the activity..."></textarea>
                                    <input type="hidden" name="extension[${extensionIndex}][created_at]" value="{{ now() }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Featured Image</label>
                                <input type="file" name="extension[${extensionIndex}][image]" class="form-control form-control-sm" accept="image/*">
                                <small class="text-muted">Recommended: 16:9 aspect ratio</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
            extensionIndex++;
        });

        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-extension-btn')) {
                if (confirm('Remove this extension activity?')) {
                    e.target.closest('.extension-item').remove();
                }
            }
        });
    });
</script>

@if(request()->get('action') === 'add')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const addBtn = document.getElementById('add-extension-btn');
            if(addBtn) {
                addBtn.click();
                window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
            }
        }, 200);
    });
</script>
@endif
