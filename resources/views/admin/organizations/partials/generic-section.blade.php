@if (($sectionContent['layout'] ?? '') === 'split')
    <div class="row g-4 align-items-center">
        <div class="col-md-7">
            <label class="form-label text-muted small fw-700 text-uppercase mb-2 d-block">Content Description</label>
            <div class="p-4 bg-light rounded-4 ql-snow border border-light-subtle">
                <div class="ql-editor" style="padding: 0;">{!! $sectionContent['body'] ?? '' !!}</div>
            </div>
        </div>
        @if (!empty($sectionContent['image']))
            <div class="col-md-5">
                <label class="form-label text-muted small fw-700 text-uppercase mb-2 d-block">Featured Image</label>
                <div class="bg-light rounded-4 border border-light-subtle d-flex align-items-center justify-content-center overflow-hidden" style="min-height: 200px;">
                    <img src="{{ str_starts_with($sectionContent['image'], 'http') || str_starts_with($sectionContent['image'], '/') || str_starts_with($sectionContent['image'], 'media/') ? asset($sectionContent['image']) : asset('/storage/' . $sectionContent['image']) }}" class="img-fluid rounded-4 shadow-sm" alt="{{ $sectionContent['title'] }}" style="max-height: 250px; object-fit: contain;">
                </div>
            </div>
        @endif
    </div>
@elseif (in_array($sectionContent['layout'] ?? '', ['grid', 'testimonials', 'highlights']))
    @php $items = $sectionContent['items'] ?? []; @endphp
    @if (!empty($items))
        <div class="row g-3">
            @foreach($items as $index => $item)
                <div class="@if(($sectionContent['layout'] ?? '') === 'grid' || ($sectionContent['layout'] ?? '') === 'testimonials') col-md-4 @else col-md-6 @endif mb-3">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-light position-relative group">
                        <div class="item-card-actions group-hover-show">
                            <button type="button" class="btn btn-primary btn-item-action shadow-sm" onclick="openItemModal('edit', {{ json_encode($item) }}, {{ $index }})">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <form action="{{ route('admin.organizations.delete-item', ['organization' => $organization, 'section' => $currentSection, 'index' => $index]) }}" method="POST" onsubmit="return confirm('Remove this item?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-item-action shadow-sm">
                                    <i class="bi bi-trash"></i> Remove
                                </button>
                            </form>
                        </div>
                        @if(($sectionContent['layout'] ?? '') === 'grid' && !empty($item['image']))
                            <img src="{{ asset($item['image']) }}" class="card-img-top object-fit-cover" style="height: 150px;">
                        @endif
                        <div class="card-body p-4 pe-5" style="padding-top: 1.5rem !important;">
                            <div class="d-flex align-items-start mb-2">
                                @if(($sectionContent['layout'] ?? '') === 'testimonials' || ($sectionContent['layout'] ?? '') === 'highlights')
                                    <div class="me-3">
                                        @if(!empty($item['image']))
                                            <img src="{{ asset($item['image']) }}" class="rounded-circle shadow-sm border" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center border" style="width: 40px; height: 40px;">
                                                <i class="bi @if(($sectionContent['layout'] ?? '') === 'testimonials') bi-person @else bi-star-fill @endif text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-0 fw-700">{{ $item['name'] ?? ($item['title'] ?? 'Untitled') }}</h6>
                                    @if(!empty($item['role']))
                                        <p class="text-primary small fw-600 mb-0" style="font-size: 0.75rem;">{{ $item['role'] }}</p>
                                    @endif
                                </div>
                            </div>
                            @if(!empty($item['description']))
                                <div class="card-text text-muted small ql-snow mt-2">
                                    <div class="ql-editor p-0" style="font-size: 0.8rem;">{!! $item['description'] !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="py-5 text-center bg-light rounded-4 border border-dashed">
            <i class="bi @if(($sectionContent['layout'] ?? '') === 'testimonials') bi-chat-quote @else bi-grid-3x3-gap @endif fs-1 text-muted opacity-25 d-block mb-2"></i>
            <p class="text-muted fst-italic mb-0">No items added yet. Click "Edit section details" to add content.</p>
        </div>
    @endif
@elseif (!empty($sectionContent['body']))
    <div class="p-4 bg-light rounded-4 ql-snow border border-light-subtle">
        <div class="ql-editor" style="padding: 0;">{!! $sectionContent['body'] !!}</div>
    </div>
@else
    <div class="py-5 text-center bg-light rounded-4 border border-dashed">
        <i class="bi bi-file-earmark-text fs-1 text-muted opacity-25 d-block mb-2"></i>
        <p class="text-muted fst-italic mb-0">No content yet. Click "Edit section details" to add content.</p>
    </div>
@endif
