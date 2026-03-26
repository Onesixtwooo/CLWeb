@extends('admin.layout')

@section('title', $institute->name)

@push('styles')
<style>
    .colleges-header-bar {
        background: var(--admin-surface);
        border-bottom: 1px solid var(--admin-border);
        padding: 0.875rem 1.5rem;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 1rem;
        margin: -2rem -2.25rem 1.5rem -2.25rem;
        padding: 1rem 2.25rem;
    }
    .colleges-header-title {
        font-weight: 700;
        font-size: 1.25rem;
        letter-spacing: -0.02em;
        color: var(--admin-text);
        margin: 0;
    }
    .colleges-header-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-left: auto;
    }
    .colleges-breadcrumb {
        font-size: 0.8125rem;
        color: var(--admin-text-muted);
    }
    .colleges-breadcrumb span:last-child { color: var(--admin-text); font-weight: 500; }
    .colleges-layout {
        display: flex;
        gap: 0;
        min-height: 480px;
    }
    .colleges-section-list {
        width: 280px;
        flex-shrink: 0;
        background: var(--admin-surface);
        border: 1px solid var(--admin-border);
        border-radius: var(--admin-radius-lg);
        overflow: hidden;
        margin-right: 1.5rem;
    }
    .colleges-section-list-header {
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid var(--admin-border);
        font-weight: 600;
        font-size: 0.8125rem;
        color: var(--admin-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .colleges-section-item {
        display: block;
        padding: 0.75rem 1.25rem;
        color: var(--admin-text);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9375rem;
        border-left: 3px solid transparent;
        transition: background 0.15s ease, border-color 0.15s ease;
    }
    .colleges-section-item:hover {
        background: var(--admin-accent-soft);
        color: var(--admin-accent);
    }
    .colleges-section-item.active {
        background: var(--admin-accent-soft);
        color: var(--admin-accent);
        border-left-color: var(--admin-accent);
    }
    .colleges-detail {
        flex: 1;
        min-width: 0;
        background: var(--admin-surface);
        border: 1px solid var(--admin-border);
        border-radius: var(--admin-radius-lg);
        overflow: hidden;
        padding: 1.75rem 2rem;
    }
    .colleges-detail-title {
        font-weight: 700;
        font-size: 1.5rem;
        letter-spacing: -0.02em;
        color: var(--admin-text);
        margin-bottom: 1.25rem;
    }
    .colleges-detail-body {
        color: var(--admin-text);
        line-height: 1.65;
    }
    .colleges-detail-body p { margin-bottom: 1rem; }
    .colleges-detail-body ul { margin-bottom: 1rem; padding-left: 1.5rem; }
    .colleges-detail-body li { margin-bottom: 0.35rem; }

    .stat-card {
        background: var(--admin-surface);
        border: 1px solid var(--admin-border);
        border-radius: var(--admin-radius);
        padding: 1rem;
        height: 100%;
    }
    .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--admin-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }
    .stat-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--admin-text);
    }

    /* Banner Grid */
    .banner-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    .banner-item {
        position: relative;
        aspect-ratio: 16/9;
        border-radius: var(--admin-radius);
        overflow: hidden;
        border: 1px solid var(--admin-border);
    }
    .banner-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Responsive Adjustments for Mobile View */
    @media (max-width: 991.98px) {
        .colleges-layout {
            flex-direction: column !important;
            gap: 1.5rem !important;
        }
        .colleges-section-list {
            width: 100% !important;
            margin-right: 0 !important;
            margin-bottom: 1.5rem !important;
        }
        .colleges-header-bar {
            flex-direction: column !important;
            align-items: flex-start !important;
            padding: 1rem !important;
            margin: -1rem -1.25rem 1.5rem -1.25rem !important;
        }
        .colleges-header-actions {
            margin-left: 0 !important;
            width: 100% !important;
            flex-wrap: wrap !important;
            gap: 0.5rem !important;
            margin-top: 0.5rem !important;
        }
        .colleges-breadcrumb {
            width: 100% !important;
            padding: 0.5rem 0 !important;
            display: block !important;
        }
    }

</style>
@endpush

