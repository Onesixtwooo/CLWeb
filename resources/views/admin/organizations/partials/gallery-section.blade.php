@php 
    $items = $sectionContent['items'] ?? []; 
    $albumIndex = $activeAlbumIndex ?? null;
    $inAlbum = $albumIndex !== null && isset($items[$albumIndex]);
    $albumData = $inAlbum ? $items[$albumIndex] : null;
    $displayItems = $inAlbum ? ($albumData['photos'] ?? []) : $items;
    $albumRouteKeys = [];

    if (! $inAlbum) {
        $usedAlbumRouteKeys = [];

        foreach (array_values($items) as $routeIndex => $routeItem) {
            $baseRouteKey = \Illuminate\Support\Str::slug($routeItem['title'] ?? ($routeItem['name'] ?? ''));

            if ($baseRouteKey === '') {
                $baseRouteKey = 'album-' . ($routeIndex + 1);
            }

            $routeKey = $baseRouteKey;
            $routeCounter = 2;

            while (isset($usedAlbumRouteKeys[$routeKey])) {
                $routeKey = $baseRouteKey . '-' . $routeCounter++;
            }

            $usedAlbumRouteKeys[$routeKey] = $routeIndex;
            $albumRouteKeys[$routeIndex] = $routeKey;
        }
    }
@endphp

@if ($inAlbum)
    <div class="mb-4 pb-3 border-bottom d-flex align-items-center gap-3">
        @if(!empty($albumData['image']))
            <img src="{{ asset($albumData['image']) }}" class="rounded shadow-sm object-fit-cover" style="width: 60px; height: 60px;">
        @else
            <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted border shadow-sm" style="width: 60px; height: 60px;">
                <i class="bi bi-folder-fill fs-3 opacity-50"></i>
            </div>
        @endif
        <div>
            <h4 class="mb-1 fw-700">{{ $albumData['title'] ?? ($albumData['name'] ?? 'Untitled Album') }}</h4>
            @if(!empty($albumData['description']))
                <div class="text-muted small mb-0 ql-snow">
                    <div class="ql-editor p-0">{!! $albumData['description'] !!}</div>
                </div>
            @else
                <p class="text-muted small mb-0">{{ count($displayItems) }} photos</p>
            @endif
        </div>
    </div>
@else
    <div class="alert alert-light border shadow-sm mb-4 d-flex align-items-center py-2 px-3 rounded-pill group">
        <i class="bi bi-info-circle-fill text-primary me-2"></i>
        <span class="small text-muted">Create an album first to organize your photos.</span>
    </div>
@endif

