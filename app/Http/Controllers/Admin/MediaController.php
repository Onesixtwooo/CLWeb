<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoogleDriveService;

class MediaController extends Controller
{
    /**
     * Proxy a Google Drive file through the server to avoid direct-link access issues.
     */
    public function proxy(string $fileId)
    {
        $driveService = app(GoogleDriveService::class);
        $mimeType = $driveService->getMimeTypeById($fileId);
        $contents = $driveService->streamFileById($fileId);

        if ($contents === null) {
            abort(404);
        }

        return response($contents, 200)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
