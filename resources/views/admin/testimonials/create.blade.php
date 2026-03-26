@extends('admin.layout')

@section('title', 'Add Testimonial')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">Add Testimonial</h1>
        @if (!empty($fromCollegeSection) && !empty($collegeSlug))
            <a href="{{ route('admin.colleges.show', ['college' => $collegeSlug, 'section' => 'testimonials']) }}" class="btn btn-outline-secondary">Back to {{ \App\Http\Controllers\Admin\CollegeController::getColleges()[$collegeSlug] ?? $collegeSlug }}</a>
        @else
            <a href="{{ route('admin.colleges.index') }}" class="btn btn-outline-secondary">Back to list</a>
        @endif
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.testimonials.store') }}" enctype="multipart/form-data">
                @csrf
                @if (!empty($collegeSlug))
                    <input type="hidden" name="return_college" value="{{ $collegeSlug }}">
                @endif
                
                @if (auth()->user()?->isSuperAdmin())
                    <div class="mb-3">
                        <label for="college_slug" class="form-label">College</label>
                        <select name="college_slug" id="college_slug" class="form-select">
                            <option value="">All colleges</option>
                            @foreach ($colleges as $slug => $name)
                                <option value="{{ $slug }}" {{ old('college_slug', $collegeSlug ?? '') === $slug ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                @elseif(!empty($collegeSlug))
                    <input type="hidden" name="college_slug" value="{{ $collegeSlug }}">
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="role" class="form-label">Role / Title</label>
                            <input type="text" name="role" id="role" class="form-control @error('role') is-invalid @enderror" value="{{ old('role') }}" placeholder="e.g. Electrical Engineer">
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="degree" class="form-label">Degree</label>
                    <input type="text" name="degree" id="degree" class="form-control @error('degree') is-invalid @enderror" value="{{ old('degree') }}" placeholder="e.g. BS Electrical Engineering">
                    @error('degree')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="quote" class="form-label">Quote <span class="text-danger">*</span></label>
                    <textarea name="quote" id="quote" class="form-control quill-editor @error('quote') is-invalid @enderror" rows="5" required>{{ old('quote') }}</textarea>
                    @error('quote')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="photo" class="form-label">Photo</label>
                    <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                    <small class="text-muted">Optional. Square photos look best.</small>
                    @error('photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="sort_order" class="form-label">Sort order</label>
                    <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_visible" name="is_visible" value="1" {{ old('is_visible', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_visible">Visible on public page</label>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-admin-primary">Add Testimonial</button>
                    @if (!empty($fromCollegeSection) && !empty($collegeSlug))
                        <a href="{{ route('admin.colleges.show', ['college' => $collegeSlug, 'section' => 'testimonials']) }}" class="btn btn-outline-secondary">Cancel</a>
                    @else
                        <a href="{{ route('admin.colleges.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
