<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\CollegeController;
use App\Http\Controllers\Admin\SettingsController;
use App\Models\Announcement;
use App\Models\CollegeContact;
use App\Models\CollegeDepartment;
use App\Models\Setting;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AnnouncementPageController extends Controller
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

    public function show(string $college, string $slug): \Illuminate\Http\RedirectResponse|\Illuminate\View\View
    {
        // Fetch the announcement by slug
        $announcement = Announcement::where('slug', $slug)
            ->first();

        // Redirect back if not found OR does not match college
        if (!$announcement || ($announcement->college_slug && $announcement->college_slug !== $college)) {
             return redirect()->route('news.announcement.board', ['college' => $college]);
        }

        $colleges = CollegeController::getColleges();
        
        if (!isset($colleges[$college])) {
            throw new NotFoundHttpException('College not found.');
        }

        $collegeName = $colleges[$college];
        $collegeShortName = self::SHORT_NAMES[$college] ?? $collegeName;

        // Get college logo
        $logoPath = Setting::get('admin_logo_path_' . $college, null);
        $collegeLogoUrl = $logoPath ? asset($logoPath) : null;
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
            ?? SettingsController::HEADER_COLOR_DEFAULT;
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

        // Get related announcements (same college or all colleges, excluding current)
        $relatedAnnouncements = Announcement::whereIn('college_slug', [$college, null])
            ->where('id', '!=', $announcement->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Get recent announcements (same college or all colleges, excluding current)
        $recentAnnouncements = Announcement::whereIn('college_slug', [$college, null])
            ->where('id', '!=', $announcement->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Convert announcement to article-like structure for the view
        $article = (object)[
            'title' => $announcement->title,
            'slug' => $announcement->slug,
            'body' => $announcement->body,
            'published_at' => $announcement->published_at ?? $announcement->created_at,
            'type' => 'announcement',
            'banner' => null, // Announcements don't have banners
            'category' => 'Announcement',
            'author' => $announcement->author,
        ];

        // Convert related/recent to article-like structure
        $relatedArticles = $relatedAnnouncements->map(function($a) {
            return (object)[
                'title' => $a->title,
                'slug' => $a->slug,
                'published_at' => $a->published_at ?? $a->created_at,
            ];
        });

        $recentArticles = $recentAnnouncements->map(function($a) {
            return (object)[
                'title' => $a->title,
                'slug' => $a->slug,
                'published_at' => $a->published_at ?? $a->created_at,
            ];
        });

        // Departments for dropdown
        $departments = CollegeDepartment::where('college_slug', $college)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('news-announcement-detail', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'recentArticles' => $recentArticles,
            'collegeName' => $collegeName,
            'departments' => $departments,
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
        ]);
    }
}
