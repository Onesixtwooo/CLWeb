@extends('admin.layout')

@section('title', "Extension Activities - " . $collegeName)

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 class="admin-page-title mb-1">Extension Activities</h1>
            <p class="text-muted small mb-0">
                {{ $collegeName }} — Manage extension activities and outreach programs
            </p>
        </div>
        <a href="{{ route('admin.colleges.extensions.create', ['college' => $college]) }}" class="btn btn-admin-primary">+ New Extension Activity</a>
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            @if ($extensions->isEmpty())
                <div class="py-5 text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px; background: var(--admin-accent-soft);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--admin-accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <p class="text-muted mb-2">No extension activities found.</p>
                    <a href="{{ route('admin.colleges.extensions.create', ['college' => $college]) }}" class="btn btn-admin-primary btn-sm">Create one</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 admin-table">
                        <thead>
                            <tr class="text-muted small text-uppercase" style="letter-spacing: 0.05em;">
                                <th class="fw-600 border-0 py-3 px-4" style="width: 60px;">Image</th>
                                <th class="fw-600 border-0 py-3 px-4">Title</th>
                                <th class="fw-600 border-0 py-3 px-4" style="width: 120px;">Date</th>
                                <th class="fw-600 border-0 py-3 px-4 text-end" style="width: 200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($extensions as $extension)
                                <tr>
                                    <td class="py-3 px-4">
                                        @if ($extension->image)
                                            <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($extension->image) }}" alt="" class="rounded" style="width: 45px; height: 45px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; color: #ccc; font-size: 0.6rem;">No img</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="fw-500">{{ Str::limit($extension->title, 50) }}</span>
                                        @if ($extension->description)
                                            <div class="text-muted small mt-1">{{ Str::limit(strip_tags($extension->description), 80) }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-muted small">{{ $extension->created_at->format('M j, Y') }}</td>
                                    <td class="py-3 px-4 text-end">
                                        <a href="{{ route('admin.colleges.extensions.edit', ['college' => $college, 'extension' => $extension->id]) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Edit</a>
                                        <form action="{{ route('admin.colleges.extensions.destroy', ['college' => $college, 'extension' => $extension->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this extension activity?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">Delete</button>
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
@endsection
