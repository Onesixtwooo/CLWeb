@extends('admin.layout')

@section('title', 'Facebook Configuration')

@section('content')
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

<div class="alert alert-info">
    🔍 **Diagnostic Data:** 
    Logged-in with College: `{{ auth()->user()->college_slug }}` | 
    Saved Database Configurations: `{{ \App\Models\FacebookConfig::count() }}` | 
    Form matches found: `{{ $configs->count() }}`
</div>

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

    <!-- Hidden toggle utility from layout scripts loader -->
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
    <div class="admin-card mb-4">
        <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
            <h5 class="mb-0">Facebook Page Configurations</h5>
            <a href="{{ route('admin.facebook.create') }}" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> Add Configuration
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr class="table-light">
                        <th style="width: 15%;">Entity Type</th>
                        <th style="width: 25%;">Page Name</th>
                        <th style="width: 20%;">Entity</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 25%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($configs as $config)
                        <tr>
                            <td>
                                <span class="badge bg-{{ $config->entity_type === 'global' ? 'primary' : ($config->entity_type === 'college' ? 'success' : ($config->entity_type === 'department' ? 'info' : 'warning')) }}">
                                    {{ ucfirst($config->entity_type) }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $config->page_name }}</strong>
                                <br>
                                <small class="text-muted">ID: {{ $config->page_id }}</small>
                            </td>
                            <td>
                                @if ($config->entity_type === 'global')
                                    <span class="text-muted">—</span>
                                @elseif ($config->entity_type === 'college')
                                    <span>{{ $colleges[$config->entity_id]?->name ?? 'Unknown College' }}</span>
                                @elseif ($config->entity_type === 'department')
                                    <span>{{ $departments[$config->entity_id]?->name ?? 'Unknown Department' }}</span>
                                @elseif ($config->entity_type === 'organization')
                                    <span>{{ $organizations[$config->entity_id]?->name ?? 'Unknown Organization' }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($config->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.facebook.edit', $config) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('admin.facebook.destroy', $config) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
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
@endif


@endsection
