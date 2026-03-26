<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\CollegeController;
use App\Http\Controllers\Admin\SettingsController;
use App\Models\Facility;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FacilityController extends Controller
{
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

    public function show(Request $request, string $college, Facility $facility): View
    {
        $colleges = CollegeController::getColleges();
        if (! isset($colleges[$college])) {
            throw new NotFoundHttpException('College not found.');
        }

        // Verify facility belongs to this college
        if ($facility->college_slug !== $college) {
             throw new NotFoundHttpException('Facility not found in this college.');
        }

        $facility->load('images');

        // Fetch other facilities for the "gallery highlights" / "see also" section
        $otherFacilities = Facility::where('college_slug', $college)
            ->where('id', '!=', $facility->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $collegeName = $colleges[$college];
        $collegeShortName = self::SHORT_NAMES[$college] ?? $collegeName;

        // Logo Logic (Shared with CollegePageController)
        $logoPath = Setting::get('admin_logo_path_' . $college, null);
        $collegeLogoUrl = $logoPath ? asset($logoPath) : null;
        if (! $collegeLogoUrl) {
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
        $collegeContact = \App\Models\CollegeContact::where('college_slug', $college)->first();
        $collegeEmail = $collegeContact->email ?? $college . '@clsu.edu.ph';
        $collegePhone = $collegeContact->phone ?? '(044) 940 8785';

        return view('facility', [
            'college' => $college,
            'collegeName' => $collegeName,
            'collegeShortName' => $collegeShortName,
            'collegeLogoUrl' => $collegeLogoUrl,
            'headerColor' => $headerColor,
            'accentColor' => $accentColor,
            'collegeContact' => $collegeContact,
            'collegeEmail' => $collegeEmail,
            'collegePhone' => $collegePhone,
            'facility' => $facility,
            'otherFacilities' => $otherFacilities,
        ]);
    }
}
