@extends('admin.layout')

@section('title', 'Settings')

@push('styles')
<style>
    .color-palette {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    .color-swatch {
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid transparent;
        transition: transform 0.1s ease, border-color 0.1s ease;
    }
    .color-swatch:hover {
        transform: scale(1.1);
    }
    .color-swatch.active {
        border-color: #000;
        transform: scale(1.1);
    }
    
    .admin-tabs {
        border-bottom: 2px solid var(--admin-border);
        gap: 0.5rem;
    }
    .admin-tabs .nav-link {
        border: none;
        color: var(--admin-text-muted);
        font-weight: 600;
        padding: 0.75rem 1.25rem;
        border-radius: 10px 10px 0 0;
        transition: all 0.2s ease;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
    }
    .admin-tabs .nav-link:hover {
        color: var(--admin-text);
        background: var(--admin-accent-soft);
    }
    .admin-tabs .nav-link.active {
        color: var(--admin-accent);
        background: transparent;
        border-bottom-color: var(--admin-accent);
    }
    .google-drive-icon {
        color: #4285F4;
    }
</style>
@endpush

@section('content')
    <h1 class="admin-page-title">Settings</h1>
    <p class="text-muted mb-4">Manage site and college settings.</p>

    @if (isset($appearanceScope) && $appearanceScope === 'global')
    <ul class="nav nav-tabs admin-tabs mb-4" id="settingsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                General
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="google-drive-tab" data-bs-toggle="tab" data-bs-target="#google-drive" type="button" role="tab" aria-controls="google-drive" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2 google-drive-icon"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Drive & Facebook
            </button>
        </li>
    </ul>
    @endif

    <div class="tab-content" id="settingsTabsContent">
        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">

    <div class="admin-card mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-600 mb-3">Appearance</h2>
            <p class="text-muted small mb-3">Header and sidebar colors. Saved and loaded <strong>per role and department</strong> in the database.</p>
            @if (isset($appearanceScope) && $appearanceScope === 'editor_college' && !empty($appearanceCollegeName))
                <p class="text-muted small mb-2">You are editing the <strong>Editor</strong> theme for <strong>{{ $appearanceCollegeName }}</strong>.</p>
            @elseif (isset($appearanceScope) && $appearanceScope === 'college' && !empty($appearanceCollegeName))
                <p class="text-muted small mb-2">You are editing the <strong>Admin</strong> theme for <strong>{{ $appearanceCollegeName }}</strong>.</p>
            @elseif (isset($appearanceScope) && $appearanceScope === 'global')
                <p class="text-muted small mb-2">You are editing the <strong>global</strong> admin theme (superadmin).</p>
            @endif
            <form method="POST" action="{{ route('admin.settings.appearance.update') }}" class="row g-3" enctype="multipart/form-data">
                @csrf
                <div class="col-12">
                    <label class="form-label d-block mb-2">Logo</label>
                    <input type="file" name="admin_logo" id="admin_logo" accept="image/*" class="d-none">
                    <input type="hidden" name="remove_admin_logo" id="remove_admin_logo" value="0">
                    <div class="d-flex flex-wrap align-items-start gap-3">
                        <div class="d-flex flex-column gap-2 align-items-center">
                            <div class="fw-600 text-muted small mb-1">Current</div>
                            <div id="admin-logo-zone" class="border rounded-3 bg-light d-flex align-items-center justify-content-center text-muted position-relative overflow-hidden"
                                 style="width: 120px; height: 120px; cursor: pointer;">
                                <img id="admin-logo-preview"
                                     src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($adminLogoPath) }}{{ !empty($adminLogoPath) ? '?v=' . time() : '' }}"
                                     alt="Logo preview"
                                     class="w-100 h-100 position-absolute top-0 start-0 {{ !empty($adminLogoPath) ? '' : 'd-none' }}"
                                     style="object-fit: cover;">
                                <div id="admin-logo-placeholder" class="small text-center px-2 {{ !empty($adminLogoPath) ? 'd-none' : '' }}">
                                    No logo<br>set
                                </div>
                            </div>
                        </div>
                        <div id="admin-logo-new-container" class="d-flex flex-column gap-2 align-items-center">
                            <div class="fw-600 text-success small mb-1">New Selection</div>
                            <div id="admin-logo-new-zone" class="border border-success rounded-3 bg-white d-flex align-items-center justify-content-center position-relative overflow-hidden"
                                 style="width: 120px; height: 120px; border-style: dashed !important;">
                                <img id="admin-logo-new-preview" src="" alt="New logo preview" class="w-100 h-100 d-none" style="object-fit: cover;">
                                <div id="admin-logo-new-placeholder" class="small text-center text-muted px-2" style="font-size: 0.7rem;">
                                    No new<br>image
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-2 justify-content-center" style="min-height:120px;">
                            <div class="fw-600 text-dark small">Logo</div>
                            <div class="text-muted" style="font-size:0.8rem;">PNG/JPG/WebP/GIF, max 2MB.</div>
                            <label for="admin_logo" class="btn btn-sm btn-outline-secondary mb-0" style="width:fit-content; cursor:pointer;">
                                📂 Change image
                            </label>
                            <button type="button" id="admin-logo-remove-btn" class="btn btn-sm btn-outline-danger {{ empty($adminLogoPath) ? 'd-none' : '' }}" style="width:fit-content;">
                                🗑 Remove logo
                            </button>
                        </div>
                    </div>
                    @error('admin_logo')
                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="admin_header_color" class="form-label">Header color</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="color" name="admin_header_color" id="admin_header_color" class="form-control form-control-color p-1" style="width: 3rem; height: 2.5rem;" value="{{ old('admin_header_color', $headerColor) }}" title="Choose header color">
                        <input type="text" class="form-control" value="{{ old('admin_header_color', $headerColor) }}" maxlength="7" pattern="#[0-9A-Fa-f]{6}" id="admin_header_color_hex" placeholder="#0d6e42" style="max-width: 8rem;">
                    </div>
                    <div class="color-palette" data-target="admin_header_color">
                        <div class="color-swatch" style="background-color: #016531;" data-color="#016531" title="Agriculture"></div>
                        <div class="color-swatch" style="background-color: #6d430f;" data-color="#6d430f" title="Arts and Social Sciences"></div>
                        <div class="color-swatch" style="background-color: #0a6daf;" data-color="#0a6daf" title="Business and Accountancy"></div>
                        <div class="color-swatch" style="background-color: #b29a00;" data-color="#b29a00" title="Education"></div>
                        <div class="color-swatch" style="background-color: #86090a;" data-color="#86090a" title="Engineering"></div>
                        <div class="color-swatch" style="background-color: #be4c00;" data-color="#be4c00" title="Fisheries"></div>
                        <div class="color-swatch" style="background-color: #a70062;" data-color="#a70062" title="Home Science and Industry"></div>
                        <div class="color-swatch" style="background-color: #008080;" data-color="#008080" title="Science"></div>
                        <div class="color-swatch" style="background-color: #494949;" data-color="#494949" title="Veterinary Science and Medicine"></div>
                    </div>
                    @error('admin_header_color')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                @if (isset($appearanceScope) && $appearanceScope === 'global')
                <div class="col-12 mt-4">
                    <label class="form-label d-block mb-2">Default Hero Background</label>
     <input type="file" name="admin_default_hero" id="admin_default_hero" accept="image/*" hidden>
                    <input type="hidden" name="remove_admin_default_hero" id="remove_admin_default_hero" value="0">
                    <div class="d-flex flex-wrap align-items-start gap-3">
                        <div class="d-flex flex-column gap-2 align-items-center">
                            <div class="fw-600 text-muted small mb-1">Current</div>
                            <div id="admin-hero-zone" class="border rounded-3 bg-light d-flex align-items-center justify-content-center text-muted position-relative overflow-hidden"
                                 style="width: 240px; height: 135px; cursor: pointer;">
                                <img id="admin-hero-preview"
                                     src="{{ \App\Providers\AppServiceProvider::resolveLogoUrl($adminDefaultHeroPath) }}{{ !empty($adminDefaultHeroPath) ? '?v=' . time() : '' }}"
                                     alt="Hero preview"
                                     class="w-100 h-100 position-absolute top-0 start-0 {{ !empty($adminDefaultHeroPath) ? '' : 'd-none' }}"
                                     style="object-fit: cover;">
                                <div id="admin-hero-placeholder" class="small text-center px-2 {{ !empty($adminDefaultHeroPath) ? 'd-none' : '' }}">
                                    No hero<br>background
                                </div>
                            </div>
                        </div>
                        <div id="admin-hero-new-container" class="d-flex flex-column gap-2 align-items-center">
                            <div class="fw-600 text-success small mb-1">New Selection</div>
                            <div id="admin-hero-new-zone"
                                class="border border-success rounded-3 bg-white d-flex align-items-center justify-content-center position-relative overflow-hidden"
                                style="width:240px;height:135px;border-style:dashed!important;">

                                <img id="admin-hero-new-preview"
                                    src=""
                                    alt="New hero preview"
                                    class="w-100 h-100 d-none position-absolute top-0 start-0"
                                    style="object-fit:cover; z-index:2;">

                                <div id="admin-hero-new-placeholder"
                                    class="small text-center text-muted px-2 position-relative"
                                    style="font-size:0.7rem; z-index:1;">
                                    No new image selected
                                </div>

                            </div>
                        </div>
                        <div class="d-flex flex-column gap-2 justify-content-center" style="min-height:135px;">
                            <div class="fw-600 text-dark small">Hero Background</div>
                            <div class="text-muted" style="font-size:0.8rem;">Preferred: 1920×1080px. PNG/JPG/WebP, max 2MB.</div>
                            <label for="admin_default_hero" class="btn btn-sm btn-outline-secondary mb-0" style="width:fit-content; cursor:pointer;">
                                📂 Change image
                            </label>
                            <button type="button" id="admin-hero-remove-btn" class="btn btn-sm btn-outline-danger {{ empty($adminDefaultHeroPath) ? 'd-none' : '' }}" style="width:fit-content;">
                                🗑 Remove hero
                            </button>
                        </div>
                    </div>
                    @error('admin_default_hero')
                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                    @enderror
                </div>
                @endif

                <div class="col-12">
                    <button type="submit"
                            class="btn btn-admin-primary"
                            style="background: {{ $headerColor }}; border-color: {{ $headerColor }};">
                        Save appearance
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if (isset($appearanceScope) && $appearanceScope === 'global')
    <div class="admin-card mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-600 mb-3">President's Contact Info</h2>
            <p class="text-muted small mb-3">Set the Office of the President contact details displayed in the header.</p>
            <form method="POST" action="{{ route('admin.settings.president.update') }}" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label for="admin_president_email" class="form-label">Email Address</label>
                    <input type="email" name="admin_president_email" id="admin_president_email" class="form-control" value="{{ old('admin_president_email', $presidentEmail ?? '') }}" placeholder="op@clsu.edu.ph" required>
                </div>
                <div class="col-md-6">
                    <label for="admin_president_phone" class="form-label">Phone Number</label>
                    <input type="text" name="admin_president_phone" id="admin_president_phone" class="form-control" value="{{ old('admin_president_phone', $presidentPhone ?? '') }}" placeholder="(044) 940 8785" required>
                </div>
                <div class="col-12">
                     <button type="submit"
                            class="btn btn-admin-primary"
                            style="background: {{ $headerColor }}; border-color: {{ $headerColor }};">
                        Save contact info
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if (isset($collegeSlug) && !empty($collegeSlug))
    <div class="admin-card mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-600 mb-3">College Email</h2>
            <p class="text-muted small mb-3">Set the email address that will be displayed in the footer of the college's public pages.</p>
            <form method="POST" action="{{ route('admin.settings.email.update') }}" class="row g-3">
                @csrf
                <div class="col-12">
                    <label for="admin_email" class="form-label">Email Address</label>
                    <input type="email" name="admin_email" id="admin_email" class="form-control" value="{{ old('admin_email', $collegeEmail ?? '') }}" placeholder="e.g., engineering@clsu.edu.ph" required>
                    <div class="form-text">This email will be displayed in the footer of the college's public pages.</div>
                    @error('admin_email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <button type="submit"
                            class="btn btn-admin-primary"
                            style="background: {{ $headerColor }}; border-color: {{ $headerColor }};">
                        Save email
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if (isset($collegeSlug) && !empty($collegeSlug))
    <div class="admin-card mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-600 mb-3">Integrations</h2>
            <p class="text-muted small mb-3">Manage features and external integrations for your college.</p>
            <form method="POST" action="{{ route('admin.settings.college-facebook.update') }}">
                @csrf
                @method('PUT')
                <div class="form-check form-switch d-flex align-items-center gap-2 mb-3 mt-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="college_facebook_integration" name="facebook_integration_enabled" value="1" {{ $facebookIntegrationEnabled ? 'checked' : '' }} style="width: 2.5em; height: 1.25em; cursor: pointer;">
                    <label class="form-check-label fw-600 text-dark" for="college_facebook_integration" style="cursor: pointer;">Enable Facebook Integration</label>
                </div>
                <div class="form-text mb-4 mt-n2 ms-1">When disabled, the Facebook menu in the sidebar will be hidden and the page will be inaccessible.</div>
                
                <button type="submit"
                        class="btn btn-admin-primary"
                        style="background: {{ $headerColor }}; border-color: {{ $headerColor }};">
                    Save integration settings
                </button>
            </form>
        </div>
    </div>
    @endif

    @if (auth()->user() && auth()->user()->role !== 'superadmin')
    <div class="admin-card mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-600 mb-3">Departments</h2>
            <p class="text-muted small mb-3">Edit the display name of each department. The URL slug cannot be changed.</p>
            @if (empty($colleges))
                <p class="text-muted mb-0">No departments available to edit.</p>
            @else
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead>
                            <tr>
                                <th class="border-0 py-2">Department</th>
                                <th class="border-0 py-2 text-end" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($colleges as $slug => $name)
                                <tr>
                                    <td class="py-2 fw-500">{{ $name }}</td>
                                    <td class="py-2 text-end">
                                        <a href="{{ route('admin.colleges.edit', ['college' => $slug]) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Edit name</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    @endif
        </div>

        @if (isset($appearanceScope) && $appearanceScope === 'global')
        <div class="tab-pane fade" id="google-drive" role="tabpanel" aria-labelledby="google-drive-tab">
            <div class="admin-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="bg-light rounded-pill p-3 d-flex align-items-center justify-content-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#4285F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <div>
                            <h2 class="h5 fw-600 mb-1">Google Drive API Configuration</h2>
                            <p class="text-muted small mb-0">Configure the Google Drive integration for file storage and media management.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.settings.google-drive.update') }}" class="row g-4">
                        @csrf
                        <div class="col-md-6">
                            <label for="google_drive_folder_id" class="form-label fw-500">Folder ID</label>
                            <input type="text" name="google_drive_folder_id" id="google_drive_folder_id" class="form-control" value="{{ old('google_drive_folder_id', $googleDriveFolderId ?? '') }}" placeholder="Enter the root Folder ID" required>
                            <div class="form-text mt-2">The unique identifier of the Google Drive folder where files will be stored.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="google_drive_client_id" class="form-label fw-500">Client ID</label>
                            <input type="text" name="google_drive_client_id" id="google_drive_client_id" class="form-control" value="{{ old('google_drive_client_id', $googleDriveClientId ?? '') }}" placeholder="Enter Client ID" required>
                        </div>

                        <div class="col-md-6">
                            <label for="google_drive_client_secret" class="form-label fw-500">Client Secret</label>
                            <div class="input-group">
                                <input type="password" name="google_drive_client_secret" id="google_drive_client_secret" class="form-control" value="{{ old('google_drive_client_secret', $googleDriveClientSecret ?? '') }}" placeholder="Enter Client Secret" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('google_drive_client_secret')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="google_drive_refresh_token" class="form-label fw-500">Refresh Token</label>
                            <div class="input-group">
                                <input type="password" name="google_drive_refresh_token" id="google_drive_refresh_token" class="form-control" value="{{ old('google_drive_refresh_token', $googleDriveRefreshToken ?? '') }}" placeholder="Enter Refresh Token" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('google_drive_refresh_token')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="col-12 mt-4 pt-2">
                             <button type="submit" 
                                    class="btn btn-admin-primary px-4 py-2"
                                    style="background: {{ $headerColor }}; border-color: {{ $headerColor }};">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                Save Google Drive Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="admin-card mt-4 border-info bg-light" style="margin-bottom: 6rem;">
                <div class="card-body p-4">
                    <div class="d-flex gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0dcaf0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-1 flex-shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        <div>
                            <h3 class="h6 fw-bold text-dark">Need help setting up?</h3>
                            <p class="text-muted small mb-3">Follow these steps to get your Google Drive credentials:</p>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="p-3 bg-white rounded-3 border h-100">
                                        <div class="fw-600 text-dark small mb-2 d-flex align-items-center gap-2">
                                            <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:20px; height:20px; font-size:10px;">1</span>
                                            Folder ID
                                        </div>
                                        <p class="text-muted mb-0" style="font-size: 0.8rem;">Open your Google Drive folder. The ID is the long string at the end of the URL: <code>drive.google.com/drive/folders/<strong>YOUR_ID_HERE</strong></code></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-white rounded-3 border h-100">
                                        <div class="fw-600 text-dark small mb-2 d-flex align-items-center gap-2">
                                            <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:20px; height:20px; font-size:10px;">2</span>
                                            Client ID &amp; Secret
                                        </div>
                                        <p class="text-muted mb-0" style="font-size: 0.8rem;">Go to the <a href="https://console.cloud.google.com/" target="_blank" class="text-primary text-decoration-none fw-500">Google Cloud Console</a>. Enable <strong>Google Drive API</strong>, then go to <strong>Credentials</strong> and create an <strong>OAuth 2.0 Client ID</strong> (Web Application).</p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="p-4 bg-white rounded-3 border h-100">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="fw-600 text-dark small d-flex align-items-center gap-2">
                                                <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:20px; height:20px; font-size:10px;">3</span>
                                                Authorization
                                            </div>
                                            @if(!empty($googleDriveRefreshToken))
                                                <span class="badge bg-success-soft text-success px-3 py-2 rounded-pill small fw-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="me-1"><polyline points="20 6 9 17 4 12"/></svg>
                                                    Authorized
                                                </span>
                                            @else
                                                <span class="badge bg-warning-soft text-warning px-3 py-2 rounded-pill small fw-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="me-1"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                                    Pending Authorization
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-muted mb-4" style="font-size: 0.8rem;">Once you've saved your <strong>Client ID</strong> and <strong>Client Secret</strong>, click the button below to authorize the application and generate a refresh token automatically.</p>
                                        
                                        <div class="d-grid shadow-sm rounded-3 overflow-hidden">
                                            <a href="{{ route('admin.settings.google-drive.auth') }}" 
                                               class="btn btn-primary py-3 fw-bold d-flex align-items-center justify-content-center gap-2 border-0"
                                               style="background: linear-gradient(135deg, #4285F4 0%, #34a853 100%); transition: transform 0.2s ease;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                                Authorize Google Drive Access
                                            </a>
                                        </div>
                                        <p class="text-center text-muted mt-3 mb-0" style="font-size: 0.75rem;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                            Secure connection to Google Cloud Console
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="p-3 bg-white rounded-3 border h-100">
                                        <div class="fw-600 text-dark small mb-2 d-flex align-items-center gap-2">
                                            <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:20px; height:20px; font-size:10px;">4</span>
                                            Authorized Redirect URI
                                        </div>
                                        <p class="text-muted mb-2" style="font-size: 0.8rem;">In the Google Cloud Console, under your OAuth 2.0 Client ID, you <strong>must</strong> add the following URL to the <strong>Authorized redirect URIs</strong> list:</p>
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control bg-light border-0" value="{{ route('admin.settings.google-drive.callback') }}" readonly id="redirectUriInput">
                                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="copyToClipboard('redirectUriInput', this)">
                                                Copy
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (auth()->user()?->isAdmin() || auth()->user()?->isSuperAdmin())
            <div class="admin-card mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="bg-light rounded-circle p-3 d-flex align-items-center justify-content-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#1877F2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a6 6 0 0 0-6 6v4a6 6 0 0 0 6 6h3"/><circle cx="9" cy="9" r="5"/><circle cx="9" cy="9" r="3"/></svg>
                    </div>
                    <div>
                        <h2 class="h5 fw-600 mb-1">Facebook API Configuration</h2>
                        <p class="text-muted small mb-0">Configure the Facebook integration for automatic post capture and article creation.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.settings.facebook-update') }}" class="row g-4">
                    @csrf
                    @method('PUT')
                    
                    <div class="col-md-6">
                        <label for="facebook_app_id" class="form-label fw-600 small text-uppercase text-muted">App ID</label>
                        <input type="text" id="facebook_app_id" name="facebook_app_id" class="form-control @error('facebook_app_id') is-invalid @enderror" 
                               value="{{ $facebookAppId ?? env('FACEBOOK_APP_ID') ?? '' }}" 
                               placeholder="Your Facebook App ID">
                        <small class="text-muted d-block mt-2">Your unique Facebook application identifier.</small>
                        @error('facebook_app_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="facebook_app_secret" class="form-label fw-600 small text-uppercase text-muted">App Secret</label>
                        <div class="input-group">
                            <input type="password" id="facebook_app_secret" name="facebook_app_secret" class="form-control @error('facebook_app_secret') is-invalid @enderror" 
                                   value="{{ $facebookAppSecret ?? env('FACEBOOK_APP_SECRET') ?? '' }}" 
                                   placeholder="•••••••••••••">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('facebook_app_secret', this)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">Keep this secret. Never share it publicly.</small>
                        @error('facebook_app_secret')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="facebook_access_token" class="form-label fw-600 small text-uppercase text-muted">Access Token</label>
                        <div class="input-group">
                            <input type="password" id="facebook_access_token" name="facebook_access_token" class="form-control @error('facebook_access_token') is-invalid @enderror" 
                                   value="{{ $facebookAccessToken ?? env('FACEBOOK_ACCESS_TOKEN') ?? '' }}" 
                                   placeholder="•••••••••••••">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('facebook_access_token', this)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">Page access token for authenticating API requests.</small>
                        @error('facebook_access_token')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="facebook_page_id" class="form-label fw-600 small text-uppercase text-muted">Page ID</label>
                        <input type="text" id="facebook_page_id" name="facebook_page_id" class="form-control @error('facebook_page_id') is-invalid @enderror" 
                               value="{{ $facebookPageId ?? env('FACEBOOK_PAGE_ID') ?? '' }}" 
                               placeholder="Your Facebook Page ID">
                        <small class="text-muted d-block mt-2">The ID of the Facebook page to fetch posts from.</small>
                        @error('facebook_page_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-admin-primary" style="background: {{ $headerColor }}; border-color: {{ $headerColor }};">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Save Facebook Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="admin-card mb-4 border-info bg-light">
            <div class="card-body p-4">
                <div class="d-flex gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0dcaf0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-1 flex-shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    <div>
                        <h3 class="h6 fw-bold text-dark">Need help setting up?</h3>
                        <p class="text-muted small mb-3">Follow these steps to get your Facebook credentials:</p>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-white rounded-3 border h-100">
                                    <div class="fw-600 text-dark small mb-2 d-flex align-items-center gap-2">
                                        <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:20px; height:20px; font-size:10px;">1</span>
                                        Create an App
                                    </div>
                                    <p class="text-muted mb-0" style="font-size: 0.8rem;">Go to <a href="https://developers.facebook.com/" target="_blank" class="text-primary fw-500">developers.facebook.com</a>, create a new app, and choose <strong>Business</strong> as the app type. Select <strong>Graph API</strong> as your use case.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-white rounded-3 border h-100">
                                    <div class="fw-600 text-dark small mb-2 d-flex align-items-center gap-2">
                                        <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:20px; height:20px; font-size:10px;">2</span>
                                        Get Credentials
                                    </div>
                                    <p class="text-muted mb-0" style="font-size: 0.8rem;">In your app settings, go to <strong>Settings</strong> → <strong>Basic</strong> to find your <strong>App ID</strong> and <strong>App Secret</strong>. Copy them to the fields above.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-white rounded-3 border h-100">
                                    <div class="fw-600 text-dark small mb-2 d-flex align-items-center gap-2">
                                        <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:20px; height:20px; font-size:10px;">3</span>
                                        Get Page ID
                                    </div>
                                    <p class="text-muted mb-0" style="font-size: 0.8rem;">Open your Facebook page. Right-click → <strong>View Page Source</strong> and search for <code>page_id</code>. Or use the <a href="https://developers.facebook.com/tools/explorer" target="_blank" class="text-primary fw-500">Graph API Explorer</a>.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-white rounded-3 border h-100">
                                    <div class="fw-600 text-dark small mb-2 d-flex align-items-center gap-2">
                                        <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:20px; height:20px; font-size:10px;">4</span>
                                        Get Access Token
                                    </div>
                                    <p class="text-muted mb-0" style="font-size: 0.8rem;">In the <a href="https://developers.facebook.com/tools/explorer" target="_blank" class="text-primary fw-500">Graph API Explorer</a>, select your app and page, then generate a <strong>Page Access Token</strong> with <code>pages_read_posts</code> permission.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        </div>

        @endif
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function syncColor(inputColor, inputHex) {
        if (!inputColor || !inputHex) return;
        
        var saveBtn = document.querySelector('button[type="submit"]');

        function updatePreview(val) {
            if (inputColor.id === 'admin_header_color' && saveBtn) {
                saveBtn.style.background = val;
                saveBtn.style.borderColor = val;
            }
        }

        inputColor.addEventListener('input', function () {
            inputHex.value = this.value;
            updatePreview(this.value);
        });
        inputHex.addEventListener('input', function () {
            var v = this.value;
            if (/^#[0-9A-Fa-f]{6}$/.test(v)) {
                inputColor.value = v;
                updatePreview(v);
            }
        });
    }
    syncColor(document.getElementById('admin_header_color'), document.getElementById('admin_header_color_hex'));

    // ── Color Palette Interaction ──
    document.addEventListener('click', function (e) {
        var swatch = e.target.closest('.color-swatch');
        if (!swatch) return;

        var palette = swatch.closest('.color-palette');
        if (!palette) return;

        var targetId = palette.getAttribute('data-target');
        var color = swatch.getAttribute('data-color');
        var colorInput = document.getElementById(targetId);
        var hexInput = document.getElementById(targetId + '_hex');
        
        if (colorInput && hexInput) {
            colorInput.value = color;
            hexInput.value = color;
            
            // Highlight active swatch
            palette.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
            swatch.classList.add('active');

            // Force preview update for header
            if (targetId === 'admin_header_color') {
                var saveBtn = document.querySelector('button[type="submit"]');
                if (saveBtn) {
                    saveBtn.style.background = color;
                    saveBtn.style.borderColor = color;
                }
            }
            
            // Still dispatch input in case other listeners are attached (e.g. for unsaved changes warnings)
            try {
                colorInput.dispatchEvent(new Event('input', { bubbles: true }));
            } catch (err) {
                // Fallback for older environments
                if (typeof colorInput.oninput === 'function') colorInput.oninput();
            }
        }
    });

    // ── Logo ──
    var logoInput      = document.getElementById('admin_logo');
    var zone           = document.getElementById('admin-logo-zone');
    var img            = document.getElementById('admin-logo-preview');
    var ph             = document.getElementById('admin-logo-placeholder');
    var removeLogoFlag = document.getElementById('remove_admin_logo');
    var removeLogoBtn  = document.getElementById('admin-logo-remove-btn');

    var logoNewContainer   = document.getElementById('admin-logo-new-container');
    var logoNewPreview     = document.getElementById('admin-logo-new-preview');
    var logoNewPlaceholder = document.getElementById('admin-logo-new-placeholder');

    function showFile(file) {
        console.log('showFile called:', file);
        if (!file) return;
        
        // Use createObjectURL which is often faster/more reliable for large images
        var url = URL.createObjectURL(file);
        console.log('ObjectURL created:', url);
        
        if (logoNewPreview) {
            logoNewPreview.onload = function() {
                console.log('logoNewPreview image loaded successfully');
            };
            logoNewPreview.onerror = function() {
                console.error('logoNewPreview failed to load image');
            };
            logoNewPreview.src = url;
            logoNewPreview.classList.remove('d-none');
        } else {
            console.error('logoNewPreview element not found');
        }
        
        if (logoNewPlaceholder) {
            logoNewPlaceholder.classList.add('d-none');
        }
        
        if (removeLogoFlag) removeLogoFlag.value = '0';
        if (removeLogoBtn) {
            removeLogoBtn.classList.remove('d-none');
            removeLogoBtn.style.display = '';
        }
    }

    if (zone && logoInput) {
        zone.addEventListener('click', function () { logoInput.click(); });
        zone.addEventListener('dragover', function (e) { e.preventDefault(); zone.classList.add('border-admin'); });
        zone.addEventListener('dragleave', function (e) { e.preventDefault(); zone.classList.remove('border-admin'); });
        zone.addEventListener('drop', function (e) {
            e.preventDefault();
            zone.classList.remove('border-admin');
            var file = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
            if (file) {
                var dt = new DataTransfer();
                dt.items.add(file);
                logoInput.files = dt.files;
                showFile(file);
            }
        });
        logoInput.addEventListener('change', function () {
            var file = this.files && this.files[0];
            if (file) showFile(file);
        });
    }

    if (removeLogoBtn) {
        removeLogoBtn.addEventListener('click', function () {
            if (logoInput && logoInput.value) {
                // If there's a pending new selection, clear it
                logoInput.value = '';
                if (logoNewPreview) {
                    logoNewPreview.src = '';
                    logoNewPreview.classList.add('d-none');
                }
                if (logoNewPlaceholder) logoNewPlaceholder.classList.remove('d-none');
                
                // If the current image exists, keep the remove button visible
                if (!img.classList.contains('d-none')) {
                    return;
                }
            }
            
            if (!confirm('Remove the current logo?')) return;
            img.src = ''; img.classList.add('d-none'); ph.classList.remove('d-none');
            if (removeLogoFlag) removeLogoFlag.value = '1';
            logoInput.value = ''; 
            this.classList.add('d-none');
            this.style.display = '';
        });
    }

    // ── Hero ──
    var heroInput = document.getElementById('admin_default_hero');  
    var heroZone       = document.getElementById('admin-hero-zone');
    var heroImg        = document.getElementById('admin-hero-preview');
    var heroPh         = document.getElementById('admin-hero-placeholder');
    var removeHeroFlag = document.getElementById('remove_admin_default_hero');
    var removeHeroBtn  = document.getElementById('admin-hero-remove-btn');

    var heroNewContainer   = document.getElementById('admin-hero-new-container');
    var heroNewPreview     = document.getElementById('admin-hero-new-preview');
    var heroNewPlaceholder = document.getElementById('admin-hero-new-placeholder');

  function showHeroFile(file) {
    if (!file) return;

    console.log("Showing hero preview:", file);

    const url = URL.createObjectURL(file);

    if (!heroNewPreview) {
        console.error("heroNewPreview element missing");
        return;
    }

    // Set preview image
    heroNewPreview.src = url;

    // Show preview
    heroNewPreview.classList.remove('d-none');

    // Hide placeholder
    if (heroNewPlaceholder) {
        heroNewPlaceholder.classList.add('d-none');
    }

    // Reset remove flag
    if (removeHeroFlag) {
        removeHeroFlag.value = '0';
    }

    // Show remove button
    if (removeHeroBtn) {
        removeHeroBtn.classList.remove('d-none');
    }
}
   // Zone interactions
if (heroZone && heroInput) {
    heroZone.addEventListener('click', function () {
        heroInput.click();
    });

    heroZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        heroZone.classList.add('border-admin');
    });

    heroZone.addEventListener('dragleave', function (e) {
        e.preventDefault();
        heroZone.classList.remove('border-admin');
    });

    heroZone.addEventListener('drop', function (e) {
        e.preventDefault();
        heroZone.classList.remove('border-admin');

        const file = e.dataTransfer?.files?.[0];
        if (!file) return;

        const dt = new DataTransfer();
        dt.items.add(file);
        heroInput.files = dt.files;

        showHeroFile(file);
    });
}

if (heroInput) {
    heroInput.addEventListener('change', function () {

        const file = this.files[0];
        if (!file) return;

        console.log("Hero file selected:", file);

        showHeroFile(file);
    });
}
    if (removeHeroBtn) {
        removeHeroBtn.addEventListener('click', function () {
          if (heroInput && heroInput.files && heroInput.files.length > 0) {

            // Clear the selected file
            heroInput.value = '';

            if (heroNewPreview) {
                heroNewPreview.src = '';
                heroNewPreview.classList.add('d-none');
            }

            if (heroNewPlaceholder) {
                heroNewPlaceholder.classList.remove('d-none');
            }

            // If the current image exists, keep the remove button visible
            if (heroImg && !heroImg.classList.contains('d-none')) {
                return;
            }
        }

            if (!confirm('Remove the current hero background?')) return;
            heroImg.src = ''; heroImg.classList.add('d-none'); heroPh.classList.remove('d-none');
            if (removeHeroFlag) removeHeroFlag.value = '1';
            heroInput.value = ''; 
            this.classList.add('d-none');
            this.style.display = '';
        });
    }

    // ── Google Drive Helpers ──
    window.togglePassword = function (id) {
        var input = document.getElementById(id);
        if (input) {
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    };

    window.copyToClipboard = function (inputId, btn) {
        var input = document.getElementById(inputId);
        if (!input) return;
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(function() {
            var oldText = btn.innerText;
            btn.innerText = 'Copied!';
            setTimeout(function() {
                btn.innerText = oldText;
            }, 2000);
        });
    };

    window.togglePasswordVisibility = function (inputId, btn) {
        var input = document.getElementById(inputId);
        if (!input) return;
        
        if (input.type === 'password') {
            input.type = 'text';
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/><line x1="12" y1="9" x2="9" y2="9"/></svg>';
        } else {
            input.type = 'password';
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
        }
    };
});

</script>
