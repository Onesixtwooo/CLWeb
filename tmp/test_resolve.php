<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$url = 'https://drive.google.com/uc?export=view&id=1XmehyJUXs1HCj8Da94ADpo9JL8Cj5_do';
$prefix = 'images';

echo "Input URL: " . $url . PHP_EOL;
echo "Prefix: " . $prefix . PHP_EOL;
echo "Resolved URL: " . \App\Providers\AppServiceProvider::resolveImageUrl($url, $prefix) . PHP_EOL;

$url2 = 'faculty/photo.jpg';
echo "Input Local: " . $url2 . PHP_EOL;
echo "Resolved Local: " . \App\Providers\AppServiceProvider::resolveImageUrl($url2, $prefix) . PHP_EOL;

// Test getUrl method directly
$driveService = app(\App\Services\GoogleDriveService::class);
$testPath = 'some/path/image.jpg'; // This should be a path that exists in Google Drive
echo "Testing getUrl with path: " . $testPath . PHP_EOL;
echo "getUrl result: " . $driveService->getUrl($testPath) . PHP_EOL;
