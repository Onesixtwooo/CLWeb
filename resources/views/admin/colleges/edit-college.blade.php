@extends('admin.layout')

@section('title', 'Edit department name')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">Edit department name</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary">Back to Settings</a>
            <a href="{{ route('admin.colleges.show', ['college' => $college->slug]) }}" class="btn btn-outline-secondary">Back to {{ $college->name }}</a>
        </div>
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.colleges.update', ['college' => $college->slug]) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-12">
                        <label for="name" class="form-label">Department name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $college->name) }}" required>
                        <small class="text-muted">Display name for this department. The URL slug ({{ $college->slug }}) cannot be changed.</small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-admin-primary">Save name</button>
                        <a href="{{ route('admin.colleges.show', ['college' => $college->slug]) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
