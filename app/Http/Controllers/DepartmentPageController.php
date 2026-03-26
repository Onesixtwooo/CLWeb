<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\CollegeController;
use App\Http\Controllers\Admin\SettingsController;
use App\Models\CollegeSection;
use App\Models\CollegeDepartment;
use App\Models\CollegeRetro;
use App\Models\Faculty;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DepartmentPageController extends Controller
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

    public function show(Request $request, string $college, string $department): View|RedirectResponse
    {
        $colleges = CollegeController::getColleges();
        if (! isset($colleges[$college])) {
            throw new NotFoundHttpException('College not found.');
        }

        $collegeName = $colleges[$college];
        $collegeShortName = self::SHORT_NAMES[$college] ?? $collegeName;

        // Get the department
        $departmentModel = CollegeDepartment::findByCollegeAndRouteKey($college, $department);

        if (!$departmentModel) {
            throw new NotFoundHttpException('Department not found.');
        }

        if ($department !== $departmentModel->getRouteKey()) {
            return redirect()->route('college.department.show', [
                'college' => $college,
                'department' => $departmentModel,
            ], 301);
        }

        $departmentsSection = CollegeSection::where('college_slug', $college)
            ->where('section_slug', 'departments')
            ->first();

        if ($departmentsSection && (! $departmentsSection->is_visible || ! $departmentsSection->isPublished())) {
            return redirect()->route('college.show', ['college' => $college]);
        }

        // Get appearance settings
        $logoPath = Setting::get('admin_logo_path_' . $college, null);
        $collegeLogoUrl = \App\Providers\AppServiceProvider::resolveLogoUrl($logoPath);
        if (! $collegeLogoUrl) {
            if (file_exists(public_path('images/colleges/' . $college . '.webp'))) {
                $collegeLogoUrl = asset('images/colleges/' . $college . '.webp');
            } elseif (file_exists(public_path('images/logos/' . $college . '.jpg'))) {
                $collegeLogoUrl = asset('images/logos/' . $college . '.jpg');
            } else {
                // Fallback to global setting before hardcoded main.webp
                $globalLogoPath = Setting::get('admin_logo_path', null);
                if ($globalLogoPath) {
                    $collegeLogoUrl = \App\Providers\AppServiceProvider::resolveLogoUrl($globalLogoPath);
                } else {
                    $collegeLogoUrl = asset('images/colleges/main.webp');
                }
            }
        }

        // Appearance colors
        $headerColor = Setting::get('admin_header_color_' . $college, null)
            ?? Setting::get('admin_header_color_' . $college . '_editor', null)
            ?? SettingsController::HEADER_COLOR_DEFAULT;
        $accentColor = Setting::get('admin_sidebar_color_' . $college, null)
            ?? Setting::get('admin_sidebar_color_' . $college . '_editor', null)
            ?? SettingsController::SIDEBAR_COLOR_DEFAULT;
        $topHeaderColor = Setting::get('admin_sidebar_color', SettingsController::SIDEBAR_COLOR_DEFAULT);

        // College email
        $collegeEmail = Setting::get('admin_email_' . $college, $college . '@clsu.edu.ph');


        // Awards Pagination
        $awardsItems = $departmentModel->awards;
        $sortedAwards = $awardsItems->sortByDesc('created_at')->values();

        $page = $request->input('page', 1);
        $perPage = 6;
        
        $paginatedAwards = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedAwards->forPage($page, $perPage),
            $sortedAwards->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'fragment' => 'awards']
        );

        // Research Pagination
        $researchItems = $departmentModel->research;
        $sortedResearch = $researchItems->sortByDesc('created_at')->values();

        $researchPage = $request->input('research_page', 1);
        
        $paginatedResearch = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedResearch->forPage($researchPage, $perPage),
            $sortedResearch->count(),
            $perPage,
            $researchPage,
            ['path' => $request->url(), 'pageName' => 'research_page', 'fragment' => 'research']
        );

        // Fetch Extension
        $extensionItems = $departmentModel->extension;
        $sortedExtension = $extensionItems->sortByDesc('created_at')->values();

        $extensionPage = $request->input('extension_page', 1);
        $paginatedExtension = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedExtension->forPage($extensionPage, $perPage),
            $sortedExtension->count(),
            $perPage,
            $extensionPage,
            ['path' => $request->url(), 'pageName' => 'extension_page', 'fragment' => 'extension']
        );

        // Fetch Training
        $trainingItems = $departmentModel->training;
        $sortedTraining = $trainingItems->sortByDesc('created_at')->values();

        $trainingPage = $request->input('training_page', 1);
        $paginatedTraining = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedTraining->forPage($trainingPage, $perPage),
            $sortedTraining->count(),
            $perPage,
            $trainingPage,
            ['path' => $request->url(), 'pageName' => 'training_page', 'fragment' => 'training']
        );

        // Fetch Facilities
        $facilitiesItems = $departmentModel->facilities;
        $sortedFacilities = $facilitiesItems->sortByDesc('created_at')->values();

        $facilitiesPage = $request->input('facilities_page', 1);
        $paginatedFacilities = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedFacilities->forPage($facilitiesPage, $perPage),
            $sortedFacilities->count(),
            $perPage,
            $facilitiesPage,
            ['path' => $request->url(), 'pageName' => 'facilities_page', 'fragment' => 'facilities']
        );

        // Fetch Alumni
        $alumniItems = $departmentModel->alumni;
        $sortedAlumni = $alumniItems->sortByDesc('created_at')->values();

        $alumniPage = $request->input('alumni_page', 1);
        $paginatedAlumni = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedAlumni->forPage($alumniPage, $perPage),
            $sortedAlumni->count(),
            $perPage,
            $alumniPage,
            ['path' => $request->url(), 'pageName' => 'alumni_page', 'fragment' => 'alumni']
        );

        // Get faculty for this department
        $faculty = Faculty::where('college_slug', $college)
            ->where('department', $departmentModel->name)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $facultySection = $departmentModel->getSection('faculty') ?? [];
        $facultySectionTitle = $facultySection['title'] ?? 'Department Faculty';
        $facultySectionDescription = $facultySection['body'] ?? 'Meet our dedicated team of educators and researchers.';
        $facultySectionVisible = (bool) ($facultySection['is_visible'] ?? true);
        $membershipSection = $departmentModel->getSection('membership') ?? [];
        $membershipSectionTitle = $membershipSection['title'] ?? 'Affiliations & Memberships';
        $membershipSectionDescription = $membershipSection['body'] ?? 'Our department\'s active involvement in professional organizations.';
        $membershipSectionVisible = (bool) ($membershipSection['is_visible'] ?? true);
        $organizationsSection = $departmentModel->getSection('organizations') ?? [];
        $organizationsSectionTitle = $organizationsSection['title'] ?? 'Student Organizations';
        $organizationsSectionDescription = $organizationsSection['body'] ?? ('Get involved with ' . $departmentModel->name . ' student organizations.');
        $organizationsSectionVisible = (bool) ($organizationsSection['is_visible'] ?? true);

        return view('department', [
            'collegeName' => $collegeName,
            'collegeSlug' => $college,
            'collegeShortName' => $collegeShortName,
            'collegeLogoUrl' => $collegeLogoUrl,
            'headerColor' => $headerColor,
            'accentColor' => $accentColor,
            'topHeaderColor' => $topHeaderColor,
            'collegeEmail' => $collegeEmail,
            'department' => $departmentModel,
            'awards' => $paginatedAwards,
            'research' => $paginatedResearch,
            'extension' => $paginatedExtension,
            'training' => $paginatedTraining,
            'facilities' => $paginatedFacilities,
            'alumni' => $paginatedAlumni,
            'faculty' => $faculty,
            'facultySectionTitle' => $facultySectionTitle,
            'facultySectionDescription' => $facultySectionDescription,
            'facultySectionVisible' => $facultySectionVisible,
            'membershipSectionTitle' => $membershipSectionTitle,
            'membershipSectionDescription' => $membershipSectionDescription,
            'membershipSectionVisible' => $membershipSectionVisible,
            'organizationsSectionTitle' => $organizationsSectionTitle,
            'organizationsSectionDescription' => $organizationsSectionDescription,
            'organizationsSectionVisible' => $organizationsSectionVisible,
            'retroList' => CollegeRetro::where('college_slug', $college)
                ->where('department_id', $departmentModel->id)
                ->where('is_visible', true)
                ->orderBy('sort_order', 'asc')
                ->orderBy('created_at', 'asc')
                ->get(),
            'memberships' => \App\Models\CollegeMembership::where('department_id', $departmentModel->id)
                ->where('is_visible', true)
                ->orderBy('sort_order')
                ->get(),
            'organizations' => \App\Models\CollegeOrganization::where('department_id', $departmentModel->id)
                ->where('is_visible', true)
                ->orderBy('sort_order')
                ->get(),
            'collegeContact' => \App\Models\CollegeContact::where('college_slug', $college)->first(),
        ]);
    }
}
