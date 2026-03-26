@extends('admin.layout')

@section('title', 'Edit Testimonial')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">Edit Testimonial</h1>
        @if (!empty($returnCollege))
            <a href="{{ route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'testimonials']) }}" class="btn btn-outline-secondary">Back to {{ \App\Http\Controllers\Admin\CollegeController::getColleges()[$returnCollege] ?? $returnCollege }}</a>
        @else
            <a href="{{ route('admin.colleges.index') }}" class="btn btn-outline-secondary">Back to list</a>
        @endif
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.testimonials.update', $testimonial) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @if (!empty($returnCollege))
                    <input type="hidden" name="return_college" value="{{ $returnCollege }}">
                @endif
                
                @if (auth()->user()?->isSuperAdmin())
                    <div class="mb-3">
                        <label for="college_slug" class="form-label">College</label>
                        <select name="college_slug" id="college_slug" class="form-select">
                            <option value="">All colleges</option>
                            @foreach ($colleges as $slug => $name)
                                <option value="{{ $slug }}" {{ old('college_slug', $testimonial->college_slug) === $slug ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $testimonial->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="role" class="form-label">Role / Title</label>
                            <input type="text" name="role" id="role" class="form-control @error('role') is-invalid @enderror" value="{{ old('role', $testimonial->role) }}">
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="degree" class="form-label">Degree</label>
                    <input type="text" name="degree" id="degree" class="form-control @error('degree') is-invalid @enderror" value="{{ old('degree', $testimonial->degree) }}">
                    @error('degree')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="quote" class="form-label">Quote <span class="text-danger">*</span></label>
                    <textarea name="quote" id="quote" class="form-control quill-editor @error('quote') is-invalid @enderror" rows="5" required>{{ old('quote', $testimonial->quote) }}</textarea>
                    @error('quote')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="photo" class="form-label">Photo</label>
                    @if ($testimonial->photo)
                        <div class="mb-2">
                            <img src="{{ $testimonial->photo }}" alt="{{ $testimonial->name }}" class="img-thumbnail" style="max-height: 100px;">
                        </div>
                    @endif
                    <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                    <small class="text-muted">Leave empty to keep current photo.</small>
                    @error('photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="sort_order" class="form-label">Sort order</label>
                    <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $testimonial->sort_order) }}" min="0">
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_visible" name="is_visible" value="1" {{ old('is_visible', $testimonial->is_visible) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_visible">Visible on public page</label>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-admin-primary">Save Changes</button>
                    @if (!empty($returnCollege))
                        <a href="{{ route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'testimonials']) }}" class="btn btn-outline-secondary">Cancel</a>
                    @else
                        <a href="{{ route('admin.colleges.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
