@extends('admin.layout')

@section('title', 'Edit Institute')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">Edit Institute</h1>
        @if (!empty($returnCollege))
            <a href="{{ route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'institutes']) }}" class="btn btn-outline-secondary">Back to {{ \App\Http\Controllers\Admin\CollegeController::getColleges()[$returnCollege] ?? $returnCollege }}</a>
        @else
            <a href="{{ route('admin.institutes.index') }}" class="btn btn-outline-secondary">Back to list</a>
        @endif
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.institutes.update', $institute) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @if (!empty($returnCollege))
                    <input type="hidden" name="return_college" value="{{ $returnCollege }}">
                @endif
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Institute name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $institute->name) }}" required>
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
                                <option value="{{ $slug }}" {{ old('college_slug', $institute->college_slug) === $slug ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <label for="sort_order" class="form-label">Sort order</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $institute->sort_order) }}" min="0">
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control quill-editor @error('description') is-invalid @enderror" rows="3">{{ old('description', $institute->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <label class="form-label d-block">Photo</label>
                        <input type="file" name="photo" id="photo" accept="image/*" class="d-none">
                        <div id="photo-zone" class="faculty-photo-zone border border-2 border-dashed rounded overflow-hidden bg-light d-flex align-items-center justify-content-center position-relative" style="width: 200px; height: 200px; cursor: pointer;">
                            <img id="photo-preview" src="{{ $institute->logo ? \App\Providers\AppServiceProvider::resolveImageUrl($institute->logo) : \App\Providers\AppServiceProvider::resolveImageUrl($institute->photo, 'images') }}" alt="Preview" class="w-100 h-100 position-absolute top-0 start-0 {{ ($institute->logo || $institute->photo) ? '' : 'd-none' }}" style="object-fit: contain;">
                            <div id="photo-placeholder" class="text-muted small text-center px-2 {{ ($institute->logo || $institute->photo) ? 'd-none' : '' }}">
                                Drop image here<br>or click to choose
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1">Optional. JPG, PNG or GIF, max 2MB. Leave empty to keep current.</small>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-admin-primary">Update Institute</button>
                        @if (!empty($returnCollege))
                            <a href="{{ route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'institutes']) }}" class="btn btn-outline-secondary">Cancel</a>
                        @else
                            <a href="{{ route('admin.institutes.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
            var initialSrc = previewImg.src || '';

            function showPreview(file) {
                if (!file || !file.type || !file.type.startsWith('image/')) return;
                var prev = URL.createObjectURL(file);
                previewImg.onload = function () { URL.revokeObjectURL(prev); };
                previewImg.src = prev;
                previewImg.classList.remove('d-none');
                placeholder.classList.add('d-none');
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
