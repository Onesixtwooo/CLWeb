@extends('admin.layout')

@php
    $isGlobal = ($college === '_global');
    $routePrefix = $isGlobal ? 'admin.scholarships' : 'admin.colleges.scholarships';
    $routeParams = $isGlobal ? [] : ['college' => $college];
@endphp

@section('title', ($scholarship ? 'Edit' : 'New') . ' Scholarship - ' . ($isGlobal ? 'Global' : $collegeName))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="admin-page-title mb-1">{{ $scholarship ? 'Edit Scholarship' : 'New Scholarship' }}</h1>
            <p class="text-muted small mb-0">{{ $isGlobal ? 'Global (appears on all college pages)' : $collegeName }}</p>
        </div>
        <a href="{{ route($routePrefix . '.index', $routeParams) }}" class="btn btn-outline-secondary">Back to Scholarships</a>
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST"
                  action="{{ $scholarship
                      ? route($routePrefix . '.update', array_merge($routeParams, ['scholarship' => $scholarship->id]))
                      : route($routePrefix . '.store', $routeParams) }}"
                  enctype="multipart/form-data">
                @csrf
                @if ($scholarship)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Scholarship Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $scholarship?->title ?? '') }}" placeholder="e.g., Academic Excellence Scholarship" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control quill-editor @error('description') is-invalid @enderror" rows="3"
                                      placeholder="Brief overview of this scholarship...">{{ old('description', $scholarship?->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="qualifications" class="form-label">Qualifications</label>
                            <textarea name="qualifications" id="qualifications" class="form-control quill-editor @error('qualifications') is-invalid @enderror" rows="3"
                                      placeholder="Who is eligible for this scholarship...">{{ old('qualifications', $scholarship?->qualifications ?? '') }}</textarea>
                            <small class="text-muted">Use new lines for each qualification.</small>
                            @error('qualifications')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="requirements" class="form-label">Requirements</label>
                            <textarea name="requirements" id="requirements" class="form-control quill-editor @error('requirements') is-invalid @enderror" rows="3"
                                      placeholder="Documents or materials needed...">{{ old('requirements', $scholarship?->requirements ?? '') }}</textarea>
                            <small class="text-muted">Use new lines for each requirement.</small>
                            @error('requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="process" class="form-label">Application Process</label>
                            <textarea name="process" id="process" class="form-control quill-editor @error('process') is-invalid @enderror" rows="3"
                                      placeholder="Step-by-step application process...">{{ old('process', $scholarship?->process ?? '') }}</textarea>
                            <small class="text-muted">Use new lines for each step.</small>
                            @error('process')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="benefits" class="form-label">Benefits</label>
                            <textarea name="benefits" id="benefits" class="form-control quill-editor @error('benefits') is-invalid @enderror" rows="3"
                                      placeholder="What the scholar receives...">{{ old('benefits', $scholarship?->benefits ?? '') }}</textarea>
                            <small class="text-muted">Use new lines for each benefit.</small>
                            @error('benefits')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Featured Image</label>

                        @if ($scholarship?->image)
                            <div class="mb-2" id="currentImageWrap">
                                <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($scholarship->image) }}" alt="Scholarship Image" class="img-fluid rounded border" style="max-height: 180px; width: 100%; object-fit: cover;">
                                <div class="form-text small text-muted">Current image</div>
                            </div>
                        @endif

                        <input type="file" name="image" class="form-control form-control-sm @error('image') is-invalid @enderror" accept="image/*">
                        <small class="text-muted d-block mt-2">Recommended: 16:9 aspect ratio. Max 2MB.</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <hr class="my-2">
                        <button type="submit" class="btn btn-admin-primary">
                            {{ $scholarship ? 'Save Changes' : 'Create Scholarship' }}
                        </button>
                        <a href="{{ route($routePrefix . '.index', $routeParams) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
(function() {
    var form = document.querySelector('form[enctype="multipart/form-data"]');
    if (!form) return;

    form.addEventListener('submit', function() {
        form.querySelectorAll('textarea[data-quill-initialized]').forEach(function(textarea) {
            var container = textarea.nextElementSibling;
            if (!container) return;
            var qlRoot = container.querySelector('.ql-editor');
            if (!qlRoot) return;
            var html = qlRoot.innerHTML;
            textarea.value = (html === '<p><br></p>') ? '' : html;
            textarea.removeAttribute('disabled');
        });
    }, true);
})();
</script>
@endpush
@endsection
