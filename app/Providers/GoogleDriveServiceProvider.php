<?php

namespace App\Providers;

use App\Services\GoogleDriveService;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter as FlysystemAdapterInterface;

class GoogleDriveServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(GoogleDriveService::class, function ($app) {
            return new GoogleDriveService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Storage::extend('google', function ($app, $config) {
            $service = $app->make(GoogleDriveService::class);
            
            $adapter = new class($service) implements FlysystemAdapterInterface {
                protected $service;
                
                public function __construct($service) {
                    $this->service = $service;
                }
                
                public function fileExists(string $path): bool { 
                    return $this->service->exists($path); 
                }
                public function directoryExists(string $path): bool {
                    // Check if the path resolves to a folder in Google Drive
                    try {
                        $items = $this->service->listFolders(dirname($path) === '.' ? null : dirname($path));
                        $folderName = basename($path);
                        foreach ($items as $item) {
                            if ($item->getName() === $folderName) {
                                return true;
                            }
                        }
                    } catch (\Throwable $e) {}
                    return false;
                }
                public function write(string $path, string $contents, \League\Flysystem\Config $config): void {
                    $this->service->put($path, $contents, ['mimetype' => $config->get('mimetype')]);
                }
                public function writeStream(string $path, $contents, \League\Flysystem\Config $config): void {
                    $data = '';
                    if (is_resource($contents)) {
                        rewind($contents);
                        while (!feof($contents)) {
                            $data .= fread($contents, 8192);
                        }
                    }
                    
                    if (empty($data) && is_resource($contents)) {
                        // Fallback if rewind/fread failed
                        $data = stream_get_contents($contents);
                    }

                    $this->service->put($path, $data, ['mimetype' => $config->get('mimetype')]);
                }
                public function read(string $path): string { return ''; }
                public function readStream(string $path) { return null; }
                public function delete(string $path): void {
                    $this->service->delete($path);
                }
                public function deleteDirectory(string $path): void { }
                public function createDirectory(string $path, \League\Flysystem\Config $config): void { }
                public function setVisibility(string $path, string $visibility): void { }
                public function visibility(string $path): \League\Flysystem\FileAttributes { 
                    return new \League\Flysystem\FileAttributes($path, null, 'public'); 
                }
                public function mimeType(string $path): \League\Flysystem\FileAttributes {
                    $meta = $this->service->getMetadata($path);
                    return new \League\Flysystem\FileAttributes($path, null, null, null, $meta->mimeType ?? 'application/octet-stream');
                }
                public function lastModified(string $path): \League\Flysystem\FileAttributes {
                    $meta = $this->service->getMetadata($path);
                    return new \League\Flysystem\FileAttributes($path, null, null, $meta ? strtotime($meta->modifiedTime) : time());
                }
                public function fileSize(string $path): \League\Flysystem\FileAttributes {
                    $meta = $this->service->getMetadata($path);
                    return new \League\Flysystem\FileAttributes($path, $meta ? (int)$meta->size : 0);
                }
                public function listContents(string $path, bool $deep): iterable { 
                    $items = $this->service->listFiles($path);
                    foreach ($items as $item) {
                        $isFolder = ($item->getMimeType() === 'application/vnd.google-apps.folder');
                        $fullPath = ($path === '.' || $path === '/' || empty($path)) 
                            ? $item->getName() 
                            : rtrim($path, '/') . '/' . $item->getName();

                        if ($isFolder) {
                            yield new \League\Flysystem\DirectoryAttributes(
                                $fullPath,
                                null,
                                strtotime($item->getModifiedTime()),
                                ['id' => $item->getId()]
                            );

                            // Recursively list subdirectory contents when $deep is true
                            if ($deep) {
                                yield from $this->listContents($fullPath, true);
                            }
                        } else {
                            $size = $item->getSize();
                            $modified = $item->getModifiedTime();
                            
                            yield new \League\Flysystem\FileAttributes(
                                $fullPath,
                                $size !== null ? (int)$size : 0,
                                null,
                                $modified ? strtotime($modified) : time(),
                                $item->getMimeType(),
                                [
                                    'id' => $item->getId(),
                                    'thumbnailLink' => $item->getThumbnailLink(),
                                    'webContentLink' => $item->getWebContentLink(),
                                    'webViewLink' => $item->getWebViewLink(),
                                ]
                            );
                        }
                    }
                }
                public function move(string $source, string $destination, \League\Flysystem\Config $config): void { }
                public function copy(string $source, string $destination, \League\Flysystem\Config $config): void { }
                
                public function url(string $path): string {
                     return $this->service->getUrl($path) ?? '';
                }

                public function getUrl(string $path): string {
                    return $this->service->getUrl($path) ?? '';
                }
            };

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
    }
}
