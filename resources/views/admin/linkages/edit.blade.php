@extends('admin.layout')

@section('title', 'Edit Partner: ' . $partner->name . ' — ' . $department->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="admin-page-title mb-0">Edit Partner: {{ $partner->name }}</h1>
            <p class="text-muted small mb-0">{{ $department->name }} — {{ $collegeName }}</p>
        </div>
        <a href="{{ route('admin.colleges.show-department', ['college' => $college, 'department' => $department, 'section' => 'linkages']) }}" class="btn btn-outline-secondary">Back to Department</a>
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('admin.linkages.update', ['college' => $college, 'department' => $department, 'linkage' => $partner->getRouteKey()]) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Partner Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $partner->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Type</label>
                                    <select name="type" class="form-select @error('type') is-invalid @enderror">
                                        <option value="local" {{ old('type', $partner->type) === 'local' ? 'selected' : '' }}>Local</option>
                                        <option value="international" {{ old('type', $partner->type) === 'international' ? 'selected' : '' }}>International</option>
                                    </select>
                                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-muted small">(Optional)</span></label>
                            <textarea name="description" class="form-control quill-editor" rows="4">{{ old('description', $partner->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Website URL <span class="text-muted small">(Optional)</span></label>
                            <input type="text" name="url"
                                   class="form-control @error('url') is-invalid @enderror"
                                   value="{{ old('url', $partner->url) }}"
                                   placeholder="https://example.edu or facebook.com">
                            @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-4 text-center">
                        <label class="form-label d-block text-start">Partner Logo</label>
                        @if ($partner->image)
                            <div class="mb-3">
                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($partner->image) }}"
                                     alt="Current Logo" class="img-thumbnail" style="max-height: 150px;">
                                <p class="small text-muted mt-1">Current Logo</p>
                            </div>
                        @endif
                        <input type="file" name="image"
                               class="form-control @error('image') is-invalid @enderror"
                               accept="image/*">
                        <small class="text-muted d-block mt-2">Upload to replace the current logo. Recommended: 200×200px.</small>
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-admin-primary">Update Partner</button>
                    <a href="{{ route('admin.colleges.show-department', ['college' => $college, 'department' => $department, 'section' => 'linkages']) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