@if (!empty($displayItems))
    <div class="row g-3 {{ $inAlbum ? 'album-photo-grid' : '' }}" @if($inAlbum) id="album-photo-grid" data-reorder-url="{{ route('admin.organizations.reorder-items', ['organization' => $organization, 'section' => $currentSection, 'album' => $albumIndex]) }}" @endif>
        @foreach($displayItems as $index => $item)
            @php
                $coverImage = !empty($item['image'])
                    ? $item['image']
                    : (!empty($item['photos'][0]['image']) ? $item['photos'][0]['image'] : null);
            @endphp
            <div class="col-md-4 col-sm-6 mb-3 {{ $inAlbum ? 'album-photo-item' : '' }}" @if($inAlbum) draggable="true" data-index="{{ $index }}" @endif>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-light position-relative group {{ $inAlbum ? 'sortable-photo-card' : '' }}" style="min-height: 120px;">
                    <div class="item-card-actions group-hover-show bg-transparent shadow-none border-0 p-1">
                        @if($inAlbum)
                            <button type="button" class="btn btn-sm btn-light bg-opacity-75 border shadow-sm drag-handle p-0 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Drag to Reorder">
                                <i class="bi bi-grip-vertical"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light bg-opacity-75 border shadow-sm p-0 rounded-circle text-success d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" onclick="openItemModal('edit', {{ json_encode($item) }}, {{ $index }}, {{ $albumIndex }})" title="Edit Photo"><i class="bi bi-pencil-square fs-6"></i></button>
                            <form action="{{ route('admin.organizations.delete-item', ['organization' => $organization, 'section' => $currentSection, 'index' => $index, 'album' => $albumIndex]) }}" method="POST" onsubmit="return confirm('Remove this photo?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light bg-opacity-75 border shadow-sm p-0 rounded-circle text-danger d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Remove/Delete"><i class="bi bi-trash fs-6"></i></button>
                            </form>
                        @else
                            <button type="button" class="btn btn-sm btn-light bg-opacity-75 border shadow-sm p-0 rounded-circle text-success d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" onclick="openItemModal('edit', {{ json_encode($item) }}, {{ $index }})" title="Edit Album"><i class="bi bi-pencil-square fs-6"></i></button>
                            <form action="{{ route('admin.organizations.delete-item', ['organization' => $organization, 'section' => $currentSection, 'index' => $index]) }}" method="POST" onsubmit="return confirm('Delete this album and all its photos?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light bg-opacity-75 border shadow-sm p-0 rounded-circle text-danger d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Remove/Delete"><i class="bi bi-trash fs-6"></i></button>
                            </form>
                        @endif
                    </div>
                    
                    <div class="position-relative overflow-hidden bg-white" style="height: 220px;">
                        @if(!empty($coverImage))
                            <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($coverImage) }}" class="img-fluid h-100 w-100 object-fit-cover shadow-sm">
                        @else
                            <div class="bg-white h-100 w-100 d-flex align-items-center justify-content-center text-muted border">
                                <i class="bi @if($inAlbum) bi-image @else bi-folder-fill @endif fs-1 opacity-25"></i>
                            </div>
                        @endif
                        
                        @if(!$inAlbum)
                        <div class="position-absolute bottom-0 start-0 w-100 p-2" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                            <span class="badge bg-dark bg-opacity-75 text-white border border-light border-opacity-25 rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                <i class="bi bi-images me-1"></i> {{ count($item['photos'] ?? []) }}
                            </span>
                        </div>
                        @endif
                    </div>
                    
                    <div class="card-body p-3 text-center d-flex flex-column justify-content-between">
                        @if($inAlbum)
                            @if(!empty($item['caption']))
                                <p class="text-muted small mb-0 fw-600">{{ $item['caption'] }}</p>
                            @else
                                <p class="text-muted small mb-0 fst-italic opacity-50">No caption</p>
                            @endif
                        @else
                            <h6 class="fw-700 mb-1 text-truncate" title="{{ $item['title'] ?? ($item['name'] ?? 'Untitled') }}">{{ $item['title'] ?? ($item['name'] ?? 'Untitled') }}</h6>
                            @if(!empty($item['description']))
                                <div class="text-muted small mb-2 ql-snow">
                                    <div class="ql-editor p-0" style="font-size: 0.8rem;">{!! $item['description'] !!}</div>
                                </div>
                            @endif
                            <a href="{{ route('admin.organizations.gallery-album', ['college' => $collegeSlug, 'organization' => $organization, 'album' => $albumRouteKeys[$index] ?? ('album-' . ($index + 1))]) }}" class="btn btn-sm btn-outline-primary rounded-pill mt-2 w-100">
                                Open Album
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="py-5 text-center bg-light rounded-4 border border-dashed">
        <i class="bi @if($inAlbum) bi-images @else bi-folder-plus @endif fs-1 text-muted opacity-25 d-block mb-2"></i>
        <p class="text-muted fst-italic mb-0">
            @if($inAlbum)
                No photos in this album yet. Click "Add Photo" or "Batch Upload" to add some.
            @else
                No albums created yet. Click "Add Album" to get started.
            @endif
        </p>
    </div>
@endif
