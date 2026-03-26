@extends('admin.layout')

@section('title', 'Edit Staff')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.colleges.index') }}">Colleges</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.colleges.show', $collegeSlug) }}">{{ $collegeSlug }}</a></li>
                    <li class="breadcrumb-item active">Edit Staff</li>
                </ol>
            </nav>
            <h1 class="admin-page-title mb-0">Edit Staff: {{ $instituteStaff->name }}</h1>
        </div>
        <a href="{{ route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'faculty']) }}" class="btn btn-outline-secondary">Back to Faculty Roster</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">
            <div class="admin-card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.institute-staff.update', $instituteStaff) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @if (!empty($returnCollege))
                            <input type="hidden" name="return_college" value="{{ $returnCollege }}">
                        @endif
                        
                        <div class="row g-4">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ old('name', $instituteStaff->name) }}" required placeholder="e.g. Juan De la Cruz">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Position</label>
                                    <input type="text" name="position" id="position" class="form-control form-control-sm @error('position') is-invalid @enderror" value="{{ old('position', $instituteStaff->position) }}" placeholder="e.g. Staff Member">
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Center / Institute <span class="text-muted fw-normal">(optional)</span></label>
                                    <select name="institute_id" id="institute_id" class="form-select form-select-sm @error('institute_id') is-invalid @enderror">
                                        <option value="">Not assigned</option>
                                        @foreach ($institutes as $availableInstitute)
                                            <option value="{{ $availableInstitute->id }}" {{ (string) old('institute_id', $instituteStaff->institute_id) === (string) $availableInstitute->id ? 'selected' : '' }}>
                                                {{ $availableInstitute->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('institute_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Sort order <span class="text-muted fw-normal">(optional)</span></label>
                                    <input type="number" name="sort_order" id="sort_order" class="form-control form-control-sm @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $instituteStaff->sort_order) }}" min="0">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-4 pt-2 border-top">
                                    <button type="submit" class="btn btn-primary btn-sm px-4">Update Staff</button>
                                    <a href="{{ route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'faculty']) }}" class="btn btn-outline-secondary btn-sm px-4 ms-2">Cancel</a>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light h-100">
                                    <label class="form-label small fw-bold d-block mb-3">Profile Photo</label>
                                    <div class="photo-upload-container text-center">
                                        <div class="mb-3 mx-auto overflow-hidden bg-white border d-flex align-items-center justify-content-center" style="width: 150px; height: 180px; border-radius: 8px;">
                                            <img id="photo-preview" src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($instituteStaff->photo, 'images') }}" class="w-100 h-100 {{ $instituteStaff->photo ? '' : 'd-none' }}" style="object-fit: cover;">
                                            <div id="photo-placeholder" class="text-muted {{ $instituteStaff->photo ? 'd-none' : '' }}">
                                                <i class="bi bi-person h1"></i>
                                                <div class="small">No Image</div>
                                            </div>
                                        </div>
                                        <input type="file" name="photo" id="photo" class="form-control form-control-sm @error('photo') is-invalid @enderror" accept="image/*">
                                        <small class="text-muted d-block mt-1">Select a new image to replace the current one. Leave empty to keep it.</small>
                                        @error('photo')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            // Photo upload preview
            const photoInput = document.getElementById('photo');
            const previewImg = document.getElementById('photo-preview');
            const placeholder = document.getElementById('photo-placeholder');
            const initialSrc = previewImg.src || '';

            photoInput.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (file && file.type && file.type.startsWith('image/')) {
                    const prev = URL.createObjectURL(file);
                    previewImg.onload = function () { URL.revokeObjectURL(prev); };
                    previewImg.src = prev;
                    previewImg.classList.remove('d-none');
                    if (placeholder) placeholder.classList.add('d-none');
                } else {
                    if (previewImg.src && previewImg.src !== initialSrc && previewImg.src.indexOf('blob:') === 0) {
                        URL.revokeObjectURL(previewImg.src);
                    }
                    previewImg.src = initialSrc;
                    if (!initialSrc) {
                        previewImg.classList.add('d-none');
                        if (placeholder) placeholder.classList.remove('d-none');
                    } else {
                        previewImg.classList.remove('d-none');
                        if (placeholder) placeholder.classList.add('d-none');
                    }
                }
            });
        })();
    </script>
    @endpush
@endsection
