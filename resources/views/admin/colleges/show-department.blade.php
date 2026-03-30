@extends('admin.layout')

@section('title', $department->name)

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
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.75rem 1.25rem;
        color: var(--admin-text);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9375rem;
        border-left: 3px solid transparent;
        transition: background 0.15s ease, border-color 0.15s ease;
    }
    .colleges-section-label {
        min-width: 0;
    }
    .colleges-section-status-dot {
        width: 0.625rem;
        height: 0.625rem;
        border-radius: 999px;
        flex-shrink: 0;
        box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.08);
    }
    .colleges-section-status-dot.is-visible {
        background: #198754;
    }
    .colleges-section-status-dot.is-hidden {
        background: #dc3545;
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
    .section-toolbar {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .organization-toolbar {
        justify-content: flex-end;
    }
    .organization-intro-card,
    .organization-table-card {
        border: 1px solid var(--admin-border);
        border-radius: 1.5rem;
        background: var(--admin-surface);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    }
    .organization-intro-copy {
        max-width: 72rem;
    }
    .organization-intro-copy p:last-child {
        margin-bottom: 0;
    }
    .organization-table {
        margin-bottom: 0;
    }
    .organization-table thead th {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--admin-text);
        white-space: nowrap;
    }
    .organization-table tbody td {
        vertical-align: middle;
    }
    .organization-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        min-width: 260px;
    }
    .organization-logo {
        width: 58px;
        height: 58px;
        border-radius: 0.75rem;
        overflow: hidden;
        flex-shrink: 0;
        background: #fff;
        border: 1px solid var(--admin-border);
    }
    .organization-name {
        font-weight: 600;
        color: var(--admin-text);
        margin-bottom: 0;
    }
    .organization-scope {
        color: #2563eb;
        font-weight: 500;
    }
    .organization-status-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 0.25rem 0.6rem;
        font-size: 0.875rem;
        font-weight: 700;
    }
    .organization-status-badge.is-visible {
        background: #198754;
        color: #fff;
    }
    .organization-status-badge.is-hidden {
        background: #6c757d;
        color: #fff;
    }
    .organization-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.625rem;
        flex-wrap: wrap;
    }
    .organization-actions form {
        margin: 0;
    }
    .organization-section-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .organization-title {
        font-size: 1.5rem;
    }
    .organization-edit-text,
    .organization-add-text {
        display: inline;
    }
    .department-heading {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }
    .department-heading-copy {
        min-width: 0;
    }
    .department-logo-panel {
        position: relative;
        width: 120px;
        height: 120px;
        border-radius: 1rem;
        overflow: hidden;
        background: #f8fafc;
        border: 1px solid var(--admin-border);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .department-logo-panel img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
</style>
@endpush

@section('content')
    {{-- Top header bar --}}
    <div class="mb-4">
        @php
            $user = auth()->user();
        @endphp
        <div class="d-flex justify-content-between align-items-center">
            <div class="department-heading">
                @if (!$user || !$user->isBoundedToDepartment())
                    <a href="{{ route('admin.colleges.show', ['college' => $collegeSlug, 'section' => 'departments']) }}" class="btn btn-outline-secondary btn-sm" title="Back to Departments">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                @endif
                <div class="department-heading-copy">
                    <h1 class="h3 mb-1">{{ $department->name }}</h1>
                    <p class="text-muted small mb-0">{{ $collegeName }}</p>
                </div>
            </div>
            @if (!$user || !$user->isBoundedToDepartment() || $user->isAdmin())
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editDepartmentInfoModal" title="Edit Department Info"><i class="bi bi-pencil-square"></i> <span class="d-none d-md-inline">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                </button>
            @endif
        </div>
    </div>

    <div class="colleges-layout">
        {{-- Left: section list --}}
        <aside class="colleges-section-list">
            <div class="colleges-section-list-header">Sections</div>
            @php
                $sectionVisibility = [
                    'overview' => true,
                    'objectives' => (bool) ($department->objectives_is_visible ?? true),
                    'faculty' => (bool) ($department->faculty_is_visible ?? true),
                    'programs' => (bool) ($department->programs_is_visible ?? true),
                    'awards' => (bool) ($department->awards_is_visible ?? true),
                    'research' => (bool) ($department->research_is_visible ?? true),
                    'linkages' => (bool) ($department->linkages_is_visible ?? true),
                    'extension' => (bool) ($department->extension_is_visible ?? false),
                    'training' => (bool) ($department->training_is_visible ?? false),
                    'membership' => (bool) ($department->membership_is_visible ?? false),
                    'organizations' => (bool) ($department->organizations_is_visible ?? true),
                    'facilities' => (bool) ($department->facilities_is_visible ?? false),
                    'alumni' => (bool) ($department->alumni_is_visible ?? false),
                ];
            @endphp
            <nav>
                @foreach ($sections as $slug => $name)
                    @php
                        $isVisible = $sectionVisibility[$slug] ?? true;
                    @endphp
                    <a href="{{ route('admin.colleges.show-department', ['college' => $collegeSlug, 'department' => $department, 'section' => $slug]) }}"
                       class="colleges-section-item {{ $currentSection === $slug ? 'active' : '' }}">
                        <span class="colleges-section-label">{{ $slug === 'admissions' ? 'Admissions' : $name }}</span>
                        <span class="colleges-section-status-dot {{ $isVisible ? 'is-visible' : 'is-hidden' }}" title="{{ $isVisible ? 'Visible on Public Page' : 'Hidden from Public Page' }}" aria-label="{{ $isVisible ? 'Visible on Public Page' : 'Hidden from Public Page' }}"></span>
                    </a>
                @endforeach
            </nav>
        </aside>

        {{-- Right: detail content --}}
        <div style="flex: 1; min-width: 0;">
            <div class="colleges-detail">
                @if ($currentSection === 'overview')
                    <div class="mb-3">
                        <div class="department-logo-panel">
                        @if (!empty($department->logo))
                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($department->logo) }}" alt="{{ $department->name }} logo" class="rounded">
                            @else
                                <div class="text-muted small text-center px-3" style="color: #ccc;">
                                    No logo uploaded
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($currentSection !== 'organizations')
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <h2 class="colleges-detail-title mb-0">{{ $sectionContent['title'] }}</h2>
                        @if ($currentSection === 'faculty')
                            <a href="{{ route('admin.colleges.edit-department-section', ['college' => $collegeSlug, 'department' => $department, 'section' => $currentSection]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil-square me-1"></i> <span class="d-none d-md-inline">Edit section details</span>
                            </a>
                        @else
                            @if($currentSection === 'overview')
                                <a href="{{ route('admin.colleges.edit-department-section', ['college' => $collegeSlug, 'department' => $department, 'section' => $currentSection]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-square me-1"></i> <span class="d-none d-md-inline">Edit section details</span>
                                </a>
                            @elseif($currentSection === 'awards')
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addAwardModal">
                                        <i class="bi bi-plus-circle me-1"></i> <span class="d-none d-md-inline">Add Award</span>
                                    </button>
                                    <a href="{{ route('admin.colleges.edit-department-section', ['college' => $collegeSlug, 'department' => $department, 'section' => $currentSection]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i> <span class="d-none d-md-inline">Edit section details</span>
                                    </a>
                                </div>
                            @elseif($currentSection === 'research')
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addResearchModal">
                                        <i class="bi bi-plus-circle me-1"></i> <span class="d-none d-md-inline">Add Research</span>
                                    </button>
                                    <a href="{{ route('admin.colleges.edit-department-section', ['college' => $collegeSlug, 'department' => $department, 'section' => $currentSection]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i> <span class="d-none d-md-inline">Edit section details</span>
                                    </a>
                                </div>
                            @elseif($currentSection === 'extension')
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('admin.colleges.create-department-extension', ['college' => $collegeSlug, 'department' => $department]) }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-plus-circle me-1"></i> <span class="d-none d-md-inline">Add Extension</span>
                                    </a>
                                    <a href="{{ route('admin.colleges.edit-department-section', ['college' => $collegeSlug, 'department' => $department, 'section' => $currentSection]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i> <span class="d-none d-md-inline">Edit section details</span>
                                    </a>
                                </div>
                            @elseif($currentSection === 'training')
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('admin.colleges.create-department-training', ['college' => $collegeSlug, 'department' => $department]) }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-plus-circle me-1"></i> <span class="d-none d-md-inline">Add Training</span>
                                    </a>
                                    <a href="{{ route('admin.colleges.edit-department-section', ['college' => $collegeSlug, 'department' => $department, 'section' => $currentSection]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i> <span class="d-none d-md-inline">Edit section details</span>
                                    </a>
                                </div>
                            @elseif($currentSection === 'objectives')
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('admin.colleges.edit-department-section', ['college' => $collegeSlug, 'department' => $department, 'section' => $currentSection]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i> <span class="d-none d-md-inline">Edit section details</span>
                                    </a>
                                    <a href="{{ route('admin.colleges.create-department-objective', ['college' => $collegeSlug, 'department' => $department]) }}" class="btn btn-sm btn-outline-primary" title="Add objective" aria-label="Add objective">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    </a>
                                </div>
                            @elseif($currentSection === 'programs')
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('admin.colleges.edit-department-section', ['college' => $collegeSlug, 'department' => $department, 'section' => $currentSection]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i> <span class="d-none d-md-inline">Edit section details</span>
                                    </a>
                                    <a href="{{ route('admin.colleges.create-department-program', ['college' => $collegeSlug, 'department' => $department]) }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-plus-circle me-1"></i> <span class="d-none d-md-inline">Add Program</span>
                                    </a>
                                </div>
                            @elseif($currentSection === 'linkages')
                                <a href="{{ route('admin.colleges.edit-department-section', ['college' => $collegeSlug, 'department' => $department, 'section' => $currentSection]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-square me-1"></i> <span class="d-none d-md-inline">Edit section details</span>
                                </a>
                            @elseif($currentSection === 'alumni')
                                <a href="{{ route('admin.colleges.edit-department-section', ['college' => $collegeSlug, 'department' => $department, 'section' => $currentSection]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-square me-1"></i> <span class="d-none d-md-inline">Edit section details</span>
                                </a>
                            @else
                                <a href="{{ route('admin.colleges.edit-department-section', ['college' => $collegeSlug, 'department' => $department, 'section' => $currentSection]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-square me-1"></i> <span class="d-none d-md-inline">Edit section details</span>
                                </a>
                            @endif
                        @endif
                    </div>
                @endif

                <div class="colleges-detail-body">
                    @if ($currentSection === 'overview')
                        @php
                            $isVisible = $sectionContent['is_visible'] ?? true;
                        @endphp

                        <div class="mb-3">
                            @if($isVisible)
                                <span class="badge bg-success">Visible on Public Page</span>
                            @else
                                <span class="badge bg-secondary">Hidden from Public Page</span>
                            @endif
                        </div>
                    @endif

                    @if ($currentSection === 'faculty')
                        @php
                            $isVisible = $sectionContent['is_visible'] ?? true;
                        @endphp

                        <div class="mb-3">
                            @if($isVisible)
                                <span class="badge bg-success">Visible on Public Page</span>
                            @else
                                <span class="badge bg-secondary">Hidden from Public Page</span>
                            @endif
                        </div>

                        @if(!empty($sectionContent['body']))
                            <div class="mb-4 text-muted" style="line-height: 1.7;">
                                {!! $sectionContent['body'] !!}
                            </div>
                        @endif

                        <div class="mb-3">
                            <a href="{{ route('admin.faculty.create-department', ['college' => $collegeSlug, 'department' => $department]) }}" class="btn btn-admin-primary btn-sm"><i class="bi bi-person-plus"></i> <span class="d-none d-md-inline">Add Faculty Member</span></a>
                        </div>
                        
                        @if ($facultyList->isEmpty())
                            <p class="text-muted">No faculty members assigned to this department yet.</p>
                        @else
                            <div class="row g-4">
                                @foreach ($facultyList as $member)
                                    <div class="col-6 col-sm-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                                            {{-- Faculty Photo --}}
                                            <div class="position-relative" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding-top: 100%;">
                                                @if (!empty($member->photo))
                                                    <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($member->photo, 'images') }}" alt="{{ $member->name }}" class="position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;">
                                                @else
                                                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                                            <circle cx="12" cy="7" r="4"></circle>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            {{-- Faculty Info --}}
                                            <div class="card-body p-3" style="background: #2d3748; color: white;">
                                                <h5 class="card-title mb-1 fw-600" style="font-size: 1rem;">{{ $member->name }}</h5>
                                                <p class="card-text mb-1" style="font-size: 0.875rem; color: #cbd5e0;">{{ $member->position ?? 'Faculty Member' }}</p>
                                                @if (!empty($member->department))
                                                    <p class="card-text mb-1" style="font-size: 0.8rem; color: #a0aec0;">{{ $member->department }}</p>
                                                @endif
                                                @if (!empty($member->email))
                                                    <p class="card-text mb-2" style="font-size: 0.75rem; color: #a0aec0; word-break: break-word;">{{ $member->email }}</p>
                                                @endif
                                                
                                                {{-- Action Buttons --}}
                                                <div class="d-flex gap-2 mt-3">
                                                    <a href="{{ route('admin.faculty.edit-department', ['college' => $collegeSlug, 'department' => $department, 'faculty' => $member]) }}" class="btn btn-sm btn-outline-light" style="font-size: 0.75rem;" title="Edit"><i class="bi bi-pencil"></i></a>
                                                    <form action="{{ route('admin.faculty.destroy', $member) }}" method="POST"  class="" onsubmit="return confirm('Remove this faculty member?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="return_college" value="{{ $collegeSlug }}">
                                                        <input type="hidden" name="return_department" value="{{ $department->getRouteKey() }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size: 0.75rem;" title="Delete"><i class="bi bi-trash"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @elseif ($currentSection === 'objectives')
                        @php
                            $isVisible = $sectionContent['is_visible'] ?? true;
                            $objectives = $sectionContent['items'] ?? [];
                            $objectivesBody = trim((string) ($sectionContent['body'] ?? ''));
                        @endphp

                        <div class="mb-3">
                            @if($isVisible)
                                <span class="badge bg-success">Visible on Public Page</span>
                            @else
                                <span class="badge bg-secondary">Hidden from Public Page</span>
                            @endif
                        </div>

                        @if($objectivesBody !== '')
                            <div class="mb-4 colleges-detail-body">
                                {!! $objectivesBody !!}
                            </div>
                        @endif

                        @if(!empty($objectives))
                            <ol class="list-group list-group-numbered mb-4 mr-1">
                                @foreach($objectives as $objective)
                                    <li class="list-group-item border-0 border-bottom d-flex justify-content-between align-items-start gap-3">
                                        <div class="flex-grow-1">{!! $objective['content'] ?? '' !!}</div>
                                        @if(auth()->user() && auth()->user()->canAccessCollege($collegeSlug))
                                            <div class="d-flex gap-2 flex-shrink-0">
                                                <a href="{{ route('admin.colleges.edit-department-objective', ['college' => $collegeSlug, 'department' => $department, 'objective' => $objective['id']]) }}" class="btn btn-sm btn-outline-secondary bg-white" title="Edit objective" aria-label="Edit objective">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                </a>
                                                <form action="{{ route('admin.colleges.update-department', ['college' => $collegeSlug, 'department' => $department]) }}" method="POST" onsubmit="return confirm('Delete this objective?');">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="save_dept_section" value="1">
                                                    <input type="hidden" name="section" value="objectives">
                                                    <input type="hidden" name="_objectives_edit" value="1">
                                                    <input type="hidden" name="delete_objective" value="{{ $objective['id'] }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger bg-white" title="Remove objective" aria-label="Remove objective">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        @elseif($objectivesBody === '')
                            <div class="alert alert-info mb-4">No objectives added yet.</div>
                        @endif

                        {{-- Curriculum Section --}}
                        <div class="border-top pt-4 mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h4 class="h5 fw-bold mb-0">Curriculum</h4>
                                    @if(!empty($sectionContent['curriculum_title']) || !empty($sectionContent['curriculum_body']))
                                        <div class="mt-2">
                                            @if(!empty($sectionContent['curriculum_title']))
                                                <div class="fw-semibold text-dark">{{ $sectionContent['curriculum_title'] }}</div>
                                            @endif
                                            @if(!empty($sectionContent['curriculum_body']))
                                                <div class="ql-editor p-0 text-muted small" style="height: auto;">{!! $sectionContent['curriculum_body'] !!}</div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                @if(auth()->user() && auth()->user()->canAccessCollege($collegeSlug))
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('admin.colleges.edit-department-curriculum-section', ['college' => $collegeSlug, 'department' => $department]) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil-square me-1"></i> <span class="d-none d-md-inline">Edit</span>
                                        </a>
                                        <a href="{{ route('admin.colleges.create-department-curriculum', ['college' => $collegeSlug, 'department' => $department]) }}" class="btn btn-sm btn-outline-primary" title="Add curriculum" aria-label="Add curriculum">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                            @php
                                $curriculum = $sectionContent['curriculum'] ?? [];
                            @endphp
                            @if(!empty($curriculum))
                                <div class="row g-3">
                                    @foreach($curriculum as $category)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="card h-100 bg-light border-0 shadow-sm position-relative">
                                                @if(auth()->user() && auth()->user()->canAccessCollege($collegeSlug))
                                                    <div class="position-absolute top-0 end-0 d-flex gap-2 p-2 z-3">
                                                        <a href="{{ route('admin.colleges.edit-department-curriculum', ['college' => $collegeSlug, 'department' => $department, 'curriculum' => \Illuminate\Support\Str::slug($category['title'] ?? '')]) }}" class="btn btn-sm btn-outline-secondary bg-white" title="Edit curriculum" aria-label="Edit curriculum">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                        </a>
                                                        <form action="{{ route('admin.colleges.update-department', ['college' => $collegeSlug, 'department' => $department]) }}" method="POST" onsubmit="return confirm('Delete this curriculum category?');">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="save_dept_section" value="1">
                                                            <input type="hidden" name="section" value="objectives">
                                                            <input type="hidden" name="_curriculum_edit" value="1">
                                                            <input type="hidden" name="delete_curriculum" value="{{ $category['id'] ?? '' }}">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger bg-white" title="Remove curriculum" aria-label="Remove curriculum">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                                <div class="card-body">
                                                    <h6 class="card-title fw-bold text-dark mb-2">{{ $category['title'] }}</h6>
                                                    @if(!empty($category['courses']))
                                                        <div class="courses-list mt-2 small">
                                                            {!! $category['courses'] !!}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-muted fst-italic">No curriculum information added.</div>
                            @endif
                        </div>
                    @elseif ($currentSection === 'programs')
                        @php
                            $programs = $department->programs ?? [];
                            $isVisible = $department->programs_is_visible ?? true;
                        @endphp

                        <div class="mb-3">
                            @if($isVisible)
                                <span class="badge bg-success">Visible on Public Page</span>
                            @else
                                <span class="badge bg-secondary">Hidden from Public Page</span>
                            @endif
                        </div>

                        @if(!empty($sectionContent['body']))
                            <div class="mb-4 text-muted" style="line-height: 1.7;">
                                {!! $sectionContent['body'] !!}
                            </div>
                        @endif

                        @if(!empty($programs) && $programs->count() > 0)
                                <div class="row g-4">
                                @foreach($programs as $program)
                                    <div class="col-12">
                                        <div class="card shadow-sm border-0 bg-light overflow-hidden">
                                            <div class="row g-0 align-items-stretch">
                                                @if(!empty($program->image))
                                                    <div class="col-md-4 col-lg-3">
                                                        <div class="h-100" style="min-height: 220px;">
                                                            <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($program->image) }}" class="w-100 h-100 object-fit-cover" alt="{{ $program->title }}">
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="{{ !empty($program->image) ? 'col-md-8 col-lg-9' : 'col-12' }}">
                                                    <div class="card-body p-4">
                                                        <div class="d-flex flex-column flex-xl-row align-items-start justify-content-between gap-3 mb-3">
                                                            <div class="flex-grow-1 min-w-0">
                                                                <h5 class="fw-bold mb-2">{{ $program->title ?? 'Program Item' }}</h5>
                                                                @if(!empty($program->numbered_content) && is_array($program->numbered_content))
                                                                    <span class="badge rounded-pill text-bg-light border">{{ count($program->numbered_content) }} items</span>
                                                                @endif
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                                                <a href="{{ route('admin.colleges.edit-department-program', ['college' => $collegeSlug, 'department' => $department, 'program' => $program->getRouteKey()]) }}" class="btn btn-sm btn-outline-secondary" title="Edit program">
                                                                    <i class="bi bi-pencil-square me-1"></i><span class="d-none d-md-inline">Edit</span>
                                                                </a>
                                                                <form action="{{ route('admin.colleges.update-department', ['college' => $collegeSlug, 'department' => $department]) }}" method="POST" onsubmit="return confirm('Remove this program?');">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="save_dept_section" value="1">
                                                                    <input type="hidden" name="section" value="programs">
                                                                    <input type="hidden" name="_programs_edit" value="1">
                                                                    <input type="hidden" name="delete_program" value="{{ $program->id }}">
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove program">
                                                                        <i class="bi bi-trash me-1"></i><span class="d-none d-md-inline">Remove</span>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                        @if(!empty($program->description))
                                                            <div class="text-muted mb-3" style="line-height: 1.7;">
                                                                {!! $program->description !!}
                                                            </div>
                                                        @endif

                                                        @if(!empty($program->numbered_content) && is_array($program->numbered_content))
                                                            <div class="border-top pt-3">
                                                                <div class="small text-uppercase fw-bold text-muted mb-2" style="letter-spacing: 0.05em;">Highlights</div>
                                                                @foreach($program->numbered_content as $content)
                                                                    <div class="row g-2 mb-3 align-items-start">
                                                                        <div class="col-12 col-lg-3">
                                                                            <div class="fw-bold small text-dark" style="line-height: 1.5; overflow-wrap: anywhere; word-break: break-word;">
                                                                                {{ $content['label'] ?? '' }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-lg-9">
                                                                            <div class="text-muted small" style="line-height: 1.7; overflow-wrap: anywhere; word-break: break-word;">{!! $content['text'] ?? '' !!}</div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                        @else
                            <div class="text-muted fst-italic">No programs listed yet.</div>
                        @endif
                    @elseif ($currentSection === 'awards')
                        @php
                            $awards = $sectionContent['items'] ?? [];
                            $isVisible = $sectionContent['is_visible'] ?? true;
                        @endphp

                        <div class="mb-3">
                            @if($isVisible)
                                <span class="badge bg-success">Visible on Public Page</span>
                            @else
                                <span class="badge bg-secondary">Hidden from Public Page</span>
                            @endif
                        </div>



                        @if(!empty($awards))
                            <div class="row g-4">
                                @foreach($awards as $award)
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="card h-100 shadow-sm border-0 bg-light">
                                            @if(!empty($award['image']))
                                                <div class="ratio ratio-16x9">
                                                    <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($award['image']) }}" class="card-img-top object-fit-cover" alt="{{ $award['title'] }}">
                                                </div>
                                            @else
                                                <div class="ratio ratio-16x9 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center">
                                                    @if($department->logo)
                                                        <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($department->logo) }}" class="img-fluid p-4" alt="{{ $department->name }} logo" style="max-height: 100%; object-fit: contain;">
                                                    @else
                                                        <span class="text-muted small">No Image</span>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="card-body d-flex flex-column">
                                                <h6 class="fw-bold mb-2">{{ $award['title'] }}</h6>
                                                @if(!empty($award['description']))
                                                    <p class="card-text small text-muted flex-grow-1">{{ $award['description'] }}</p>
                                                @endif
                                                <div class="d-flex justify-content-end gap-2 mt-3">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editAwardModal_{{ $award['id'] }}" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form action="{{ route('admin.colleges.update-department', ['college' => $collegeSlug, 'department' => $department]) }}" method="POST" onsubmit="return confirm('Delete this award?')" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="save_dept_section" value="1">
                                                        <input type="hidden" name="section" value="awards">
                                                        <input type="hidden" name="_awards_edit" value="1">
                                                        <input type="hidden" name="delete_award" value="{{ $award['id'] }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted fst-italic">No awards listed. Click "Edit section details" to add content.</div>
                        @endif

                        {{-- Award Modals --}}
                        <!-- Add Award Modal -->
                        <div class="modal fade" id="addAwardModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add New Award</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.colleges.update-department', ['college' => $collegeSlug, 'department' => $department]) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="save_dept_section" value="1">
                                        <input type="hidden" name="section" value="awards">
                                        <input type="hidden" name="_awards_edit" value="1">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Award Title <span class="text-danger">*</span></label>
                                                <input type="text" name="award_title" class="form-control" required placeholder="e.g., Best Capstone Project">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea name="award_description" class="form-control" rows="3" placeholder="Brief description..."></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Image</label>
                                                <input type="file" name="award_image" class="form-control" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-admin-primary">Add Award</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Award Modal -->
                        @foreach($awards as $award)
                            <div class="modal fade" id="editAwardModal_{{ $award['id'] }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Award</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('admin.colleges.update-department', ['college' => $collegeSlug, 'department' => $department]) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="save_dept_section" value="1">
                                            <input type="hidden" name="section" value="awards">
                                            <input type="hidden" name="_awards_edit" value="1">
                                            <input type="hidden" name="award_id" value="{{ $award['id'] }}">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Award Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="award_title" class="form-control" value="{{ $award['title'] }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="award_description" class="form-control" rows="3">{{ $award['description'] ?? '' }}</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Image</label>
                                                    @if(!empty($award['image']))
                                                        <div class="mb-2">
                                                            <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($award['image']) }}" alt="Award Image" class="img-fluid rounded border" style="max-height: 100px; object-fit: contain;">
                                                        </div>
                                                    @endif
                                                    <input type="file" name="award_image" class="form-control" accept="image/*">
                                                    <small class="text-muted">Leave empty to keep current image.</small>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-admin-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @elseif ($currentSection === 'research')
                        @php
                            $research = $sectionContent['items'] ?? [];
                            $isVisible = $sectionContent['is_visible'] ?? true;
                        @endphp

                        <div class="mb-3">
                            @if($isVisible)
                                <span class="badge bg-success">Visible on Public Page</span>
                            @else
                                <span class="badge bg-secondary">Hidden from Public Page</span>
                            @endif
                        </div>



                        @if(!empty($research))
                            <div class="row g-4">
                                @foreach($research as $item)
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="card h-100 shadow-sm border-0 bg-light">
                                            @if(!empty($item['image']))
                                                <div class="ratio ratio-16x9">
                                                    <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($item['image']) }}" class="card-img-top object-fit-cover" alt="{{ $item['title'] }}">
                                                </div>
                                            @else
                                                <div class="ratio ratio-16x9 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center">
                                                    @if($department->logo)
                                                        <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($department->logo) }}" class="img-fluid p-4" alt="{{ $department->name }} logo" style="max-height: 100%; object-fit: contain;">
                                                    @else
                                                        <span class="text-muted small">No Image</span>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="card-body d-flex flex-column">
                                                <h6 class="fw-bold mb-1">{{ $item['title'] }}</h6>
                                                @if(!empty($item['completed_year']))
                                                    <div class="text-secondary small mb-2">
                                                        <i class="bi bi-calendar-check me-1"></i> {{ $item['completed_year'] }}
                                                    </div>
                                                @endif
                                                @if(!empty($item['description']))
                                                    <p class="card-text small text-muted flex-grow-1">{{ $item['description'] }}</p>
                                                @endif
                                                <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editResearchModal_{{ $item['id'] }}" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form action="{{ route('admin.colleges.update-department', ['college' => $collegeSlug, 'department' => $department]) }}" method="POST" onsubmit="return confirm('Delete this research item?')" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="save_dept_section" value="1">
                                                        <input type="hidden" name="section" value="research">
                                                        <input type="hidden" name="_research_edit" value="1">
                                                        <input type="hidden" name="delete_research" value="{{ $item['id'] }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted fst-italic">No research items listed.</div>
                        @endif

                        {{-- Research Modals --}}
                        <!-- Add Research Modal -->
                        <div class="modal fade" id="addResearchModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add New Research</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.colleges.update-department', ['college' => $collegeSlug, 'department' => $department]) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="save_dept_section" value="1">
                                        <input type="hidden" name="section" value="research">
                                        <input type="hidden" name="_research_edit" value="1">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Research Title <span class="text-danger">*</span></label>
                                                <input type="text" name="research_title" class="form-control" required placeholder="e.g., Sustainable Agriculture">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Year Completed/Published</label>
                                                <input type="text" name="research_completed_year" class="form-control" placeholder="e.g., 2024 or 2023-2024">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea name="research_description" class="form-control" rows="3" placeholder="Brief description..."></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Image</label>
                                                <input type="file" name="research_image" class="form-control" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-admin-primary">Add Research</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Research Modal -->
                        @foreach($research as $item)
                            <div class="modal fade" id="editResearchModal_{{ $item['id'] }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Research</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('admin.colleges.update-department', ['college' => $collegeSlug, 'department' => $department]) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="save_dept_section" value="1">
                                            <input type="hidden" name="section" value="research">
                                            <input type="hidden" name="_research_edit" value="1">
                                            <input type="hidden" name="research_id" value="{{ $item['id'] }}">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Research Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="research_title" class="form-control" value="{{ $item['title'] }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Year Completed/Published</label>
                                                    <input type="text" name="research_completed_year" class="form-control" value="{{ $item['completed_year'] ?? '' }}" placeholder="e.g., 2024">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="research_description" class="form-control" rows="3">{{ $item['description'] ?? '' }}</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Featured Image</label>
                                                    @if(!empty($item['image']))
                                                        <div class="mb-2">
                                                            <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($item['image']) }}" alt="Research Image" class="img-fluid rounded border" style="max-height: 100px; object-fit: contain;">
                                                        </div>
                                                    @endif
                                                    <input type="file" name="research_image" class="form-control" accept="image/*">
                                                    <small class="text-muted">Leave empty to keep current image.</small>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-admin-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
