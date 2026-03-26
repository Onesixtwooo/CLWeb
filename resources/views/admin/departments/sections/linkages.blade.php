@if ($editMode !== 'roster' && $editMode !== 'edit_partner' && $editMode !== 'add_partner')
    <input type="hidden" name="_linkages_edit" value="1">
    {{-- LINKAGES EDITOR --}}
    <div class="col-12 mb-3">
        <label for="title" class="form-label">Linkages Section Title</label>
        <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $content['title'] ?? 'Linkages') }}" placeholder="e.g., Linkages">
    </div>

    <div class="col-12 mb-3">
        <label for="body" class="form-label">Introductory Text</label>
        <textarea name="body" id="body" class="form-control quill-editor" rows="4" placeholder="Brief introduction about your department's linkages...">{{ old('body', $content['body'] ?? '') }}</textarea>
    </div>

    <div class="col-12 mb-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
        </div>
    </div>

@elseif ($editMode === 'roster')
    <input type="hidden" name="_roster_edit" value="1">
    {{-- ROSTER EDITOR --}}
    <div class="col-12">
        <h4 class="fw-bold mb-3">Partnership Roster</h4>
        <p class="text-muted small">Manage local and international linkages for this department.</p>
        
        @php
            $linkages = $content['items'] ?? [];
        @endphp

        <div id="linkages-container">
            @forelse($linkages as $index => $item)
                <div class="card mb-4 linkage-item border">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Partner #{{ $index + 1 }}</span>
                        <button type="button" class="btn btn-danger btn-sm remove-linkage-btn">&times; Remove</button>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Partner Name</label>
                                            <input type="text" name="linkages[{{ $index }}][name]" class="form-control" value="{{ $item['name'] ?? '' }}" placeholder="e.g., University of Example">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Type</label>
                                            <select name="linkages[{{ $index }}][type]" class="form-select">
                                                <option value="local" {{ ($item['type'] ?? '') === 'local' ? 'selected' : '' }}>Local</option>
                                                <option value="international" {{ ($item['type'] ?? '') === 'international' ? 'selected' : '' }}>International</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description (Optional)</label>
                                    <textarea name="linkages[{{ $index }}][description]" class="form-control quill-editor" rows="2" placeholder="Brief description of the partnership...">{{ $item['description'] ?? '' }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Website URL (Optional)</label>
                                    <input type="url" name="linkages[{{ $index }}][url]" class="form-control" value="{{ $item['url'] ?? '' }}" placeholder="https://example.edu">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Partner Logo</label>
                                @if(!empty($item['image']))
                                    <div class="mb-2">
                                        <img src="{{ $item['image'] }}" alt="Partner Logo" class="img-fluid rounded border" style="max-height: 100px; width: 100%; object-fit: contain;">
                                        <input type="hidden" name="linkages[{{ $index }}][existing_image]" value="{{ $item['image'] }}">
                                    </div>
                                @endif
                                <input type="file" name="linkages[{{ $index }}][image]" class="form-control form-control-sm" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>
            @empty
            @endforelse
        </div>

        <button type="button" id="add-linkage-btn" class="btn btn-outline-primary">
            + Add New Partner
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('linkages-container');
            const addBtn = document.getElementById('add-linkage-btn');
            let linkageIndex = container.querySelectorAll('.linkage-item').length;

            addBtn.addEventListener('click', function() {
                const template = `
                    <div class="card mb-4 linkage-item border">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <span class="fw-bold">New Partner</span>
                            <button type="button" class="btn btn-danger btn-sm remove-linkage-btn">&times; Remove</button>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label class="form-label">Partner Name</label>
                                                <input type="text" name="linkages[${linkageIndex}][name]" class="form-control" placeholder="e.g., University of Example">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Type</label>
                                                <select name="linkages[${linkageIndex}][type]" class="form-select">
                                                    <option value="local">Local</option>
                                                    <option value="international">International</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Description (Optional)</label>
                                        <textarea name="linkages[${linkageIndex}][description]" class="form-control quill-editor" rows="2" placeholder="Brief description of the partnership..."></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Website URL (Optional)</label>
                                        <input type="url" name="linkages[${linkageIndex}][url]" class="form-control" placeholder="https://example.edu">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Partner Logo</label>
                                    <input type="file" name="linkages[${linkageIndex}][image]" class="form-control form-control-sm" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', template);
                linkageIndex++;
            });

            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-linkage-btn')) {
                    if (confirm('Remove this partner?')) {
                        e.target.closest('.linkage-item').remove();
                    }
                }
            });
        });
    </script>

    @if(request()->get('action') === 'add')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const addBtn = document.getElementById('add-linkage-btn'); // Fixed id from add-alumni-btn in original
                if(addBtn) {
                    addBtn.click();
                    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
                }
            }, 200);
        });
    </script>
    @endif

@elseif ($editMode === 'edit_partner')
    <input type="hidden" name="_edit_partner_edit" value="1">
    <input type="hidden" name="partner_id" value="{{ $partner->id ?? '' }}">
    {{-- SINGLE PARTNER EDITOR --}}
    <div class="col-12">
        <div class="card border">
            <div class="card-header bg-light">
                <h5 class="mb-0 fw-bold">Edit Partner: {{ $partner->name }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Partner Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $partner->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Type</label>
                                    <select name="type" class="form-select">
                                        <option value="local" {{ old('type', $partner->type) === 'local' ? 'selected' : '' }}>Local</option>
                                        <option value="international" {{ old('type', $partner->type) === 'international' ? 'selected' : '' }}>International</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description (Optional)</label>
                            <textarea name="description" class="form-control quill-editor" rows="4">{{ old('description', $partner->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Website URL (Optional)</label>
                            <input type="url" name="url" class="form-control" value="{{ old('url', $partner->url) }}" placeholder="https://example.edu">
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <label class="form-label d-block text-start">Partner Logo</label>
                        @if($partner->image)
                            <div class="mb-3">
                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($partner->image) }}" alt="Current Logo" class="img-thumbnail" style="max-height: 150px;">
                                <p class="small text-muted mt-1">Current Logo</p>
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted d-block mt-2">Upload to replace current logo. Recommended size: 200x200px.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

@elseif ($editMode === 'add_partner')
    <input type="hidden" name="_add_partner_edit" value="1">
    {{-- SINGLE PARTNER ADDER --}}
    <div class="col-12">
        <div class="card border">
            <div class="card-header bg-light">
                <h5 class="mb-0 fw-bold">New Partner</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Partner Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g., University of Example" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Type</label>
                                    <select name="type" class="form-select">
                                        <option value="local">Local</option>
                                        <option value="international">International</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description (Optional)</label>
                            <textarea name="description" class="form-control quill-editor" rows="4" placeholder="Brief description of the partnership..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Website URL (Optional)</label>
                            <input type="url" name="url" class="form-control" placeholder="https://example.edu">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Partner Logo</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted d-block mt-2">Recommended size: 200x200px (PNG or JPG)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
