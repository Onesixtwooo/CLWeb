@extends('admin.layout')

@section('title', 'New Article')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">New Article</h1>
        <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data" id="articleForm">
                @csrf
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="slug" class="form-label">URL slug <span class="text-secondary">(Auto-generated)</span></label>
                        <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug') }}" placeholder="Leave empty to auto-generate">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="news" {{ old('type') === 'news' ? 'selected' : '' }}>News</option>
                            <option value="announcement" {{ old('type') === 'announcement' ? 'selected' : '' }}>Announcement</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @if (auth()->user()?->isSuperAdmin())
                    <div class="col-md-4">
                        <label for="college_slug" class="form-label">Department</label>
                        <select name="college_slug" id="college_slug" class="form-select @error('college_slug') is-invalid @enderror">
                            <option value="">— Select department —</option>
                            @foreach ($colleges as $slug => $name)
                                <option value="{{ $slug }}" {{ old('college_slug') === $slug ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('college_slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                    <div class="col-md-4">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" name="category" id="category" class="form-control" value="{{ old('category') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="author" class="form-label">Author</label>
                        <input type="text" name="author" id="author" class="form-control" value="{{ old('author') }}">
                    </div>
                    <div class="col-12">
                        <label for="body" class="form-label">Body (Rich Text)</label>
                        <textarea name="body" id="body" class="form-control quill-editor" rows="12">{!! old('body') !!}</textarea>
                        @error('body')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="banner" class="form-label">Banner Images (Select 5+ for slideshow)</label>
                        
                        {{-- Media picker selected images --}}
                        <div id="mediaPickerSelected" style="display: none;">
                            <label class="form-label small text-muted">Selected from Media Library:</label>
                            <div class="mp-selected-previews" id="mediaPickerPreviews"></div>
                        </div>

                        {{-- Hidden inputs for media picker selections --}}
                        <div id="mediaPickerInputs"></div>

                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="openMediaPicker">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                Media Library
                            </button>
                            <span class="text-muted small d-flex align-items-center">or upload directly:</span>
                            <input type="file" name="banner[]" id="banner" class="form-control form-control-sm" accept="image/*" multiple style="max-width: 300px;">
                        </div>
                        <div class="form-text">First image is used as main banner. Use Media Library to pick existing images, or upload new ones.</div>
                        @error('banner')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="published_at" class="form-label">Published at</label>
                        <input type="datetime-local" name="published_at" id="published_at" class="form-control" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="hidden" name="banner_dark" value="0">
                            <input type="checkbox" name="banner_dark" id="banner_dark" value="1" class="form-check-input" {{ old('banner_dark') ? 'checked' : '' }}>
                            <label for="banner_dark" class="form-check-label">Banner dark overlay</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-admin-primary">Create Article</button>
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('partials.media-picker')
    <script>
        document.getElementById('articleForm').addEventListener('submit', function(e) {
            if (this.checkValidity()) {
                window.showAdminLoading(
                    'Creating your article...',
                    'We\'re organizing your files and uploading them to Google Drive. This may take a moment depending on the image sizes.'
                );
            }
        });
    </script>
@endsection
