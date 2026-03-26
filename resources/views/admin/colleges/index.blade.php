@extends('admin.layout')

@section('title', 'Colleges')

@section('content')
    <h1 class="admin-page-title">Colleges</h1>
    <p class="text-muted mb-4">Select a college to view and manage its details.</p>

    @if (auth()->user()?->isSuperAdmin())
        <div class="admin-card mb-4">
            <div class="card-body p-4">
                <h2 class="h5 fw-600 mb-3">Manage colleges</h2>
                <p class="text-muted small mb-3">Add, rename, or remove colleges. Slugs are used in URLs and cannot be changed once created.</p>
                <form method="POST" action="{{ route('admin.colleges.store') }}" class="row g-2 align-items-end mb-4">
                    @csrf
                    <div class="col-md-5">
                        <label for="college_name" class="form-label">New college name</label>
                        <input type="text" name="name" id="college_name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-5">
                        <label for="college_slug" class="form-label">
                            Slug (optional)
                            <small class="text-muted d-block fw-normal" style="font-size: 0.75rem;">Used in URLs; leave blank to auto-generate.</small>
                        </label>
                        <input type="text" name="slug" id="college_slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" placeholder="e.g. engineering">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-admin-primary w-100">Add</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead>
                            <tr>
                                <th class="border-0 py-2">College</th>
                                <th class="border-0 py-2 text-end" style="width: 220px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($colleges as $slug => $name)
                                <tr>
                                    <td class="py-2 fw-500">{{ $name }}</td>
                                    <td class="py-2 text-end">
                                        <a href="{{ route('admin.colleges.show', ['college' => $slug]) }}" class="btn btn-sm btn-outline-success rounded-circle p-2" title="View college" style="width: 36px; height: 36px;">👁</a>
                                        <a href="{{ route('admin.colleges.edit', ['college' => $slug]) }}" class="btn btn-sm btn-outline-secondary rounded-circle p-2 ms-1" title="Edit name" style="width: 36px; height: 36px;">✎</a>
                                        <form action="{{ route('admin.colleges.destroy', ['college' => $slug]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this college? This is only allowed when it has no content or faculty.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2 ms-1" title="Delete college" style="width: 36px; height: 36px;">✕</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('styles')
<style>
</style>
@endpush
