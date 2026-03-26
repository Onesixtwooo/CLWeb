<?php
$filePath = 'd:/htdocs/CLSU/resources/views/admin/colleges/edit-department-section.blade.php';
$content = file_get_contents($filePath);
if ($content === false) {
    die("Could not read file");
}

// Target the entire old bulk alumni section
$startMarker = '@elseif ($isAlumniRosterEdit)';
$endMarker = '@elseif ($isLinkagesEdit)';

$startPos = strpos($content, $startMarker);
$endPos = strpos($content, $endMarker);

if ($startPos !== false && $endPos !== false) {
    $alumniSections = '                    @elseif ($isAlumniRosterEdit)
                        {{-- ALUMNI ROSTER MANAGER --}}
                        <div class="col-12">
                            <h4 class="fw-bold mb-3">Alumni Roster</h4>
                            <p class="text-muted small">Manage the list of notable alumni for this department.</p>
                            
                            @php
                                $alumni = $content[\'items\'] ?? [];
                            @endphp

                            <div class="table-responsive">
                                <table class="table table-hover align-middle border">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 80px;">Photo</th>
                                            <th>Name</th>
                                            <th>Batch/Year</th>
                                            <th>Description</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($alumni as $index => $item)
                                            <tr>
                                                <td>
                                                    @if(!empty($item[\'image\']))
                                                        <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($item[\'image\']) }}" alt="Photo" class="rounded-circle shadow-sm" style="width: 45px; height: 45px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border shadow-sm" style="width: 45px; height: 45px;">
                                                            <i class="fas fa-user text-muted small"></i>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="fw-bold text-dark">{{ $item[\'title\'] ?? $item[\'name\'] ?? \'Unnamed\' }}</td>
                                                <td><span class="badge bg-admin-soft text-admin-primary px-3 rounded-pill">{{ $item[\'year_graduated\'] ?? $item[\'year\'] ?? $item[\'batch\'] ?? \'N/A\' }}</span></td>
                                                <td><div class="text-truncate" style="max-width: 250px;">{!! strip_tags($item[\'description\'] ?? \'\') !!}</div></td>
                                                <td class="text-end">
                                                    <div class="d-flex gap-2 justify-content-end">
                                                        <a href="{{ route(\'admin.colleges.edit-department-section\', [\'college\' => $collegeSlug, \'department\' => $department->id, \'section\' => \'alumni\']) }}?edit=edit_alumnus&alumnus_id={{ $item[\'id\'] ?? $index }}" class="btn btn-sm btn-outline-primary shadow-sm" title="Edit Profile">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route(\'admin.colleges.destroy-alumnus\', [\'college\' => $collegeSlug, \'department\' => $department->id, \'alumnus\' => $item[\'id\'] ?? $index]) }}" method="POST" onsubmit="return confirm(\'Are you sure you want to remove this alumni profile?\');" class="d-inline">
                                                            @csrf
                                                            @method(\'DELETE\')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm" title="Remove">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    <i class="fas fa-user-graduate fa-3x mb-3 opacity-25"></i>
                                                    <p class="mb-0">No alumni records found.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route(\'admin.colleges.edit-department-section\', [\'college\' => $collegeSlug, \'department\' => $department->id, \'section\' => \'alumni\']) }}?edit=add_alumnus" class="btn btn-primary shadow-sm rounded-pill px-4">
                                    <i class="fas fa-plus me-2"></i> Add New Alumni Profile
                                </a>
                            </div>
                        </div>

                    @elseif ($isAddAlumnusEdit)
                        {{-- ADD ALUMNUS FORM --}}
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0 fw-bold">Add New Alumni Profile</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <div class="col-md-9">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control form-control-lg border-primary-subtle" placeholder="e.g., John Doe" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Year Graduated / Batch <span class="text-danger">*</span></label>
                                                        <input type="text" name="year" class="form-control form-control-lg border-primary-subtle" placeholder="e.g., 2020" required>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Description / Achievement <span class="text-danger">*</span></label>
                                                        <textarea name="description" class="form-control quill-editor" rows="6" placeholder="Share a brief bio or description of achievements..."></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3 text-center">
                                                <label class="form-label fw-bold d-block text-start">Profile Photo</label>
                                                <div class="mb-3 mx-auto border rounded bg-light d-flex align-items-center justify-content-center overflow-hidden" style="width: 200px; height: 200px;">
                                                    <i class="fas fa-camera fa-3x text-muted opacity-25"></i>
                                                </div>
                                                <input type="file" name="image" class="form-control" accept="image/*">
                                                <p class="small text-muted mt-2">Recommended: Square aspect ratio (e.g., 400x400px)</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @elseif ($isEditAlumnusEdit)
                        {{-- EDIT ALUMNUS FORM --}}
                        @php
                            $targetAlumnus = null;
                            $alumniItems = $content[\'items\'] ?? [];
                            $alumnusId = request()->get(\'alumnus_id\');
                            foreach($alumniItems as $item) {
                                if (($item[\'id\'] ?? \'\') == $alumnusId) {
                                    $targetAlumnus = $item;
                                    break;
                                }
                            }
                        @endphp

                        @if($targetAlumnus)
                            <div class="col-12">
                                <div class="card border shadow-sm">
                                    <div class="card-header bg-admin-primary text-white">
                                        <h5 class="mb-0 fw-bold">Edit Alumni: {{ $targetAlumnus[\'title\'] ?? $targetAlumnus[\'name\'] ?? \'Profile\' }}</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="row g-4">
                                            <div class="col-md-9">
                                                <div class="row g-3">
                                                    <div class="col-md-8">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-admin-primary">Full Name <span class="text-danger">*</span></label>
                                                            <input type="text" name="name" class="form-control border-admin-soft" value="{{ $targetAlumnus[\'title\'] ?? $targetAlumnus[\'name\'] ?? \'\' }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-admin-primary">Year Graduated / Batch <span class="text-danger">*</span></label>
                                                            <input type="text" name="year" class="form-control border-admin-soft" value="{{ $targetAlumnus[\'year_graduated\'] ?? $targetAlumnus[\'year\'] ?? ($targetAlumnus[\'batch\'] ?? \'\') }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-admin-primary">Description / Achievement <span class="text-danger">*</span></label>
                                                            <textarea name="description" class="form-control quill-editor" rows="6">{{ $targetAlumnus[\'description\'] ?? \'\' }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3 text-center">
                                                    <label class="form-label fw-bold d-block text-start text-admin-primary">Profile Photo</label>
                                                    @if(!empty($targetAlumnus[\'image\']))
                                                        <div class="mb-3 mx-auto border rounded overflow-hidden shadow-sm" style="width: 200px; height: 200px;">
                                                            <img src="{{ \App\Providers\AppServiceProvider::resolveImageUrl($targetAlumnus[\'image\']) }}" alt="Current Photo" style="width: 100%; height: 100%; object-fit: cover;">
                                                            <input type="hidden" name="existing_image" value="{{ $targetAlumnus[\'image\'] }}">
                                                        </div>
                                                        <p class="small text-muted">Current profile photo</p>
                                                    @else
                                                        <div class="mb-3 mx-auto border rounded bg-light d-flex align-items-center justify-content-center overflow-hidden" style="width: 200px; height: 200px;">
                                                            <i class="fas fa-camera fa-3x text-muted opacity-25"></i>
                                                        </div>
                                                    @endif
                                                    <input type="file" name="image" class="form-control border-admin-soft" accept="image/*">
                                                    <small class="text-muted d-block mt-2">Upload to replace photo</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-12 text-center py-5">
                                <div class="alert alert-warning d-inline-block px-5">
                                    <i class="fas fa-exclamation-triangle me-2"></i> Alumni record not found.
                                    <br><br>
                                    <a href="{{ route(\'admin.colleges.edit-department-section\', [\'college\' => $collegeSlug, \'department\' => $department->id, \'section\' => \'alumni\']) }}?edit=alumni_roster" class="btn btn-outline-warning">Back to Roster</a>
                                </div>
                            </div>
                        @endif
';
    
    $newContent = substr($content, 0, $startPos) . $alumniSections . substr($content, $endPos);
    file_put_contents($filePath, $newContent);
    echo "Successfully replaced bulk alumni editor with singular record manager.";
} else {
    echo "Could not find start or end markers. Start: " . ($startPos !== false ? "found" : "not found") . ", End: " . ($endPos !== false ? "found" : "not found");
}