@elseif ($currentSection === 'extension')
                        @php
                            $extension = $sectionContent['items'] ?? [];
                            $isVisible = $sectionContent['is_visible'] ?? false;
                        @endphp
                        
                        <div class="mb-3">
                            @if($isVisible)
                                <span class="badge bg-success">Visible on Public Page</span>
                            @else
                                <span class="badge bg-secondary">Hidden from Public Page</span>
                            @endif
                        </div>

                        @if(!empty($extension))
                            <div class="row g-4">
                                @foreach($extension as $item)
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="card h-100 shadow-sm border-0 bg-light">
                                            @if(!empty($item['image']))
                                                <div class="ratio ratio-16x9">
                                                    <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($item['image']) }}" class="card-img-top object-fit-cover" alt="{{ $item['title'] }}">
                                                </div>
                                            @else
                                                <div class="ratio ratio-16x9 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center">
                                                    @if($department->logo)
                                                        <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($department->logo) }}" class="img-fluid p-4" alt="{{ $department->name }} logo" style="max-height: 100%; object-fit: contain;">
                                                    @else
                                                        <span class="text-muted small">No Image</span>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="card-body">
                                                <h6 class="fw-bold mb-2">{{ $item['title'] }}</h6>
                                                @if(!empty($item['description']))
                                                    <p class="card-text small text-muted">{{ $item['description'] }}</p>
                                                @endif
                                            </div>
                                            <div class="card-footer bg-white border-0 d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.colleges.edit-department-extension', ['college' => $collegeSlug, 'department' => $department, 'extension' => \Illuminate\Support\Str::slug($item['title'] ?? '')]) }}" class="btn btn-sm btn-outline-secondary" title="Edit extension">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.colleges.update-department', ['college' => $collegeSlug, 'department' => $department]) }}" method="POST" onsubmit="return confirm('Remove this extension activity?')" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="save_dept_section" value="1">
                                                    <input type="hidden" name="section" value="extension">
                                                    <input type="hidden" name="_extension_edit" value="1">
                                                    <input type="hidden" name="delete_extension" value="{{ $item['id'] }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove extension">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted fst-italic">No extension activities listed. Click "Edit section details" to add content.</div>
                        @endif

                    @elseif ($currentSection === 'facilities')
                        @php
                            $isVisible = $sectionContent['is_visible'] ?? false;
                        @endphp
                        
                        <div class="mb-3">
                            @if($isVisible)
                                <span class="badge bg-success">Visible on Public Page</span>
                            @else
                                <span class="badge bg-secondary">Hidden from Public Page</span>
                            @endif
                        </div>

                        {!! $sectionContent['body'] ?? '<p class="text-muted">No section description yet. Click "Edit section details" to add content.</p>' !!}
                    @elseif ($currentSection === 'training')
                        @php
                            $training = $sectionContent['items'] ?? [];
                            $isVisible = $sectionContent['is_visible'] ?? false;
                        @endphp

                        <div class="mb-3">
                            @if($isVisible)
                                <span class="badge bg-success">Visible on Public Page</span>
                            @else
                                <span class="badge bg-secondary">Hidden from Public Page</span>
                            @endif
                        </div>

                        @if(!empty($training))
                            <div class="row g-4">
                                @foreach($training as $item)
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="card h-100 shadow-sm border-0 bg-light">
                                            @if(!empty($item['image']))
                                                <div class="ratio ratio-16x9">
                                                    <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($item['image']) }}" class="card-img-top object-fit-cover" alt="{{ $item['title'] }}">
                                                </div>
                                            @else
                                                <div class="ratio ratio-16x9 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center">
                                                    <span class="text-muted small">No Image</span>
                                                </div>
                                            @endif
                                            <div class="card-body">
                                                <h6 class="fw-bold mb-2">{{ $item['title'] }}</h6>
                                                @if(!empty($item['description']))
                                                    <p class="card-text small text-muted">{{ $item['description'] }}</p>
                                                @endif
                                            </div>
                                            <div class="card-footer bg-white border-0 d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.colleges.edit-department-training', ['college' => $collegeSlug, 'department' => $department, 'training' => \Illuminate\Support\Str::slug($item['title'] ?? '')]) }}" class="btn btn-sm btn-outline-secondary" title="Edit training">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.colleges.update-department', ['college' => $collegeSlug, 'department' => $department]) }}" method="POST" onsubmit="return confirm('Remove this training item?')" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="save_dept_section" value="1">
                                                    <input type="hidden" name="section" value="training">
                                                    <input type="hidden" name="_training_edit" value="1">
                                                    <input type="hidden" name="delete_training" value="{{ $item['id'] }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove training">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted fst-italic">No training items listed. Click "Edit section details" to add content.</div>
                        @endif
                    @elseif ($currentSection === 'alumni')
                        @php
                            $alumni = $sectionContent['items'] ?? [];
                            $isVisible = $sectionContent['is_visible'] ?? false;
                        @endphp
                        
                        <div class="mb-3">
                            @if($isVisible)
                                <span class="badge bg-success">Visible on Public Page</span>
                            @else
                                <span class="badge bg-secondary">Hidden from Public Page</span>
                            @endif
                        </div>

                        @if (!empty($sectionContent['body']))
                            <div class="text-muted mb-4">{!! $sectionContent['body'] !!}</div>
                        @endif



                    @elseif ($currentSection === 'membership')
                        <div class="mb-4">
                            <a href="{{ route('admin.colleges.create-department-membership', ['college' => $collegeSlug, 'department' => $department]) }}" class="btn btn-admin-primary btn-sm" title="Add Membership"><i class="bi bi-person-check"></i> <span class="d-none d-md-inline">Add Membership</span></a>
                        </div>
                        
                        @if ($membershipList->isEmpty())
                            <div class="text-center py-5 bg-light rounded-4">
                                <i class="bi bi-award text-muted mb-2" style="font-size: 2rem;"></i>
                                <p class="text-muted mb-0">No professional memberships or affiliations found for this department.</p>
                            </div>
                        @else
                            <div class="row g-3">
                                @foreach ($membershipList as $membership)
                                    <div class="col-12">
                                        <div class="card shadow-sm border-0 bg-light rounded-3 overflow-hidden">
                                            <div class="row g-0 align-items-center">
                                                <div class="col-auto p-3">
                                                    <div style="width: 60px; height: 60px;">
                                                        @if ($membership->logo)
                                                            <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($membership->logo) }}" 
                                                                 alt="{{ $membership->organization }}" 
                                                                 class="w-100 h-100 object-fit-contain rounded">
                                                        @else
                                                            <div class="w-100 h-100 bg-white border d-flex align-items-center justify-content-center rounded">
                                                                <i class="bi bi-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col px-2">
                                                    <h6 class="fw-bold mb-0 text-dark">{{ $membership->organization }}</h6>
                                                    <p class="text-muted small mb-0">{{ $membership->membership_type }}</p>
                                                    @if($membership->valid_until)
                                                        <span class="text-muted" style="font-size: 0.75rem;">Valid until: {{ $membership->valid_until->format('M d, Y') }}</span>
                                                    @endif
                                                </div>
                                                <div class="col-auto p-3 text-end">
                                                    <div class="d-flex gap-2">
                                                        @if(!$membership->is_visible)
                                                            <span class="badge bg-secondary rounded-pill me-2">Hidden</span>
                                                        @endif
                                                        <a href="{{ route('admin.colleges.edit-department-membership', ['college' => $collegeSlug, 'department' => $department, 'membership' => $membership]) }}" class="btn btn-sm btn-outline-primary rounded-circle" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('admin.memberships.destroy', $membership) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this membership record?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="return_college" value="{{ $collegeSlug }}">
                                                            <input type="hidden" name="return_department" value="{{ $department->getRouteKey() }}">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @elseif ($currentSection === 'organizations')
                        @php
                            $isVisible = $sectionContent['is_visible'] ?? true;
                        @endphp

                        <div class="organization-intro-card p-4 mb-4">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                                <h2 class="colleges-detail-title mb-0">{{ $sectionContent['title'] ?? 'Student Organizations' }}</h2>
                                <a href="{{ route('admin.colleges.edit-department-section', ['college' => $collegeSlug, 'department' => $department, 'section' => 'organizations']) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil-square me-1"></i> Edit section details
                                </a>
                            </div>

                            <div class="mb-3">
                                @if($isVisible)
                                    <span class="badge bg-success">Visible on Public Page</span>
                                @else
                                    <span class="badge bg-secondary">Hidden from Public Page</span>
                                @endif
                            </div>

                            @if(!empty($sectionContent['body']))
                                <div class="organization-intro-copy fst-italic">
                                    {!! $sectionContent['body'] !!}
                                </div>
                            @else
                                <p class="organization-intro-copy fst-italic text-muted mb-0">Student organizations provide a dynamic platform for students to cultivate leadership, collaborate on real-world projects, and bridge the gap between academic theory and professional practice.</p>
                            @endif
                        </div>

                        <div class="organization-table-card p-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                                <div class="d-flex flex-column gap-2">
                                    <h2 class="colleges-detail-title mb-0">{{ $sectionContent['title'] ?? 'Student Organizations' }}</h2>
                                    @if($isVisible)
                                        <span class="badge bg-success align-self-start">Visible on Public Page</span>
                                    @else
                                        <span class="badge bg-secondary align-self-start">Hidden from Public Page</span>
                                    @endif
                                </div>
                                <a href="{{ route('admin.organizations.create-department', ['college' => $collegeSlug, 'department' => $department]) }}" class="btn btn-admin-primary">
                                    <i class="bi bi-plus-circle me-1"></i> Add Organization Record
                                </a>
                            </div>

                            @if ($organizationList->isEmpty())
                                <div class="text-center py-5 bg-light rounded-4">
                                    <i class="bi bi-people text-muted mb-2" style="font-size: 2rem;"></i>
                                    <p class="text-muted mb-0">No student organizations found for this department.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table organization-table align-middle">
                                        <thead>
                                            <tr>
                                                <th>Organization Info</th>
                                                <th>Scope</th>
                                                <th>Adviser</th>
                                                <th>Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($organizationList as $org)
                                                <tr>
                                                    <td>
                                                        <div class="organization-info">
                                                            <div class="organization-logo">
                                                                @if ($org->logo)
                                                                    <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($org->logo) }}"
                                                                         alt="{{ $org->name }}"
                                                                         class="w-100 h-100 object-fit-cover">
                                                                @else
                                                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                                                        @if ($department->logo)
                                                                            <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($department->logo) }}"
                                                                                 alt="{{ $department->name }} logo"
                                                                                 class="w-100 h-100 object-fit-contain p-2">
                                                                        @else
                                                                            <i class="bi bi-image text-muted"></i>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div>
                                                                <div class="organization-name">
                                                                    {{ $org->name }}
                                                                    @if($org->acronym)
                                                                        <span class="text-muted fw-normal">({{ $org->acronym }})</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><span class="organization-scope">{{ $org->department?->name ?? $department->name }}</span></td>
                                                    <td>{{ $org->adviser ?: 'N/A' }}</td>
                                                    <td>
                                                        <span class="organization-status-badge {{ $org->is_visible ? 'is-visible' : 'is-hidden' }}">
                                                            {{ $org->is_visible ? 'Visible' : 'Hidden' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="organization-actions">
                                                            <a href="{{ route('admin.organizations.show', ['college' => $collegeSlug, 'organization' => $org]) }}" class="btn btn-sm btn-outline-success">Open</a>
                                                            <a href="{{ route('admin.organizations.edit-scoped', ['college' => $collegeSlug, 'organization' => $org]) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                                            <form action="{{ route('admin.organizations.destroy', $org) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this organization?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <input type="hidden" name="return_college" value="{{ $collegeSlug }}">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @else
                        {!! $sectionContent['body'] ?: '<p class="text-muted">No content yet. Click "Edit section details" to add content.</p>' !!}
                    @endif
                </div>
            </div>

            @if ($currentSection === 'alumni')
                @php
                    $alumni = $sectionContent['items'] ?? [];
                @endphp
                <div class="colleges-detail mt-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 class="colleges-detail-title mb-0">Alumni Roster</h2>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.colleges.create-department-alumnus', ['college' => $collegeSlug, 'department' => $department]) }}" class="btn btn-primary btn-sm" title="Add Alumnus"><i class="bi bi-mortarboard"></i> <span class="d-none d-md-inline">
                                <i class="bi bi-plus-circle me-1"></i> Add Alumnus
                            </a>
                        </div>
                    </div>
                    <div class="colleges-detail-body p-0">
                        @if(!empty($alumni))
                            <div class="p-3 border rounded-top border-bottom-0 bg-light">
                                <div class="row g-2 align-items-center">
                                    <div class="col-md-8">
                                        <input
                                            type="search"
                                            id="alumniRosterSearch"
                                            class="form-control"
                                            placeholder="Search alumni by name, batch, or description"
                                        >
                                    </div>
                                    <div class="col-md-4">
                                        <select id="alumniRosterSort" class="form-select">
                                            <option value="name_asc">Name: A to Z</option>
                                            <option value="name_desc">Name: Z to A</option>
                                            <option value="year_asc">Batch Year: Asc</option>
                                            <option value="year_desc">Batch Year: Desc</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <ul class="list-group list-group-flush rounded border">
                                @foreach($alumni as $item)
                                    <li
                                        class="list-group-item p-4 position-relative alumni-roster-item"
                                        data-name="{{ strtolower($item['title'] ?? '') }}"
                                        data-year="{{ strtolower($item['year_graduated'] ?? '') }}"
                                        data-description="{{ strtolower(trim(strip_tags($item['description'] ?? ''))) }}"
                                    >
                                        <div class="position-absolute top-0 end-0 m-3 d-flex gap-2">
                                            <a href="{{ route('admin.colleges.edit-department-alumnus', ['college' => $collegeSlug, 'department' => $department, 'alumnus' => \Illuminate\Support\Str::slug($item['title'] ?? '')]) }}" class="btn btn-sm btn-outline-primary bg-white shadow-sm" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('admin.colleges.destroy-alumnus', ['college' => $collegeSlug, 'department' => $department, 'alumnus' => $item['id']]) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this alumni profile?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger bg-white shadow-sm" title="Remove">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="d-flex flex-column flex-sm-row gap-4 align-items-center align-items-sm-start">
                                            @if(!empty($item['image']))
                                                <div style="width: 100px; height: 100px; flex-shrink: 0;">
                                                    <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($item['image']) }}" class="rounded-circle w-100 h-100 object-fit-cover shadow-sm" alt="{{ $item['title'] }}">
                                                </div>
                                            @else
                                                <div style="width: 100px; height: 100px; flex-shrink: 0;" class="bg-light border rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                                                    <i class="fas fa-user text-muted fs-3"></i>
                                                </div>
                                            @endif
                                            <div class="text-center text-sm-start flex-grow-1">
                                                        <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-baseline gap-2 mb-2">
                                                            <h5 class="fw-bold mb-0 text-dark">{{ $item['title'] }}</h5>
                                                            @if(!empty($item['year_graduated']))
                                                                <span class="badge bg-secondary">{{ $item['year_graduated'] }}</span>
                                                            @endif
                                                        </div>
                                                        @if(!empty($item['description']))
                                                            <div class="text-muted fst-italic testimonial-richtext" style="line-height: 1.6;">
                                                                {!! $item['description'] !!}
                                                            </div>
                                                        @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <div id="alumniRosterEmptyState" class="text-muted fst-italic p-4 border rounded-bottom d-none">No alumni match your search.</div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const searchInput = document.getElementById('alumniRosterSearch');
                                    const sortSelect = document.getElementById('alumniRosterSort');
                                    const list = document.querySelector('.colleges-detail-body .list-group');
                                    const emptyState = document.getElementById('alumniRosterEmptyState');
                                    if (!searchInput || !sortSelect || !list) return;

                                    const getYearValue = (value) => {
                                        const match = String(value || '').match(/\d{4}/);
                                        return match ? parseInt(match[0], 10) : Number.POSITIVE_INFINITY;
                                    };

                                    const sortItems = () => {
                                        const items = Array.from(list.querySelectorAll('.alumni-roster-item'));
                                        const sortMode = sortSelect.value;
                                        items.sort((a, b) => {
                                            const aName = a.dataset.name || '';
                                            const bName = b.dataset.name || '';
                                            const aYear = getYearValue(a.dataset.year);
                                            const bYear = getYearValue(b.dataset.year);

                                            switch (sortMode) {
                                                case 'name_desc':
                                                    return bName.localeCompare(aName);
                                                case 'year_asc':
                                                    return aYear - bYear || aName.localeCompare(bName);
                                                case 'year_desc':
                                                    return bYear - aYear || aName.localeCompare(bName);
                                                case 'name_asc':
                                                default:
                                                    return aName.localeCompare(bName);
                                            }
                                        });

                                        items.forEach((item) => list.appendChild(item));
                                    };

                                    const filterItems = () => {
                                        const query = searchInput.value.trim().toLowerCase();
                                        let visibleCount = 0;
                                        list.querySelectorAll('.alumni-roster-item').forEach((item) => {
                                            const haystack = [
                                                item.dataset.name || '',
                                                item.dataset.year || '',
                                                item.dataset.description || '',
                                            ].join(' ');
                                            const matches = !query || haystack.includes(query);
                                            item.classList.toggle('d-none', !matches);
                                            if (matches) visibleCount++;
                                        });

                                        if (emptyState) {
                                            emptyState.classList.toggle('d-none', visibleCount > 0);
                                        }
                                    };

                                    const refreshRoster = () => {
                                        sortItems();
                                        filterItems();
                                    };

                                    searchInput.addEventListener('input', filterItems);
                                    sortSelect.addEventListener('change', refreshRoster);
                                    refreshRoster();
                                });
                            </script>
                        @else
                            <div class="text-muted fst-italic p-4 border rounded">No alumni listed in the roster.</div>
                        @endif
                    </div>
                </div>
            @endif

            @if ($currentSection === 'linkages')
                <div class="colleges-detail mt-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 class="colleges-detail-title mb-0">Partnership Roster</h2>
                        <a href="{{ route('admin.linkages.create', ['college' => $collegeSlug, 'department' => $department]) }}" class="btn btn-admin-primary btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Partner
                        </a>
                    </div>
                    <div class="colleges-detail-body">
                        @php
                            $linkages = $sectionContent['items'] ?? [];
                        @endphp

                        @if(!empty($linkages))
                            @php
                                $grouped = collect($linkages)->groupBy('type');
                            @endphp
                            @foreach($grouped as $type => $items)
                                <h6 class="fw-bold text-muted text-uppercase mt-3 mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">{{ ucfirst($type) }} Linkages</h6>
                                <ul class="list-unstyled mb-3">
                                    @foreach($items as $linkage)
                                        <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <span class="me-2 text-muted">&bull;</span>
                                                @if(!empty($linkage['url']))
                                                    <a href="{{ $linkage['url'] }}" target="_blank" class="fw-semibold text-decoration-none">{{ $linkage['name'] }}</a>
                                                @else
                                                    <span class="fw-semibold">{{ $linkage['name'] }}</span>
                                                @endif
                                            </div>
                                            <div class="d-flex gap-1 flex-shrink-0 ms-3">
                                                <a href="{{ route('admin.linkages.edit', ['college' => $collegeSlug, 'department' => $department, 'linkage' => \Illuminate\Support\Str::slug($linkage['name'] ?? '')]) }}" class="btn btn-sm btn-outline-primary" title="Edit Partner">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                </a>
                                                <form action="{{ route('admin.linkages.destroy', ['college' => $collegeSlug, 'department' => $department, 'linkage' => $linkage['id']]) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this partner?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove Partner">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endforeach
                        @else
                            <div class="text-muted fst-italic">No partners listed in the roster.</div>
                        @endif
                    </div>
                </div>
            @endif

            @if ($currentSection === 'facilities')
                <div class="colleges-detail mt-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 class="colleges-detail-title mb-0">Facility Roster</h2>
                        <a href="{{ route('admin.colleges.create-department-facility', ['college' => $collegeSlug, 'department' => $department]) }}" class="btn btn-admin-primary btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Facility
                        </a>
                    </div>
                    <div class="colleges-detail-body">
                        @php
                            $facilities = $sectionContent['items'] ?? [];
                            $facilitySlugCounts = collect($facilities)->countBy(function ($item) {
                                return \Illuminate\Support\Str::slug($item['title'] ?? '');
                            });
                        @endphp

                        @if(!empty($facilities))
                            <ul class="list-unstyled mb-0">
                                @foreach($facilities as $item)
                                    @php
                                        $facilitySlug = \Illuminate\Support\Str::slug($item['title'] ?? '');
                                        $facilityRouteKey = (($facilitySlugCounts[$facilitySlug] ?? 0) > 1 && !empty($item['id']))
                                            ? $facilitySlug . '-' . $item['id']
                                            : ($facilitySlug !== '' ? $facilitySlug : ($item['id'] ?? ''));
                                    @endphp
                                    <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <span class="me-2 text-muted">&bull;</span>
                                            <div>
                                                <span class="fw-semibold">{{ $item['title'] }}</span>
                                                @if(!empty($item['description']))
                                                    <span class="text-muted small ms-2">— {{ Str::limit(strip_tags($item['description']), 60) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1 flex-shrink-0 ms-3">
                                            <a href="{{ route('admin.colleges.edit-department-facility', ['college' => $collegeSlug, 'department' => $department, 'facility' => $facilityRouteKey]) }}" class="btn btn-sm btn-outline-primary" title="Edit Facility">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            </a>
                                            <form action="{{ route('admin.colleges.destroy-facility-item', ['college' => $collegeSlug, 'department' => $department, 'facility' => $item['id']]) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this facility?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove Facility">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-muted fst-italic">No facilities listed in the roster.</div>
                        @endif
                    </div>
                </div>
            @endif

            @if ($currentSection === 'overview')
                {{-- Card Image Section --}}
                <div class="colleges-detail mt-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <h2 class="colleges-detail-title mb-0">Card Image</h2>
                        <a href="{{ route('admin.colleges.edit-department-card-image', ['college' => $collegeSlug, 'department' => $department]) }}" class="btn btn-outline-secondary btn-sm" title="Edit card image"><i class="bi bi-image"></i> <span class="d-none d-md-inline">Edit card image</span></a>
                    </div>
                    <div class="colleges-detail-body">
                        @if (!empty($sectionContent['card_image']))
                            <div class="rounded overflow-hidden">
                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($sectionContent['card_image']) }}" alt="Department Card" class="img-fluid w-100" style="max-height: 400px; object-fit: cover;">
                            </div>
                        @else
                            <div class="position-relative rounded overflow-hidden" style="min-height: 300px; background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);">
                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.1);">
                                    <div class="text-center text-dark">
                                        <h3 class="fw-bold mb-2">Card Image Placeholder</h3>
                                        <p class="mb-0 opacity-75">Click "Edit card image" to upload a card image</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="colleges-detail mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="colleges-detail-title mb-0">Retro Section Items</h2>
                        @php
                            $retroCount = $retroList->count();
                        @endphp
                        @if ($retroCount < 4)
                            <a href="{{ route('admin.colleges.create-department-retro', ['college' => $collegeSlug, 'department' => $department]) }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add Item</span></a>
                        @else
                            <button class="btn btn-secondary btn-sm" disabled title="Maximum 4 items reached.">Max items reached</button>
                        @endif
                    </div>

                    @if($retroList->count() > 0)
                        <div class="row g-3">
                            @foreach($retroList as $retro)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 shadow-sm border-admin-light">
                                        <div class="card-body">
                                            @if($retro->background_image)
                                                <div class="rounded mb-2 overflow-hidden" style="height: 150px;">
                                                    <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($retro->background_image) }}" class="w-100 h-100 object-fit-cover" alt="Retro bg">
                                                </div>
                                            @endif
                                            <h5 class="card-title fw-bold text-dark">{{ $retro->title ?: 'No Title' }}</h5>
                                            <div class="mb-2">
                                                @if($retro->stamp)
                                                    <span class="badge bg-secondary text-white">{{ $retro->stamp }}</span>
                                                @endif
                                            </div>
                                            <p class="card-text small text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($retro->description), 100) }}</p>
                                        </div>
                                        <div class="card-footer bg-transparent border-top-0 d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.colleges.edit-department-retro', ['college' => $collegeSlug, 'department' => $department, 'retro' => $retro->id]) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                                            <form action="{{ route('admin.colleges.update-department', ['college' => $collegeSlug, 'department' => $department]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this retro item?');">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="save_dept_section" value="1">
                                                <input type="hidden" name="section" value="overview">
                                                <input type="hidden" name="_edit_mode" value="delete_retro">
                                                <input type="hidden" name="retro_id" value="{{ $retro->id }}">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Remove</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="colleges-detail-body">
                            <p class="text-muted mb-0">No retro items found.</p>
                            <p class="text-muted small mt-2 mb-0">The current overview layout stays as-is until you add the first retro section item.</p>
                            @if ($retroCount < 4)
                                <p class="mt-2 mb-0">
                                    <a href="{{ route('admin.colleges.create-department-retro', ['college' => $collegeSlug, 'department' => $department]) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Create your first item</span></a>
                                </p>
                            @endif
                        </div>
                    @endif
                </div>


                {{-- Graduate Outcomes Section --}}
                <div class="colleges-detail mt-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <h2 class="colleges-detail-title mb-0">Graduate Outcomes</h2>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.colleges.create-department-graduate-outcome', ['college' => $collegeSlug, 'department' => $department]) }}" class="btn btn-admin-primary btn-sm" title="Add"><i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add</span></a>
                        </div>
                    </div>
                    <div class="colleges-detail-body">
                        {{-- Title --}}
                        @if($department->graduate_outcomes_title)
                             <h5 class="fw-bold mb-3">{{ $department->graduate_outcomes_title }}</h5>
                        @endif

                        {{-- Dynamic Outcomes --}}
                        @if(isset($department->outcomes) && $department->outcomes->count() > 0)
                            <div class="row g-4">
                                @foreach($department->outcomes as $outcome)
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="card h-100 shadow-sm border-0 bg-light">
                                            @if($outcome->image)
                                                <div class="ratio ratio-16x9">
                                                    <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($outcome->image) }}" class="card-img-top object-fit-cover" alt="{{ $outcome->title }}">
                                                </div>
                                            @endif
                                            <div class="card-body d-flex flex-column">
                                                <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
                                                    @if($outcome->title)
                                                        <h6 class="fw-bold mb-0 flex-grow-1" style="min-width: 0; overflow-wrap: anywhere;">{{ $outcome->title }}</h6>
                                                    @endif
                                                    <div class="d-flex gap-2 flex-shrink-0">
                                                        <a href="{{ route('admin.colleges.edit-department-graduate-outcome', ['college' => $collegeSlug, 'department' => $department, 'outcome' => $outcome->id]) }}" class="btn btn-sm btn-outline-secondary" title="Edit this outcome">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                                                        </a>
                                                        <form action="{{ route('admin.colleges.update-department', ['college' => $collegeSlug, 'department' => $department]) }}" method="POST" onsubmit="return confirm('Delete this outcome?');">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="save_dept_section" value="1">
                                                            <input type="hidden" name="section" value="overview">
                                                            <input type="hidden" name="_graduate_outcomes_edit" value="1">
                                                            <input type="hidden" name="delete_outcome" value="{{ $outcome->id }}">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete this outcome">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                @if($outcome->description)
                                                    <p class="card-text small text-muted">{!! $outcome->description !!}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        {{-- Check Legacy Outcome --}}
                        @elseif ($department->graduate_outcomes)
                            <div class="prose max-w-none">
                                {!! nl2br(e($department->graduate_outcomes)) !!}
                            </div>
                            @if ($department->graduate_outcomes_image)
                                <div class="mt-3">
                                    <h6 class="fw-bold mb-2">Graduate Outcomes Image</h6>
                                    <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($department->graduate_outcomes_image) }}" class="img-fluid rounded" alt="Graduate Outcomes Image" style="max-height: 200px;">
                                </div>
                            @endif
                        @else
                            <p class="text-muted fst-italic">No graduate outcomes added yet.</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Edit section form (collapse) --}}
        {{-- Removed: Edit form is now on a separate page --}}
    </div>

    {{-- Edit Department Info Modal --}}
    <div class="modal fade" id="editDepartmentInfoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Department Info</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.colleges.update-department', ['college' => $collegeSlug, 'department' => $department]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Department Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $department->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="details" class="form-control quill-editor" rows="3">{{ old('details', $department->details ?? '') }}</textarea>
                            <small class="text-muted">Optional short description of the department.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Logo</label>
                            @if (!empty($department->logo))
                                <div class="mb-2">
                                    <img src="{{ $department->logo }}" alt="logo" style="max-width: 100px; max-height: 100px; object-fit: contain;" class="rounded">
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

    @push('styles')
    <style>
        .testimonial-richtext p:last-child {
            margin-bottom: 0 !important;
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

    @media (max-width: 575.98px) {
        .colleges-layout {
            gap: 0 !important;
        }
        .colleges-section-list {
            margin-bottom: 0.65rem !important;
            border-radius: 1.125rem;
        }
        .colleges-section-list-header {
            padding: 0.7rem 1rem;
            font-size: 0.75rem;
        }
        .colleges-section-item {
            gap: 0.45rem;
            padding: 0.68rem 1rem;
            font-size: 0.92rem;
        }
        .colleges-section-label {
            line-height: 1.2;
        }
        .colleges-detail {
            padding: 1.15rem 1rem;
            border-radius: 1.125rem;
        }
        .department-heading {
            align-items: center;
        }
        .organization-title {
            font-size: 1.25rem;
            line-height: 1.2;
        }
        .organization-table thead {
            display: none;
        }
        .organization-table,
        .organization-table tbody,
        .organization-table tr,
        .organization-table td {
            display: block;
            width: 100%;
        }
        .organization-table tr {
            border: 1px solid var(--admin-border);
            border-radius: 1rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #f8fafc;
        }
        .organization-table td {
            border: 0 !important;
            padding: 0.35rem 0;
            text-align: left !important;
        }
        .organization-actions {
            justify-content: flex-start;
            margin-top: 0.5rem;
        }
        .section-toolbar {
            width: 100%;
        }
        .organization-toolbar {
            justify-content: flex-end;
        }
        .section-toolbar > .btn {
            width: fit-content;
        }
    }

</style>
    @endpush
@endsection
