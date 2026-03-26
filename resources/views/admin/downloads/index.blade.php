@extends('admin.layout')

@section('title', 'Downloads - ' . $collegeName)

@push('styles')
<style>
    .colleges-layout {
        display: flex;
        gap: 0;
        min-height: 480px;
    }
    .colleges-section-list {
        width: 280px;
        flex-shrink: 0;
        background: var(--admin-surface);
        border: 1px solid var(--admin-border);
        border-radius: var(--admin-radius-lg);
        overflow: hidden;
        margin-right: 1.5rem;
    }
    .colleges-section-list-header {
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid var(--admin-border);
        font-weight: 600;
        font-size: 0.8125rem;
        color: var(--admin-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .colleges-section-item {
        display: block;
        padding: 0.75rem 1.25rem;
        color: var(--admin-text);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9375rem;
        border-left: 3px solid transparent;
        transition: background 0.15s ease, border-color 0.15s ease;
    }
    .colleges-section-item:hover {
        background: var(--admin-accent-soft);
        color: var(--admin-accent);
    }
    .colleges-section-item.active {
        background: var(--admin-accent-soft);
        color: var(--admin-accent);
        border-left-color: var(--admin-accent);
    }
    .downloads-detail {
        width: 100%;
    }
    @media (max-width: 991px) {
        .colleges-layout {
            flex-direction: column;
            gap: 1rem;
        }
        .colleges-section-list {
            width: 100%;
            margin-right: 0;
        }
    }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 class="admin-page-title mb-1">Downloads</h1>
            <p class="text-muted small mb-0">{{ $collegeName }} file resources</p>
        </div>
        <a href="{{ route('admin.colleges.downloads.create', ['college' => $college]) }}" class="btn btn-admin-primary">+ New File</a>
    </div>

    <div class="colleges-layout">
        <aside class="colleges-section-list">
            <div class="colleges-section-list-header">Sections</div>
            <nav>
                <a href="{{ route('admin.colleges.show', ['college' => $college, 'section' => 'live-page']) }}"
                   class="colleges-section-item {{ $currentSection === 'live-page' ? 'active' : '' }}"
                   style="display: flex; align-items: center; gap: 0.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                    Live Page
                </a>
                @foreach ($sections as $slug => $name)
                    <a href="{{ route('admin.colleges.show', ['college' => $college, 'section' => $slug]) }}"
                       class="colleges-section-item {{ $currentSection === $slug ? 'active' : '' }}">
                        {{ $name }}
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="downloads-detail">
            <div class="admin-card">
                <div class="card-body p-4">
                    @if ($downloads->isEmpty())
                        <div class="py-5 text-center">
                            <p class="text-muted mb-2">No downloadable resources found.</p>
                            <a href="{{ route('admin.colleges.downloads.create', ['college' => $college]) }}" class="btn btn-admin-primary btn-sm">Upload one</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>File</th>
                                        <th>Visibility</th>
                                        <th>Date</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($downloads as $download)
                                        <tr>
                                            <td>
                                                <div class="fw-600">{{ $download->title }}</div>
                                                @if ($download->description)
                                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit(strip_tags($download->description), 90) }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <div>{{ $download->file_name }}</div>
                                                <div class="text-muted small">{{ number_format(($download->file_size ?? 0) / 1024, 1) }} KB</div>
                                            </td>
                                            <td>
                                                <span class="badge {{ $download->is_visible ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $download->is_visible ? 'Visible' : 'Hidden' }}
                                                </span>
                                            </td>
                                            <td class="text-muted small">{{ $download->created_at?->format('M j, Y') }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.colleges.downloads.edit', ['college' => $college, 'download' => $download]) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                                <form action="{{ route('admin.colleges.downloads.destroy', ['college' => $college, 'download' => $download]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this file resource?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
