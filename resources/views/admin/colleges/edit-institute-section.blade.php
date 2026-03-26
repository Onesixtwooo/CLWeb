@extends('admin.layout')

@section('title', "Edit {$sectionName} - {$institute->name}")

@section('content')
    @php
        $editMode = request()->get('edit'); 
        $isOverviewEdit = $editMode === 'overview';
        $isBannerEdit = $editMode === 'banner';
        $isProgramsEdit = $editMode === 'programs' || $sectionSlug === 'programs';
        $isGraduateOutcomesEdit = $editMode === 'graduate_outcomes';
        $isGoalsEdit = $editMode === 'goals' || $sectionSlug === 'goals';
        $isHistoryEdit = $editMode === 'history' || $sectionSlug === 'history';
        $isStaffEdit = $editMode === 'staff' || $sectionSlug === 'staff';
        // Relational
        $isResearchEdit = $editMode === 'research' || $sectionSlug === 'research';
        $isExtensionEdit = $editMode === 'extension' || $sectionSlug === 'extension';
        $isFacilitiesEdit = $editMode === 'facilities' || $sectionSlug === 'facilities';
        
        // Legacy/Generic mapping if needed (e.g. if we still support editing 'objectives' via 'goals' logic, but we prefer 'goals')
        $isObjectivesEdit = $editMode === 'objectives' || $sectionSlug === 'objectives'; 
        // We might fallback to isGoalsEdit logic
        
        $isAwardsEdit = $editMode === 'awards' || $sectionSlug === 'awards';
        $isTrainingEdit = $editMode === 'training' || $sectionSlug === 'training';
        $isAlumniEdit = $editMode === 'alumni' || $sectionSlug === 'alumni';

        $pageTitle = "Edit section: {$sectionName}";
        if ($isBannerEdit) $pageTitle = 'Edit Banner';
        elseif ($isProgramsEdit) $pageTitle = 'Edit Programs';
        elseif ($isGraduateOutcomesEdit) $pageTitle = 'Edit Graduate Outcomes';
        elseif ($isGoalsEdit) $pageTitle = 'Edit Goals';
        elseif ($isHistoryEdit) $pageTitle = 'Edit History';
        elseif ($isStaffEdit) $pageTitle = 'Edit Staff';
        elseif ($isResearchEdit) $pageTitle = 'Edit Research';
        elseif ($isExtensionEdit) $pageTitle = 'Edit Extension';
        elseif ($isFacilitiesEdit) $pageTitle = 'Edit Facilities';
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="admin-page-title mb-0">{{ $pageTitle }}</h1>
            <p class="text-muted small mb-0">{{ $institute->name }} — {{ $collegeName }}</p>
        </div>
        <a href="{{ route('admin.colleges.show-institute', ['college' => $collegeSlug, 'institute' => $institute->id, 'section' => $sectionSlug]) }}" class="btn btn-outline-secondary">Back to Institute</a>
    </div>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.colleges.update-institute', ['college' => $collegeSlug, 'institute' => $institute->id]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="save_inst_section" value="1">
                <input type="hidden" name="section" value="{{ $sectionSlug }}">
                
                @if ($isOverviewEdit)
                    <input type="hidden" name="_overview_edit" value="1">
                @elseif ($isBannerEdit)
                    <input type="hidden" name="_banner_edit" value="1">
                @elseif ($isProgramsEdit)
                    <input type="hidden" name="_programs_edit" value="1">
                @elseif ($isGraduateOutcomesEdit)
                    <input type="hidden" name="_graduate_outcomes_edit" value="1">
                @elseif ($isObjectivesEdit)
                    <input type="hidden" name="_objectives_edit" value="1">
                @elseif ($isAwardsEdit)
                    <input type="hidden" name="_awards_edit" value="1">
                @elseif ($isResearchEdit)
                    <input type="hidden" name="_research_edit" value="1">
                @elseif ($isExtensionEdit)
                    <input type="hidden" name="_extension_edit" value="1">
                @elseif ($isTrainingEdit)
                    <input type="hidden" name="_training_edit" value="1">
                @elseif ($isFacilitiesEdit)
                    <input type="hidden" name="_facilities_edit" value="1">
                @elseif ($isAlumniEdit)
                    <input type="hidden" name="_alumni_edit" value="1">
                @endif
                
                <div class="row g-3">
                    @if ($isBannerEdit)
                        {{-- BANNER ONLY FIELDS --}}
                        <div class="col-12">
                            <label class="form-label">Banner Images (Max 3)</label>
                            
                            @php
                                $bannerImages = $content['banner_images'] ?? [];
                                if (empty($bannerImages) && !empty($content['banner_image'])) {
                                    $bannerImages[] = $content['banner_image'];
                                }
                            @endphp

                            @if (!empty($bannerImages))
                                <div class="row g-3 mb-3">
                                    @foreach($bannerImages as $index => $img)
                                        <div class="col-md-4">
                                            <div class="position-relative">
                                                <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($img) }}" class="img-fluid rounded border" alt="banner">
                                                <button type="submit" name="delete_banner_image" value="{{ $index }}" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" onclick="return confirm('Delete this banner image?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if (count($bannerImages) < 3)
                                <input type="file" name="banner_image" class="form-control" accept="image/*">
                                <small class="text-muted">Choose a banner image (1920x600 recommended). You can add up to 3 images.</small>
                            @else
                                <div class="alert alert-info">Max 3 banner images reached. Delete one to upload a new one.</div>
                            @endif
                        </div>

                    @elseif ($isProgramsEdit)
                        {{-- PROGRAMS EDITOR --}}
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch text-warning">
                                <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($institute->programs_is_visible ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                            </div>
                        </div>

                        <div id="programs-container">
                            @php $programs = $institute->programs ?? [] @endphp
                            @forelse($programs as $index => $program)
                                <div class="card mb-3 program-item">
                                    <div class="card-header d-flex justify-content-between">
                                        <span>Program #{{ $index + 1 }}</span>
                                        <button type="button" class="btn btn-danger btn-sm remove-program-btn px-2">&times;</button>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <div class="mb-2">
                                                    <label class="form-label small fw-bold">Program Title</label>
                                                    <input type="text" name="programs[{{ $index }}][title]" class="form-control form-control-sm" value="{{ $program->title }}" required>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-bold">Description</label>
                                                    <textarea name="programs[{{ $index }}][description]" class="form-control form-control-sm quill-editor" rows="2">{{ $program->description }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Icon/Image</label>
                                                @if($program->image)
                                                    <div class="mb-2">
                                                        <img src="{{ asset($program->image) }}" class="img-thumbnail" style="height: 60px;">
                                                        <input type="hidden" name="programs[{{ $index }}][existing_image]" value="{{ $program->image }}">
                                                    </div>
                                                @endif
                                                <input type="file" name="programs[{{ $index }}][image]" class="form-control form-control-sm" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                            @endforelse
                        </div>
                        <button type="button" id="add-program-btn" class="btn btn-outline-primary btn-sm mt-2">+ Add Program</button>

                    @elseif ($isHistoryEdit)
                        {{-- HISTORY EDITOR --}}
                        <div class="col-12">
                            <label class="form-label">History</label>
                            <textarea name="history" class="form-control quill-editor" rows="10">{{ old('history', $content['history'] ?? '') }}</textarea>
                        </div>

                    @elseif ($isGoalsEdit)
                        {{-- GOALS EDITOR --}}
                        <div class="col-12 mb-4">
                            <h5 class="fw-bold mb-3 border-bottom pb-2">Goals</h5>
                            <div id="goals-container">
                                @forelse($goals as $index => $goal)
                                    <div class="input-group mb-2 goal-item">
                                        <span class="input-group-text small">{{ $index + 1 }}</span>
                                        <input type="text" name="goals[]" class="form-control" value="{{ $goal->content }}" placeholder="Enter goal...">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-goal-btn px-3">&times;</button>
                                    </div>
                                @empty
                                    {{-- Empty state or existing objectives fallback if migration didn't happen yet --}}
                                @endforelse
                            </div>
                            <button type="button" id="add-goal-btn" class="btn btn-outline-primary btn-sm mt-2">+ Add Goal</button>
                        </div>

                    @elseif ($isStaffEdit)
                        {{-- STAFF EDITOR (LEGACY BULK MANAGEMENT REMOVED) --}}
                        <div class="col-12 mb-3">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Staff members are now managed individually within the <strong>Faculty Roster</strong>.
                                <br><br>
                                <a href="{{ route('admin.colleges.show', ['college' => $collegeSlug, 'section' => 'faculty']) }}" class="btn btn-sm btn-primary">
                                    Go to Faculty Roster
                                </a>
                            </div>
                        </div>

                    @elseif ($isAwardsEdit || $isResearchEdit || $isExtensionEdit || $isTrainingEdit || $isFacilitiesEdit || $isAlumniEdit)
                        {{-- RELATIONAL SECTIONS EDITOR --}}
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch text-warning">
                                <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ ($content['is_visible'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                            </div>
                        </div>

                        <div id="items-container">
                            @php 
                                $items = $content['items'] ?? [];
                                if ($isResearchEdit) $items = $research ?? [];
                                elseif ($isExtensionEdit) $items = $extension ?? [];
                                elseif ($isFacilitiesEdit) $items = $facilities ?? [];
                            @endphp
                            @forelse($items as $index => $item)
                                <div class="card mb-3 item-node">
                                    <div class="card-header d-flex justify-content-between">
                                        <span>Item #{{ $index + 1 }}</span>
                                        <button type="button" class="btn btn-danger btn-sm remove-item-btn px-2">&times;</button>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <div class="mb-2">
                                                    <label class="form-label small fw-bold">Title</label>
                                                    <input type="text" name="items[{{ $index }}][title]" class="form-control form-control-sm" value="{{ $item['title'] ?? $item->title ?? '' }}" required>
                                                </div>
                                                @if($isAlumniEdit)
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Year Graduated/Batch</label>
                                                        <input type="text" name="items[{{ $index }}][year_graduated]" class="form-control form-control-sm" value="{{ $item['year_graduated'] ?? '' }}">
                                                    </div>
                                                @endif
                                                <div class="mb-0">
                                                    <label class="form-label small fw-bold">Description</label>
                                                    <textarea name="items[{{ $index }}][description]" class="form-control form-control-sm quill-editor" rows="2">{{ $item['description'] ?? $item->description ?? '' }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Featured Image</label>
                                                @php $img = $item['image'] ?? $item->image ?? ''; @endphp
                                                @if(!empty($img))
                                                    <div class="mb-2">
                                                        <img src="{{ asset($img) }}" class="img-thumbnail" style="height: 60px;">
                                                        <input type="hidden" name="items[{{ $index }}][existing_image]" value="{{ $img }}">
                                                    </div>
                                                @endif
                                                <input type="file" name="items[{{ $index }}][image]" class="form-control form-control-sm" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                            @endforelse
                        </div>
                        <button type="button" id="add-item-btn" class="btn btn-outline-primary btn-sm mt-2">+ Add New Item</button>

                    @else
                        {{-- OVERVIEW FIELDS --}}
                        <div class="col-12">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $content['title']) }}" required>
                        </div>

                        <div class="col-12">
                            <label for="body" class="form-label">Overview Body</label>
                            <textarea name="body" id="body" class="form-control quill-editor" rows="8">{{ old('body', $content['body'] ?? '') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="logo" class="form-label">Institute Logo</label>
                            @if(!empty($institute->logo))
                                <div class="mb-2">
                                    <img src="{{ asset($institute->logo) }}" class="img-thumbnail" style="height: 100px;">
                                </div>
                            @endif
                            <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                            <small class="text-muted">Recommended: Square image, transparent background.</small>
                        </div>

                        <div class="col-md-6">
                            <label for="card_image" class="form-label">Card Image</label>
                            @php
                                $cardImage = $content['card_image'] ?? $institute->card_image;
                            @endphp
                            @if(!empty($cardImage))
                                <div class="mb-2">
                                    <img src="{{ asset($cardImage) }}" class="img-thumbnail" style="height: 100px;">
                                </div>
                            @endif
                            <input type="file" name="card_image" id="card_image" class="form-control" accept="image/*">
                            <small class="text-muted">Displayed on the college portal and institute list cards.</small>
                        </div>

                        <div class="col-12">
                            <hr class="my-4">
                            <h5 class="h6 fw-bold">Banner Images (Max 3)</h5>
                            @php
                                $bannerImages = $content['banner_images'] ?? [];
                                if (empty($bannerImages) && !empty($content['banner_image'])) {
                                    $bannerImages[] = $content['banner_image'];
                                }
                            @endphp

                            @if (!empty($bannerImages))
                                <div class="row g-3 mb-3">
                                    @foreach($bannerImages as $index => $img)
                                        <div class="col-md-4">
                                            <div class="position-relative">
                                                <img src="{{ asset($img) }}" class="img-fluid rounded border" alt="banner">
                                                <button type="submit" name="delete_banner_image" value="{{ $index }}" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" onclick="return confirm('Delete this banner image?')">
                                                    <i class="bi bi-trash">Delete</i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if (count($bannerImages) < 3)
                                <input type="file" name="banner_image" class="form-control" accept="image/*">
                                <small class="text-muted">Choose a banner image (1920x600 recommended). You can add up to 3 images.</small>
                            @else
                                <div class="alert alert-info py-2 small">Max 3 banner images reached. Delete one to upload a new one.</div>
                            @endif
                        </div>

                        <div class="col-12">
                            <hr class="my-4">
                            <h5 class="h6 fw-bold">Contact & Social Info</h5>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $content['email'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $content['phone'] ?? '') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Facebook URL</label>
                            <input type="url" name="social_facebook" class="form-control" value="{{ $content['social_facebook'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">X/Twitter URL</label>
                            <input type="url" name="social_x" class="form-control" value="{{ $content['social_x'] ?? '' }}">
                        </div>

                        @if ($isGraduateOutcomesEdit)
                            <div class="col-12 mt-3">
                                <label class="form-label">Graduate Outcomes Description</label>
                                <textarea name="graduate_outcomes" class="form-control quill-editor" rows="5">{{ $institute->graduate_outcomes }}</textarea>
                            </div>
                        @elseif ($editMode === 'program_description')
                            <div class="col-12 mt-3">
                                <label class="form-label">Program Description</label>
                                <textarea name="program_description" class="form-control quill-editor" rows="8">{{ $institute->program_description }}</textarea>
                            </div>
                        @endif
                    @endif
                    
                    <div class="col-12 pt-3 border-top mt-4">
                        <button type="submit" class="btn btn-admin-primary px-4 py-2">Save Changes</button>
                        <a href="{{ route('admin.colleges.show-institute', ['college' => $collegeSlug, 'institute' => $institute->id, 'section' => $sectionSlug]) }}" class="btn btn-outline-secondary px-4 py-2 ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Programs
        const programsContainer = document.getElementById('programs-container');
        if (programsContainer) {
            let pIndex = programsContainer.querySelectorAll('.program-item').length;
            document.getElementById('add-program-btn').addEventListener('click', function() {
                const html = `
                    <div class="card mb-3 program-item">
                        <div class="card-header d-flex justify-content-between">
                            <span>New Program</span>
                            <button type="button" class="btn btn-danger btn-sm remove-program-btn px-2">&times;</button>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Program Title</label>
                                        <input type="text" name="programs[${pIndex}][title]" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label small fw-bold">Description</label>
                                        <textarea name="programs[${pIndex}][description]" class="form-control form-control-sm quill-editor" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Icon/Image</label>
                                    <input type="file" name="programs[${pIndex}][image]" class="form-control form-control-sm" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>`;
                programsContainer.insertAdjacentHTML('beforeend', html);
                pIndex++;
            });
            programsContainer.addEventListener('click', e => {
                if (e.target.classList.contains('remove-program-btn')) e.target.closest('.program-item').remove();
            });
        }

        // Generic Items (Awards, etc.)
        const itemsContainer = document.getElementById('items-container');
        if (itemsContainer) {
            let iIndex = itemsContainer.querySelectorAll('.item-node').length;
            document.getElementById('add-item-btn').addEventListener('click', function() {
                const html = `
                    <div class="card mb-3 item-node">
                        <div class="card-header d-flex justify-content-between">
                            <span>New Item</span>
                            <button type="button" class="btn btn-danger btn-sm remove-item-btn px-2">&times;</button>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Title</label>
                                        <input type="text" name="items[${iIndex}][title]" class="form-control form-control-sm" required>
                                    </div>
                                    @if($isAlumniEdit)
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold">Year Graduated/Batch</label>
                                            <input type="text" name="items[${iIndex}][year_graduated]" class="form-control form-control-sm">
                                        </div>
                                    @endif
                                    <div class="mb-0">
                                        <label class="form-label small fw-bold">Description</label>
                                        <textarea name="items[${iIndex}][description]" class="form-control form-control-sm quill-editor" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Featured Image</label>
                                    <input type="file" name="items[${iIndex}][image]" class="form-control form-control-sm" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>`;
                itemsContainer.insertAdjacentHTML('beforeend', html);
                iIndex++;
            });
            itemsContainer.addEventListener('click', e => {
                if (e.target.classList.contains('remove-item-btn')) e.target.closest('.item-node').remove();
            });
        }

        // Goals
        const goalsContainer = document.getElementById('goals-container');
        if (goalsContainer) {
            document.getElementById('add-goal-btn').addEventListener('click', function() {
                const count = goalsContainer.querySelectorAll('.goal-item').length + 1;
                const html = `
                    <div class="input-group mb-2 goal-item">
                        <span class="input-group-text small">${count}</span>
                        <input type="text" name="goals[]" class="form-control" placeholder="Enter goal...">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-goal-btn px-3">&times;</button>
                    </div>`;
                goalsContainer.insertAdjacentHTML('beforeend', html);
            });
            goalsContainer.addEventListener('click', e => {
                if (e.target.classList.contains('remove-goal-btn')) e.target.closest('.goal-item').remove();
            });
        }

        // Staff
        const staffContainer = document.getElementById('staff-container');
        if (staffContainer) {
            let sIndex = staffContainer.querySelectorAll('.staff-item').length;
            document.getElementById('add-staff-btn').addEventListener('click', function() {
                const html = `
                    <div class="card mb-3 staff-item">
                        <div class="card-header d-flex justify-content-between">
                            <span>New Staff</span>
                            <button type="button" class="btn btn-danger btn-sm remove-staff-btn px-2">&times;</button>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Name</label>
                                        <input type="text" name="staff[${sIndex}][name]" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label small fw-bold">Position</label>
                                        <input type="text" name="staff[${sIndex}][position]" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Photo</label>
                                    <input type="file" name="staff[${sIndex}][photo]" class="form-control form-control-sm" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>`;
                staffContainer.insertAdjacentHTML('beforeend', html);
                sIndex++;
            });
            staffContainer.addEventListener('click', e => {
                if (e.target.classList.contains('remove-staff-btn')) e.target.closest('.staff-item').remove();
            });
        }
    });
</script>
@endpush
