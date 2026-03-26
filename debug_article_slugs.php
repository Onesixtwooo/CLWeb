<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\Article;

$article2 = Article::where('slug', 'footer-test-2')->first();
$article1 = Article::where('slug', 'footer-test-1')->first();

echo "Article 1 (footer-test-1): " . ($article1 ? "slug={$article1->slug}, college=" . ($article1->college_slug ?? 'NULL') : "NOT FOUND") . "\n";
echo "Article 2 (footer-test-2): " . ($article2 ? "slug={$article2->slug}, college=" . ($article2->college_slug ?? 'NULL') : "NOT FOUND") . "\n";
