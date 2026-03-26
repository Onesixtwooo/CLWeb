<input type="hidden" name="_banner_edit" value="1">
{{-- BANNER ONLY FIELDS --}}
<div class="col-12">
    <label class="form-label">Banner Images (Max 3)</label>
    
    {{-- Display Existing Images --}}
    @php
        $bannerImages = $content['banner_images'] ?? [];
        if (empty($bannerImages) && !empty($content['banner_image'])) {
            $bannerImages[] = $content['banner_image'];
        }
    @endphp

    @if (!empty($bannerImages))
        <div class="row g-3 mb-3">
            @foreach($bannerImages as $index => $img)
                <div class="col-md-4">
                    <div class="position-relative">
                        <img src="{{ $img }}" alt="Banner {{ $index + 1 }}" class="img-fluid rounded border" style="width: 100%; height: 150px; object-fit: cover;">
                        <button type="submit" name="delete_banner_image" value="{{ $index }}" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" onclick="return confirm('Delete this banner image?')" title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Upload Input (Show only if count < 3) --}}
    @if (count($bannerImages) < 3)
        <label for="banner_image" class="form-label">Add Banner Image</label>
        <input type="file" name="banner_image" id="banner_image" class="form-control @error('banner_image') is-invalid @enderror" accept="image/*">
        <small class="text-muted">Upload a banner image. Recommended size: 1200x300 pixels. remaining: {{ 3 - count($bannerImages) }}</small>
        @error('banner_image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    @else
        <div class="alert alert-info py-2">
            <small>Maximum of 3 banner images reached. Delete an image to upload a new one.</small>
        </div>
    @endif
</div>
