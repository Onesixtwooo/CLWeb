<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College as CollegeModel;
use App\Models\CollegeDepartment;
use App\Models\CollegeDownload;
use App\Models\CollegeSection;
use App\Models\CollegeVideo;
use App\Models\CollegeRetro;
use App\Models\CollegeExtension;
use App\Models\CollegeTraining;
use App\Models\DepartmentObjective;
use App\Models\DepartmentOutcome;
use App\Models\DepartmentProgram;
use App\Models\DepartmentResearch;
use App\Models\DepartmentTraining;
use App\Models\DepartmentAlumnus;
use App\Models\Facility;
use App\Models\Faculty;
use App\Models\CollegeInstitute;
use App\Models\InstituteGoal;
use App\Models\InstituteStaff;
use App\Models\InstituteResearch;
use App\Models\InstituteExtension;
use App\Models\InstituteFacility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CollegeController extends Controller
{
    private function encodeCollegeRetroKey(string $college, int $retroId): string
    {
        return \Illuminate\Support\Facades\Crypt::encryptString($college . ':' . $retroId);
    }

    private function decodeCollegeRetroKey(?string $retroKey, string $college): ?int
    {
        if (empty($retroKey)) {
            return null;
        }

        try {
            $payload = \Illuminate\Support\Facades\Crypt::decryptString($retroKey);
        } catch (\Throwable $e) {
            return null;
        }

        [$tokenCollege, $retroId] = array_pad(explode(':', $payload, 2), 2, null);

        if ($tokenCollege !== $college || !ctype_digit((string) $retroId)) {
            return null;
        }

        return (int) $retroId;
    }

    private const DEFAULT_COLLEGES = [
        'agriculture' => 'College of Agriculture',
        'arts-and-social-sciences' => 'College of Arts and Social Sciences',
        'business-and-accountancy' => 'College of Business and Accountancy',
        'education' => 'College of Education',
        'engineering' => 'College of Engineering',
        'fisheries' => 'College of Fisheries',
        'home-science-and-industry' => 'College of Home Science and Industry',
        'veterinary-science-and-medicine' => 'College of Veterinary Science and Medicine',
        'science' => 'College of Science',
        'dot-uni' => 'CLSU Distance, Open, and Transnational University (DOT-Uni)',
    ];

    /** @return array<string, string> */
    public static function getColleges(): array
    {
        try {
            $colleges = CollegeModel::orderBy('name')->pluck('name', 'slug')->all();
            return $colleges !== [] ? $colleges : self::DEFAULT_COLLEGES;
        } catch (\Throwable) {
            return self::DEFAULT_COLLEGES;
        }
    }

    public static function getSections(): array
    {
        return [
            'overview' => 'Overview',
            'departments' => 'Departments',
            'institutes' => 'Institutes',
            'facilities' => 'Facilities',
            'faculty' => 'Faculty',
            'alumni' => 'Alumni',
            'admissions' => 'Admissions',
            'faq' => 'FAQs',
            'extension' => 'Extension',
            'training' => 'Training',
            'scholarships' => 'Scholarships',
            'downloads' => 'Downloads',
            'accreditation' => 'Accreditation',
            'membership' => 'Membership',
            'organizations' => 'Student Organizations',
        ];
    }

    /** @return array<string, string> */
    public static function getDepartmentSections(): array
    {
        return [
            'overview' => 'Overview',
            'objectives' => 'Objectives',
            'faculty' => 'Faculty',
            'programs' => 'Programs',
            'awards' => 'Awards',
            'research' => 'Research',
            'linkages' => 'Linkages',
            'extension' => 'Extension',
            'training' => 'Training',
            'membership' => 'Membership',
            'organizations' => 'Student Organizations',
            'facilities' => 'Facilities',
            'alumni' => 'Alumni',
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! $user->isSuperAdmin()) {
            abort(403, 'Only superadmins can create departments.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:80'],
        ]);

        $slug = $data['slug'] ?: Str::slug($data['name']);
        if ($slug === '') {
            return back()->withErrors(['slug' => 'Unable to generate slug for this name.'])->withInput();
        }

        // Ensure slug uniqueness
        $original = $slug;
        $i = 2;
        while (CollegeModel::find($slug)) {
            $slug = $original . '-' . $i;
            $i++;
        }

        CollegeModel::create([
            'slug' => $slug,
            'name' => $data['name'],
        ]);

        return redirect()
            ->route('admin.colleges.show', ['college' => $slug])
            ->with('success', 'Department added successfully.');
    }

    public function editCollege(Request $request, string $college): View
    {
        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }
        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }
        $model = CollegeModel::find($college);
        if (! $model) {
            abort(404, 'College not found.');
        }
        return view('admin.colleges.edit-college', [
            'college' => $model,
            'collegeName' => $model->name,
        ]);
    }

    public function updateCollege(Request $request, string $college): RedirectResponse
    {
        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }
        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }
        $model = CollegeModel::find($college);
        if (! $model) {
            abort(404, 'College not found.');
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        $model->update(['name' => $data['name']]);

        return redirect()
            ->route('admin.colleges.show', ['college' => $college])
            ->with('success', 'College name updated successfully.');
    }

    public function index(Request $request): View|RedirectResponse
    {
        $allColleges = self::getColleges();
        $user = $request->user();
        
        // If user is bounded to a department, redirect directly to department dashboard
        if ($user && $user->isBoundedToDepartment()) {
            $collegeSlug = $user->college_slug;
            $departmentRouteKey = $user->getDepartmentRouteKey($collegeSlug);
            if ($departmentRouteKey !== null) {
                return redirect()->route('admin.colleges.show-department', [
                    'college' => $collegeSlug,
                    'department' => $departmentRouteKey,
                    'section' => 'overview',
                ]);
            }
        }
        
        if ($user && $user->isBoundedToCollege()) {
            $slug = $user->college_slug;
            if (empty($slug) || ! isset($allColleges[$slug])) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Editor and admin must be assigned to a college. Contact a superadmin.');
            }
            return redirect()->route('admin.colleges.show', ['college' => $slug]);
        }

        return view('admin.colleges.index', [
            'colleges' => $allColleges,
        ]);
    }

    public function show(Request $request, string $college, ?string $section = null): View|array|RedirectResponse
    {
        // Handle POST requests for departments (add/edit/delete/section-save)
        if ($request->isMethod('POST') && $request->input('section') === 'departments' && $request->has('college')) {
            return $this->handleDepartmentAction($request, $college);
        }
        
        // Handle POST requests for explore items (add/edit/delete)
        if ($request->isMethod('POST') && $request->input('section') === 'explore' && $request->has('college')) {
            return $this->handleExploreAction($request, $college);
        }

        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }
        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }

        $sections = self::getSections();
        // Override section display names with user-edited titles from DB
        $customTitles = CollegeSection::where('college_slug', $college)
            ->whereNotNull('title')
            ->where('title', '!=', '')
            ->pluck('title', 'section_slug')
            ->toArray();
        foreach ($customTitles as $slug => $customTitle) {
            if (isset($sections[$slug])) {
                $sections[$slug] = $customTitle;
            }
        }

        // Build URL slug map: internal slug => URL slug (slugified custom title or original key)
        $sectionUrlSlugs = [];
        foreach ($sections as $internalSlug => $displayName) {
            $sectionUrlSlugs[$internalSlug] = \Illuminate\Support\Str::slug($displayName);
        }

        // Resolve incoming $section: try both internal slug and custom URL slug
        if ($section !== null && $section !== 'live-page' && !isset($sections[$section])) {
            // Try to find by matching custom URL slug
            $resolvedSlug = array_search($section, $sectionUrlSlugs);
            if ($resolvedSlug !== false) {
                $section = $resolvedSlug;
            }
        }

        // Add Appearance section for superadmin
        if ($user && $user->isSuperAdmin()) {
            $sections['appearance'] = 'Appearance';
            $sectionUrlSlugs['appearance'] = 'appearance';
        }

        $section = $section ?? array_key_first($sections);
        if ($section !== 'live-page' && !isset($sections[$section])) {
            $section = array_key_first($sections);
        }

        if ($section === 'live-page') {
            $content = ['title' => 'Live Page', 'body' => ''];
        } elseif ($section === 'appearance') {
            if (!$user || !$user->isSuperAdmin()) {
                abort(403, 'Only superadmins can access this section.');
            }
            $content = [
                'title' => 'Appearance',
                'headerColor' => \App\Models\Setting::get('admin_header_color_' . $college, \App\Http\Controllers\Admin\SettingsController::HEADER_COLOR_DEFAULT),
                'sidebarColor' => \App\Models\Setting::get('admin_sidebar_color_' . $college, \App\Http\Controllers\Admin\SettingsController::SIDEBAR_COLOR_DEFAULT),
                'adminLogoPath' => \App\Models\Setting::get('admin_logo_path_' . $college, null),
            ];
        } else {
            $content = $this->getSectionContent($college, $colleges[$college], $section);
        }

        if ($request->wantsJson()) {
            return ['content' => $content];
        }

        // If user is bounded to a department, redirect to department dashboard
        if ($user && $user->isBoundedToDepartment()) {
            $departmentRouteKey = $user->getDepartmentRouteKey($college);
            if ($departmentRouteKey !== null) {
                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentRouteKey,
                    'section' => 'overview',
                ]);
            }
        }

        if ($section === 'faculty') {
            $facultyList = Faculty::where('college_slug', $college)->orderBy('sort_order')->orderBy('name')->get();
            
            // Get institute staff and merge into faculty list, matching public page logic
            $institutes = \App\Models\CollegeInstitute::where('college_slug', $college)->with('staff')->get();
            foreach ($institutes as $institute) {
                foreach ($institute->staff as $staff) {
                    $staff->department = $institute->name;
                    // Note: Institute staff images might start with /images/, but we'll let the view handle it 
                    // or just use it as is since the admin view uses `asset('images/' . $member->photo)`
                    if ($staff->photo) {
                        $staff->photo = ltrim(str_replace(['/images/', 'images/'], '', $staff->photo), '/');
                    }
                }
                $facultyList = $facultyList->concat($institute->staff);
            }

            $unassignedInstituteStaff = InstituteStaff::where('college_slug', $college)
                ->whereNull('institute_id')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            foreach ($unassignedInstituteStaff as $staff) {
                $staff->department = 'Unassigned Staff';
                if ($staff->photo) {
                    $staff->photo = ltrim(str_replace(['/images/', 'images/'], '', $staff->photo), '/');
                }
            }

            $facultyList = $facultyList->concat($unassignedInstituteStaff);
            // Sort by sort_order then name
            $facultyList = $facultyList->sortBy([
                ['sort_order', 'asc'],
                ['name', 'asc'],
            ]);
        } else {
            $facultyList = collect();
        }

        $facilityList = $section === 'facilities'
        ? Facility::where('college_slug', $college)->orderBy('sort_order')->orderBy('name')->get()
        : collect();

    $instituteList = $section === 'institutes'
        ? \App\Models\CollegeInstitute::where('college_slug', $college)->orderBy('sort_order')->orderBy('name')->get()
        : collect();

    $faqList = $section === 'faq'
            ? \App\Models\CollegeFaq::where('college_slug', $college)->orderBy('sort_order')->get()
            : collect();

        $retroList = $section === 'overview'
            ? \App\Models\CollegeRetro::where('college_slug', $college)->whereNull('department_id')->orderBy('sort_order')->get()
            : collect();

        $testimonialList = $section === 'testimonials'
            ? \App\Models\CollegeTestimonial::where('college_slug', $college)->orderBy('sort_order')->get()
            : collect();

        $alumniList = $section === 'alumni'
            ? \App\Models\DepartmentAlumnus::with('department')
                ->where(function ($query) use ($college) {
                    $query->where(function ($directQuery) use ($college) {
                        $directQuery->where('college_slug', $college)
                            ->whereNull('department_id')
                            ->whereNull('institute_id');
                    })->orWhereHas('department', function ($departmentQuery) use ($college) {
                        $departmentQuery->where('college_slug', $college);
                    });
                })
                ->latest()
                ->paginate(5, ['*'], 'alumni_page')
                ->withQueryString()
            : collect();

        $accreditationList = $section === 'accreditation'
            ? \App\Models\CollegeAccreditation::where('college_slug', $college)->with('program')->orderBy('sort_order')->get()
            : collect();

        $membershipList = $section === 'membership'
            ? \App\Models\CollegeMembership::where('college_slug', $college)->with('department')->orderBy('sort_order')->get()
            : collect();

        $organizationList = $section === 'organizations'
            ? \App\Models\CollegeOrganization::where('college_slug', $college)->with('department')->orderBy('sort_order')->get()
            : collect();

        $extensionList = ($section === 'extension')
            ? CollegeExtension::where('college_slug', $college)->orderBy('sort_order')->get()
            : collect();

        $trainingList = ($section === 'training')
            ? CollegeTraining::where('college_slug', $college)->orderBy('sort_order')->get()
            : collect();

        $downloadList = ($section === 'downloads')
            ? CollegeDownload::where('college_slug', $college)->orderBy('sort_order')->orderByDesc('created_at')->get()
            : collect();

        // Get departments list for departments section
        $departmentsList = collect();
        if ($section === 'departments') {
            $departmentsList = CollegeDepartment::where('college_slug', $college)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }
        
        // Get explore items list for explore section
        $exploreList = [];
        if ($section === 'explore') {
            $sectionContent = CollegeSection::query()
                ->where('college_slug', $college)
                ->where('section_slug', 'explore')
                ->first();
            
            // Read explore items from meta column
            if ($sectionContent && !empty($sectionContent->meta)) {
                $decoded = $sectionContent->meta;
                if (is_array($decoded) && isset($decoded['explore_items'])) {
                    $exploreList = $decoded['explore_items'];
                }
            }
        }

        // Selected department ID (optional) when viewing departments
        $selectedDepartment = null;
        if ($section === 'departments') {
            $deptId = $request->query('department');
            if ($deptId !== null && is_numeric($deptId)) {
                $selectedDepartment = CollegeDepartment::find((int) $deptId);
            }
        }

        // Get college model for icon access
        $collegeModel = CollegeModel::find($college);

        // Get featured video for overview section
        $videoData = null;
        if ($section === 'overview') {
            $videoData = CollegeVideo::where('college_slug', $college)->first();
        }

        // Load section statuses for draft/scheduled badges
        $sectionStatuses = CollegeSection::where('college_slug', $college)
            ->get()
            ->keyBy('section_slug');

        // Determine completeness of each section
        $filledSectionSlugs = CollegeSection::where('college_slug', $college)
            ->whereNotNull('body')
            ->where('body', '!=', '')
            ->pluck('section_slug')
            ->toArray();

        $hasDepartments = CollegeDepartment::where('college_slug', $college)->exists();
        $hasFaculty = Faculty::where('college_slug', $college)->exists();
        $hasFacilities = Facility::where('college_slug', $college)->exists();
        $hasInstitutes = \App\Models\CollegeInstitute::where('college_slug', $college)->exists();
        $hasFaqs = \App\Models\CollegeFaq::where('college_slug', $college)->exists();
        $hasTestimonials = \App\Models\CollegeTestimonial::where('college_slug', $college)->exists();
        $hasAlumni = \App\Models\DepartmentAlumnus::where(function ($query) use ($college) {
                $query->where(function ($directQuery) use ($college) {
                    $directQuery->where('college_slug', $college)
                        ->whereNull('department_id')
                        ->whereNull('institute_id');
                })->orWhereHas('department', function ($departmentQuery) use ($college) {
                    $departmentQuery->where('college_slug', $college);
                });
            })
            ->exists();
        $hasExtensions = CollegeExtension::where('college_slug', $college)->exists();
        $hasTrainings = CollegeTraining::where('college_slug', $college)->exists();
        $hasDownloads = CollegeDownload::where('college_slug', $college)->exists();
        $hasAccreditation = \App\Models\CollegeAccreditation::where('college_slug', $college)->exists();
        $hasOrganizations = \App\Models\CollegeOrganization::where('college_slug', $college)->exists();

        $completedSections = [];
        foreach ($sections as $sSlug => $sName) {
            if ($sSlug === 'appearance') {
                $completedSections[$sSlug] = true;
                continue;
            }
            $isFilled = in_array($sSlug, $filledSectionSlugs);
            if (!$isFilled) {
                $isFilled = match ($sSlug) {
                    'departments' => $hasDepartments,
                    'faculty' => $hasFaculty,
                    'alumni' => $hasAlumni,
                    'facilities' => $hasFacilities,
                    'institutes' => $hasInstitutes,
                    'faq' => $hasFaqs,
                    'testimonials' => $hasTestimonials,
                    'extension' => $hasExtensions,
                    'training' => $hasTrainings,
                    'downloads' => $hasDownloads,
                    'accreditation' => $hasAccreditation,
                    'organizations' => $hasOrganizations,
                    default => false,
                };
            }
            $completedSections[$sSlug] = $isFilled;
        }

        $headerColor = \App\Models\Setting::get('admin_header_color_' . $college, null)
            ?? \App\Models\Setting::get('admin_header_color_' . $college . '_editor', null)
            ?? \App\Http\Controllers\Admin\SettingsController::HEADER_COLOR_DEFAULT;

        return view('admin.colleges.show', [
            'collegeSlug' => $college,
            'collegeName' => $colleges[$college],
            'collegeModel' => $collegeModel,
            'sections' => $sections,
            'currentSection' => $section,
            'content' => $content,
            'headerColor' => $headerColor,
            'sectionStatuses' => $sectionStatuses,
            'completedSections' => $completedSections,
            'facultyList' => $facultyList,
            'facilityList' => $facilityList,
            'instituteList' => $instituteList,
            'faqList' => $faqList,
            'testimonialList' => $testimonialList,
            'alumniList' => $alumniList,
            'accreditationList' => $accreditationList,
            'membershipList' => $membershipList,
            'organizationList' => $organizationList,
            'retroList' => $retroList,
            'departmentsList' => $departmentsList,
            'selectedDepartment' => $selectedDepartment,
            'exploreList' => $exploreList,
            'videoData' => $videoData,
            'sectionUrlSlugs' => $sectionUrlSlugs,
            'extensionList' => $extensionList,
            'trainingList' => $trainingList,
            'downloadList' => $downloadList,
        ]);
    }

    public function edit(Request $request, string $college, string $section): View
    {
        return $this->renderCollegeSectionEditor($request, $college, $section);
    }

    public function createCollegeRetro(Request $request, string $college): View|RedirectResponse
    {
        return $this->renderCollegeSectionEditor($request, $college, 'overview', 'retro');
    }

    public function editCollegeFeaturedVideo(Request $request, string $college): View|RedirectResponse
    {
        return $this->renderCollegeSectionEditor($request, $college, 'overview', 'featured_video');
    }

    public function editCollegeRetro(Request $request, string $college, int $retro): View|RedirectResponse
    {
        return $this->renderCollegeSectionEditor($request, $college, 'overview', 'retro', $retro);
    }

    private function renderCollegeSectionEditor(
        Request $request,
        string $college,
        string $section,
        ?string $forcedEditMode = null,
        ?int $forcedRetroId = null
    ): View|RedirectResponse
    {
        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }
        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }
        $sections = self::getSections();
        // Resolve custom URL slugs (e.g., 'center' -> 'institutes')
        if (! isset($sections[$section])) {
            $customTitles = CollegeSection::where('college_slug', $college)
                ->whereNotNull('title')
                ->where('title', '!=', '')
                ->pluck('title', 'section_slug')
                ->toArray();
            foreach ($customTitles as $internalSlug => $customTitle) {
                if (\Illuminate\Support\Str::slug($customTitle) === $section && isset($sections[$internalSlug])) {
                    $section = $internalSlug;
                    break;
                }
            }
        }
        if (! isset($sections[$section])) {
            abort(404, 'Section not found.');
        }
        $sectionContent = CollegeSection::query()
            ->where('college_slug', $college)
            ->where('section_slug', $section)
            ->first();
        $defaultContent = $this->getSectionContent($college, $colleges[$college], $section);

        $detailsText = $sectionContent?->body ?? self::htmlToPlainText($defaultContent['body'] ?? '');

        // Get college model for icon access
        $collegeModel = CollegeModel::find($college);

        // Parse meta data if available for special edit modes (retro)
        $content = $sectionContent;
        
        // For featured_video edit mode, load from CollegeVideo table
        $editMode = $forcedEditMode ?? $request->query('edit');
        if ($section === 'overview' && $editMode === 'featured_video') {
            $videoRecord = CollegeVideo::where('college_slug', $college)->first();
            $content = $videoRecord; // Use video record directly
        } elseif ($editMode === 'retro') {
            $retroId = $forcedRetroId
                ?? $this->decodeCollegeRetroKey($request->query('retro_key'), $college)
                ?? $request->integer('retro_id');
            if ($retroId) {
                // Edit existing
                $retro = CollegeRetro::where('college_slug', $college)->whereNull('department_id')->where('id', $retroId)->first();
                if (!$retro) {
                     abort(404, 'Retro item not found.');
                }
                 $content = (object) array_merge(
                    $sectionContent ? $sectionContent->toArray() : [],
                    [
                        'id' => $retro->id,
                        'retro_key' => $this->encodeCollegeRetroKey($college, $retro->id),
                        'retro_title' => $retro->title,
                        'retro_description' => $retro->description,
                        'retro_stamp' => $retro->stamp,
                        'hero_background_image' => $retro->background_image,
                    ]
                );
            } else {
                // Check if max limit reached
                $currentCount = CollegeRetro::where('college_slug', $college)->whereNull('department_id')->count();
                if ($currentCount >= 4) {
                    return redirect()->route('admin.colleges.show', ['college' => $college, 'section' => $section])
                        ->with('error', 'Maximum limit of 4 retro items reached.');
                }

                // Create mode - empty/default
                $content = (object) array_merge(
                    $sectionContent ? $sectionContent->toArray() : [],
                    [
                        'id' => null,
                        'retro_key' => null,
                        'retro_title' => '',
                        'retro_description' => '',
                        'retro_stamp' => '',
                        'hero_background_image' => '',
                    ]
                );
            }
        } elseif ($sectionContent && $sectionContent->meta) {
            // For other edit modes, use meta directly (already cast to array)
            $meta = $sectionContent->meta;
            if (is_string($meta)) {
                $meta = json_decode($meta, true);
            }
            if ($meta && is_array($meta)) {
                $content = (object) array_merge(
                    $sectionContent->toArray(),
                    $meta
                );
            }
        }
        
        // Merge contact info for overview section
        if ($section === 'overview') {
            $contact = \App\Models\CollegeContact::where('college_slug', $college)->first();
            if ($contact) {
                if (is_object($content)) {
                    $content->contact_data = $contact;
                } else {
                    $content['contact_data'] = $contact;
                }
            }
        }

        return view('admin.colleges.edit-section', [
            'collegeSlug' => $college,
            'collegeName' => $colleges[$college],
            'collegeModel' => $collegeModel,
            'sectionSlug' => $section,
            'sectionName' => $sectionContent?->title ?: $sections[$section],
            'sectionModel' => $sectionContent,
            'content' => $content,
            'defaultTitle' => $defaultContent['title'],
            'defaultBody' => $defaultContent['body'] ?? '',
            'detailsText' => $detailsText,
            'resolvedEditMode' => $editMode,
        ]);
    }

    public function toggleVisibility(Request $request, string $college, string $section): RedirectResponse
    {
        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }

        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }

        $sections = self::getSections();
        if (! isset($sections[$section])) {
            $customTitles = CollegeSection::where('college_slug', $college)
                ->whereNotNull('title')
                ->where('title', '!=', '')
                ->pluck('title', 'section_slug')
                ->toArray();

            foreach ($customTitles as $internalSlug => $customTitle) {
                if (\Illuminate\Support\Str::slug($customTitle) === $section && isset($sections[$internalSlug])) {
                    $section = $internalSlug;
                    break;
                }
            }
        }

        if (! isset($sections[$section])) {
            abort(404, 'Section not found.');
        }

        $sectionModel = CollegeSection::where('college_slug', $college)
            ->where('section_slug', $section)
            ->first();

        $defaultContent = $this->getSectionContent($college, $colleges[$college], $section);
        $currentVisible = $sectionModel?->is_visible ?? true;

        CollegeSection::updateOrCreate(
            [
                'college_slug' => $college,
                'section_slug' => $section,
            ],
            [
                'title' => $sectionModel?->title ?? ($defaultContent['title'] ?? ($sections[$section] ?? ucfirst($section))),
                'body' => $sectionModel?->body ?? ($defaultContent['body'] ?? ''),
                'is_visible' => ! $currentVisible,
                'is_draft' => $sectionModel?->is_draft ?? false,
                'publish_at' => $sectionModel?->publish_at,
                'meta' => $sectionModel?->meta ?? [],
            ]
        );

        return redirect()
            ->route('admin.colleges.show', ['college' => $college, 'section' => $section])
            ->with('success', ucfirst($section) . ' visibility updated successfully.');
    }

    public function redirectDepartmentShow(Request $request, string $college, string $department, ?string $section = null): RedirectResponse
    {
        $section = $section ?? $request->query('section');

        return redirect()->route('admin.colleges.show-department', [
            'college' => $college,
            'department' => $department,
            'section' => is_string($section) && $section !== '' ? $section : 'overview',
        ], 301);
    }

    public function redirectDepartmentEditSection(Request $request, string $college, string $department, string $section): RedirectResponse
    {
        $url = route('admin.colleges.edit-department-section', [
            'college' => $college,
            'department' => $department,
            'section' => $section,
        ]);

        $query = $request->query();

        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }

        return redirect($url, 301);
    }

    public function createDepartmentCurriculum(Request $request, string $college, string $department): View|RedirectResponse
    {
        $request->query->set('edit', 'add_curriculum');

        return $this->editDepartmentSection($request, $college, $department, 'objectives');
    }

    public function editDepartmentCurriculumSection(Request $request, string $college, string $department): View|RedirectResponse
    {
        $request->query->set('edit', 'curriculum');

        return $this->editDepartmentSection($request, $college, $department, 'objectives');
    }

    public function editDepartmentCurriculum(Request $request, string $college, string $department, string $curriculum): View|RedirectResponse
    {
        $request->query->set('edit', 'edit_curriculum');
        $request->query->set('curriculum', $curriculum);

        return $this->editDepartmentSection($request, $college, $department, 'objectives', $curriculum);
    }

    public function createDepartmentExtension(Request $request, string $college, string $department): View|RedirectResponse
    {
        $request->query->set('edit', 'extension');
        $request->query->set('action', 'add');

        return $this->editDepartmentSection($request, $college, $department, 'extension');
    }

    public function editDepartmentExtension(Request $request, string $college, string $department, string $extension): View|RedirectResponse
    {
        $request->query->set('edit', 'extension');
        $request->query->set('action', 'edit');
        $request->query->set('extension_id', $extension);

        return $this->editDepartmentSection($request, $college, $department, 'extension');
    }

    public function createDepartmentTraining(Request $request, string $college, string $department): View|RedirectResponse
    {
        $request->query->set('edit', 'training');
        $request->query->set('action', 'add');

        return $this->editDepartmentSection($request, $college, $department, 'training');
    }

    public function editDepartmentTraining(Request $request, string $college, string $department, string $training): View|RedirectResponse
    {
        $request->query->set('edit', 'training');
        $request->query->set('action', 'edit');
        $request->query->set('training_id', $training);

        return $this->editDepartmentSection($request, $college, $department, 'training');
    }

    public function createDepartmentProgram(Request $request, string $college, string $department): View|RedirectResponse
    {
        $request->query->set('edit', 'programs');
        $request->query->set('action', 'add');

        return $this->editDepartmentSection($request, $college, $department, 'programs');
    }

    public function editDepartmentProgram(Request $request, string $college, string $department, string $program): View|RedirectResponse
    {
        $request->query->set('edit', 'programs');
        $request->query->set('action', 'edit');
        $request->query->set('program_id', $program);

        return $this->editDepartmentSection($request, $college, $department, 'programs');
    }

    public function createDepartmentFacility(Request $request, string $college, string $department): View|RedirectResponse
    {
        $request->query->set('edit', 'add_facility');

        return $this->editDepartmentSection($request, $college, $department, 'facilities');
    }

    public function editDepartmentFacility(Request $request, string $college, string $department, string $facility): View|RedirectResponse
    {
        $request->query->set('edit', 'edit_facility');
        $request->query->set('facility_id', $facility);

        return $this->editDepartmentSection($request, $college, $department, 'facilities');
    }

    public function createDepartmentAlumnus(Request $request, string $college, string $department): View|RedirectResponse
    {
        $request->query->set('edit', 'add_alumnus');

        return $this->editDepartmentSection($request, $college, $department, 'alumni');
    }

    public function editDepartmentAlumnus(Request $request, string $college, string $department, string $alumnus): View|RedirectResponse
    {
        $request->query->set('edit', 'edit_alumnus');
        $request->query->set('alumnus_id', $alumnus);

        return $this->editDepartmentSection($request, $college, $department, 'alumni');
    }

    public function createDepartmentObjective(Request $request, string $college, string $department): View|RedirectResponse
    {
        $request->query->set('edit', 'add_objective');

        return $this->editDepartmentSection($request, $college, $department, 'objectives');
    }

    public function editDepartmentObjective(Request $request, string $college, string $department, string $objective): View|RedirectResponse
    {
        $request->query->set('edit', 'edit_objective');
        $request->query->set('objective_id', $objective);

        return $this->editDepartmentSection($request, $college, $department, 'objectives');
    }

    public function editDepartmentCardImage(Request $request, string $college, string $department): View|RedirectResponse
    {
        $request->query->set('edit', 'card');

        return $this->editDepartmentSection($request, $college, $department, 'overview');
    }

    public function createDepartmentRetro(Request $request, string $college, string $department): View|RedirectResponse
    {
        $request->query->set('edit', 'retro');

        return $this->editDepartmentSection($request, $college, $department, 'overview');
    }

    public function editDepartmentRetro(Request $request, string $college, string $department, string $retro): View|RedirectResponse
    {
        $request->query->set('edit', 'retro');
        $request->query->set('retro_id', $retro);

        return $this->editDepartmentSection($request, $college, $department, 'overview');
    }

    public function createDepartmentGraduateOutcome(Request $request, string $college, string $department): View|RedirectResponse
    {
        $request->query->set('edit', 'graduate_outcomes');
        $request->query->set('add_outcome', '1');

        return $this->editDepartmentSection($request, $college, $department, 'overview');
    }

    public function editDepartmentGraduateOutcome(Request $request, string $college, string $department, string $outcome): View|RedirectResponse
    {
        $request->query->set('edit', 'graduate_outcomes');
        $request->query->set('edit_outcome', $outcome);

        return $this->editDepartmentSection($request, $college, $department, 'overview');
    }

    public function showDepartment(Request $request, string $college, string $departmentId, string $section): View|RedirectResponse
    {
        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }
        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }

        $department = CollegeDepartment::findByCollegeAndRouteKey($college, $departmentId);
        if (! $department) {
            abort(404, 'Department not found.');
        }

        $canonicalSection = $section === 'section'
            ? (string) $request->query('section', 'overview')
            : $section;

        if ($departmentId !== $department->getRouteKey()) {
            return redirect()->route('admin.colleges.show-department', [
                'college' => $college,
                'department' => $department,
                'section' => $canonicalSection,
            ], 301);
        }

        // Check department access
        if ($user && !$user->canAccessDepartment($college, $department->name)) {
            abort(403, 'You do not have access to this department.');
        }

        // Use department-specific sections
        $sections = self::getDepartmentSections();
        if (! isset($sections[$canonicalSection])) {
            return redirect()->route('admin.colleges.show-department', [
                'college' => $college,
                'department' => $department,
                'section' => array_key_first($sections),
            ], 302);
        }

        // Get section content from department's sections JSON
        $storedContent = $department->getSection($canonicalSection) ?? [];
        $sectionContent = array_merge([
            'title' => $sections[$canonicalSection],
            'body' => '',
        ], $storedContent);

        // Get faculty list for this department
        $facultyList = Faculty::where('college_slug', $college)
            ->where('department', $department->name)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $retroList = $canonicalSection === 'overview'
            ? CollegeRetro::where('college_slug', $college)
                ->where('department_id', $department->id)
                ->orderBy('sort_order')
                ->get()
            : collect();

        return view('admin.colleges.show-department', [
            'collegeSlug' => $college,
            'collegeName' => $colleges[$college],
            'department' => $department,
            'sections' => $sections,
            'currentSection' => $canonicalSection,
            'sectionContent' => $sectionContent,
            'facultyList' => $facultyList,
            'retroList' => $retroList,
            'membershipList' => \App\Models\CollegeMembership::where('department_id', $department->id)->orderBy('sort_order')->get(),
            'organizationList' => \App\Models\CollegeOrganization::where('department_id', $department->id)->orderBy('sort_order')->get(),
        ]);
    }


    public function editDepartmentSection(Request $request, string $college, string $department, string $section = 'objectives', ?string $curriculum = null): View|RedirectResponse
    {
        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }
        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }

        $departmentModel = CollegeDepartment::findByCollegeAndRouteKey($college, $department);
        if (! $departmentModel) {
            abort(404, 'Department not found.');
        }

        if ($department !== $departmentModel->getRouteKey()) {
            return redirect()->route('admin.colleges.edit-department-section', [
                'college' => $college,
                'department' => $departmentModel,
                'section' => $section,
            ], 301);
        }

        // Check department access
        if ($user && !$user->canAccessDepartment($college, $departmentModel->name)) {
            abort(403, 'You do not have access to this department.');
        }

        // Use department-specific sections
        $sections = self::getDepartmentSections();
        if (! isset($sections[$section])) {
            abort(404, 'Section not found.');
        }

        $routeName = $request->route()?->getName();
        $curriculumMode = match ($routeName) {
            'admin.colleges.create-department-curriculum' => 'add',
            'admin.colleges.edit-department-curriculum' => 'edit',
            default => $request->route('curriculumMode'),
        };
        $editMode = match ($curriculumMode) {
            'add' => 'add_curriculum',
            'edit' => 'edit_curriculum',
            default => $request->query('edit'),
        };

        if ($section === 'overview' && $editMode === 'banner') {
            return redirect()->route('admin.colleges.show-department', [
                'college' => $college,
                'department' => $departmentModel,
                'section' => 'overview',
            ])->with('info', 'Department banner editing has been removed. Use retro items for the overview background.');
        }

        $canonicalModes = [
            'overview' => 'overview',
            'awards' => 'awards',
            'research' => 'research',
            'objectives' => 'objectives',
            'linkages' => 'linkages',
            'organizations' => 'organizations',
            'alumni' => 'alumni_details',
        ];

        $canonicalMode = $canonicalModes[$section] ?? null;

        if ($canonicalMode !== null && $canonicalMode === $editMode) {
            $query = $request->query();
            unset($query['edit']);

            $url = route('admin.colleges.edit-department-section', [
                'college' => $college,
                'department' => $departmentModel,
                'section' => $section,
            ]);

            if ($query !== []) {
                $url .= '?' . http_build_query($query);
            }

            return redirect($url, 301);
        }

        if ($request->query('view') === 'section-details') {
            $query = $request->query();
            unset($query['view']);

            $url = route('admin.colleges.edit-department-section', [
                'college' => $college,
                'department' => $departmentModel,
                'section' => $section,
            ]);

            if ($query !== []) {
                $url .= '?' . http_build_query($query);
            }

            return redirect($url, 301);
        }

        // Get section content from department's sections JSON
        $storedContent = $departmentModel->getSection($section) ?? [];
        $sectionContent = array_merge([
            'title' => $sections[$section],
            'body' => '',
        ], $storedContent);
        $content = $sectionContent;

        if ($section === 'facilities') {
            $content['title'] = $departmentModel->facilities_title
                ?? ($sectionContent['title'] ?? $sections[$section]);
            $content['body'] = $departmentModel->facilities_body
                ?? ($sectionContent['body'] ?? '');
            $content['is_visible'] = $departmentModel->facilities_is_visible
                ?? ($sectionContent['is_visible'] ?? true);
        }

        if (
            $section === 'overview'
            && $request->query('edit') === 'card'
            && ! $request->routeIs('admin.colleges.edit-department-card-image')
        ) {
            return redirect()->route('admin.colleges.edit-department-card-image', [
                'college' => $college,
                'department' => $departmentModel,
            ]);
        }

        if (
            $section === 'objectives'
            && $request->query('edit') === 'curriculum'
            && ! $request->routeIs('admin.colleges.edit-department-curriculum-section')
        ) {
            return redirect()->route('admin.colleges.edit-department-curriculum-section', [
                'college' => $college,
                'department' => $departmentModel,
            ]);
        }

        if (
            $section === 'objectives'
            && $request->query('edit') === 'add_objective'
            && ! $request->routeIs('admin.colleges.create-department-objective')
        ) {
            return redirect()->route('admin.colleges.create-department-objective', [
                'college' => $college,
                'department' => $departmentModel,
            ]);
        }

        if (
            $section === 'objectives'
            && $request->query('edit') === 'edit_objective'
            && $request->query('objective_id')
            && ! $request->routeIs('admin.colleges.edit-department-objective')
        ) {
            return redirect()->route('admin.colleges.edit-department-objective', [
                'college' => $college,
                'department' => $departmentModel,
                'objective' => $request->query('objective_id'),
            ]);
        }

        if (
            $section === 'overview'
            && $request->query('edit') === 'retro'
            && $request->query('retro_id')
            && ! $request->routeIs('admin.colleges.edit-department-retro')
        ) {
            return redirect()->route('admin.colleges.edit-department-retro', [
                'college' => $college,
                'department' => $departmentModel,
                'retro' => $request->query('retro_id'),
            ]);
        }

        if (
            $section === 'overview'
            && $request->query('edit') === 'retro'
            && ! $request->query('retro_id')
            && ! $request->routeIs('admin.colleges.create-department-retro')
        ) {
            return redirect()->route('admin.colleges.create-department-retro', [
                'college' => $college,
                'department' => $departmentModel,
            ]);
        }

        if ($section === 'overview' && $request->query('edit') === 'retro') {
            $retroId = $request->query('retro_id');

            if ($retroId) {
                $retro = CollegeRetro::where('college_slug', $college)
                    ->where('department_id', $departmentModel->id)
                    ->find($retroId);

                if (! $retro) {
                    abort(404, 'Retro item not found.');
                }

                $content = array_merge($sectionContent, [
                    'id' => $retro->id,
                    'retro_title' => $retro->title,
                    'retro_description' => $retro->description,
                    'retro_stamp' => $retro->stamp,
                    'hero_background_image' => $retro->background_image,
                ]);
            } else {
                $currentCount = CollegeRetro::where('college_slug', $college)
                    ->where('department_id', $departmentModel->id)
                    ->count();

                if ($currentCount >= 4) {
                    return redirect()->route('admin.colleges.show-department', [
                        'college' => $college,
                        'department' => $departmentModel,
                        'section' => $section,
                    ])->with('error', 'Maximum limit of 4 retro items reached.');
                }

                $content = array_merge($sectionContent, [
                    'id' => null,
                    'retro_title' => '',
                    'retro_description' => '',
                    'retro_stamp' => '',
                    'hero_background_image' => '',
                ]);
            }
        }

        $partner = null;
        if ($section === 'linkages' && $request->query('edit') === 'edit_partner') {
            $partner = \App\Models\DepartmentLinkage::where('department_id', $departmentModel->id)
                ->findOrFail($request->query('partner_id'));
        }

        if (
            $section === 'programs'
            && $request->query('edit') === 'programs'
            && $request->query('action') === 'add'
            && ! $request->routeIs('admin.colleges.create-department-program')
        ) {
            return redirect()->route('admin.colleges.create-department-program', [
                'college' => $college,
                'department' => $departmentModel,
            ]);
        }

        if (
            $section === 'programs'
            && $request->query('edit') === 'programs'
            && $request->query('action') === 'edit'
            && $request->query('program_id')
            && ! $request->routeIs('admin.colleges.edit-department-program')
        ) {
            $selectedProgram = DepartmentProgram::findByDepartmentAndRouteKey(
                $departmentModel->id,
                $request->query('program_id')
            );

            if (! $selectedProgram) {
                abort(404, 'Program not found.');
            }

            return redirect()->route('admin.colleges.edit-department-program', [
                'college' => $college,
                'department' => $departmentModel,
                'program' => $selectedProgram->getRouteKey(),
            ]);
        }

        if (
            $section === 'extension'
            && $request->query('edit') === 'extension'
            && $request->query('action') === 'edit'
            && $request->query('extension_id')
            && ! $request->routeIs('admin.colleges.edit-department-extension')
        ) {
            $selectedExtension = \App\Models\DepartmentExtension::findByDepartmentAndRouteKey(
                $departmentModel->id,
                $request->query('extension_id')
            );

            if (! $selectedExtension) {
                abort(404, 'Extension item not found.');
            }

            return redirect()->route('admin.colleges.edit-department-extension', [
                'college' => $college,
                'department' => $departmentModel,
                'extension' => $selectedExtension->getRouteKey(),
            ]);
        }

        if (
            $section === 'facilities'
            && $request->query('edit') === 'add_facility'
            && ! $request->routeIs('admin.colleges.create-department-facility')
        ) {
            return redirect()->route('admin.colleges.create-department-facility', [
                'college' => $college,
                'department' => $departmentModel,
            ]);
        }

        if (
            $section === 'alumni'
            && $request->query('edit') === 'add_alumnus'
            && ! $request->routeIs('admin.colleges.create-department-alumnus')
        ) {
            return redirect()->route('admin.colleges.create-department-alumnus', [
                'college' => $college,
                'department' => $departmentModel,
            ]);
        }

        if (
            $section === 'alumni'
            && $request->query('edit') === 'edit_alumnus'
            && $request->query('alumnus_id')
            && ! $request->routeIs('admin.colleges.edit-department-alumnus')
        ) {
            $selectedAlumnus = DepartmentAlumnus::findByDepartmentAndRouteKey(
                $departmentModel->id,
                $request->query('alumnus_id')
            );

            if (! $selectedAlumnus) {
                abort(404, 'Alumnus not found.');
            }

            return redirect()->route('admin.colleges.edit-department-alumnus', [
                'college' => $college,
                'department' => $departmentModel,
                'alumnus' => $selectedAlumnus->getRouteKey(),
            ]);
        }

        if (
            $section === 'facilities'
            && $request->query('edit') === 'edit_facility'
            && $request->query('facility_id')
            && ! $request->routeIs('admin.colleges.edit-department-facility')
        ) {
            $selectedFacility = \App\Models\DepartmentFacility::findByDepartmentAndRouteKey(
                $departmentModel->id,
                $request->query('facility_id')
            );

            if (! $selectedFacility) {
                abort(404, 'Facility not found.');
            }

            return redirect()->route('admin.colleges.edit-department-facility', [
                'college' => $college,
                'department' => $departmentModel,
                'facility' => $selectedFacility->getRouteKey(),
            ]);
        }

        if (
            $request->query('edit') === 'graduate_outcomes'
            && $request->boolean('add_outcome')
            && ! $request->routeIs('admin.colleges.create-department-graduate-outcome')
        ) {
            return redirect()->route('admin.colleges.create-department-graduate-outcome', [
                'college' => $college,
                'department' => $departmentModel,
            ]);
        }

        if (
            $request->query('edit') === 'graduate_outcomes'
            && $request->query('edit_outcome')
            && ! $request->routeIs('admin.colleges.edit-department-graduate-outcome')
        ) {
            return redirect()->route('admin.colleges.edit-department-graduate-outcome', [
                'college' => $college,
                'department' => $departmentModel,
                'outcome' => $request->query('edit_outcome'),
            ]);
        }

        if (
            $request->query('edit') === 'graduate_outcomes'
            && ! $request->boolean('add_outcome')
            && ! $request->query('edit_outcome')
        ) {
            return redirect()->route('admin.colleges.show-department', [
                'college' => $college,
                'department' => $departmentModel,
                'section' => 'overview',
            ]);
        }

        $selectedOutcome = null;
        if ($request->query('edit') === 'graduate_outcomes' && $request->query('edit_outcome')) {
            $selectedOutcome = DepartmentOutcome::where('department_id', $departmentModel->id)
                ->findOrFail($request->query('edit_outcome'));
        }

        $selectedAward = null;
        if ($section === 'awards' && $request->query('edit') === 'edit_award' && $request->query('award_id')) {
            $selectedAward = \App\Models\DepartmentAward::where('department_id', $departmentModel->id)
                ->findOrFail($request->query('award_id'));
        }

        $selectedObjective = null;
        if ($section === 'objectives' && $request->query('edit') === 'edit_objective' && $request->query('objective_id')) {
            $selectedObjective = DepartmentObjective::where('department_id', $departmentModel->id)
                ->findOrFail($request->query('objective_id'));
        }

        $selectedProgram = null;
        if ($section === 'programs' && $request->query('edit') === 'programs' && $request->query('action') === 'edit' && $request->query('program_id')) {
            $selectedProgram = DepartmentProgram::findByDepartmentAndRouteKey(
                $departmentModel->id,
                $request->query('program_id')
            );

            if (! $selectedProgram) {
                abort(404, 'Program not found.');
            }
        }

        $selectedFacility = null;
        if ($section === 'facilities' && $request->query('edit') === 'edit_facility' && $request->query('facility_id')) {
            $selectedFacility = \App\Models\DepartmentFacility::findByDepartmentAndRouteKey(
                $departmentModel->id,
                $request->query('facility_id')
            );

            if (! $selectedFacility) {
                abort(404, 'Facility not found.');
            }
        }

        $selectedAlumnus = null;
        if ($section === 'alumni' && $request->query('edit') === 'edit_alumnus' && $request->query('alumnus_id')) {
            $selectedAlumnus = DepartmentAlumnus::findByDepartmentAndRouteKey(
                $departmentModel->id,
                $request->query('alumnus_id')
            );

            if (! $selectedAlumnus) {
                abort(404, 'Alumnus not found.');
            }
        }

        $selectedCurriculum = null;
        if ($section === 'objectives' && $editMode === 'edit_curriculum') {
            $curriculumRouteKey = $curriculum ?? $request->query('curriculum') ?? $request->query('curriculum_id');

            if ($curriculumRouteKey) {
                $selectedCurriculum = \App\Models\DepartmentCurriculum::findByDepartmentAndRouteKey($departmentModel->id, $curriculumRouteKey);

                if (! $selectedCurriculum) {
                    abort(404, 'Curriculum category not found.');
                }

                if ($curriculumMode !== 'edit' || (string) $curriculum !== $selectedCurriculum->getRouteKey()) {
                    return redirect()->route('admin.colleges.edit-department-curriculum', [
                        'college' => $college,
                        'department' => $departmentModel,
                        'curriculum' => $selectedCurriculum->getRouteKey(),
                    ], 301);
                }
            }
        }

        if ($section === 'objectives' && $editMode === 'add_curriculum' && $curriculumMode !== 'add') {
            return redirect()->route('admin.colleges.create-department-curriculum', [
                'college' => $college,
                'department' => $departmentModel,
            ], 301);
        }

        return view('admin.colleges.edit-department-section', [
            'collegeSlug' => $college,
            'collegeName' => $colleges[$college],
            'department' => $departmentModel,
            'sectionSlug' => $section,
            'sectionName' => $sections[$section],
            'content' => $content,
            'partner' => $partner,
            'selectedOutcome' => $selectedOutcome,
            'selectedObjective' => $selectedObjective,
            'selectedProgram' => $selectedProgram,
            'selectedFacility' => $selectedFacility,
            'selectedAlumnus' => $selectedAlumnus,
            'selectedCurriculum' => $selectedCurriculum,
            'isAddPartner' => $request->query('edit') === 'add_partner',
            'isEditPartner' => $request->query('edit') === 'edit_partner',
        ]);
    }

    public function updateDepartment(Request $request, string $college, string $departmentId): RedirectResponse
    {
        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }
        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }

        $department = CollegeDepartment::findByCollegeAndRouteKey($college, $departmentId);
        if (! $department) {
            abort(404, 'Department not found.');
        }

        // Check department access
        if ($user && !$user->canAccessDepartment($college, $department->name)) {
            abort(403, 'You do not have access to this department.');
        }

        // If saving a department section
        if ($request->has('save_dept_section')) {
            $section = $request->input('section') ?? 'overview';
            
            // Handle banner edit mode
            if ($request->has('_banner_edit')) {
                $data = $request->validate([
                    'banner_image' => ['nullable', 'image', 'max:2048'],
                ]);

                $currentSection = $department->getSection($section) ?? [];

                // Initialize banner_images array if not present, but fallback to single banner_image
                $bannerImages = $currentSection['banner_images'] ?? [];
                if (empty($bannerImages) && !empty($currentSection['banner_image'])) {
                    $bannerImages[] = $currentSection['banner_image'];
                }

                // Handle deletion
                if ($request->has('delete_banner_image')) {
                    $indexToDelete = (int) $request->input('delete_banner_image');
                    if (isset($bannerImages[$indexToDelete])) {
                        // Delete file
                        if (file_exists(public_path($bannerImages[$indexToDelete]))) {
                            @unlink(public_path($bannerImages[$indexToDelete]));
                        }
                        // Remove from array and re-index
                        unset($bannerImages[$indexToDelete]);
                        $bannerImages = array_values($bannerImages);
                    }
                }

                // Handle banner image upload (append if count < 3)
                if ($request->hasFile('banner_image')) {
                    if (count($bannerImages) < 3) {
                        $file = $request->file('banner_image');
                        $filename = time() . '_banner_' . Str::slug($department->name) . '_' . count($bannerImages) . '.' . $file->getClientOriginalExtension();
                        $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($department->name) . "/banners", $file, $filename);
                        if ($imagePath) {
                            $fileId = app(\App\Services\GoogleDriveService::class)->getFileId($imagePath);
                            $bannerImages[] = 'media/proxy/' . ($fileId ?? $imagePath);
                        }
                    }
                }

                // Update section data
                $currentSection['banner_images'] = $bannerImages;
                // Sync legacy field for backward compatibility
                $currentSection['banner_image'] = $bannerImages[0] ?? null;

                $department->setSection($section, $currentSection);
                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section
                ])->with('success', 'Banner updated successfully.');
            }

            // Handle programs edit mode
            if ($request->has('_programs_edit')) {
                if ($request->filled('delete_program')) {
                    $program = DepartmentProgram::where('department_id', $department->id)
                        ->find($request->input('delete_program'));

                    if ($program) {
                        $program->delete();
                    }

                    return redirect()->route('admin.colleges.show-department', [
                        'college' => $college,
                        'department' => $departmentId,
                        'section' => $section
                    ])->with('success', 'Program removed successfully.');
                }

                if ($request->filled('editing_program_id') || $request->filled('title')) {
                    $data = $request->validate([
                        'editing_program_id' => ['nullable', 'integer'],
                        'title' => ['required', 'string', 'max:255'],
                        'description' => ['nullable', 'string'],
                        'image' => ['nullable', 'image', 'max:2048'],
                        'numbered_content' => ['nullable', 'array'],
                        'numbered_content.*.label' => ['nullable', 'string', 'max:255'],
                        'numbered_content.*.text' => ['nullable', 'string'],
                    ]);

                    $program = $request->filled('editing_program_id')
                        ? DepartmentProgram::where('department_id', $department->id)->findOrFail($request->input('editing_program_id'))
                        : new DepartmentProgram(['department_id' => $department->id]);

                    $imagePath = $program->image;
                    if ($request->hasFile('image')) {
                        $file = $request->file('image');
                        $filename = time() . '_program_' . Str::slug($data['title']) . '.' . $file->getClientOriginalExtension();
                        $storedPath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs(
                            "colleges/{$college}/departments/" . Str::slug($department->name) . "/programs",
                            $file,
                            $filename
                        );
                        $imagePath = $storedPath ? \Illuminate\Support\Facades\Storage::disk('google')->url($storedPath) : $imagePath;
                    }

                    $numberedContent = collect($data['numbered_content'] ?? [])
                        ->filter(fn ($item) => !empty($item['label'] ?? null) || !empty($item['text'] ?? null))
                        ->values()
                        ->all();

                    $program->fill([
                        'title' => $data['title'],
                        'description' => $data['description'] ?? '',
                        'image' => $imagePath,
                        'numbered_content' => $numberedContent ?: null,
                        'sort_order' => $program->exists ? $program->sort_order : ((int) DepartmentProgram::where('department_id', $department->id)->max('sort_order') + 1),
                    ]);
                    $program->save();

                    return redirect()->route('admin.colleges.show-department', [
                        'college' => $college,
                        'department' => $departmentId,
                        'section' => $section
                    ])->with('success', $request->filled('editing_program_id') ? 'Program updated successfully.' : 'Program added successfully.');
                }

                $programs = $request->input('programs', []);
                
                $department->programs()->delete();

                if (is_array($programs)) {
                    foreach ($programs as $index => $item) {
                        $existingImage = $item['existing_image'] ?? null;
                        $imagePath = $existingImage;
                        
                        if ($request->hasFile("programs.{$index}.image")) {
                            $file = $request->file("programs.{$index}.image");
                            $filename = time() . '_program_' . $index . '_' . Str::slug($department->name) . '.' . $file->getClientOriginalExtension();
                            $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($department->name) . "/programs", $file, $filename);
                            
                            if ($existingImage && file_exists(public_path($existingImage))) {
                                @unlink(public_path($existingImage));
                            }
                            $imagePath = $imagePath ? \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath) : $existingImage;
                        }

                        \App\Models\DepartmentProgram::create([
                            'department_id' => $department->id,
                            'title' => $item['title'] ?? null,
                            'numbering' => $item['numbering'] ?? null,
                            'description' => $item['description'] ?? '',
                            'numbered_content' => isset($item['numbered_content']) ? $item['numbered_content'] : null,
                            'image' => $imagePath,
                            'sort_order' => $index,
                            'created_at' => $item['created_at'] ?? now(),
                        ]);
                    }
                }

                $department->programs_is_visible = $request->has('is_visible');
                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section
                ])->with('success', 'Programs updated successfully.');
            }

            // Handle objectives edit mode
            if ($section === 'programs' && $request->has('_programs_section_edit')) {
                $data = $request->validate([
                    'title' => ['required', 'string', 'max:255'],
                    'body' => ['nullable', 'string'],
                    'is_visible' => ['nullable', 'boolean'],
                ]);

                $department->programs_title = $data['title'];
                $department->programs_body = $data['body'] ?? '';
                $department->programs_is_visible = $request->has('is_visible');
                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section,
                ])->with('success', 'Programs section details updated successfully.');
            }

            if ($section === 'faculty' && $request->has('_faculty_section_edit')) {
                $data = $request->validate([
                    'title' => ['required', 'string', 'max:255'],
                    'body' => ['nullable', 'string'],
                    'is_visible' => ['nullable', 'boolean'],
                ]);

                $department->faculty_title = $data['title'];
                $department->faculty_body = $data['body'] ?? '';
                $department->faculty_is_visible = $request->has('is_visible');
                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section,
                ])->with('success', 'Faculty section details updated successfully.');
            }

            // Handle objectives edit mode
            if ($request->has('_objectives_section_edit')) {
                $data = $request->validate([
                    'title' => ['required', 'string', 'max:255'],
                    'body' => ['nullable', 'string'],
                    'is_visible' => ['nullable', 'boolean'],
                ]);

                $department->objectives_title = $data['title'];
                $department->objectives_body = $data['body'] ?? '';
                $department->objectives_is_visible = $request->has('is_visible');
                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section,
                ])->with('success', 'Objectives section details updated successfully.');
            }

            if ($request->has('_objectives_edit')) {
                $data = $request->validate([
                    'new_objective_content' => ['nullable', 'string'],
                    'new_objective_sort' => ['nullable', 'integer'],
                    'editing_objective_id' => ['nullable', 'integer'],
                    'objectives' => ['nullable', 'array'],
                    'objectives.*.content' => ['required', 'string'],
                    'objectives.*.sort_order' => ['nullable', 'integer'],
                ]);

                // Handle Department Objectives (Update Existing)
                if ($request->has('objectives')) {
                    foreach ($request->input('objectives') as $id => $data) {
                        $objective = DepartmentObjective::where('department_id', $department->id)->find($id);
                        if ($objective) {
                            $objective->update([
                                'content' => $data['content'],
                                'sort_order' => $data['sort_order'] ?? 0,
                            ]);
                        }
                    }
                }

                // Handle Department Objectives (Delete)
                if ($request->has('delete_objective')) {
                    $objectiveId = $request->input('delete_objective');
                    $objective = DepartmentObjective::where('department_id', $department->id)->find($objectiveId);
                    if ($objective) {
                        $objective->delete();
                        return redirect()->route('admin.colleges.show-department', [
                            'college' => $college,
                            'department' => $department,
                            'section' => $section,
                        ])->with('success', 'Objective deleted successfully.');
                    }
                }

                // Handle Department Objective (Update Single)
                if ($request->filled('editing_objective_id') && $request->filled('new_objective_content')) {
                    $objective = DepartmentObjective::where('department_id', $department->id)
                        ->find($request->input('editing_objective_id'));

                    if ($objective) {
                        $objective->update([
                            'content' => $request->input('new_objective_content'),
                            'sort_order' => $request->input('new_objective_sort', 0),
                        ]);

                        return redirect()->route('admin.colleges.show-department', [
                            'college' => $college,
                            'department' => $department,
                            'section' => $section,
                        ])->with('success', 'Objective updated successfully.');
                    }
                }

                // Handle Department Objectives (Create)
                if ($request->filled('new_objective_content')) {
                    DepartmentObjective::create([
                        'department_id' => $department->id,
                        'content' => $request->input('new_objective_content'),
                        'sort_order' => $request->input('new_objective_sort', 0),
                    ]);
                }
                
                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $department,
                    'section' => $section,
                ])->with('success', 'Objective added successfully.');
            }

            // Handle curriculum edit mode
            if ($request->has('_curriculum_edit')) {
                $data = $request->validate([
                    'title' => ['nullable', 'string', 'max:255'],
                    'courses' => ['nullable', 'string'],
                    'curriculum_title' => ['nullable', 'string', 'max:255'],
                    'curriculum_body' => ['nullable', 'string'],
                    'editing_curriculum_id' => ['nullable', 'integer'],
                    'delete_curriculum' => ['nullable', 'integer'],
                    'curriculum' => ['nullable', 'array'],
                    'curriculum.*.title' => ['required', 'string', 'max:255'],
                    'curriculum.*.courses' => ['nullable', 'string'],
                ]);

                if ($request->filled('delete_curriculum')) {
                    $curriculum = \App\Models\DepartmentCurriculum::where('department_id', $department->id)
                        ->find($request->input('delete_curriculum'));

                    if ($curriculum) {
                        $curriculum->delete();
                    }

                    return redirect()->route('admin.colleges.show-department', [
                        'college' => $college,
                        'department' => $department,
                        'section' => $section,
                    ])->with('success', 'Curriculum category removed successfully.');
                }

                if ($request->filled('editing_curriculum_id') && $request->filled('title')) {
                    $curriculum = \App\Models\DepartmentCurriculum::where('department_id', $department->id)
                        ->find($request->input('editing_curriculum_id'));

                    if ($curriculum) {
                        $curriculum->update([
                            'title' => $request->input('title'),
                            'courses' => $request->input('courses', ''),
                        ]);
                    }

                    return redirect()->route('admin.colleges.show-department', [
                        'college' => $college,
                        'department' => $department,
                        'section' => $section,
                    ])->with('success', 'Curriculum category updated successfully.');
                }

                if ($request->filled('title')) {
                    $nextSortOrder = ($department->curricula()->max('sort_order') ?? -1) + 1;

                    $department->curricula()->create([
                        'title' => $request->input('title'),
                        'courses' => $request->input('courses', ''),
                        'sort_order' => $nextSortOrder,
                    ]);

                    return redirect()->route('admin.colleges.show-department', [
                        'college' => $college,
                        'department' => $department,
                        'section' => $section,
                    ])->with('success', 'Curriculum category added successfully.');
                }

                // Handle Curriculum (Save to department_curricula table)
                $department->update([
                    'curriculum_title' => $data['curriculum_title'] ?? null,
                    'curriculum_body' => $data['curriculum_body'] ?? null,
                ]);

                if ($request->has('curriculum') && is_array($request->input('curriculum'))) {
                    // Delete existing curricula for this department to refresh
                    $department->curricula()->delete();
                    
                    foreach ($request->input('curriculum') as $index => $category) {
                        if (empty($category['title'])) continue;

                        $department->curricula()->create([
                            'title' => $category['title'],
                            'courses' => $category['courses'] ?? '',
                            'sort_order' => $index,
                        ]);
                    }
                }
                
                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $department,
                    'section' => $section,
                ])->with('success', 'Curriculum updated successfully.');
            }

            // Handle graduate outcomes edit mode
            if ($request->has('_graduate_outcomes_edit')) {
                $data = $request->validate([
                    'graduate_outcomes_title' => ['nullable', 'string', 'max:255'],
                    'graduate_outcomes' => ['nullable', 'string'],
                    'graduate_outcomes_image' => ['nullable', 'image', 'max:2048'],
                    'editing_outcome_id' => ['nullable', 'integer'],
                    'new_outcome_title' => ['nullable', 'string', 'max:255'],
                    'new_outcome_description' => ['nullable', 'string'],
                    'new_outcome_image' => ['nullable', 'image', 'max:2048'],
                    'new_outcome_sort' => ['nullable', 'integer'],
                ]);

                // Update Title & Description
                $department->graduate_outcomes_title = $data['graduate_outcomes_title'] ?? null;
                $department->graduate_outcomes = $data['graduate_outcomes'] ?? null;

                // Handle Section Image
                if ($request->hasFile('graduate_outcomes_image')) {
                    if ($department->graduate_outcomes_image && file_exists(public_path($department->graduate_outcomes_image))) {
                        @unlink(public_path($department->graduate_outcomes_image));
                    }

                    $file = $request->file('graduate_outcomes_image');
                    $filename = time() . '_grad_outcomes_' . Str::slug($department->name) . '.' . $file->getClientOriginalExtension();
                    $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($department->name) . "/graduate-outcomes", $file, $filename);

                    if ($imagePath) {
                        $fileId = app(\App\Services\GoogleDriveService::class)->getFileId($imagePath);
                        $department->graduate_outcomes_image = 'media/proxy/' . ($fileId ?? $imagePath);
                    }
                }

                if ($request->has('delete_graduate_outcomes_image')) {
                    if ($department->graduate_outcomes_image && file_exists(public_path($department->graduate_outcomes_image))) {
                        @unlink(public_path($department->graduate_outcomes_image));
                    }
                    $department->graduate_outcomes_image = null;
                }

                // Handle Department Outcomes (Delete)
                if ($request->has('delete_outcome')) {
                    $outcomeId = $request->input('delete_outcome');
                    $outcome = DepartmentOutcome::where('department_id', $department->id)->find($outcomeId);
                    if ($outcome) {
                         if ($outcome->image && file_exists(public_path($outcome->image))) {
                            unlink(public_path($outcome->image));
                        }
                        $outcome->delete();
                        return redirect()->back()->with('success', 'Outcome deleted successfully.');
                    }
                }

                // Handle Department Outcomes (Update)
                if ($request->filled('editing_outcome_id') && $request->filled('new_outcome_title')) {
                    $outcome = DepartmentOutcome::where('department_id', $department->id)
                        ->find($request->input('editing_outcome_id'));

                    if ($outcome) {
                        $outcome->title = $request->input('new_outcome_title');
                        $outcome->description = $request->input('new_outcome_description');
                        $outcome->sort_order = $request->input('new_outcome_sort', 0);

                        if ($request->hasFile('new_outcome_image')) {
                            if ($outcome->image && file_exists(public_path($outcome->image))) {
                                unlink(public_path($outcome->image));
                            }

                            $file = $request->file('new_outcome_image');
                            $filename = time() . '_outcome_' . Str::slug($outcome->title) . '.' . $file->getClientOriginalExtension();
                            $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($department->name) . "/graduate-outcomes/items", $file, $filename);

                            if ($imagePath) {
                                $fileId = app(\App\Services\GoogleDriveService::class)->getFileId($imagePath);
                                $outcome->image = 'media/proxy/' . ($fileId ?? $imagePath);
                            }
                        }

                        $outcome->save();

                        $department->save();

                        return redirect()->route('admin.colleges.show-department', [
                            'college' => $college,
                            'department' => $departmentId,
                            'section' => 'overview',
                        ])->with('success', 'Outcome updated successfully.');
                    }
                }

                // Handle Department Outcomes (Create)
                if ($request->filled('new_outcome_title')) {
                    $outcomeTitle = $request->input('new_outcome_title');
                    $outcomeDesc = $request->input('new_outcome_description');
                    $outcomeSort = $request->input('new_outcome_sort', 0);

                    $imagePath = null;
                    if ($request->hasFile('new_outcome_image')) {
                        $file = $request->file('new_outcome_image');
                        $filename = time() . '_outcome_' . Str::slug($outcomeTitle) . '.' . $file->getClientOriginalExtension();
                        $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($department->name) . "/graduate-outcomes/items", $file, $filename);
                        if ($imagePath) {
                            $fileId = app(\App\Services\GoogleDriveService::class)->getFileId($imagePath);
                            $imagePath = 'media/proxy/' . ($fileId ?? $imagePath);
                        }
                    }

                    DepartmentOutcome::create([
                        'department_id' => $department->id,
                        'title' => $outcomeTitle,
                        'description' => $outcomeDesc,
                        'image' => $imagePath,
                        'sort_order' => $outcomeSort,
                    ]);

                    $department->save();

                    return redirect()->route('admin.colleges.show-department', [
                        'college' => $college,
                        'department' => $departmentId,
                        'section' => 'overview',
                    ])->with('success', 'Outcome added successfully.');
                }

                $department->save();

                return redirect()->back()->with('success', 'Graduate outcomes updated successfully.');
            }

            // Handle card image edit mode
            if ($request->has('_card_edit')) {
                $data = $request->validate([
                    'card_image' => ['nullable', 'image', 'max:2048'],
                ]);

                $currentSection = $department->getSection($section) ?? [];

                if ($request->has('delete_card_image')) {
                    if (!empty($currentSection['card_image']) && file_exists(public_path($currentSection['card_image']))) {
                        unlink(public_path($currentSection['card_image']));
                    }

                    $currentSection['card_image'] = null;
                }

                // Handle card image upload
                if ($request->hasFile('card_image')) {
                    // Delete old card image if exists
                    if (!empty($currentSection['card_image']) && file_exists(public_path($currentSection['card_image']))) {
                        unlink(public_path($currentSection['card_image']));
                    }

                    $file = $request->file('card_image');
                    $filename = time() . '_card_' . Str::slug($department->name) . '.' . $file->getClientOriginalExtension();
                    $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs(
                        "colleges/{$college}/departments/" . Str::slug($department->name) . "/cards",
                        $file,
                        $filename
                    );
                    if ($imagePath) {
                        $fileId = app(\App\Services\GoogleDriveService::class)->getFileId($imagePath);
                        $currentSection['card_image'] = 'media/proxy/' . ($fileId ?? $imagePath);
                    }
                }

                $department->setSection($section, $currentSection);
                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section
                ])->with('success', 'Card image updated successfully.');
            }

            if ($request->has('_retro_edit')) {
                $retroData = $request->validate([
                    'retro_title' => ['nullable', 'string', 'max:255'],
                    'retro_description' => ['nullable', 'string'],
                    'retro_stamp' => ['nullable', 'string', 'max:100'],
                    'hero_background_image' => ['nullable', 'image', 'max:2048'],
                    'retro_id' => ['nullable', 'integer'],
                ]);

                $retroId = $request->input('retro_id');

                if (! $retroId) {
                    $count = CollegeRetro::where('college_slug', $college)
                        ->where('department_id', $department->id)
                        ->count();

                    if ($count >= 4) {
                        return redirect()
                            ->back()
                            ->with('error', 'Maximum 4 retro items allowed.');
                    }
                }

                $heroImage = null;
                $existingRetro = null;

                if ($retroId) {
                    $existingRetro = CollegeRetro::where('college_slug', $college)
                        ->where('department_id', $department->id)
                        ->find($retroId);

                    if ($existingRetro) {
                        $heroImage = $existingRetro->background_image;
                    }
                }

                if ($request->hasFile('hero_background_image')) {
                    if ($retroId && $existingRetro && ! empty($existingRetro->background_image)) {
                        $existingPath = str_starts_with($existingRetro->background_image, 'media/proxy/')
                            ? substr($existingRetro->background_image, strlen('media/proxy/'))
                            : $existingRetro->background_image;

                        if (\Illuminate\Support\Facades\Storage::disk('google')->exists($existingPath)) {
                            \Illuminate\Support\Facades\Storage::disk('google')->delete($existingPath);
                        }
                    }

                    $file = $request->file('hero_background_image');
                    $filename = time() . '_hero_' . Str::slug($department->name) . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs(
                        "colleges/{$college}/departments/" . Str::slug($department->name) . "/retro",
                        $file,
                        $filename
                    );

                    if ($imagePath) {
                        $fileId = app(\App\Services\GoogleDriveService::class)->getFileId($imagePath);
                        $heroImage = 'media/proxy/' . ($fileId ?? $imagePath);
                    }
                }

                CollegeRetro::updateOrCreate(
                    [
                        'id' => $retroId,
                        'college_slug' => $college,
                        'department_id' => $department->id,
                    ],
                    [
                        'title' => $retroData['retro_title'] ?? null,
                        'description' => $retroData['retro_description'] ?? null,
                        'stamp' => $retroData['retro_stamp'] ?? null,
                        'background_image' => $heroImage,
                        'sort_order' => $retroId
                            ? ($existingRetro->sort_order ?? 0)
                            : ((int) CollegeRetro::where('college_slug', $college)
                                ->where('department_id', $department->id)
                                ->max('sort_order')) + 1,
                    ]
                );

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section,
                ])->with('success', 'Retro item saved successfully.');
            }

            if ($section === 'overview' && $request->input('_edit_mode') === 'delete_retro') {
                $retroId = $request->input('retro_id');
                $retro = CollegeRetro::where('college_slug', $college)
                    ->where('department_id', $department->id)
                    ->find($retroId);

                if ($retro) {
                    if (! empty($retro->background_image)) {
                        $existingPath = str_starts_with($retro->background_image, 'media/proxy/')
                            ? substr($retro->background_image, strlen('media/proxy/'))
                            : $retro->background_image;

                        if (\Illuminate\Support\Facades\Storage::disk('google')->exists($existingPath)) {
                            \Illuminate\Support\Facades\Storage::disk('google')->delete($existingPath);
                        }
                    }

                    $retro->delete();

                    return redirect()->route('admin.colleges.show-department', [
                        'college' => $college,
                        'department' => $departmentId,
                        'section' => $section,
                    ])->with('success', 'Retro item removed successfully.');
                }

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section,
                ])->with('error', 'Retro item not found.');
            }

            // Handle awards edit mode
            if ($request->has('_awards_edit')) {
                // Check Individual Delete layout
                if ($request->has('delete_award')) {
                    $awardId = $request->input('delete_award');
                    $award = \App\Models\DepartmentAward::where('department_id', $department->id)->find($awardId);
                    if ($award) {
                        if ($award->image) {
                            // Extract ID from Google drive URL layout: id=[file_id]
                            if (preg_match('/id=([a-zA-Z0-9_-]+)/', $award->image, $matches)) {
                                \Illuminate\Support\Facades\Storage::disk('google')->delete($matches[1]);
                            }
                        }
                        $award->delete();
                    }
                    return redirect()->back()->with('success', 'Award deleted successfully.');
                }

                // Check Individual Add / Edit Form layout
                if ($request->has('award_title')) {
                    $awardId = $request->input('award_id');
                    $data = $request->validate([
                        'award_title' => 'required|string|max:255',
                        'award_description' => 'nullable|string',
                        'award_image' => 'nullable|image|max:10240', // 10MB
                    ]);

                    $imagePath = null;
                    if ($request->hasFile('award_image')) {
                        $file = $request->file('award_image');
                        $filename = time() . '_award_' . Str::slug($data['award_title']) . '.' . $file->getClientOriginalExtension();
                        $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($department->name) . "/awards/" . Str::slug($data['award_title']), $file, $filename);
                    }

                    if ($awardId) {
                        // Edit Mode
                        $award = \App\Models\DepartmentAward::where('department_id', $department->id)->findOrFail($awardId);
                        $award->title = $data['award_title'];
                        $award->description = $data['award_description'] ?? '';
                        if ($imagePath) {
                            if ($award->image && preg_match('/id=([a-zA-Z0-9_-]+)/', $award->image, $matches)) {
                                \Illuminate\Support\Facades\Storage::disk('google')->delete($matches[1]);
                            }
                            $award->image = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                        }
                        $award->save();
                        $msg = 'Award updated successfully.';
                    } else {
                        // Add Mode
                        \App\Models\DepartmentAward::create([
                            'department_id' => $department->id,
                            'title' => $data['award_title'],
                            'description' => $data['award_description'] ?? '',
                            'image' => $imagePath ? \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath) : null,
                            'sort_order' => \App\Models\DepartmentAward::where('department_id', $department->id)->count(),
                        ]);
                        $msg = 'Award added successfully.';
                    }

                    return redirect()->back()->with('success', $msg);
                }

                // Overall Visibility Toggle layout fallback (Batch rebuild removed)
                // Handle Section Details Edit (Title & Description)
                $data = $request->validate([
                    'title' => 'nullable|string|max:255',
                    'body' => 'nullable|string',
                ]);

                $department->awards_title = $data['title'] ?? null;
                $department->awards_body = $data['body'] ?? null;
                $department->awards_is_visible = $request->has('is_visible');
                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section
                ])->with('success', 'Awards section details updated.');
            }

            // Handle research delete
            if ($request->has('delete_research')) {
                $researchId = $request->input('delete_research');
                $item = \App\Models\DepartmentResearch::where('department_id', $department->id)->find($researchId);
                if ($item) {
                    if ($item->image && preg_match('/id=([a-zA-Z0-9_-]+)/', $item->image, $matches)) {
                        \Illuminate\Support\Facades\Storage::disk('google')->delete($matches[1]);
                    }
                    $item->delete();
                }
                return redirect()->back()->with('success', 'Research deleted successfully.');
            }

            // Handle individual Add / Edit
            if ($request->has('research_title')) {
                $researchId = $request->input('research_id');
                $data = $request->validate([
                    'research_title' => 'required|string|max:255',
                    'research_description' => 'nullable|string',
                    'research_completed_year' => 'nullable|string|max:255',
                    'research_image' => 'nullable|image|max:10240', // 10MB
                ]);

                $imagePath = null;
                if ($request->hasFile('research_image')) {
                    $file = $request->file('research_image');
                    $filename = time() . '_research_' . Str::slug($data['research_title']) . '.' . $file->getClientOriginalExtension();
                    $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($department->name) . "/research/" . Str::slug($data['research_title']), $file, $filename);
                }

                if ($researchId) {
                    // Edit Mode
                    $item = \App\Models\DepartmentResearch::where('department_id', $department->id)->findOrFail($researchId);
                    $item->title = $data['research_title'];
                    $item->description = $data['research_description'] ?? '';
                    $item->completed_year = $data['research_completed_year'] ?? null;
                    if ($imagePath) {
                        if ($item->image && preg_match('/id=([a-zA-Z0-9_-]+)/', $item->image, $matches)) {
                            \Illuminate\Support\Facades\Storage::disk('google')->delete($matches[1]);
                        }
                        $item->image = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                    }
                    $item->save();
                    $msg = 'Research updated successfully.';
                } else {
                    // Add Mode
                    \App\Models\DepartmentResearch::create([
                        'department_id' => $department->id,
                        'title' => $data['research_title'],
                        'description' => $data['research_description'] ?? '',
                        'completed_year' => $data['research_completed_year'] ?? null,
                        'image' => $imagePath ? \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath) : null,
                        'sort_order' => \App\Models\DepartmentResearch::where('department_id', $department->id)->count(),
                    ]);
                    $msg = 'Research added successfully.';
                }

                return redirect()->back()->with('success', $msg);
            }

            // Handle Research section details
            if ($request->has('_research_edit')) {
                $data = $request->validate([
                    'title' => ['required', 'string', 'max:255'],
                    'body' => ['nullable', 'string'],
                ]);

                $department->research_title = $data['title'];
                $department->research_body = $data['body'] ?? '';
                $department->research_is_visible = $request->has('is_visible');
                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section,
                ])->with('success', 'Research section details updated.');
            }

            // Handle Extension Section
            if ($request->has('_extension_section_edit')) {
                $data = $request->validate([
                    'title' => ['required', 'string', 'max:255'],
                    'body' => ['nullable', 'string'],
                ]);

                $department->extension_title = $data['title'];
                $department->extension_body = $data['body'] ?? '';
                $department->extension_is_visible = $request->has('is_visible');
                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section
                ])->with('success', 'Extension section details updated successfully.');
            }

            if ($request->has('_extension_edit')) {
                $data = $request->validate([
                    'editing_extension_id' => ['nullable', 'integer'],
                    'delete_extension' => ['nullable', 'integer'],
                    'title' => ['nullable', 'string', 'max:255'],
                    'description' => ['nullable', 'string'],
                    'image' => ['nullable', 'image', 'max:2048'],
                    'extension' => ['array'],
                    'extension.*.title' => ['required', 'string', 'max:255'],
                    'extension.*.description' => ['nullable', 'string'],
                    'extension.*.image' => ['nullable', 'image', 'max:2048'],
                    'extension.*.existing_image' => ['nullable', 'string'],
                    'extension.*.created_at' => ['nullable', 'date'],
                    'is_visible' => ['nullable', 'boolean'],
                ]);

                $extensionItems = $request->input('extension', []);

                if ($request->filled('delete_extension')) {
                    $item = \App\Models\DepartmentExtension::where('department_id', $department->id)
                        ->find($request->input('delete_extension'));

                    if ($item) {
                        if ($item->image && file_exists(public_path($item->image))) {
                            @unlink(public_path($item->image));
                        }

                        $item->delete();
                    }

                    return redirect()->route('admin.colleges.show-department', [
                        'college' => $college,
                        'department' => $departmentId,
                        'section' => $section
                    ])->with('success', 'Extension activity removed successfully.');
                }

                if ($request->filled('editing_extension_id') && $request->filled('title')) {
                    $item = \App\Models\DepartmentExtension::where('department_id', $department->id)
                        ->find($request->input('editing_extension_id'));

                    if ($item) {
                        $imagePath = $item->image;

                        if ($request->hasFile('image')) {
                            if ($imagePath && file_exists(public_path($imagePath))) {
                                @unlink(public_path($imagePath));
                            }

                            $file = $request->file('image');
                            $filename = time() . '_ext_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                            $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs(
                                "colleges/{$college}/departments/" . Str::slug($department->name) . "/extension/" . Str::slug($request->input('title')),
                                $file,
                                $filename
                            );

                            if ($imagePath) {
                                $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                            }
                        }

                        $item->update([
                            'title' => $request->input('title'),
                            'description' => $request->input('description', ''),
                            'image' => $imagePath,
                        ]);
                    }

                    return redirect()->route('admin.colleges.show-department', [
                        'college' => $college,
                        'department' => $departmentId,
                        'section' => $section
                    ])->with('success', 'Extension activity updated successfully.');
                }
                
                $department->extension()->delete();

                foreach ($extensionItems as $index => $item) {
                    $imagePath = $item['existing_image'] ?? null;

                    if ($request->hasFile("extension.{$index}.image")) {
                        if ($imagePath && file_exists(public_path($imagePath))) {
                            unlink(public_path($imagePath));
                        }
                        $file = $request->file("extension.{$index}.image");
                        $filename = time() . '_ext_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                        $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($department->name) . "/extension/" . Str::slug($item['title'] ?? 'item'), $file, $filename);
                        if ($imagePath) {
                            $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                        }
                    } elseif (isset($item['remove_image']) && $item['remove_image'] == '1') {
                        if ($imagePath && file_exists(public_path($imagePath))) {
                            unlink(public_path($imagePath));
                        }
                        $imagePath = null;
                    }

                    \App\Models\DepartmentExtension::create([
                        'department_id' => $department->id,
                        'title' => $item['title'],
                        'description' => $item['description'] ?? '',
                        'image' => $imagePath,
                        'sort_order' => $index,
                        'created_at' => $item['created_at'] ?? now(),
                    ]);
                }

                $department->extension_is_visible = $request->has('is_visible');
                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section
                ])->with('success', 'Extension section updated successfully.');
            }

            // Handle Training Section
            if ($request->has('_training_section_edit')) {
                $data = $request->validate([
                    'title' => ['required', 'string', 'max:255'],
                    'body' => ['nullable', 'string'],
                ]);

                $department->training_title = $data['title'];
                $department->training_body = $data['body'] ?? '';
                $department->training_is_visible = $request->has('is_visible');
                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section
                ])->with('success', 'Training section details updated successfully.');
            }

            if ($request->has('_training_edit')) {
                $data = $request->validate([
                    'editing_training_id' => ['nullable', 'integer'],
                    'delete_training' => ['nullable', 'integer'],
                    'title' => ['nullable', 'string', 'max:255'],
                    'description' => ['nullable', 'string'],
                    'image' => ['nullable', 'image', 'max:2048'],
                    'training' => ['array'],
                    'training.*.title' => ['required', 'string', 'max:255'],
                    'training.*.description' => ['nullable', 'string'],
                    'training.*.image' => ['nullable', 'image', 'max:2048'],
                    'training.*.existing_image' => ['nullable', 'string'],
                    'training.*.created_at' => ['nullable', 'date'],
                    'is_visible' => ['nullable', 'boolean'],
                ]);

                $trainingItems = $request->input('training', []);

                if ($request->filled('delete_training')) {
                    $item = \App\Models\DepartmentTraining::where('department_id', $department->id)
                        ->find($request->input('delete_training'));

                    if ($item) {
                        if ($item->image && file_exists(public_path($item->image))) {
                            @unlink(public_path($item->image));
                        }

                        $item->delete();
                    }

                    return redirect()->route('admin.colleges.show-department', [
                        'college' => $college,
                        'department' => $departmentId,
                        'section' => $section
                    ])->with('success', 'Training item removed successfully.');
                }

                if ($request->filled('editing_training_id') && $request->filled('title')) {
                    $item = \App\Models\DepartmentTraining::where('department_id', $department->id)
                        ->find($request->input('editing_training_id'));

                    if ($item) {
                        $imagePath = $item->image;

                        if ($request->hasFile('image')) {
                            if ($imagePath && file_exists(public_path($imagePath))) {
                                @unlink(public_path($imagePath));
                            }

                            $file = $request->file('image');
                            $filename = time() . '_train_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                            $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs(
                                "colleges/{$college}/departments/" . Str::slug($department->name) . "/training/" . Str::slug($request->input('title')),
                                $file,
                                $filename
                            );

                            if ($imagePath) {
                                $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                            }
                        }

                        $item->update([
                            'title' => $request->input('title'),
                            'description' => $request->input('description', ''),
                            'image' => $imagePath,
                        ]);
                    }

                    return redirect()->route('admin.colleges.show-department', [
                        'college' => $college,
                        'department' => $departmentId,
                        'section' => $section
                    ])->with('success', 'Training item updated successfully.');
                }
                
                $department->training()->delete();

                foreach ($trainingItems as $index => $item) {
                    $imagePath = $item['existing_image'] ?? null;

                    if ($request->hasFile("training.{$index}.image")) {
                        if ($imagePath && file_exists(public_path($imagePath))) {
                            unlink(public_path($imagePath));
                        }
                        $file = $request->file("training.{$index}.image");
                        $filename = time() . '_train_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                        $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($department->name) . "/training/" . Str::slug($item['title'] ?? 'item'), $file, $filename);
                        if ($imagePath) {
                            $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                        }
                    } elseif (isset($item['remove_image']) && $item['remove_image'] == '1') {
                        if ($imagePath && file_exists(public_path($imagePath))) {
                            unlink(public_path($imagePath));
                        }
                        $imagePath = null;
                    }

                    \App\Models\DepartmentTraining::create([
                        'department_id' => $department->id,
                        'title' => $item['title'],
                        'description' => $item['description'] ?? '',
                        'image' => $imagePath,
                        'sort_order' => $index,
                        'created_at' => $item['created_at'] ?? now(),
                    ]);
                }

                $department->training_is_visible = $request->has('is_visible');
                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section
                ])->with('success', 'Training section updated successfully.');
            }

            // Handle Facilities Section Details
            if ($section === 'facilities' && $request->has('_facilities_edit')) {
                $data = $request->validate([
                    'title' => ['required', 'string', 'max:255'],
                    'body' => ['nullable', 'string'],
                    'is_visible' => ['nullable', 'boolean'],
                ]);
                
                $department->facilities_title = $data['title'];
                $department->facilities_body = $data['body'] ?? '';
                $department->facilities_is_visible = $request->has('is_visible');
                $department->setSection('facilities', [
                    'title' => $data['title'],
                    'body' => $data['body'] ?? '',
                    'is_visible' => $request->has('is_visible'),
                    'items' => $department->getSection('facilities')['items'] ?? [],
                ]);
                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section
                ])->with('success', 'Facilities details saved.');
            }

            if ($section === 'membership' && $request->has('_membership_section_edit')) {
                $data = $request->validate([
                    'title' => ['required', 'string', 'max:255'],
                    'body' => ['nullable', 'string'],
                    'is_visible' => ['nullable', 'boolean'],
                ]);

                $department->setSection('membership', [
                    'title' => $data['title'],
                    'body' => $data['body'] ?? '',
                    'is_visible' => $request->has('is_visible'),
                ]);
                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section,
                ])->with('success', 'Membership section details updated successfully.');
            }

            if ($request->has('_alumni_section_edit')) {
                $data = $request->validate([
                    'title' => ['required', 'string', 'max:255'],
                    'body' => ['nullable', 'string'],
                    'is_visible' => ['nullable', 'boolean'],
                ]);

                $department->setSection('alumni', [
                    'title' => $data['title'],
                    'body' => $data['body'] ?? '',
                    'is_visible' => $request->has('is_visible'),
                ]);
                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section,
                ])->with('success', 'Alumni section details updated successfully.');
            }

            // Handle Alumni Section
            if ($request->has('_alumni_edit')) {
                if ($request->filled('editing_alumnus_id') || $request->filled('title')) {
                    $data = $request->validate([
                        'editing_alumnus_id' => ['nullable', 'integer'],
                        'title' => ['required', 'string', 'max:255'],
                        'year_graduated' => ['nullable', 'string', 'max:255'],
                        'description' => ['nullable', 'string'],
                        'image' => ['nullable', 'image', 'max:2048'],
                        'remove_image' => ['nullable', 'boolean'],
                    ]);

                    $alumnus = $request->filled('editing_alumnus_id')
                        ? DepartmentAlumnus::where('department_id', $department->id)->findOrFail($request->input('editing_alumnus_id'))
                        : new DepartmentAlumnus(['department_id' => $department->id]);

                    $imagePath = $alumnus->image;

                    if ($request->boolean('remove_image')) {
                        $imagePath = null;
                    }

                    if ($request->hasFile('image')) {
                        $file = $request->file('image');
                        $filename = time() . '_alumni_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                        $storedPath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs(
                            "colleges/{$college}/departments/" . Str::slug($department->name) . "/alumni/" . Str::slug($data['title']),
                            $file,
                            $filename
                        );
                        $imagePath = $storedPath ? \Illuminate\Support\Facades\Storage::disk('google')->url($storedPath) : $imagePath;
                    }

                    $alumnus->fill([
                        'title' => $data['title'],
                        'year_graduated' => $data['year_graduated'] ?? null,
                        'description' => $data['description'] ?? '',
                        'image' => $imagePath,
                        'sort_order' => $alumnus->exists ? $alumnus->sort_order : ((int) DepartmentAlumnus::where('department_id', $department->id)->max('sort_order') + 1),
                    ]);
                    $alumnus->save();

                    return redirect()->route('admin.colleges.show-department', [
                        'college' => $college,
                        'department' => $departmentId,
                        'section' => $section
                    ])->with('success', $request->filled('editing_alumnus_id') ? 'Alumnus updated successfully.' : 'Alumnus added successfully.');
                }

                $data = $request->validate([
                    'alumni' => ['array'],
                    'alumni.*.title' => ['required', 'string', 'max:255'],
                    'alumni.*.year_graduated' => ['nullable', 'string', 'max:255'],
                    'alumni.*.description' => ['nullable', 'string'],
                    'alumni.*.image' => ['nullable', 'image', 'max:2048'],
                    'alumni.*.existing_image' => ['nullable', 'string'],
                    'alumni.*.created_at' => ['nullable', 'date'],
                    'is_visible' => ['nullable', 'boolean'],
                ]);

                $alumniItems = $request->input('alumni', []);
                
                $department->alumni()->delete();

                foreach ($alumniItems as $index => $item) {
                    $imagePath = $item['existing_image'] ?? null;

                    if ($request->hasFile("alumni.{$index}.image")) {
                        if ($imagePath && file_exists(public_path($imagePath))) {
                            unlink(public_path($imagePath));
                        }
                        $file = $request->file("alumni.{$index}.image");
                        $filename = time() . '_alumni_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                        $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($department->name) . "/alumni/" . Str::slug($item['title'] ?? 'item'), $file, $filename);
                        if ($imagePath) {
                            $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                        }
                    } elseif (isset($item['remove_image']) && $item['remove_image'] == '1') {
                        if ($imagePath && file_exists(public_path($imagePath))) {
                            unlink(public_path($imagePath));
                        }
                        $imagePath = null;
                    }

                    \App\Models\DepartmentAlumnus::create([
                        'department_id' => $department->id,
                        'title' => $item['title'],
                        'year_graduated' => $item['year_graduated'] ?? null,
                        'description' => $item['description'] ?? '',
                        'image' => $imagePath,
                        'sort_order' => $index,
                        'created_at' => $item['created_at'] ?? now(),
                    ]);
                }

                $department->alumni_is_visible = $request->has('is_visible');
                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section
                ])->with('success', 'Alumni section updated successfully.');
            }

            // Handle Linkages Section Details
            if ($section === 'linkages' && $request->has('_linkages_edit')) {
                $data = $request->validate([
                    'title' => ['required', 'string', 'max:255'],
                    'body' => ['nullable', 'string'],
                    'is_visible' => ['nullable', 'boolean'],
                ]);
                
                $department->linkages_title = $data['title'];
                $department->linkages_body = $data['body'] ?? '';
                $department->linkages_is_visible = $request->has('is_visible');
                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section
                ])->with('success', 'Linkages details saved.');
            }

            if ($section === 'organizations' && $request->has('_organizations_edit')) {
                $data = $request->validate([
                    'title' => ['required', 'string', 'max:255'],
                    'body' => ['nullable', 'string'],
                    'is_visible' => ['nullable', 'boolean'],
                ]);

                $department->organizations_title = $data['title'];
                $department->organizations_body = $data['body'] ?? '';
                $department->organizations_is_visible = $request->has('is_visible');
                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section
                ])->with('success', 'Student organizations details saved.');
            }

            // Handle Partnership Roster (Bulk Edit)
            if ($section === 'linkages' && $request->has('_roster_edit')) {
                // No generic title/body validation needed here
                $linkages = $request->input('linkages', []);
                $department->linkages()->delete();

                if (is_array($linkages)) {
                    foreach ($linkages as $index => $item) {
                        $existingImage = $item['existing_image'] ?? null;
                        $imagePath = $existingImage;
                        
                        if ($request->hasFile("linkages.{$index}.image")) {
                            $file = $request->file("linkages.{$index}.image");
                            $filename = time() . '_linkage_' . $index . '_' . Str::slug($department->name) . '.' . $file->getClientOriginalExtension();
                            $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($department->name) . "/linkages/" . Str::slug($item['title'] ?? 'item'), $file, $filename);
                            
                            if ($existingImage && file_exists(public_path($existingImage))) {
                                @unlink(public_path($existingImage));
                            }
                            $imagePath = $imagePath ? \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath) : $existingImage;
                        }

                        \App\Models\DepartmentLinkage::create([
                            'department_id' => $department->id,
                            'name' => $item['name'],
                            'description' => $item['description'] ?? '',
                            'image' => $imagePath,
                            'url' => $item['url'] ?? null,
                            'type' => $item['type'] ?? 'local',
                            'sort_order' => $index,
                        ]);
                    }
                }

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section
                ])->with('success', 'Partnership roster updated.');
            }

            // Handle Add Single Partner
            if ($section === 'linkages' && $request->has('_add_partner_edit')) {
                $data = $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'type' => ['required', 'string', 'in:local,international'],
                    'description' => ['nullable', 'string'],
                    'url' => ['nullable', 'string', 'max:500'],
                    'image' => ['nullable', 'image', 'max:2048'],
                ]);

                $data['url'] = $this->normalizeOptionalUrl($data['url'] ?? null);

                $imagePath = null;
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $filename = time() . '_linkage_new_' . Str::slug($department->name) . '.' . $file->getClientOriginalExtension();
                    $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($department->name) . "/linkages/" . Str::slug($data['name'] ?? 'item'), $file, $filename);
                    if ($imagePath) {
                        $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                    }
                }

                $maxSortOrder = $department->linkages()->max('sort_order') ?? -1;

                \App\Models\DepartmentLinkage::create([
                    'department_id' => $department->id,
                    'name' => $data['name'],
                    'type' => $data['type'],
                    'description' => $data['description'] ?? '',
                    'url' => $data['url'] ?? null,
                    'image' => $imagePath,
                    'sort_order' => $maxSortOrder + 1,
                ]);

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section
                ])->with('success', 'Partner added successfully.');
            }

            // Handle Facilities Roster (Bulk Edit)
            if ($section === 'facilities' && $request->has('_roster_edit')) {
                $facilities = $request->input('facilities', []);
                $department->facilities()->delete();

                if (is_array($facilities)) {
                    foreach ($facilities as $index => $item) {
                        $existingImage = $item['existing_image'] ?? null;
                        $imagePath = $existingImage;
                        
                        if ($request->hasFile("facilities.{$index}.image")) {
                            $file = $request->file("facilities.{$index}.image");
                            $filename = time() . '_fac_' . $index . '_' . Str::slug($department->name) . '.' . $file->getClientOriginalExtension();
                            $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($department->name) . "/facilities/" . Str::slug($item['title'] ?? 'item'), $file, $filename);
                            
                            if ($existingImage && file_exists(public_path($existingImage))) {
                                @unlink(public_path($existingImage));
                            }
                            $imagePath = $imagePath ? \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath) : $existingImage;
                        }

                        \App\Models\DepartmentFacility::create([
                            'department_id' => $department->id,
                            'title' => $item['title'],
                            'description' => $item['description'] ?? '',
                            'image' => $imagePath,
                            'sort_order' => $index,
                        ]);
                    }
                }

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section
                ])->with('success', 'Facilities roster updated.');
            }

            // Handle Add Single Facility
            if ($section === 'facilities' && $request->has('_add_facility_edit')) {
                $data = $request->validate([
                    'title' => ['required', 'string', 'max:255'],
                    'description' => ['nullable', 'string'],
                    'image' => ['nullable', 'image', 'max:2048'],
                ]);

                $imagePath = null;
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $filename = time() . '_fac_new_' . Str::slug($department->name) . '.' . $file->getClientOriginalExtension();
                    $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($department->name) . "/facilities/" . Str::slug($data['title'] ?? 'item'), $file, $filename);
                    if ($imagePath) {
                        $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                    }
                }

                $maxSortOrder = $department->facilities()->max('sort_order') ?? -1;

                \App\Models\DepartmentFacility::create([
                    'department_id' => $department->id,
                    'title' => $data['title'],
                    'description' => $data['description'] ?? '',
                    'image' => $imagePath,
                    'sort_order' => $maxSortOrder + 1,
                ]);

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section
                ])->with('success', 'Facility added successfully.');
            }

            // Handle Edit Single Facility
            if ($section === 'facilities' && $request->has('_edit_facility_edit')) {
                $facilityId = $request->input('facility_id');
                $facility = \App\Models\DepartmentFacility::where('department_id', $department->id)
                    ->findOrFail($facilityId);

                $data = $request->validate([
                    'title' => ['required', 'string', 'max:255'],
                    'description' => ['nullable', 'string'],
                    'image' => ['nullable', 'image', 'max:2048'],
                ]);

                $imagePath = $facility->image;
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $filename = time() . '_fac_edit_' . Str::slug($department->name) . '.' . $file->getClientOriginalExtension();
                    $newPath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($department->name) . "/facilities/" . Str::slug($data['title'] ?? 'item'), $file, $filename);
                    if ($newPath) {
                        $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->url($newPath);
                    }
                }

                $facility->update([
                    'title' => $data['title'],
                    'description' => $data['description'] ?? '',
                    'image' => $imagePath,
                ]);

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $department->id,
                    'section' => $section
                ])->with('success', 'Facility updated successfully.');
            }

            // Handle Edit Single Partner
            if ($section === 'linkages' && $request->has('_edit_partner_edit')) {
                $partnerId = $request->input('partner_id');
                $partner = \App\Models\DepartmentLinkage::where('department_id', $department->id)
                    ->findOrFail($partnerId);

                $data = $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'type' => ['required', 'string', 'in:local,international'],
                    'description' => ['nullable', 'string'],
                    'url' => ['nullable', 'string', 'max:500'],
                    'image' => ['nullable', 'image', 'max:2048'],
                ]);

                $data['url'] = $this->normalizeOptionalUrl($data['url'] ?? null);

                $imagePath = $partner->image;
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $filename = time() . '_linkage_edit_' . Str::slug($department->name) . '.' . $file->getClientOriginalExtension();
                    $newPath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($department->name) . "/linkages/" . Str::slug($item['title'] ?? 'item'), $file, $filename);
                    if ($newPath) {
                        // Optional: Delete old image from Drive if needed, but Drive usually handles versioning or we can leave it
                        $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->url($newPath);
                    }
                }

                $partner->update([
                    'name' => $data['name'],
                    'type' => $data['type'],
                    'description' => $data['description'] ?? '',
                    'url' => $data['url'] ?? null,
                    'image' => $imagePath,
                ]);

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $department->id,
                    'section' => $section
                ])->with('success', 'Partner updated successfully.');
            }
            
            $data = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'body' => ['nullable', 'string'],
                'program_description' => ['nullable', 'string'],
                'logo' => ['nullable', 'image', 'max:2048'],
                'graduate_outcomes' => ['nullable', 'string'],
                'graduate_outcomes_title' => ['nullable', 'string', 'max:255'],
                'social_facebook' => ['nullable', 'url', 'max:500'],
                'social_x' => ['nullable', 'url', 'max:500'],
                'social_youtube' => ['nullable', 'url', 'max:500'],
                'social_linkedin' => ['nullable', 'url', 'max:500'],
                'social_instagram' => ['nullable', 'url', 'max:500'],
                'social_other' => ['nullable', 'url', 'max:500'],
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:255'],
                'is_visible' => ['nullable'],
                'bulk_section_visibility' => ['nullable'],
                'bulk_section_visibility_mode' => ['nullable'],
            ]);

            // Handle Overview Section with direct columns
            if ($section === 'overview') {
                $department->overview_title = $data['title'];
                $department->overview_body = $data['body'] ?? '';
                $department->overview_is_visible = $request->has('is_visible');

                if ($request->has('bulk_section_visibility_mode')) {
                    $visibleState = $request->boolean('bulk_section_visibility');
                    foreach ([
                        'overview_is_visible',
                        'faculty_is_visible',
                        'objectives_is_visible',
                        'programs_is_visible',
                        'awards_is_visible',
                        'research_is_visible',
                        'extension_is_visible',
                        'training_is_visible',
                        'facilities_is_visible',
                        'membership_is_visible',
                        'alumni_is_visible',
                        'linkages_is_visible',
                        'organizations_is_visible',
                    ] as $visibilityColumn) {
                        $department->{$visibilityColumn} = $visibleState;
                    }
                }

                if ($request->boolean('remove_logo') && ! $request->hasFile('logo')) {
                    if (! empty($department->logo)) {
                        preg_match('/[?&]id=([^&]+)/', $department->logo, $logoMatches);

                        if (! empty($logoMatches[1])) {
                            app(\App\Services\GoogleDriveService::class)->deleteByFileId($logoMatches[1]);
                        } elseif (file_exists(public_path($department->logo))) {
                            unlink(public_path($department->logo));
                        }
                    }

                    $department->logo = null;
                }

                // Handle logo upload
                if ($request->hasFile('logo')) {
                    // Delete old logo if exists
                    if (! empty($department->logo)) {
                        preg_match('/[?&]id=([^&]+)/', $department->logo, $logoMatches);

                        if (! empty($logoMatches[1])) {
                            app(\App\Services\GoogleDriveService::class)->deleteByFileId($logoMatches[1]);
                        } elseif (file_exists(public_path($department->logo))) {
                            unlink(public_path($department->logo));
                        }
                    }
                    $file = $request->file('logo');
                    $filename = time() . '_' . Str::slug($department->name) . '.' . $file->getClientOriginalExtension();
                    $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($department->name) . "/logos", $file, $filename);
                    if ($imagePath) {
                        $department->logo = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                    }
                }

                $department->social_facebook = $data['social_facebook'] ?? null;
                $department->social_x = $data['social_x'] ?? null;
                $department->social_youtube = $data['social_youtube'] ?? null;
                $department->social_linkedin = $data['social_linkedin'] ?? null;
                $department->social_instagram = $data['social_instagram'] ?? null;
                $department->social_other = $data['social_other'] ?? null;
                $department->email = $data['email'] ?? null;
                $department->phone = $data['phone'] ?? null;

                $department->save();

                return redirect()->route('admin.colleges.show-department', [
                    'college' => $college,
                    'department' => $departmentId,
                    'section' => $section
                ])->with('success', 'Department section saved.');
            }
        }

        // Otherwise update basic department data (name/details/logo)
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        // Store old department name for faculty update
        $oldDepartmentName = $department->name;
        
        // Update department name
        $department->name = $data['name'];
        
        // Update details if provided
        if ($request->has('details')) {
            $department->details = $data['details'] ?? '';
        }

        if ($request->boolean('remove_logo') && ! $request->hasFile('logo')) {
            if (! empty($department->logo)) {
                preg_match('/[?&]id=([^&]+)/', $department->logo, $logoMatches);

                if (! empty($logoMatches[1])) {
                    app(\App\Services\GoogleDriveService::class)->deleteByFileId($logoMatches[1]);
                } elseif (file_exists(public_path($department->logo))) {
                    unlink(public_path($department->logo));
                }
            }

            $department->logo = null;
        }

        // Handle logo upload - only update if new file provided
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if (! empty($department->logo)) {
                preg_match('/[?&]id=([^&]+)/', $department->logo, $logoMatches);

                if (! empty($logoMatches[1])) {
                    app(\App\Services\GoogleDriveService::class)->deleteByFileId($logoMatches[1]);
                } elseif (file_exists(public_path($department->logo))) {
                    unlink(public_path($department->logo));
                }
            }
            $file = $request->file('logo');
            $filename = time() . '_' . Str::slug($data['name']) . '.' . $file->getClientOriginalExtension();
            $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($data['name']) . "/logos", $file, $filename);
            if ($imagePath) {
                $department->logo = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
            }
        }

        $department->save();

        // Update faculty members if department name changed
        if ($oldDepartmentName !== $data['name']) {
            Faculty::where('college_slug', $college)
                ->where('department', $oldDepartmentName)
                ->update(['department' => $data['name']]);
        }

        return redirect()->route('admin.colleges.show-department', [
            'college' => $college,
            'department' => $departmentId,
            'section' => 'overview' // Default to overview
        ])->with('success', 'Department updated.');
    }

    private function normalizeOptionalUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (! preg_match('/^https?:\/\//i', $url)) {
            $url = 'https://' . $url;
        }

        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    public function destroyLinkagePartner(Request $request, string $college, string $department, int $partner): RedirectResponse
    {
        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }
        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }

        $departmentModel = CollegeDepartment::findByCollegeAndRouteKey($college, $department);
        if (! $departmentModel) {
            abort(404, 'Department not found.');
        }

        $partnerModel = \App\Models\DepartmentLinkage::where('department_id', $departmentModel->id)
            ->findOrFail($partner);

        $partnerModel->delete();

        return redirect()->route('admin.colleges.show-department', [
            'college' => $college,
            'department' => $department,
            'section' => 'linkages'
        ])->with('success', 'Partner removed successfully.');
    }

    public function destroyFacilityItem(Request $request, string $college, string $department, int $facility): RedirectResponse
    {
        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }
        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }

        $departmentModel = CollegeDepartment::findByCollegeAndRouteKey($college, $department);
        if (! $departmentModel) {
            abort(404, 'Department not found.');
        }

        $facilityModel = \App\Models\DepartmentFacility::where('department_id', $departmentModel->id)
            ->findOrFail($facility);

        $facilityModel->delete();

        return redirect()->route('admin.colleges.show-department', [
            'college' => $college,
            'department' => $department,
            'section' => 'facilities'
        ])->with('success', 'Facility removed successfully.');
    }

    public function destroyAlumnus(Request $request, string $college, string $department, int $alumnus): RedirectResponse
    {
        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }
        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }

        $departmentModel = CollegeDepartment::findByCollegeAndRouteKey($college, $department);
        if (! $departmentModel) {
            abort(404, 'Department not found.');
        }

        $alumnusModel = \App\Models\DepartmentAlumnus::where('department_id', $departmentModel->id)
            ->findOrFail($alumnus);

        if (!empty($alumnusModel->image) && \Illuminate\Support\Facades\Storage::disk('google')->exists($alumnusModel->image)) {
            \Illuminate\Support\Facades\Storage::disk('google')->delete($alumnusModel->image);
        }

        $alumnusModel->delete();

        return redirect()->route('admin.colleges.show-department', [
            'college' => $college,
            'department' => $department,
            'section' => 'alumni'
        ])->with('success', 'Alumnus removed successfully.');
    }

    public function destroyCollegeAlumnus(Request $request, string $college, int $alumnus): RedirectResponse
    {
        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }
        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }

        $alumnusModel = DepartmentAlumnus::where('college_slug', $college)
            ->whereNull('department_id')
            ->whereNull('institute_id')
            ->findOrFail($alumnus);

        if (! empty($alumnusModel->image) && \Illuminate\Support\Facades\Storage::disk('google')->exists($alumnusModel->image)) {
            \Illuminate\Support\Facades\Storage::disk('google')->delete($alumnusModel->image);
        }

        $alumnusModel->delete();

        return redirect()->route('admin.colleges.show', [
            'college' => $college,
            'section' => 'alumni'
        ])->with('success', 'Alumnus removed successfully.');
    }

    public function update(Request $request, string $college, string $section): RedirectResponse
    {
        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }
        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }
        $sections = self::getSections();
        // Resolve custom URL slugs (e.g., 'center' -> 'institutes')
        if (! isset($sections[$section])) {
            $customTitles = CollegeSection::where('college_slug', $college)
                ->whereNotNull('title')
                ->where('title', '!=', '')
                ->pluck('title', 'section_slug')
                ->toArray();
            foreach ($customTitles as $internalSlug => $customTitle) {
                if (\Illuminate\Support\Str::slug($customTitle) === $section && isset($sections[$internalSlug])) {
                    $section = $internalSlug;
                    break;
                }
            }
        }
        if (! isset($sections[$section])) {
            abort(404, 'Section not found.');
        }

        // Check edit mode FIRST before general validation
        $editMode = $request->input('_edit_mode');

        // Handle featured video for overview section (separate validation)
        if ($section === 'overview' && $editMode === 'featured_video') {
            // Validate video fields
            $videoData = $request->validate([
                'video_type' => ['required', 'in:url,file'],
                'video_url' => ['nullable', 'url', 'max:500'],
                'video_file' => ['nullable', 'file', 'mimes:mp4,webm,ogg', 'max:51200'], // max 50MB
                'video_title' => ['nullable', 'string', 'max:255'],
                'video_description' => ['nullable', 'string'],
                'is_visible' => ['nullable', 'string'],
            ]);

            // The form submits "1" for checked, so use Laravel's boolean parsing here.
            $videoData['is_visible'] = $request->boolean('is_visible');

            // Get existing video record
            $existingVideo = CollegeVideo::where('college_slug', $college)->first();

            // Prepare data for update/create
            $dataToSave = [
                'college_slug' => $college,
                'video_type' => $videoData['video_type'],
                'video_title' => $videoData['video_title'] ?? null,
                'video_description' => $videoData['video_description'] ?? null,
                'is_visible' => $videoData['is_visible'],
            ];

            // Handle video file upload
            if ($videoData['video_type'] === 'file') {
                if ($request->hasFile('video_file')) {
                    // Delete old video file if exists
                    if ($existingVideo && $existingVideo->video_file && file_exists(public_path($existingVideo->video_file))) {
                        unlink(public_path($existingVideo->video_file));
                    }

                    $file = $request->file('video_file');
                    $filename = time() . '_' . Str::slug($colleges[$college]) . '.' . $file->getClientOriginalExtension();
                    $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/videos", $file, $filename);
                    if ($imagePath) {
                        $dataToSave['video_file'] = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                    }
                    $dataToSave['video_url'] = null;
                } else {
                    // Keep existing file if no new upload
                    if ($existingVideo && $existingVideo->video_file) {
                        $dataToSave['video_file'] = $existingVideo->video_file;
                    } else {
                        $dataToSave['video_file'] = null;
                    }
                    $dataToSave['video_url'] = null;
                }
            } elseif ($videoData['video_type'] === 'url') {
                // Delete old video file if switching from file to URL
                if ($existingVideo && $existingVideo->video_file && \Illuminate\Support\Facades\Storage::disk('google')->exists($existingVideo->video_file)) {
                    \Illuminate\Support\Facades\Storage::disk('google')->delete($existingVideo->video_file);
                }
                $dataToSave['video_file'] = null;
                $dataToSave['video_url'] = $videoData['video_url'] ?? null;
            }

            // Update or create video record
            CollegeVideo::updateOrCreate(
                ['college_slug' => $college],
                $dataToSave
            );

            return redirect()
                ->route('admin.colleges.show', ['college' => $college, 'section' => $section])
                ->with('success', 'Featured video updated successfully.');
        }

        // Handle retro section for overview
        if ($section === 'overview' && $editMode === 'retro') {
            // Validate retro fields
            $retroData = $request->validate([
                'retro_title' => ['nullable', 'string', 'max:255'],
                'retro_description' => ['nullable', 'string'],
                'retro_stamp' => ['nullable', 'string', 'max:100'],
                'hero_background_image' => ['nullable', 'image', 'max:2048'],
                'retro_id' => ['nullable', 'integer'],
                'retro_title_size' => ['nullable', 'integer', 'min:1', 'max:500'],
                'retro_stamp_size' => ['nullable', 'integer', 'min:1', 'max:500'],
            ]);
            
            $retroId = $request->input('retro_id');
            
            // Check count if creating
            if (!$retroId) {
                $count = CollegeRetro::where('college_slug', $college)->whereNull('department_id')->count();
                if ($count >= 4) {
                    return redirect()
                        ->back()
                        ->with('error', 'Maximum 4 retro items allowed.');
                }
            }

            // Handle hero background image upload
            $heroImage = null;
            $existingRetro = null;

            if ($retroId) {
                $existingRetro = CollegeRetro::where('college_slug', $college)->whereNull('department_id')->find($retroId);
                // Keep existing image if no new upload
                if ($existingRetro) {
                    $heroImage = $existingRetro->background_image;
                }
            }
            
            if ($request->hasFile('hero_background_image')) {
                // Delete old image if exists (only if updating)
                if ($retroId && $existingRetro && !empty($existingRetro->background_image) && \Illuminate\Support\Facades\Storage::disk('google')->exists($existingRetro->background_image)) {
                    \Illuminate\Support\Facades\Storage::disk('google')->delete($existingRetro->background_image);
                }

                $file = $request->file('hero_background_image');
                $filename = time() . '_hero_' . Str::slug($colleges[$college]) . '_' . uniqid() . '.' . $file->getClientOriginalExtension(); // unique name
                $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/hero", $file, $filename);
                if ($imagePath) {
                    $heroImage = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                }
            }

            // Update or create retro record
            CollegeRetro::updateOrCreate(
                ['id' => $retroId, 'college_slug' => $college, 'department_id' => null],
                [
                    'title' => $retroData['retro_title'] ?? null,
                    'description' => $retroData['retro_description'] ?? null,
                    'stamp' => $retroData['retro_stamp'] ?? null,
                    'background_image' => $heroImage,
                    'title_size' => $retroData['retro_title_size'] ?? null,
                    'stamp_size' => $retroData['retro_stamp_size'] ?? null,
                    'sort_order' => $retroId ? ($existingRetro->sort_order ?? 0) : (CollegeRetro::where('college_slug', $college)->whereNull('department_id')->max('sort_order') + 1),
                ]
            );

            return redirect()
                ->route('admin.colleges.show', ['college' => $college, 'section' => $section])
                ->with('success', 'Retro item saved successfully.');
        }

        // Handle retro item deletion
        if ($section === 'overview' && $editMode === 'delete_retro') {
            $retroId = $request->input('retro_id');
            $retro = CollegeRetro::where('college_slug', $college)->whereNull('department_id')->find($retroId);

            if ($retro) {
                // Delete background image if exists
                if (!empty($retro->background_image) && \Illuminate\Support\Facades\Storage::disk('google')->exists($retro->background_image)) {
                    \Illuminate\Support\Facades\Storage::disk('google')->delete($retro->background_image);
                }
                $retro->delete();

                return redirect()
                    ->route('admin.colleges.show', ['college' => $college, 'section' => $section])
                    ->with('success', 'Retro item removed successfully.');
            }

            return redirect()
                ->route('admin.colleges.show', ['college' => $college, 'section' => $section])
                ->with('error', 'Retro item not found.');
        }

        // Handle retro button visibility settings for overview
        if ($section === 'overview' && $editMode === 'retro_settings') {
            $retroSettings = $request->validate([
                'show_primary_retro_btn' => ['nullable'],
                'show_secondary_retro_btn' => ['nullable'],
            ]);

            $existingSection = CollegeSection::where('college_slug', $college)
                ->where('section_slug', 'overview')
                ->first();

            $meta = $existingSection && $existingSection->meta ? $existingSection->meta : [];
            
            $meta['show_primary_retro_btn'] = $request->has('show_primary_retro_btn');
            $meta['show_secondary_retro_btn'] = $request->has('show_secondary_retro_btn');

            CollegeSection::updateOrCreate(
                ['college_slug' => $college, 'section_slug' => 'overview'],
                ['meta' => $meta]
            );

            return redirect()
                ->route('admin.colleges.show', ['college' => $college, 'section' => $section])
                ->with('success', 'Retro settings updated successfully.');
        }

        // Handle contact section update
        if ($section === 'contact') {
            $contactData = $request->validate([
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:50'],
                'address' => ['nullable', 'string'],
                'facebook' => ['nullable', 'url', 'max:255'],
                'instagram' => ['nullable', 'url', 'max:255'],
                'custom_links' => ['nullable', 'array', 'max:2'],
                'custom_links.*' => ['nullable', 'url', 'max:255'],
            ]);

            \App\Models\CollegeContact::updateOrCreate(
                ['college_slug' => $college],
                $contactData
            );

            return redirect()
                ->route('admin.colleges.show', ['college' => $college, 'section' => $section])
                ->with('success', 'Contact info updated successfully.');
        }

        if ($section === 'extension') {
            $data = $request->validate([
                'title' => ['nullable', 'string', 'max:255'],
                'body' => ['nullable', 'string'],
                'is_visible' => ['nullable'],
            ]);

            $existingSection = CollegeSection::where('college_slug', $college)
                ->where('section_slug', $section)
                ->first();

            CollegeSection::updateOrCreate(
                [
                    'college_slug' => $college,
                    'section_slug' => $section,
                ],
                [
                    'title' => $data['title'] ?? ($existingSection->title ?? 'Extension'),
                    'body' => $data['body'] ?? ($existingSection->body ?? ''),
                    'meta' => $existingSection->meta ?? [],
                    'is_visible' => $request->has('is_visible'),
                ]
            );

            return redirect()
                ->route('admin.colleges.show', ['college' => $college, 'section' => $section])
                ->with('success', 'Extension section updated successfully.');
        }

        // Handle Training Section
        if ($section === 'training') {
            $inputName = $section;
            
            // Basic Validation
            $validated = $request->validate([
                'is_visible' => ['nullable', 'string'],
                'title' => ['nullable', 'string', 'max:255'],
                'body' => ['nullable', 'string'],
            ]);
            
            // Get Items
            $items = $request->input($inputName, []);
            $processedItems = [];

            // Get existing data to handle image retention/deletion
            $existingSection = CollegeSection::where('college_slug', $college)
                ->where('section_slug', $section)
                ->first();
            
            $existingMeta = $existingSection && $existingSection->meta ? $existingSection->meta : [];
            $existingItems = $existingMeta['items'] ?? [];

            // Process Items
            foreach ($items as $index => $item) {
                // Image Handling
                // Check if new image uploaded
                if ($request->hasFile("{$inputName}.{$index}.image")) {
                    $file = $request->file("{$inputName}.{$index}.image");
                    $filename = time() . "_{$section}_" . Str::slug($item['title'] ?? 'item') . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("images/college/{$section}", $file, $filename);
                    $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->url($path);
                } else {
                    // Keep existing if available
                    $imagePath = $item['existing_image'] ?? null;
                }

                $processedItem = [
                    'title' => $item['title'] ?? '',
                    'description' => $item['description'] ?? '',
                    'image' => $imagePath,
                    'created_at' => $item['created_at'] ?? now()->toDateTimeString(),
                ];

                // Include scholarship-specific fields
                if ($section === 'scholarships') {
                    $processedItem['qualifications'] = $item['qualifications'] ?? '';
                    $processedItem['process'] = $item['process'] ?? '';
                    $processedItem['requirements'] = $item['requirements'] ?? '';
                    $processedItem['benefits'] = $item['benefits'] ?? '';
                }

                $processedItems[] = $processedItem;
            }

            // Clean up old images that are no longer in processed items
            $newImages = array_filter(array_column($processedItems, 'image'));
            foreach ($existingItems as $oldItem) {
                $oldImage = $oldItem['image'] ?? null;
                if ($oldImage && !in_array($oldImage, $newImages)) {
                    if (\Illuminate\Support\Facades\Storage::disk('google')->exists($oldImage)) {
                        \Illuminate\Support\Facades\Storage::disk('google')->delete($oldImage);
                    }
                }
            }

            // Default titles per section
            $defaultTitles = [
                'training' => 'Training & Workshops',
            ];

            // Determine title/body (allow admin override)
            $sectionTitle = $validated['title'] ?? ($existingSection->title ?? ($defaultTitles[$section] ?? ucfirst($section)));
            $sectionBody = $validated['body'] ?? ($existingSection->body ?? '');

            // Save to DB
            CollegeSection::updateOrCreate(
                [
                    'college_slug' => $college,
                    'section_slug' => $section,
                ],
                [
                    'title' => $sectionTitle,
                    'body' => $sectionBody,
                    'meta' => ['items' => $processedItems],
                    'is_visible' => $request->has('is_visible'),
                ]
            );

            return redirect()
                ->route('admin.colleges.show', ['college' => $college, 'section' => $section])
                ->with('success', ucfirst($section) . ' section updated successfully.');
        }

        // Handle Scholarships Section Details
        if ($section === 'scholarships') {
            $validated = $request->validate([
                'is_visible' => ['nullable', 'string'],
                'title' => ['nullable', 'string', 'max:255'],
                'body' => ['nullable', 'string'],
            ]);

            $existingSection = CollegeSection::where('college_slug', $college)
                ->where('section_slug', $section)
                ->first();

            CollegeSection::updateOrCreate(
                [
                    'college_slug' => $college,
                    'section_slug' => $section,
                ],
                [
                    'title' => $validated['title'] ?? ($existingSection->title ?? 'Scholarships'),
                    'body' => $validated['body'] ?? ($existingSection->body ?? ''),
                    'meta' => $existingSection->meta ?? [],
                    'is_visible' => $request->has('is_visible'),
                ]
            );

            return redirect()
                ->route('admin.colleges.show', ['college' => $college, 'section' => $section])
                ->with('success', 'Scholarships section updated successfully.');
        }

        if ($section === 'alumni') {
            if ($editMode === 'alumni_details') {
                $data = $request->validate([
                    'title' => ['required', 'string', 'max:255'],
                    'body' => ['nullable', 'string'],
                    'is_visible' => ['nullable'],
                ]);

                $existingSection = CollegeSection::where('college_slug', $college)
                    ->where('section_slug', $section)
                    ->first();

                CollegeSection::updateOrCreate(
                    [
                        'college_slug' => $college,
                        'section_slug' => $section,
                    ],
                    [
                        'title' => $data['title'],
                        'body' => $data['body'] ?? ($existingSection->body ?? ''),
                        'meta' => $existingSection->meta ?? [],
                        'is_visible' => $request->has('is_visible'),
                    ]
                );

                return redirect()
                    ->route('admin.colleges.show', ['college' => $college, 'section' => $section])
                    ->with('success', 'Alumni section details updated successfully.');
            }

            if ($editMode === 'add_alumnus' || $editMode === 'edit_alumnus') {
                $data = $request->validate([
                    'editing_alumnus_id' => ['nullable', 'integer'],
                    'title' => ['required', 'string', 'max:255'],
                    'year_graduated' => ['nullable', 'string', 'max:255'],
                    'description' => ['nullable', 'string'],
                    'image' => ['nullable', 'image', 'max:2048'],
                    'remove_image' => ['nullable'],
                ]);

                $alumnus = $request->filled('editing_alumnus_id')
                    ? DepartmentAlumnus::where('college_slug', $college)
                        ->whereNull('department_id')
                        ->whereNull('institute_id')
                        ->findOrFail($request->input('editing_alumnus_id'))
                    : new DepartmentAlumnus([
                        'college_slug' => $college,
                        'department_id' => null,
                        'institute_id' => null,
                    ]);

                $imagePath = $alumnus->image;

                if ($request->boolean('remove_image')) {
                    $imagePath = null;
                }

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $filename = time() . '_college_alumni_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $storedPath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs(
                        "colleges/{$college}/alumni/" . Str::slug($data['title']),
                        $file,
                        $filename
                    );
                    $imagePath = $storedPath ? \Illuminate\Support\Facades\Storage::disk('google')->url($storedPath) : $imagePath;
                }

                $alumnus->fill([
                    'college_slug' => $college,
                    'department_id' => null,
                    'institute_id' => null,
                    'title' => $data['title'],
                    'year_graduated' => $data['year_graduated'] ?? null,
                    'description' => $data['description'] ?? '',
                    'image' => $imagePath,
                    'sort_order' => $alumnus->exists
                        ? $alumnus->sort_order
                        : ((int) DepartmentAlumnus::where('college_slug', $college)
                            ->whereNull('department_id')
                            ->whereNull('institute_id')
                            ->max('sort_order') + 1),
                ]);
                $alumnus->save();

                return redirect()
                    ->route('admin.colleges.show', ['college' => $college, 'section' => $section])
                    ->with('success', $request->filled('editing_alumnus_id') ? 'Alumnus updated successfully.' : 'Alumnus added successfully.');
            }
        }

        // General section validation (for non-video, non-retro saves)
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'icon' => ['nullable', 'image', 'max:2048'], // max 2MB
            'about_images' => ['nullable', 'array', 'max:5'],
            'about_images.*' => ['nullable', 'image', 'max:5120'], // max 5MB each
            'is_visible' => ['nullable'],
            // Contact fields for overview
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'facebook' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'url', 'max:255'],
            'custom_links' => ['nullable', 'array', 'max:2'],
            'custom_links.*' => ['nullable', 'url', 'max:255'],
        ];

        if ($section === 'accreditation') {
            $rules['hero_title'] = ['nullable', 'string', 'max:255'];
        }

        $data = $request->validate($rules);

        // Save section content (preserve meta/departments)
        $sectionModel = CollegeSection::query()
            ->where('college_slug', $college)
            ->where('section_slug', $section)
            ->first();

        $isDraft = $request->has('is_draft');
        $publishAt = $isDraft ? null : ($request->input('publish_at') ?: null);

        $meta = $sectionModel ? ($sectionModel->meta ?? []) : [];
        if ($section === 'accreditation') {
            $meta['hero_title'] = $request->input('hero_title');
        }

        if ($sectionModel) {
            // Update only title and body, preserve meta
            $sectionModel->update([
                'title' => $data['title'],
                'body' => $data['body'] ?? '',
                'is_visible' => $request->has('is_visible'),
                'is_draft' => $isDraft,
                'publish_at' => $publishAt,
                'meta' => $meta,
            ]);
        } else {
            // Create new section
            CollegeSection::create([
                'college_slug' => $college,
                'section_slug' => $section,
                'title' => $data['title'],
                'body' => $data['body'] ?? '',
                'is_visible' => $request->has('is_visible'),
                'is_draft' => $isDraft,
                'publish_at' => $publishAt,
                'meta' => $meta,
            ]);
        }

        // Handle icon and about image uploads for overview section
        if ($section === 'overview') {
            $collegeModel = CollegeModel::find($college);
            if ($collegeModel) {
                                // Handle icon deletion
                if ($request->input("delete_icon") == "1" && !$request->hasFile("icon")) {
                    if (!empty($collegeModel->icon)) {
                        preg_match('/[?&]id=([^&]+)/', $collegeModel->icon, $_esIconM);
                        if (isset($_esIconM[1])) {
                            try {
                                \Illuminate\Support\Facades\Storage::disk('google')->delete($_esIconM[1]);
                            } catch (\Exception $e) {
                                \Log::error('College Icon Delete Failed: ' . $e->getMessage() . ' | Icon: ' . $collegeModel->icon);
                            }
                        }
                    }

                    $collegeModel->icon = null;
                    
                    // Explicitly delete physical file fallbacks to align Welcome page layout
                    $logoPath = public_path('images/logos/' . $college . '.jpg');
                    $webpPath = public_path('images/colleges/' . $college . '.webp');
                    if (file_exists($logoPath)) @unlink($logoPath);
                    if (file_exists($webpPath)) @unlink($webpPath);
                }

                // Handle icon upload
                if ($request->hasFile('icon')) {
                    // Delete old icon if exists
                    if (!empty($collegeModel->icon)) {
                        preg_match('/[?&]id=([^&]+)/', $collegeModel->icon, $_esIconM);
                        if (isset($_esIconM[1])) {
                            try {
                                app(\App\Services\GoogleDriveService::class)->deleteById($_esIconM[1]);
                            } catch (\Exception $e) {
                                // Silent fail layout index
                            }
                        }
                    }

                    // Upload new icon
                    $file = $request->file('icon');
                    $filename = time() . '_' . Str::slug($colleges[$college]);
                    $filename = $college . '__' . $filename . '.' . $file->getClientOriginalExtension();
                    
                    $path = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}", $file, $filename);
                    $collegeModel->icon = \Illuminate\Support\Facades\Storage::disk('google')->url($path);
                }

                // Handle about images upload and deletion
                $currentImages = $collegeModel->about_images ?? [];
                if (!is_array($currentImages)) {
                    $currentImages = json_decode($currentImages, true) ?? [];
                }

                // Log request data for debugging
                \Log::info('About images request data', [
                    'has_delete_about_image' => $request->has('delete_about_image'),
                    'delete_about_image_input' => $request->input('delete_about_image', []),
                    'all_request_keys' => array_keys($request->all()),
                    'current_images_count' => count($currentImages),
                ]);

                // Handle deletion of specific images by index
                if ($request->has('delete_about_image')) {
                    $indicesToDelete = $request->input('delete_about_image', []);
                    \Log::info('Deleting images at indices: ' . json_encode($indicesToDelete));
                    
                    // Sort indices in reverse order so we delete from the end first
                    rsort($indicesToDelete);
                    
                    foreach ($indicesToDelete as $index) {
                        $index = (int)$index;
                        if (isset($currentImages[$index])) {
                            $imageUrl = $currentImages[$index];
                            
                            // Delete from Google Drive if it's a Google Drive URL
                            if (str_contains($imageUrl, 'drive.google.com')) {
                                try {
                                    preg_match('/[?&]id=([^&]+)/', $imageUrl, $matches);
                                    if (isset($matches[1])) {
                                        $fileId = $matches[1];
                                        \Illuminate\Support\Facades\Storage::disk('google')->delete($fileId);
                                    }
                                } catch (\Exception $e) {
                                    // Log error but continue
                                    \Log::warning('Failed to delete about image from Google Drive: ' . $e->getMessage());
                                }
                            }
                            
                            // Remove from array
                            unset($currentImages[$index]);
                        }
                    }
                    
                    // Reindex array
                    $currentImages = array_values($currentImages);
                }

                // Handle new image uploads
                if ($request->hasFile('about_images')) {
                    $uploadedFiles = $request->file('about_images');
                    $maxImages = 5;

                    foreach ($uploadedFiles as $file) {
                        if (count($currentImages) >= $maxImages) {
                            break; // Stop if we've reached the limit
                        }

                        $filename = time() . '_about_' . Str::slug($colleges[$college]) . '_' . count($currentImages) . '.' . $file->getClientOriginalExtension();

                        $path = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/about", $file, $filename);
                        $imageUrl = \Illuminate\Support\Facades\Storage::disk('google')->url($path);

                        $currentImages[] = $imageUrl;
                    }
                }

                // Save the updated images array
                $collegeModel->about_images = json_encode($currentImages);

                $collegeModel->save();
            }
        }

        // Handle contact info saving for overview section
        if ($section === 'overview' && $editMode === 'overview') {
            \App\Models\CollegeContact::updateOrCreate(
                ['college_slug' => $college],
                [
                    'email' => $data['email'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'address' => $data['address'] ?? null,
                    'facebook' => $data['facebook'] ?? null,
                    'instagram' => $data['instagram'] ?? null,
                    'custom_links' => $data['custom_links'] ?? null,
                ]
            );
        }

        return redirect()
            ->route('admin.colleges.show', ['college' => $college, 'section' => $section])
            ->with('success', 'Section updated successfully.');
    }

    public function destroy(Request $request, string $college): RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! $user->isSuperAdmin()) {
            abort(403, 'Only superadmins can delete departments.');
        }

        $model = CollegeModel::find($college);
        if (! $model) {
            return redirect()
                ->route('admin.colleges.index')
                ->with('error', 'Department not found.');
        }

        // Basic safety: prevent delete if there is related content
        $hasFaculty = Faculty::where('college_slug', $college)->exists();
        $hasSections = CollegeSection::where('college_slug', $college)->exists();
        if ($hasFaculty || $hasSections) {
            return redirect()
                ->route('admin.colleges.index')
                ->with('error', 'Cannot delete department with existing faculty or content.');
        }

        $model->delete();

        return redirect()
            ->route('admin.colleges.index')
            ->with('success', 'Department deleted successfully.');
    }

    private function getSectionContent(string $collegeSlug, string $collegeName, string $section): array
    {
        $stored = CollegeSection::query()
            ->where('college_slug', $collegeSlug)
            ->where('section_slug', $section)
            ->first();
        if ($stored && ($stored->title !== null || $stored->body !== null)) {
            $result = [
                'title' => $stored->title ?? (self::getSections()[$section] ?? $section),
                'body' => $stored->body ?? '',
                'plain' => !preg_match('/<[a-z][\s\S]*>/i', $stored->body ?? ''),
            ];
            
            // Merge meta data if available (for retro, video, etc.)
            if ($stored->meta) {
                $meta = $stored->meta;
                if ($meta && is_array($meta)) {
                    $result = array_merge($result, $meta);
                }
            }

            // Merge contact data if section is contact
            if ($section === 'contact') {
                $contact = \App\Models\CollegeContact::firstOrCreate(
                    ['college_slug' => $collegeSlug],
                     // Default values only for creation
                    ['email' => null, 'phone' => null]
                );
                $result['contact_data'] = $contact;
            }
            
            
            // Merge retro data and contact data if overview
            if ($section === 'overview') {
                $retro = CollegeRetro::where('college_slug', $collegeSlug)->whereNull('department_id')->first();
                if ($retro) {
                    $result['retro_title'] = $retro->title;
                    $result['retro_description'] = $retro->description;
                    $result['retro_stamp'] = $retro->stamp;
                    $result['hero_background_image'] = $retro->background_image;
                }
                
                $contact = \App\Models\CollegeContact::where('college_slug', $collegeSlug)->first();
                if ($contact) {
                    $result['contact_data'] = $contact;
                }
                
                // Add retro settings to result for toggles (from the merged meta)
                $result['show_primary_retro_btn'] = $result['show_primary_retro_btn'] ?? true;
                $result['show_secondary_retro_btn'] = $result['show_secondary_retro_btn'] ?? true;
            }

            return $result;
        }

        $title = self::getSections()[$section] ?? $section;
        $placeholders = [
            'overview' => [
                'title' => 'Overview',
                'body' => '<p>The ' . e($collegeName) . ' is one of the colleges of Central Luzon State University. It has remained an important center of higher learning and scientific research, keeping pace with the University and national thrusts.</p><p>Content for this section can be managed here. Edit overview, vision, mission, and key information specific to ' . e($collegeName) . '.</p>',
            ],
            'departments' => [
                'title' => 'Departments',
                'body' => '<p>Departments and units under ' . e($collegeName) . '.</p><p>List department names, chairs, and brief descriptions. This content can be edited in the CMS.</p>',
            ],
            'facilities' => [
                'title' => 'Facilities',
                'body' => '<p>Laboratories, buildings, and other facilities of ' . e($collegeName) . '.</p><p>Describe key facilities, labs, and learning spaces. Photos and details can be added here.</p>',
            ],
            'explore' => [
                'title' => 'Explore',
                'body' => '<p>Laboratories and facilities of ' . e($collegeName) . '.</p><p>Specialized spaces that support instruction, research, and practical training. List and describe key laboratories, equipment, and learning facilities available to students and faculty.</p>',
            ],
            'faculty' => [
                'title' => 'Faculty',
                'body' => '<p>Faculty and staff of ' . e($collegeName) . '.</p><p>Overview of faculty profile, research areas, and contact. Detailed roster can be managed in this section.</p>',
            ],
            'alumni' => [
                'title' => 'Alumni',
                'body' => '<p>Outstanding alumni of ' . e($collegeName) . '.</p><p>Highlight notable graduates, their achievements, and the stories that inspire current students.</p>',
            ],
            'downloads' => [
                'title' => 'Downloads',
                'body' => '<p>Downloadable files, forms, and reference materials for ' . e($collegeName) . '.</p><p>Upload PDF, DOCX, and XLSX resources from the admin panel and make them available to the public on this page.</p>',
            ],
            'admissions' => [
                'title' => 'Admissions',
                'body' => '<p>Admission requirements and procedures for ' . e($collegeName) . '.</p><p>Entry requirements, deadlines, and application steps. Keep this section updated each academic year.</p>',
            ],
            'contact' => [
                'title' => 'Contact',
                // Fetch contact info from CollegeContact model
                'contact_info' => \App\Models\CollegeContact::firstOrCreate(
                    ['college_slug' => $collegeSlug],
                    ['email' => $collegeSlug . '@clsu.edu.ph', 'phone' => '(044) 940 8785']
                ),
            ],
        ];

        if ($section === 'contact') {
             $contact = \App\Models\CollegeContact::firstOrCreate(
                ['college_slug' => $collegeSlug],
                ['email' => $collegeSlug . '@clsu.edu.ph', 'phone' => '(044) 940 8785']
            );
            return [
                'title' => 'Contact',
                'contact_data' => $contact,
            ];
        }

        $result = $placeholders[$section] ?? [
            'title' => $title,
            'body' => '<p>Content for ' . e($title) . ' – ' . e($collegeName) . '.</p>',
        ];

        // Merge retro data and contact data if overview
        if ($section === 'overview') {
            $retro = CollegeRetro::where('college_slug', $collegeSlug)->whereNull('department_id')->first();
            if ($retro) {
                $result['retro_title'] = $retro->title;
                $result['retro_description'] = $retro->description;
                $result['retro_stamp'] = $retro->stamp;
                $result['hero_background_image'] = $retro->background_image;
            }
            
            $contact = \App\Models\CollegeContact::where('college_slug', $collegeSlug)->first();
            if ($contact) {
                $result['contact_data'] = $contact;
            }
        }

        return $result;
    }

    private static function htmlToPlainText(string $html): string
    {
        $text = strip_tags(str_replace(['</p>', '<br>', '<br/>', '<br />', '</li>'], "\n", $html));
        return trim(preg_replace("/\n{3,}/", "\n\n", $text));
    }

    private function handleDepartmentAction(Request $request, string $college): RedirectResponse
    {
        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }
        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }

        $redirectDeptId = null;

        // Handle add department
        if ($request->has('dept_name')) {
            $data = $request->validate([
                'dept_name' => ['required', 'string', 'max:255'],
                'dept_logo' => ['nullable', 'image', 'max:2048'],
            ]);

            $logo = null;
            if ($request->hasFile('dept_logo')) {
                $file = $request->file('dept_logo');
                $filename = time() . '_' . Str::slug($data['dept_name']) . '.' . $file->getClientOriginalExtension();
                $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($data['dept_name']) . "/logo", $file, $filename);
                if ($imagePath) {
                    $logo = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                }
            }

            // Get the next sort order
            $maxSortOrder = CollegeDepartment::where('college_slug', $college)->max('sort_order') ?? 0;

            $department = CollegeDepartment::create([
                'college_slug' => $college,
                'name' => $data['dept_name'],
                'logo' => $logo,
                'sort_order' => $maxSortOrder + 1,
            ]);

            $redirectDeptId = $department->id;
        }

        // Handle edit department
        if ($request->has('edit_dept')) {
            $deptId = (int) $request->input('edit_dept');
            $department = CollegeDepartment::where('college_slug', $college)->find($deptId);
            
            if ($department) {
                $data = $request->validate([
                    'edit_name' => ['required', 'string', 'max:255'],
                    'edit_logo' => ['nullable', 'image', 'max:2048'],
                ]);

                // Store old department name for faculty update
                $oldDepartmentName = $department->name;
                
                $department->name = $data['edit_name'];

                if ($request->hasFile('edit_logo')) {
                    // Delete old logo if exists
                    if (!empty($department->logo)) {
                        preg_match('/[?&]id=([^&]+)/', $department->logo, $_esIconM);
                        if (isset($_esIconM[1])) {
                            try {
                                \Illuminate\Support\Facades\Storage::disk('google')->delete($_esIconM[1]);
                            } catch (\Exception $e) {
                                // Silent fail layout index
                            }
                        } elseif (file_exists(public_path($department->logo))) {
                            unlink(public_path($department->logo));
                        }
                    }

                    $file = $request->file('edit_logo');
                    $filename = time() . '_' . Str::slug($data['edit_name']) . '.' . $file->getClientOriginalExtension();
                    $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/departments/" . Str::slug($data['edit_name']) . "/logo", $file, $filename);
                    if ($imagePath) {
                        $department->logo = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                    }
                }
                
                $department->save();
                
                // Update faculty members if department name changed
                if ($oldDepartmentName !== $data['edit_name']) {
                    Faculty::where('college_slug', $college)
                        ->where('department', $oldDepartmentName)
                        ->update(['department' => $data['edit_name']]);
                }

                $redirectDeptId = $department->id;
            }
        }

        // Handle save department section content
        if ($request->has('save_dept_section')) {
            $deptId = (int) $request->input('edit_dept_index');
            $department = CollegeDepartment::where('college_slug', $college)->find($deptId);
            
            if ($department) {
                $data = $request->validate([
                    'dept_section_content' => ['nullable', 'string'],
                ]);
                $department->details = $data['dept_section_content'] ?? '';
                $department->save();
                $redirectDeptId = $department->id;
            }
        }

        // Handle delete department
        if ($request->has('delete_dept')) {
            $deptId = (int) $request->input('delete_dept');
            $department = CollegeDepartment::where('college_slug', $college)->find($deptId);
            
            if ($department) {
                // Delete logo file if exists
                if (!empty($department->logo) && file_exists(public_path($department->logo))) {
                    unlink(public_path($department->logo));
                }
                $department->delete();
                
                return redirect()
                    ->route('admin.colleges.show', ['college' => $college, 'section' => 'departments'])
                    ->with('success', 'Department deleted successfully.');
            }
        }

        $routeParams = ['college' => $college, 'section' => 'departments'];
        if ($redirectDeptId !== null) {
            return redirect()
                ->to(route('admin.colleges.show', $routeParams) . '?department=' . $redirectDeptId)
                ->with('success', 'Department updated successfully.');
        }

        return redirect()
            ->route('admin.colleges.show', $routeParams)
            ->with('success', 'Department updated successfully.');
    }
    
    private function handleExploreAction(Request $request, string $college): RedirectResponse
    {
        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }
        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }

        // Get current explore items
        $sectionContent = CollegeSection::query()
            ->where('college_slug', $college)
            ->where('section_slug', 'explore')
            ->first();
        
        $exploreItems = [];
        if ($sectionContent && !empty($sectionContent->meta)) {
            $decoded = $sectionContent->meta;
            if (is_array($decoded) && isset($decoded['explore_items'])) {
                $exploreItems = $decoded['explore_items'];
            }
        }

        // Handle add explore item
        if ($request->has('explore_name')) {
            $data = $request->validate([
                'explore_name' => ['required', 'string', 'max:255'],
                'explore_image' => ['nullable', 'image', 'max:2048'],
                'explore_description' => ['nullable', 'string'],
            ]);

            $image = null;
            if ($request->hasFile('explore_image')) {
                $file = $request->file('explore_image');
                $filename = time() . '_' . Str::slug($data['explore_name']) . '.' . $file->getClientOriginalExtension();
                $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/explore", $file, $filename);
                if ($imagePath) {
                    $image = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                }
            }

            $exploreItems[] = [
                'name' => $data['explore_name'],
                'image' => $image,
                'description' => $data['explore_description'] ?? '',
            ];
        }

        // Handle edit explore item
        if ($request->has('edit_explore')) {
            $index = (int) $request->input('edit_explore');
            if (isset($exploreItems[$index])) {
                $data = $request->validate([
                    'edit_explore_name' => ['required', 'string', 'max:255'],
                    'edit_explore_image' => ['nullable', 'image', 'max:2048'],
                    'edit_explore_description' => ['nullable', 'string'],
                ]);

                $exploreItems[$index]['name'] = $data['edit_explore_name'];
                $exploreItems[$index]['description'] = $data['edit_explore_description'] ?? '';

                if ($request->hasFile('edit_explore_image')) {
                    // Delete old image if exists
                    if (!empty($exploreItems[$index]['image']) && file_exists(public_path($exploreItems[$index]['image']))) {
                        unlink(public_path($exploreItems[$index]['image']));
                    }

                    $file = $request->file('edit_explore_image');
                    $filename = time() . '_' . Str::slug($data['edit_explore_name']) . '.' . $file->getClientOriginalExtension();
                    $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/explore", $file, $filename);
                    if ($imagePath) {
                        $exploreItems[$index]['image'] = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                    }
                }
            }
        }

        // Handle delete explore item
        if ($request->has('delete_explore')) {
            $index = (int) $request->input('delete_explore');
            if (isset($exploreItems[$index])) {
                // Delete image file if exists
                if (!empty($exploreItems[$index]['image']) && file_exists(public_path($exploreItems[$index]['image']))) {
                    unlink(public_path($exploreItems[$index]['image']));
                }
                array_splice($exploreItems, $index, 1);
            }
        }

        // Save explore items back to database
        CollegeSection::updateOrCreate(
            [
                'college_slug' => $college,
                'section_slug' => 'explore',
            ],
            [
                'title' => 'Explore',
                'meta' => ['explore_items' => $exploreItems],
            ]
        );

        return redirect()
            ->route('admin.colleges.show', ['college' => $college, 'section' => 'explore'])
            ->with('success', 'Explore item updated successfully.');
    }

    /** @return array<string, string> */
    public static function getInstituteSections(): array
    {
        return [
            'overview' => 'Overview',
            'goals' => 'Goals',
            'history' => 'History',
            'staff' => 'Staff',
            'research' => 'Research',
            'extension' => 'Extension',
            'facilities' => 'Facilities',
        ];
    }

    public function showInstitute(Request $request, string $college, int $instituteId): View
    {
        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }
        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }

        // Find institute by ID
        $institute = CollegeInstitute::where('college_slug', $college)
            ->findOrFail($instituteId);

        // Use institute-specific sections
        $sections = self::getInstituteSections();
        $section = $request->query('section') ?? array_key_first($sections);
        if (! isset($sections[$section])) {
            $section = array_key_first($sections);
        }

        // Get section content
    $storedContent = $institute->getSection($section) ?? [];
    
    // Fetch data from new tables if applicable
    $sectionData = [];
    if ($section === 'goals') {
        $sectionData['goals'] = $institute->goals()->orderBy('sort_order')->get();
    } elseif ($section === 'staff') {
        $sectionData['staff'] = $institute->staff()->orderBy('sort_order')->get();
    } elseif ($section === 'history') {
        // History is on the model now
        $sectionData['history'] = $institute->history;
    } elseif (in_array($section, ['research', 'extension', 'facilities'])) {
        // Map these to 'items' for the generic view or pass as specific variable
        // The view expects 'items' in $sectionContent for generic display, 
        // OR we can pass a specific variable. Let's map to items for now if structure matches.
        $relation = $section; // research, extension, facilities
        $items = $institute->$relation()->orderBy('created_at', 'desc')->get();
        // Convert to array or collection that view can use. 
        // The view checks $sectionContent['items'].
        // Let's populate $storedContent['items'] with these models.
        $storedContent['items'] = $items;
    }

    $sectionContent = array_merge([
        'title' => $sections[$section],
        'body' => '',
    ], $storedContent, $sectionData);

    // Get faculty list for this college (scoped to department/institute usually, but here we might just show all faculty or scope it)
    // For now, mirroring department behavior where faculty might be filtered by name.
    // But institutes don't have a 'department' field they are usually the filter themselves.
    $facultyList = Faculty::where('college_slug', $college)
        ->where('department', $institute->name)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

    return view('admin.colleges.show-institute', [
        'collegeSlug' => $college,
        'collegeName' => $colleges[$college],
        'institute' => $institute,
        'sections' => $sections,
        'currentSection' => $section,
        'sectionContent' => $sectionContent,
        'facultyList' => $facultyList,
    ]);
    }

    public function editInstituteSection(Request $request, string $college, int $institute, string $section): View
    {
        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }
        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }

        // Find institute by ID
        $instituteModel = CollegeInstitute::where('college_slug', $college)
            ->findOrFail($institute);

        // Use institute-specific sections
        $sections = self::getInstituteSections();
        if (! isset($sections[$section])) {
            abort(404, 'Section not found.');
        }

        // Get section content
        $storedContent = $instituteModel->getSection($section) ?? [];

        // Special handling for new sections
        if ($section === 'goals') {
             $goals = InstituteGoal::where('institute_id', $instituteModel->id)->orderBy('sort_order')->get();
             // Format for view if necessary, or pass as is. The view expects 'content' for objectives.
             // But we are moving to standard list.
             // We can pass $goals separately or put in sectionContent.
        } elseif ($section === 'history') {
             $storedContent = ['history' => $instituteModel->history];
        } elseif ($section === 'staff') {
             $staff = InstituteStaff::where('institute_id', $instituteModel->id)->orderBy('sort_order')->get();
        } elseif ($section === 'research') {
             $research = InstituteResearch::where('institute_id', $instituteModel->id)->orderBy('sort_order')->get();
        } elseif ($section === 'extension') {
             $extension = InstituteExtension::where('institute_id', $instituteModel->id)->orderBy('sort_order')->get();
        } elseif ($section === 'facilities') {
             $facilities = InstituteFacility::where('institute_id', $instituteModel->id)->orderBy('sort_order')->get();
        }

        $sectionContent = array_merge([
            'title' => $sections[$section],
            'body' => '',
        ], $storedContent);

        return view('admin.colleges.edit-institute-section', [
            'collegeSlug' => $college,
            'collegeName' => $colleges[$college],
            'institute' => $instituteModel,
            'sectionSlug' => $section,
            'sectionName' => $sections[$section],
            'content' => $sectionContent,
            'goals' => $goals ?? [],
            'staff' => $staff ?? [],
            'research' => $research ?? [],
            'extension' => $extension ?? [],
            'facilities' => $facilities ?? [],
        ]);
    }

    public function updateInstitute(Request $request, string $college, int $instituteId): RedirectResponse
    {
        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }
        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }

        // Find institute by ID
        $institute = CollegeInstitute::where('college_slug', $college)
            ->findOrFail($instituteId);

        // If saving an institute section
        if ($request->has('save_inst_section')) {
            $section = $request->input('section') ?? 'overview';
            
            // Handle banner edit mode
            if ($request->has('_banner_edit')) {
                $data = $request->validate([
                    'banner_image' => ['nullable', 'image', 'max:2048'],
                ]);

                $currentSection = $institute->getSection($section) ?? [];

                // Initialize banner_images array
                $bannerImages = $currentSection['banner_images'] ?? [];
                if (empty($bannerImages) && !empty($currentSection['banner_image'])) {
                    $bannerImages[] = $currentSection['banner_image'];
                }

                // Handle deletion
                if ($request->has('delete_banner_image')) {
                    $indexToDelete = (int) $request->input('delete_banner_image');
                    if (isset($bannerImages[$indexToDelete])) {
                        if (file_exists(public_path($bannerImages[$indexToDelete]))) {
                            @unlink(public_path($bannerImages[$indexToDelete]));
                        }
                        unset($bannerImages[$indexToDelete]);
                        $bannerImages = array_values($bannerImages);
                    }
                }

                // Handle banner image upload
                if ($request->hasFile('banner_image')) {
                    if (count($bannerImages) < 3) {
                        $file = $request->file('banner_image');
                        $filename = time() . '_banner_' . Str::slug($institute->name) . '_' . count($bannerImages) . '.' . $file->getClientOriginalExtension();
                        $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/institutes/" . Str::slug($institute->name) . "/banners", $file, $filename);
                        if ($imagePath) {
                            $fileId = app(\App\Services\GoogleDriveService::class)->getFileId($imagePath);
                            $bannerImages[] = 'media/proxy/' . ($fileId ?? $imagePath);
                        }
                    }
                }

                // Update section data
                $currentSection['banner_images'] = $bannerImages;
                $currentSection['banner_image'] = $bannerImages[0] ?? null;

                $institute->setSection($section, $currentSection);
                $institute->save();

                return redirect()->route('admin.colleges.show-institute', [
                    'college' => $college,
                    'institute' => $instituteId,
                    'section' => $section
                ])->with('success', 'Banner updated successfully.');
            }

            // Handle programs edit mode
            if ($request->has('_programs_edit')) {
                $programs = $request->input('programs', []);
                $institute->programs()->delete();

                if (is_array($programs)) {
                    foreach ($programs as $index => $item) {
                        $imagePath = $item['existing_image'] ?? null;
                        
                        if ($request->hasFile("programs.{$index}.image")) {
                            $file = $request->file("programs.{$index}.image");
                            $filename = time() . '_program_' . $index . '_' . Str::slug($institute->name) . '.' . $file->getClientOriginalExtension();
                            $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/institutes/" . Str::slug($institute->name) . "/programs", $file, $filename);
                            if ($imagePath) {
                                $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                            }
                        }

                        \App\Models\DepartmentProgram::create([
                            'institute_id' => $institute->id,
                            'title' => $item['title'],
                            'description' => $item['description'] ?? '',
                            'image' => $imagePath,
                            'sort_order' => $index,
                            'created_at' => $item['created_at'] ?? now(),
                        ]);
                    }
                }

                $institute->programs_is_visible = $request->has('is_visible');
                $institute->save();

                return redirect()->route('admin.colleges.show-institute', [
                    'college' => $college,
                    'institute' => $instituteId,
                    'section' => $section
                ])->with('success', 'Programs updated successfully.');
            }

            // Handle relational sections (Research, Extension, Facilities, etc.)
            $relationalMapping = [
                'research' => ['model' => \App\Models\InstituteResearch::class, 'visibility_col' => 'research_is_visible'],
                'extension' => ['model' => \App\Models\InstituteExtension::class, 'visibility_col' => 'extension_is_visible'],
                'facilities' => ['model' => \App\Models\InstituteFacility::class, 'visibility_col' => 'facilities_is_visible'],
            ];

            if (isset($relationalMapping[$section])) {
                $mapping = $relationalMapping[$section];
                $items = $request->input('items', []);
                
                // Delete existing items for this institute
                $mapping['model']::where('institute_id', $institute->id)->delete();

                if (is_array($items)) {
                    foreach ($items as $index => $item) {
                        $imagePath = $item['existing_image'] ?? null;
                        
                        if ($request->hasFile("items.{$index}.image")) {
                            $file = $request->file("items.{$index}.image");
                            $filename = time() . '_' . $section . '_' . $index . '_' . Str::slug($institute->name) . '.' . $file->getClientOriginalExtension();
                            $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/institutes/" . Str::slug($institute->name) . "/{$section}", $file, $filename);
                            if ($imagePath) {
                                $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                            }
                        }

                        $data = [
                            'institute_id' => $institute->id,
                            'title' => $item['title'],
                            'description' => $item['description'] ?? '',
                            'image' => $imagePath,
                            'sort_order' => $index,
                            'created_at' => $item['created_at'] ?? now(),
                        ];

                        $mapping['model']::create($data);
                    }
                }

                $institute->save();

                return redirect()->route('admin.colleges.show-institute', [
                    'college' => $college,
                    'institute' => $instituteId,
                    'section' => $section
                ])->with('success', ucfirst($section) . ' updated successfully.');
            }

            // Handle Goals
            if ($section === 'goals') {
                $goals = $request->input('goals', []);
                
                InstituteGoal::where('institute_id', $institute->id)->delete();

                if (is_array($goals)) {
                    foreach ($goals as $index => $content) {
                        if (empty($content)) continue;
                        InstituteGoal::create([
                            'institute_id' => $institute->id,
                            'content' => $content,
                            'sort_order' => $index,
                        ]);
                    }
                }

                return redirect()->route('admin.colleges.show-institute', [
                    'college' => $college,
                    'institute' => $instituteId,
                    'section' => $section
                ])->with('success', 'Goals updated successfully.');
            }

            // Handle History
            if ($section === 'history') {
                $validated = $request->validate([
                    'history' => ['nullable', 'string'],
                ]);
                
                $institute->history = $validated['history'];
                $institute->save();

                return redirect()->route('admin.colleges.show-institute', [
                    'college' => $college,
                    'institute' => $instituteId,
                    'section' => $section
                ])->with('success', 'History updated successfully.');
            }

            // Handle Staff
            if ($section === 'staff') {
                $staff = $request->input('staff', []);
                
                InstituteStaff::where('institute_id', $institute->id)->delete();

                if (is_array($staff)) {
                    foreach ($staff as $index => $item) {
                        if (empty($item['name'])) continue;

                        $photoPath = $item['existing_photo'] ?? null;
                        
                        if ($request->hasFile("staff.{$index}.photo")) {
                            $file = $request->file("staff.{$index}.photo");
                            $filename = time() . '_staff_' . $index . '_' . Str::slug($institute->name) . '.' . $file->getClientOriginalExtension();
                            $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/institutes/" . Str::slug($institute->name) . "/staff", $file, $filename);
                            if ($imagePath) {
                                $photoPath = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                            }
                        }

                        InstituteStaff::create([
                            'institute_id' => $institute->id,
                            'name' => $item['name'],
                            'position' => $item['position'] ?? null,
                            'photo' => $photoPath,
                            'sort_order' => $index,
                        ]);
                    }
                }

                return redirect()->route('admin.colleges.show-institute', [
                    'college' => $college,
                    'institute' => $instituteId,
                    'section' => $section
                ])->with('success', 'Staff updated successfully.');
            }

            // General section update (Overview and others)
            $data = $request->all();
            
            // Handle logo upload
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = time() . '_logo_' . Str::slug($institute->name) . '.' . $file->getClientOriginalExtension();
                $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/institutes/" . Str::slug($institute->name) . "/logos", $file, $filename);
                if ($imagePath) {
                    $institute->logo = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                }
                $data['logo'] = $institute->logo;
            }

            // Handle card image upload
            if ($request->hasFile('card_image')) {
                $file = $request->file('card_image');
                $filename = time() . '_card_' . Str::slug($institute->name) . '.' . $file->getClientOriginalExtension();
                $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/institutes/" . Str::slug($institute->name) . "/logos", $file, $filename);
                if ($imagePath) {
                    $institute->card_image = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                }
                $data['card_image'] = $institute->card_image;
            }

            // Handle Banner Images (if overview)
            if ($section === 'overview') {
                $currentSection = $institute->getSection($section) ?? [];
                $bannerImages = $currentSection['banner_images'] ?? [];
                if (empty($bannerImages) && !empty($currentSection['banner_image'])) {
                    $bannerImages[] = $currentSection['banner_image'];
                }

                // Handle deletion
                if ($request->has('delete_banner_image')) {
                    $indexToDelete = (int) $request->input('delete_banner_image');
                    if (isset($bannerImages[$indexToDelete])) {
                        if (file_exists(public_path($bannerImages[$indexToDelete]))) {
                            @unlink(public_path($bannerImages[$indexToDelete]));
                        }
                        unset($bannerImages[$indexToDelete]);
                        $bannerImages = array_values($bannerImages);
                    }
                }

                // Handle banner image upload
                if ($request->hasFile('banner_image')) {
                    if (count($bannerImages) < 3) {
                        $file = $request->file('banner_image');
                        $filename = time() . '_banner_' . Str::slug($institute->name) . '_' . count($bannerImages) . '.' . $file->getClientOriginalExtension();
                        $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/institutes/" . Str::slug($institute->name) . "/banners", $file, $filename);
                        if ($imagePath) {
                            $fileId = app(\App\Services\GoogleDriveService::class)->getFileId($imagePath);
                            $bannerImages[] = 'media/proxy/' . ($fileId ?? $imagePath);
                        }
                    }
                }

                $data['banner_images'] = $bannerImages;
                $data['banner_image'] = $bannerImages[0] ?? null;
            }

            // Handle outcome image upload
            if ($request->hasFile('graduate_outcomes_image')) {
                $file = $request->file('graduate_outcomes_image');
                $filename = time() . '_outcomes_' . Str::slug($institute->name) . '.' . $file->getClientOriginalExtension();
                $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/institutes/" . Str::slug($institute->name) . "/logos", $file, $filename);
                if ($imagePath) {
                    $institute->graduate_outcomes_image = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                }
                $data['graduate_outcomes_image'] = $institute->graduate_outcomes_image;
            }

            $institute->setSection($section, $data);
            $institute->save();

            return redirect()->route('admin.colleges.show-institute', [
                'college' => $college,
                'institute' => $instituteId,
                'section' => $section
            ])->with('success', 'Section updated successfully.');
        }

        // Handle general institute profile update (name, email, etc.)
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $institute->update($data);

        return redirect()->route('admin.colleges.show-institute', [
            'college' => $college,
            'institute' => $instituteId
        ])->with('success', 'Institute profile updated successfully.');
    }

    public function updateAppearance(Request $request, string $college): RedirectResponse
    {
        $user = $request->user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Only superadmins can update appearance.');
        }

        $data = $request->validate([
            'admin_header_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'admin_sidebar_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'admin_logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $collegesDir = public_path('images/colleges');
        if (! is_dir($collegesDir)) {
            mkdir($collegesDir, 0755, true);
        }

        \App\Models\Setting::set('admin_header_color_' . $college, $data['admin_header_color']);
        \App\Models\Setting::set('admin_sidebar_color_' . $college, $data['admin_sidebar_color']);

        if ($request->hasFile('admin_logo')) {
            // Delete old logo file if it exists
            $oldLogoPath = \App\Models\Setting::get('admin_logo_path_' . $college, null);
            if ($oldLogoPath && file_exists(public_path($oldLogoPath))) {
                @unlink(public_path($oldLogoPath));
            }
            
            $file = $request->file('admin_logo');
            $ext = $file->getClientOriginalExtension() ?: 'png';
            $name = $college . '-' . Str::random(8) . '.' . $ext;
            $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("colleges/{$college}/admin", $file, $name);
            if ($imagePath) {
                \App\Models\Setting::set('admin_logo_path_' . $college, \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath));
            }
        }

        return redirect()->route('admin.colleges.show', ['college' => $college, 'section' => 'appearance'])
            ->with('success', 'Appearance settings saved.');
    }}
