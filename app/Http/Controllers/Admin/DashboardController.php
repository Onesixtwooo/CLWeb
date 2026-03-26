<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Article;
use App\Models\CollegeDepartment;
use App\Models\CollegeSection;
use App\Models\Facility;
use App\Models\Faculty;
use App\Models\CollegeInstitute;
use App\Models\CollegeFaq;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Initial counts (global)
        $articlesQuery = Article::query();
        $announcementsQuery = Announcement::query();
        $facultyQuery = Faculty::query();

        // Scope counts if bounded to a department
        if ($user && $user->isBoundedToDepartment()) {
            $slug = $user->college_slug;

            $articlesQuery->where('college_slug', $slug)
                ->whereHas('user', function ($q) use ($user) {
                    $q->where('college_slug', $user->college_slug)
                        ->where('department', $user->department);
                });

            $announcementsQuery->where(function($q) use ($slug) {
                $q->where('college_slug', $slug)->orWhereNull('college_slug');
            });

            $facultyQuery->where('college_slug', $slug)
                ->where('department', $user->department);
        } elseif ($user && $user->isBoundedToCollege()) {
            $slug = $user->college_slug;

            // Articles and Announcements include college-specific OR global (null)
            $articlesQuery->where(function($q) use ($slug) {
                $q->where('college_slug', $slug)->orWhereNull('college_slug');
            });
            $announcementsQuery->where(function($q) use ($slug) {
                $q->where('college_slug', $slug)->orWhereNull('college_slug');
            });

            // Faculty is college-specific only
            $facultyQuery->where('college_slug', $slug);
        }

        $articlesCount = $articlesQuery->count();
        $announcementsCount = $announcementsQuery->count();
        $facultyCount = $facultyQuery->count();

        // Recent articles (also scoped)
        $recentArticles = (clone $articlesQuery)->latest('published_at')->take(5)->get();

        // College content completeness (superadmin sees all; college admin sees theirs)
        $colleges = CollegeController::getColleges();
        $sections = CollegeController::getSections(); // overview, departments, institutes, etc.
        $sectionCount = count($sections);

        $collegeStats = [];

        // If user is bounded to a college, only show that college
        $collegesToCheck = $colleges;
        if ($user && $user->isBoundedToCollege()) {
            $slug = $user->college_slug;
            if (isset($colleges[$slug])) {
                $collegesToCheck = [$slug => $colleges[$slug]];
            }
        }

        foreach ($collegesToCheck as $slug => $name) {
            // Count sections that have content saved
            $filledSections = CollegeSection::where('college_slug', $slug)
                ->whereNotNull('body')
                ->where('body', '!=', '')
                ->pluck('section_slug')
                ->toArray();

            // Also count related data as "filled" indicators
            $hasDepartments = CollegeDepartment::where('college_slug', $slug)->exists();
            $hasFaculty = Faculty::where('college_slug', $slug)->exists();
            $hasFacilities = Facility::where('college_slug', $slug)->exists();
            $hasInstitutes = CollegeInstitute::where('college_slug', $slug)->exists();
            $hasFaqs = CollegeFaq::where('college_slug', $slug)->exists();

            // Build completeness: a section is "filled" if it has content OR related data
            $filled = 0;
            foreach ($sections as $sectionSlug => $sectionName) {
                $isFilled = in_array($sectionSlug, $filledSections);
                if (!$isFilled) {
                    // Check related data tables
                    $isFilled = match ($sectionSlug) {
                        'departments' => $hasDepartments,
                        'faculty' => $hasFaculty,
                        'facilities' => $hasFacilities,
                        'institutes' => $hasInstitutes,
                        'faq' => $hasFaqs,
                        default => false,
                    };
                }
                if ($isFilled) $filled++;
            }

            $deptCount = CollegeDepartment::where('college_slug', $slug)->count();
            $facCount = Faculty::where('college_slug', $slug)
                ->when($user && $user->isBoundedToDepartment() && $user->college_slug === $slug, function ($q) use ($user) {
                    $q->where('department', $user->department);
                })
                ->count();

            $collegeStats[] = [
                'slug' => $slug,
                'name' => $name,
                'filled' => $filled,
                'total' => $sectionCount,
                'percent' => $sectionCount > 0 ? round(($filled / $sectionCount) * 100) : 0,
                'departments' => $deptCount,
                'faculty' => $facCount,
                'faculty_label' => $user && $user->isBoundedToDepartment() && $user->college_slug === $slug
                    ? $user->department
                    : null,
            ];
        }

        // Sort by completeness (least complete first, to surface gaps)
        usort($collegeStats, fn($a, $b) => $a['percent'] - $b['percent']);

        return view('admin.dashboard', [
            'articlesCount' => $articlesCount,
            'announcementsCount' => $announcementsCount,
            'facultyCount' => $facultyCount,
            'recentArticles' => $recentArticles,
            'collegeStats' => $collegeStats,
            'totalColleges' => count($collegesToCheck),
        ]);
    }
}
