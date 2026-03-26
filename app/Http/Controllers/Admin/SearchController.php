<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Article;
use App\Models\CollegeDepartment;
use App\Models\CollegeInstitute;
use App\Models\Faculty;
use App\Models\Facility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = trim($request->input('q', ''));
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $user = $request->user();
        $results = [];
        $limit = 5; // per category

        // 1. Articles
        $articles = Article::where('title', 'like', "%{$query}%")
            ->latest('published_at')
            ->take($limit)
            ->get();
        foreach ($articles as $item) {
            $results[] = [
                'type' => 'Article',
                'icon' => 'file-text',
                'title' => $item->title,
                'subtitle' => ucfirst($item->type) . ' · ' . $item->date_formatted,
                'url' => route('admin.articles.edit', $item),
            ];
        }

        // 2. Announcements
        $announcements = Announcement::where('title', 'like', "%{$query}%")
            ->latest()
            ->take($limit)
            ->get();
        foreach ($announcements as $item) {
            $results[] = [
                'type' => 'Announcement',
                'icon' => 'bell',
                'title' => $item->title,
                'subtitle' => $item->created_at?->format('M j, Y') ?? '',
                'url' => route('admin.announcements.edit', $item),
            ];
        }



        // 4. Faculty
        $faculty = Faculty::where('name', 'like', "%{$query}%")
            ->orWhere('position', 'like', "%{$query}%")
            ->take($limit)
            ->get();
        foreach ($faculty as $item) {
            $results[] = [
                'type' => 'Faculty',
                'icon' => 'user',
                'title' => $item->name,
                'subtitle' => $item->position ?? $item->college_slug ?? '',
                'url' => route('admin.faculty.edit', $item),
            ];
        }

        // 5. Departments
        $departments = CollegeDepartment::where('name', 'like', "%{$query}%")
            ->take($limit)
            ->get();
        foreach ($departments as $item) {
            $colleges = CollegeController::getColleges();
            $results[] = [
                'type' => 'Department',
                'icon' => 'layers',
                'title' => $item->name,
                'subtitle' => $colleges[$item->college_slug] ?? $item->college_slug,
                'url' => route('admin.colleges.show-department', ['college' => $item->college_slug, 'department' => $item, 'section' => 'overview']),
            ];
        }

        // 6. Colleges 
        $allColleges = CollegeController::getColleges();
        foreach ($allColleges as $slug => $name) {
            if (stripos($name, $query) !== false || stripos($slug, $query) !== false) {
                $results[] = [
                    'type' => 'College',
                    'icon' => 'building',
                    'title' => $name,
                    'subtitle' => '',
                    'url' => route('admin.colleges.show', ['college' => $slug]),
                ];
            }
        }

        // 7. Institutes
        $institutes = CollegeInstitute::where('name', 'like', "%{$query}%")
            ->take($limit)
            ->get();
        foreach ($institutes as $item) {
            $results[] = [
                'type' => 'Institute',
                'icon' => 'building',
                'title' => $item->name,
                'subtitle' => $allColleges[$item->college_slug] ?? $item->college_slug,
                'url' => route('admin.colleges.show-institute', ['college' => $item->college_slug, 'institute' => $item->id]),
            ];
        }

        // 8. Facilities
        $facilities = Facility::where('name', 'like', "%{$query}%")
            ->take($limit)
            ->get();
        foreach ($facilities as $item) {
            $results[] = [
                'type' => 'Facility',
                'icon' => 'home',
                'title' => $item->name,
                'subtitle' => $allColleges[$item->college_slug] ?? $item->college_slug ?? '',
                'url' => route('admin.facilities.edit', $item),
            ];
        }

        return response()->json([
            'results' => array_slice($results, 0, 20), // cap total results
        ]);
    }
}
