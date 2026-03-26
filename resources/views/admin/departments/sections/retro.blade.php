<input type="hidden" name="_retro_edit" value="1">
{{-- RETRO ONLY FIELDS --}}
<div class="col-12">
    <label for="retro_title" class="form-label">Retro Title</label>
    <input type="text" name="retro_title" id="retro_title" class="form-control @error('retro_title') is-invalid @enderror" value="{{ old('retro_title', $content['retro_title'] ?? '') }}">
    <small class="text-muted">Title for the hero overlay section.</small>
    @error('retro_title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-12">
    <label for="retro_description" class="form-label">Retro Description</label>
    <textarea name="retro_description" id="retro_description" class="form-control quill-editor @error('retro_description') is-invalid @enderror" rows="4">{{ old('retro_description', $content['retro_description'] ?? '') }}</textarea>
    <small class="text-muted">Description for the hero overlay section.</small>
    @error('retro_description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-12">
    <label for="retro_stamp" class="form-label">Retro Stamp</label>
    <input type="text" name="retro_stamp" id="retro_stamp" class="form-control @error('retro_stamp') is-invalid @enderror" value="{{ old('retro_stamp', $content['retro_stamp'] ?? '') }}">
    <small class="text-muted">Stamp text for the hero overlay (e.g., year or badge text).</small>
    @error('retro_stamp')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-12">
    <label for="hero_background_image" class="form-label">Hero Background Image</label>
    @if (!empty($content['hero_background_image']))
        <div class="mb-2">
            <img src="{{ $content['hero_background_image'] }}" alt="Hero Background" class="img-fluid rounded" style="max-width: 300px; max-height: 200px; object-fit: cover;">
        </div>
    @endif
    <input type="file" name="hero_background_image" id="hero_background_image" class="form-control @error('hero_background_image') is-invalid @enderror" accept="image/*">
    <small class="text-muted">Upload a background image for the hero overlay. Leave empty to keep current image.</small>
    @error('hero_background_image')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
