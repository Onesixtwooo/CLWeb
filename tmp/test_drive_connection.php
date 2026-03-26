<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$driveService = app(\App\Services\GoogleDriveService::class);

echo "Google Drive Service configured: " . ($driveService->isConfigured() ? 'YES' : 'NO') . PHP_EOL;

if ($driveService->isConfigured()) {
    // Try to list files in the root folder
    $files = $driveService->listFiles();
    echo "Files in root folder: " . count($files) . PHP_EOL;
    if (count($files) > 0) {
        echo "First file: " . $files[0]->getName() . PHP_EOL;
    }
} else {
    echo "Service not configured - check credentials" . PHP_EOL;
}