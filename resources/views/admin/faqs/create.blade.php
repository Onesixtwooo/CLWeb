@extends('admin.layout')

@section('title', 'Add FAQ')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">Add FAQ</h1>
        @if (!empty($fromCollegeSection) && !empty($collegeSlug))
            <a href="{{ route('admin.colleges.show', ['college' => $collegeSlug, 'section' => 'faq']) }}" class="btn btn-outline-secondary">Back to {{ \App\Http\Controllers\Admin\CollegeController::getColleges()[$collegeSlug] ?? $collegeSlug }}</a>
        @else
            <a href="{{ route('admin.colleges.index') }}" class="btn btn-outline-secondary">Back to list</a>
        @endif
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.faqs.store') }}">
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

                <div class="mb-3">
                    <label for="question" class="form-label">Question <span class="text-danger">*</span></label>
                    <input type="text" name="question" id="question" class="form-control @error('question') is-invalid @enderror" value="{{ old('question') }}" required>
                    @error('question')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="answer" class="form-label">Answer <span class="text-danger">*</span></label>
                    <textarea name="answer" id="answer" class="form-control quill-editor @error('answer') is-invalid @enderror" rows="5" required>{{ old('answer') }}</textarea>
                    @error('answer')
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
                    <button type="submit" class="btn btn-admin-primary">Add FAQ</button>
                    @if (!empty($fromCollegeSection) && !empty($collegeSlug))
                        <a href="{{ route('admin.colleges.show', ['college' => $collegeSlug, 'section' => 'faq']) }}" class="btn btn-outline-secondary">Cancel</a>
                    @else
                        <a href="{{ route('admin.colleges.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
