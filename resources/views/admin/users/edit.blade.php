@extends('admin.layout')

@section('title', 'Edit User')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">Edit User</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="editor" {{ old('role', $user->role) === 'editor' ? 'selected' : '' }}>Editor</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            @if (auth()->user()?->isSuperAdmin())
                                <option value="superadmin" {{ old('role', $user->role) === 'superadmin' ? 'selected' : '' }}>Superadmin (all colleges)</option>
                            @endif
                        </select>
                        <small class="text-muted">
                            @if (auth()->user()?->isSuperAdmin())
                                Editor and Admin are scoped to a college. Superadmin has access to all colleges.
                            @else
                                Editors can view/edit content. Admins can also manage users.
                            @endif
                        </small>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6" id="college_slug-wrap">
                        <label for="college_slug" class="form-label">College <span class="text-danger" id="college-required-asterisk">*</span></label>
                        <select name="college_slug" id="college_slug" class="form-select @error('college_slug') is-invalid @enderror">
                            <option value="">— Superadmin only (all colleges) —</option>
                            @foreach ($colleges as $slug => $name)
                                <option value="{{ $slug }}" {{ old('college_slug', $user->college_slug) === $slug ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Editor and admin can only access their assigned college.</small>
                        @error('college_slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="department" class="form-label">Department</label>
                        <select name="department" id="department" class="form-select @error('department') is-invalid @enderror">
                            <option value="">— None (College-wide access) —</option>
                        </select>
                        <small class="text-muted">Optional. If specified, user will only have access to this department.</small>
                        @error('department')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="organization_id" class="form-label">Organization</label>
                        <select name="organization_id" id="organization_id" class="form-select @error('organization_id') is-invalid @enderror">
                            <option value="">— None (College/Dept wide access) —</option>
                        </select>
                        <small class="text-muted">Optional. If specified, user will only have access to this organization.</small>
                        @error('organization_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <hr class="my-3">
                        <p class="text-muted small mb-2">Leave password blank to keep current password.</p>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">New password</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirm new password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-admin-primary">Update User</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Department and Organization data from backend
        const departmentsByCollege = @json($departmentsByCollege);
        const organizationsByCollege = @json($organizationsByCollege);
        
        const collegeSelect = document.getElementById('college_slug');
        const departmentSelect = document.getElementById('department');
        const orgSelect = document.getElementById('organization_id');
        const oldDepartment = "{{ old('department', $user->department) }}";
        const oldOrg = "{{ old('organization_id', $user->organization_id) }}";
        
        function updateDepartments() {
            const selectedCollege = collegeSelect.value;
            
            // Clear current options except the first one
            departmentSelect.innerHTML = '<option value="">— None (College-wide access) —</option>';
            
            if (selectedCollege && departmentsByCollege[selectedCollege]) {
                const departments = departmentsByCollege[selectedCollege];
                departments.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept.name;
                    option.textContent = dept.name;
                    option.dataset.id = dept.id;
                    if (dept.name === oldDepartment) {
                        option.selected = true;
                    }
                    departmentSelect.appendChild(option);
                });
            }
            // Update organizations whenever department list is rebuilt
            updateOrganizations();
        }
        
        function updateOrganizations() {
            const selectedCollege = collegeSelect.value;
            const selectedDeptOption = departmentSelect.options[departmentSelect.selectedIndex];
            const selectedDeptId = selectedDeptOption && selectedDeptOption.dataset.id ? parseInt(selectedDeptOption.dataset.id) : null;
            
            orgSelect.innerHTML = '<option value="">— None (College/Dept wide access) —</option>';
            
            if (selectedCollege && organizationsByCollege[selectedCollege]) {
                const orgs = organizationsByCollege[selectedCollege].filter(org => {
                    if (selectedDeptId) {
                        return org.department_id === null || org.department_id === selectedDeptId;
                    }
                    return true;
                });

                orgs.forEach(org => {
                    const option = document.createElement('option');
                    option.value = org.id;
                    option.textContent = org.name;
                    if (org.id == oldOrg) {
                        option.selected = true;
                    }
                    orgSelect.appendChild(option);
                });
            }
        }
        
        // Update departments when college changes
        collegeSelect.addEventListener('change', updateDepartments);
        // Update organizations when department changes
        departmentSelect.addEventListener('change', updateOrganizations);
        
        // Initialize on page load
        updateDepartments();
    </script>
@endsection
