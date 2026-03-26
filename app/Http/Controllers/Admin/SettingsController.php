<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacebookConfig;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public const HEADER_COLOR_DEFAULT = '#0d6e42';

    public const SIDEBAR_COLOR_DEFAULT = '#009639';

    /** Default colors for editor role (blue/dark theme). */
    public const EDITOR_HEADER_COLOR_DEFAULT = '#1e3a5f';

    public const EDITOR_SIDEBAR_COLOR_DEFAULT = '#0f172a';

    public function index(Request $request): View
    {
        $allColleges = CollegeController::getColleges();
        $user = $request->user();
        if ($user && $user->isBoundedToCollege()) {
            $slug = $user->college_slug;
            if (empty($slug) || ! isset($allColleges[$slug])) {
                $colleges = [];
            } else {
                $colleges = [$slug => $allColleges[$slug]];
            }
        } else {
            $colleges = $allColleges;
        }

        $bounded = $user && $user->isBoundedToCollege() && ! empty($user->college_slug);
        $collegeSlug = $bounded ? $user->college_slug : null;

        // All college-scoped users (admin, editor) use the same college appearance colors
        if ($bounded) {
            $headerColor  = Setting::get('admin_header_color_' . $collegeSlug,  self::HEADER_COLOR_DEFAULT);
            $sidebarColor = Setting::get('admin_sidebar_color_' . $collegeSlug, self::SIDEBAR_COLOR_DEFAULT);
            $adminLogoPath = Setting::get('admin_logo_path_' . $collegeSlug, null);
            $collegeEmail = Setting::get('admin_email_' . $collegeSlug, $collegeSlug . '@clsu.edu.ph');
            $appearanceScope = 'college';
            $appearanceCollegeName = $allColleges[$collegeSlug] ?? $collegeSlug;
        } else {
            $headerColor  = Setting::get('admin_header_color',  self::HEADER_COLOR_DEFAULT);
            $sidebarColor = Setting::get('admin_sidebar_color', self::SIDEBAR_COLOR_DEFAULT);
            $adminLogoPath = Setting::get('admin_logo_path', null);
            $adminDefaultHeroPath = Setting::get('admin_default_hero', null);
            $collegeEmail = null;
            $presidentEmail = Setting::get('admin_president_email', 'op@clsu.edu.ph');
            $presidentPhone = Setting::get('admin_president_phone', '(044) 940 8785');
            $appearanceScope = 'global';
            $appearanceCollegeName = null;

            // Google Drive Settings (only for superadmin)
            $googleDriveFolderId = Setting::get('google_drive_folder_id', config('filesystems.disks.google.folder_id'));
            $googleDriveClientId = Setting::get('google_drive_client_id', config('filesystems.disks.google.client_id'));
            $googleDriveClientSecret = Setting::get('google_drive_client_secret', config('filesystems.disks.google.client_secret'));
            $googleDriveRefreshToken = Setting::get('google_drive_refresh_token', config('filesystems.disks.google.refresh_token'));
        }


        $facebookAppId = Setting::get('facebook_app_id', config('services.facebook.app_id'));
        $facebookAppSecret = Setting::get('facebook_app_secret', config('services.facebook.app_secret'));
        $facebookAccessToken = Setting::get('facebook_access_token', config('services.facebook.access_token'));
        $facebookPageId = Setting::get('facebook_page_id', config('services.facebook.page_id'));
        $facebookIntegrationEnabled = Setting::get('facebook_integration_enabled' . ($collegeSlug ? '_' . $collegeSlug : ''), '1');

        return view('admin.settings.index', [
            'colleges' => $colleges,
            'headerColor' => $headerColor,
            'sidebarColor' => $sidebarColor,
            'adminLogoPath' => $adminLogoPath,
            'adminDefaultHeroPath' => $adminDefaultHeroPath ?? null,
            'collegeEmail' => $collegeEmail ?? null,
            'presidentEmail' => $presidentEmail ?? null,
            'presidentPhone' => $presidentPhone ?? null,
            'appearanceScope' => $appearanceScope,
            'appearanceCollegeName' => $appearanceCollegeName,
            'collegeSlug' => $collegeSlug,
            // Google Drive
            'googleDriveFolderId' => $googleDriveFolderId ?? null,
            'googleDriveClientId' => $googleDriveClientId ?? null,
            'googleDriveClientSecret' => $googleDriveClientSecret ?? null,
            'googleDriveRefreshToken' => $googleDriveRefreshToken ?? null,
            // Facebook
            'facebookConfigs' => FacebookConfig::all(),
            'facebookAppId' => $facebookAppId,
            'facebookAppSecret' => $facebookAppSecret,
            'facebookAccessToken' => $facebookAccessToken,
            'facebookPageId' => $facebookPageId,
            'facebookIntegrationEnabled' => $facebookIntegrationEnabled == '1',
        ]);
    }

    public function updateAppearance(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'admin_header_color'        => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'admin_sidebar_color'        => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'admin_logo'                 => ['nullable', 'image', 'max:2048'],
            'admin_default_hero'         => ['nullable', 'image', 'max:2048'],
            'remove_admin_logo'          => ['nullable', 'string'],
            'remove_admin_default_hero'  => ['nullable', 'string'],
        ]);

        $user = $request->user();
        $bounded = $user && $user->isBoundedToCollege() && ! empty($user->college_slug);
        $collegeSlug = $bounded ? $user->college_slug : null;
        $isEditor = $user && $user->role === \App\Models\User::ROLE_EDITOR;

        // Save to DB per role and department
        $collegesDir = public_path('images/colleges');
        if (! is_dir($collegesDir)) {
            mkdir($collegesDir, 0755, true);
        }
        $settingsDir = public_path('images/settings');
        if (! is_dir($settingsDir)) {
            mkdir($settingsDir, 0755, true);
        }

        if ($bounded) {
            // All college-scoped users write to the shared college color keys
            Setting::set('admin_header_color_' . $collegeSlug,  $data['admin_header_color']);
            Setting::set('admin_sidebar_color_' . $collegeSlug, $data['admin_sidebar_color']);
            if ($request->hasFile('admin_logo')) {
                $oldLogoPath = Setting::get('admin_logo_path_' . $collegeSlug, null);
                if ($oldLogoPath && !str_starts_with($oldLogoPath, 'http') && file_exists(public_path($oldLogoPath))) {
                    try {
                        unlink(public_path($oldLogoPath));
                    } catch (\Exception $e) {}
                }
                $file = $request->file('admin_logo');
                $ext  = $file->getClientOriginalExtension() ?: 'png';
                $name = $collegeSlug . '-' . Str::random(8) . '.' . $ext;
                $path = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs('colleges/logos', $file, $name);
                if ($path) {
                    Setting::set('admin_logo_path_' . $collegeSlug, \Illuminate\Support\Facades\Storage::disk('google')->url($path));
                }
            } elseif ($request->input('remove_admin_logo') === '1') {
                Setting::set('admin_logo_path_' . $collegeSlug, null);
            }
        } else {
            Setting::set('admin_header_color', $data['admin_header_color']);
            Setting::set('admin_sidebar_color', $data['admin_sidebar_color']);
            if ($request->hasFile('admin_logo')) {
                $oldLogoPath = Setting::get('admin_logo_path', null);
                if ($oldLogoPath && !str_starts_with($oldLogoPath, 'http') && file_exists(public_path($oldLogoPath))) {
                    try {
                        unlink(public_path($oldLogoPath));
                    } catch (\Exception $e) {}
                }
                $file = $request->file('admin_logo');
                $ext  = $file->getClientOriginalExtension() ?: 'png';
                $name = 'main-' . Str::random(8) . '.' . $ext;
                // Save locally for superadmin
                $path = $file->move(public_path('images/settings'), $name);
                if ($path) {
                    Setting::set('admin_logo_path', 'images/settings/' . $name);
                }
            } elseif ($request->input('remove_admin_logo') === '1') {
                Setting::set('admin_logo_path', null);
            }
            if ($request->hasFile('admin_default_hero')) {
                $oldHeroPath = Setting::get('admin_default_hero', null);
                if ($oldHeroPath && !str_starts_with($oldHeroPath, 'http') && file_exists(public_path($oldHeroPath))) {
                    try {
                        unlink(public_path($oldHeroPath));
                    } catch (\Exception $e) {}
                }
                $file = $request->file('admin_default_hero');
                $ext  = $file->getClientOriginalExtension() ?: 'jpg';
                $name = 'default-hero-' . Str::random(8) . '.' . $ext;
                // Save locally for superadmin
                $path = $file->move(public_path('images/settings'), $name);
                if ($path) {
                    Setting::set('admin_default_hero', 'images/settings/' . $name);
                }
            } elseif ($request->input('remove_admin_default_hero') === '1') {
                $oldHeroPath = Setting::get('admin_default_hero', null);
                if ($oldHeroPath && !str_starts_with($oldHeroPath, 'http') && file_exists(public_path($oldHeroPath))) {
                    try {
                        unlink(public_path($oldHeroPath));
                    } catch (\Exception $e) {
                        // Ignore file deletion errors to ensure the database is cleared.
                    }
                }
                Setting::set('admin_default_hero', null);
            }
        }

        return redirect()->route('admin.settings.index')->with('success', 'Appearance settings saved.');
    }

    public function updateGoogleDrive(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user && $user->isBoundedToCollege()) {
            return redirect()->route('admin.settings.index')->with('error', 'Only superadmins can update Google Drive settings.');
        }

        $data = $request->validate([
            'google_drive_folder_id'     => ['required', 'string'],
            'google_drive_client_id'     => ['required', 'string'],
            'google_drive_client_secret' => ['required', 'string'],
            'google_drive_refresh_token' => ['nullable', 'string'],
        ]);

        Setting::set('google_drive_folder_id', $data['google_drive_folder_id']);
        Setting::set('google_drive_client_id', $data['google_drive_client_id']);
        Setting::set('google_drive_client_secret', $data['google_drive_client_secret']);
        
        if ($request->filled('google_drive_refresh_token')) {
            Setting::set('google_drive_refresh_token', $data['google_drive_refresh_token']);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Google Drive API settings saved.');
    }

    public function googleDriveAuth(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user && $user->isBoundedToCollege()) {
            return redirect()->route('admin.settings.index')->with('error', 'Only superadmins can authorize Google Drive.');
        }

        $clientId = Setting::get('google_drive_client_id', config('filesystems.disks.google.client_id'));
        $clientSecret = Setting::get('google_drive_client_secret', config('filesystems.disks.google.client_secret'));

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('admin.settings.index')->with('error', 'Please set Client ID and Client Secret first.');
        }

        $client = new \Google\Client();
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri(route('admin.settings.google-drive.callback'));
        $client->addScope(\Google\Service\Drive::DRIVE);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        return redirect()->away($client->createAuthUrl());
    }

    public function googleDriveCallback(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user && $user->isBoundedToCollege()) {
            return redirect()->route('admin.settings.index')->with('error', 'Unauthorized.');
        }

        if ($request->has('error')) {
            return redirect()->route('admin.settings.index')->with('error', 'Google Auth Error: ' . $request->get('error'));
        }

        if (!$request->has('code')) {
            return redirect()->route('admin.settings.index')->with('error', 'No authorization code received.');
        }

        $clientId = Setting::get('google_drive_client_id', config('filesystems.disks.google.client_id'));
        $clientSecret = Setting::get('google_drive_client_secret', config('filesystems.disks.google.client_secret'));

        $client = new \Google\Client();
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri(route('admin.settings.google-drive.callback'));

        try {
            $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));
            
            if (isset($token['error'])) {
                return redirect()->route('admin.settings.index')->with('error', 'Token Error: ' . ($token['error_description'] ?? $token['error']));
            }

            if (!isset($token['refresh_token'])) {
                 return redirect()->route('admin.settings.index')->with('warning', 'Google Drive authorized, but no refresh token was received. If you already authorized, try revoking access in Google Account settings first.');
            }

            Setting::set('google_drive_refresh_token', $token['refresh_token']);

            return redirect()->route('admin.settings.index')->with('success', 'Google Drive successfully authorized and refresh token saved.');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.index')->with('error', 'OAuth Exception: ' . $e->getMessage());
        }
    }

    public function updateEmail(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'admin_email' => ['required', 'email', 'max:255'],
        ]);

        $user = $request->user();
        $bounded = $user && $user->isBoundedToCollege() && ! empty($user->college_slug);
        $collegeSlug = $bounded ? $user->college_slug : null;

        if (!$bounded) {
            return redirect()->route('admin.settings.index')->with('error', 'Email can only be set for college-specific settings.');
        }

        Setting::set('admin_email_' . $collegeSlug, $data['admin_email']);

        return redirect()->route('admin.settings.index')->with('success', 'College email saved.');
    }

    public function updatePresidentContact(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'admin_president_email' => ['required', 'email', 'max:255'],
            'admin_president_phone' => ['required', 'string', 'max:255'],
        ]);

        $user = $request->user();
        // Only superadmin (not bounded) can update this
        if ($user && $user->isBoundedToCollege()) {
            return redirect()->route('admin.settings.index')->with('error', 'Only superadmins can update President\'s contact info.');
        }

        Setting::set('admin_president_email', $data['admin_president_email']);
        Setting::set('admin_president_phone', $data['admin_president_phone']);

        return redirect()->route('admin.settings.index')->with('success', 'President\'s contact info saved.');
    }

    public function updateFacebook(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user && $user->isBoundedToCollege()) {
            return redirect()->route('admin.settings.index')->with('error', 'Only superadmins can update Facebook API settings.');
        }

        $data = $request->validate([
            'facebook_app_id'      => ['required', 'string'],
            'facebook_app_secret'  => ['required', 'string'],
            'facebook_access_token' => ['required', 'string'],
            'facebook_page_id'     => ['required', 'string'],
        ]);

        // Update .env file (in memory for current request)
        // In a production environment, consider using a configuration management approach
        Setting::set('facebook_app_id', $data['facebook_app_id']);
        Setting::set('facebook_app_secret', $data['facebook_app_secret']);
        Setting::set('facebook_access_token', $data['facebook_access_token']);
        Setting::set('facebook_page_id', $data['facebook_page_id']);

        // Also update services config array dynamically
        config([
            'services.facebook.app_id' => $data['facebook_app_id'],
            'services.facebook.app_secret' => $data['facebook_app_secret'],
            'services.facebook.access_token' => $data['facebook_access_token'],
            'services.facebook.page_id' => $data['facebook_page_id'],
        ]);

        return redirect()->route('admin.settings.index')->with('success', 'Facebook API settings saved successfully.');
    }

    public function updateCollegeFacebookIntegration(Request $request)
    {
        $user = $request->user();
        $collegeSlug = $user && method_exists($user, 'isBoundedToCollege') && $user->isBoundedToCollege() 
            ? $user->college_slug 
            : null;

        if ($collegeSlug) {
            $enabled = $request->has('facebook_integration_enabled') ? '1' : '0';
            Setting::set('facebook_integration_enabled_' . $collegeSlug, $enabled);
            return back()->with('success', 'Facebook integration settings updated successfully.');
        }

        return back()->with('error', 'Only college admins can update this setting here.');
    }
}
