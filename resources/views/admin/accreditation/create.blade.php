@extends('admin.layout')



@section('title', 'Add Accreditation Record')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">Add Accreditation Record</h1>
        @if (!empty($fromCollegeSection) && !empty($collegeSlug))
            <a href="{{ route('admin.colleges.show', ['college' => $collegeSlug, 'section' => 'accreditation']) }}" class="btn btn-outline-secondary">Back to {{ \App\Http\Controllers\Admin\CollegeController::getColleges()[$collegeSlug] ?? $collegeSlug }}</a>
        @else
            <a href="{{ route('admin.colleges.index') }}" class="btn btn-outline-secondary">Back to list</a>
        @endif
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.accreditations.store') }}" enctype="multipart/form-data">
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
                    <label for="program_id" class="form-label">Program (Optional)</label>
                    <select name="program_id" id="program_id" class="form-select @error('program_id') is-invalid @enderror">
                        <option value="">Entire College / Institution</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>{{ $program->title }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Leave empty if this accreditation applies to the entire college.</small>
                    @error('program_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="agency" class="form-label">Accrediting Agency <span class="text-danger">*</span></label>
                            <input type="text" name="agency" id="agency" class="form-control @error('agency') is-invalid @enderror" value="{{ old('agency') }}" placeholder="e.g. CHED, AACCUP" required>
                            @error('agency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="level" class="form-label">Level / Status <span class="text-danger">*</span></label>
                            <input type="text" name="level" id="level" class="form-control @error('level') is-invalid @enderror" value="{{ old('level') }}" placeholder="e.g. Level IV Re-accredited" required>
                            @error('level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="valid_until" class="form-label">Valid Until</label>
                    <input type="date" name="valid_until" id="valid_until" class="form-control @error('valid_until') is-invalid @enderror" value="{{ old('valid_until') }}">
                    @error('valid_until')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="logo" class="form-label">Agency / Program Logo</label>
                    


                    <div class="d-flex flex-column gap-2 mt-2">
                        <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror">
                    </div>
                    <small class="text-muted">Recommended: Square image, transparent PNG or SVG.</small>
                    @error('logo')
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
                    <button type="submit" class="btn btn-admin-primary">Add Accreditation</button>
                    @if (!empty($fromCollegeSection) && !empty($collegeSlug))
                        <a href="{{ route('admin.colleges.show', ['college' => $collegeSlug, 'section' => 'accreditation']) }}" class="btn btn-outline-secondary">Cancel</a>
                    @else
                        <a href="{{ route('admin.colleges.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    @endif
                </div>
            </form>
        </div>
    </div>


@endsection