@section('content')
    <div class="colleges-header-bar">
        <div>
            <div class="colleges-breadcrumb">
                <a href="{{ route('admin.colleges.show', $collegeSlug) }}" class="text-decoration-none text-muted">Colleges</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.colleges.show', $collegeSlug) }}" class="text-decoration-none text-muted">{{ $collegeName }}</a>
                <span class="mx-2">/</span>
                <span>{{ $institute->name }}</span>
            </div>
            <h1 class="colleges-header-title mt-1">{{ $institute->name }} Dashboard</h1>
        </div>
        <div class="colleges-header-actions">
            <button class="btn btn-admin-primary btn-sm px-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#editInstituteInfoModal">
                <i class="bi bi-pencil-square me-1"></i> Edit Profile
            </button>
            <a href="{{ route('admin.colleges.show', $collegeSlug) }}" class="btn btn-outline-secondary btn-sm px-3 rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Back to College
            </a>
        </div>
    </div>

    <div class="colleges-layout">
        {{-- Sidebar Section List --}}
        <div class="colleges-section-list">
            <div class="colleges-section-list-header">Institute Sections</div>
            @foreach($sections as $slug => $name)
                <a href="{{ route('admin.colleges.show-institute', ['college' => $collegeSlug, 'institute' => $institute->id, 'section' => $slug]) }}" 
                   class="colleges-section-item {{ $currentSection === $slug ? 'active' : '' }}">
                    {{ $name }}
                </a>
            @endforeach
        </div>

        {{-- Section Detail Content --}}
        <div class="flex-grow-1">
            <div class="colleges-detail">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h2 class="colleges-detail-title mb-1">{{ $sections[$currentSection] }}</h2>
                        <p class="text-muted small mb-0">Manage the content for the {{ strtolower($sections[$currentSection]) }} section.</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.colleges.edit-institute-section', ['college' => $collegeSlug, 'institute' => $institute->id, 'section' => $currentSection]) }}" class="btn btn-sm btn-admin-primary px-3 rounded-pill">
                            <i class="bi bi-pencil me-1"></i> <span class="d-none d-md-inline">Edit Section</span>
                        </a>
                    </div>
                </div>

                <div class="colleges-detail-body">
                    @if($currentSection === 'overview')
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="stat-card">
                                    <div class="stat-label">Contact Email</div>
                                    <div class="stat-value">{{ $institute->email ?? 'Not set' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-card">
                                    <div class="stat-label">Phone Number</div>
                                    <div class="stat-value">{{ $institute->phone ?? 'Not set' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold mb-3">Overview Body</h5>
                            <div class="p-3 bg-light rounded border">
                                {!! !empty($sectionContent['body']) ? nl2br(e($sectionContent['body'])) : '<span class="text-muted fst-italic">No overview content added yet.</span>' !!}
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                                <h5 class="fw-bold mb-0">Banner Images (Max 3)</h5>
                                <a href="{{ route('admin.colleges.edit-institute-section', ['college' => $collegeSlug, 'institute' => $institute->id, 'section' => 'overview']) }}?edit=banners" class="btn btn-outline-secondary btn-sm rounded-pill px-3"><i class="bi bi-images"></i> <span class="d-none d-md-inline">Manage Banners</span></a>
                            </div>
                            @php
                                $bannerImages = $sectionContent['banner_images'] ?? [];
                                if (empty($bannerImages) && !empty($sectionContent['banner_image'])) {
                                    $bannerImages = [$sectionContent['banner_image']];
                                }
                            @endphp
                            @if(!empty($bannerImages))
                                <div class="banner-grid">
                                    @foreach($bannerImages as $img)
                                        <div class="banner-item">
                                            <img src="{{ asset($img) }}" alt="Banner">
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-4 bg-light rounded border text-center text-muted fst-italic">
                                    No banners uploaded yet.
                                </div>
                            @endif
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3">Institute Logo</h5>
                                @if($institute->logo)
                                    <img src="{{ asset($institute->logo) }}" class="img-fluid rounded border p-2 bg-white" style="max-height: 150px;" alt="Logo">
                                @else
                                    <div class="rounded border bg-light d-flex align-items-center justify-content-center text-secondary" style="height: 150px; width: 150px;">
                                        <i class="bi bi-image fs-1 opacity-50"></i>
                                    </div>
                                    <p class="text-muted small mt-2 mb-0 fst-italic">No logo uploaded.</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3">Social Media</h5>
                                <ul class="list-unstyled">
                                    <li><i class="bi bi-facebook me-2 text-primary"></i> {{ $sectionContent['social_facebook'] ?? 'Not set' }}</li>
                                    <li><i class="bi bi-twitter-x me-2"></i> {{ $sectionContent['social_x'] ?? 'Not set' }}</li>
                                    <li><i class="bi bi-linkedin me-2 text-info"></i> {{ $sectionContent['social_linkedin'] ?? 'Not set' }}</li>
                                    <li><i class="bi bi-instagram me-2 text-danger"></i> {{ $sectionContent['social_instagram'] ?? 'Not set' }}</li>
                                </ul>
                            </div>
                        </div>
                    @elseif($currentSection === 'goals')
                        @if(isset($sectionContent['goals']) && $sectionContent['goals']->count() > 0)
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-body">
                                    <h5 class="fw-bold mb-3">Goals</h5>
                                    <ul class="list-group list-group-flush">
                                        @foreach($sectionContent['goals'] as $index => $goal)
                                            <li class="list-group-item d-flex align-items-start px-0">
                                                <span class="badge bg-admin-primary rounded-pill me-3">{{ $index + 1 }}</span>
                                                <div>{{ $goal->content }}</div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @else
                            <div class="text-center p-5 bg-light rounded border border-dashed">
                                <i class="bi bi-bullseye display-4 text-muted mb-3 d-block"></i>
                                <h5 class="text-muted">No goals found.</h5>
                                <p class="small text-muted mb-4">Click "Edit Section" to add goals.</p>
                                <a href="{{ route('admin.colleges.edit-institute-section', ['college' => $collegeSlug, 'institute' => $institute->id, 'section' => $currentSection]) }}" class="btn btn-admin-primary btn-sm px-4 rounded-pill">
                                    Add Goals
                                </a>
                            </div>
                        @endif

                    @elseif($currentSection === 'history')
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">History</h5>
                                @if(!empty($sectionContent['history']))
                                    <div class="prose max-w-none">
                                        {!! nl2br(e($sectionContent['history'])) !!}
                                    </div>
                                @else
                                    <p class="text-muted fst-italic">No history content added yet.</p>
                                    <div class="mt-3 text-center">
                                         <a href="{{ route('admin.colleges.edit-institute-section', ['college' => $collegeSlug, 'institute' => $institute->id, 'section' => $currentSection]) }}" class="btn btn-admin-primary btn-sm px-4 rounded-pill">
                                            Add History
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                    @elseif($currentSection === 'staff')
                        @if(isset($sectionContent['staff']) && $sectionContent['staff']->count() > 0)
                             <div class="row g-3">
                                @foreach($sectionContent['staff'] as $member)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 shadow-sm border-0 bg-light">
                                            <div class="card-body d-flex align-items-center gap-3">
                                                <div class="flex-shrink-0">
                                                    @if($member->photo)
                                                        <img src="{{ asset($member->photo) }}" class="rounded-circle" style="width: 64px; height: 64px; object-fit: cover;" alt="{{ $member->name }}">
                                                    @else
                                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 64px; height: 64px;">
                                                            <span class="fs-4">{{ substr($member->name, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1 min-width-0">
                                                    <h6 class="fw-bold mb-1 text-truncate">{{ $member->name }}</h6>
                                                    <p class="text-muted small mb-0 text-truncate">{{ $member->position }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                             <div class="text-center p-5 bg-light rounded border border-dashed">
                                <i class="bi bi-people display-4 text-muted mb-3 d-block"></i>
                                <h5 class="text-muted">No staff members found.</h5>
                                <p class="small text-muted mb-4">Click "Edit Section" to add staff.</p>
                                <a href="{{ route('admin.colleges.edit-institute-section', ['college' => $collegeSlug, 'institute' => $institute->id, 'section' => $currentSection]) }}" class="btn btn-admin-primary btn-sm px-4 rounded-pill">
                                    Add Staff
                                </a>
                            </div>
                        @endif

                    @else
                        {{-- Generic Section View --}}
                        <div class="section-preview">
                            @if(isset($sectionContent['items']) && is_array($sectionContent['items']) && count($sectionContent['items']) > 0)
                                <div class="row g-3">
                                    @foreach($sectionContent['items'] as $item)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="card h-100 shadow-sm border-0 bg-light">
                                                @if(!empty($item['image']))
                                                    <img src="{{ asset($item['image']) }}" class="card-img-top object-fit-cover" style="height: 160px;" alt="{{ $item['title'] }}">
                                                @elseif(!empty($item->image))
                                                     <img src="{{ asset($item->image) }}" class="card-img-top object-fit-cover" style="height: 160px;" alt="{{ $item->title ?? $item['title'] }}">
                                                @endif
                                                <div class="card-body">
                                                    <h6 class="fw-bold mb-2">{{ $item['title'] ?? $item->title }}</h6>
                                                    @isset($item['year_graduated'])
                                                        <div class="badge bg-secondary mb-2">Class of {{ $item['year_graduated'] }}</div>
                                                    @endisset
                                                    @isset($item->year_graduated)
                                                        <div class="badge bg-secondary mb-2">Class of {{ $item->year_graduated }}</div>
                                                    @endisset
                                                    
                                                    @if(!empty($item['description']))
                                                        <p class="card-text small text-muted text-truncate-3">{{ $item['description'] }}</p>
                                                    @elseif(!empty($item->description))
                                                         <p class="card-text small text-muted text-truncate-3">{{ $item->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($currentSection === 'faculty')
                                @if($facultyList->count() > 0)
                                    <div class="row g-3">
                                        @foreach($facultyList as $member)
                                            <div class="col-md-6 col-lg-4">
                                                <div class="card h-100 shadow-sm border-0 bg-light">
                                                    <div class="card-body d-flex align-items-center gap-3">
                                                        <div class="flex-shrink-0">
                                                            @if($member->photo)
                                                                <img src="{{ asset($member->photo) }}" class="rounded-circle" style="width: 48px; height: 48px; object-fit: cover;" alt="{{ $member->name }}">
                                                            @else
                                                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 48px; height: 48px;">
                                                                    {{ substr($member->name, 0, 1) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1 min-width-0">
                                                            <h6 class="fw-bold mb-0 text-truncate">{{ $member->name }}</h6>
                                                            <p class="text-muted small mb-0 text-truncate">{{ $member->designation }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-4 bg-light rounded text-center text-muted">
                                        No faculty members assigned to this institute yet.
                                    </div>
                                @endif
                            @else
                                <div class="text-center p-5 bg-light rounded border border-dashed">
                                    <i class="bi bi-folder2-open display-4 text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted">No content found in this section.</h5>
                                    <p class="small text-muted mb-4">Click "Edit Section" to add content items.</p>
                                    <a href="{{ route('admin.colleges.edit-institute-section', ['college' => $collegeSlug, 'institute' => $institute->id, 'section' => $currentSection]) }}" class="btn btn-admin-primary btn-sm px-4 rounded-pill">
                                        Add Content Now
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if($currentSection === 'overview')

            @endif
        </div>
    </div>

    {{-- Edit Institute Info Modal --}}
    <div class="modal fade" id="editInstituteInfoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Institute Info</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.colleges.update-institute', ['college' => $collegeSlug, 'institute' => $institute->id]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Institute Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $institute->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $institute->email) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $institute->phone) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="details" class="form-control quill-editor" rows="3">{{ old('details', $institute->details ?? '') }}</textarea>
                            <small class="text-muted">Optional short description of the institute.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Logo</label>
                            @if (!empty($institute->logo))
                                <div class="mb-2">
                                    <img src="{{ $institute->logo }}" alt="logo" style="max-width: 100px; max-height: 100px; object-fit: contain;" class="rounded">
                                </div>
                            @endif
                            <input type="file" name="logo" class="form-control" accept="image/*">
                            <small class="text-muted">Leave empty to keep current logo.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-admin-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
