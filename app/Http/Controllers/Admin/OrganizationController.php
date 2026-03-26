<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollegeOrganization;
use App\Models\CollegeDepartment;
use App\Http\Controllers\Admin\CollegeController;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Faculty;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class OrganizationController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.colleges.index');
    }

    public function createForDepartment(Request $request, string $college, string $department): View|RedirectResponse
    {
        $departmentModel = CollegeDepartment::findByCollegeAndRouteKey($college, $department);
        if (! $departmentModel) {
            abort(404, 'Department not found.');
        }

        $request->query->set('college', $college);
        $request->query->set('department', (string) $departmentModel->id);

        return $this->create($request);
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $colleges = $user->isSuperAdmin() ? CollegeController::getColleges() : [];
        $collegeSlug = $request->query('college');
        $fromCollegeSection = (string) $collegeSlug !== '';
        $selectedDepartmentId = $request->query('department');
        $fromDepartmentSection = false;
        
        if ($fromCollegeSection && $user->isBoundedToCollege()) {
            $collegeSlug = $user->college_slug;
        }

        $departments = [];
        if ($collegeSlug) {
            $departments = CollegeDepartment::where('college_slug', $collegeSlug)->orderBy('name')->get();
        }

        if ($user->isBoundedToDepartment()) {
            $collegeSlug = $user->college_slug;
            $selectedDepartmentId = $user->getDepartmentId($collegeSlug);
            $fromCollegeSection = true;
            $fromDepartmentSection = true;
            $departments = CollegeDepartment::where('college_slug', $collegeSlug)->orderBy('name')->get();
        } elseif ($selectedDepartmentId) {
            $fromDepartmentSection = true;
        }

        return view('admin.organizations.create', compact(
            'colleges',
            'collegeSlug',
            'fromCollegeSection',
            'fromDepartmentSection',
            'departments',
            'selectedDepartmentId'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'acronym' => ['nullable', 'string', 'max:50', 'unique:college_organizations,acronym'],
            'department_id' => ['nullable', 'exists:college_departments,id'],
            'adviser' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'media_image' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'college_slug' => ['nullable', 'string', 'max:80'],
            'is_visible' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $returnCollege = $request->input('return_college');

        if ($user->isBoundedToCollege()) {
            $data['college_slug'] = $user->college_slug;
        } elseif (empty($data['college_slug']) && $returnCollege) {
            $data['college_slug'] = $returnCollege;
        }

        if ($user->isBoundedToDepartment()) {
            $data['department_id'] = $user->getDepartmentId($user->college_slug);
        }

        if (! empty($data['department_id'])) {
            $department = CollegeDepartment::find($data['department_id']);
            if (! $department || $department->college_slug !== ($data['college_slug'] ?? null)) {
                return back()->withErrors(['department_id' => 'Selected department does not belong to the chosen college.'])->withInput();
            }
        }

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_visible'] = $request->has('is_visible');

        if ($request->hasFile('logo')) {
            $file       = $request->file('logo');
            $folderName = ! empty($data['acronym'])
                ? Str::upper(Str::slug($data['acronym']))
                : Str::slug($data['name']);
            $filename   = time() . '_logo.' . $file->getClientOriginalExtension();
            $collegeLogoPath = $data['college_slug'] ?? 'global';
            $path       = Storage::disk('google')->putFileAs("colleges/{$collegeLogoPath}/student-organization/{$folderName}/logos", $file, $filename);
            $data['logo'] = Storage::disk('google')->url($path);
        } elseif ($request->filled('media_image')) {
            $data['logo'] = $request->input('media_image');
        }

        CollegeOrganization::create($data);

        if ($returnCollege) {
            if (! empty($data['department_id'])) {
                $departmentModel = CollegeDepartment::find($data['department_id']);
                if ($departmentModel) {
                    return redirect()->route('admin.colleges.show-department', [
                        'college' => $returnCollege,
                        'department' => $departmentModel,
                        'section' => 'organizations',
                    ])->with('success', 'Student organization added successfully.');
                }
            }

            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'organizations'])
                ->with('success', 'Student organization added successfully.');
        }

        return redirect()->route('admin.colleges.index')->with('success', 'Student organization added successfully.');
    }

    public function showLegacy(Request $request, CollegeOrganization $organization): RedirectResponse
    {
        $this->authorizeCollege($organization->college_slug);

        $query = $request->query();
        unset($query['college']);

        $url = route('admin.organizations.show', [
            'college' => $organization->college_slug,
            'organization' => $organization,
        ]);

        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }

        return redirect($url, 301);
    }

    public function show(Request $request, string $college, CollegeOrganization $organization): View|RedirectResponse
    {
        $this->authorizeCollege($organization->college_slug);

        if ($organization->college_slug !== $college) {
            abort(404, 'Organization not found in this college.');
        }

        if ($request->query('section') === 'gallery' && $request->filled('album')) {
            $gallerySection = $organization->getSection('gallery') ?? [];
            $galleryItems = $gallerySection['items'] ?? [];
            $albumIndex = (int) $request->query('album');

            if (! isset($galleryItems[$albumIndex])) {
                abort(404, 'Album not found.');
            }

            return redirect()->route('admin.organizations.gallery-album', [
                'college' => $college,
                'organization' => $organization,
                'album' => $this->getGalleryAlbumRouteKey($galleryItems, $albumIndex),
            ]);
        }

        if ($request->filled('section') && ! $request->routeIs('admin.organizations.show-section')) {
            return redirect()->route('admin.organizations.show-section', [
                'college' => $college,
                'organization' => $organization,
                'section' => $request->query('section'),
            ], 301);
        }

        $colleges = CollegeController::getColleges();
        $collegeSlug = $organization->college_slug;
        $collegeName = $colleges[$collegeSlug] ?? $collegeSlug;

        $sections = $this->getSectionsMetadata($organization);

        $currentSection = $request->query('section', 'overview');
        if (! isset($sections[$currentSection])) {
            $currentSection = 'overview';
        }

        // Load section content stored in organization's sections JSON column (if any)
        $sectionContent = [
            'title' => $sections[$currentSection],
            'body'  => '',
        ];
        
        $stored = $organization->getSection($currentSection);
        if ($stored) {
            $sectionContent = array_merge($sectionContent, $stored);
        }

        $activitiesPagination = null;
        if ($currentSection === 'activities') {
            $allItems = collect($sectionContent['items'] ?? [])->values();
            $perPage = 5;
            $currentPage = max(1, (int) $request->query('page', 1));
            $pagedItems = $allItems->slice(($currentPage - 1) * $perPage, $perPage)->values();

            $sectionContent['items'] = $pagedItems
                ->map(function ($item, $localIndex) use ($currentPage, $perPage) {
                    $item['__index'] = (($currentPage - 1) * $perPage) + $localIndex;
                    return $item;
                })
                ->all();

            $activitiesPagination = new LengthAwarePaginator(
                $pagedItems,
                $allItems->count(),
                $perPage,
                $currentPage,
                [
                    'path' => $request->url(),
                    'pageName' => 'page',
                    'query' => $request->query(),
                ]
            );
        }

        $faculty = $this->getAdviserFaculty($organization);

        return view('admin.organizations.show', [
            'organization'   => $organization,
            'collegeSlug'    => $collegeSlug,
            'collegeName'    => $collegeName,
            'sections'       => $sections,
            'currentSection' => $currentSection,
            'sectionContent' => $sectionContent,
            'faculty'        => $faculty,
            'activeAlbumIndex' => null,
            'activitiesPagination' => $activitiesPagination,
        ]);
    }

    public function showSection(Request $request, string $college, CollegeOrganization $organization, string $section): View|RedirectResponse
    {
        $query = $request->query();
        $query['section'] = $section;
        $request->query->replace($query);

        return $this->show($request, $college, $organization);
    }

    public function showGalleryAlbum(Request $request, string $college, CollegeOrganization $organization, string $album): View|RedirectResponse
    {
        $this->authorizeCollege($organization->college_slug);

        if ($organization->college_slug !== $college) {
            abort(404, 'Organization not found in this college.');
        }

        $colleges = CollegeController::getColleges();
        $collegeSlug = $organization->college_slug;
        $collegeName = $colleges[$collegeSlug] ?? $collegeSlug;
        $sections = $this->getSectionsMetadata($organization);

        $sectionContent = [
            'title' => $sections['gallery'],
            'body' => '',
        ];

        $stored = $organization->getSection('gallery');
        if ($stored) {
            $sectionContent = array_merge($sectionContent, $stored);
        }

        $items = $sectionContent['items'] ?? [];
        $albumIndex = $this->resolveGalleryAlbumIndex($items, $album);

        if ($albumIndex === null || ! isset($items[$albumIndex])) {
            abort(404, 'Album not found.');
        }

        $canonicalAlbum = $this->getGalleryAlbumRouteKey($items, $albumIndex);
        if ($album !== $canonicalAlbum) {
            return redirect()->route('admin.organizations.gallery-album', [
                'college' => $college,
                'organization' => $organization,
                'album' => $canonicalAlbum,
            ], 301);
        }

        $faculty = $this->getAdviserFaculty($organization);

        return view('admin.organizations.show', [
            'organization' => $organization,
            'collegeSlug' => $collegeSlug,
            'collegeName' => $collegeName,
            'sections' => $sections,
            'currentSection' => 'gallery',
            'sectionContent' => $sectionContent,
            'faculty' => $faculty,
            'activeAlbumIndex' => $albumIndex,
        ]);
    }

    public function editSection(Request $request, CollegeOrganization $organization, string $section): View
    {
        $this->authorizeCollege($organization->college_slug);
        
        $colleges = CollegeController::getColleges();
        $collegeSlug = $organization->college_slug;
        $collegeName = $colleges[$collegeSlug] ?? $collegeSlug;

        $sections = $this->getSectionsMetadata($organization);

        if (! isset($sections[$section])) {
            abort(404, 'Section not found.');
        }

        $content = $organization->getSection($section) ?? [
            'title' => $sections[$section],
            'body'  => '',
        ];

        $faculty = $this->getAdviserFaculty($organization);
            
        return view('admin.organizations.edit-section', [
            'organization'   => $organization,
            'collegeSlug'    => $collegeSlug,
            'collegeName'    => $collegeName,
            'sectionSlug'    => $section,
            'sectionName'    => $sections[$section],
            'content'        => $content,
            'faculty'        => $faculty,
        ]);
    }

    public function updateSection(Request $request, CollegeOrganization $organization, string $section): RedirectResponse
    {
        $this->authorizeCollege($organization->college_slug);

        $sections = $this->getSectionsMetadata($organization);

        if (! isset($sections[$section])) {
            abort(404, 'Section not found.');
        }

        $data = $request->all();
        $existing = $organization->getSection($section) ?? [];
        $sectionData = array_merge($existing, [
            'title'  => $data['title'] ?? ($existing['title'] ?? $sections[$section]),
            'layout' => $data['layout'] ?? ($existing['layout'] ?? 'grid'),
        ]);

        // Handle diverse data based on layout
        if (isset($data['body'])) {
            $sectionData['body'] = $data['body'];
        } elseif (isset($data['body_alt'])) {
            $sectionData['body'] = $data['body_alt'];
        }

        if ($request->hasFile('image_upload')) {
            $sectionData['image'] = $this->uploadSectionItemImage($organization, $section, $request->file('image_upload'));
        } elseif (isset($data['image'])) {
            $sectionData['image'] = $data['image'];
        }

        if (isset($data['items'])) {
            $sectionData['items'] = $data['items'];
        }
        
        if ($section === 'overview') {
            // Sync with main description if in overview? 
            // Actually, keep them separate or just use the main one.
            // The user wants to add officers and adviser.
        } elseif ($section === 'officers') {
            if ($request->has('adviser')) {
                $organization->adviser = $request->input('adviser');
            }
        } elseif ($section === 'activities') {
            $sectionData['items'] = $data['items'] ?? [];
        } elseif ($section === 'gallery') {
            $sectionData['items'] = $data['items'] ?? [];
        } else {
            // Custom sections use simple body
            $sectionData['body'] = $data['body'] ?? '';
        }

        $organization->setSection($section, $sectionData);
        $organization->save();

        return redirect()->route('admin.organizations.show-section', [
            'college' => $organization->college_slug,
            'organization' => $organization,
            'section' => $section,
        ])->with('success', "{$sections[$section]} section updated successfully.");
    }

    public function edit(Request $request, CollegeOrganization $organization): View|RedirectResponse
    {
        $this->authorizeCollege($organization->college_slug);

        if ($request->filled('return_college') && ! $request->routeIs('admin.organizations.edit-scoped')) {
            return redirect()->route('admin.organizations.edit-scoped', [
                'college' => $request->input('return_college'),
                'organization' => $organization,
            ], 301);
        }

        $user = $request->user();
        $colleges = $user->isSuperAdmin() ? CollegeController::getColleges() : [];
        $returnCollege = $organization->college_slug;

        $collegeSlug = $organization->college_slug;
        $departments = CollegeDepartment::where('college_slug', $collegeSlug)->orderBy('name')->get();
        $backUrl = $this->resolveEditReturnUrl($request, $organization);

        $request->session()->put($this->getEditReturnSessionKey($organization), $backUrl);

        return view('admin.organizations.edit', compact('organization', 'colleges', 'returnCollege', 'departments', 'backUrl'));
    }

    public function editScoped(Request $request, string $college, CollegeOrganization $organization): View|RedirectResponse
    {
        if ($organization->college_slug !== $college) {
            abort(404, 'Organization not found in this college.');
        }

        return $this->edit($request, $organization);
    }

    public function update(Request $request, CollegeOrganization $organization): RedirectResponse
    {
        $this->authorizeCollege($organization->college_slug);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'acronym' => ['nullable', 'string', 'max:50', 'unique:college_organizations,acronym,' . $organization->id],
            'department_id' => ['nullable', 'exists:college_departments,id'],
            'adviser' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'media_image' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_visible' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();

        if ($user->isBoundedToDepartment()) {
            $data['department_id'] = $user->getDepartmentId($user->college_slug);
        }

        if (! empty($data['department_id'])) {
            $department = CollegeDepartment::find($data['department_id']);
            if (! $department || $department->college_slug !== $organization->college_slug) {
                return back()->withErrors(['department_id' => 'Selected department does not belong to this college.'])->withInput();
            }
        }

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_visible'] = $request->has('is_visible');

        if ($request->hasFile('logo')) {
            $file       = $request->file('logo');
            $folderName = ! empty($data['acronym'])
                ? Str::upper(Str::slug($data['acronym']))
                : Str::slug($data['name']);
            $filename   = time() . '_logo.' . $file->getClientOriginalExtension();
            $collegeLogoPath = $organization->college_slug ?: 'global';
            $path       = Storage::disk('google')->putFileAs("colleges/{$collegeLogoPath}/student-organization/{$folderName}/logos", $file, $filename);
            $data['logo'] = Storage::disk('google')->url($path);
        } elseif ($request->filled('media_image')) {
            $data['logo'] = $request->input('media_image');
        } elseif ($request->input('remove_logo') === '1') {
            $data['logo'] = null;
        }

        $organization->update($data);

        $returnTo = $request->session()->pull($this->getEditReturnSessionKey($organization));
        if ($returnTo) {
            return redirect()->to($returnTo)
                ->with('success', 'Student organization updated successfully.');
        }

        $returnCollege = $request->input('return_college');
        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'organizations'])
                ->with('success', 'Student organization updated successfully.');
        }

        return redirect()->route('admin.colleges.index')->with('success', 'Student organization updated successfully.');
    }

    public function destroy(Request $request, CollegeOrganization $organization): RedirectResponse
    {
        $this->authorizeCollege($organization->college_slug);
        
        $googleDrive = app(\App\Services\GoogleDriveService::class);

        // Delete Logo from Drive
        if ($organization->logo) {
            $googleDrive->delete($organization->logo);
        }

        if ($organization->logo && !str_starts_with($organization->logo, 'images/') && Storage::disk('public')->exists($organization->logo)) {
            Storage::disk('public')->delete($organization->logo);
        }

        // Delete images from all sections
        $sections = $organization->sections ?? [];
        foreach ($sections as $sectionSlug => $sectionData) {
            if (isset($sectionData['items'])) {
                foreach ($sectionData['items'] as $item) {
                    if (isset($item['image'])) {
                        $googleDrive->delete($item['image']);
                    }
                    if (isset($item['photos'])) {
                        foreach ($item['photos'] as $photo) {
                            if (isset($photo['image'])) {
                                $googleDrive->delete($photo['image']);
                            }
                        }
                    }
                }
            }
            if (isset($sectionData['image'])) {
                $googleDrive->delete($sectionData['image']);
            }
        }

        $organization->delete();

        $returnCollege = $request->input('return_college');
        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'organizations'])
                ->with('success', 'Student organization deleted successfully.');
        }

        return redirect()->route('admin.colleges.index')->with('success', 'Student organization deleted successfully.');
    }

    public function addSection(Request $request, CollegeOrganization $organization): RedirectResponse
    {
        $this->authorizeCollege($organization->college_slug);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:100'],
        ]);

        $slug = Str::slug($data['title']);
        $originalSlug = $slug;
        $counter = 1;

        // Ensure unique slug within default and stored sections
        $existing = $this->getSectionsMetadata($organization);
        while (isset($existing[$slug])) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $organization->setSection($slug, [
            'title' => $data['title'],
            'body'  => '',
        ]);
        $organization->save();

        return redirect()->route('admin.organizations.show-section', [
            'college' => $organization->college_slug,
            'organization' => $organization,
            'section' => $slug,
        ])->with('success', 'New section added successfully.');
    }

    public function deleteSection(Request $request, CollegeOrganization $organization, string $section): RedirectResponse
    {
        $this->authorizeCollege($organization->college_slug);

        $defaults = ['overview', 'activities', 'officers', 'gallery'];
        if (in_array($section, $defaults)) {
            abort(403, 'Cannot delete default sections.');
        }

        $stored = $organization->sections ?? [];
        if (isset($stored[$section])) {
            $sectionData = $stored[$section];
            $googleDrive = app(\App\Services\GoogleDriveService::class);

            if (isset($sectionData['items'])) {
                foreach ($sectionData['items'] as $item) {
                    if (isset($item['image'])) {
                        $googleDrive->delete($item['image']);
                    }
                    if (isset($item['photos'])) {
                        foreach ($item['photos'] as $photo) {
                            if (isset($photo['image'])) {
                                $googleDrive->delete($photo['image']);
                            }
                        }
                    }
                }
            }
            if (isset($sectionData['image'])) {
                $googleDrive->delete($sectionData['image']);
            }

            unset($stored[$section]);
            $organization->sections = $stored;
            $organization->save();
        }

        return redirect()->route('admin.organizations.show', [
            'college' => $organization->college_slug,
            'organization' => $organization,
        ])->with('success', 'Section deleted successfully.');
    }

    public function storeItem(Request $request, CollegeOrganization $organization, string $section): RedirectResponse
    {
        $this->authorizeCollege($organization->college_slug);

        $sectionData = $organization->getSection($section) ?? [];
        $items = $sectionData['items'] ?? [];
        $imagePath = $request->input('image');

        $albumIndex = $request->query('album');
        $albumTitle = null;

        if ($section === 'gallery' && $albumIndex !== null && isset($items[$albumIndex])) {
            $albumTitle = $items[$albumIndex]['title'] ?? $items[$albumIndex]['name'] ?? null;
        }

        if ($request->hasFile('image_upload')) {
            $uploadTitle = $albumTitle ?: ($request->input('title') ?? $request->input('name'));
            $imagePath = $this->uploadSectionItemImage($organization, $section, $request->file('image_upload'), $uploadTitle);
        }

        $newItem = [
            'name' => $request->input('name'),
            'role' => $request->input('role') ?? $request->input('title'), // fallback for different types
            'title' => $request->input('title') ?? $request->input('name'),
            'date' => $request->input('date'),
            'image' => $imagePath,
            'description' => $request->input('description'),
            'caption' => $request->input('caption'),
            'is_visible' => $request->boolean('is_visible'),
            'photos' => [], // For gallery albums
        ];

        // If storing inside an album
        $albumIndex = $request->query('album');
        if ($section === 'gallery' && $albumIndex !== null && isset($items[$albumIndex])) {
            if (!isset($items[$albumIndex]['photos'])) {
                $items[$albumIndex]['photos'] = [];
            }
            $items[$albumIndex]['photos'][] = $newItem;
        } else {
            $items[] = $newItem;
        }

        $sectionData['items'] = $items;
        $organization->setSection($section, $sectionData);
        $organization->save();

        return redirect()->back()->with('success', 'Item added successfully.');
    }

    public function storeBatchItems(Request $request, CollegeOrganization $organization, string $section): RedirectResponse
    {
        $this->authorizeCollege($organization->college_slug);

        $sectionData = $organization->getSection($section) ?? [];
        $items = $sectionData['items'] ?? [];

        $albumIndex = $request->query('album');
        $addedCount = 0;
        $newPaths = [];

        $albumTitle = null;
        if ($section === 'gallery' && $albumIndex !== null && isset($items[$albumIndex])) {
            $albumTitle = $items[$albumIndex]['title'] ?? $items[$albumIndex]['name'] ?? null;
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                // Pass Album Title to store images inside Album Folder
                $path = $this->uploadSectionItemImage($organization, $section, $file, $albumTitle);
                if ($path) {
                    $newPaths[] = $path;
                }
            }
        } else {
            $newPaths = $request->input('images', []);
        }

        if ($section === 'gallery' && $albumIndex !== null && isset($items[$albumIndex])) {
            if (!isset($items[$albumIndex]['photos'])) {
                $items[$albumIndex]['photos'] = [];
            }
            foreach ($newPaths as $path) {
                $items[$albumIndex]['photos'][] = [
                    'image' => $path,
                    'title' => '',
                    'caption' => '',
                    'description' => '',
                ];
                $addedCount++;
            }
        } else {
            foreach ($newPaths as $path) {
                $items[] = [
                    'image' => $path,
                    'title' => '',
                    'caption' => '',
                    'description' => '',
                    'photos' => [],
                ];
                $addedCount++;
            }
        }

        $sectionData['items'] = $items;
        $organization->setSection($section, $sectionData);
        $organization->save();

        return redirect()->back()->with('success', $addedCount . ' items added to gallery successfully.');
    }

    public function updateItem(Request $request, CollegeOrganization $organization, string $section, int $index): RedirectResponse
    {
        $this->authorizeCollege($organization->college_slug);

        $sectionData = $organization->getSection($section);
        if (!$sectionData || !isset($sectionData['items'][$index])) {
            abort(404, 'Item not found.');
        }

        $albumIndex = $request->query('album');
        $imagePath = $request->input('image');

        $albumTitle = null;
        if ($section === 'gallery' && $albumIndex !== null && isset($sectionData['items'][$albumIndex])) {
            $albumTitle = $sectionData['items'][$albumIndex]['title'] ?? $sectionData['items'][$albumIndex]['name'] ?? null;
        }

        if ($request->hasFile('image_upload')) {
            $uploadTitle = $albumTitle ?: ($request->input('title') ?? $request->input('name'));
            $imagePath = $this->uploadSectionItemImage($organization, $section, $request->file('image_upload'), $uploadTitle);
        }

        if ($section === 'gallery' && $albumIndex !== null && isset($sectionData['items'][$albumIndex])) {
            if (!isset($sectionData['items'][$albumIndex]['photos'][$index])) {
                abort(404, 'Photo not found.');
            }
            $sectionData['items'][$albumIndex]['photos'][$index] = [
                'name' => $request->input('name'),
                'role' => $request->input('role') ?? $request->input('title'),
                'title' => $request->input('title') ?? $request->input('name'),
                'date' => $request->input('date'),
                'image' => $imagePath,
                'description' => $request->input('description'),
                'caption' => $request->input('caption'),
                'is_visible' => $request->boolean('is_visible'),
            ];
        } else {
            // Keep existing photos if any
            $existingPhotos = $sectionData['items'][$index]['photos'] ?? [];
            
            $sectionData['items'][$index] = [
                'name' => $request->input('name'),
                'role' => $request->input('role') ?? $request->input('title'),
                'title' => $request->input('title') ?? $request->input('name'),
                'date' => $request->input('date'),
                'image' => $imagePath,
                'description' => $request->input('description'),
                'caption' => $request->input('caption'),
                'is_visible' => $request->boolean('is_visible'),
                'photos' => $existingPhotos,
            ];
        }

        $organization->setSection($section, $sectionData);
        $organization->save();

        return redirect()->back()->with('success', 'Item updated successfully.');
    }

    public function moveItem(Request $request, CollegeOrganization $organization, string $section, int $index): RedirectResponse
    {
        $this->authorizeCollege($organization->college_slug);

        $direction = $request->input('direction');
        if (! in_array($direction, ['up', 'down'], true)) {
            return redirect()->back()->with('error', 'Invalid move direction.');
        }

        $sectionData = $organization->getSection($section);
        if (! $sectionData) {
            abort(404, 'Section not found.');
        }

        $albumIndex = $request->query('album');

        if ($section === 'gallery' && $albumIndex !== null && isset($sectionData['items'][$albumIndex])) {
            $photos = $sectionData['items'][$albumIndex]['photos'] ?? [];
            if (! isset($photos[$index])) {
                abort(404, 'Photo not found.');
            }

            $targetIndex = $direction === 'up' ? $index - 1 : $index + 1;
            if (! isset($photos[$targetIndex])) {
                return redirect()->back();
            }

            [$photos[$index], $photos[$targetIndex]] = [$photos[$targetIndex], $photos[$index]];
            $sectionData['items'][$albumIndex]['photos'] = array_values($photos);
        } else {
            $items = $sectionData['items'] ?? [];
            if (! isset($items[$index])) {
                abort(404, 'Item not found.');
            }

            $targetIndex = $direction === 'up' ? $index - 1 : $index + 1;
            if (! isset($items[$targetIndex])) {
                return redirect()->back();
            }

            [$items[$index], $items[$targetIndex]] = [$items[$targetIndex], $items[$index]];
            $sectionData['items'] = array_values($items);
        }

        $organization->setSection($section, $sectionData);
        $organization->save();

        return redirect()->back()->with('success', 'Item order updated successfully.');
    }

    public function reorderItems(Request $request, CollegeOrganization $organization, string $section): RedirectResponse
    {
        $this->authorizeCollege($organization->college_slug);

        $order = $request->input('order', []);
        if (! is_array($order) || empty($order)) {
            return redirect()->back()->with('error', 'Invalid item order.');
        }

        $sectionData = $organization->getSection($section);
        if (! $sectionData) {
            abort(404, 'Section not found.');
        }

        $albumIndex = $request->query('album');

        if ($section === 'gallery' && $albumIndex !== null && isset($sectionData['items'][$albumIndex])) {
            $photos = $sectionData['items'][$albumIndex]['photos'] ?? [];
            $reorderedPhotos = [];

            foreach ($order as $originalIndex) {
                if (isset($photos[(int) $originalIndex])) {
                    $reorderedPhotos[] = $photos[(int) $originalIndex];
                }
            }

            if (count($reorderedPhotos) !== count($photos)) {
                return redirect()->back()->with('error', 'Photo order could not be saved.');
            }

            $sectionData['items'][$albumIndex]['photos'] = array_values($reorderedPhotos);
        } else {
            $items = $sectionData['items'] ?? [];
            $reorderedItems = [];

            foreach ($order as $originalIndex) {
                if (isset($items[(int) $originalIndex])) {
                    $reorderedItems[] = $items[(int) $originalIndex];
                }
            }

            if (count($reorderedItems) !== count($items)) {
                return redirect()->back()->with('error', 'Item order could not be saved.');
            }

            $sectionData['items'] = array_values($reorderedItems);
        }

        $organization->setSection($section, $sectionData);
        $organization->save();

        return redirect()->back()->with('success', 'Item order updated successfully.');
    }

    public function deleteItem(Request $request, CollegeOrganization $organization, string $section, int $index): RedirectResponse
    {
        $this->authorizeCollege($organization->college_slug);

        $sectionData = $organization->getSection($section);
        if (!$sectionData || !isset($sectionData['items'][$index])) {
            abort(404, 'Item not found.');
        }

        $albumIndex = $request->query('album');
        $googleDrive = app(\App\Services\GoogleDriveService::class);
        $parentId = null;

        if ($section === 'gallery' && $albumIndex !== null && isset($sectionData['items'][$albumIndex])) {
            if (!isset($sectionData['items'][$albumIndex]['photos'][$index])) {
                abort(404, 'Photo not found.');
            }
            $photo = $sectionData['items'][$albumIndex]['photos'][$index];
            if (isset($photo['image'])) {
                $parentId = $googleDrive->getParentIdOfFile($photo['image']);
                $googleDrive->delete($photo['image']);
            }
            array_splice($sectionData['items'][$albumIndex]['photos'], $index, 1);
        } else {
            $item = $sectionData['items'][$index];
            if (isset($item['image'])) {
                $parentId = $googleDrive->getParentIdOfFile($item['image']);
                $googleDrive->delete($item['image']);
            }
            if (isset($item['photos'])) {
                foreach ($item['photos'] as $photo) {
                    if (isset($photo['image'])) {
                        $googleDrive->delete($photo['image']);
                    }
                }
            }
            $items = $sectionData['items'];
            array_splice($items, $index, 1);
            $sectionData['items'] = $items;
        }

        $organization->setSection($section, $sectionData);
        $organization->save();

        if ($parentId) {
            $googleDrive->deleteFolderIfEmpty($parentId);
        }

        return redirect()->back()->with('success', 'Item removed successfully.');
    }

    protected function getSectionsMetadata(CollegeOrganization $organization): array
    {
        $sections = [
            'overview'   => 'Overview',
            'activities' => 'Activities',
            'officers'   => 'Members',
            'gallery'    => 'Gallery',
        ];

        $stored = $organization->sections ?? [];
        foreach ($stored as $slug => $data) {
            if (! isset($sections[$slug])) {
                $sections[$slug] = $data['title'] ?? Str::title(str_replace('-', ' ', $slug));
            }
        }

        return $sections;
    }

    protected function authorizeCollege(?string $collegeSlug): void
    {
        $user = auth()->user();
        if ($user && !$user->canAccessCollege($collegeSlug)) {
            abort(403, 'Unauthorized.');
        }
    }

    protected function getAdviserFaculty(CollegeOrganization $organization)
    {
        $query = Faculty::query()
            ->where('college_slug', $organization->college_slug);

        if ($organization->department) {
            $query->where('department', $organization->department->name);
        }

        return $query->orderBy('name')->get();
    }

    protected function getEditReturnSessionKey(CollegeOrganization $organization): string
    {
        return 'organization_edit_return_url_' . $organization->getKey();
    }

    protected function resolveEditReturnUrl(Request $request, CollegeOrganization $organization): string
    {
        $fallbackUrl = route('admin.organizations.show', [
            'college' => $organization->college_slug,
            'organization' => $organization,
        ]);

        $previousUrl = url()->previous();
        if (! is_string($previousUrl) || $previousUrl === '') {
            return $fallbackUrl;
        }

        $previousPath = parse_url($previousUrl, PHP_URL_PATH);
        $editPath = parse_url(route('admin.organizations.edit', $organization), PHP_URL_PATH);
        $scopedEditPath = parse_url(route('admin.organizations.edit-scoped', [
            'college' => $organization->college_slug,
            'organization' => $organization,
        ]), PHP_URL_PATH);

        if (
            ! is_string($previousPath)
            || $previousPath === ''
            || $previousPath === $editPath
            || $previousPath === $scopedEditPath
        ) {
            return $fallbackUrl;
        }

        $appUrl = config('app.url');
        $appHost = is_string($appUrl) ? parse_url($appUrl, PHP_URL_HOST) : null;
        $previousHost = parse_url($previousUrl, PHP_URL_HOST);

        if ($appHost && $previousHost && ! hash_equals($appHost, $previousHost)) {
            return $fallbackUrl;
        }

        return $previousUrl;
    }

    protected function getGalleryAlbumRouteKey(array $items, int $targetIndex): string
    {
        $used = [];

        foreach (array_values($items) as $index => $item) {
            $base = Str::slug($item['title'] ?? $item['name'] ?? '');

            if ($base === '') {
                $base = 'album-' . ($index + 1);
            }

            $slug = $base;
            $counter = 2;

            while (isset($used[$slug])) {
                $slug = $base . '-' . $counter++;
            }

            $used[$slug] = $index;

            if ($index === $targetIndex) {
                return $slug;
            }
        }

        return 'album-' . ($targetIndex + 1);
    }

    protected function resolveGalleryAlbumIndex(array $items, string $album): ?int
    {
        if (ctype_digit($album)) {
            $legacyIndex = (int) $album;

            return isset($items[$legacyIndex]) ? $legacyIndex : null;
        }

        $used = [];

        foreach (array_values($items) as $index => $item) {
            $base = Str::slug($item['title'] ?? $item['name'] ?? '');

            if ($base === '') {
                $base = 'album-' . ($index + 1);
            }

            $slug = $base;
            $counter = 2;

            while (isset($used[$slug])) {
                $slug = $base . '-' . $counter++;
            }

            $used[$slug] = $index;

            if ($slug === $album) {
                return $index;
            }
        }

        return null;
    }

    protected function uploadSectionItemImage(CollegeOrganization $organization, string $section, UploadedFile $file, ?string $title = null): ?string
    {
        $college = $organization->college_slug ?: 'global';
        $sectionSlug = Str::slug($section);
        $folderName = ! empty($organization->acronym)
            ? Str::upper(Str::slug($organization->acronym))
            : Str::slug($organization->name);
            
        $folderPath = "colleges/{$college}/student-organization/{$folderName}/{$sectionSlug}";

        if ($title) {
            $folderPath .= '/' . Str::slug($title);
        }

        $filename = time() . '_' . $sectionSlug . '.' . $file->getClientOriginalExtension();
        $path = Storage::disk('google')->putFileAs($folderPath, $file, $filename);

        if (! $path) {
            return null;
        }

        $fileId = app(\App\Services\GoogleDriveService::class)->getFileId($path);

        return 'media/proxy/' . ($fileId ?? $path);
    }
}
