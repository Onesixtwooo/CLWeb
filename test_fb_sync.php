<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FacebookConfig;
use App\Services\FacebookService;

$config = FacebookConfig::first();
if (!$config) {
    die("No configs in DB.\n");
}

$service = new FacebookService();
$posts = $service->fetchPostsFromConfig($config);

echo "Total posts fetched from API: " . count($posts) . "\n";
if (count($posts) > 0) {
    echo "First post message: " . ($posts[0]['message'] ?? 'No message') . "\n";
} else {
    echo "Check laravel.log for direct errors!\n";
}
