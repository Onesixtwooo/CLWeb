<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->ensureCollegesLogoInPublic();

        View::composer('admin.layout', function ($view) {
            $headerColor  = '#0d6e42';
            $sidebarColor = '#0d2818';
            $adminLogoUrl = null;
            try {
                $user = Auth::user();
                if ($user && $user->isBoundedToCollege() && !empty($user->college_slug)) {
                    // All college-scoped users (admin, editor, dept-admin, org-admin)
                    // always use the college's appearance colors set by admin/superadmin.
                    $slug = $user->college_slug;
                    $headerColor  = Setting::get('admin_header_color_' . $slug,  $headerColor);
                    $sidebarColor = Setting::get('admin_sidebar_color_' . $slug, $sidebarColor);
                    $path = Setting::get('admin_logo_path_' . $slug, null);
                    $adminLogoUrl = self::resolveLogoUrl($path);
                } else {
                    // Superadmin: use global settings
                    $headerColor  = Setting::get('admin_header_color',  $headerColor);
                    $sidebarColor = Setting::get('admin_sidebar_color', $sidebarColor);
                    $path = Setting::get('admin_logo_path', null);
                    $adminLogoUrl = $path ? self::resolveLogoUrl($path) : asset('images/colleges/main.webp');
                }
            } catch (\Throwable) {
                // keep defaults
            }
            $view->with([
                'adminHeaderColor'  => $headerColor,
                'adminSidebarColor' => $sidebarColor,
                'adminLogoUrl'      => $adminLogoUrl,
            ]);
        });

        // Register CollegeFooterComposer
        View::composer('includes.college-footer', \App\View\Composers\CollegeFooterComposer::class);
    }

    /**
     * Resolves a logo path to a full URL.
     * If it's a Google Drive URL, it extracts the ID and use the proxy.
     * Otherwise, it uses asset().
     */
    public static function resolveLogoUrl(?string $path, string $localPrefix = ''): ?string
    {
        return self::resolveImageUrl($path, $localPrefix);
    }

    /**
     * Resolves an image path to a full URL.
     * If it's a full URL, return as is (handling GDrive proxying).
     * If it's a local path, prepends the prefix and uses asset().
     */
    public static function resolveImageUrl(?string $path, string $localPrefix = ''): ?string
    {
        if (empty($path)) return null;

        // Normalize some common prefixes that may be stored in the database but should not be output as-is.
        // Example: "/storage//media/proxy/xyz" should become "media/proxy/xyz" and then resolve to "/media/proxy/xyz".
        if (str_starts_with($path, '/storage/')) {
            $path = substr($path, 9); // strip leading "/storage/"
        }
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }

        // Trim any leading slashes so asset() doesn't emit "/storage//..." etc.
        $path = ltrim($path, '/');

        // 1. Extract GDrive ID and use proxy if it's a Drive URL or ALREADY a proxy URL
        if (str_contains($path, 'drive.google.com') || 
            str_contains($path, 'googleusercontent.com') || 
            str_contains($path, 'media/proxy/')) {
            if (preg_match('/[?&]id=([^&]+)/', $path, $matches) || 
                preg_match('/\/file\/d\/([^\/?&#]+)/', $path, $matches) ||
                preg_match('/media\/proxy\/([^\/?&#]+)/', $path, $matches)) {
                return '/media/proxy/' . $matches[1];
            }
        }

        // 2. If it's already a full URL, return as is
        if (str_starts_with($path, 'http')) return $path;

        // 3. Handing local vs GDrive internal paths
        $cleanPath = ltrim($path, '/');
        
        // Check if it exists in public folder
        if (File::exists(public_path($cleanPath))) {
            return asset($cleanPath);
        }

        // Check if it exists under the local prefix in public folder
        $prefixClean = trim($localPrefix, '/');
        if ($prefixClean && File::exists(public_path($prefixClean . '/' . $cleanPath))) {
            return asset($prefixClean . '/' . $cleanPath);
        }

        // If not found locally, check if it matches GDrive patterns (images/, logos/, storage/, or admin/)
        // and try to resolve it via the GDrive disk
        if (str_starts_with($cleanPath, 'images/') || 
            str_starts_with($cleanPath, 'logos/') || 
            str_starts_with($cleanPath, 'storage/') ||
            str_starts_with($cleanPath, 'admin/') ||
            str_starts_with($cleanPath, 'training/') ||
            str_starts_with($cleanPath, 'colleges/')) {
            try {
                // If it's storage/ or admin/, we should strip the prefix for the disk check
                $diskPath = $cleanPath;
                if (str_starts_with($cleanPath, 'storage/')) $diskPath = substr($cleanPath, 8);
                if (str_starts_with($cleanPath, 'admin/')) $diskPath = substr($cleanPath, 6);

                $gUrl = \Illuminate\Support\Facades\Storage::disk('google')->url($diskPath);
                if ($gUrl && !str_contains($gUrl, 'localhost') && !str_contains($gUrl, '127.0.0.1')) {
                    return self::resolveImageUrl($gUrl); // Recursive to handle proxying
                }
            } catch (\Throwable $e) {
                // ignore and fall back
            }
        }

        // 4. Fallback: original logic to prevent breaking other things
        $localPrefix = rtrim($localPrefix, '/');
        if (!empty($localPrefix) && str_starts_with($cleanPath, $localPrefix . '/')) {
            return asset($cleanPath);
        }

        $fullPath = $localPrefix ? $localPrefix . '/' . $cleanPath : $cleanPath;
        return asset($fullPath);
    }

    /**
     * Copy resources/images/colleges/main.webp to public/images/colleges/ if missing so superadmin logo works.
     */
    private function ensureCollegesLogoInPublic(): void
    {
        $publicDir = public_path('images/colleges');
        $publicFile = $publicDir . '/main.webp';
        $sourceFile = resource_path('images/colleges/main.webp');
        if (! File::isFile($publicFile) && File::isFile($sourceFile)) {
            if (! File::isDirectory($publicDir)) {
                File::makeDirectory($publicDir, 0755, true);
            }
            File::copy($sourceFile, $publicFile);
        }
    }
}
