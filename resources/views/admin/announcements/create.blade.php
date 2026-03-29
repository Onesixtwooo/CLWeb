@extends('admin.layout')

@section('title', 'Add Announcement')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">Add Announcement</h1>
        <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.announcements.store') }}" enctype="multipart/form-data" id="announcementForm">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @if (auth()->user()?->isSuperAdmin())
                    <div class="col-md-6">
                        <label for="college_slug" class="form-label">College</label>
                        <select name="college_slug" id="college_slug" class="form-select @error('college_slug') is-invalid @enderror">
                            <option value="">All colleges</option>
                            @foreach ($colleges as $slug => $name)
                                <option value="{{ $slug }}" {{ old('college_slug') === $slug ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('college_slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="department_name" class="form-label">Department</label>
                        <select name="department_name" id="department_name" class="form-select @error('department_name') is-invalid @enderror" disabled>
                            <option value="">-- Select department --</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->name }}" data-college="{{ $department->college_slug }}" {{ old('department_name') === $department->name ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @elseif (auth()->user()?->isBoundedToDepartment())
                    <input type="hidden" name="department_name" value="{{ auth()->user()->department }}">
                    <div class="col-md-6">
                        <label class="form-label">Department</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->department }}" readonly>
                    </div>
                    @elseif (auth()->user()?->isBoundedToCollege())
                    <div class="col-md-6">
                        <label for="department_name" class="form-label">Department</label>
                        <select name="department_name" id="department_name" class="form-select @error('department_name') is-invalid @enderror">
                            <option value="">-- Select department --</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->name }}" data-college="{{ $department->college_slug }}" {{ old('department_name') === $department->name ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                    <div class="col-md-6">
                        <label for="published_at" class="form-label">Published at</label>
                        <input type="datetime-local" name="published_at" id="published_at" class="form-control" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="author" class="form-label">Author</label>
                        <input type="text" name="author" id="author" class="form-control @error('author') is-invalid @enderror" value="{{ old('author', auth()->user()?->name) }}">
                        @error('author')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="body" class="form-label">Body (HTML allowed)</label>
                        <textarea name="body" id="body" class="form-control quill-editor @error('body') is-invalid @enderror" rows="10">{{ old('body') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Banner Image (Optional)</label>
                        <input type="file" name="banner[]" id="banner" class="form-control form-control-sm mt-2" accept="image/*">
                        <div class="form-text">Choose an image for this announcement.</div>
                        @error('banner')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="hidden" name="banner_dark" value="0">
                            <input type="checkbox" name="banner_dark" id="banner_dark" value="1" class="form-check-input" {{ old('banner_dark') ? 'checked' : '' }}>
                            <label for="banner_dark" class="form-check-label">Banner dark overlay</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-admin-primary">Create Announcement</button>
                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        function filterAnnouncementDepartments() {
            const collegeSelect = document.getElementById('college_slug');
            const departmentSelect = document.getElementById('department_name');

            if (!collegeSelect || !departmentSelect) {
                return;
            }

            const selectedCollege = collegeSelect.value;
            const currentDepartment = departmentSelect.value;
            let selectedOptionStillVisible = false;

            departmentSelect.disabled = !selectedCollege;

            Array.from(departmentSelect.options).forEach((option, index) => {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }

                const matchesCollege = !selectedCollege || option.dataset.college === selectedCollege;
                option.hidden = !matchesCollege;

                if (matchesCollege && option.value === currentDepartment) {
                    selectedOptionStillVisible = true;
                }
            });

            if (!selectedOptionStillVisible) {
                departmentSelect.value = '';
            }
        }

        document.getElementById('college_slug')?.addEventListener('change', filterAnnouncementDepartments);
        document.addEventListener('DOMContentLoaded', filterAnnouncementDepartments);

        document.getElementById('announcementForm').addEventListener('submit', function(e) {
            if (this.checkValidity()) {
                window.showAdminLoading(
                    'Creating announcement...',
                    'We\'re organizing your files and uploading them to Google Drive. This may take a moment depending on the image sizes.'
                );
            }
        });
    </script>
@endsection
