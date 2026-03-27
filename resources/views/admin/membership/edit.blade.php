@extends('admin.layout')

@section('title', 'Edit Membership Record')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12" style="background: #e1f5e9; padding: 2rem; border-radius: 15px; border-left: 5px solid #0d6e42;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 fw-800" style="color: #0d2818;">Edit Membership Record</h1>
                    <p class="text-muted mb-0">Update the details for {{ $membership->organization }}.</p>
                </div>
                <a href="{{ isset($departmentContext) ? route('admin.colleges.show-department', ['college' => $membership->college_slug, 'department' => $departmentContext, 'section' => 'membership']) : route('admin.colleges.show', ['college' => $membership->college_slug, 'section' => 'membership']) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    Back to {{ isset($departmentContext) ? 'Department' : 'College' }}
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <form action="{{ route('admin.memberships.update', $membership) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="return_college" value="{{ $returnCollege }}">
                @if(isset($departmentContext))
                    <input type="hidden" name="return_department" value="{{ $departmentContext->getRouteKey() }}">
                    <input type="hidden" name="department_id" value="{{ $departmentContext->id }}">
                @endif

                <div class="row g-4">
                    <!-- College Selection (Superadmin only) -->
                    @if(!isset($departmentContext) && auth()->user()->isSuperAdmin())
                    <div class="col-md-6">
                        <label for="college_slug" class="form-label fw-700">College <span class="text-danger">*</span></label>
                        <select name="college_slug" id="college_slug" class="form-select rounded-3 @error('college_slug') is-invalid @enderror" required>
                            @foreach($colleges as $slug => $name)
                                <option value="{{ $slug }}" {{ old('college_slug', $membership->college_slug) == $slug ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('college_slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @else
                        @if(!isset($departmentContext))
                            <input type="hidden" name="college_slug" value="{{ $membership->college_slug }}">
                        @endif
                    @endif

                    <!-- Department Selection -->
                    @if(isset($departmentContext))
                        <div class="col-md-6">
                            <label class="form-label fw-700">Department</label>
                            <input type="text" class="form-control rounded-3" value="{{ $departmentContext->name }}" readonly>
                            <small class="text-muted">This membership belongs to this department.</small>
                        </div>
                    @else
                        <div class="col-md-6">
                            <label for="department_id" class="form-label fw-700">Department (Optional)</label>
                            <select name="department_id" id="department_id" class="form-select rounded-3 @error('department_id') is-invalid @enderror">
                                <option value="">Entire College / Institution</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $membership->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Leave empty if this membership applies to the entire college.</small>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    <!-- Organization Name -->
                    <div class="col-md-6">
                        <label for="organization" class="form-label fw-700">Organization / Society Name <span class="text-danger">*</span></label>
                        <input type="text" name="organization" id="organization" class="form-control rounded-3 @error('organization') is-invalid @enderror" value="{{ old('organization', $membership->organization) }}" required>
                        @error('organization')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Membership Type -->
                    <div class="col-md-6">
                        <label for="membership_type" class="form-label fw-700">Membership Type <span class="text-danger">*</span></label>
                        <input type="text" name="membership_type" id="membership_type" class="form-control rounded-3 @error('membership_type') is-invalid @enderror" value="{{ old('membership_type', $membership->membership_type) }}" required>
                        @error('membership_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Membership Description -->
                    <div class="col-md-6">
                        <label for="description" class="form-label fw-700">Description</label>
                        <textarea name="description" id="description" class="form-control rounded-3 @error('description') is-invalid @enderror" rows="2" placeholder="Optional extra details about the membership.">{{ old('description', $membership->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Valid Until -->
                    <div class="col-md-6">
                        <label for="valid_until" class="form-label fw-700">Valid Until</label>
                        <input type="date" name="valid_until" id="valid_until" class="form-control rounded-3 @error('valid_until') is-invalid @enderror" value="{{ old('valid_until', $membership->valid_until ? $membership->valid_until->format('Y-m-d') : '') }}">
                        @error('valid_until')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Logo -->
                    <div class="col-md-12">
                        <label class="form-label fw-700">Organization Logo</label>
                        <div class="mb-3">
                            <div id="logo-preview-container" class="mb-2 {{ $membership->logo ? '' : 'd-none' }}">
                                @php
                                    $logoUrl = '#';
                                    if ($membership->logo) {
                                        $logoUrl = \App\Providers\AppServiceProvider::resolveLogoUrl($membership->logo);
                                    }
                                @endphp
                                <img id="logo-preview" src="{{ $logoUrl }}" alt="Preview" style="max-height: 100px; border-radius: 8px;">
                                <button type="button" class="btn btn-sm btn-danger ms-2" onclick="clearLogoPreview()">Remove</button>
                            </div>
                            
                            
                            
                            <div class="d-flex gap-2">
                                
                                <input type="file" name="logo" id="logo_input" class="form-control form-control-sm rounded-3 w-auto @error('logo') is-invalid @enderror" accept="image/*" onchange="previewLocalImage(this)">
                            </div>
                            @error('logo')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Sort Order & Visibility -->
                    <div class="col-md-4">
                        <label for="sort_order" class="form-label fw-700">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control rounded-3 @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $membership->sort_order) }}" min="0">
                    </div>

                    <div class="col-md-8 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_visible" id="is_visible" value="1" {{ old('is_visible', $membership->is_visible) ? 'checked' : '' }}>
                            <label class="form-check-label fw-600" for="is_visible">Visible on public page</label>
                        </div>
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-admin-primary px-5 rounded-pill shadow-sm">Update Membership</button>
                        <a href="{{ isset($departmentContext) ? route('admin.colleges.show-department', ['college' => $membership->college_slug, 'department' => $departmentContext, 'section' => 'membership']) : route('admin.colleges.show', ['college' => $membership->college_slug, 'section' => 'membership']) }}" class="btn btn-light px-4 rounded-pill ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function previewLocalImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('logo-preview');
                const container = document.getElementById('logo-preview-container');
                preview.src = e.target.result;
                container.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearLogoPreview() {
        document.getElementById('logo-preview').src = '#';
        document.getElementById('logo-preview-container').classList.add('d-none');
        document.getElementById('logo_input').value = '';
    }
</script>
@endpush
