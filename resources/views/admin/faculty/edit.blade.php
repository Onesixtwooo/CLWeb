@extends('admin.layout')

@section('title', 'Edit Faculty')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">Edit Faculty</h1>
        @if (!empty($returnCollege) && !empty($returnDepartment))
            <a href="{{ route('admin.colleges.show-department', ['college' => $returnCollege, 'department' => $returnDepartment, 'section' => 'faculty']) }}" class="btn btn-outline-secondary">Back to Department</a>
        @elseif (!empty($returnCollege))
            <a href="{{ route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'faculty']) }}" class="btn btn-outline-secondary">Back to {{ \App\Http\Controllers\Admin\CollegeController::getColleges()[$returnCollege] ?? $returnCollege }}</a>
        @else
            <a href="{{ route('admin.faculty.index') }}" class="btn btn-outline-secondary">Back to list</a>
        @endif
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">
            <div class="admin-card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.faculty.update', $faculty) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @if (!empty($returnCollege))
                            <input type="hidden" name="return_college" value="{{ $returnCollege }}">
                        @endif
                        @if (!empty($returnDepartment))
                            <input type="hidden" name="return_department" value="{{ $returnDepartment }}">
                        @endif
                        <div class="row g-4">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ old('name', $faculty->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Position</label>
                                    <input type="text" name="position" id="position" class="form-control form-control-sm @error('position') is-invalid @enderror" value="{{ old('position', $faculty->position) }}">
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row g-3 mb-3">
                                    @if (auth()->user()?->isSuperAdmin())
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">College</label>
                                            <select name="college_slug" id="college_slug" class="form-select form-select-sm">
                                                <option value="">Select College First</option>
                                                @foreach ($colleges as $slug => $name)
                                                    <option value="{{ $slug }}" {{ old('college_slug', $faculty->college_slug) === $slug ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <input type="hidden" name="college_slug" id="college_slug" value="{{ $faculty->college_slug }}">
                                    @endif

                                    <div class="col-md-{{ auth()->user()?->isSuperAdmin() ? '6' : '12' }}">
                                        <label class="form-label small fw-bold">Department</label>
                                        <select name="department" id="department" class="form-select form-select-sm @error('department') is-invalid @enderror">
                                            <option value="">Select Department</option>
                                            @foreach ($departments as $dept)
                                                <option value="{{ $dept->name }}" data-college="{{ $dept->college_slug }}" {{ old('department', $faculty->department) === $dept->name ? 'selected' : '' }}>
                                                    {{ $dept->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('department')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Sort order <span class="text-muted fw-normal">(optional)</span></label>
                                    <input type="number" name="sort_order" id="sort_order" class="form-control form-control-sm @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $faculty->sort_order) }}" min="0">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-4 pt-2 border-top">
                                    <button type="submit" class="btn btn-primary btn-sm px-4">Update Faculty</button>
                                    @if (!empty($returnCollege) && !empty($returnDepartment))
                                        <a href="{{ route('admin.colleges.show-department', ['college' => $returnCollege, 'department' => $returnDepartment, 'section' => 'faculty']) }}" class="btn btn-outline-secondary btn-sm px-4 ms-2">Cancel</a>
                                    @elseif (!empty($returnCollege))
                                        <a href="{{ route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'faculty']) }}" class="btn btn-outline-secondary btn-sm px-4 ms-2">Cancel</a>
                                    @else
                                        <a href="{{ route('admin.faculty.index') }}" class="btn btn-outline-secondary btn-sm px-4 ms-2">Cancel</a>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light h-100">
                                    <label class="form-label small fw-bold d-block mb-3">Profile Photo</label>
                                    <div class="photo-upload-container text-center">
                                        <div class="mb-3 mx-auto overflow-hidden bg-white border d-flex align-items-center justify-content-center" style="width: 150px; height: 180px; border-radius: 8px;">
                                            <img id="photo-preview" src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($faculty->photo, 'images') }}" class="w-100 h-100 {{ $faculty->photo ? '' : 'd-none' }}" style="object-fit: cover;">
                                            <div id="photo-placeholder" class="text-muted {{ $faculty->photo ? 'd-none' : '' }}">
                                                <i class="bi bi-person h1"></i>
                                                <div class="small">No Image</div>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <input type="file" name="photo" id="photo" class="form-control form-control-sm @error('photo') is-invalid @enderror" accept="image/*">
                                        </div>

                                        <small class="text-muted d-block mt-2">Select a new image to replace the current one. Leave empty to keep it.</small>
                                        <small class="text-primary d-block mt-1 fw-bold">To fit on the page, please use a 2x2 image.</small>
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
            // Department filtering
            const collegeSelect = document.getElementById('college_slug');
            const departmentSelect = document.getElementById('department');
            const allDepartments = Array.from(departmentSelect.options);

            function filterDepartments() {
                const selectedCollege = collegeSelect ? (collegeSelect.value || collegeSelect.getAttribute('value')) : document.getElementById('college_slug').value;
                
                // Reset selection if current selection is hidden
                const currentDept = departmentSelect.value;
                let isCurrentValid = false;
                
                departmentSelect.innerHTML = '';
                
                // Always add empty option
                if (allDepartments.length > 0) {
                    departmentSelect.appendChild(allDepartments[0]);
                }

                allDepartments.forEach(opt => {
                    if (opt.value === '') return; // Skip placeholder
                    
                    const deptCollege = opt.getAttribute('data-college');
                    if (!selectedCollege || deptCollege === selectedCollege) {
                        departmentSelect.appendChild(opt);
                        if (opt.value === currentDept) isCurrentValid = true;
                    }
                });

                if (isCurrentValid) {
                    departmentSelect.value = currentDept;
                } else {
                    departmentSelect.value = '';
                }
            }

            if (collegeSelect && collegeSelect.tagName === 'SELECT') {
                collegeSelect.addEventListener('change', filterDepartments);
            }
            
            // Initial filter
            filterDepartments();

            // Photo upload preview (simple)
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
                    placeholder.classList.add('d-none');
                } else if (initialSrc) {
                    previewImg.src = initialSrc;
                    previewImg.classList.remove('d-none');
                    placeholder.classList.add('d-none');
                } else {
                    previewImg.src = '';
                    previewImg.classList.add('d-none');
                    placeholder.classList.remove('d-none');
                }
            });
        })();
    </script>
    @endpush
@endsection
