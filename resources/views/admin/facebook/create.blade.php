@extends('admin.layout')

@section('title', 'Create Facebook Configuration')

@section('content')
<style>
    .facebook-form-shell {
        width: 100%;
        max-width: none;
    }
    .facebook-form-card {
        overflow: hidden;
    }
    .facebook-form-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.5rem;
        border-bottom: 1px solid var(--admin-border);
    }
    .facebook-form-title {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 700;
    }
    .facebook-form-subtitle {
        margin: 0.35rem 0 0;
        color: var(--admin-text-muted);
        font-size: 0.92rem;
    }
    .facebook-form-body {
        padding: 1.5rem;
    }
    .facebook-form-grid {
        row-gap: 1.25rem;
    }
    .facebook-token-input {
        min-height: 132px;
        resize: vertical;
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        font-size: 0.92rem;
    }
    .facebook-form-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 0.75rem;
    }
    @media (max-width: 767.98px) {
        .facebook-form-header {
            align-items: flex-start;
            flex-direction: column;
        }
        .facebook-form-header .btn {
            width: 100%;
        }
    }
</style>
<div class="admin-page-title">Add Facebook Configuration</div>

<div class="facebook-form-shell">
<div class="admin-card facebook-form-card">
    <div class="facebook-form-header">
        <div>
            <h2 class="facebook-form-title">Add Facebook Configuration</h2>
            <p class="facebook-form-subtitle">Create a new page configuration for a college Facebook page.</p>
        </div>
        <a href="{{ route('admin.facebook.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>
    <form action="{{ route('admin.facebook.store') }}" method="POST" class="facebook-form-body">
        @csrf

        <div class="row facebook-form-grid">
        @if (auth()->user()->isBoundedToCollege())
            <input type="hidden" name="entity_type" value="college">
            <input type="hidden" name="entity_id" value="{{ auth()->user()->college_slug }}">
            <div class="col-12">
                <label class="form-label">Configuring for</label>
                <div class="form-control-plaintext font-weight-bold" style="color: var(--admin-accent)">
                    @php
                        $slug = auth()->user()->college_slug;
                        echo \App\Http\Controllers\Admin\CollegeController::getColleges()[$slug] ?? $slug;
                    @endphp
                </div>
            </div>
        @else
            <div class="col-md-6">
                <label for="entity_type" class="form-label">Entity Type</label>
                <select name="entity_type" id="entity_type" class="form-select @error('entity_type') is-invalid @enderror" required>
                    <option value="college" selected>College</option>
                </select>
                @error('entity_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6" id="entity_id_field">
                <label for="entity_id" class="form-label">Entity</label>
                <select name="entity_id" id="entity_id" class="form-select @error('entity_id') is-invalid @enderror" required>
                    <option value="">-- Select --</option>
                    @foreach ($colleges as $college)
                        <option value="{{ $college->slug }}" {{ old('entity_id') === $college->slug ? 'selected' : '' }}>{{ $college->name }}</option>
                    @endforeach
                </select>
                @error('entity_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        @endif

        <div class="col-md-6">
            <label for="page_name" class="form-label">Page Name</label>
            <input type="text" name="page_name" id="page_name" class="form-control @error('page_name') is-invalid @enderror" 
                   value="{{ old('page_name') }}" placeholder="e.g., College of Agriculture" required>
            @error('page_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label for="page_id" class="form-label">Facebook Page ID</label>
            <input type="text" name="page_id" id="page_id" class="form-control @error('page_id') is-invalid @enderror" 
                   value="{{ old('page_id') }}" placeholder="e.g., 123456789" required>
            @error('page_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <label for="access_token" class="form-label">Access Token</label>
            <textarea name="access_token" id="access_token" class="form-control facebook-token-input @error('access_token') is-invalid @enderror" 
                      rows="3" placeholder="Paste your Facebook page access token" required>{{ old('access_token') }}</textarea>
            @error('access_token') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label for="article_category" class="form-label">Article Category (Optional)</label>
            <input type="text" name="article_category" id="article_category" class="form-control" 
                   value="{{ old('article_category') }}" placeholder="e.g., College News">
        </div>

        <div class="col-md-6">
            <label for="article_author" class="form-label">Article Author (Optional)</label>
            <input type="text" name="article_author" id="article_author" class="form-control" 
                   value="{{ old('article_author') }}" placeholder="e.g., College of Agriculture">
        </div>

        <div class="col-md-6">
            <label for="fetch_limit" class="form-label">Posts to Fetch Per Run</label>
            <input type="number" name="fetch_limit" id="fetch_limit" class="form-control @error('fetch_limit') is-invalid @enderror" 
                   value="{{ old('fetch_limit', 5) }}" min="1" max="100" required>
            @error('fetch_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" 
                       {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    Active
                </label>
            </div>
        </div>

        <div class="col-12">
        <div class="facebook-form-actions">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-lg"></i> Create Configuration
            </button>
            <a href="{{ route('admin.facebook.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
        </div>
        </div>
    </form>
</div>
</div>
@endsection
