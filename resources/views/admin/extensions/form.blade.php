@extends('admin.layout')

@section('title', ($extension ? 'Edit' : 'New') . ' Extension Activity - ' . $collegeName)



@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="admin-page-title mb-1">{{ $extension ? 'Edit Extension Activity' : 'New Extension Activity' }}</h1>
            <p class="text-muted small mb-0">{{ $collegeName }}</p>
        </div>
        <a href="{{ route('admin.colleges.show', ['college' => $college, 'section' => 'extension']) }}" class="btn btn-outline-secondary">Back to List</a>
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST"
                  action="{{ $extension
                      ? route('admin.colleges.extensions.update', ['college' => $college, 'extension' => $extension->id])
                      : route('admin.colleges.extensions.store', ['college' => $college]) }}"
                  enctype="multipart/form-data">
                @csrf
                @if ($extension)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Activity Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $extension?->title ?? '') }}" placeholder="e.g., Community Outreach Program" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control quill-editor @error('description') is-invalid @enderror" rows="10"
                                      placeholder="Detailed overview of this extension activity...">{{ old('description', $extension?->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Featured Image</label>

                        {{-- Current image --}}
                        @if ($extension?->image)
                            <div class="mb-2" id="currentImageWrap">
                                <img src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($extension->image) }}" alt="Extension Image" class="img-fluid rounded border" style="max-height: 180px; width: 100%; object-fit: cover;">
                                <div class="form-text small text-muted">Current image</div>
                            </div>
                        @endif



                        <div class="d-flex flex-column gap-2 mt-2">
                            <input type="file" name="image" class="form-control form-control-sm @error('image') is-invalid @enderror" accept="image/*">
                        </div>
                        <small class="text-muted">Recommended: 16:9 aspect ratio. Max 2MB.</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <hr class="my-2">
                        <button type="submit" class="btn btn-admin-primary">
                            {{ $extension ? 'Save Changes' : 'Add Activity' }}
                        </button>
                        <a href="{{ route('admin.colleges.show', ['college' => $college, 'section' => 'extension']) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Shared Media Picker Modal --}}


@endsection

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
