<input type="hidden" name="_overview_edit" value="1">
{{-- OVERVIEW ONLY FIELDS --}}
<div class="col-12">
    <label for="logo" class="form-label">Department Logo</label>
    @if (!empty($department->logo))
        <div class="mb-2">
            <img src="{{ $department->logo }}" alt="Current Logo" class="img-fluid rounded" style="max-width: 150px; max-height: 150px; object-fit: contain;">
        </div>
    @endif
    <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
    <small class="text-muted">Upload a new logo to replace the current one. Recommended size: 500x500 pixels.</small>
    @error('logo')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-12">
    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $content['title']) }}" required>
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-12">
    <label for="body" class="form-label">Body</label>
    <textarea name="body" id="body" class="form-control quill-editor @error('body') is-invalid @enderror" rows="10">{{ old('body', $content['body'] ?? '') }}</textarea>
    <small class="text-muted">You can use HTML for formatting.</small>
    @error('body')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-12">
    <hr class="my-4">
    <h5 class="h6 fw-600 mb-3">Contact Information (Footer)</h5>
    <small class="text-muted d-block mb-3">These details will be displayed in the department footer. If left empty, the college's contact information will be used as a fallback.</small>
</div>

<div class="col-md-6">
    <label for="email" class="form-label">Contact Email</label>
    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $content['email'] ?? '') }}" placeholder="dept@clsu.edu.ph">
    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-6">
    <label for="phone" class="form-label">Contact Phone</label>
    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $content['phone'] ?? '') }}" placeholder="(044) 940 1234">
    @error('phone')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-12">
    <hr class="my-4">
    <h5 class="h6 fw-600 mb-3">Social Media Links (Footer)</h5>
    <small class="text-muted d-block mb-3">Add social media links to display in the footer. Leave URL empty to hide the link.</small>
</div>

<div class="col-md-6">
    <label for="social_facebook" class="form-label">Facebook URL</label>
    <input type="url" name="social_facebook" id="social_facebook" class="form-control @error('social_facebook') is-invalid @enderror" value="{{ old('social_facebook', $content['social_facebook'] ?? '') }}" placeholder="https://facebook.com/yourpage">
    @error('social_facebook')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-6">
    <label for="social_x" class="form-label">X (Twitter) URL</label>
    <input type="url" name="social_x" id="social_x" class="form-control @error('social_x') is-invalid @enderror" value="{{ old('social_x', $content['social_x'] ?? '') }}" placeholder="https://x.com/yourhandle">
    @error('social_x')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-6">
    <label for="social_youtube" class="form-label">YouTube URL</label>
    <input type="url" name="social_youtube" id="social_youtube" class="form-control @error('social_youtube') is-invalid @enderror" value="{{ old('social_youtube', $content['social_youtube'] ?? '') }}" placeholder="https://youtube.com/@yourchannel">
    @error('social_youtube')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-6">
    <label for="social_instagram" class="form-label">Instagram URL</label>
    <input type="url" name="social_instagram" id="social_instagram" class="form-control @error('social_instagram') is-invalid @enderror" value="{{ old('social_instagram', $content['social_instagram'] ?? '') }}" placeholder="https://instagram.com/yourprofile">
    @error('social_instagram')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-12">
    <label class="form-label fw-600">Custom Links</label>
    <small class="text-muted d-block mb-2">Add additional custom social media or external links.</small>
    <div id="custom-links-container">
        @php
            $customLinks = old('custom_links', $content['custom_links'] ?? []);
        @endphp
        @if(is_array($customLinks) && count($customLinks) > 0)
            @foreach($customLinks as $index => $link)
                <div class="custom-link-row mb-2" data-index="{{ $index }}">
                    <div class="input-group">
                        <input type="url" name="custom_links[]" class="form-control" value="{{ $link }}" placeholder="https://example.com">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-link">×</button>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-custom-link">+ Add Custom Link</button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let linkIndex = {{ is_array($customLinks ?? []) ? count($customLinks ?? []) : 0 }};
    
    document.getElementById('add-custom-link').addEventListener('click', function() {
        const container = document.getElementById('custom-links-container');
        const newRow = document.createElement('div');
        newRow.className = 'custom-link-row mb-2';
        newRow.setAttribute('data-index', linkIndex++);
        newRow.innerHTML = `
            <div class="input-group">
                <input type="url" name="custom_links[]" class="form-control" placeholder="https://example.com">
                <button type="button" class="btn btn-outline-danger btn-sm remove-link">×</button>
            </div>
        `;
        container.appendChild(newRow);
    });
    
    document.getElementById('custom-links-container').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-link')) {
            e.target.closest('.custom-link-row').remove();
        }
    });
});
</script>
