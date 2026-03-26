@extends('admin.layout')

@section('title', 'Announcements')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <h1 class="admin-page-title mb-0">Announcements</h1>
        <a href="{{ route('admin.announcements.create') }}" class="btn btn-admin-primary">Add Announcement</a>
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search announcements..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-secondary w-100">Search</button>
                </div>
            </form>

            @if ($announcements->isEmpty())
                <div class="py-5 text-center">
                    <p class="text-muted mb-2">No announcements yet.</p>
                    <a href="{{ route('admin.announcements.create') }}" class="btn btn-admin-primary btn-sm">Add announcement</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 admin-table">
                        <thead>
                            <tr class="text-muted small text-uppercase" style="letter-spacing: 0.05em;">
                                <th class="fw-600 border-0 py-3 px-4">Title</th>
                                <th class="fw-600 border-0 py-3 px-4" style="width: 160px;">Author</th>
                                <th class="fw-600 border-0 py-3 px-4" style="width: 130px;">Published</th>
                                @if (auth()->user()?->isSuperAdmin())
                                    <th class="fw-600 border-0 py-3 px-4" style="width: 180px;">Department (binding)</th>
                                @endif
                                <th class="fw-600 border-0 py-3 px-4 text-end" style="width: 140px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($announcements as $announcement)
                                <tr>
                                    <td class="py-3 px-4 fw-500">{{ Str::limit($announcement->title, 50) }}</td>
                                    <td class="py-3 px-4 text-muted small">{{ $announcement->author ?? '—' }}</td>
                                    <td class="py-3 px-4 text-muted small">{{ $announcement->published_at?->format('M j, Y') ?? '—' }}</td>
                                    @if (auth()->user()?->isSuperAdmin())
                                        <td class="py-3 px-4 text-muted small">
                                            {{ $announcement->college_slug ? (\App\Http\Controllers\Admin\CollegeController::getColleges()[$announcement->college_slug] ?? $announcement->college_slug) : 'All' }}
                                        </td>
                                    @endif
                                    <td class="py-3 px-4 text-end">
                                        @if(auth()->user()?->canManageAnnouncement($announcement))
                                            <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Edit</a>
                                            <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this announcement?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center py-3 border-top">
                    {{ $announcements->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
