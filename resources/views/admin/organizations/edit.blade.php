@extends('admin.layout')

@section('title', 'Edit Student Organization')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12" style="background: #e1f5e9; padding: 2rem; border-radius: 15px; border-left: 5px solid #0d6e42;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 fw-800" style="color: #0d2818;">Edit Student Organization</h1>
                    <p class="text-muted mb-0">Update the details for {{ $organization->name }}.</p>
                </div>
                <a href="{{ $backUrl }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    Back to College
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <form action="{{ route('admin.organizations.update', $organization) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="return_college" value="{{ $returnCollege }}">

                <div class="row g-4">
                    <!-- College Selection (Superadmin only) -->
                    @if(auth()->user()->isSuperAdmin())
                    <div class="col-md-6">
                        <label for="college_slug" class="form-label fw-700">College <span class="text-danger">*</span></label>
                        <select name="college_slug" id="college_slug" class="form-select rounded-3 @error('college_slug') is-invalid @enderror" required>
                            @foreach($colleges as $slug => $collegeName)
                                <option value="{{ $slug }}" {{ old('college_slug', $organization->college_slug) == $slug ? 'selected' : '' }}>{{ $collegeName }}</option>
                            @endforeach
                        </select>
                        @error('college_slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @else
                        <input type="hidden" name="college_slug" value="{{ $organization->college_slug }}">
                    @endif

                    <!-- Department Selection -->
                    <div class="col-md-6">
                        <label for="department_id" class="form-label fw-700">Department {{ auth()->user()->isBoundedToDepartment() ? '' : '(Optional)' }}</label>
                        @if(auth()->user()->isBoundedToDepartment())
                            @php
                                $selectedDepartment = $departments->firstWhere('id', (int) old('department_id', $organization->department_id));
                            @endphp
                            <input type="hidden" name="department_id" value="{{ old('department_id', $organization->department_id) }}">
                            <input type="text" class="form-control rounded-3" value="{{ $selectedDepartment?->name ?? auth()->user()->department }}" disabled>
                            <small class="text-muted">Department-level accounts can only assign organizations to their own department.</small>
                        @else
                            <select name="department_id" id="department_id" class="form-select rounded-3 @error('department_id') is-invalid @enderror">
                                <option value="">Entire College</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $organization->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Leave empty if this organization applies to the entire college.</small>
                        @endif
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Organization Name -->
                    <div class="col-md-8">
                        <label for="name" class="form-label fw-700">Organization Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control rounded-3 @error('name') is-invalid @enderror" value="{{ old('name', $organization->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Acronym -->
                    <div class="col-md-4">
                        <label for="acronym" class="form-label fw-700">Acronym</label>
                        <input type="text" name="acronym" id="acronym" class="form-control rounded-3 @error('acronym') is-invalid @enderror" value="{{ old('acronym', $organization->acronym) }}">
                        @error('acronym')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Description -->
                    <div class="col-md-12">
                        <label for="description" class="form-label fw-700">Description</label>
                        <textarea name="description" id="description" rows="3" class="form-control quill-editor rounded-3 @error('description') is-invalid @enderror">{{ old('description', $organization->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Logo -->
                    <div class="col-md-12">
                        <label class="form-label fw-700">Organization Logo</label>
                        <div class="mb-3">
                            <div id="logo-preview-container" class="mb-2 {{ $organization->logo ? '' : 'd-none' }}">
                                @php
                                $logoUrl = '#';
                                if ($organization->logo) {
                                    $logoUrl = \App\Providers\AppServiceProvider::resolveImageUrl($organization->logo);
                                }
                            @endphp
                                <img id="logo-preview" src="{{ $logoUrl }}" alt="Preview" style="max-height: 100px; border-radius: 8px;">
                                <button type="button" class="btn btn-sm btn-danger ms-2" onclick="clearLogoPreview()">Remove</button>
                            </div>
                            
                            
                            <input type="hidden" name="remove_logo" id="remove_logo_input" value="0">
                            
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
                        <input type="number" name="sort_order" id="sort_order" class="form-control rounded-3 @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $organization->sort_order) }}" min="0">
                    </div>

                    <div class="col-md-8 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_visible" id="is_visible" value="1" {{ old('is_visible', $organization->is_visible) ? 'checked' : '' }}>
                            <label class="form-check-label fw-600" for="is_visible">Visible on public page</label>
                        </div>
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-admin-primary px-5 rounded-pill shadow-sm">Update Organization</button>
                        <a href="{{ $backUrl }}" class="btn btn-light px-4 rounded-pill ms-2">Cancel</a>
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
        document.getElementById('remove_logo_input').value = '1'; // Signal controller to clear logo
    }
</script>
@endpush
