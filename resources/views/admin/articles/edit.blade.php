@extends('admin.layout')

@section('title', 'Edit Article')

@push('styles')
<style>

</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">Edit Article</h1>
        <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.articles.update', $article) }}" enctype="multipart/form-data" id="articleForm">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $article->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="slug" class="form-label">URL slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug', $article->slug) }}" required>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="news" {{ old('type', $article->type) === 'news' ? 'selected' : '' }}>News</option>
                            <option value="announcement" {{ old('type', $article->type) === 'announcement' ? 'selected' : '' }}>Announcement</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @if (auth()->user()?->isSuperAdmin())
                    <div class="col-md-4">
                        <label for="college_slug" class="form-label">College</label>
                        <select name="college_slug" id="college_slug" class="form-select @error('college_slug') is-invalid @enderror">
                            <option value="">-- Select college --</option>
                            @foreach ($colleges as $slug => $name)
                                <option value="{{ $slug }}" {{ old('college_slug', $article->college_slug) === $slug ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('college_slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="department_name" class="form-label">Department</label>
                        <select name="department_name" id="department_name" class="form-select @error('department_name') is-invalid @enderror" disabled>
                            <option value="">-- Select department --</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->name }}" data-college="{{ $department->college_slug }}" {{ old('department_name', $article->department_name) === $department->name ? 'selected' : '' }}>
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
                    <div class="col-md-4">
                        <label class="form-label">Department</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->department }}" readonly>
                    </div>
                    @elseif (auth()->user()?->isBoundedToCollege())
                    <div class="col-md-4">
                        <label for="department_name" class="form-label">Department</label>
                        <select name="department_name" id="department_name" class="form-select @error('department_name') is-invalid @enderror">
                            <option value="">-- Select department --</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->name }}" data-college="{{ $department->college_slug }}" {{ old('department_name', $article->department_name) === $department->name ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                    <div class="col-md-4">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" name="category" id="category" class="form-control" value="{{ old('category', $article->category) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="author" class="form-label">Author</label>
                        <input type="text" name="author" id="author" class="form-control" value="{{ old('author', $article->author) }}">
                    </div>
                    <div class="col-12">
                        <label for="body" class="form-label">Body (Rich Text)</label>
                        <textarea name="body" id="body" class="form-control quill-editor" rows="12">{!! old('body', $article->body) !!}</textarea>
                        @error('body')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Banner Images (Select 5+ for slideshow)</label>

                        @php
                            $currentImages = $article->images ?? ($article->banner ? [$article->banner] : []);
                        @endphp

                        @if(count($currentImages) > 0)
                            <div class="mb-3">
                                <label class="form-label small text-muted">Current Images:</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($currentImages as $img)
                                        @php
                                            preg_match('/[?&]id=([^&]+)/', $img, $_em);
                                            $_thumbSrc = isset($_em[1])
                                                ? route('media.proxy.public', ['fileId' => $_em[1]])
                                                : $img;
                                        @endphp
                                        <div class="position-relative" style="width: 100px; height: 100px;">
                                            <img src="{{ $_thumbSrc }}" alt="Banner" class="w-100 h-100 object-fit-cover rounded border">
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="clear_images" id="clear_images" value="1">
                                    <label class="form-check-label text-danger" for="clear_images">
                                        Clear existing images
                                    </label>
                                </div>
                            </div>
                        @endif

                        <input type="file" name="banner[]" id="banner" class="form-control form-control-sm mt-2" accept="image/*" multiple>
                        <div class="form-text">First image is used as main banner. Upload one or more images directly.</div>
                        @error('banner')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @error('banner.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="published_at" class="form-label">Published at</label>
                        <input type="datetime-local" name="published_at" id="published_at" class="form-control"
                               value="{{ old('published_at', $article->published_at?->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="hidden" name="banner_dark" value="0">
                            <input type="checkbox" name="banner_dark" id="banner_dark" value="1" class="form-check-input"
                                   {{ old('banner_dark', $article->banner_dark) ? 'checked' : '' }}>
                            <label for="banner_dark" class="form-check-label">Banner dark overlay</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-admin-primary">Update Article</button>
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        function filterArticleDepartments() {
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

        document.getElementById('college_slug')?.addEventListener('change', filterArticleDepartments);
        document.addEventListener('DOMContentLoaded', filterArticleDepartments);

        document.getElementById('articleForm').addEventListener('submit', function(e) {
            if (this.checkValidity()) {
                window.history.replaceState({}, '', '{{ route('admin.articles.index') }}');
                window.showAdminLoading(
                    'Updating your article...',
                    'We\'re organizing your files and uploading them to Google Drive. This may take a moment depending on the image sizes.'
                );
            }
        });
    </script>
@endsection
