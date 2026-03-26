<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected $service;
    protected $folderCache = [];
    protected $configured = false;
    protected $folderId;

    public function __construct()
    {
        $client = new Client();
        
        // Fetch settings from database, fallback to config if not set
        $clientId = \App\Models\Setting::get('google_drive_client_id', config('filesystems.disks.google.client_id'));
        $clientSecret = \App\Models\Setting::get('google_drive_client_secret', config('filesystems.disks.google.client_secret'));
        $refreshToken = \App\Models\Setting::get('google_drive_refresh_token', config('filesystems.disks.google.refresh_token'));
        $folderId = \App\Models\Setting::get('google_drive_folder_id', config('filesystems.disks.google.folder_id'));

        if (empty($clientId) || empty($clientSecret) || empty($refreshToken)) {
            Log::warning('Google Drive Service is not fully configured. Missing credentials.');
            $this->configured = false;
            return;
        }

        try {
            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
            
            // Try to refresh the token
            $accessToken = $client->refreshToken($refreshToken);
            
            // Check if refresh failed
            if (isset($accessToken['error'])) {
                Log::error('Google Drive Token Refresh Error: ' . json_encode($accessToken));
                
                // Try to get a new refresh token from the file if available
                $newRefreshToken = $this->getNewRefreshTokenFromFile();
                if ($newRefreshToken && $newRefreshToken !== $refreshToken) {
                    Log::info('Attempting to use new refresh token from file');
                    $accessToken = $client->refreshToken($newRefreshToken);
                    
                    if (!isset($accessToken['error'])) {
                        // Update the stored token
                        $this->updateStoredRefreshToken($newRefreshToken);
                        $refreshToken = $newRefreshToken;
                        Log::info('Successfully updated to new refresh token');
                    }
                }
                
                if (isset($accessToken['error'])) {
                    Log::error('Google Drive authentication failed. Please run: php get_refresh_token.php');
                    $this->configured = false;
                    return;
                }
            }
            
            $client->addScope(Drive::DRIVE);
            $this->service = new Drive($client);
            $this->folderId = $folderId;
            $this->folderCache['/'] = $this->folderId;
            $this->configured = true;
            
            Log::info('Google Drive Service initialized successfully');
        } catch (\Exception $e) {
            Log::error('Google Drive Initialization Error: ' . $e->getMessage());
            $this->configured = false;
        }
    }

    public function isConfigured(): bool
    {
        return $this->configured;
    }

    public function exists($path): bool
    {
        if (!$this->configured || empty($path)) {
            return false;
        }

        return $this->getFileId($path) !== null;
    }

    public function put($path, $contents, $options = [])
    {
        if (!$this->configured) return null;

        $dirname = dirname($path);
        $filename = basename($path);
        
        $parentId = $this->getOrCreatePath($dirname);
        if (!$parentId) return null;

        $fileMetadata = new DriveFile([
            'name' => $filename,
            'parents' => [$parentId]
        ]);

        try {
            $file = $this->service->files->create($fileMetadata, [
                'data' => $contents,
                'mimeType' => $options['mimetype'] ?? 'application/octet-stream',
                'uploadType' => 'multipart',
                'fields' => 'id, webViewLink, webContentLink',
                'supportsAllDrives' => true,
            ]);

            // Make the file public for direct linking
            $this->makePublic($file->id);

            return $file;
        } catch (\Exception $e) {
            Log::error('Google Drive Upload Error: ' . $e->getMessage());
            return null;
        }
    }

    protected function getOrCreatePath($path)
    {
        if ($path === '.' || $path === '/' || empty($path)) {
            return $this->folderId;
        }

        $path = trim($path, '/');
        
        if (isset($this->folderCache[$path])) {
            return $this->folderCache[$path];
        }

        $parts = explode('/', $path);
        $currentParentId = $this->folderId;
        $currentPath = '';

        foreach ($parts as $part) {
            $currentPath .= ($currentPath === '' ? '' : '/') . $part;
            
            if (isset($this->folderCache[$currentPath])) {
                $currentParentId = $this->folderCache[$currentPath];
                continue;
            }

            // Search for the folder
            $optParams = [
                'q' => "name = '{$part}' and '{$currentParentId}' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false",
                'fields' => 'files(id)',
                'supportsAllDrives' => true,
                'includeItemsFromAllDrives' => true,
            ];

            if (!$this->configured) return null;

            try {
                $results = $this->service->files->listFiles($optParams);
                if (count($results->getFiles()) > 0) {
                    $currentParentId = $results->getFiles()[0]->getId();
                } else {
                    // Create the folder
                    $folderMetadata = new DriveFile([
                        'name' => $part,
                        'mimeType' => 'application/vnd.google-apps.folder',
                        'parents' => [$currentParentId],
                    ]);
                    $folder = $this->service->files->create($folderMetadata, [
                        'fields' => 'id',
                        'supportsAllDrives' => true,
                    ]);
                    $currentParentId = $folder->id;
                    
                    // Also make the folder public (optional, but helps with consistency)
                    $this->makePublic($currentParentId);
                }
            } catch (\Exception $e) {
                Log::error('Google Drive getOrCreatePath error: ' . $e->getMessage());
                return null;
            }
            
            $this->folderCache[$currentPath] = $currentParentId;
        }

        return $currentParentId;
    }

    protected function resolvePath($path)
    {
        if ($path === '.' || $path === '/' || empty($path)) {
            return $this->folderId;
        }

        $path = trim($path, '/');
        
        if (isset($this->folderCache[$path])) {
            return $this->folderCache[$path];
        }

        $parts = explode('/', $path);
        $currentParentId = $this->folderId;
        $currentPath = '';

        foreach ($parts as $part) {
            $currentPath .= ($currentPath === '' ? '' : '/') . $part;
            
            if (isset($this->folderCache[$currentPath])) {
                $currentParentId = $this->folderCache[$currentPath];
                continue;
            }

            // Search for the folder (read-only — never create)
            $optParams = [
                'q' => "name = '{$part}' and '{$currentParentId}' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false",
                'fields' => 'files(id)',
                'supportsAllDrives' => true,
                'includeItemsFromAllDrives' => true,
            ];

            if (!$this->configured) return null;

            try {
                $results = $this->service->files->listFiles($optParams);
                if (count($results->getFiles()) > 0) {
                    $currentParentId = $results->getFiles()[0]->getId();
                } else {
                    // Folder not found — return null (don't create)
                    return null;
                }
            } catch (\Exception $e) {
                Log::error('Google Drive resolvePath error: ' . $e->getMessage());
                return null;
            }
            
            $this->folderCache[$currentPath] = $currentParentId;
        }

        return $currentParentId;
    }

    public function delete($path)
    {
        if (!$this->configured) return false;

        $fileId = $this->getFileId($path);
        if (!$fileId) return false;

        return $this->deleteById($fileId);
    }

    public function deleteById(string $fileId): bool
    {
        if (!$this->configured || empty($fileId)) return false;

        try {
            $this->service->files->delete($fileId, [
                'supportsAllDrives' => true,
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Google Drive Delete Error: ' . $e->getMessage());
            return false;
        }
    }

    public function getFileId($path)
    {
        if (preg_match('/media\/proxy\/([^\/?&#]+)/', (string) $path, $matches)) {
            return $matches[1];
        }

        // If the path is already a LH3 link or similar, extract ID
        if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $path, $matches)) {
            return $matches[1];
        }

        // Handle drive.google.com/uc?id=... format
        if (preg_match('/id=([a-zA-Z0-9_-]+)/', $path, $matches)) {
            return $matches[1];
        }

        // Resolve the parent directory ID first
        $dirname = dirname($path);
        $filename = basename($path);
        
        $parentId = ($dirname === '.' || $dirname === '/' || empty($dirname)) 
            ? $this->folderId 
            : $this->resolvePath($dirname);

        // Search for the file in that specific parent folder
        $optParams = [
            'q' => "name = '{$filename}' and '{$parentId}' in parents and trashed = false",
            'fields' => 'files(id)',
            'supportsAllDrives' => true,
            'includeItemsFromAllDrives' => true,
        ];
        
        if (!$this->configured) return null;

        try {
            $results = $this->service->files->listFiles($optParams);
            if (count($results->getFiles()) > 0) {
                return $results->getFiles()[0]->getId();
            }
        } catch (\Exception $e) {
            Log::error('Google Drive getFileId listFiles error: ' . $e->getMessage());
        }

        return null;
    }

    public function deleteFolder(string $path): bool
    {
        if (!$this->configured || empty($path)) {
            return false;
        }

        $folderId = $this->resolvePath($path);
        if (!$folderId) {
            return false;
        }

        return $this->deleteFolderById($folderId);
    }

    protected function deleteFolderById(string $folderId): bool
    {
        try {
            $children = $this->service->files->listFiles([
                'q' => "'{$folderId}' in parents and trashed = false",
                'fields' => 'files(id, mimeType)',
                'supportsAllDrives' => true,
                'includeItemsFromAllDrives' => true,
                'pageSize' => 1000,
            ])->getFiles();

            foreach ($children as $child) {
                if ($child->getMimeType() === 'application/vnd.google-apps.folder') {
                    $this->deleteFolderById($child->getId());
                    continue;
                }

                $this->deleteById($child->getId());
            }

            return $this->deleteById($folderId);
        } catch (\Exception $e) {
            Log::error('Google Drive deleteFolder Error: ' . $e->getMessage());
            return false;
        }
    }

    public function getUrl($path)
    {
        if (!$this->configured) return null;

        $fileId = $this->getFileId($path);
        if (!$fileId) return null;
        
        // Return proxy URL to stream through server and avoid CORS/permission issues
        return "/media/proxy/{$fileId}";
    }

    /**
     * Stream a file by its Drive file ID through the application server.
     * This avoids all 403/permission issues with direct Drive URLs.
     */
    public function streamFileById(string $fileId)
    {
        if (!$this->configured) return null;

        try {
            $response = $this->service->files->get($fileId, [
                'supportsAllDrives' => true,
                'alt' => 'media',
            ]);
            
            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            Log::error('Google Drive streamFileById Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get mime type for a file ID.
     */
    public function getMimeTypeById(string $fileId): string
    {
        if (!$this->configured) return 'application/octet-stream';

        try {
            $file = $this->service->files->get($fileId, [
                'fields' => 'mimeType',
                'supportsAllDrives' => true,
            ]);
            return $file->getMimeType() ?? 'application/octet-stream';
        } catch (\Exception $e) {
            return 'application/octet-stream';
        }
    }

    public function getMetadata($path)
    {
        if (!$this->configured) return null;

        $fileId = $this->getFileId($path);
        if (!$fileId) return null;

        try {
            return $this->service->files->get($fileId, [
                'fields' => 'id, name, size, mimeType, modifiedTime, thumbnailLink, webContentLink',
                'supportsAllDrives' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Google Drive GetMetadata Error: ' . $e->getMessage());
            return null;
        }
    }

    public function makePublic($fileId)
    {
        if (!$this->configured) return false;

        try {
            $permission = new \Google\Service\Drive\Permission([
                'type' => 'anyone',
                'role' => 'reader',
            ]);
            $this->service->permissions->create($fileId, $permission, [
                'supportsAllDrives' => true,
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Google Drive Permission Error: ' . $e->getMessage());
            return false;
        }
    }
    
    public function listFiles($folder = null)
    {
        if (!$this->configured) return [];

        $id = $this->resolvePath($folder);
        if (!$id) return [];
        
        $allFiles = [];
        $pageToken = null;

        try {
            do {
                $optParams = [
                    'q' => "'{$id}' in parents and trashed = false",
                    'fields' => 'nextPageToken, files(id, name, mimeType, size, modifiedTime, thumbnailLink, webContentLink, webViewLink)',
                    'pageSize' => 1000,
                    'supportsAllDrives' => true,
                    'includeItemsFromAllDrives' => true,
                ];

                if ($pageToken) {
                    $optParams['pageToken'] = $pageToken;
                }

                $results = $this->service->files->listFiles($optParams);
                $allFiles = array_merge($allFiles, $results->getFiles());
                $pageToken = $results->getNextPageToken();
            } while ($pageToken !== null);

            return $allFiles;
        } catch (\Exception $e) {
            Log::error('Google Drive listFiles Error: ' . $e->getMessage());
            return $allFiles;
        }
    }

    public function listFolders($parentFolder = null)
    {
        if (!$this->configured) return [];

        $id = $this->resolvePath($parentFolder);
        if (!$id) return [];
        
        $allFolders = [];
        $pageToken = null;

        try {
            do {
                $optParams = [
                    'q' => "'{$id}' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false",
                    'fields' => 'nextPageToken, files(id, name, modifiedTime)',
                    'pageSize' => 1000,
                    'supportsAllDrives' => true,
                    'includeItemsFromAllDrives' => true,
                ];

                if ($pageToken) {
                    $optParams['pageToken'] = $pageToken;
                }

                $results = $this->service->files->listFiles($optParams);
                $allFolders = array_merge($allFolders, $results->getFiles());
                $pageToken = $results->getNextPageToken();
            } while ($pageToken !== null);

            return $allFolders;
        } catch (\Exception $e) {
            Log::error('Google Drive listFolders Error: ' . $e->getMessage());
            return $allFolders;
        }
    }

    protected function getNewRefreshTokenFromFile(): ?string
    {
        $tokenFile = base_path('refresh_token.txt');
        if (file_exists($tokenFile)) {
            $token = trim(file_get_contents($tokenFile));
            return $token ?: null;
        }
        return null;
    }

    protected function updateStoredRefreshToken(string $newToken): void
    {
        // Update database setting
        $setting = \App\Models\Setting::where('key', 'google_drive_refresh_token')->first();
        if ($setting) {
            $setting->update(['value' => $newToken]);
        }
        
        // Also update .env file
        $envFile = base_path('.env');
        if (file_exists($envFile)) {
            $envContent = file_get_contents($envFile);
            $envContent = preg_replace(
                '/GOOGLE_DRIVE_REFRESH_TOKEN=.*/',
                'GOOGLE_DRIVE_REFRESH_TOKEN=' . $newToken,
                $envContent
            );
            file_put_contents($envFile, $envContent);
        }
    }

    public function getParentIdOfFile(string $path): ?string
    {
        if (!$this->configured) return null;

        $fileId = $this->getFileId($path);
        if (!$fileId) return null;

        try {
            $file = $this->service->files->get($fileId, [
                'fields' => 'parents',
                'supportsAllDrives' => true,
            ]);
            $parents = $file->getParents();
            return !empty($parents) ? $parents[0] : null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Drive getParentIdOfFile error: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteFolderIfEmpty(string $folderId): bool
    {
        if (!$this->configured || empty($folderId)) return false;

        try {
            $results = $this->service->files->listFiles([
                'q' => "'{$folderId}' in parents and trashed = false",
                'fields' => 'files(id)',
                'supportsAllDrives' => true,
                'includeItemsFromAllDrives' => true,
                'pageSize' => 1,
            ]);

            if (count($results->getFiles()) === 0) {
                return $this->deleteById($folderId);
            }
            return false;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Drive deleteFolderIfEmpty Error: ' . $e->getMessage());
            return false;
        }
    }
}
