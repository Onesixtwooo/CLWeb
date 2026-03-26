cul@extends('admin.layout')

@section('title', 'Edit Facebook Configuration')

@section('content')
<div class="admin-page-title">Edit Facebook Configuration</div>

<div class="admin-card" style="max-width: 600px;">
    <form action="{{ route('admin.facebook.update', $facebookConfig) }}" method="POST" class="p-4">
        @csrf
        @method('PUT')

        @if (auth()->user()->isBoundedToCollege())
            <input type="hidden" name="entity_type" value="college">
            <input type="hidden" name="entity_id" value="{{ auth()->user()->college_slug }}">
            <div class="mb-3">
                <label class="form-label">Configuring for</label>
                <div class="form-control-plaintext font-weight-bold" style="color: var(--admin-accent)">
                    @php
                        $slug = auth()->user()->college_slug;
                        echo \App\Http\Controllers\Admin\CollegeController::getColleges()[$slug] ?? $slug;
                    @endphp
                </div>
            </div>
        @else
            <div class="mb-3">
                <label for="entity_type" class="form-label">Entity Type</label>
                <select name="entity_type" id="entity_type" class="form-select @error('entity_type') is-invalid @enderror" required onchange="updateEntityField()">
                    <option value="">-- Select --</option>
                    <option value="global" {{ old('entity_type', $facebookConfig->entity_type) === 'global' ? 'selected' : '' }}>Global</option>
                    <option value="college" {{ old('entity_type', $facebookConfig->entity_type) === 'college' ? 'selected' : '' }}>College</option>
                    <option value="department" {{ old('entity_type', $facebookConfig->entity_type) === 'department' ? 'selected' : '' }}>Department</option>
                    <option value="organization" {{ old('entity_type', $facebookConfig->entity_type) === 'organization' ? 'selected' : '' }}>Student Organization</option>
                </select>
                @error('entity_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3" id="entity_id_field" style="display: none;">
                <label for="entity_id" class="form-label">Entity</label>
                <select name="entity_id" id="entity_id" class="form-select @error('entity_id') is-invalid @enderror">
                    <option value="">-- Select --</option>
                    @foreach ($colleges as $college)
                        <option value="{{ $college->slug }}" data-type="college" {{ old('entity_id', $facebookConfig->entity_id) === $college->slug ? 'selected' : '' }}>{{ $college->name }}</option>
                    @endforeach
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" data-type="department" {{ old('entity_id', $facebookConfig->entity_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                    @foreach ($organizations as $org)
                        <option value="{{ $org->id }}" data-type="organization" {{ old('entity_id', $facebookConfig->entity_id) == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                    @endforeach
                </select>
                @error('entity_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        @endif

        <div class="mb-3">
            <label for="page_name" class="form-label">Page Name</label>
            <input type="text" name="page_name" id="page_name" class="form-control @error('page_name') is-invalid @enderror" 
                   value="{{ old('page_name', $facebookConfig->page_name) }}" placeholder="e.g., College of Agriculture" required>
            @error('page_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="page_id" class="form-label">Facebook Page ID</label>
            <input type="text" name="page_id" id="page_id" class="form-control @error('page_id') is-invalid @enderror" 
                   value="{{ old('page_id', $facebookConfig->page_id) }}" placeholder="e.g., 123456789" required>
            @error('page_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="access_token" class="form-label">Access Token</label>
            <textarea name="access_token" id="access_token" class="form-control @error('access_token') is-invalid @enderror" 
                      rows="3" placeholder="Paste your Facebook page access token" required>{{ old('access_token', $facebookConfig->access_token) }}</textarea>
            <small class="text-muted"></small>
            @error('access_token') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="article_category" class="form-label">Article Category (Optional)</label>
            <input type="text" name="article_category" id="article_category" class="form-control" 
                   value="{{ old('article_category', $facebookConfig->article_category) }}" placeholder="e.g., College News">
        </div>

        <div class="mb-3">
            <label for="article_author" class="form-label">Article Author (Optional)</label>
            <input type="text" name="article_author" id="article_author" class="form-control" 
                   value="{{ old('article_author', $facebookConfig->article_author) }}" placeholder="e.g., College of Agriculture">
        </div>

        <div class="mb-3">
            <label for="fetch_limit" class="form-label">Posts to Fetch Per Run</label>
            <input type="number" name="fetch_limit" id="fetch_limit" class="form-control @error('fetch_limit') is-invalid @enderror" 
                   value="{{ old('fetch_limit', $facebookConfig->fetch_limit) }}" min="1" max="100" required>
            @error('fetch_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <div class="form-check">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" 
                       {{ old('is_active', $facebookConfig->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    Active
                </label>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i> Update Configuration
            </button>
            <a href="{{ route('admin.facebook.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
function updateEntityField() {
    const entityType = document.getElementById('entity_type').value;
    const entityIdField = document.getElementById('entity_id_field');
    
    if (entityType === 'global') {
        entityIdField.style.display = 'none';
        document.getElementById('entity_id').value = '';
    } else {
        entityIdField.style.display = 'block';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', updateEntityField);
</script>
@endsection
