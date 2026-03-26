<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\GoogleDriveService;
use App\Models\Article;
use App\Models\Scholarship;

$service = app(GoogleDriveService::class);

echo "Updating Article URLs to LH3 format...\n";
Article::all()->each(function ($article) use ($service) {
    $updated = false;
    
    // Check banner
    if ($article->banner && (str_contains($article->banner, 'drive.google.com/uc') || str_contains($article->banner, 'googleusercontent.com/u/0/d/'))) {
        $article->banner = $service->getUrl($article->banner);
        $updated = true;
    }
    
    // Check images array
    if ($article->images) {
        $newImages = [];
        foreach ($article->images as $img) {
            if (str_contains($img, 'drive.google.com/uc') || str_contains($img, 'googleusercontent.com/u/0/d/')) {
                $newImages[] = $service->getUrl($img);
                $updated = true;
            } else {
                $newImages[] = $img;
            }
        }
        $article->images = $newImages;
    }
    
    if ($updated) {
        $article->save();
        echo "Updated Article: {$article->title}\n";
    }
});

echo "\nUpdating Scholarship URLs to LH3 format...\n";
Scholarship::all()->each(function ($scholarship) use ($service) {
    if ($scholarship->image && (str_contains($scholarship->image, 'drive.google.com/uc') || str_contains($scholarship->image, 'googleusercontent.com/u/0/d/'))) {
        $scholarship->image = $service->getUrl($scholarship->image);
        $scholarship->save();
        echo "Updated Scholarship: {$scholarship->title}\n";
    }
});

echo "\nDone!\n";
