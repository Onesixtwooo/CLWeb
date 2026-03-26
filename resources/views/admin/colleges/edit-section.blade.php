@extends('admin.layout')

@section('title', "Edit {$sectionName} - {$collegeName}")

@section('content')
    @php
        $editMode = request()->get('edit'); // 'overview', 'retro', 'featured_video', or null
        $isOverviewEdit = $editMode === 'overview';
        $isRetroEdit = $editMode === 'retro';
        $isFeaturedVideoEdit = $editMode === 'featured_video';
        $isExtensionEdit = $sectionSlug === 'extension';
        $isTrainingEdit = $sectionSlug === 'training';
        $isScholarshipsEdit = $sectionSlug === 'scholarships';
        
        $pageTitle = "Edit section: {$sectionName}";
        if ($isRetroEdit) {
            $pageTitle = !empty($content->id) ? 'Edit Retro Item' : 'Add Retro Item';
        } elseif ($isFeaturedVideoEdit) {
            $pageTitle = 'Edit Featured Video';
        } elseif ($isExtensionEdit) {
             $pageTitle = 'Edit Extension';
        } elseif ($isTrainingEdit) {
             $pageTitle = 'Edit Training';
        } elseif ($isScholarshipsEdit) {
             $pageTitle = 'Edit Scholarships';
        }
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">{{ $pageTitle }}</h1>
        <a href="{{ route('admin.colleges.show', ['college' => $collegeSlug, 'section' => $sectionSlug]) }}" class="btn btn-outline-secondary">Back to {{ $collegeName }}</a>
    </div>

    <p class="text-muted small mb-3">{{ $collegeName }} — {{ $isRetroEdit ? 'Retro Section' : ($isFeaturedVideoEdit ? 'Featured Video' : ($isExtensionEdit ? 'Extension' : ($isTrainingEdit ? 'Training' : ($isScholarshipsEdit ? 'Scholarships' : $sectionName)))) }}</p>

    <div class="admin-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.colleges.update-section', ['college' => $collegeSlug, 'section' => $sectionSlug]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="_edit_mode" value="{{ $editMode }}">
                @if ($isRetroEdit && !empty($content->id))
                     <input type="hidden" name="retro_id" value="{{ $content->id }}">
                @endif
                
                <div class="row g-3">
                    @if (!$isRetroEdit)
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" {{ old('is_visible', $content?->is_visible ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_visible">Visible on Public Page</label>
                            </div>
                            <small class="text-muted">Toggle to show or hide this section on the college page.</small>
                        </div>
                    @endif

                    @if ($isFeaturedVideoEdit)
                        {{-- FEATURED VIDEO ONLY FIELDS --}}
                        <div class="col-12">
                            <label class="form-label">Video Source Type</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="video_type" id="video_type_url" value="url" {{ old('video_type', $content?->video_type ?? 'url') === 'url' ? 'checked' : '' }} onchange="toggleVideoFields()">
                                    <label class="form-check-label" for="video_type_url">Video URL</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="video_type" id="video_type_file" value="file" {{ old('video_type', $content?->video_type ?? '') === 'file' ? 'checked' : '' }} onchange="toggleVideoFields()">
                                    <label class="form-check-label" for="video_type_file">Upload Video File</label>
                                </div>
                            </div>
                            <small class="text-muted">Choose between embedding a video URL (YouTube, Vimeo, etc.) or uploading a video file.</small>
                        </div>
                        
                        <div class="col-12" id="video_url_field">
                            <label for="video_url" class="form-label">Video URL</label>
                            <input type="url" name="video_url" id="video_url" class="form-control @error('video_url') is-invalid @enderror" value="{{ old('video_url', $content?->video_url ?? '') }}" placeholder="https://www.youtube.com/watch?v=...">
                            <small class="text-muted">YouTube, Vimeo, or direct video URL.</small>
                            @error('video_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12" id="video_file_field" style="display: none;">
                            <label for="video_file" class="form-label">Video File</label>
                            @if (!empty($content?->video_file))
                                <div class="mb-2">
                                    <video controls style="max-width: 400px; max-height: 300px;" class="rounded">
                                        <source src="{{ $content->video_file }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    <p class="text-muted small mt-1">Current video: {{ basename($content->video_file) }}</p>
                                </div>
                            @endif
                            <input type="file" name="video_file" id="video_file" class="form-control @error('video_file') is-invalid @enderror" accept="video/*">
                            <small class="text-muted">Upload MP4, WebM, or OGG video file. Leave empty to keep current video.</small>
                            @error('video_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="video_title" class="form-label">Video Title (Optional)</label>
                            <input type="text" name="video_title" id="video_title" class="form-control @error('video_title') is-invalid @enderror" value="{{ old('video_title', $content?->video_title ?? '') }}">
                            @error('video_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="video_description" class="form-label">Video Description (Optional)</label>
                            <textarea name="video_description" id="video_description" class="form-control quill-editor @error('video_description') is-invalid @enderror" rows="3">{{ old('video_description', $content?->video_description ?? '') }}</textarea>
                            @error('video_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <script>
                        function toggleVideoFields() {
                            const type = document.querySelector('input[name="video_type"]:checked').value;
                            document.getElementById('video_url_field').style.display = type === 'url' ? 'block' : 'none';
                            document.getElementById('video_file_field').style.display = type === 'file' ? 'block' : 'none';
                        }
                        
                        // Initialize on page load
                        document.addEventListener('DOMContentLoaded', toggleVideoFields);
                        </script>
                    @elseif ($isRetroEdit)
                        {{-- RETRO ONLY FIELDS --}}
                        <div class="row g-3 col-12 mb-3">
                            <div class="col-md-9">
                                <label for="retro_title" class="form-label">Retro Title</label>
                                <input type="text" name="retro_title" id="retro_title" class="form-control @error('retro_title') is-invalid @enderror" value="{{ old('retro_title', $content?->retro_title ?? ($content?->title ?? '')) }}">
                                <small class="text-muted">Title for the hero overlay section.</small>
                                @error('retro_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="retro_title_size" class="form-label">Title Font Size (px)</label>
                                <input type="number" name="retro_title_size" id="retro_title_size" class="form-control @error('retro_title_size') is-invalid @enderror" value="{{ old('retro_title_size', $content?->retro_title_size ?? ($content?->title_size ?? '')) }}" placeholder="e.g. 48">
                                <small class="text-muted">Unit in pixels. Leave empty for default.</small>
                                @error('retro_title_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label for="retro_description" class="form-label">Retro Description</label>
                            <textarea name="retro_description" id="retro_description" class="form-control quill-editor @error('retro_description') is-invalid @enderror" rows="4">{{ old('retro_description', $content?->retro_description ?? '') }}</textarea>
                            <small class="text-muted">Description for the hero overlay section.</small>
                            @error('retro_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row g-3 col-12 mb-3">
                            <div class="col-md-9">
                                <label for="retro_stamp" class="form-label">Retro Stamp</label>
                                <input type="text" name="retro_stamp" id="retro_stamp" class="form-control @error('retro_stamp') is-invalid @enderror" value="{{ old('retro_stamp', $content?->retro_stamp ?? ($content?->stamp ?? '')) }}">
                                <small class="text-muted">Stamp text for the hero overlay (e.g., year or badge text).</small>
                                @error('retro_stamp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="retro_stamp_size" class="form-label">Stamp Font Size (px)</label>
                                <input type="number" name="retro_stamp_size" id="retro_stamp_size" class="form-control @error('retro_stamp_size') is-invalid @enderror" value="{{ old('retro_stamp_size', $content?->retro_stamp_size ?? ($content?->stamp_size ?? '')) }}" placeholder="e.g. 14">
                                <small class="text-muted">Unit in pixels. Leave empty for default.</small>
                                @error('retro_stamp_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label for="hero_background_image" class="form-label">Hero Background Image</label>
                            @if (!empty($content?->hero_background_image))
                                <div class="mb-2">
                                    <img src="{{ $content->hero_background_image }}" alt="Hero Background" class="img-fluid rounded" style="max-width: 300px; max-height: 200px; object-fit: cover;">
                                </div>
                            @endif
                            <input type="file" name="hero_background_image" id="hero_background_image" class="form-control @error('hero_background_image') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">Upload a background image for the hero overlay. Leave empty to keep current image.</small>
                            @error('hero_background_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @elseif ($isExtensionEdit)
                         {{-- EXTENSION SECTION DETAILS --}}
                        <div class="col-12 mt-2">
                            <div class="mb-4">
                                <label for="title" class="form-label">Section Title</label>
                                <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $content->title ?? ($sectionName ?? 'Extension')) }}" placeholder="Extension">
                                <small class="text-muted">This title appears above the extension activities on the public college page.</small>
                            </div>

                            <div class="mb-4">
                                <label for="body" class="form-label">Section Description</label>
                                <textarea name="body" id="body" class="form-control quill-editor" rows="4" placeholder="Describe this section...">{{ old('body', $content->body ?? '') }}</textarea>
                                <small class="text-muted">This text appears below the title on the public college page.</small>
                            </div>
                        </div>

                    @elseif ($isTrainingEdit)
                        {{-- TRAINING EDITOR --}}
                        <div class="col-12 mt-2">
                            <h5 class="fw-bold mb-3">Training & Workshops</h5>

                            <div class="mb-4">
                                <label for="title" class="form-label">Section Title</label>
                                <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $content->title ?? ($sectionName ?? 'Training & Workshops')) }}" placeholder="Training & Workshops">
                                <small class="text-muted">This title appears above the list of trainings on the public college page.</small>
                            </div>

                            <div class="mb-4">
                                <label for="body" class="form-label">Section Description</label>
                                <textarea name="body" id="body" class="form-control quill-editor" rows="4" placeholder="Describe this section...">{{ old('body', $content->body ?? '') }}</textarea>
                                <small class="text-muted">This description appears below the title on the public college page.</small>
                            </div>

                            </div>
                        </div>

                    @elseif ($isScholarshipsEdit)
                        <div class="col-12">
                            <label for="title" class="form-label">Section Title</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $content->title ?? ($sectionName ?? 'Scholarships')) }}" placeholder="Scholarships">
                            <small class="text-muted">This title appears above the scholarships on the public college page.</small>
                        </div>

                        <div class="col-12">
                            <label for="body" class="form-label">Section Description</label>
                            <textarea name="body" id="body" class="form-control quill-editor" rows="4" placeholder="Describe this section...">{{ old('body', $content->body ?? '') }}</textarea>
                            <small class="text-muted">This description appears below the title on the public college page.</small>
                        </div>
                    @else
                        {{-- DEFAULT EDITOR --}}
                        @if ($sectionSlug === 'accreditation')
                            <div class="col-12">
                                <label for="hero_title" class="form-label">Hero Title</label>
                                <input type="text" name="hero_title" id="hero_title" class="form-control @error('hero_title') is-invalid @enderror"
                                       value="{{ old('hero_title', $content->hero_title ?? 'Commitment to Excellence') }}">
                                <small class="text-muted">The main header displayed at the top of the accreditation page.</small>
                                @error('hero_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="col-12">
                            <label for="title" class="form-label">Section title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $content?->title ?? $defaultTitle) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($sectionSlug === 'overview' && $isOverviewEdit)
                            <div class="col-12">
                                @if (!empty($collegeModel?->icon))
                                    <div id="college-icon-group" class="mb-2">
                                        <label for="icon" class="form-label d-block">College Icon</label>
                                        <div class="position-relative d-inline-block" id="college-icon-wrapper">
                                            @php
                                                preg_match('/[?&]id=([^&]+)/', $collegeModel->icon, $_esIconM);
                                                $_esIconSrc = isset($_esIconM[1])
                                                    ? route('admin.media.proxy', ['fileId' => $_esIconM[1]])
                                                    : $collegeModel->icon;
                                            @endphp
                                            <img src="{{ $_esIconSrc }}" alt="Current icon" style="max-width: 100px; max-height: 100px; object-fit: contain;" class="rounded">
                                            <button type="button" class="btn btn-danger position-absolute top-0 end-0 m-1 rounded-circle d-flex align-items-center justify-content-center delete-college-icon shadow-sm" style="width: 22px; height: 22px; padding: 0; border: 1px solid white;" title="Remove Logo">
                                                <i class="bi bi-x-lg" style="font-size: 11px;"></i>
                                            </button>
                                            <input type="hidden" name="delete_icon" id="delete-icon-input" value="0">
                                        </div>
                                    </div>
                                @else
                                    <label for="icon" class="form-label d-block">College Icon</label>
                                @endif
                                <input type="file" name="icon" id="icon" class="form-control @error('icon') is-invalid @enderror" accept="image/*">
                                <small class="text-muted">Upload a college icon/logo (PNG, JPG, or SVG). Max size: 2MB.</small>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="about_images" class="form-label">About Section Images</label>
                                <p class="text-muted small mb-2">Upload up to 5 images for the About section carousel. These images will be displayed in a scrollable format on the public college page.</p>

                                <!-- Current images preview -->
                                <div id="about-images-preview" class="mb-3">
                                    @php
                                        $currentImages = $collegeModel?->about_images ?? [];
                                        if (!is_array($currentImages)) {
                                            $currentImages = json_decode($currentImages, true) ?? [];
                                        }
                                    @endphp
                                    @if(!empty($currentImages))
                                        <div class="row g-3">
                                            @foreach($currentImages as $index => $imageUrl)
                                                <div class="col-md-4 col-sm-6" data-image-index="{{ $index }}">
                                                    <div class="position-relative">
                                                        @php
                                                            preg_match('/[?&]id=([^&]+)/', $imageUrl, $_aboutImageM);
                                                            $_aboutImageSrc = isset($_aboutImageM[1])
                                                                ? route('admin.media.proxy', ['fileId' => $_aboutImageM[1]])
                                                                : $imageUrl;
                                                        @endphp
                                                        <img src="{{ $_aboutImageSrc }}" alt="About image {{ $index + 1 }}" class="img-fluid rounded shadow-sm" style="width: 100%; height: 150px; object-fit: cover;">
                                                        <button type="button" class="btn btn-danger position-absolute top-0 end-0 m-2 delete-about-image rounded-circle d-flex align-items-center justify-content-center" style="width: 22px; height: 22px; padding: 0; border: 1px solid white;" data-image-index="{{ $index }}" title="Remove Image">
                                                            <i class="bi bi-x-lg" style="font-size: 11px;"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Hidden container for images to delete -->
                                <div id="images-to-delete-container"></div>

                                <!-- File upload -->
                                <input type="file" name="about_images[]" id="about_images" class="form-control @error('about_images') is-invalid @enderror" accept="image/*" multiple>
                                <small class="text-muted">Upload multiple images (PNG, JPG, WebP). Max 5MB each. You can upload up to {{ 5 - count($currentImages) }} more images.</small>
                                @error('about_images')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        @if ($sectionSlug !== 'facilities')
                            <div class="col-12">
                                <label for="body" class="form-label">Details</label>
                                <textarea name="body" id="body" class="form-control quill-editor @error('body') is-invalid @enderror" rows="12" placeholder="Add details for this section. Use new lines for paragraphs.">{{ old('body', $detailsText) }}</textarea>
                                <small class="text-muted">Add details in plain text. Line breaks will be kept when displayed.</small>
                                @error('body')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        @if ($sectionSlug === 'overview' && $isOverviewEdit)
                             <div class="col-12">
                                <hr class="my-4">
                                <h5 class="h6 fw-600 mb-3">Contact Information</h5>
                                <p class="text-muted small">This information is displayed at the top of the college overview page.</p>
                            </div>

                            @php $contact = $content->contact_data ?? null; @endphp

                            <div class="col-md-6">
                                <label for="email" class="form-label">College Email</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                    value="{{ old('email', $contact->email ?? '') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" 
                                    value="{{ old('phone', $contact->phone ?? '') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $contact->address ?? '') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-600">Additional Links</label>
                                <small class="text-muted d-block mb-2">Add up to 2 additional custom links (e.g., Website, Portal).</small>
                                <div id="contact-custom-links-container">
                                    @php
                                        $customLinks = old('custom_links', $contact->custom_links ?? []);
                                        if (!is_array($customLinks)) {
                                            $customLinks = json_decode($customLinks, true) ?? [];
                                        }
                                    @endphp
                                    @foreach($customLinks as $index => $link)
                                        <div class="custom-link-row mb-2" data-index="{{ $index }}">
                                            <div class="input-group">
                                                <input type="url" name="custom_links[]" class="form-control" value="{{ $link }}" placeholder="https://example.com" required>
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-link">×</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-contact-custom-link" {{ count($customLinks) >= 2 ? 'disabled' : '' }}>+ Add Link</button>
                            </div>

                            <div class="col-12 mt-3">
                                <h5 class="h6 fw-600 mb-3">Social Media Links (Footer)</h5>
                                <small class="text-muted d-block mb-3">Add social media links to display in the footer. Leave URL empty to hide the link.</small>
                            </div>

                            <div class="col-md-6">
                                <label for="social_facebook" class="form-label">Facebook URL</label>
                                <input type="url" name="social_facebook" id="social_facebook" class="form-control @error('social_facebook') is-invalid @enderror" value="{{ old('social_facebook', $content?->social_facebook ?? '') }}" placeholder="https://facebook.com/yourpage">
                                @error('social_facebook')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="social_instagram" class="form-label">Instagram URL</label>
                                <input type="url" name="social_instagram" id="social_instagram" class="form-control @error('social_instagram') is-invalid @enderror" value="{{ old('social_instagram', $content?->social_instagram ?? '') }}" placeholder="https://instagram.com/yourprofile">
                                @error('social_instagram')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="social_x" class="form-label">X (Twitter) URL</label>
                                <input type="url" name="social_x" id="social_x" class="form-control @error('social_x') is-invalid @enderror" value="{{ old('social_x', $content?->social_x ?? '') }}" placeholder="https://x.com/yourhandle">
                                @error('social_x')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="social_youtube" class="form-label">YouTube URL</label>
                                <input type="url" name="social_youtube" id="social_youtube" class="form-control @error('social_youtube') is-invalid @enderror" value="{{ old('social_youtube', $content?->social_youtube ?? '') }}" placeholder="https://youtube.com/@yourchannel">
                                @error('social_youtube')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const container = document.getElementById('contact-custom-links-container');
                                const addBtn = document.getElementById('add-contact-custom-link');
                                if (!container || !addBtn) return;

                                const maxLinks = 2;

                                function updateAddButtonState() {
                                    const currentLinks = container.querySelectorAll('.custom-link-row').length;
                                    addBtn.disabled = currentLinks >= maxLinks;
                                }
                                
                                addBtn.addEventListener('click', function() {
                                    const currentLinks = container.querySelectorAll('.custom-link-row').length;
                                    if (currentLinks >= maxLinks) return;

                                    const newRow = document.createElement('div');
                                    newRow.className = 'custom-link-row mb-2';
                                    newRow.innerHTML = `
                                        <div class="input-group">
                                            <input type="url" name="custom_links[]" class="form-control" placeholder="https://example.com" required>
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-link">×</button>
                                        </div>
                                    `;
                                    container.appendChild(newRow);
                                    updateAddButtonState();
                                });
                                
                                container.addEventListener('click', function(e) {
                                    if (e.target.classList.contains('remove-link')) {
                                        e.target.closest('.custom-link-row').remove();
                                        updateAddButtonState();
                                    }
                                });
                            });
                            </script>
                        @elseif ($sectionSlug === 'facilities')
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="body" id="description" class="form-control quill-editor @error('body') is-invalid @enderror" rows="6" placeholder="A brief description for the facilities section header.">{{ old('body', $content?->body ?? '') }}</textarea>
                                <div id="description-help" class="form-text">
                                    A brief description for the facilities section header. Supports rich text formatting.
                                </div>
                                @error('body')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="banner" class="form-label">Banner Image</label>
                                @if (!empty($content?->banner))
                                    <div class="mb-2">
                                        <img src="{{ $content->banner }}" alt="Current banner" class="img-fluid rounded" style="max-height: 200px; width: 100%; object-fit: cover;">
                                    </div>
                                @endif
                                <input type="file" name="banner" id="banner" class="form-control @error('banner') is-invalid @enderror" accept="image/*">
                                <small class="text-muted">Upload a banner image for the facilities section.</small>
                                @error('banner')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    @endif
                    
                    {{-- Draft / Schedule Controls Removed --}}
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-admin-primary">
                            @if ($isRetroEdit)
                                {{ !empty($content->id) ? 'Save Item' : 'Create Item' }}
                            @elseif ($isFeaturedVideoEdit)
                                Save Video
                            @elseif ($isExtensionEdit || $isTrainingEdit || $isScholarshipsEdit)
                                Save {{ $sectionName }}
                            @else
                                Save section
                            @endif
                        </button>
                        <a href="{{ route('admin.colleges.show', ['college' => $collegeSlug, 'section' => $sectionSlug]) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Track images to delete
    let imagesToDelete = [];

    document.addEventListener('DOMContentLoaded', function() {
        updateFileInputMax();

        // Use event delegation for delete buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-about-image')) {
                e.preventDefault();
                const btn = e.target.closest('.delete-about-image');
                removeAboutImage(btn);
            }
        });
    });

    
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-college-icon')) {
                e.preventDefault();
                if (confirm('Are you sure you want to remove the college logo?')) {
                    const input = document.getElementById('delete-icon-input');
                    if (input) input.value = '1';
                    const group = document.getElementById('college-icon-group');
                    if (group) group.style.display = 'none';
                    const wrapper = document.getElementById('college-icon-wrapper');
                    if (wrapper) wrapper.style.display = 'none';
                }
            }
        });

        function removeAboutImage(btn) {
        const imageIndex = btn.getAttribute('data-image-index');
        console.log('Attempting to delete image at index:', imageIndex);
        
        if (confirm('Are you sure you want to remove this image?')) {
            // Track this image for deletion
            if (!imagesToDelete.includes(imageIndex)) {
                imagesToDelete.push(imageIndex);
                console.log('Added to deletion queue. Queue:', imagesToDelete);
            }

            // Remove from preview
            const imageContainer = btn.closest('div[data-image-index]');
            if (imageContainer) {
                imageContainer.remove();
                console.log('DOM element removed');
            }

            // Update the file input max count
            updateFileInputMax();

            // Update hidden inputs for deletion
            updateDeleteContainer();
        }
    }

    function updateDeleteContainer() {
        const container = document.getElementById('images-to-delete-container');
        if (!container) {
            console.error('images-to-delete-container not found!');
            return;
        }
        
        container.innerHTML = ''; // Clear existing inputs

        console.log('Creating hidden inputs for indices:', imagesToDelete);
        
        imagesToDelete.forEach((index, pos) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_about_image[]';
            input.value = index;
            container.appendChild(input);
            console.log(`Created hidden input ${pos}:`, input.name, '=', input.value);
        });
    }

    function updateFileInputMax() {
        const currentImages = document.querySelectorAll('#about-images-preview div[data-image-index]').length;
        const maxAllowed = 5 - currentImages;
        const fileInput = document.getElementById('about_images');
        const helpText = fileInput.nextElementSibling;

        if (helpText && helpText.tagName === 'SMALL') {
            helpText.textContent = `Upload multiple images (PNG, JPG, WebP). Max 5MB each. You can upload up to ${maxAllowed} more images.`;
        }

        // Disable file input if max reached
        fileInput.disabled = maxAllowed <= 0;
    }
    </script>
@endsection
