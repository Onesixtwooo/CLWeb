<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$driveService = app(\App\Services\GoogleDriveService::class);

echo "Google Drive Service configured: " . ($driveService->isConfigured() ? 'YES' : 'NO') . PHP_EOL;

$fileId = '1XmehyJUXs1HCj8Da94ADpo9JL8Cj5_do'; // Test file ID from the test script

echo "Testing file ID: {$fileId}" . PHP_EOL;

$mimeType = $driveService->getMimeTypeById($fileId);
echo "Mime type: " . ($mimeType ?: 'null') . PHP_EOL;

$contents = $driveService->streamFileById($fileId);
echo "Content length: " . (is_null($contents) ? 'null' : strlen($contents)) . PHP_EOL;

if ($contents === null) {
    echo "Failed to stream file - check Google Drive configuration" . PHP_EOL;
} else {
    echo "Successfully streamed file content" . PHP_EOL;
}