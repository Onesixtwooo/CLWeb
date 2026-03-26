<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$a = \App\Models\Article::latest()->first();
echo "ARTICLE_ID: " . $a->id . "\n";
echo "BANNER: " . $a->banner . "\n";
if ($a->images) {
    echo "IMAGES:\n";
    foreach ($a->images as $img) {
        echo "  - $img\n";
    }
}
