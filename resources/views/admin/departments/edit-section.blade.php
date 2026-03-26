@extends('admin.layout')

@section('title', "Edit {$sectionName} - {$department->name}")

@section('content')
    @php
        $editMode = request()->get('edit'); // 'overview', 'retro', 'banner', 'card', 'program_description', 'graduate_outcomes' or null
        $sectionSlug = request()->route('section') ?? request()->get('section', 'overview');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">{{ $sectionName }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.colleges.index') }}">Colleges</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.colleges.show', ['college' => $collegeSlug, 'section' => 'departments']) }}">{{ strtoupper($collegeSlug) }} Departments</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.colleges.show-department', ['college' => $collegeSlug, 'department' => $department, 'section' => 'overview']) }}">{{ $department->title ?? $department->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit {{ $sectionName }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.colleges.show-department', ['college' => $collegeSlug, 'department' => $department, 'section' => 'overview']) }}" class="btn btn-secondary btn-sm shadow-sm rounded-pill px-3">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Back to Department
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4 rounded-3">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
            <h6 class="m-0 font-weight-bold text-primary">Edit Information</h6>
        </div>
        <div class="card-body p-4">
            {{-- Update Form Action --}}
            <form action="{{ route('admin.colleges.update-department', ['college' => $collegeSlug, 'department' => $department]) }}" method="POST" enctype="multipart/form-data" id="edit-section-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="save_dept_section" value="1">
                <input type="hidden" name="section" value="{{ $sectionSlug }}">
                
                <div class="row g-4">
                    {{-- Dynamically load the appropriate section view --}}
                    @php
                        $viewToLoad = $sectionSlug;
                        if ($sectionSlug === 'overview' && in_array($editMode, ['banner', 'card', 'retro', 'graduate_outcomes'])) {
                            $viewToLoad = str_replace('_', '-', $editMode);
                        }
                    @endphp
                    
                    @if (view()->exists('admin.departments.sections.' . $viewToLoad))
                        @include('admin.departments.sections.' . $viewToLoad, ['editMode' => $editMode])
                    @else
                        {{-- Fallback: Title and Body --}}
                        <div class="col-12 mb-3">
                            <label for="title" class="form-label">Section Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $content['title'] ?? '') }}" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="body" class="form-label">Body</label>
                            <textarea name="body" id="body" class="form-control quill-editor" rows="10">{{ old('body', $content['body'] ?? '') }}</textarea>
                            <small class="text-muted">You can use HTML for formatting.</small>
                        </div>
                    @endif

                    <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        @if ($editMode === 'alumni_roster' || $editMode === 'roster')
                            {{-- Save isn't needed for listing rosters, but just in case keeping consistent --}}
                            <a href="{{ route('admin.colleges.show-department', ['college' => $collegeSlug, 'department' => $department, 'section' => 'overview']) }}" class="btn btn-secondary px-4">Done</a>
                        @else
                            <a href="{{ route('admin.colleges.show-department', ['college' => $collegeSlug, 'department' => $department, 'section' => 'overview']) }}" class="btn btn-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm" id="save-section-btn">Save Changes</button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
