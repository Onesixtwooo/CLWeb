@extends('admin.layout')

@section('title', 'Add Facility')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">Add Facility</h1>
        @if (!empty($fromCollegeSection) && !empty($collegeSlug))
            <a href="{{ route('admin.colleges.show', ['college' => $collegeSlug, 'section' => 'facilities']) }}" class="btn btn-outline-secondary">Back to {{ \App\Http\Controllers\Admin\CollegeController::getColleges()[$collegeSlug] ?? $collegeSlug }}</a>
        @else
            <a href="{{ route('admin.facilities.index') }}" class="btn btn-outline-secondary">Back to list</a>
        @endif
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.facilities.store') }}" enctype="multipart/form-data">
                @csrf
                @if (!empty($collegeSlug))
                    <input type="hidden" name="return_college" value="{{ $collegeSlug }}">
                @endif
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Facility name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @if (auth()->user()?->isSuperAdmin())
                    <div class="col-md-6">
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
                    <div class="col-md-6">
                        <label for="department_name" class="form-label">Department</label>
                        <select name="department_name" id="department_name" class="form-select">
                            <option value="">No specific department</option>
                            @if(!empty($departments))
                                @foreach ($departments as $deptName)
                                    <option value="{{ $deptName }}" {{ old('department_name') === $deptName ? 'selected' : '' }}>{{ $deptName }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="sort_order" class="form-label">Sort order</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control quill-editor @error('description') is-invalid @enderror" rows="3" placeholder="Brief description of this facility">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <label class="form-label d-block">Photo</label>
                        <div class="facility-photo-container">
                            <div id="photo-zone" class="faculty-photo-zone border border-2 border-dashed rounded overflow-hidden bg-light d-flex align-items-center justify-content-center position-relative mb-3" style="width: 200px; height: 200px; cursor: pointer;">
                                <img id="photo-preview" src="" alt="Preview" class="w-100 h-100 position-absolute top-0 start-0 d-none" style="object-fit: cover;">
                                <div id="photo-placeholder" class="text-muted small text-center px-2">
                                    Drop image here<br>or click to choose
                                </div>
                            </div>

                            <input type="file" name="photo" id="photo" accept="image/*" class="form-control">
                            <small class="text-muted d-block mt-1">Optional. JPG, PNG or GIF, max 2MB.</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-admin-primary">Add Facility</button>
                        @if (!empty($fromCollegeSection) && !empty($collegeSlug))
                            <a href="{{ route('admin.colleges.show', ['college' => $collegeSlug, 'section' => 'facilities']) }}" class="btn btn-outline-secondary">Cancel</a>
                        @else
                            <a href="{{ route('admin.facilities.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>



    @push('scripts')
    <script>
        (function () {
            var photoInput = document.getElementById('photo');
            var zone = document.getElementById('photo-zone');
            var previewImg = document.getElementById('photo-preview');
            var placeholder = document.getElementById('photo-placeholder');
            var mediaPickerPreviews = document.getElementById('mediaPickerPreviews');

            function showPreview(file) {
                if (!file || !file.type || !file.type.startsWith('image/')) return;
                var prev = URL.createObjectURL(file);
                previewImg.onload = function () { URL.revokeObjectURL(prev); };
                previewImg.src = prev;
                previewImg.classList.remove('d-none');
                placeholder.classList.add('d-none');

                // Clear media picker selection if file is chosen
                if (mediaPickerPreviews) mediaPickerPreviews.innerHTML = '';
                const inputsContainer = document.getElementById('mediaPickerInputs');
                if (inputsContainer) inputsContainer.innerHTML = '';
                const selectedContainer = document.getElementById('mediaPickerSelected');
                if (selectedContainer) selectedContainer.style.display = 'none';
            }

            function clearPreview() {
                if (previewImg.src) URL.revokeObjectURL(previewImg.src);
                previewImg.src = '';
                previewImg.classList.add('d-none');
                placeholder.classList.remove('d-none');
            }

            zone.addEventListener('click', function (e) { if (e.target !== photoInput) photoInput.click(); });
            zone.addEventListener('dragover', function (e) { e.preventDefault(); e.stopPropagation(); zone.classList.add('border-admin'); });
            zone.addEventListener('dragleave', function (e) { e.preventDefault(); zone.classList.remove('border-admin'); });
            zone.addEventListener('drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
                zone.classList.remove('border-admin');
                var file = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
                if (file && file.type && file.type.startsWith('image/')) {
                    var dt = new DataTransfer();
                    dt.items.add(file);
                    photoInput.files = dt.files;
                    showPreview(file);
                }
            });

            photoInput.addEventListener('change', function () {
                var file = this.files && this.files[0];
                if (file) showPreview(file);
                else clearPreview();
            });


        })();
    </script>
    @endpush
@endsection
