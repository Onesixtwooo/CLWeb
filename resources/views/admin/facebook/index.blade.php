@extends('admin.layout')

@section('title', 'Facebook Configuration')

@section('content')
<style>
    .facebook-config-shell {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    .facebook-config-card {
        overflow: hidden;
    }
    .facebook-config-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.5rem;
        border-bottom: 1px solid var(--admin-border);
    }
    .facebook-config-header h5 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
    }
    .facebook-config-toolbar {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .facebook-config-table {
        margin-bottom: 0;
    }
    .facebook-config-table thead th {
        padding: 0.95rem 1.5rem;
        background: #f8fafc;
        border-bottom: 1px solid var(--admin-border);
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        color: var(--admin-text-muted);
        white-space: nowrap;
    }
    .facebook-config-table tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
    }
    .facebook-config-page {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }
    .facebook-config-page strong {
        font-size: 1rem;
    }
    .facebook-config-page small {
        font-size: 0.82rem;
    }
    .facebook-config-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .facebook-config-actions form {
        margin: 0;
    }
    @media (max-width: 767.98px) {
        .facebook-config-header {
            align-items: flex-start;
            flex-direction: column;
        }
        .facebook-config-toolbar {
            width: 100%;
        }
        .facebook-config-toolbar .btn {
            width: 100%;
            justify-content: center;
        }
        .facebook-config-table thead th,
        .facebook-config-table tbody td {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        .facebook-config-actions {
            justify-content: flex-start;
        }
    }
</style>
<div class="admin-page-title">Facebook Settings</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (auth()->user()->isBoundedToCollege())
    <div class="admin-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-light rounded-pill p-3 d-flex align-items-center justify-content-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#1877F2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </div>
                    <div>
                        <h2 class="h5 fw-600 mb-1">Facebook Configuration</h2>
                        <p class="text-muted small mb-0">Configure the Facebook integration for automatic post capture and article creation.</p>
                    </div>
                </div>
                @if ($configs->count() > 0)
                    <div>
                        <form action="{{ route('admin.facebook.sync') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary d-flex align-items-center gap-2 px-3 py-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2v6h-6M3 12a9 9 0 0 1 15-6.7L21 8M3 22v-6h6M21 12a9 9 0 0 1-15 6.7L3 16"/></svg>
                                Sync Now
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            @php
                $config = $configs->first();
                $action = $config ? route('admin.facebook.update', $config) : route('admin.facebook.store');
            @endphp

            <form method="POST" action="{{ $action }}" class="row g-4">
                @csrf
                @if ($config)
                    @method('PUT')
                @endif

                <div class="col-md-12">
                    <label for="page_id" class="form-label fw-500">Facebook Page ID</label>
                    <input type="text" name="page_id" id="page_id" class="form-control @error('page_id') is-invalid @enderror" value="{{ old('page_id', $config->page_id ?? '') }}" placeholder="123456789" required>
                    @error('page_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-12">
                    <label for="access_token" class="form-label fw-500">Access Token</label>
                    <div class="input-group">
                        <input type="password" name="access_token" id="access_token" class="form-control @error('access_token') is-invalid @enderror" value="{{ old('access_token', $config->access_token ?? '') }}" placeholder="Paste your Facebook Page Access Token" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('access_token', this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    @error('access_token') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label for="article_category" class="form-label fw-500">Article Category (Optional)</label>
                    <input type="text" name="article_category" id="article_category" class="form-control" value="{{ old('article_category', $config->article_category ?? '') }}" placeholder="e.g., College News">
                </div>

                <div class="col-md-6">
                    <label for="article_author" class="form-label fw-500">Article Author (Optional)</label>
                    <input type="text" name="article_author" id="article_author" class="form-control" value="{{ old('article_author', $config->article_author ?? \App\Http\Controllers\Admin\CollegeController::getColleges()[auth()->user()->college_slug] ?? '') }}" placeholder="e.g., College of Agriculture">
                </div>

                <div class="col-md-6">
                    <label for="fetch_limit" class="form-label fw-500">Posts to Fetch Per Run</label>
                    <input type="number" name="fetch_limit" id="fetch_limit" class="form-control" value="{{ old('fetch_limit', $config->fetch_limit ?? 5) }}" min="1" max="100" required>
                </div>

                <div class="col-md-6 d-flex align-items-center pt-4">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" {{ old('is_active', $config ? $config->is_active : true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-500" for="is_active">Active</label>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-admin-primary px-4 py-2" style="background: var(--admin-accent); border-color: var(--admin-accent);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Save Facebook Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function togglePasswordVisibility(id, btn) {
        const input = document.getElementById(id);
        if (input.type === 'password') {
            input.type = 'text';
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 19c-7 0-11-7-11-7a11.93 11.93 0 0 1 2.22-3.14m3.42-3.42A9.76 9.76 0 0 1 12 5c7 0 11 7 11 7a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
        } else {
            input.type = 'password';
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
        }
    }
    </script>
@else
    <div class="facebook-config-shell">
    <div class="admin-card facebook-config-card mb-4">
        <div class="facebook-config-header">
            <h5>Facebook Page Configurations</h5>
            <div class="facebook-config-toolbar">
                <a href="{{ route('admin.facebook.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-lg"></i> Add Configuration
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover facebook-config-table">
                <thead>
                    <tr>
                        <th style="width: 28%;">College / Entity</th>
                        <th style="width: 15%;">Entity Type</th>
                        <th style="width: 22%;">Page Name</th>
                        <th style="width: 13%;">Status</th>
                        <th class="text-end" style="width: 22%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($configs as $config)
                        <tr>
                            <td>
                                <div class="facebook-config-page">
                                    <strong>
                                        @if ($config->entity_type === 'global')
                                            Global
                                        @elseif ($config->entity_type === 'college')
                                            {{ $colleges[$config->entity_id]?->name ?? 'Unknown College' }}
                                        @elseif ($config->entity_type === 'department')
                                            {{ $departments[$config->entity_id]?->name ?? 'Unknown Department' }}
                                        @elseif ($config->entity_type === 'organization')
                                            {{ $organizations[$config->entity_id]?->name ?? 'Unknown Organization' }}
                                        @endif
                                    </strong>
                                    <small class="text-muted">
                                        @if ($config->entity_type === 'global')
                                            Global scope
                                        @else
                                            {{ ucfirst($config->entity_type) }} configuration
                                        @endif
                                    </small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $config->entity_type === 'global' ? 'primary' : ($config->entity_type === 'college' ? 'success' : ($config->entity_type === 'department' ? 'info' : 'warning')) }}">
                                    {{ ucfirst($config->entity_type) }}
                                </span>
                            </td>
                            <td>
                                <div class="facebook-config-page">
                                    <strong>{{ $config->page_name }}</strong>
                                    <small class="text-muted">ID: {{ $config->page_id }}</small>
                                </div>
                            </td>
                            <td>
                                @if ($config->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="facebook-config-actions">
                                    <a href="{{ route('admin.facebook.edit', $config) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.facebook.destroy', $config) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No Facebook configurations yet. <a href="{{ route('admin.facebook.create') }}">Create one</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>
@endif

@endsection
