<input type="hidden" name="_research_edit" value="1">
{{-- RESEARCH EDITOR --}}
<div class="col-12 mb-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? true) ? 'checked' : '' }}>
        <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
    </div>
    <small class="text-muted">Toggle to show or hide this section on the department page.</small>
</div>

<div class="col-12 mt-4">
    <h4 class="fw-bold mb-3">Research Requests & Projects</h4>
    <p class="text-muted small">Manage research initiatives and featured projects.</p>
    
    @php
        $research = $content['items'] ?? [];
    @endphp

    <div id="research-container">
        @forelse($research as $index => $item)
            <div class="card mb-4 research-item border">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Research #{{ $index + 1 }}</span>
                    <button type="button" class="btn btn-danger btn-sm remove-research-btn">&times; Remove</button>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Research Title</label>
                                <input type="text" name="research[{{ $index }}][title]" class="form-control" value="{{ $item['title'] ?? '' }}" placeholder="e.g., Sustainable Agriculture">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="research[{{ $index }}][description]" class="form-control quill-editor" rows="3" placeholder="Brief description of the research...">{{ $item['description'] ?? '' }}</textarea>
                                <input type="hidden" name="research[{{ $index }}][created_at]" value="{{ $item['created_at'] ?? now() }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Featured Image</label>
                            @if(!empty($item['image']))
                                <div class="mb-2">
                                    <img src="{{ asset($item['image']) }}" alt="Research Image" class="img-fluid rounded border" style="max-height: 120px; width: 100%; object-fit: cover;">
                                    <input type="hidden" name="research[{{ $index }}][existing_image]" value="{{ $item['image'] }}">
                                </div>
                            @endif
                            <input type="file" name="research[{{ $index }}][image]" class="form-control form-control-sm" accept="image/*">
                            <small class="text-muted">Recommended: 16:9 aspect ratio</small>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty state handled by JS --}}
        @endforelse
    </div>

    <button type="button" id="add-research-btn" class="btn btn-outline-primary">
        + Add New Research
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('research-container');
        const addBtn = document.getElementById('add-research-btn');
        const existingItems = container.querySelectorAll('.research-item');
        let researchIndex = existingItems.length > 0 ? existingItems.length : 0;

        addBtn.addEventListener('click', function() {
            const template = `
                <div class="card mb-4 research-item border">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <span class="fw-bold">New Research</span>
                        <button type="button" class="btn btn-danger btn-sm remove-research-btn">&times; Remove</button>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Research Title</label>
                                    <input type="text" name="research[${researchIndex}][title]" class="form-control" placeholder="e.g., Sustainable Agriculture">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="research[${researchIndex}][description]" class="form-control quill-editor" rows="3" placeholder="Brief description of the research..."></textarea>
                                    <input type="hidden" name="research[${researchIndex}][created_at]" value="{{ now() }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Featured Image</label>
                                <input type="file" name="research[${researchIndex}][image]" class="form-control form-control-sm" accept="image/*">
                                <small class="text-muted">Recommended: 16:9 aspect ratio</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
            researchIndex++;
        });

        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-research-btn')) {
                if (confirm('Remove this research item?')) {
                    e.target.closest('.research-item').remove();
                }
            }
        });
    });
</script>

@if(request()->get('action') === 'add')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const addBtn = document.getElementById('add-research-btn');
            if(addBtn) {
                addBtn.click();
                window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
            }
        }, 200);
    });
</script>
@endif
