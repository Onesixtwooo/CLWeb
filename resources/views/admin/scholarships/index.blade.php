@extends('admin.layout')
@php
    $isGlobal = ($college === '_global');
    $routePrefix = $isGlobal ? 'admin.scholarships' : 'admin.colleges.scholarships';
    $routeParams = $isGlobal ? [] : ['college' => $college];
@endphp

@section('title', "Scholarships - " . ($isGlobal ? 'All Scholarships' : $collegeName))

@section('content')
    @php
        $scholarshipsSection = $isGlobal
            ? null
            : \App\Models\CollegeSection::where('college_slug', $college)->where('section_slug', 'scholarships')->first();
        $scholarshipsVisible = $scholarshipsSection ? (bool) $scholarshipsSection->is_visible : true;
    @endphp
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 class="admin-page-title mb-1">{{ $isGlobal ? 'All Scholarships' : 'Scholarships' }}</h1>
            <p class="text-muted small mb-0">
                @if($isGlobal)
                    Manage both Global and College-specific scholarships in one place.
                @else
                    {{ $collegeName }} — Manage scholarship programs
                @endif
            </p>
            @unless($isGlobal)
                <div class="mt-2">
                    <form method="POST" action="{{ route('admin.colleges.toggle-visibility', ['college' => $college, 'section' => 'scholarships']) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="badge border-0 {{ $scholarshipsVisible ? 'bg-success' : 'bg-danger' }}" style="cursor: pointer;">
                            {{ $scholarshipsVisible ? 'Visible on Public Page' : 'Hidden from Public Page' }}
                        </button>
                    </form>
                </div>
            @endunless
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @unless($isGlobal)
                <a href="{{ route('admin.colleges.edit-section', ['college' => $college, 'section' => 'scholarships']) }}" class="btn btn-outline-secondary">
                    Edit section details
                </a>
            @endunless
            <a href="{{ route($routePrefix . '.create', $routeParams) }}" class="btn btn-admin-primary">+ New Scholarship</a>
        </div>
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Search scholarships..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-secondary w-100">Search</button>
                </div>
            </form>

            @if ($scholarships->isEmpty())
                <div class="py-5 text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px; background: var(--admin-accent-soft);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--admin-accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.1 2.7 3 6 3s6-1.9 6-3v-5"/></svg>
                    </div>
                    <p class="text-muted mb-2">No scholarships found.</p>
                    <a href="{{ route($routePrefix . '.create', $routeParams) }}" class="btn btn-admin-primary btn-sm">Create one</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 admin-table">
                        <thead>
                            <tr class="text-muted small text-uppercase" style="letter-spacing: 0.05em;">
                                <th class="fw-600 border-0 py-3 px-4" style="width: 60px;">Image</th>
                                <th class="fw-600 border-0 py-3 px-4">Title</th>
                                @if ($isGlobal)
                                    <th class="fw-600 border-0 py-3 px-4" style="width: 150px;">College</th>
                                @endif
                                <th class="fw-600 border-0 py-3 px-4" style="width: 120px;">Added By</th>
                                <th class="fw-600 border-0 py-3 px-4" style="width: 120px;">Date</th>
                                <th class="fw-600 border-0 py-3 px-4 text-end" style="width: 200px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($scholarships as $scholarship)
                                @php $locked = $scholarship->isLockedFor(auth()->user()); @endphp
                                <tr>
                                    <td class="py-3 px-4">
                                        @if ($scholarship->image)
                                            <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($scholarship->image) }}" alt="" class="rounded" style="width: 45px; height: 45px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; color: #ccc; font-size: 0.6rem;">No img</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="fw-500">{{ Str::limit($scholarship->title, 50) }}</span>
                                        @if ($scholarship->college_slug === '_global')
                                            <span class="badge rounded-pill ms-1" style="background: rgba(var(--admin-accent-rgb), 0.1); color: var(--admin-accent); font-size: 0.625rem; font-weight: 600; padding: 0.2em 0.5em;">Global</span>
                                        @endif
                                        @if ($locked)
                                            <span class="badge rounded-pill ms-1" style="background: rgba(239,68,68,0.12); color: #ef4444; font-size: 0.625rem; font-weight: 600; padding: 0.2em 0.5em;">🔒 Protected</span>
                                        @endif
                                        @if ($scholarship->description)
                                            <div class="text-muted small mt-1">{{ Str::limit(strip_tags($scholarship->description), 80) }}</div>
                                        @endif
                                    </td>
                                    @if ($isGlobal)
                                        <td class="py-3 px-4">
                                            @if($scholarship->college_slug === '_global')
                                                <span class="badge rounded-pill px-2 py-1" style="background: rgba(var(--admin-accent-rgb), 0.12); color: var(--admin-accent); font-weight: 600;">
                                                    Global
                                                </span>
                                            @else
                                                <span class="badge rounded-pill px-2 py-1" style="background: #f1f5f9; color: #475569; font-weight: 500;">
                                                    {{ $colleges[$scholarship->college_slug] ?? $scholarship->college_slug }}
                                                </span>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="py-3 px-4">
                                        <span class="badge rounded-pill px-2 py-1" style="background: {{ $scholarship->added_by === 'superadmin' ? 'rgba(139,92,246,0.12)' : 'var(--admin-accent-soft)' }}; color: {{ $scholarship->added_by === 'superadmin' ? '#8b5cf6' : 'var(--admin-accent)' }}; font-weight: 500;">
                                            {{ $scholarship->added_by === 'superadmin' ? 'Super Admin' : 'Admin' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-muted small">{{ $scholarship->created_at->format('M j, Y') }}</td>
                                    <td class="py-3 px-4 text-end">
                                        @if (!$locked)
                                            @php
                                                $editRoute = $isGlobal
                                                    ? route('admin.colleges.scholarships.edit', ['college' => $scholarship->college_slug, 'scholarship' => $scholarship->id])
                                                    : route($routePrefix . '.edit', array_merge($routeParams, ['scholarship' => $scholarship->id]));
                                                $deleteRoute = $isGlobal
                                                    ? route('admin.colleges.scholarships.destroy', ['college' => $scholarship->college_slug, 'scholarship' => $scholarship->id])
                                                    : route($routePrefix . '.destroy', array_merge($routeParams, ['scholarship' => $scholarship->id]));
                                            @endphp
                                            <a href="{{ $editRoute }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Edit</a>
                                            <form action="{{ $deleteRoute }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this scholarship?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">Delete</button>
                                            </form>
                                        @else
                                            <span class="text-muted small fst-italic">Protected</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center py-3 border-top">
                    {{ $scholarships->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
