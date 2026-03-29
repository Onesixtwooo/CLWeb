@extends('admin.layout')

@section('title', "Edit {$sectionName} - {$department->name}")

@section('content')
    @php
        $editMode = request()->get('edit'); // 'overview', 'retro', 'card', 'program_description', 'graduate_outcomes' or null
        $isOverviewEdit = $editMode === 'overview';
        $isRetroEdit = $editMode === 'retro';
        $isCardEdit = $editMode === 'card';
        $isFacultySectionEdit = $sectionSlug === 'faculty' && !$editMode;
        $isProgramsSectionEdit = $sectionSlug === 'programs' && !$editMode;
        $isProgramsEdit = $editMode === 'programs';
        $isAddProgramEdit = $sectionSlug === 'programs' && $editMode === 'programs' && request()->get('action') === 'add';
        $isEditProgramEdit = $sectionSlug === 'programs' && $editMode === 'programs' && request()->get('action') === 'edit' && !empty($selectedProgram);
        $isGraduateOutcomesEdit = $editMode === 'graduate_outcomes';
        $isGraduateOutcomeAdd = $isGraduateOutcomesEdit && request()->boolean('add_outcome');
        $isGraduateOutcomeItemEdit = $isGraduateOutcomesEdit && !empty($selectedOutcome);

        $pageTitle = "Edit section: {$sectionName}";
        if ($isRetroEdit) $pageTitle = 'Edit Retro Section';
        elseif ($isCardEdit) $pageTitle = 'Edit Card Image';
        elseif ($isAddProgramEdit) $pageTitle = 'Add Program';
        elseif ($isEditProgramEdit) $pageTitle = 'Edit Program';
        elseif ($isProgramsSectionEdit) $pageTitle = 'Edit Programs Section Details';
        elseif ($isProgramsEdit) $pageTitle = 'Manage Programs';
        elseif ($isGraduateOutcomeAdd) $pageTitle = 'Add Graduate Outcome';
        elseif ($isGraduateOutcomeItemEdit) $pageTitle = 'Edit Graduate Outcome';
        elseif ($isGraduateOutcomesEdit) $pageTitle = 'Edit Graduate Outcomes';
        elseif ($editMode === 'objectives') $pageTitle = 'Edit Objectives';
        elseif ($editMode === 'add_objective') $pageTitle = 'Add Objective';
        elseif ($editMode === 'edit_objective') $pageTitle = 'Edit Objective';
        elseif ($editMode === 'curriculum') $pageTitle = 'Edit Curriculum';
        elseif ($editMode === 'add_curriculum') $pageTitle = 'Add Curriculum';
        elseif ($editMode === 'edit_curriculum') $pageTitle = 'Edit Curriculum Category';

        $isObjectivesSectionEdit = $editMode === 'objectives' || ($sectionSlug === 'objectives' && !$editMode);
        $isAddObjectiveEdit = $editMode === 'add_objective';
        $isEditObjectiveEdit = $editMode === 'edit_objective' && !empty($selectedObjective);
        $isCurriculumEdit = $editMode === 'curriculum';
        $isAddCurriculumEdit = $editMode === 'add_curriculum';
        $isEditCurriculumEdit = $editMode === 'edit_curriculum' && !empty($selectedCurriculum);
        $isAwardsEdit = $editMode === 'awards';
        $isAwardsSectionEdit = ($sectionSlug === 'awards' && !$editMode);
        $isResearchEdit = $editMode === 'research';
        $isResearchSectionEdit = ($sectionSlug === 'research' && !$editMode);
        $isExtensionEdit = $editMode === 'extension';
        $isExtensionSectionEdit = ($sectionSlug === 'extension' && !$editMode);
        $isTrainingEdit = $editMode === 'training';
        $isTrainingSectionEdit = ($sectionSlug === 'training' && !$editMode);
        $isFacilitiesEdit = ($sectionSlug === 'facilities' && (!$editMode || $editMode === 'facilities'));
        $isFacilitiesRosterEdit = $editMode === 'facilities_roster';
        $isAddFacilityEdit = $editMode === 'add_facility';
        $isEditFacilityEdit = $editMode === 'edit_facility';
        $isAlumniSectionEdit = ($sectionSlug === 'alumni' && (!$editMode || $editMode === 'alumni_details'));
        $isAlumniRosterEdit = ($sectionSlug === 'alumni' && $editMode === 'alumni_roster');
        $isAddAlumnusEdit = ($sectionSlug === 'alumni' && $editMode === 'add_alumnus');
        $isEditAlumnusEdit = ($sectionSlug === 'alumni' && $editMode === 'edit_alumnus' && !empty($selectedAlumnus));
        $isLinkagesEdit = ($sectionSlug === 'linkages' && (!$editMode || $editMode === 'linkages'));
        $isOrganizationsEdit = ($sectionSlug === 'organizations' && (!$editMode || $editMode === 'organizations'));
        $isRosterEdit = $editMode === 'roster';
        $isAddPartnerEdit = $editMode === 'add_partner';
        $isEditPartnerEdit = $editMode === 'edit_partner';

        if ($isAwardsEdit || $isAwardsSectionEdit) $pageTitle = 'Edit Awards Section Details';
        elseif ($isResearchEdit || $isResearchSectionEdit) $pageTitle = 'Edit Research Section Details';
        elseif ($isExtensionEdit) $pageTitle = request()->get('action') === 'add' ? 'Add Extension' : 'Edit Extension';
        elseif ($isExtensionSectionEdit) $pageTitle = 'Edit Extension Section Details';
        elseif ($isTrainingEdit) $pageTitle = request()->get('action') === 'add' ? 'Add Training' : 'Edit Training';
        elseif ($isTrainingSectionEdit) $pageTitle = 'Edit Training Section Details';
        elseif ($isObjectivesSectionEdit) $pageTitle = 'Edit Objectives Section Details';
        elseif ($isAddObjectiveEdit) $pageTitle = 'Add New Objective';
        elseif ($isEditObjectiveEdit) $pageTitle = 'Edit Objective';
        elseif ($isAddCurriculumEdit) $pageTitle = 'Add Curriculum Category';
        elseif ($isEditCurriculumEdit) $pageTitle = 'Edit Curriculum Category';
        elseif ($isFacilitiesEdit) $pageTitle = 'Edit Facilities Section Details';
        elseif ($isFacilitiesRosterEdit) $pageTitle = 'Manage Facilities Roster';
        elseif ($isAddFacilityEdit) $pageTitle = 'Add New Facility';
        elseif ($isEditFacilityEdit) $pageTitle = 'Edit Facility';
        elseif ($isAlumniSectionEdit) $pageTitle = 'Edit Alumni Section Details';
        elseif ($isAddAlumnusEdit) $pageTitle = 'Add Alumnus';
        elseif ($isEditAlumnusEdit) $pageTitle = 'Edit Alumnus';
        elseif ($isAlumniRosterEdit) $pageTitle = 'Manage Alumni';
        elseif ($isLinkagesEdit) $pageTitle = 'Edit Linkages Section Details';
        elseif ($isOrganizationsEdit) $pageTitle = 'Edit Student Organizations Section Details';
        elseif ($isRosterEdit) $pageTitle = 'Manage Partnership Roster';
        elseif ($isAddPartnerEdit) $pageTitle = 'Add New Partner';
        elseif ($isEditPartnerEdit) $pageTitle = 'Edit Partner: ' . ($partner->name ?? '');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="admin-page-title mb-0">{{ $pageTitle }}</h1>
            <p class="text-muted small mb-0">{{ $department->name }} — {{ $collegeName }}</p>
        </div>
        <a href="{{ route('admin.colleges.show-department', ['college' => $collegeSlug, 'department' => $department, 'section' => $sectionSlug]) }}" class="btn btn-outline-secondary">Back to Department</a>
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.colleges.update-department', ['college' => $collegeSlug, 'department' => $department]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="save_dept_section" value="1">
                <input type="hidden" name="section" value="{{ $sectionSlug }}">
                
                @if ($isRetroEdit)
                    <input type="hidden" name="_retro_edit" value="1">
                    @if (!empty($content['id'] ?? null))
                        <input type="hidden" name="retro_id" value="{{ $content['id'] }}">
                    @endif
                @elseif ($isOverviewEdit)
                    <input type="hidden" name="_overview_edit" value="1">
                @elseif ($isCardEdit)
                    <input type="hidden" name="_card_edit" value="1">
                @endif

                @if ($isRetroEdit)
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="retro_title" class="form-label">Retro Title</label>
                            <input
                                type="text"
                                name="retro_title"
                                id="retro_title"
                                class="form-control @error('retro_title') is-invalid @enderror"
                                value="{{ old('retro_title', $content['retro_title'] ?? '') }}"
                            >
                            @error('retro_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="retro_stamp" class="form-label">Retro Stamp</label>
                            <input
                                type="text"
                                name="retro_stamp"
                                id="retro_stamp"
                                class="form-control @error('retro_stamp') is-invalid @enderror"
                                value="{{ old('retro_stamp', $content['retro_stamp'] ?? '') }}"
                                placeholder="e.g. College of Engineering"
                            >
                            @error('retro_stamp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="retro_description" class="form-label">Retro Description</label>
                            <textarea
                                name="retro_description"
                                id="retro_description"
                                class="form-control quill-editor @error('retro_description') is-invalid @enderror"
                                rows="8"
                            >{{ old('retro_description', $content['retro_description'] ?? '') }}</textarea>
                            @error('retro_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="hero_background_image" class="form-label">Hero Background Image</label>
                            @if (!empty($content['hero_background_image'] ?? null))
                                <div class="mb-3">
                                    <img
                                        src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($content['hero_background_image']) }}"
                                        alt="Retro Background"
                                        class="img-fluid rounded border"
                                        style="max-height: 220px; width: 100%; object-fit: cover;"
                                    >
                                </div>
                            @endif
                            <input
                                type="file"
                                name="hero_background_image"
                                id="hero_background_image"
                                class="form-control @error('hero_background_image') is-invalid @enderror"
                                accept="image/*"
                            >
                            <small class="text-muted">Leave empty to keep the current image.</small>
                            @error('hero_background_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @elseif ($isCardEdit)
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="card_image" class="form-label">Card Image</label>
                            @if (!empty($content['card_image'] ?? null))
                                <div class="mb-3">
                                    <img
                                        src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($content['card_image']) }}"
                                        alt="Card Image"
                                        class="img-fluid rounded border"
                                        style="max-height: 320px; width: 100%; object-fit: cover;"
                                    >
                                </div>
                            @endif
                            <input
                                type="file"
                                name="card_image"
                                id="card_image"
                                class="form-control @error('card_image') is-invalid @enderror"
                                accept="image/*"
                            >
                            <small class="text-muted">Upload a replacement image for this department card.</small>
                            @error('card_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if (!empty($content['card_image'] ?? null))
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="delete_card_image" id="delete_card_image" value="1">
                                    <label class="form-check-label" for="delete_card_image">Remove current card image</label>
                                </div>
                            </div>
                        @endif
                    </div>
                @elseif ($isAddProgramEdit || $isEditProgramEdit)
                    <input type="hidden" name="_programs_edit" value="1">
                    @if ($isEditProgramEdit)
                        <input type="hidden" name="editing_program_id" value="{{ $selectedProgram->id }}">
                    @endif

                    <div class="col-12 mb-3">
                        <label for="title" class="form-label">Program Title</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $selectedProgram->title ?? '') }}" placeholder="e.g., Bachelor of Science in Information Technology">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control quill-editor @error('description') is-invalid @enderror" rows="8" placeholder="Brief description of the program...">{{ old('description', $selectedProgram->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Additional Numbered Items</label>
                        <div id="program-numbered-items">
                            @foreach(old('numbered_content', $selectedProgram->numbered_content ?? []) as $nIndex => $content)
                                <div class="input-group mb-2 program-numbered-item">
                                    <span class="input-group-text">Label</span>
                                    <input type="text" name="numbered_content[{{ $nIndex }}][label]" class="form-control" style="max-width: 100px;" value="{{ $content['label'] ?? '' }}" placeholder="e.g. V">
                                    <span class="input-group-text">Text</span>
                                    <input type="text" name="numbered_content[{{ $nIndex }}][text]" class="form-control" value="{{ $content['text'] ?? '' }}" placeholder="Content...">
                                    <button type="button" class="btn btn-outline-danger remove-program-numbered-item">&times;</button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" id="add-program-numbered-item" class="btn btn-sm btn-outline-secondary">+ Add Item</button>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="image" class="form-label">Program Image</label>
                        @if ($isEditProgramEdit && !empty($selectedProgram->image))
                            <div class="mb-2">
                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($selectedProgram->image) }}" alt="Program Image" class="img-fluid rounded border" style="max-height: 180px; width: 100%; object-fit: cover;">
                            </div>
                        @endif
                        <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const container = document.getElementById('program-numbered-items');
                            const addBtn = document.getElementById('add-program-numbered-item');
                            if (!container || !addBtn) return;

                            addBtn.addEventListener('click', function () {
                                const newIndex = container.querySelectorAll('.program-numbered-item').length;
                                const wrapper = document.createElement('div');
                                wrapper.className = 'input-group mb-2 program-numbered-item';
                                wrapper.innerHTML = `
                                    <span class="input-group-text">Label</span>
                                    <input type="text" name="numbered_content[${newIndex}][label]" class="form-control" style="max-width: 100px;" placeholder="e.g. V">
                                    <span class="input-group-text">Text</span>
                                    <input type="text" name="numbered_content[${newIndex}][text]" class="form-control" placeholder="Content...">
                                    <button type="button" class="btn btn-outline-danger remove-program-numbered-item">&times;</button>
                                `;
                                container.appendChild(wrapper);
                            });

                            container.addEventListener('click', function (event) {
                                const button = event.target.closest('.remove-program-numbered-item');
                                if (button) {
                                    button.closest('.program-numbered-item')?.remove();
                                }
                            });
                        });
                    </script>
                @elseif ($isFacultySectionEdit)
                    <input type="hidden" name="_faculty_section_edit" value="1">
                    <div class="col-12 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                        </div>
                        <small class="text-muted">Toggle to show or hide this section on the department page.</small>
                    </div>

                    <div class="col-12">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $content['title'] ?? 'Faculty') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="body" class="form-label">Details</label>
                        <textarea name="body" id="body" class="form-control quill-editor @error('body') is-invalid @enderror" rows="10">{{ old('body', $content['body'] ?? '') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                @elseif ($isProgramsSectionEdit)
                    <input type="hidden" name="_programs_section_edit" value="1">
                    <div class="col-12 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                        </div>
                        <small class="text-muted">Toggle to show or hide this section on the department page.</small>
                    </div>

                    <div class="col-12">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $content['title'] ?? 'Programs') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="body" class="form-label">Details</label>
                        <textarea name="body" id="body" class="form-control quill-editor @error('body') is-invalid @enderror" rows="10">{{ old('body', $content['body'] ?? '') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                @elseif ($isProgramsEdit)
                    @include('admin.departments.sections.programs')
                @elseif ($isGraduateOutcomesEdit)
                    <input type="hidden" name="_graduate_outcomes_edit" value="1">
                    @if ($isGraduateOutcomeAdd || $isGraduateOutcomeItemEdit)
                        @if ($isGraduateOutcomeItemEdit)
                            <input type="hidden" name="editing_outcome_id" value="{{ $selectedOutcome->id }}">
                        @endif

                        <div class="col-12 mb-3">
                            <label for="new_outcome_title" class="form-label">Outcome Title <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                name="new_outcome_title"
                                id="new_outcome_title"
                                class="form-control @error('new_outcome_title') is-invalid @enderror"
                                value="{{ old('new_outcome_title', $selectedOutcome->title ?? '') }}"
                                placeholder="e.g., Technical Proficiency"
                                required
                            >
                            @error('new_outcome_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label for="new_outcome_description" class="form-label">Description</label>
                            <textarea
                                name="new_outcome_description"
                                id="new_outcome_description"
                                class="form-control quill-editor @error('new_outcome_description') is-invalid @enderror"
                                rows="8"
                                placeholder="Describe this graduate outcome..."
                            >{{ old('new_outcome_description', $selectedOutcome->description ?? '') }}</textarea>
                            @error('new_outcome_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="new_outcome_image" class="form-label">Image (Optional)</label>
                            @if ($isGraduateOutcomeItemEdit && !empty($selectedOutcome->image))
                                <div class="mb-2">
                                    <img
                                        src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($selectedOutcome->image) }}"
                                        alt="Graduate Outcome Image"
                                        class="img-fluid rounded border"
                                        style="max-height: 180px; width: 100%; object-fit: cover;"
                                    >
                                </div>
                            @endif
                            <input
                                type="file"
                                name="new_outcome_image"
                                id="new_outcome_image"
                                class="form-control @error('new_outcome_image') is-invalid @enderror"
                                accept="image/*"
                            >
                            @error('new_outcome_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="new_outcome_sort" class="form-label">Sort Order</label>
                            <input
                                type="number"
                                name="new_outcome_sort"
                                id="new_outcome_sort"
                                class="form-control @error('new_outcome_sort') is-invalid @enderror"
                                value="{{ old('new_outcome_sort', $selectedOutcome->sort_order ?? 0) }}"
                                min="0"
                            >
                            @error('new_outcome_sort')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        @include('admin.departments.sections.graduate-outcomes')
                    @endif
                @elseif ($isObjectivesSectionEdit)
                    <input type="hidden" name="_objectives_section_edit" value="1">
                    <div class="col-12 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                        </div>
                        <small class="text-muted">Toggle to show or hide this section on the department page.</small>
                    </div>
                    <div class="col-12">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $content['title'] ?? 'Objectives') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="body" class="form-label">Details</label>
                        <textarea name="body" id="body" class="form-control quill-editor @error('body') is-invalid @enderror" rows="10">{{ old('body', $content['body'] ?? '') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                @elseif ($isAddObjectiveEdit || $isEditObjectiveEdit)
                    <input type="hidden" name="_objectives_edit" value="1">
                    @if ($isEditObjectiveEdit)
                        <input type="hidden" name="editing_objective_id" value="{{ $selectedObjective->id }}">
                    @endif
                    <div class="col-12 mb-3">
                        <label for="new_objective_content" class="form-label">Objective Content</label>
                        <textarea name="new_objective_content" id="new_objective_content" class="form-control quill-editor" rows="6" placeholder="Enter objective content...">{{ old('new_objective_content', $selectedObjective->content ?? '') }}</textarea>
                    </div>

                    <div class="col-12 mb-4">
                        <label for="new_objective_sort" class="form-label">Sort Order</label>
                        <input type="number" name="new_objective_sort" id="new_objective_sort" class="form-control" value="{{ old('new_objective_sort', $selectedObjective->sort_order ?? 0) }}">
                    </div>

                @elseif ($isCurriculumEdit)
                    <input type="hidden" name="_curriculum_edit" value="1">
                    @include('admin.departments.sections.curriculum')

                @elseif ($isAddCurriculumEdit || $isEditCurriculumEdit)
                    <input type="hidden" name="_curriculum_edit" value="1">
                    @if ($isEditCurriculumEdit)
                        <input type="hidden" name="editing_curriculum_id" value="{{ $selectedCurriculum->id }}">
                    @endif
                    <div class="col-12 mb-3">
                        <label for="title" class="form-label">Category Title</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $selectedCurriculum->title ?? '') }}" placeholder="e.g., Networking">
                    </div>

                    <div class="col-12 mb-4">
                        <label for="courses" class="form-label">Courses</label>
                        <textarea name="courses" id="courses" class="form-control quill-editor" rows="12" placeholder="List courses here, one per line or with rich formatting...">{{ old('courses', $selectedCurriculum->courses ?? '') }}</textarea>
                    </div>

                @elseif ($isAwardsEdit)
                    @include('admin.departments.sections.awards')

                @elseif ($isResearchEdit)
                    @include('admin.departments.sections.research')

                @elseif ($isExtensionEdit)
                        {{-- EXTENSION EDITOR --}}
                        <input type="hidden" name="_extension_edit" value="1">
                        @php
                            $extensionRouteKey = (string) request('extension_id');
                            $selectedExtension = collect($content['items'] ?? [])->first(function ($item) use ($extensionRouteKey) {
                                $itemId = (string) ($item['id'] ?? '');
                                $itemTitleSlug = \Illuminate\Support\Str::slug($item['title'] ?? '');

                                return $itemId === $extensionRouteKey || $itemTitleSlug === $extensionRouteKey;
                            });
                        @endphp

                        @if(request()->get('action') === 'edit' && $selectedExtension)
                        <input type="hidden" name="editing_extension_id" value="{{ $selectedExtension['id'] }}">
                        <div class="col-12 mt-4">
                            <h4 class="fw-bold mb-3">Edit Extension Activity</h4>
                            <p class="text-muted small">Update this extension activity.</p>

                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Activity Title</label>
                                        <input type="text" name="title" class="form-control" value="{{ old('title', $selectedExtension['title'] ?? '') }}" placeholder="e.g., Community Livelihood Program">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control quill-editor" rows="6" placeholder="Brief description of the activity...">{{ old('description', $selectedExtension['description'] ?? '') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Featured Image</label>
                                    @if(!empty($selectedExtension['image']))
                                        <div class="mb-2">
                                            <img src="{{ asset($selectedExtension['image']) }}" alt="Extension Image" class="img-fluid rounded border" style="max-height: 120px; width: 100%; object-fit: cover;">
                                        </div>
                                    @endif
                                    <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                                    <small class="text-muted">Recommended: 16:9 aspect ratio</small>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="col-12 mt-4">
                            <h4 class="fw-bold mb-3">Extension Activities</h4>
                            <p class="text-muted small">Manage extension programs and community engagement activities.</p>
                            
                            @php
                                $extension = $content['items'] ?? [];
                            @endphp

                            <div id="extension-container">
                                @forelse($extension as $index => $item)
                                    <div class="card mb-4 extension-item border">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">Extension #{{ $index + 1 }}</span>
                                            <button type="button" class="btn btn-danger btn-sm remove-extension-btn" title="Remove"><i class="bi bi-trash"></i> <span class="d-none d-md-inline">Remove</span></button>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <div class="mb-3">
                                                        <label class="form-label">Activity Title</label>
                                                        <input type="text" name="extension[{{ $index }}][title]" class="form-control" value="{{ $item['title'] ?? '' }}" placeholder="e.g., Community Livelihood Program">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description</label>
                                                        <textarea name="extension[{{ $index }}][description]" class="form-control quill-editor" rows="3" placeholder="Brief description of the activity...">{{ $item['description'] ?? '' }}</textarea>
                                                        <input type="hidden" name="extension[{{ $index }}][created_at]" value="{{ $item['created_at'] ?? now() }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Featured Image</label>
                                                    @if(!empty($item['image']))
                                                        <div class="mb-2">
                                                            <img src="{{ asset($item['image']) }}" alt="Extension Image" class="img-fluid rounded border" style="max-height: 120px; width: 100%; object-fit: cover;">
                                                            <input type="hidden" name="extension[{{ $index }}][existing_image]" value="{{ $item['image'] }}">
                                                        </div>
                                                    @endif
                                                    <input type="file" name="extension[{{ $index }}][image]" class="form-control form-control-sm" accept="image/*">
                                                    <small class="text-muted">Recommended: 16:9 aspect ratio</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    {{-- Empty state handled by JS --}}
                                @endforelse
                            </div>

                            <button type="button" id="add-extension-btn" class="btn btn-outline-primary" title="Add New Extension Activity"><i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add New Extension Activity</span></button>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const container = document.getElementById('extension-container');
                                const addBtn = document.getElementById('add-extension-btn');
                                const existingItems = container.querySelectorAll('.extension-item');
                                let extensionIndex = existingItems.length > 0 ? existingItems.length : 0;

                                addBtn.addEventListener('click', function() {
                                    const template = `
                                        <div class="card mb-4 extension-item border">
                                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">New Extension Activity</span>
                                                <button type="button" class="btn btn-danger btn-sm remove-extension-btn" title="Remove"><i class="bi bi-trash"></i> <span class="d-none d-md-inline">Remove</span></button>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-8">
                                                        <div class="mb-3">
                                                            <label class="form-label">Activity Title</label>
                                                            <input type="text" name="extension[${extensionIndex}][title]" class="form-control" placeholder="e.g., Community Livelihood Program">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Description</label>
                                                            <textarea name="extension[${extensionIndex}][description]" class="form-control quill-editor" rows="3" placeholder="Brief description of the activity..."></textarea>
                                                            <input type="hidden" name="extension[${extensionIndex}][created_at]" value="{{ now() }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Featured Image</label>
                                                        <input type="file" name="extension[${extensionIndex}][image]" class="form-control form-control-sm" accept="image/*">
                                                        <small class="text-muted">Recommended: 16:9 aspect ratio</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    container.insertAdjacentHTML('beforeend', template);
                                    extensionIndex++;
                                });

                                container.addEventListener('click', function(e) {
                                    const btn = e.target.closest('.remove-extension-btn');
                                    if (btn) {
                                        if (confirm('Remove this extension activity?')) {
                                            btn.closest('.extension-item').remove();
                                        }
                                    }
                                });

                                @if(request()->get('action') === 'add')
                                setTimeout(function() {
                                    if (addBtn) {
                                        addBtn.click();
                                        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
                                    }
                                }, 200);
                                @endif
                            });
                        </script>
                        @endif

                    @elseif ($isTrainingEdit)
                        {{-- TRAINING EDITOR --}}
                        <input type="hidden" name="_training_edit" value="1">
                        @php
                            $trainingRouteKey = (string) request('training_id');
                            $selectedTraining = collect($content['items'] ?? [])->first(function ($item) use ($trainingRouteKey) {
                                $itemId = (string) ($item['id'] ?? '');
                                $itemTitleSlug = \Illuminate\Support\Str::slug($item['title'] ?? '');

                                return $itemId === $trainingRouteKey || $itemTitleSlug === $trainingRouteKey;
                            });
                        @endphp

                        @if(request()->get('action') === 'edit' && $selectedTraining)
                        <input type="hidden" name="editing_training_id" value="{{ $selectedTraining['id'] }}">
                        <div class="col-12 mt-4">
                            <h4 class="fw-bold mb-3">Edit Training Item</h4>
                            <p class="text-muted small">Update this training item.</p>

                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Training Title</label>
                                        <input type="text" name="title" class="form-control" value="{{ old('title', $selectedTraining['title'] ?? '') }}" placeholder="e.g., Advanced Web Development Workshop">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control quill-editor" rows="6" placeholder="Brief description of the training...">{{ old('description', $selectedTraining['description'] ?? '') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Featured Image</label>
                                    @if(!empty($selectedTraining['image']))
                                        <div class="mb-2">
                                            <img src="{{ asset($selectedTraining['image']) }}" alt="Training Image" class="img-fluid rounded border" style="max-height: 120px; width: 100%; object-fit: cover;">
                                        </div>
                                    @endif
                                    <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                                    <small class="text-muted">Recommended: 16:9 aspect ratio</small>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="col-12 mt-4">
                            <h4 class="fw-bold mb-3">Training & Workshops</h4>
                            <p class="text-muted small">Manage training programs and workshops offered.</p>

                            @php
                                $training = $content['items'] ?? [];
                            @endphp

                            <div id="training-container">
                                @forelse($training as $index => $item)
                                    <div class="card mb-4 training-item border">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">Training #{{ $index + 1 }}</span>
                                            <button type="button" class="btn btn-danger btn-sm remove-training-btn" title="Remove"><i class="bi bi-trash"></i> <span class="d-none d-md-inline">Remove</span></button>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <div class="mb-3">
                                                        <label class="form-label">Training Title</label>
                                                        <input type="text" name="training[{{ $index }}][title]" class="form-control" value="{{ $item['title'] ?? '' }}" placeholder="e.g., Advanced Web Development Workshop">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description</label>
                                                        <textarea name="training[{{ $index }}][description]" class="form-control quill-editor" rows="3" placeholder="Brief description of the training...">{{ $item['description'] ?? '' }}</textarea>
                                                        <input type="hidden" name="training[{{ $index }}][created_at]" value="{{ $item['created_at'] ?? now() }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Featured Image</label>
                                                    @if(!empty($item['image']))
                                                        <div class="mb-2">
                                                            <img src="{{ asset($item['image']) }}" alt="Training Image" class="img-fluid rounded border" style="max-height: 120px; width: 100%; object-fit: cover;">
                                                            <input type="hidden" name="training[{{ $index }}][existing_image]" value="{{ $item['image'] }}">
                                                        </div>
                                                    @endif
                                                    <input type="file" name="training[{{ $index }}][image]" class="form-control form-control-sm" accept="image/*">
                                                    <small class="text-muted">Recommended: 16:9 aspect ratio</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    {{-- Empty state handled by JS --}}
                                @endforelse
                            </div>

                            <button type="button" id="add-training-btn" class="btn btn-outline-primary" title="Add New Training"><i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add New Training</span></button>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const container = document.getElementById('training-container');
                                const addBtn = document.getElementById('add-training-btn');
                                const existingItems = container.querySelectorAll('.training-item');
                                let trainingIndex = existingItems.length > 0 ? existingItems.length : 0;

                                addBtn.addEventListener('click', function() {
                                    const template = `
                                        <div class="card mb-4 training-item border">
                                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">New Training</span>
                                                <button type="button" class="btn btn-danger btn-sm remove-training-btn" title="Remove"><i class="bi bi-trash"></i> <span class="d-none d-md-inline">Remove</span></button>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-8">
                                                        <div class="mb-3">
                                                            <label class="form-label">Training Title</label>
                                                            <input type="text" name="training[${trainingIndex}][title]" class="form-control" placeholder="e.g., Advanced Web Development Workshop">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Description</label>
                                                            <textarea name="training[${trainingIndex}][description]" class="form-control quill-editor" rows="3" placeholder="Brief description of the training..."></textarea>
                                                            <input type="hidden" name="training[${trainingIndex}][created_at]" value="{{ now() }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Featured Image</label>
                                                        <input type="file" name="training[${trainingIndex}][image]" class="form-control form-control-sm" accept="image/*">
                                                        <small class="text-muted">Recommended: 16:9 aspect ratio</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    container.insertAdjacentHTML('beforeend', template);
                                    trainingIndex++;
                                });

                                container.addEventListener('click', function(e) {
                                    const btn = e.target.closest('.remove-training-btn');
                                    if (btn) {
                                        if (confirm('Remove this training program?')) {
                                            btn.closest('.training-item').remove();
                                        }
                                    }
                                });

                                @if(request()->get('action') === 'add')
                                setTimeout(function() {
                                    if (addBtn) {
                                        addBtn.click();
                                        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
                                    }
                                }, 200);
                                @endif
                            });
                        </script>
                        @endif

                    @elseif ($isFacilitiesEdit)
                        {{-- FACILITIES EDITOR --}}
                        <input type="hidden" name="_facilities_edit" value="1">
                        <div class="col-12 mb-3">
                            <label for="title" class="form-label">Facilities Section Title</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $content['title'] ?? 'Facilities & Resources') }}" placeholder="e.g., Facilities & Resources">
                        </div>

                        <div class="col-12 mb-3">
                            <label for="body" class="form-label">Introductory Text</label>
                            <textarea name="body" id="body" class="form-control quill-editor" rows="4" placeholder="Brief introduction about your department's facilities...">{{ old('body', $content['body'] ?? '') }}</textarea>
                        </div>

                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                            </div>
                        </div>

                    @elseif ($isFacilitiesRosterEdit)
                        {{-- FACILITIES ROSTER EDITOR --}}
                        <div class="col-12">
                            <h4 class="fw-bold mb-3">Facilities Roster</h4>
                            <p class="text-muted small">Manage laboratory facilities, equipment, and other departmental resources.</p>
                            
                            @php
                                $facilities = $content['items'] ?? [];
                            @endphp

                            <div id="facilities-container">
                                @forelse($facilities as $index => $item)
                                    <div class="card mb-4 facilities-item border">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">Facility #{{ $index + 1 }}</span>
                                            <button type="button" class="btn btn-danger btn-sm remove-facilities-btn" title="Remove"><i class="bi bi-trash"></i> <span class="d-none d-md-inline">Remove</span></button>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <div class="mb-3">
                                                        <label class="form-label">Facility Title</label>
                                                        <input type="text" name="facilities[{{ $index }}][title]" class="form-control" value="{{ $item['title'] ?? '' }}" placeholder="e.g., Computer Laboratory 1">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description (Optional)</label>
                                                        <textarea name="facilities[{{ $index }}][description]" class="form-control quill-editor" rows="2" placeholder="Brief description of the facility...">{{ $item['description'] ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Featured Image</label>
                                                    @if(!empty($item['image']))
                                                        <div class="mb-2">
                                                            <img src="{{ $item['image'] }}" alt="Facility Image" class="img-fluid rounded border" style="max-height: 100px; width: 100%; object-fit: contain;">
                                                            <input type="hidden" name="facilities[{{ $index }}][existing_image]" value="{{ $item['image'] }}">
                                                        </div>
                                                    @endif
                                                    <input type="file" name="facilities[{{ $index }}][image]" class="form-control form-control-sm" accept="image/*">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            </div>

                            <button type="button" id="add-facilities-btn" class="btn btn-outline-primary" title="Add New Facility"><i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add New Facility</span></button>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const container = document.getElementById('facilities-container');
                                const addBtn = document.getElementById('add-facilities-btn');
                                let facilitiesIndex = container.querySelectorAll('.facilities-item').length;

                                addBtn.addEventListener('click', function() {
                                    const template = `
                                        <div class="card mb-4 facilities-item border">
                                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">New Facility</span>
                                                <button type="button" class="btn btn-danger btn-sm remove-facilities-btn" title="Remove"><i class="bi bi-trash"></i> <span class="d-none d-md-inline">Remove</span></button>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-8">
                                                        <div class="mb-3">
                                                            <label class="form-label">Facility Title</label>
                                                            <input type="text" name="facilities[${facilitiesIndex}][title]" class="form-control" placeholder="e.g., Computer Laboratory 1">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Description (Optional)</label>
                                                            <textarea name="facilities[${facilitiesIndex}][description]" class="form-control quill-editor" rows="2" placeholder="Brief description of the facility..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Featured Image</label>
                                                        <input type="file" name="facilities[${facilitiesIndex}][image]" class="form-control form-control-sm" accept="image/*">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    container.insertAdjacentHTML('beforeend', template);
                                    facilitiesIndex++;
                                });

                                container.addEventListener('click', function(e) {
                                    const btn = e.target.closest('.remove-facilities-btn');
                                    if (btn) {
                                        if (confirm('Remove this facility item?')) {
                                            btn.closest('.facilities-item').remove();
                                        }
                                    }
                                });
                            });
                        </script>

                    @elseif ($isAddFacilityEdit)
                        {{-- ADD SINGLE FACILITY --}}
                        <input type="hidden" name="_add_facility_edit" value="1">
                        <div class="col-12">
                            <h4 class="fw-bold mb-3">Add New Facility</h4>
                            <p class="text-muted small">Add a new laboratory facility or resource to the catalog.</p>
                            
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Facility Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="e.g., Computer Laboratory 1">
                                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Description (Optional)</label>
                                        <textarea name="description" class="form-control quill-editor" rows="4" placeholder="Brief description of the facility...">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Featured Image</label>
                                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                        <small class="text-muted d-block mt-1">Recommended: 16:9 aspect ratio</small>
                                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                    @elseif ($isEditFacilityEdit)
                        {{-- EDIT SINGLE FACILITY --}}
                        <input type="hidden" name="_edit_facility_edit" value="1">
                        <div class="col-12">
                            <h4 class="fw-bold mb-3">Edit Facility</h4>
                            <p class="text-muted small">Update details for this facility.</p>

                            @if(!empty($selectedFacility))
                                <input type="hidden" name="facility_id" value="{{ $selectedFacility->id }}">
                                
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Facility Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $selectedFacility->title ?? '') }}" required placeholder="e.g., Computer Laboratory 1">
                                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Description (Optional)</label>
                                            <textarea name="description" class="form-control quill-editor" rows="4" placeholder="Brief description of the facility...">{{ old('description', $selectedFacility->description ?? '') }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Featured Image</label>
                                            @if(!empty($selectedFacility->image))
                                                <div class="mb-2 p-2 border rounded text-center bg-light">
                                                    <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($selectedFacility->image) }}" alt="{{ $selectedFacility->title ?? 'Facility Image' }}" class="img-fluid" style="max-height: 120px; object-fit: contain;">
                                                </div>
                                            @endif
                                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                            <small class="text-muted d-block mt-1">Upload to replace the current image. Recommended: 16:9 aspect ratio</small>
                                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-danger">Facility not found.</div>
                            @endif
                        </div>

                    @elseif ($isAddAlumnusEdit || $isEditAlumnusEdit)
                        <input type="hidden" name="_alumni_edit" value="1">
                        @if($isEditAlumnusEdit)
                            <input type="hidden" name="editing_alumnus_id" value="{{ $selectedAlumnus->id }}">
                        @endif

                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Alumnus Name <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $selectedAlumnus->title ?? '') }}" required placeholder="e.g., Juan Dela Cruz">
                                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Year Graduated/Batch</label>
                                        <input type="text" name="year_graduated" class="form-control @error('year_graduated') is-invalid @enderror" value="{{ old('year_graduated', $selectedAlumnus->year_graduated ?? '') }}" placeholder="e.g., Batch 2020">
                                        @error('year_graduated')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Achievements/Success Story</label>
                                        <textarea name="description" class="form-control quill-editor @error('description') is-invalid @enderror" rows="6" placeholder="Brief story or achievement...">{{ old('description', $selectedAlumnus->description ?? '') }}</textarea>
                                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Profile Photo</label>
                                    @if($isEditAlumnusEdit && !empty($selectedAlumnus->image))
                                        <div class="mb-2">
                                            <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($selectedAlumnus->image) }}" alt="Alumnus Image" class="img-fluid rounded border" style="max-height: 180px; width: 100%; object-fit: cover;">
                                        </div>
                                    @endif
                                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                    <small class="text-muted d-block mt-1">Recommended: 1:1 aspect ratio</small>
                                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror

                                    @if($isEditAlumnusEdit && !empty($selectedAlumnus->image))
                                        <div class="form-check mt-3">
                                            <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                                            <label class="form-check-label" for="remove_image">Remove current image</label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    @elseif ($isAlumniRosterEdit)
                        {{-- ALUMNI EDITOR --}}
                        <input type="hidden" name="_alumni_edit" value="1">
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                            </div>
                            <small class="text-muted">Toggle to show or hide this section on the department page.</small>
                        </div>

                        <div class="col-12 mt-4">
                            <h4 class="fw-bold mb-3">Notable Alumni</h4>
                            <p class="text-muted small">Manage successful alumni stories and achievements.</p>
                            
                            @php
                                $alumni = $content['items'] ?? [];
                            @endphp

                            <div id="alumni-container">
                                @forelse($alumni as $index => $item)
                                    <div class="card mb-4 alumni-item border">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">Alumnus #{{ $index + 1 }}</span>
                                            <button type="button" class="btn btn-danger btn-sm remove-alumni-btn" title="Remove"><i class="bi bi-trash"></i> <span class="d-none d-md-inline">Remove</span></button>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <div class="mb-3">
                                                        <label class="form-label">Alumnus Name</label>
                                                        <input type="text" name="alumni[{{ $index }}][title]" class="form-control" value="{{ $item['title'] ?? '' }}" placeholder="e.g., Juan Dela Cruz">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Year Graduated/Batch</label>
                                                        <input type="text" name="alumni[{{ $index }}][year_graduated]" class="form-control" value="{{ $item['year_graduated'] ?? '' }}" placeholder="e.g., Batch 2020">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Achievements/Success Story</label>
                                                        <textarea name="alumni[{{ $index }}][description]" class="form-control quill-editor" rows="3" placeholder="Brief story or achievement...">{{ $item['description'] ?? '' }}</textarea>
                                                        <input type="hidden" name="alumni[{{ $index }}][created_at]" value="{{ $item['created_at'] ?? now() }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Profile Photo</label>
                                                    @if(!empty($item['image']))
                                                        <div class="mb-2">
                                                            <img src="{{ asset($item['image']) }}" alt="Alumni Image" class="img-fluid rounded border" style="max-height: 120px; width: 100%; object-fit: cover;">
                                                            <input type="hidden" name="alumni[{{ $index }}][existing_image]" value="{{ $item['image'] }}">
                                                        </div>
                                                    @endif
                                                    <input type="file" name="alumni[{{ $index }}][image]" class="form-control form-control-sm" accept="image/*">
                                                    <small class="text-muted">Recommended: 1:1 aspect ratio</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            </div>

                            <button type="button" id="add-alumni-btn" class="btn btn-outline-primary" title="Add New Alumnus"><i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add New Alumnus</span></button>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const container = document.getElementById('alumni-container');
                                const addBtn = document.getElementById('add-alumni-btn');
                                let alumniIndex = container.querySelectorAll('.alumni-item').length;

                                addBtn.addEventListener('click', function() {
                                    const template = `
                                        <div class="card mb-4 alumni-item border">
                                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">New Alumnus</span>
                                                <button type="button" class="btn btn-danger btn-sm remove-alumni-btn" title="Remove"><i class="bi bi-trash"></i> <span class="d-none d-md-inline">Remove</span></button>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-8">
                                                        <div class="mb-3">
                                                            <label class="form-label">Alumnus Name</label>
                                                            <input type="text" name="alumni[${alumniIndex}][title]" class="form-control" placeholder="e.g., Juan Dela Cruz">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Year Graduated/Batch</label>
                                                            <input type="text" name="alumni[${alumniIndex}][year_graduated]" class="form-control" placeholder="e.g., Batch 2020">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Achievements/Success Story</label>
                                                            <textarea name="alumni[${alumniIndex}][description]" class="form-control quill-editor" rows="3" placeholder="Brief story or achievement..."></textarea>
                                                            <input type="hidden" name="alumni[${alumniIndex}][created_at]" value="{{ now() }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Profile Photo</label>
                                                        <input type="file" name="alumni[${alumniIndex}][image]" class="form-control form-control-sm" accept="image/*">
                                                        <small class="text-muted">Recommended: 1:1 aspect ratio</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    container.insertAdjacentHTML('beforeend', template);
                                    alumniIndex++;
                                });

                                container.addEventListener('click', function(e) {
                                    const btn = e.target.closest('.remove-alumni-btn');
                                    if (btn) {
                                        if (confirm('Remove this alumnus?')) {
                                            btn.closest('.alumni-item').remove();
                                        }
                                    }
                                });

                                @if(request()->get('edit') === 'add_alumnus')
                                setTimeout(function() {
                                    if (addBtn) {
                                        addBtn.click();
                                        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
                                    }
                                }, 200);
                                @endif
                            });
                        </script>

                    @elseif ($isAwardsEdit)
                        {{-- Individual New View layout --}}
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                            </div>
                        </div>

                        @if (request()->query('edit') === 'add_award' || (request()->query('edit') === 'edit_award' && isset($selectedAward)))
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h5 class="fw-bold mb-0">{{ isset($selectedAward) ? 'Edit Award' : 'Add New Award' }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label class="form-label">Award Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="award_title" class="form-control" value="{{ old('award_title', $selectedAward->title ?? '') }}" required placeholder="e.g., Best Capstone Project">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="award_description" class="form-control quill-editor" rows="4" placeholder="Brief description of the award...">{{ old('award_description', $selectedAward->description ?? '') }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Award Image</label>
                                                @if(isset($selectedAward) && $selectedAward->image)
                                                    <div class="mb-2">
                                                        <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($selectedAward->image) }}" alt="Award Image" class="img-fluid rounded border" style="max-height: 120px; object-fit: contain;">
                                                    </div>
                                                @endif
                                                <input type="file" name="award_image" class="form-control form-control-sm" accept="image/*">
                                                @if(isset($selectedAward))
                                                    <input type="hidden" name="award_id" value="{{ $selectedAward->id }}">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-12 mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="fw-bold mb-0">Student & Faculty Awards</h4>
                                    <a href="{{ route('admin.colleges.edit-department-section', ['college' => $collegeSlug, 'department' => $department, 'section' => $sectionSlug]) }}?edit=add_award" class="btn btn-outline-primary btn-sm">
                                        Add Award
                                    </a>
                                </div>

                                @php
                                    $awards = $department->awards()->orderBy('sort_order')->get();
                                @endphp

                                @if($awards->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle">
                                            <thead>
                                                <tr>
                                                    <th style="width: 50px">Sort</th>
                                                    <th>Title</th>
                                                    <th>Description</th>
                                                    <th style="width: 120px">Image</th>
                                                    <th style="width: 140px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($awards as $award)
                                                    <tr>
                                                        <td class="text-center">{{ $award->sort_order }}</td>
                                                        <td class="fw-bold">{{ $award->title }}</td>
                                                        <td>
                                                            <div style="max-width: 100%; overflow-wrap: anywhere; word-break: break-word;">
                                                                {!! $award->description !!}
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            @if($award->image)
                                                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($award->image) }}" alt="Award Image" style="height: 40px; width: auto; object-fit: contain;">
                                                            @else
                                                                <span class="text-muted small">No Image</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="d-inline-flex gap-2">
                                                                <a href="{{ route('admin.colleges.edit-department-section', ['college' => $collegeSlug, 'department' => $department, 'section' => $sectionSlug]) }}?edit=edit_award&award_id={{ $award->id }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                                                                <button type="submit" name="delete_award" value="{{ $award->id }}" class="btn btn-danger btn-sm" onclick="return confirm('Delete this award?')">Delete</button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">No awards added yet.</div>
                                @endif
                            </div>
                        @endif

                    @elseif ($isResearchEdit)
                        {{-- RESEARCH EDITOR --}}
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <h4 class="fw-bold mb-3">Research Requests & Projects</h4>
                            <div id="research-container">
                                @php $research = $content['items'] ?? []; @endphp
                                @forelse($research as $index => $item)
                                    <div class="card mb-4 research-item border">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center"><span>Research #{{ $index+1 }}</span><button type="button" class="btn btn-danger btn-sm remove-research-btn">Remove</button></div>
                                        <div class="card-body">
                                            <input type="text" name="research[{{ $index }}][title]" class="form-control mb-2" value="{{ $item['title'] ?? '' }}" placeholder="Title">
                                            <textarea name="research[{{ $index }}][description]" class="form-control quill-editor" rows="3">{{ $item['description'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            </div>
                            <button type="button" class="btn btn-outline-primary" id="add-research-btn">Add Research</button>
                        </div>

                    @elseif ($isExtensionEdit)
                        {{-- EXTENSION EDITOR --}}
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold">Visible</label>
                            </div>
                        </div>

                    @elseif ($isTrainingEdit)
                        {{-- TRAINING EDITOR --}}
                        <div class="col-12 mt-4"><h4>Training Programs</h4></div>

                    @elseif ($isAlumniRosterEdit)
                        {{-- ALUMNI EDITOR --}}
                        <div class="col-12 mt-4"><h4>Alumni</h4></div>

                    @elseif ($isLinkagesEdit)
                        {{-- LINKAGES EDITOR --}}
                        <input type="hidden" name="_linkages_edit" value="1">
                        <div class="col-12 mb-3">
                            <label for="title" class="form-label">Linkages Section Title</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $content['title'] ?? 'Linkages') }}" placeholder="e.g., Linkages">
                        </div>

                        <div class="col-12 mb-3">
                            <label for="body" class="form-label">Introductory Text</label>
                            <textarea name="body" id="body" class="form-control quill-editor" rows="4" placeholder="Brief introduction about your department's linkages...">{{ old('body', $content['body'] ?? '') }}</textarea>
                        </div>

                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                            </div>
                        </div>

                    @elseif ($isOrganizationsEdit)
                        <input type="hidden" name="_organizations_edit" value="1">
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                            </div>
                            <small class="text-muted">Toggle to show or hide this section on the department page.</small>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="title" class="form-label">Student Organizations Section Title</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $content['title'] ?? 'Student Organizations') }}" placeholder="e.g., Student Organizations">
                        </div>

                        <div class="col-12 mb-3">
                            <label for="body" class="form-label">Introductory Text</label>
                            <textarea name="body" id="body" class="form-control quill-editor" rows="5" placeholder="Brief introduction about your department's student organizations...">{{ old('body', $content['body'] ?? '') }}</textarea>
                        </div>

                    @elseif ($isRosterEdit)
                        {{-- ROSTER EDITOR --}}
                        <div class="col-12">
                            <h4 class="fw-bold mb-3">Partnership Roster</h4>
                            <p class="text-muted small">Manage local and international linkages for this department.</p>
                            
                            @php
                                $linkages = $content['items'] ?? [];
                            @endphp

                            <div id="linkages-container">
                                @forelse($linkages as $index => $item)
                                    <div class="card mb-4 linkage-item border">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">Partner #{{ $index + 1 }}</span>
                                            <button type="button" class="btn btn-danger btn-sm remove-linkage-btn" title="Remove"><i class="bi bi-trash"></i> <span class="d-none d-md-inline">Remove</span></button>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <div class="row g-3">
                                                        <div class="col-md-8">
                                                            <div class="mb-3">
                                                                <label class="form-label">Partner Name</label>
                                                                <input type="text" name="linkages[{{ $index }}][name]" class="form-control" value="{{ $item['name'] ?? '' }}" placeholder="e.g., University of Example">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">Type</label>
                                                                <select name="linkages[{{ $index }}][type]" class="form-select">
                                                                    <option value="local" {{ ($item['type'] ?? '') === 'local' ? 'selected' : '' }}>Local</option>
                                                                    <option value="international" {{ ($item['type'] ?? '') === 'international' ? 'selected' : '' }}>International</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Description (Optional)</label>
                                                        <textarea name="linkages[{{ $index }}][description]" class="form-control quill-editor" rows="2" placeholder="Brief description of the partnership...">{{ $item['description'] ?? '' }}</textarea>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Website URL (Optional)</label>
                                                        <input type="url" name="linkages[{{ $index }}][url]" class="form-control" value="{{ $item['url'] ?? '' }}" placeholder="https://example.edu">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Partner Logo</label>
                                                    @if(!empty($item['image']))
                                                        <div class="mb-2">
                                                            <img src="{{ $item['image'] }}" alt="Partner Logo" class="img-fluid rounded border" style="max-height: 100px; width: 100%; object-fit: contain;">
                                                            <input type="hidden" name="linkages[{{ $index }}][existing_image]" value="{{ $item['image'] }}">
                                                        </div>
                                                    @endif
                                                    <input type="file" name="linkages[{{ $index }}][image]" class="form-control form-control-sm" accept="image/*">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            </div>

                            <button type="button" id="add-linkage-btn" class="btn btn-outline-primary" title="Add New Partner"><i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add New Partner</span></button>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const container = document.getElementById('linkages-container');
                                const addBtn = document.getElementById('add-linkage-btn');
                                let linkageIndex = container.querySelectorAll('.linkage-item').length;

                                addBtn.addEventListener('click', function() {
                                    const template = `
                                        <div class="card mb-4 linkage-item border">
                                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">New Partner</span>
                                                <button type="button" class="btn btn-danger btn-sm remove-linkage-btn" title="Remove"><i class="bi bi-trash"></i> <span class="d-none d-md-inline">Remove</span></button>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-8">
                                                        <div class="row g-3">
                                                            <div class="col-md-8">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Partner Name</label>
                                                                    <input type="text" name="linkages[${linkageIndex}][name]" class="form-control" placeholder="e.g., University of Example">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Type</label>
                                                                    <select name="linkages[${linkageIndex}][type]" class="form-select">
                                                                        <option value="local">Local</option>
                                                                        <option value="international">International</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Description (Optional)</label>
                                                            <textarea name="linkages[${linkageIndex}][description]" class="form-control quill-editor" rows="2" placeholder="Brief description of the partnership..."></textarea>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Website URL (Optional)</label>
                                                            <input type="url" name="linkages[${linkageIndex}][url]" class="form-control" placeholder="https://example.edu">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Partner Logo</label>
                                                        <input type="file" name="linkages[${linkageIndex}][image]" class="form-control form-control-sm" accept="image/*">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    container.insertAdjacentHTML('beforeend', template);
                                    linkageIndex++;
                                });

                                container.addEventListener('click', function(e) {
                                    const btn = e.target.closest('.remove-linkage-btn');
                                    if (btn) {
                                        if (confirm('Remove this partner?')) {
                                            btn.closest('.linkage-item').remove();
                                        }
                                    }
                                });
                            });
                        </script>
                    @elseif ($isEditPartnerEdit)
                        {{-- SINGLE PARTNER EDITOR --}}
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0 fw-bold">Edit Partner: {{ $partner->name }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <div class="mb-3">
                                                        <label class="form-label">Partner Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control" value="{{ old('name', $partner->name) }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Type</label>
                                                        <select name="type" class="form-select">
                                                            <option value="local" {{ old('type', $partner->type) === 'local' ? 'selected' : '' }}>Local</option>
                                                            <option value="international" {{ old('type', $partner->type) === 'international' ? 'selected' : '' }}>International</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Description (Optional)</label>
                                                <textarea name="description" class="form-control quill-editor" rows="4">{{ old('description', $partner->description) }}</textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Website URL (Optional)</label>
                                                <input type="text" name="url" class="form-control" value="{{ old('url', $partner->url) }}" placeholder="https://example.edu or facebook.com">
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <label class="form-label d-block text-start">Partner Logo</label>
                                            @if($partner->image)
                                                <div class="mb-3">
                                                    <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($partner->image) }}" alt="Current Logo" class="img-thumbnail" style="max-height: 150px;">
                                                    <p class="small text-muted mt-1">Current Logo</p>
                                                </div>
                                            @endif
                                            <input type="file" name="image" class="form-control" accept="image/*">
                                            <small class="text-muted d-block mt-2">Upload to replace current logo. Recommended size: 200x200px.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif ($isAddPartnerEdit)
                        {{-- SINGLE PARTNER ADDER --}}
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0 fw-bold">New Partner</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <div class="mb-3">
                                                        <label class="form-label">Partner Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control" placeholder="e.g., University of Example" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Type</label>
                                                        <select name="type" class="form-select">
                                                            <option value="local">Local</option>
                                                            <option value="international">International</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Description (Optional)</label>
                                                <textarea name="description" class="form-control quill-editor" rows="4" placeholder="Brief description of the partnership..."></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Website URL (Optional)</label>
                                                <input type="text" name="url" class="form-control" placeholder="https://example.edu or facebook.com">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Partner Logo</label>
                                            <input type="file" name="image" class="form-control" accept="image/*">
                                            <small class="text-muted d-block mt-2">Recommended size: 200x200px (PNG or JPG)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @else
                        @if($isTrainingSectionEdit)
                            <input type="hidden" name="_training_section_edit" value="1">
                            <div class="col-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                                </div>
                                <small class="text-muted">Toggle to show or hide this section on the department page.</small>
                            </div>
                        @endif

                        @if($isExtensionSectionEdit)
                            <input type="hidden" name="_extension_section_edit" value="1">
                            <div class="col-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                                </div>
                                <small class="text-muted">Toggle to show or hide this section on the department page.</small>
                            </div>
                        @endif

                        @if($isResearchSectionEdit)
                            <input type="hidden" name="_research_edit" value="1">
                            <div class="col-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                                </div>
                                <small class="text-muted">Toggle to show or hide this section on the department page.</small>
                            </div>
                        @endif

                        @if($isAlumniSectionEdit)
                            <input type="hidden" name="_alumni_section_edit" value="1">
                            <div class="col-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                                </div>
                                <small class="text-muted">Toggle to show or hide this section on the department page.</small>
                            </div>
                        @endif

                        @if($isAwardsSectionEdit)
                            <input type="hidden" name="_awards_edit" value="1">
                            <div class="col-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                                </div>
                                <small class="text-muted">Toggle to show or hide this section on the department page.</small>
                            </div>
                        @endif

                        @if($sectionSlug === 'membership' && !$editMode)
                            <input type="hidden" name="_membership_section_edit" value="1">
                            <div class="col-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                                </div>
                                <small class="text-muted">Toggle to show or hide this section on the department page.</small>
                            </div>
                        @endif

                        @if($sectionSlug === 'overview' && !$editMode)
                            <div class="col-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                                </div>
                                <small class="text-muted">Toggle to show or hide this section on the department page.</small>
                            </div>

                            <div class="col-12 mb-3">
                                <input type="hidden" name="bulk_section_visibility_mode" value="1">
                                <input type="hidden" name="bulk_section_visibility" value="0">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="bulk_section_visibility" name="bulk_section_visibility" value="1" {{ old('bulk_section_visibility', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="bulk_section_visibility">Make all department sections visible</label>
                                </div>
                                <small class="text-muted">Turn this off to hide all department sections in one go.</small>
                            </div>

                            <div class="col-md-6">
                                <label for="logo" class="form-label">Department Logo</label>
                                <div class="border rounded-4 bg-light d-flex align-items-center justify-content-center overflow-hidden mb-3 position-relative" style="width: 160px; height: 160px;">
                                    @if (!empty($department->logo))
                                        <img
                                            src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($department->logo) }}"
                                            alt="{{ $department->name }} logo"
                                            style="max-width: 100%; max-height: 100%; object-fit: contain;"
                                        >
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-light border rounded-circle position-absolute top-0 end-0 m-2 d-inline-flex align-items-center justify-content-center"
                                            style="width: 34px; height: 34px;"
                                            title="Remove current logo"
                                            aria-label="Remove current logo"
                                            onclick="document.getElementById('remove_logo').value = '1'; this.closest('.position-relative').querySelector('img')?.remove(); this.remove(); this.closest('.position-relative').insertAdjacentHTML('beforeend', '<div class=&quot;text-center text-muted px-3&quot;><i class=&quot;bi bi-image fs-1 d-block mb-2 opacity-50&quot;></i><small>No logo uploaded</small></div>');"
                                        >
                                            <i class="bi bi-trash text-danger"></i>
                                        </button>
                                    @else
                                        <div class="text-center text-muted px-3">
                                            <i class="bi bi-image fs-1 d-block mb-2 opacity-50"></i>
                                            <small>No logo uploaded</small>
                                        </div>
                                    @endif
                                </div>
                                <input type="hidden" name="remove_logo" id="remove_logo" value="0">
                                <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                                <small class="text-muted d-block mt-2">Upload a square logo to represent this department.</small>
                                @error('logo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="col-12">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $content['title']) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="body" class="form-label">Body</label>
                            <textarea name="body" id="body" class="form-control quill-editor @error('body') is-invalid @enderror" rows="12">{{ old('body', $content['body'] ?? '') }}</textarea>
                            <small class="text-muted">You can use HTML for formatting.</small>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-admin-primary">
                            @if($isCardEdit) Save Card Image 
                            @elseif($isRetroEdit) Save Retro 
                            @elseif($isGraduateOutcomeAdd) Add Outcome
                            @elseif($isGraduateOutcomeItemEdit) Save Outcome Changes
                            @elseif($isObjectivesSectionEdit) Save Section
                            @elseif($isAddObjectiveEdit) Add Objective
                            @elseif($isEditObjectiveEdit) Save Objective Changes
                            @elseif($isCurriculumEdit) Save Curriculum Changes
                            @elseif($isAddCurriculumEdit) Add Curriculum
                            @elseif($isEditCurriculumEdit) Save Curriculum Changes
                            @elseif($isEditPartnerEdit) Update Partner
                            @elseif($isAddPartnerEdit) Add Partner
                            @elseif($isExtensionEdit && request()->get('action') === 'add') Add Extension
                            @elseif($isExtensionEdit && request()->get('action') === 'edit') Save Extension Changes
                            @elseif($isTrainingEdit && request()->get('action') === 'add') Add Training
                            @elseif($isTrainingEdit && request()->get('action') === 'edit') Save Training Changes
                            @elseif($isAddAlumnusEdit) Add Alumnus
                            @elseif($isEditAlumnusEdit) Save Alumnus Changes
                            @else Save Section 
                            @endif
                        </button>
                        @if($isGraduateOutcomeAdd || $isGraduateOutcomeItemEdit)
                            <a href="{{ route('admin.colleges.show-department', ['college' => $collegeSlug, 'department' => $department, 'section' => 'overview']) }}" class="btn btn-outline-secondary">Back to Department</a>
                        @elseif($isAddObjectiveEdit || $isEditObjectiveEdit)
                            <a href="{{ route('admin.colleges.show-department', ['college' => $collegeSlug, 'department' => $department, 'section' => 'objectives']) }}" class="btn btn-outline-secondary">Back to Objectives</a>
                        @elseif($isAddCurriculumEdit || $isEditCurriculumEdit)
                            <a href="{{ route('admin.colleges.show-department', ['college' => $collegeSlug, 'department' => $department, 'section' => 'objectives']) }}" class="btn btn-outline-secondary">Back to Objectives</a>
                        @elseif($isAddAlumnusEdit || $isEditAlumnusEdit || $isAlumniRosterEdit)
                            <a href="{{ route('admin.colleges.show-department', ['college' => $collegeSlug, 'department' => $department, 'section' => 'alumni']) }}" class="btn btn-outline-secondary">Back to Alumni</a>
                        @else
                            <a href="{{ route('admin.colleges.show-department', ['college' => $collegeSlug, 'department' => $department, 'section' => $sectionSlug]) }}" class="btn btn-outline-secondary">Cancel</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
