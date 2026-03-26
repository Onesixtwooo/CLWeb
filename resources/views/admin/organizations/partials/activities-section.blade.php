@php
    $items = $sectionContent['items'] ?? [];
    $activitiesPagination = $activitiesPagination ?? null;
@endphp

@if (!empty($items))
    <div class="table-responsive">
        <table class="table align-middle table-hover border rounded overflow-hidden">
            <thead class="table-light">
                <tr>
                    <th style="width: 72px;">Image</th>
                    <th>Activity</th>
                    <th style="width: 140px;">Date</th>
                    <th style="width: 120px;">Visibility</th>
                    <th class="text-end" style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                    @php
                        $itemIndex = $item['__index'] ?? $index;
                    @endphp
                    <tr>
                        <td>
                            @if(!empty($item['image']))
                                <img
                                    src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($item['image']) }}"
                                    class="rounded border object-fit-cover"
                                    alt="{{ $item['title'] ?? 'Activity image' }}"
                                    style="width: 52px; height: 52px;"
                                >
                            @else
                                <div class="bg-light rounded border d-flex align-items-center justify-content-center text-muted" style="width: 52px; height: 52px;">
                                    <i class="bi bi-image opacity-50"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-700">{{ $item['title'] ?? 'Untitled Activity' }}</div>
                            @if(!empty($item['description']))
                                <div class="text-muted small mt-1">{{ \Illuminate\Support\Str::limit(strip_tags($item['description']), 120) }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-white text-primary border border-primary-subtle rounded-pill px-3">
                                {{ $item['date'] ?? 'No date' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge rounded-pill {{ !array_key_exists('is_visible', $item) || $item['is_visible'] ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }}">
                                {{ !array_key_exists('is_visible', $item) || $item['is_visible'] ? 'Visible' : 'Hidden' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-light border shadow-sm d-inline-flex align-items-center justify-content-center"
                                    style="width: 36px; height: 36px; padding: 0;"
                                    onclick="openItemModal('edit', {{ json_encode($item) }}, {{ $itemIndex }})"
                                    title="Edit activity"
                                    aria-label="Edit activity"
                                >
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('admin.organizations.delete-item', ['organization' => $organization, 'section' => $currentSection, 'index' => $itemIndex]) }}" method="POST" onsubmit="return confirm('Remove this activity?')">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-light border border-danger-subtle text-danger shadow-sm d-inline-flex align-items-center justify-content-center"
                                        style="width: 36px; height: 36px; padding: 0;"
                                        title="Remove activity"
                                        aria-label="Remove activity"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($activitiesPagination && $activitiesPagination->hasPages())
        <div class="mt-3 d-flex justify-content-center">
            {{ $activitiesPagination->appends(request()->except('page'))->links() }}
        </div>
    @endif
@else
    <div class="py-5 text-center bg-light rounded-4 border border-dashed">
        <i class="bi bi-calendar-event fs-1 text-muted opacity-25 d-block mb-2"></i>
        <p class="text-muted fst-italic mb-0">No activities added yet. Click "Add Activity" to create one.</p>
    </div>
@endif
