<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\CollegeController;
use App\Http\Controllers\Admin\SettingsController;
use App\Models\Announcement;
use App\Models\Article;
use App\Models\CollegeContact;
use App\Models\CollegeDepartment;
use App\Models\Setting;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NewsBoardController extends Controller
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

    public function index(string $college): View
    {
        $colleges = CollegeController::getColleges();
        if (!isset($colleges[$college])) {
            throw new NotFoundHttpException('College not found.');
        }

        $collegeName = $colleges[$college];
        $collegeShortName = self::SHORT_NAMES[$college] ?? $collegeName;

        // Get college logo
        $logoPath = Setting::get('admin_logo_path_' . $college, null);
        $collegeLogoUrl = \App\Providers\AppServiceProvider::resolveLogoUrl($logoPath);
        if (!$collegeLogoUrl) {
            if (file_exists(public_path('images/colleges/' . $college . '.webp'))) {
                $collegeLogoUrl = asset('images/colleges/' . $college . '.webp');
            } elseif (file_exists(public_path('images/logos/' . $college . '.jpg'))) {
                $collegeLogoUrl = asset('images/logos/' . $college . '.jpg');
            } else {
                // Fallback to global setting before hardcoded main.webp
                $globalLogoPath = Setting::get('admin_logo_path', null);
                if ($globalLogoPath) {
                    $collegeLogoUrl = asset($globalLogoPath);
                } else {
                    $collegeLogoUrl = asset('images/colleges/main.webp');
                }
            }
        }

        // Appearance colors
        $headerColor = Setting::get('admin_header_color_' . $college, null)
            ?? Setting::get('admin_header_color_' . $college . '_editor', null)
            ?? '#009639';
        $accentColor = Setting::get('admin_sidebar_color_' . $college, null)
            ?? Setting::get('admin_sidebar_color_' . $college . '_editor', null)
            ?? SettingsController::SIDEBAR_COLOR_DEFAULT;

        // College contact info
        $collegeContact = CollegeContact::where('college_slug', $college)->first();
        $collegeEmail = $collegeContact?->email ?? $college . '@clsu.edu.ph';
        $collegePhone = $collegeContact?->phone ?? '(044) 940 8785';

        // President's Contact Info (Global)
        $presidentEmail = Setting::get('admin_president_email', 'op@clsu.edu.ph');
        $presidentPhone = Setting::get('admin_president_phone', '(044) 940 8785');

        // Departments for dropdown
        $departments = CollegeDepartment::where('college_slug', $college)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Fetch news from articles table (type='news')
        // Include both college-specific and all-college (null) items
        $newsArticles = Article::where('type', 'news')
            ->where(function ($query) use ($college) {
                $query->where('college_slug', $college)
                      ->orWhereNull('college_slug');
            })
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->get();
        
        // Fetch announcements from Article table (type='announcement')
        $announcementsFromArticles = Article::where('type', 'announcement')
            ->where(function ($query) use ($college) {
                $query->where('college_slug', $college)
                      ->orWhereNull('college_slug');
            })
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->get()
            ->map(fn($a) => (object)[
                'title'        => $a->title,
                'slug'         => $a->slug,
                'body'         => $a->body,
                'banner'       => $a->banner ?? null,
                'published_at' => $a->published_at,
                'route_name'   => 'news.announcement.detail', // uses ArticlePageController
            ]);

        // Fetch announcements from the dedicated Announcement model
        // college_slug = NULL means it is a global announcement for all colleges
        $announcementsFromModel = \App\Models\Announcement::where(function ($q) use ($college) {
                $q->where('college_slug', $college)
                  ->orWhereNull('college_slug');
            })
            ->orderBy('published_at', 'desc')
            ->get()
            ->map(fn($a) => (object)[
                'title'        => $a->title,
                'slug'         => $a->slug,
                'body'         => $a->body,
                'banner'       => $a->image, // Announcement model now has an image field
                'published_at' => $a->published_at ?? $a->created_at,
                'route_name'   => 'announcement.detail', // uses AnnouncementPageController
            ]);

        // Merge and sort by published_at descending
        // Use collect() on both sides to get plain Collections (not Eloquent Collections)
        // so merge() doesn't call getKey() expecting Eloquent models
        $announcements = collect($announcementsFromArticles)
            ->merge(collect($announcementsFromModel))
            ->sortByDesc('published_at')
            ->values();

        return view('news-announcement-board', [
            'newsArticles' => $newsArticles,
            'announcements' => $announcements,
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
            'departments' => $departments,
        ]);
    }
}
