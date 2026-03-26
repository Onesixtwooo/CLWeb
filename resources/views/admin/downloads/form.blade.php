@extends('admin.layout')

@section('title', ($download ? 'Edit' : 'New') . ' Download - ' . $collegeName)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="admin-page-title mb-1">{{ $download ? 'Edit Download' : 'New Download' }}</h1>
            <p class="text-muted small mb-0">{{ $collegeName }}</p>
        </div>
        <a href="{{ route('admin.colleges.show', ['college' => $college, 'section' => 'downloads']) }}" class="btn btn-outline-secondary">Back to Downloads</a>
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST"
                  action="{{ $download
                      ? route('admin.colleges.downloads.update', ['college' => $college, 'download' => $download])
                      : route('admin.colleges.downloads.store', ['college' => $college]) }}"
                  enctype="multipart/form-data">
                @csrf
                @if ($download)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $download?->title ?? '') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" rows="6" class="form-control @error('description') is-invalid @enderror">{{ old('description', $download?->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="file" class="form-label">File {{ $download ? '' : '*' }}</label>
                            <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" accept=".pdf,.docx,.xlsx" {{ $download ? '' : 'required' }}>
                            <small class="text-muted d-block mt-2">Allowed: PDF, DOCX, XLSX. Max 10MB.</small>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($download)
                            <div class="rounded border bg-light p-3 small">
                                <div class="fw-600 text-dark mb-1">Current file</div>
                                <div class="text-break">{{ $download->file_name }}</div>
                                <div class="text-muted mt-1">{{ number_format(($download->file_size ?? 0) / 1024, 1) }} KB</div>
                            </div>
                        @endif

                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="is_visible" id="is_visible" value="1" {{ old('is_visible', $download?->is_visible ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_visible">
                                Visible on public page
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <hr class="my-2">
                        <button type="submit" class="btn btn-admin-primary">
                            {{ $download ? 'Save Changes' : 'Add Download' }}
                        </button>
                        <a href="{{ route('admin.colleges.show', ['college' => $college, 'section' => 'downloads']) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
