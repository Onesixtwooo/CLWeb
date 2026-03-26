<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoogleDriveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MediaController extends Controller
{
    /** Default Google Drive directories to scan. */
    private const GDRIVE_DIRS = [
        'images/news-board',
        'images/colleges',
        'images/logos',
        'images/faculty',
        'images/facilities',
        'images/announcements',
        'images/events',
        'images/uploads',
        'images/gallery',
        'logos/organizations',
        'logos',
        'colleges',
    ];

    /** Local subdirectories under public/images to scan. */
    private const LOCAL_BASE = 'images'; // relative to public_path()

    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'mp4', 'webm', 'pdf'];

    // -------------------------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------------------------

    private function humanFileSize(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        $i     = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), 1) . ' ' . $units[$i];
    }

    /**
     * Scan local public/images/* folders and return folder list + stats.
     */
    private function getLocalFolders(bool $isBounded, ?string $collegeSlug): array
    {
        $base    = public_path(self::LOCAL_BASE);
        $folders = [];
        $stats   = [];

        if (!is_dir($base)) {
            return [$folders, $stats];
        }

        $subdirs = array_filter(glob($base . DIRECTORY_SEPARATOR . '*'), 'is_dir');
        foreach ($subdirs as $dir) {
            $rel = self::LOCAL_BASE . '/' . basename($dir);
            $folders[] = $rel;

            $count = 0;
            $files = File::files($dir);
            foreach ($files as $file) {
                $ext = strtolower($file->getExtension());
                if (!in_array($ext, self::ALLOWED_EXTENSIONS)) continue;
                if ($isBounded && $collegeSlug) {
                    $fname = $file->getFilename();
                    if (!str_starts_with($fname, $collegeSlug . '__') && stripos($fname, $collegeSlug) === false) continue;
                }
                $count++;
            }
            $stats[$rel] = ['count' => $count, 'size' => 0];
        }

        return [$folders, $stats];
    }

    /**
     * Scan Google Drive images/* folders and return folder list + stats.
     */
    private function getGdriveFolders(bool $isBounded, ?string $collegeSlug): array
    {
        $folders = [];

        try {
            // First, list ALL root-level directories from Google Drive
            $rootContents = Storage::disk('google')->listContents('.', false);
            $rootDirs = [];
            foreach ($rootContents as $item) {
                if ($item instanceof \League\Flysystem\DirectoryAttributes) {
                    $rootDirs[] = $item->path();
                }
            }

            // If we found root dirs, recurse into each to find subfolders
            if (!empty($rootDirs)) {
                $folders = $rootDirs;
                foreach ($rootDirs as $rootDir) {
                    try {
                        $subDirs = Storage::disk('google')->allDirectories($rootDir);
                        $folders = array_merge($folders, $subDirs);
                    } catch (\Throwable $e) {
                        // Skip individual folder errors
                    }
                }
                $folders = array_unique($folders);
            }
        } catch (\Throwable $e) {
            // Fallback: try the legacy hardcoded approach
            try {
                $imageFolders  = Storage::disk('google')->allDirectories('images');
                $logosFolders  = Storage::disk('google')->allDirectories('logos');
                $folders       = array_unique(array_merge(['images', 'logos'], $imageFolders, $logosFolders));
            } catch (\Throwable $e2) {
                $folders = self::GDRIVE_DIRS;
            }
        }

        if (empty($folders)) {
            $folders = self::GDRIVE_DIRS;
        }

        $stats = [];
        foreach ($folders as $dir) {
            try {
                $items = Storage::disk('google')->files($dir);
            } catch (\Throwable $e) {
                $items = [];
            }
            $count = 0;
            foreach ($items as $item) {
                $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                if (!in_array($ext, self::ALLOWED_EXTENSIONS)) continue;
                if ($isBounded && $collegeSlug) {
                    $fname = basename($item);
                    if (!str_starts_with($fname, $collegeSlug . '__') && stripos($fname, $collegeSlug) === false) continue;
                }
                $count++;
            }
            $stats[$dir] = ['count' => $count, 'size' => 0];
        }

        return [$folders, $stats];
    }

    // -------------------------------------------------------------------------
    // INDEX
    // -------------------------------------------------------------------------

    public function index(Request $request): View|RedirectResponse
    {
        $search    = $request->input('search', '');
        $folder    = $request->input('folder', '');
        $source    = $request->input('source', 'local'); // 'local' | 'gdrive'
        $user      = $request->user();

        $isCollegeAdmin = $user && $user->isAdmin() && !$user->isSuperAdmin() && $user->college_slug;
        if ($isCollegeAdmin) {
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorized access to Media Library.');
        }

        $isBounded = $user && $user->isBoundedToCollege();
        $collegeSlug = $isBounded ? $user->college_slug : null;

        [$localFolders, $localFolderStats]   = $this->getLocalFolders($isBounded, $collegeSlug);
        [$gdriveFolders, $gdriveFolderStats] = $this->getGdriveFolders($isBounded, $collegeSlug);

        $localTotal  = array_sum(array_column($localFolderStats, 'count'));
        $gdriveTotal = array_sum(array_column($gdriveFolderStats, 'count'));
        $totalFiles  = $localTotal + $gdriveTotal;

        // No folder selected → show folder grid
        if (!$folder && !$search) {
            return view('admin.media.index', [
                'files'            => new LengthAwarePaginator([], 0, 12),
                'localFolders'     => $localFolders,
                'localFolderStats' => $localFolderStats,
                'gdriveFolders'    => $gdriveFolders,
                'gdriveFolderStats'=> $gdriveFolderStats,
                'currentFolder'    => '',
                'source'           => $source,
                'search'           => '',
                'totalFiles'       => $totalFiles,
                'localTotal'       => $localTotal,
                'gdriveTotal'      => $gdriveTotal,
                'showFolderGrid'   => true,
            ]);
        }

        // A specific folder (or search) is active → list its files
        $files = [];

        if ($source === 'local') {
            // ---------- Local files ----------
            $dirs = $folder ? [$folder] : $localFolders;
            foreach ($dirs as $dir) {
                $absDir = public_path($dir);
                if (!is_dir($absDir)) continue;
                foreach (File::files($absDir) as $file) {
                    $ext = strtolower($file->getExtension());
                    if (!in_array($ext, self::ALLOWED_EXTENSIONS)) continue;
                    $filename = $file->getFilename();
                    if ($search && stripos($filename, $search) === false) continue;
                    if ($isBounded && $collegeSlug) {
                        if (!str_starts_with($filename, $collegeSlug . '__') && stripos($filename, $collegeSlug) === false) continue;
                    }
                    $url      = asset($dir . '/' . $filename);
                    $modified = $file->getMTime();
                    $files[]  = [
                        'name'           => $filename,
                        'path'           => $dir . '/' . $filename,
                        'url'            => $url,
                        'size'           => $file->getSize(),
                        'size_human'     => $this->humanFileSize($file->getSize()),
                        'ext'            => $ext,
                        'modified'       => $modified,
                        'modified_human' => date('M j, Y', $modified),
                        'folder'         => $dir,
                        'is_image'       => in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']),
                        'source'         => 'local',
                    ];
                }
            }
        } else {
            // ---------- Google Drive files ----------
            $dirs = $folder ? [$folder] : $gdriveFolders;
            foreach ($dirs as $dir) {
                try {
                    $contents = Storage::disk('google')->listContents($dir, false);
                } catch (\Throwable $e) {
                    continue;
                }
                foreach ($contents as $item) {
                    if (!$item->isFile()) continue;
                    $path = $item->path();
                    $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    if (!in_array($ext, self::ALLOWED_EXTENSIONS)) continue;

                    $filename = basename($path);
                    if ($search && stripos($filename, $search) === false) continue;
                    if ($isBounded && $collegeSlug) {
                        if (!str_starts_with($filename, $collegeSlug . '__') && stripos($filename, $collegeSlug) === false) continue;
                    }

                    $extra  = $item->extraMetadata();
                    $fileId = $extra['id'] ?? null;
                    $url    = $fileId ? route('admin.media.proxy', ['fileId' => $fileId]) : '';

                    $files[] = [
                        'name'           => $filename,
                        'path'           => $path,
                        'url'            => $url,
                        'size'           => $item->isFile() ? $item->fileSize() : 0,
                        'size_human'     => $this->humanFileSize($item->isFile() ? $item->fileSize() : 0),
                        'ext'            => $ext,
                        'modified'       => $item->lastModified(),
                        'modified_human' => date('M j, Y', $item->lastModified()),
                        'folder'         => $dir,
                        'is_image'       => in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']),
                        'source'         => 'gdrive',
                    ];
                }
            }
        }

        usort($files, fn($a, $b) => $b['modified'] - $a['modified']);

        $perPage     = 24;
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $pagedData   = array_slice($files, ($currentPage - 1) * $perPage, $perPage);

        $paginatedFiles = new LengthAwarePaginator(
            $pagedData,
            count($files),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('admin.media.index', [
            'files'             => $paginatedFiles,
            'localFolders'      => $localFolders,
            'localFolderStats'  => $localFolderStats,
            'gdriveFolders'     => $gdriveFolders,
            'gdriveFolderStats' => $gdriveFolderStats,
            'currentFolder'     => $folder,
            'source'            => $source,
            'search'            => $search,
            'totalFiles'        => $totalFiles,
            'localTotal'        => $localTotal,
            'gdriveTotal'       => $gdriveTotal,
            'showFolderGrid'    => false,
        ]);
    }

    // -------------------------------------------------------------------------
    // UPLOAD (unchanged – uploads to GDrive)
    // -------------------------------------------------------------------------

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'files'    => ['required', 'array', 'min:1'],
            'files.*'  => ['file', 'max:5120'],
            'folder'   => ['required', 'string'],
            'source'   => ['nullable', 'string', 'in:local,gdrive'],
        ]);

        $folder = $request->input('folder', 'images/uploads');
        $source = $request->input('source', 'gdrive');

        if (!str_starts_with($folder, 'images/') && !in_array($folder, self::GDRIVE_DIRS)) {
            $folder = 'images/uploads';
        }

        $uploaded = 0;
        foreach ($request->file('files') as $file) {
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, self::ALLOWED_EXTENSIONS)) continue;

            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = Str::slug($originalName);
            $timestamp = time();
            
            $filename = $safeName . '_' . $timestamp . '.' . $ext;
            $user = $request->user();
            if ($user && $user->isBoundedToCollege() && $user->college_slug) {
                $filename = $user->college_slug . '__' . $filename;
            }

            if ($source === 'local') {
                $dest = public_path($folder);
                if (!is_dir($dest)) {
                    mkdir($dest, 0775, true);
                }
                $file->move($dest, $filename);
            } else {
                Storage::disk('google')->put($folder . '/' . $filename, File::get($file->getRealPath()));
            }
            $uploaded++;
        }

        return redirect()->route('admin.media.index', ['folder' => $folder, 'source' => $source])
            ->with('success', "{$uploaded} file(s) uploaded successfully.");
    }

    // -------------------------------------------------------------------------
    // DELETE
    // -------------------------------------------------------------------------

    public function destroy(Request $request): RedirectResponse
    {
        $path   = $request->input('path');
        $source = $request->input('source', 'gdrive');

        if (!$path) {
            return redirect()->route('admin.media.index')->with('error', 'No file specified.');
        }

        $isAllowed = str_starts_with($path, 'images/') || in_array(dirname($path), self::GDRIVE_DIRS);
        if (!$isAllowed) {
            return redirect()->route('admin.media.index')->with('error', 'Invalid file path.');
        }

        if ($source === 'local') {
            $absPath = public_path($path);
            if (file_exists($absPath)) {
                unlink($absPath);
                return redirect()->route('admin.media.index')->with('success', 'File deleted.');
            }
            return redirect()->route('admin.media.index')->with('error', 'File not found locally.');
        }

        if (Storage::disk('google')->exists($path)) {
            Storage::disk('google')->delete($path);
            return redirect()->route('admin.media.index')->with('success', 'File deleted.');
        }

        return redirect()->route('admin.media.index')->with('error', 'File not found.');
    }

    // -------------------------------------------------------------------------
    // JSON API (media picker modal)
    // -------------------------------------------------------------------------

    public function apiIndex(Request $request): JsonResponse
    {
        $search    = $request->input('search', '');
        $folder    = $request->input('folder', '');
        $source    = $request->input('source', 'gdrive');
        $page      = (int) $request->input('page', 1);
        $perPage   = 24;
        $user      = $request->user();
        $isBounded = $user && $user->isBoundedToCollege();
        $collegeSlug = $isBounded ? $user->college_slug : null;

        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $files     = [];

        if ($source === 'local') {
            [$localFolders] = $this->getLocalFolders($isBounded, $collegeSlug);
            $dirs = $folder ? [$folder] : $localFolders;
            foreach ($dirs as $dir) {
                $absDir = public_path($dir);
                if (!is_dir($absDir)) continue;
                foreach (File::files($absDir) as $file) {
                    $ext = strtolower($file->getExtension());
                    if (!in_array($ext, $imageExts)) continue;
                    $filename = $file->getFilename();
                    if ($search && stripos($filename, $search) === false) continue;
                    if ($isBounded && $collegeSlug) {
                        if (!str_starts_with($filename, $collegeSlug . '__') && stripos($filename, $collegeSlug) === false) continue;
                    }
                    $files[] = [
                        'name'       => $filename,
                        'path'       => $dir . '/' . $filename,
                        'url'        => asset($dir . '/' . $filename),
                        'size_human' => $this->humanFileSize($file->getSize()),
                        'modified'   => $file->getMTime(),
                        'folder'     => $dir,
                        'source'     => 'local',
                    ];
                }
            }
        } else {
            // When no specific folder selected, scan all known GDrive directories
            if ($folder) {
                $dirs = [$folder];
            } else {
                [$dirs] = $this->getGdriveFolders($isBounded, $collegeSlug);
            }
            foreach ($dirs as $dir) {
                try {
                    $contents = Storage::disk('google')->listContents($dir, false);
                } catch (\Throwable $e) {
                    continue;
                }
                foreach ($contents as $item) {
                    if (!$item->isFile()) continue;
                    $path = $item->path();
                    $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    if (!in_array($ext, $imageExts)) continue;

                    $filename = basename($path);
                    if ($search && stripos($filename, $search) === false) continue;
                    if ($isBounded && $collegeSlug) {
                        if (!str_starts_with($filename, $collegeSlug . '__') && stripos($filename, $collegeSlug) === false) continue;
                    }

                    $extra  = $item->extraMetadata();
                    $fileId = $extra['id'] ?? null;
                    $url    = $fileId ? route('admin.media.proxy', ['fileId' => $fileId]) : '';

                    $files[] = [
                        'name'           => $filename,
                        'path'           => $path,
                        'url'            => $url,
                        'size_human' => $this->humanFileSize($item->isFile() ? $item->fileSize() : 0),
                        'modified'   => $item->lastModified(),
                        'folder'     => $dir,
                        'source'     => 'gdrive',
                    ];
                }
            }
        }

        usort($files, fn($a, $b) => $b['modified'] - $a['modified']);
        $total    = count($files);
        $pagedData = array_slice($files, ($page - 1) * $perPage, $perPage);

        [$localFolders]  = $this->getLocalFolders($isBounded, $collegeSlug);
        [$gdriveFolders] = $this->getGdriveFolders($isBounded, $collegeSlug);

        return response()->json([
            'files'         => $pagedData,
            'total'         => $total,
            'page'          => $page,
            'per_page'      => $perPage,
            'has_more'      => ($page * $perPage) < $total,
            'local_folders' => $localFolders,
            'gdrive_folders'=> $gdriveFolders,
            // 'folders' is what the media-modal.blade.php JS uses for the dropdown
            'folders'       => array_values(array_unique(array_merge($gdriveFolders, $localFolders))),
        ]);
    }

    /**
     * AJAX upload: returns JSON with uploaded file paths (GDrive only).
     */
    public function apiUpload(Request $request): JsonResponse
    {
        $request->validate([
            'files'    => ['required', 'array', 'min:1'],
            'files.*'  => ['file', 'max:5120'],
            'folder'   => ['nullable', 'string'],
        ]);

        $folder = $request->input('folder', 'images/uploads');
        if (empty($folder)) {
            $folder = 'images/uploads';
        }

        $uploaded  = [];
        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $user = $request->user(); // Define $user here
        foreach ($request->file('files') as $file) {
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, $imageExts)) continue;

            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = Str::slug($originalName);
            $timestamp = time();
            
            $filename = $safeName . '_' . $timestamp . '.' . $ext;
            
            // Add college prefix if bounded
            if ($user && $user->isBoundedToCollege() && $user->college_slug) {
                $filename = $user->college_slug . '__' . $filename;
            }
            $path = $folder . '/' . $filename;
            Storage::disk('google')->put($path, File::get($file->getRealPath()));

            $uploaded[] = [
                'name' => $filename,
                'path' => $path,
                'url'  => \App\Providers\AppServiceProvider::resolveLogoUrl(Storage::disk('google')->url($path)),
            ];
        }

        return response()->json(['files' => $uploaded, 'count' => count($uploaded)]);
    }

    /**
     * Proxy a Google Drive file through the server to avoid 403 on direct URLs.
     */
    public function proxy(string $fileId)
    {
        $driveService = app(GoogleDriveService::class);
        $mimeType     = $driveService->getMimeTypeById($fileId);
        $contents     = $driveService->streamFileById($fileId);

        if ($contents === null) {
            abort(404);
        }

        return response($contents, 200)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
