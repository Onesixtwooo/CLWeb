@extends('admin.layout')

@section('title', 'Edit Announcement')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">Edit Announcement</h1>
        <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" enctype="multipart/form-data" id="announcementForm">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-12">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $announcement->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @if (auth()->user()?->isSuperAdmin())
                    <div class="col-md-6">
                        <label for="college_slug" class="form-label">Department</label>
                        <select name="college_slug" id="college_slug" class="form-select">
                            <option value="">All departments</option>
                            @foreach ($colleges as $slug => $name)
                                <option value="{{ $slug }}" {{ old('college_slug', $announcement->college_slug) === $slug ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <label for="published_at" class="form-label">Published at</label>
                        <input type="datetime-local" name="published_at" id="published_at" class="form-control" value="{{ old('published_at', $announcement->published_at?->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="author" class="form-label">Author</label>
                        <input type="text" name="author" id="author" class="form-control @error('author') is-invalid @enderror" value="{{ old('author', $announcement->author ?? auth()->user()?->name) }}">
                        @error('author')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="body" class="form-label">Body (HTML allowed)</label>
                        <textarea name="body" id="body" class="form-control quill-editor @error('body') is-invalid @enderror" rows="10">{{ old('body', $announcement->body) }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Banner Image (Optional)</label>

                        @php
                            $currentImages = $announcement->images ?? ($announcement->image ? [$announcement->image] : []);
                        @endphp

                        {{-- Current images from database --}}
                        @if(count($currentImages) > 0)
                            <div class="mb-3">
                                <label class="form-label small text-muted">Current Image:</label>
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
                                        Clear existing image
                                    </label>
                                </div>
                            </div>
                        @endif

                        <input type="file" name="banner[]" id="banner" class="form-control form-control-sm mt-2" accept="image/*">
                        <div class="form-text">Choose an image for this announcement.</div>
                        @error('banner')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="hidden" name="banner_dark" value="0">
                            <input type="checkbox" name="banner_dark" id="banner_dark" value="1" class="form-check-input" {{ old('banner_dark', $announcement->banner_dark) ? 'checked' : '' }}>
                            <label for="banner_dark" class="form-check-label">Banner dark overlay</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-admin-primary">Update Announcement</button>
                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('announcementForm').addEventListener('submit', function(e) {
            if (this.checkValidity()) {
                window.showAdminLoading(
                    'Updating announcement...',
                    'We\'re organizing your files and uploading them to Google Drive. This may take a moment depending on the image sizes.'
                );
            }
        });
    </script>
@endsection
