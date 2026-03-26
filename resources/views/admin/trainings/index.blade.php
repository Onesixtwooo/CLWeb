@extends('admin.layout')

@section('title', "Training & Workshops - " . $collegeName)

@push('styles')
<style>
    @media (max-width: 991.98px) {
        .admin-card {
            padding: 1rem !important; /* Reduce card ambient space */
        }
        .table-responsive {
            border: none !important;
            overflow-x: visible !important;
        }
        .admin-table {
            display: block !important;
            width: 100% !important;
            border: none !important;
        }
        .admin-table thead {
            display: none !important;
        }
        .admin-table tbody {
            display: block !important;
            width: 100% !important;
        }
        .admin-table tbody tr {
            display: flex !important;
            flex-direction: column !important;
            background: #fff !important;
            border: 1px solid var(--admin-border) !important;
            border-radius: 12px !important;
            margin-bottom: 1rem !important;
            padding: 1.25rem !important;
            box-shadow: var(--admin-shadow) !important;
            width: 100% !important;
        }
        .admin-table tbody td {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 100% !important;
            padding: 0.625rem 0 !important;
            border: none !important;
            border-bottom: 1px solid rgba(0,0,0,0.03) !important;
            text-align: right !important;
        }
        .admin-table tbody td:last-child {
            border-bottom: none !important;
            padding-top: 1rem !important;
            justify-content: flex-start !important;
        }
        .admin-table tbody td::before {
            font-weight: 600 !important;
            color: var(--admin-text-muted) !important;
            font-size: 0.8125rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            text-align: left !important;
            margin-right: 1rem !important;
        }

        /* Image Cell - Card Header Icon */
        .admin-table tbody td:nth-of-type(1) {
            justify-content: center !important;
            border-bottom: none !important;
            padding-bottom: 0.25rem !important;
        }
        .admin-table tbody td:nth-of-type(1)::before { content: ''; }

        /* Title Cell - Card Title Header */
        .admin-table tbody td:nth-of-type(2) {
            justify-content: center !important;
            font-size: 1.15rem !important;
            font-weight: 700 !important;
            border-bottom: 1px solid rgba(0,0,0,0.06) !important;
            padding-bottom: 0.75rem !important;
            margin-bottom: 0.25rem !important;
            text-align: center !important;
        }
        .admin-table tbody td:nth-of-type(2)::before { content: ''; }
        
        .admin-table td:nth-of-type(3)::before { content: 'Date'; }
        .admin-table td:nth-of-type(4)::before { content: ''; }

        .admin-table tbody td .btn {
            padding: 0.4rem 0.8rem !important;
            width: auto !important;
            height: auto !important;
            border-radius: 6px !important;
            display: inline-flex !important;
        }
    }
</style>
@endpush


@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 class="admin-page-title mb-1">Training & Workshops</h1>
            <p class="text-muted small mb-0">
                {{ $collegeName }} — Manage faculty trainings, student workshops, and seminars
            </p>
        </div>
        <a href="{{ route('admin.colleges.trainings.create', ['college' => $college]) }}" class="btn btn-admin-primary">+ New Activity</a>
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            @if ($trainings->isEmpty())
                <div class="py-5 text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px; background: var(--admin-accent-soft);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--admin-accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <p class="text-muted mb-2">No training activities found.</p>
                    <a href="{{ route('admin.colleges.trainings.create', ['college' => $college]) }}" class="btn btn-admin-primary btn-sm">Create one</a>
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
                            @foreach ($trainings as $training)
                                <tr>
                                    <td class="py-3 px-4">
                                        @if ($training->image)
                                            <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($training->image) }}" alt="" class="rounded" style="width: 45px; height: 45px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; color: #ccc; font-size: 0.6rem;">No img</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="fw-500">{{ Str::limit($training->title, 50) }}</span>
                                        @if ($training->description)
                                            <div class="text-muted small mt-1">{{ Str::limit(strip_tags($training->description), 80) }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-muted small">{{ $training->created_at->format('M j, Y') }}</td>
                                    <td class="py-3 px-4 text-end">
                                        <a href="{{ route('admin.colleges.trainings.edit', ['college' => $college, 'training' => $training]) }}" class="btn btn-sm btn-outline-secondary rounded-circle p-0" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.colleges.trainings.destroy', ['college' => $college, 'training' => $training]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this training activity?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-0" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
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
