<input type="hidden" name="_card_edit" value="1">
{{-- CARD IMAGE ONLY FIELDS --}}
<div class="col-12">
    <label for="card_image" class="form-label">Card Image</label>
    @if (!empty($content['card_image']))
        <div class="mb-2 position-relative d-inline-block">
            <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($content['card_image']) }}" alt="Card Image" class="img-fluid rounded" style="max-width: 100%; max-height: 400px; object-fit: cover;">
            <button type="submit" name="delete_card_image" value="1" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" onclick="return confirm('Remove this card image?')" title="Remove image">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
            </button>
        </div>
    @endif
    <input type="file" name="card_image" id="card_image" class="form-control @error('card_image') is-invalid @enderror" accept="image/*">
    <small class="text-muted">Upload a card image for the department. Recommended size: 800x600 pixels.</small>
    @error('card_image')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
