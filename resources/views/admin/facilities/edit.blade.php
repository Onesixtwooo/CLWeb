@extends('admin.layout')

@section('title', 'Edit Facility')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">Edit Facility</h1>
        @if (!empty($returnCollege))
            <a href="{{ route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'facilities']) }}" class="btn btn-outline-secondary">Back to {{ \App\Http\Controllers\Admin\CollegeController::getColleges()[$returnCollege] ?? $returnCollege }}</a>
        @else
            <a href="{{ route('admin.facilities.index') }}" class="btn btn-outline-secondary">Back to list</a>
        @endif
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.facilities.update', $facility) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @if (!empty($returnCollege))
                    <input type="hidden" name="return_college" value="{{ $returnCollege }}">
                @endif
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Facility name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $facility->name) }}" required>
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
                                <option value="{{ $slug }}" {{ old('college_slug', $facility->college_slug) === $slug ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <label for="department_name" class="form-label">Department</label>
                        <select name="department_name" id="department_name" class="form-select">
                            <option value="">No specific department</option>
                            @if(!empty($departments))
                                @foreach ($departments as $deptName)
                                    <option value="{{ $deptName }}" {{ old('department_name', $facility->department_name) === $deptName ? 'selected' : '' }}>{{ $deptName }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="sort_order" class="form-label">Sort order</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $facility->sort_order) }}" min="0">
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control quill-editor @error('description') is-invalid @enderror" rows="3">{{ old('description', $facility->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <label class="form-label d-block">Photo</label>
                        <div class="facility-photo-container">
                            <div id="photo-zone-container" class="position-relative d-inline-block mb-3">
                                <div id="photo-zone" class="faculty-photo-zone border border-2 border-dashed rounded overflow-hidden bg-light d-flex align-items-center justify-content-center position-relative" style="width: 200px; height: 200px; cursor: pointer;">
                                    <img id="photo-preview" src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($facility->photo, 'images') }}" alt="Preview" class="w-100 h-100 position-absolute top-0 start-0 {{ $facility->photo ? '' : 'd-none' }}" style="object-fit: cover;">
                                    <div id="photo-placeholder" class="text-muted small text-center px-2 {{ $facility->photo ? 'd-none' : '' }}">
                                        Drop image here<br>or click to choose
                                    </div>
                                </div>
                                <button type="button" id="remove-photo-btn" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 {{ $facility->photo ? '' : 'd-none' }}" style="z-index: 10;" title="Remove Photo">Remove</button>
                                <input type="hidden" name="remove_photo" id="remove-photo-input" value="0">
                            </div>

                            <input type="file" name="photo" id="photo" accept="image/*" class="form-control">
                            <small class="text-muted d-block mt-1">Optional. JPG, PNG or GIF, max 2MB. Leave empty to keep current.</small>
                        </div>
                    </div>

                    <div class="col-12">
                        <hr class="my-4">
                        <h4 class="mb-3">Image Gallery Highlights</h4>
                        
                        <div class="mb-3">
                            <label class="form-label d-block">Add Images</label>
                            <input type="file" name="gallery[]" id="gallery" accept="image/*" multiple class="form-control">
                            <small class="text-muted">You can select multiple images to upload.</small>
                        </div>

                        @if($facility->images->count() > 0)
                            <div class="row g-3 mt-2">
                                @foreach($facility->images as $image)
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <div class="position-relative border rounded overflow-hidden">
                                            <div class="ratio ratio-1x1">
                                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($image->image_path, 'images') }}" alt="Gallery Image" class="object-fit-cover">
                                            </div>
                                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" 
                                                    onclick="if(confirm('Delete this image?')) document.getElementById('delete-image-{{ $image->id }}').submit();"
                                                    title="Delete Image">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Hidden forms for deleting images --}}
                    @foreach($facility->images as $image)
                    @endforeach
                    <div class="col-12">
                        <button type="submit" class="btn btn-admin-primary">Update Facility</button>
                        @if (!empty($returnCollege))
                            <a href="{{ route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'facilities']) }}" class="btn btn-outline-secondary">Cancel</a>
                        @else
                            <a href="{{ route('admin.facilities.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    @foreach($facility->images as $image)
        <form id="delete-image-{{ $image->id }}" action="{{ route('admin.facilities.images.destroy', $image->id) }}" method="POST" class="d-none">
            @csrf
            @method('DELETE')
        </form>
    @endforeach



    @push('scripts')
    <script>
        (function () {
            var photoInput = document.getElementById('photo');
            var zone = document.getElementById('photo-zone');
            var previewImg = document.getElementById('photo-preview');
            var placeholder = document.getElementById('photo-placeholder');
            var mediaPickerPreviews = document.getElementById('mediaPickerPreviews');
            var removeBtn = document.getElementById('remove-photo-btn');
            var removeInput = document.getElementById('remove-photo-input');
            var initialSrc = previewImg.src || '';

            if (removeBtn) {
                removeBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    if (confirm('Are you sure you want to remove the assigned photo?')) {
                        previewImg.src = '';
                        previewImg.classList.add('d-none');
                        placeholder.classList.remove('d-none');
                        removeInput.value = '1';
                        removeBtn.classList.add('d-none');
                        photoInput.value = '';
                        if (mediaPickerPreviews) mediaPickerPreviews.innerHTML = '';
                        const inputsContainer = document.getElementById('mediaPickerInputs');
                        if (inputsContainer) inputsContainer.innerHTML = '';
                        const selectedContainer = document.getElementById('mediaPickerSelected');
                        if (selectedContainer) selectedContainer.style.display = 'none';
                    }
                });
            }

            function showPreview(file) {
                if (!file || !file.type || !file.type.startsWith('image/')) return;
                var prev = URL.createObjectURL(file);
                previewImg.onload = function () { URL.revokeObjectURL(prev); };
                previewImg.src = prev;
                previewImg.classList.remove('d-none');
                placeholder.classList.add('d-none');
                if (removeInput) removeInput.value = '0';
                if (removeBtn) removeBtn.classList.remove('d-none');

                // Clear media picker selection
                if (mediaPickerPreviews) mediaPickerPreviews.innerHTML = '';
                const inputsContainer = document.getElementById('mediaPickerInputs');
                if (inputsContainer) inputsContainer.innerHTML = '';
                const selectedContainer = document.getElementById('mediaPickerSelected');
                if (selectedContainer) selectedContainer.style.display = 'none';
            }

            function clearPreview() {
                if (previewImg.src && previewImg.src !== initialSrc && previewImg.src.indexOf('blob:') === 0) URL.revokeObjectURL(previewImg.src);
                previewImg.src = initialSrc;
                if (!initialSrc) previewImg.classList.add('d-none');
                placeholder.classList.toggle('d-none', !!initialSrc);
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
