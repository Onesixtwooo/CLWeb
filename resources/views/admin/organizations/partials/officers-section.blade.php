@if ($organization->adviser)
    <div class="alert alert-info border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
            <i class="bi bi-person-badge-fill fs-4"></i>
        </div>
        <div>
            <p class="text-muted small mb-0 fw-600 text-uppercase ls-wide" style="font-size: 0.7rem;">Faculty Adviser</p>
            <h5 class="mb-0 fw-700">{{ $organization->adviser }}</h5>
        </div>
    </div>
@endif
@php $items = $sectionContent['items'] ?? []; @endphp

@if (!empty($items))
    <div class="row g-3">
        @foreach($items as $index => $item)
            <div class="col-md-6 mb-3">
                <div class="d-flex align-items-center p-3 pe-5 bg-light rounded-4 border border-light-subtle h-100 position-relative group">
                    <div class="item-card-actions group-hover-show">
                        <button type="button" class="btn btn-primary btn-item-action shadow-sm" onclick="openItemModal('edit', {{ json_encode($item) }}, {{ $index }})">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        <form action="{{ route('admin.organizations.delete-item', ['organization' => $organization, 'section' => $currentSection, 'index' => $index]) }}" method="POST" onsubmit="return confirm('Remove this member?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-item-action shadow-sm">
                                <i class="bi bi-trash"></i> Remove
                            </button>
                        </form>
                    </div>
                    <div class="flex-shrink-0 me-3">
                        @if(!empty($item['image']))
                            <img src="{{ asset($item['image']) }}" class="rounded-circle shadow-sm border" style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            @php
                                $orgLogo = \App\Providers\AppServiceProvider::resolveLogoUrl($organization->logo);
                            @endphp
                            @if($orgLogo)
                                <img src="{{ $orgLogo }}" class="rounded-circle shadow-sm border p-1 bg-white" style="width: 60px; height: 60px; object-fit: contain;">
                            @else
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center border" style="width: 60px; height: 60px;">
                                    <i class="bi bi-person text-muted fs-3"></i>
                                </div>
                            @endif
                        @endif
                    </div>
                    <div>
                        <h5 class="mb-0 fw-700">{{ $item['name'] ?? 'Untitled' }}</h5>
                        <p class="text-primary small fw-600 mb-1">{{ $item['role'] ?? 'No role' }}</p>
                        @if(!empty($item['description']))
                            <p class="text-muted small mb-0">{{ Str::limit($item['description'], 80) }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="py-5 text-center bg-light rounded-4 border border-dashed">
        <i class="bi bi-people fs-1 text-muted opacity-25 d-block mb-2"></i>
        <p class="text-muted fst-italic mb-0">No members added yet. Click "Edit section details" to add content.</p>
    </div>
@endif
