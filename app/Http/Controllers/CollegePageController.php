<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\CollegeController;
use App\Http\Controllers\Admin\SettingsController;
use App\Models\College;
use App\Models\CollegeDepartment;
use App\Models\CollegeDownload;
use App\Models\CollegeVideo;
use App\Models\CollegeRetro;
use App\Models\CollegeSection;
use App\Models\CollegeExtension;
use App\Models\CollegeTraining;
use App\Models\Setting;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CollegePageController extends Controller
{
    /** Short display names per college slug (for nav, loader). */
    private const SHORT_NAMES = [
        'agriculture' => 'CAg',
        'arts-and-social-sciences' => 'CASS',
        'business-and-accountancy' => 'CBAA',
        'education' => 'CED',
        'engineering' => 'CEn',
        'fisheries' => 'CoF',
        'home-science-and-industry' => 'CHSI',
        'veterinary-science-and-medicine' => 'CVSM',
        'science' => 'CoS',
        'dot-uni' => 'DOT-Uni',
    ];

    private function resolveCollegeLogoUrl(string $college, ?College $collegeModel = null): string
    {
        $collegeModel ??= College::find($college);

        if ($collegeModel && !empty($collegeModel->icon)) {
            $overviewIconUrl = \App\Providers\AppServiceProvider::resolveLogoUrl($collegeModel->icon);
            if ($overviewIconUrl) {
                return $overviewIconUrl;
            }
        }

        $globalLogoPath = Setting::get('admin_logo_path', null);
        if ($globalLogoPath) {
            $globalLogoUrl = \App\Providers\AppServiceProvider::resolveLogoUrl($globalLogoPath);
            if ($globalLogoUrl) {
                return $globalLogoUrl;
            }
        }

        return asset('images/colleges/main.webp');
    }

    public function show(Request $request, string $college): View
    {
        $colleges = CollegeController::getColleges();
        if (! isset($colleges[$college])) {
            throw new NotFoundHttpException('College not found.');
        }

        // Get college model for icons and images
        $collegeModel = College::find($college);

        $collegeName = $colleges[$college];
        $collegeShortName = self::SHORT_NAMES[$college] ?? $collegeName;

        $collegeLogoUrl = $this->resolveCollegeLogoUrl($college, $collegeModel);

        // Appearance colors
        $headerColor = Setting::get('admin_header_color_' . $college, null)
            ?? Setting::get('admin_header_color_' . $college . '_editor', null)
            ?? SettingsController::HEADER_COLOR_DEFAULT;
        $accentColor = Setting::get('admin_sidebar_color_' . $college, null)
            ?? Setting::get('admin_sidebar_color_' . $college . '_editor', null)
            ?? SettingsController::SIDEBAR_COLOR_DEFAULT;

        // Default Hero Background
        $adminDefaultHeroPath = Setting::get('admin_default_hero', null);
        $adminDefaultHeroUrl = $adminDefaultHeroPath ? \App\Providers\AppServiceProvider::resolveLogoUrl($adminDefaultHeroPath) : asset('images/CLSU.jpg');

        // College contact info
        $collegeContact = \App\Models\CollegeContact::where('college_slug', $college)->first();
        $collegeEmail = $collegeContact->email ?? $college . '@clsu.edu.ph';
        $collegePhone = $collegeContact->phone ?? '(044) 940 8785';

        // President's Contact Info (Global)
        $presidentEmail = Setting::get('admin_president_email', 'op@clsu.edu.ph');
        $presidentPhone = Setting::get('admin_president_phone', '(044) 940 8785');

        // Get departments for this college
        $departments = CollegeDepartment::where('college_slug', $college)
            ->orderBy('name')
            ->get();

        // Get facilities for this college
        $facilities = \App\Models\Facility::where('college_slug', $college)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Get featured video
        $featuredVideo = CollegeVideo::where('college_slug', $college)->first();

        // Get retro section data
        $retroList = CollegeRetro::where('college_slug', $college)
            ->whereNull('department_id')
            ->where('is_visible', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        // Get overview section for "About the College" text
        $overviewSection = \App\Models\CollegeSection::published()
            ->where('college_slug', $college)
            ->whereIn('section_slug', ['overview', 'Overview'])
            ->latest('updated_at')
            ->first();

        $departmentsSection = \App\Models\CollegeSection::published()
            ->where('college_slug', $college)
            ->where('section_slug', 'departments')
            ->first();

        // Get institutes section for visibility check
        $institutesSection = \App\Models\CollegeSection::published()
            ->where('college_slug', $college)
            ->where('section_slug', 'institutes')
            ->first();

        // Get FAQs for this college if the section is visible and published
        $faqSection = \App\Models\CollegeSection::published()
            ->where('college_slug', $college)
            ->where('section_slug', 'faq')
            ->first();

        if ($faqSection && !$faqSection->is_visible) {
            $faqs = collect();
        } else {
            $faqs = \App\Models\CollegeFaq::where('college_slug', $college)
                ->where('is_visible', true)
                ->orderBy('sort_order')
                ->get();
        }

        // Get recent articles (news)
        $news = \App\Models\Article::where(function ($query) use ($college) {
                $query->where('college_slug', $college)
                      ->orWhereNull('college_slug');
            })
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->get()
            ->map(function ($item) {
                $item->type = $item->type ?: 'news';
                $item->route_name = 'news.announcement.detail';
                return $item;
            });

        // Get recent announcements
        $announcements = \App\Models\Announcement::where(function ($query) use ($college) {
                $query->where('college_slug', $college)
                      ->orWhereNull('college_slug');
            })
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->get()
            ->map(function ($ann) {
                $ann->banner = $ann->image;
                $ann->type = 'announcement';
                $ann->route_name = 'announcement.detail';
                return $ann;
            });

        // Merge and sort
        $articles = $news->concat($announcements)
            ->sortByDesc('published_at')
            ->take(4);
        
        // Get extension section
        $extensionSection = \App\Models\CollegeSection::published()
            ->where('college_slug', $college)
            ->where('section_slug', 'extension')
            ->first();

        $extensions = CollegeExtension::where('college_slug', $college)
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get();

        // Get admissions section
        $admissionsSection = \App\Models\CollegeSection::published()
            ->where('college_slug', $college)
            ->where('section_slug', 'admissions')
            ->first();

        // Get recently migrated training data from training list
        $trainings = CollegeTraining::where('college_slug', $college)
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get();

        // Get training section for title and body
        $trainingSection = \App\Models\CollegeSection::published()
            ->where('college_slug', $college)
            ->where('section_slug', 'training')
            ->first();

        // Get scholarships section
        $scholarshipsSection = \App\Models\CollegeSection::where('college_slug', $college)
            ->where('section_slug', 'scholarships')
            ->first();

        // Get scholarships from dedicated table (college-specific + global)
        $scholarships = (!$scholarshipsSection || $scholarshipsSection->is_visible)
            ? \App\Models\Scholarship::whereIn('college_slug', [$college, '_global'])
                ->orderBy('sort_order')
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        // Get institutes for this college
        $institutes = \App\Models\CollegeInstitute::where('college_slug', $college)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Process overview data for retro button visibility regardless of its published status
        $overviewSectionForMeta = \App\Models\CollegeSection::where('college_slug', $college)
            ->where('section_slug', 'overview')
            ->first();

        $showPrimaryRetroBtn = true;
        $showSecondaryRetroBtn = true;
        if ($overviewSectionForMeta && !empty($overviewSectionForMeta->meta)) {
            $metaData = $overviewSectionForMeta->meta;
            if (is_array($metaData)) {
                $showPrimaryRetroBtn = filter_var($metaData['show_primary_retro_btn'] ?? true, FILTER_VALIDATE_BOOLEAN);
                $showSecondaryRetroBtn = filter_var($metaData['show_secondary_retro_btn'] ?? true, FILTER_VALIDATE_BOOLEAN);
            }
        }

        // Get facilities section
        $facilitiesSection = \App\Models\CollegeSection::published()
            ->where('college_slug', $college)
            ->where('section_slug', 'facilities')
            ->first();

        // Get accreditation section
        $accreditationSection = \App\Models\CollegeSection::published()
            ->where('college_slug', $college)
            ->where('section_slug', 'accreditation')
            ->first();

        $alumniSection = \App\Models\CollegeSection::query()
            ->where('college_slug', $college)
            ->where('section_slug', 'alumni')
            ->first();

        $alumniPreview = \App\Models\DepartmentAlumnus::with('department')
            ->where(function ($query) use ($college) {
                $query->where(function ($directQuery) use ($college) {
                    $directQuery->where('college_slug', $college)
                        ->whereNull('department_id')
                        ->whereNull('institute_id');
                })->orWhereHas('department', function ($departmentQuery) use ($college) {
                    $departmentQuery->where('college_slug', $college)
                        ->where('alumni_is_visible', true);
                });
            })
            ->latest()
            ->limit(3)
            ->get();

        return view('college-blade', [
            'collegeName' => $collegeName,
            'collegeSlug' => $college,
            'collegeShortName' => $collegeShortName,
            'collegeLogoUrl' => $collegeLogoUrl,
            'collegeModel' => $collegeModel,
            'adminDefaultHeroUrl' => $adminDefaultHeroUrl,
            'headerColor' => $headerColor,
            'accentColor' => $accentColor,
            'collegeEmail' => $collegeEmail,
            'collegePhone' => $collegePhone,
            'departments' => $departments,
            'departmentsSection' => $departmentsSection,
            'facilities' => $facilities,
            'facilitiesSection' => $facilitiesSection,
            'featuredVideo' => $featuredVideo,
            'overviewSection' => $overviewSection,
            'retroList' => $retroList,
            'institutesSection' => $institutesSection,
            'institutes' => $institutes,
            'faqs' => $faqs,
            'faqSection' => $faqSection,
            'collegeContact' => $collegeContact,
            'presidentEmail' => $presidentEmail,
            'presidentPhone' => $presidentPhone,
            'articles' => $articles,
            'extensionSection' => $extensionSection,
            'extensions' => $extensions,
            'admissionsSection' => $admissionsSection,
            'trainingSection' => $trainingSection,
            'trainings' => $trainings,
            'scholarshipsSection' => $scholarshipsSection,
            'scholarships' => $scholarships,
            'showPrimaryRetroBtn' => $showPrimaryRetroBtn,
            'showSecondaryRetroBtn' => $showSecondaryRetroBtn,
            'alumniSection' => $alumniSection,
            'testimonialPreview' => $alumniPreview,
            'accreditationPreview' => \App\Models\CollegeAccreditation::where('college_slug', $college)
                ->where('is_visible', true)
                ->orderBy('sort_order')
                ->limit(5)
                ->get(),
            'accreditationSection' => $accreditationSection,
            'membershipPreview' => \App\Models\CollegeMembership::with('department')
                ->where('college_slug', $college)
                ->where('is_visible', true)
                ->orderBy('sort_order')
                ->limit(5)
                ->get(),
            'membershipSection' => \App\Models\CollegeSection::published()
                ->where('college_slug', $college)
                ->where('section_slug', 'membership')
                ->first(),
            'organizationsSection' => \App\Models\CollegeSection::published()
                ->where('college_slug', $college)
                ->where('section_slug', 'organizations')
                ->first(),
            'downloadsSection' => \App\Models\CollegeSection::published()
                ->where('section_slug', 'downloads')
                ->where('college_slug', $college)
                ->first(),
            'organizationPreview' => \App\Models\CollegeOrganization::where('college_slug', $college)
                ->where('is_visible', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function explore(Request $request, string $college): View
    {
        $colleges = CollegeController::getColleges();
        if (! isset($colleges[$college])) {
            throw new NotFoundHttpException('College not found.');
        }

        $collegeName = $colleges[$college];
        $collegeShortName = self::SHORT_NAMES[$college] ?? $collegeName;

        $collegeLogoUrl = $this->resolveCollegeLogoUrl($college);

        // Appearance colors
        $headerColor = Setting::get('admin_header_color_' . $college, null)
            ?? Setting::get('admin_header_color_' . $college . '_editor', null)
            ?? SettingsController::HEADER_COLOR_DEFAULT;
        $accentColor = Setting::get('admin_sidebar_color_' . $college, null)
            ?? Setting::get('admin_sidebar_color_' . $college . '_editor', null)
            ?? SettingsController::SIDEBAR_COLOR_DEFAULT;

        // Get explore items from meta column
        $exploreItems = [];
        $sectionContent = \App\Models\CollegeSection::query()
            ->where('college_slug', $college)
            ->where('section_slug', 'explore')
            ->first();
        
        if ($sectionContent && !empty($sectionContent->meta)) {
            $decoded = $sectionContent->meta;
            if (is_array($decoded) && isset($decoded['explore_items'])) {
                $exploreItems = $decoded['explore_items'];
            }
        }

        // Get section title and description
        $sectionTitle = $sectionContent->title ?? 'Explore';
        $sectionDescription = $sectionContent->body ?? '';

        // College email
        $collegeEmail = Setting::get('admin_email_' . $college, $college . '@clsu.edu.ph');

        return view('college-explore', [
            'collegeName' => $collegeName,
            'collegeSlug' => $college,
            'collegeShortName' => $collegeShortName,
            'collegeLogoUrl' => $collegeLogoUrl,
            'headerColor' => $headerColor,
            'accentColor' => $accentColor,
            'exploreItems' => $exploreItems,
            'sectionTitle' => $sectionTitle,
            'sectionDescription' => $sectionDescription,
            'collegeEmail' => $collegeEmail,
            'collegeContact' => \App\Models\CollegeContact::where('college_slug', $college)->first(),
        ]);
    }

    public function faculty(Request $request, string $college): View|\Illuminate\Http\RedirectResponse
    {
        $colleges = CollegeController::getColleges();
        if (! isset($colleges[$college])) {
            throw new NotFoundHttpException('College not found.');
        }

        $collegeName = $colleges[$college];
        $collegeShortName = self::SHORT_NAMES[$college] ?? $collegeName;

        $collegeLogoUrl = $this->resolveCollegeLogoUrl($college);

        // Appearance colors
        $headerColor = Setting::get('admin_header_color_' . $college, null)
            ?? Setting::get('admin_header_color_' . $college . '_editor', null)
            ?? SettingsController::HEADER_COLOR_DEFAULT;
        $accentColor = Setting::get('admin_sidebar_color_' . $college, null)
            ?? Setting::get('admin_sidebar_color_' . $college . '_editor', null)
            ?? SettingsController::SIDEBAR_COLOR_DEFAULT;

        // College email
        $collegeEmail = Setting::get('admin_email_' . $college, $college . '@clsu.edu.ph');

        // Get faculty for this college
        $faculty = \App\Models\Faculty::where('college_slug', $college)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Get institute staff and merge into faculty list
        $institutes = \App\Models\CollegeInstitute::where('college_slug', $college)->with('staff')->get();
        foreach ($institutes as $institute) {
            foreach ($institute->staff as $staff) {
                // Dynamically add 'department' property for display purposes
                $staff->department = $institute->name;
                
                // Clean photo path to match Faculty model format (which is just 'faculty/filename.jpg')
                // InstituteStaff saves as '/images/faculty/filename.jpg', but view prepends 'images/'
                if ($staff->photo) {
                    $staff->photo = ltrim(str_replace(['/images/', 'images/'], '', $staff->photo), '/');
                }
            }
            // Use concat to avoid overwriting models with the same ID (since they are from different tables)
            $faculty = $faculty->concat($institute->staff);
        }

        $unassignedInstituteStaff = \App\Models\InstituteStaff::where('college_slug', $college)
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

        $faculty = $faculty->concat($unassignedInstituteStaff);
        // Sort by surname (last word of name)
        $faculty = $faculty->sortBy(function($person) {
            $parts = explode(' ', trim($person->name));
            return end($parts);
        }, SORT_NATURAL | SORT_FLAG_CASE);

        // Get faculty section data from college_section table
        $facultySection = \App\Models\CollegeSection::where('college_slug', $college)
            ->where('section_slug', 'faculty')
            ->first();

        if ($facultySection && (! $facultySection->is_visible || ! $facultySection->isPublished())) {
            return redirect()->route('college.show', ['college' => $college]);
        }
        
        $sectionTitle = $facultySection->title ?? 'Faculty';
        $sectionDescription = $facultySection->body ?? 'Meet our faculty and staff';

        return view('college-faculty', [
            'collegeName' => $collegeName,
            'collegeSlug' => $college,
            'collegeShortName' => $collegeShortName,
            'collegeLogoUrl' => $collegeLogoUrl,
            'headerColor' => $headerColor,
            'accentColor' => $accentColor,
            'collegeEmail' => $collegeEmail,
            'faculty' => $faculty,
            'sectionTitle' => $sectionTitle,
            'sectionDescription' => $sectionDescription,
            'collegeContact' => \App\Models\CollegeContact::where('college_slug', $college)->first(),
        ]);
    }

    public function facilities(Request $request, string $college): View
    {
        $colleges = CollegeController::getColleges();
        if (! isset($colleges[$college])) {
            throw new NotFoundHttpException('College not found.');
        }

        $collegeName = $colleges[$college];
        $collegeShortName = self::SHORT_NAMES[$college] ?? $collegeName;

        $collegeLogoUrl = $this->resolveCollegeLogoUrl($college);

        // Appearance colors
        $headerColor = Setting::get('admin_header_color_' . $college, null)
            ?? Setting::get('admin_header_color_' . $college . '_editor', null)
            ?? SettingsController::HEADER_COLOR_DEFAULT;
        $accentColor = Setting::get('admin_sidebar_color_' . $college, null)
            ?? Setting::get('admin_sidebar_color_' . $college . '_editor', null)
            ?? SettingsController::SIDEBAR_COLOR_DEFAULT;

        // College email
        $collegeEmail = Setting::get('admin_email_' . $college, $college . '@clsu.edu.ph');

        // Get facilities for this college
        $facilities = \App\Models\Facility::where('college_slug', $college)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Get facilities section data from college_section table
        $facilitiesSection = \App\Models\CollegeSection::where('college_slug', $college)
            ->where('section_slug', 'facilities')
            ->first();

        if ($facilitiesSection && (! $facilitiesSection->is_visible || ! $facilitiesSection->isPublished())) {
            return redirect()->route('college.show', ['college' => $college]);
        }

        $sectionTitle = $facilitiesSection->title ?? 'Facilities';
        $sectionDescription = $facilitiesSection->body ?? 'Explore our facilities and resources';

        return view('college-facilities', [
            'collegeName' => $collegeName,
            'collegeSlug' => $college,
            'collegeShortName' => $collegeShortName,
            'collegeLogoUrl' => $collegeLogoUrl,
            'headerColor' => $headerColor,
            'accentColor' => $accentColor,
            'collegeEmail' => $collegeEmail,
            'facilities' => $facilities,
            'sectionTitle' => $sectionTitle,
            'sectionDescription' => $sectionDescription,
            'collegeContact' => \App\Models\CollegeContact::where('college_slug', $college)->first(),
        ]);
    }

    public function training(Request $request, string $college): View
    {
        $colleges = CollegeController::getColleges();
        if (! isset($colleges[$college])) {
            throw new NotFoundHttpException('College not found.');
        }

        $collegeName = $colleges[$college];
        $collegeShortName = self::SHORT_NAMES[$college] ?? $collegeName;

        $collegeLogoUrl = $this->resolveCollegeLogoUrl($college);

        // Appearance colors
        $headerColor = Setting::get('admin_header_color_' . $college, null)
            ?? Setting::get('admin_header_color_' . $college . '_editor', null)
            ?? SettingsController::HEADER_COLOR_DEFAULT;
        $accentColor = Setting::get('admin_sidebar_color_' . $college, null)
            ?? Setting::get('admin_sidebar_color_' . $college . '_editor', null)
            ?? SettingsController::SIDEBAR_COLOR_DEFAULT;

        // College email
        $collegeEmail = Setting::get('admin_email_' . $college, $college . '@clsu.edu.ph');

        // Get training section
        $trainingSection = \App\Models\CollegeSection::published()
            ->where('college_slug', $college)
            ->where('section_slug', 'training')
            ->first();

        // Get training list
        $trainingItems = CollegeTraining::where('college_slug', $college)
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get();

        // Title and description
        $sectionTitle = $trainingSection->title ?? 'Training & Workshops';
        $sectionDescription = $trainingSection->body ?? 'Capacity building and skills development.';

        return view('college-training', [
            'collegeName' => $collegeName,
            'collegeSlug' => $college,
            'collegeShortName' => $collegeShortName,
            'collegeLogoUrl' => $collegeLogoUrl,
            'headerColor' => $headerColor,
            'accentColor' => $accentColor,
            'collegeEmail' => $collegeEmail,
            'trainingItems' => $trainingItems,
            'sectionTitle' => $sectionTitle,
            'sectionDescription' => $sectionDescription,
            'collegeContact' => \App\Models\CollegeContact::where('college_slug', $college)->first(),
        ]);
    }

    public function showTraining(Request $request, string $college, string $slug): \Illuminate\Http\RedirectResponse|\Illuminate\View\View
    {
        $colleges = CollegeController::getColleges();
        if (! isset($colleges[$college])) {
            return redirect()->route('college.show', ['college' => $college]);
        }

        $collegeName = $colleges[$college];
        $collegeShortName = self::SHORT_NAMES[$college] ?? $collegeName;

        $collegeLogoUrl = $this->resolveCollegeLogoUrl($college);

        // Appearance colors
        $headerColor = Setting::get('admin_header_color_' . $college, null)
            ?? Setting::get('admin_header_color_' . $college . '_editor', null)
            ?? SettingsController::HEADER_COLOR_DEFAULT;
        $accentColor = Setting::get('admin_sidebar_color_' . $college, null)
            ?? Setting::get('admin_sidebar_color_' . $college . '_editor', null)
            ?? SettingsController::SIDEBAR_COLOR_DEFAULT;

        // College contact info
        $collegeContact = \App\Models\CollegeContact::where('college_slug', $college)->first();
        $collegeEmail = $collegeContact->email ?? $college . '@clsu.edu.ph';
        $collegePhone = $collegeContact->phone ?? '(044) 940 8785';

        // President's Contact Info (Global)
        $presidentEmail = Setting::get('admin_president_email', 'op@clsu.edu.ph');
        $presidentPhone = Setting::get('admin_president_phone', '(044) 940 8785');

        // Get training item
        $trainingItem = CollegeTraining::where('college_slug', $college)
            ->get()
            ->first(function ($t) use ($slug) {
                return \Illuminate\Support\Str::slug($t->title) === $slug;
            });

        // Fallback for ID backward compatibility
        if (!$trainingItem && is_numeric($slug)) {
            $trainingItem = CollegeTraining::where('college_slug', $college)->find((int)$slug);
        }

        if (!$trainingItem) {
            return redirect()->to(route('college.show', ['college' => $college]) . '#training');
        }

        // Recent trainings (excluding current)
        $recentTrainings = CollegeTraining::where('college_slug', $college)
            ->where('id', '!=', $trainingItem->id)
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->limit(5)
            ->get();

        return view('college-training-detail', [
            'collegeName' => $collegeName,
            'collegeSlug' => $college,
            'collegeShortName' => $collegeShortName,
            'collegeLogoUrl' => $collegeLogoUrl,
            'headerColor' => $headerColor,
            'accentColor' => $accentColor,
            'collegeEmail' => $collegeEmail,
            'collegePhone' => $collegePhone,
            'collegeContact' => $collegeContact,
            'presidentEmail' => $presidentEmail,
            'presidentPhone' => $presidentPhone,
            'trainingItem' => $trainingItem,
            'recentTrainings' => $recentTrainings,
            'currentIndex' => $trainingItem->id,
            'departments' => CollegeDepartment::where('college_slug', $college)->orderBy('sort_order')->orderBy('name')->get()
        ]);
    }

    public function scholarships(Request $request, string $college): View
    {
        $colleges = CollegeController::getColleges();
        if (! isset($colleges[$college])) {
            throw new NotFoundHttpException('College not found.');
        }

        $collegeName = $colleges[$college];
        $collegeShortName = self::SHORT_NAMES[$college] ?? $collegeName;

        $collegeLogoUrl = $this->resolveCollegeLogoUrl($college);

        // Appearance colors
        $headerColor = Setting::get('admin_header_color_' . $college, null)
            ?? Setting::get('admin_header_color_' . $college . '_editor', null)
            ?? SettingsController::HEADER_COLOR_DEFAULT;
        $accentColor = Setting::get('admin_sidebar_color_' . $college, null)
            ?? Setting::get('admin_sidebar_color_' . $college . '_editor', null)
            ?? SettingsController::SIDEBAR_COLOR_DEFAULT;

        // College email
        $collegeEmail = Setting::get('admin_email_' . $college, $college . '@clsu.edu.ph');

        // Get scholarships section
        $scholarshipSection = \App\Models\CollegeSection::where('college_slug', $college)
            ->where('section_slug', 'scholarships')
            ->first();

        if ($scholarshipSection && !$scholarshipSection->is_visible) {
            return redirect()->route('college.show', ['college' => $college]);
        }

        // Get scholarships list
        $scholarshipItems = \App\Models\Scholarship::whereIn('college_slug', [$college, '_global'])
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        // Title and description
        $sectionTitle = $scholarshipSection->title ?? 'Scholarships';
        $sectionDescription = $scholarshipSection->body ?? 'Scholarship programs and opportunities for students.';

        return view('college-scholarships', [
            'collegeName' => $collegeName,
            'collegeSlug' => $college,
            'collegeShortName' => $collegeShortName,
            'collegeLogoUrl' => $collegeLogoUrl,
            'headerColor' => $headerColor,
            'accentColor' => $accentColor,
            'collegeEmail' => $collegeEmail,
            'scholarshipItems' => $scholarshipItems,
            'sectionTitle' => $sectionTitle,
            'sectionDescription' => $sectionDescription,
            'scholarshipsSection' => $scholarshipSection,
            'collegeContact' => \App\Models\CollegeContact::where('college_slug', $college)->first(),
        ]);
    }

    public function showScholarship(Request $request, string $college, string $slug): \Illuminate\Http\RedirectResponse|\Illuminate\View\View
    {
        $scholarshipSection = \App\Models\CollegeSection::where('college_slug', $college)
            ->where('section_slug', 'scholarships')
            ->first();

        if ($scholarshipSection && !$scholarshipSection->is_visible) {
            return redirect()->route('college.show', ['college' => $college]);
        }

        $colleges = CollegeController::getColleges();
        if (! isset($colleges[$college])) {
            throw new NotFoundHttpException('College not found.');
        }

        $collegeName = $colleges[$college];
        $collegeShortName = self::SHORT_NAMES[$college] ?? $collegeName;

        $collegeLogoUrl = $this->resolveCollegeLogoUrl($college);

        // Appearance colors
        $headerColor = Setting::get('admin_header_color_' . $college, null)
            ?? Setting::get('admin_header_color_' . $college . '_editor', null)
            ?? SettingsController::HEADER_COLOR_DEFAULT;
        $accentColor = Setting::get('admin_sidebar_color_' . $college, null)
            ?? Setting::get('admin_sidebar_color_' . $college . '_editor', null)
            ?? SettingsController::SIDEBAR_COLOR_DEFAULT;

        // College contact info
        $collegeContact = \App\Models\CollegeContact::where('college_slug', $college)->first();
        $collegeEmail = $collegeContact->email ?? $college . '@clsu.edu.ph';
        $collegePhone = $collegeContact->phone ?? '(044) 940 8785';

        // President's Contact Info (Global)
        $presidentEmail = Setting::get('admin_president_email', 'op@clsu.edu.ph');
        $presidentPhone = Setting::get('admin_president_phone', '(044) 940 8785');

        // Get scholarship by slug (slugified title) or numeric ID fallback
        $scholarship = \App\Models\Scholarship::whereIn('college_slug', [$college, '_global'])
            ->get()
            ->first(function ($s) use ($slug) {
                return \Illuminate\Support\Str::slug($s->title) === $slug;
            });
        // Fallback: try numeric ID for backward compatibility
        if (!$scholarship && is_numeric($slug)) {
            $scholarship = \App\Models\Scholarship::whereIn('college_slug', [$college, '_global'])->find((int) $slug);
        }
        if (!$scholarship) {
            return redirect()->to(route('college.show', ['college' => $college]) . '#scholarships');
        }

        $scholarshipItem = $scholarship->toArray();

        // Other scholarships (excluding current, but include global)
        $otherScholarships = \App\Models\Scholarship::whereIn('college_slug', [$college, '_global'])
            ->where('id', '!=', $scholarship->id)
            ->orderBy('sort_order')
            ->limit(5)
            ->get()
            ->toArray();

        return view('college-scholarship-detail', [
            'collegeName' => $collegeName,
            'collegeSlug' => $college,
            'collegeShortName' => $collegeShortName,
            'collegeLogoUrl' => $collegeLogoUrl,
            'headerColor' => $headerColor,
            'accentColor' => $accentColor,
            'collegeEmail' => $collegeEmail,
            'collegePhone' => $collegePhone,
            'collegeContact' => $collegeContact,
            'presidentEmail' => $presidentEmail,
            'presidentPhone' => $presidentPhone,
            'scholarshipItem' => $scholarshipItem,
            'otherScholarships' => $otherScholarships,
            'currentIndex' => $scholarship->id,
            'departments' => CollegeDepartment::where('college_slug', $college)->orderBy('sort_order')->orderBy('name')->get()
        ]);
    }

    public function organizations(Request $request, string $college): View
    {
        $colleges = CollegeController::getColleges();
        if (! isset($colleges[$college])) {
            throw new NotFoundHttpException('College not found.');
        }

        $collegeName = $colleges[$college];
        $collegeShortName = self::SHORT_NAMES[$college] ?? $collegeName;

        $collegeLogoUrl = $this->resolveCollegeLogoUrl($college);

        // Appearance colors
        $headerColor = Setting::get('admin_header_color_' . $college, null)
            ?? Setting::get('admin_header_color_' . $college . '_editor', null)
            ?? SettingsController::HEADER_COLOR_DEFAULT;
        $accentColor = Setting::get('admin_sidebar_color_' . $college, null)
            ?? Setting::get('admin_sidebar_color_' . $college . '_editor', null)
            ?? SettingsController::SIDEBAR_COLOR_DEFAULT;

        // College email
        $collegeEmail = Setting::get('admin_email_' . $college, $college . '@clsu.edu.ph');

        // Get visible organizations for this college
        $organizations = \App\Models\CollegeOrganization::with('department')
            ->where('college_slug', $college)
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Get organizations section data from college_section table
        $organizationsSection = \App\Models\CollegeSection::where('college_slug', $college)
            ->where('section_slug', 'organizations')
            ->first();

        $sectionTitle = $organizationsSection->title ?? 'Student Organizations';
        $sectionDescription = $organizationsSection->body ?? '';

        return view('college-organizations', [
            'collegeName'     => $collegeName,
            'collegeSlug'     => $college,
            'collegeShortName' => $collegeShortName,
            'collegeLogoUrl'  => $collegeLogoUrl,
            'headerColor'     => $headerColor,
            'accentColor'     => $accentColor,
            'collegeEmail'    => $collegeEmail,
            'organizations'   => $organizations,
            'sectionTitle'    => $sectionTitle,
            'sectionDescription' => $sectionDescription,
            'collegeContact'  => \App\Models\CollegeContact::where('college_slug', $college)->first(),
        ]);
    }

    public function showOrganization(Request $request, string $college, \App\Models\CollegeOrganization $organization): View
    {
        $colleges = CollegeController::getColleges();
        if (! isset($colleges[$college])) {
            throw new NotFoundHttpException('College not found.');
        }
        if ($organization->college_slug !== $college) {
            throw new NotFoundHttpException('Organization not found in this college.');
        }
        if (! $organization->is_visible) {
            throw new NotFoundHttpException('This organization is not publicly visible.');
        }

        $collegeName     = $colleges[$college];
        $collegeShortName = self::SHORT_NAMES[$college] ?? $collegeName;

        $collegeLogoUrl = $this->resolveCollegeLogoUrl($college);

        $headerColor = Setting::get('admin_header_color_' . $college, null)
            ?? Setting::get('admin_header_color_' . $college . '_editor', null)
            ?? SettingsController::HEADER_COLOR_DEFAULT;
        $accentColor = Setting::get('admin_sidebar_color_' . $college, null)
            ?? Setting::get('admin_sidebar_color_' . $college . '_editor', null)
            ?? SettingsController::SIDEBAR_COLOR_DEFAULT;

        $organization->load('department');
        $department = $organization->department;

        // Load section data stored in the JSON `sections` column
        $sectionData = $organization->sections ?? [];
        
        // Ensure core sections exist even if empty (for layout consistency if needed, though view handles it)
        $coreSections = ['overview', 'officers', 'activities', 'gallery'];
        foreach ($coreSections as $core) {
            if (!isset($sectionData[$core])) {
                $sectionData[$core] = [];
            }
        }

        if (isset($sectionData['activities']['items']) && is_array($sectionData['activities']['items'])) {
            $sectionData['activities']['items'] = array_values(array_filter(
                $sectionData['activities']['items'],
                fn ($item) => !array_key_exists('is_visible', $item) || (bool) $item['is_visible']
            ));
        }

        $adviserFaculty = null;
        if (!empty($organization->adviser)) {
            $adviserFaculty = \App\Models\Faculty::where('name', $organization->adviser)
                ->where('college_slug', $college)
                ->first();
        }

        return view('organization', [
            'organization'  => $organization,
            'department'    => $department,
            'collegeName'   => $collegeName,
            'collegeSlug'   => $college,
            'collegeShortName' => $collegeShortName,
            'collegeLogoUrl' => $collegeLogoUrl,
            'headerColor'   => $headerColor,
            'accentColor'   => $accentColor,
            'sectionData'   => $sectionData,
            'adviserFaculty' => $adviserFaculty,
            'collegeContact' => \App\Models\CollegeContact::where('college_slug', $college)->first(),
        ]);
    }

    public function showOrganizationAlbum(Request $request, string $college, \App\Models\CollegeOrganization $organization, int $index): View
    {
        $colleges = CollegeController::getColleges();
        if (! isset($colleges[$college])) {
            throw new NotFoundHttpException('College not found.');
        }
        if ($organization->college_slug !== $college) {
            throw new NotFoundHttpException('Organization not found in this college.');
        }
        if (! $organization->is_visible) {
            throw new NotFoundHttpException('This organization is not publicly visible.');
        }

        $collegeName     = $colleges[$college];
        $collegeShortName = self::SHORT_NAMES[$college] ?? $collegeName;

        $collegeLogoUrl = $this->resolveCollegeLogoUrl($college);

        $headerColor = Setting::get('admin_header_color_' . $college, null)
            ?? Setting::get('admin_header_color_' . $college . '_editor', null)
            ?? SettingsController::HEADER_COLOR_DEFAULT;
        $accentColor = Setting::get('admin_sidebar_color_' . $college, null)
            ?? Setting::get('admin_sidebar_color_' . $college . '_editor', null)
            ?? SettingsController::SIDEBAR_COLOR_DEFAULT;

        $organization->load('department');
        $department = $organization->department;
        
        $stored = $organization->sections ?? [];
        
        $gallery = isset($stored['gallery']['items']) && is_array($stored['gallery']['items']) ? $stored['gallery']['items'] : [];
        if (!isset($gallery[$index])) {
            throw new NotFoundHttpException('Album not found.');
        }
        
        $album = $gallery[$index];

        return view('organization-album', [
            'organization'    => $organization,
            'department'      => $department,
            'collegeName'     => $collegeName,
            'collegeSlug'     => $college,
            'collegeShortName' => $collegeShortName,
            'collegeLogoUrl'  => $collegeLogoUrl,
            'headerColor'     => $headerColor,
            'accentColor'     => $accentColor,
            'album'           => $album,
            'albumIndex'      => $index,
            'collegeContact'  => \App\Models\CollegeContact::where('college_slug', $college)->first(),
        ]);
    }

    public function testimonials(Request $request, string $college): View
    {
        $colleges = CollegeController::getColleges();
        if (! isset($colleges[$college])) {
            throw new NotFoundHttpException('College not found.');
        }

        $collegeName = $colleges[$college];
        $collegeShortName = self::SHORT_NAMES[$college] ?? $collegeName;

        $collegeLogoUrl = $this->resolveCollegeLogoUrl($college);

        $headerColor = Setting::get('admin_header_color_' . $college, null)
            ?? Setting::get('admin_header_color_' . $college . '_editor', null)
            ?? SettingsController::HEADER_COLOR_DEFAULT;
        $accentColor = Setting::get('admin_sidebar_color_' . $college, null)
            ?? Setting::get('admin_sidebar_color_' . $college . '_editor', null)
            ?? SettingsController::SIDEBAR_COLOR_DEFAULT;

        $testimonials = \App\Models\DepartmentAlumnus::where(function ($query) use ($college) {
                $query->where(function ($directQuery) use ($college) {
                    $directQuery->where('college_slug', $college)
                        ->whereNull('department_id')
                        ->whereNull('institute_id');
                })->orWhereHas('department', function ($departmentQuery) use ($college) {
                    $departmentQuery->where('college_slug', $college)
                        ->where('alumni_is_visible', true);
                });
            })
            ->with('department')
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('college-testimonials', [
            'collegeName' => $collegeName,
            'collegeSlug' => $college,
            'collegeShortName' => $collegeShortName,
            'collegeLogoUrl' => $collegeLogoUrl,
            'headerColor' => $headerColor,
            'accentColor' => $accentColor,
            'testimonials' => $testimonials,
            'collegeContact' => \App\Models\CollegeContact::where('college_slug', $college)->first(),
            'collegeEmail' => Setting::get('admin_email_' . $college, $college . '@clsu.edu.ph'),
        ]);
    }

    public function accreditation(Request $request, string $college): View
    {
        $colleges = CollegeController::getColleges();
        if (! isset($colleges[$college])) {
            throw new NotFoundHttpException('College not found.');
        }

        $collegeName = $colleges[$college];
        $collegeShortName = self::SHORT_NAMES[$college] ?? $collegeName;

        $collegeLogoUrl = $this->resolveCollegeLogoUrl($college);

        $headerColor = Setting::get('admin_header_color_' . $college, null)
            ?? Setting::get('admin_header_color_' . $college . '_editor', null)
            ?? SettingsController::HEADER_COLOR_DEFAULT;
        $accentColor = Setting::get('admin_sidebar_color_' . $college, null)
            ?? Setting::get('admin_sidebar_color_' . $college . '_editor', null)
            ?? SettingsController::SIDEBAR_COLOR_DEFAULT;

        $accreditations = \App\Models\CollegeAccreditation::where('college_slug', $college)
            ->where('is_visible', true)
            ->with('program')
            ->orderBy('sort_order')
            ->get();

        $accreditationSection = \App\Models\CollegeSection::published()
            ->where('college_slug', $college)
            ->where('section_slug', 'accreditation')
            ->first();

        $membershipSection = \App\Models\CollegeSection::published()
            ->where('college_slug', $college)
            ->where('section_slug', 'membership')
            ->first();

        $memberships = (!$membershipSection || $membershipSection->is_visible)
            ? \App\Models\CollegeMembership::where('college_slug', $college)
                ->where('is_visible', true)
                ->with('department')
                ->orderBy('sort_order')
                ->get()
            : collect();
        
        $heroTitle = $accreditationSection?->title ?: 'Commitment to Excellence';
        $heroDescription = $accreditationSection?->body ?: 'Our programs are recognized by national and international accrediting bodies, ensuring the highest standards of education.';
        if ($accreditationSection && !empty($accreditationSection->meta)) {
            $heroTitle = $accreditationSection->meta['hero_title'] ?? $heroTitle;
        }

        return view('college-accreditation', [
            'collegeName' => $collegeName,
            'collegeSlug' => $college,
            'collegeShortName' => $collegeShortName,
            'collegeLogoUrl' => $collegeLogoUrl,
            'headerColor' => $headerColor,
            'accentColor' => $accentColor,
            'accreditations' => $accreditations,
            'memberships' => $memberships,
            'accreditationSection' => $accreditationSection,
            'membershipSection' => $membershipSection,
            'heroTitle' => $heroTitle,
            'heroDescription' => $heroDescription,
            'collegeContact' => \App\Models\CollegeContact::where('college_slug', $college)->first(),
            'collegeEmail' => Setting::get('admin_email_' . $college, $college . '@clsu.edu.ph'),
        ]);
    }

    public function downloads(Request $request, string $college): View
    {
        $colleges = CollegeController::getColleges();
        if (! isset($colleges[$college])) {
            throw new NotFoundHttpException('College not found.');
        }

        $collegeName = $colleges[$college];
        $collegeShortName = self::SHORT_NAMES[$college] ?? $collegeName;

        $collegeLogoUrl = $this->resolveCollegeLogoUrl($college);

        $headerColor = Setting::get('admin_header_color_' . $college, null)
            ?? Setting::get('admin_header_color_' . $college . '_editor', null)
            ?? SettingsController::HEADER_COLOR_DEFAULT;
        $accentColor = Setting::get('admin_sidebar_color_' . $college, null)
            ?? Setting::get('admin_sidebar_color_' . $college . '_editor', null)
            ?? SettingsController::SIDEBAR_COLOR_DEFAULT;

        $downloadsSection = \App\Models\CollegeSection::published()
            ->where('college_slug', $college)
            ->where('section_slug', 'downloads')
            ->first();

        $downloads = CollegeDownload::published()
            ->where('college_slug', $college)
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('college-downloads', [
            'collegeName' => $collegeName,
            'collegeSlug' => $college,
            'collegeShortName' => $collegeShortName,
            'collegeLogoUrl' => $collegeLogoUrl,
            'headerColor' => $headerColor,
            'accentColor' => $accentColor,
            'downloads' => $downloads,
            'sectionTitle' => $downloadsSection?->title ?? 'Downloads',
            'sectionDescription' => $downloadsSection?->body ?? '',
            'collegeContact' => \App\Models\CollegeContact::where('college_slug', $college)->first(),
            'collegeEmail' => Setting::get('admin_email_' . $college, $college . '@clsu.edu.ph'),
        ]);
    }

    public function downloadFile(Request $request, string $college, CollegeDownload $download): Response
    {
        $colleges = CollegeController::getColleges();
        if (! isset($colleges[$college])) {
            throw new NotFoundHttpException('College not found.');
        }

        if ($download->college_slug !== $college || ! $download->is_visible || $download->is_draft || ($download->publish_at && $download->publish_at->isFuture())) {
            throw new NotFoundHttpException('File not found.');
        }

        $driveService = app(\App\Services\GoogleDriveService::class);
        $fileId = $driveService->getFileId($download->file_path);
        if (! $fileId) {
            throw new NotFoundHttpException('File not found.');
        }

        $contents = $driveService->streamFileById($fileId);
        if ($contents === null) {
            throw new NotFoundHttpException('File not found.');
        }

        $filename = $download->file_name ?: basename($download->file_path);
        $mimeType = $download->mime_type ?: $driveService->getMimeTypeById($fileId);

        return response($contents, 200, [
            'Content-Type' => $mimeType ?: 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . addslashes($filename) . '"',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }
}
