<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\CollegeSection;
use Illuminate\View\View;
use Illuminate\Support\Facades\Vite;

class WelcomeController extends Controller
{
    public function index(): View
    {
        $colleges = College::orderBy('name')->get()->map(function ($college) {
            // Get Overview
            $overview = CollegeSection::where('college_slug', $college->slug)
                ->where('section_slug', 'overview')
                ->value('body');

            // Strip tags and limit length for preview
            $college->overview_preview = \Illuminate\Support\Str::limit(strip_tags($overview), 150);

            // Get Logo
            // 1. Check if set in database (admin_logo_path_{slug}) - skipped for now as per plan, relying on standard paths
            // 2. Check public/images/colleges/{slug}.webp
            // 3. Fallback to Vite asset
            
            // Get Logo — priority: Google Drive icon → local file → fallback
            $logoUrl = null;

            if (!empty($college->icon)) {
                if ($college->icon !== 'none') {
                    $logoUrl = \App\Providers\AppServiceProvider::resolveLogoUrl($college->icon);
                } else {
                    $logoUrl = null; // Explicit No Logo layout
                }
            } else {
                $globalLogoPath = \App\Models\Setting::get('admin_logo_path', 'images/colleges/main.webp');
                $logoUrl = \App\Providers\AppServiceProvider::resolveLogoUrl($globalLogoPath);
                
                if (!$logoUrl) {
                    try {
                        $logoUrl = Vite::asset('resources/images/logos/clsu-logo-green.png');
                    } catch (\Exception $e) {
                        $logoUrl = asset('images/logos/images.png');
                    }
                }
            }

            $college->logo_url = $logoUrl;

            // Get Contact Info
            $contact = \App\Models\CollegeContact::where('college_slug', $college->slug)->first();
            $college->contact_email = $contact->email ?? null;
            $college->contact_phone = $contact->phone ?? null;

            return $college;
        });

        return view('welcome', compact('colleges'));
    }
}
