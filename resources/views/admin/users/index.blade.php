@extends('admin.layout')

@section('title', 'User Management')

@push('styles')
<style>
    .limit-toggle-group {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }
    .limit-toggle {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        transition: background 0.15s ease, color 0.15s ease;
        user-select: none;
        color: #dc3545;
        font-weight: 500;
    }
    .limit-toggle input[type="checkbox"] {
        appearance: none;
        -webkit-appearance: none;
        width: 15px;
        height: 15px;
        border: 2px solid #dc3545;
        border-radius: 4px;
        flex-shrink: 0;
        position: relative;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease;
    }
    .limit-toggle input[type="checkbox"]:checked {
        background: #dc3545;
        border-color: #dc3545;
    }
    .limit-toggle input[type="checkbox"]:checked::after {
        content: '';
        position: absolute;
        left: 2px;
        top: -1px;
        width: 7px;
        height: 10px;
        border: 2px solid #fff;
        border-top: none;
        border-left: none;
        transform: rotate(45deg);
    }
    .limit-toggle.lifted {
        color: #198754;
        text-decoration: line-through;
        text-decoration-color: #198754;
        opacity: 0.65;
    }
    .limit-toggle.lifted input[type="checkbox"] {
        border-color: #198754;
        background: #198754;
    }
    .limit-toggle.lifted input[type="checkbox"]::after {
        content: '';
        position: absolute;
        left: 2px;
        top: -1px;
        width: 7px;
        height: 10px;
        border: 2px solid #fff;
        border-top: none;
        border-left: none;
        transform: rotate(45deg);
    }
    .limit-toggle:hover {
        background: rgba(0,0,0,0.04);
    }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <h1 class="admin-page-title mb-0">User Management</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-admin-primary">Add User</a>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-pills mb-4 gap-2" id="userTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="btn btn-sm btn-outline-secondary active rounded-pill px-4" id="users-tab" data-bs-toggle="tab" data-bs-target="#users-pane" type="button" role="tab" aria-controls="users-pane" aria-selected="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Users
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="btn btn-sm btn-outline-secondary rounded-pill px-4" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles-pane" type="button" role="tab" aria-controls="roles-pane" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Roles & Escalation
            </button>
        </li>
    </ul>

    <div class="tab-content" id="userTabsContent">
        <!-- Users Tab -->
        <div class="tab-pane fade show active" id="users-pane" role="tabpanel" aria-labelledby="users-tab">
            <div class="admin-card">
                <div class="card-body p-4">
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="role" class="form-select">
                                <option value="">All roles</option>
                                <option value="superadmin" {{ request('role') === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="editor" {{ request('role') === 'editor' ? 'selected' : '' }}>Editor</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-secondary w-100">Filter</button>
                        </div>
                    </form>

                    @if ($users->isEmpty())
                        <div class="py-5 text-center">
                            <p class="text-muted mb-2">No users found.</p>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-admin-primary btn-sm">Add user</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 admin-table">
                                <thead>
                                    <tr class="text-muted small text-uppercase" style="letter-spacing: 0.05em;">
                                        <th class="fw-600 border-0 py-3 px-4">Name</th>
                                        <th class="fw-600 border-0 py-3 px-4">Email</th>
                                        <th class="fw-600 border-0 py-3 px-4" style="width: 120px;">Role</th>
                                        <th class="fw-600 border-0 py-3 px-4" style="width: 180px;">College</th>
                                        <th class="fw-600 border-0 py-3 px-4" style="width: 160px;">Department</th>
                                        <th class="fw-600 border-0 py-3 px-4 text-end" style="width: 140px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td class="py-3 px-4 fw-500">{{ $user->name }}</td>
                                            <td class="py-3 px-4 text-muted">{{ $user->email }}</td>
                                            <td class="py-3 px-4">
                                                @php
                                                    $role = $user->role ?? 'editor';
                                                    $badgeBg = $role === 'superadmin' ? 'var(--admin-accent)' : ($role === 'admin' ? 'var(--admin-accent)' : 'var(--admin-accent-soft)');
                                                    $badgeColor = in_array($role, ['superadmin', 'admin'], true) ? '#fff' : 'var(--admin-accent)';
                                                @endphp
                                                <span class="badge rounded-pill px-2 py-1" style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; font-weight: 500;">
                                                    {{ ucfirst($role) }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-muted small">
                                                @if ($user->role === \App\Models\User::ROLE_SUPERADMIN || empty($user->college_slug))
                                                    All colleges
                                                @else
                                                    {{ \App\Http\Controllers\Admin\CollegeController::getColleges()[$user->college_slug] ?? $user->college_slug }}
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-muted small">
                                                @if ($user->organization)
                                                    <span class="fw-500 text-dark">{{ $user->organization->name }}</span>
                                                    @if ($user->organization->department)
                                                        <div class="extra-small text-muted" style="font-size:0.75rem;">Dept: {{ $user->organization->department->name }}</div>
                                                    @endif
                                                @else
                                                    {{ $user->department ?: '—' }}
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-end">
                                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Edit</a>
                                                @if ($user->id !== auth()->id())
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this user?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">Delete</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($users->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $users->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Roles Tab -->
        <div class="tab-pane fade" id="roles-pane" role="tabpanel" aria-labelledby="roles-tab">
            <div class="admin-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                        <h2 class="h5 fw-600 mb-0">Roles & Hierarchy</h2>
                        @if(auth()->user()->isSuperAdmin())
                            <span class="badge rounded-pill text-bg-secondary" style="font-size:0.7rem;">⚙ Superadmin — click limitations to toggle</span>
                        @else
                            <span class="badge rounded-pill text-bg-warning" style="font-size:0.7rem;">🔒 View only — Superadmin can manage limitations</span>
                        @endif
                    </div>
                    <p class="text-muted small mb-4">System roles, their scope of access, and escalation paths.</p>

                    @php
                    $arrow = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>';
                    $isSuperAdmin = auth()->user()->isSuperAdmin();
                    @endphp
                    
                    {{-- Superadmin Row --}}
                    <div class="admin-card mb-3" style="border-left: 4px solid var(--admin-accent);">
                        <div class="card-body p-3">
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <div style="min-width: 130px;">
                                    <span class="badge rounded-pill bg-success px-3 py-1 fw-600">Superadmin</span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-600 small mb-1">Full System Access</div>
                                    <div class="text-muted small">Manage global settings, all colleges, all users, and system configuration. No scope restrictions.</div>
                                </div>
                                <div class="text-center" style="min-width: 140px;">
                                    <span class="text-success fw-600 small">⬤ System Owner</span><br>
                                    <span class="text-muted" style="font-size:0.7rem;">No escalation</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Admin sub-types --}}
                    <div class="mb-2 text-muted text-uppercase fw-600 small px-1 mt-3">Admin Types</div>
                    <div class="admin-card mb-3">
                        <div class="card-body p-0">
                            <table class="table admin-table align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small text-uppercase" style="letter-spacing: 0.04em;">
                                        <th class="fw-600 border-0 py-2 px-4" style="width:180px;">Admin Type</th>
                                        <th class="fw-600 border-0 py-2 px-4">Scope</th>
                                        <th class="fw-600 border-0 py-2 px-4">Limitations</th>
                                        <th class="fw-600 border-0 py-2 px-4" style="width:190px;">Escalation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     {{-- College Admin --}}
                                    <tr>
                                        <td class="py-3 px-4">
                                            <span class="badge rounded-pill bg-success px-2 py-1 me-1">Admin</span>
                                            <span class="badge rounded-pill px-2 py-1" style="background:#e8f5e9; color: #198754; font-size:0.7rem;">College</span>
                                        </td>
                                        <td class="py-3 px-4 text-muted small">
                                            Assigned to a <strong>single college</strong>. Can access all departments within that college.
                                        </td>
                                        <td class="py-3 px-4 small">
                                            <div class="limit-toggle-group" data-role="college-admin">
                                                @php $ca = $rolePermissions['college-admin']; @endphp
                                                <label class="limit-toggle {{ !$ca['can_access_other_colleges'] ? 'active' : 'lifted' }}">
                                                    <input type="checkbox" data-perm-key="can_access_other_colleges" {{ !$ca['can_access_other_colleges'] ? 'checked' : '' }} {{ !$isSuperAdmin ? 'disabled' : '' }}> Cannot access other colleges</label>
                                                <label class="limit-toggle {{ !$ca['can_change_global_settings'] ? 'active' : 'lifted' }}">
                                                    <input type="checkbox" data-perm-key="can_change_global_settings" {{ !$ca['can_change_global_settings'] ? 'checked' : '' }} {{ !$isSuperAdmin ? 'disabled' : '' }}> Cannot change global settings</label>
                                                <label class="limit-toggle {{ !$ca['can_create_superadmin'] ? 'active' : 'lifted' }}">
                                                    <input type="checkbox" data-perm-key="can_create_superadmin" {{ !$ca['can_create_superadmin'] ? 'checked' : '' }} {{ !$isSuperAdmin ? 'disabled' : '' }}> Cannot create Superadmin users</label>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-muted small">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span>College Admin</span>
                                                {!! $arrow !!}
                                                <span class="fw-600">Superadmin</span>
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- Department Admin --}}
                                    <tr>
                                        <td class="py-3 px-4">
                                            <span class="badge rounded-pill bg-success px-2 py-1 me-1">Admin</span>
                                            <span class="badge rounded-pill px-2 py-1" style="background:#e8f5e9; color: #198754; font-size:0.7rem;">Department</span>
                                        </td>
                                        <td class="py-3 px-4 text-muted small">
                                            Assigned to a <strong>specific department</strong> within a college.
                                        </td>
                                        <td class="py-3 px-4 small">
                                            <div class="limit-toggle-group" data-role="dept-admin">
                                                @php $da = $rolePermissions['dept-admin']; @endphp
                                                <label class="limit-toggle {{ !$da['can_access_other_departments'] ? 'active' : 'lifted' }}">
                                                    <input type="checkbox" data-perm-key="can_access_other_departments" {{ !$da['can_access_other_departments'] ? 'checked' : '' }} {{ !$isSuperAdmin ? 'disabled' : '' }}> Cannot access other departments</label>
                                                <label class="limit-toggle {{ !$da['can_manage_college_settings'] ? 'active' : 'lifted' }}">
                                                    <input type="checkbox" data-perm-key="can_manage_college_settings" {{ !$da['can_manage_college_settings'] ? 'checked' : '' }} {{ !$isSuperAdmin ? 'disabled' : '' }}> Cannot manage college-wide settings</label>
                                                <label class="limit-toggle {{ !$da['can_manage_users_outside_dept'] ? 'active' : 'lifted' }}">
                                                    <input type="checkbox" data-perm-key="can_manage_users_outside_dept" {{ !$da['can_manage_users_outside_dept'] ? 'checked' : '' }} {{ !$isSuperAdmin ? 'disabled' : '' }}> Cannot manage users outside their dept.</label>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-muted small">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span>Dept. Admin</span>
                                                {!! $arrow !!}
                                                <span>College Admin</span>
                                                {!! $arrow !!}
                                                <span class="fw-600">Superadmin</span>
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- Organization Admin --}}
                                    <tr>
                                        <td class="py-3 px-4">
                                            <span class="badge rounded-pill bg-success px-2 py-1 me-1">Admin</span>
                                            <span class="badge rounded-pill px-2 py-1" style="background:#e8f5e9; color: #198754; font-size:0.7rem;">Organization</span>
                                        </td>
                                        <td class="py-3 px-4 text-muted small">
                                            Manages a <strong>student organization</strong> tied to a college.
                                        </td>
                                        <td class="py-3 px-4 small">
                                            <div class="limit-toggle-group" data-role="org-admin">
                                                @php $oa = $rolePermissions['org-admin']; @endphp
                                                <label class="limit-toggle {{ !$oa['can_access_outside_org'] ? 'active' : 'lifted' }}">
                                                    <input type="checkbox" data-perm-key="can_access_outside_org" {{ !$oa['can_access_outside_org'] ? 'checked' : '' }} {{ !$isSuperAdmin ? 'disabled' : '' }}> Limited to org-specific content only</label>
                                                <label class="limit-toggle {{ !$oa['can_manage_users_or_departments'] ? 'active' : 'lifted' }}">
                                                    <input type="checkbox" data-perm-key="can_manage_users_or_departments" {{ !$oa['can_manage_users_or_departments'] ? 'checked' : '' }} {{ !$isSuperAdmin ? 'disabled' : '' }}> Cannot manage users or departments</label>
                                                <label class="limit-toggle {{ !$oa['can_access_college_settings'] ? 'active' : 'lifted' }}">
                                                    <input type="checkbox" data-perm-key="can_access_college_settings" {{ !$oa['can_access_college_settings'] ? 'checked' : '' }} {{ !$isSuperAdmin ? 'disabled' : '' }}> Cannot access college-level settings</label>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-muted small">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span>Org. Admin</span>
                                                {!! $arrow !!}
                                                <span>College Admin</span>
                                                {!! $arrow !!}
                                                <span class="fw-600">Superadmin</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Editor Row --}}
                    <div class="mb-2 text-muted text-uppercase fw-600 small px-1 mt-3">Content Roles</div>
                    <div class="admin-card">
                        <div class="card-body p-3">
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <div style="min-width: 130px;">
                                    <span class="badge rounded-pill px-3 py-1 fw-600" style="background: var(--admin-accent-soft); color: var(--admin-accent);">Editor</span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-600 small mb-1">Content Management</div>
                                    <div class="text-muted small mb-2">Manage articles, announcements, and department-specific content.</div>
                                    <div class="limit-toggle-group" data-role="editor">
                                        @php $ed = $rolePermissions['editor']; @endphp
                                        <label class="limit-toggle {{ !$ed['can_manage_users'] ? 'active' : 'lifted' }}">
                                            <input type="checkbox" data-perm-key="can_manage_users" {{ !$ed['can_manage_users'] ? 'checked' : '' }} {{ !$isSuperAdmin ? 'disabled' : '' }}> Cannot manage users</label>
                                        <label class="limit-toggle {{ !$ed['can_change_settings'] ? 'active' : 'lifted' }}">
                                            <input type="checkbox" data-perm-key="can_change_settings" {{ !$ed['can_change_settings'] ? 'checked' : '' }} {{ !$isSuperAdmin ? 'disabled' : '' }}> Cannot change settings or colors</label>
                                        <label class="limit-toggle {{ !$ed['unrestricted_scope'] ? 'active' : 'lifted' }}">
                                            <input type="checkbox" data-perm-key="unrestricted_scope" {{ !$ed['unrestricted_scope'] ? 'checked' : '' }} {{ !$isSuperAdmin ? 'disabled' : '' }}> Limited to their assigned college/department</label>
                                    </div>
                                </div>
                                <div style="min-width: 180px;">
                                    <div class="d-flex align-items-center gap-2 flex-wrap text-muted small">
                                        <span>Editor</span>
                                        {!! $arrow !!}
                                        <span>Admin</span>
                                        {!! $arrow !!}
                                        <span class="fw-600">Superadmin</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab button appearance
            var triggerTabList = [].slice.call(document.querySelectorAll('#userTabs button'))
            triggerTabList.forEach(function (triggerEl) {
                var tabTrigger = new bootstrap.Tab(triggerEl)
                triggerEl.addEventListener('click', function (event) {
                    event.preventDefault()
                    tabTrigger.show()
                })
                triggerEl.addEventListener('show.bs.tab', function() {
                    triggerTabList.forEach(btn => {
                        btn.classList.remove('active', 'btn-admin-primary');
                        btn.classList.add('btn-outline-secondary');
                    });
                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('active', 'btn-admin-primary');
                });
            });
            var activeTab = document.querySelector('#userTabs button.active');
            if(activeTab) {
                activeTab.classList.remove('btn-outline-secondary');
                activeTab.classList.add('btn-admin-primary');
            }

            // ── Role limitation toggles ──────────────────────────────────────────
            @if(auth()->user()->isSuperAdmin())
            const saveUrl  = '{{ route('admin.users.roles.permissions') }}';
            const csrfToken = '{{ csrf_token() }}';

            // Toast element
            const toastEl = document.createElement('div');
            toastEl.innerHTML = `
                <div id="permToast" class="toast align-items-center border-0 position-fixed bottom-0 end-0 m-3" role="alert" style="z-index:9999; min-width:220px;">
                    <div class="d-flex">
                        <div class="toast-body fw-500" id="permToastMsg">Saved!</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>`;
            document.body.appendChild(toastEl);
            const bsToast = new bootstrap.Toast(document.getElementById('permToast'), {delay: 2500});

            function showToast(msg, success = true) {
                const toast = document.getElementById('permToast');
                toast.classList.remove('text-bg-success', 'text-bg-danger');
                toast.classList.add(success ? 'text-bg-success' : 'text-bg-danger');
                document.getElementById('permToastMsg').textContent = msg;
                bsToast.show();
            }

            function savePermissions(roleType, permissions) {
                fetch(saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ role_type: roleType, permissions })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) showToast('Permissions saved!', true);
                    else showToast(data.error || 'Failed to save.', false);
                })
                .catch(() => showToast('Network error.', false));
            }

            document.querySelectorAll('.limit-toggle-group').forEach(group => {
                const roleType = group.dataset.role;

                group.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const label = this.closest('label');
                        // A checked box = restriction enforced (red). Unchecked = lifted (green strikethrough).
                        if (this.checked) {
                            label.classList.remove('lifted');
                            label.classList.add('active');
                        } else {
                            label.classList.remove('active');
                            label.classList.add('lifted');
                        }
                        // Collect all permissions in this group
                        const permissions = {};
                        group.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                            const key = cb.dataset.permKey;
                            if (key) {
                                // checked = restricted (false in DB). unchecked = lifted (true).
                                permissions[key] = !cb.checked;
                            }
                        });
                        savePermissions(roleType, permissions);
                    });
                });
            });
            @endif
        });
    </script>
    @endpush
@endsection
