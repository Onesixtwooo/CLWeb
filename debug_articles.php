<?php

use App\Models\Article;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$college = 'veterinary-science-and-medicine'; 

$articles = Article::where(function ($query) use ($college) {
        $query->where('college_slug', $college)
              ->orWhereNull('college_slug');
    })
    ->whereNotNull('published_at')
    ->orderBy('published_at', 'desc')
    ->limit(4)
    ->get();

echo "Count: " . $articles->count() . "\n";
foreach ($articles as $article) {
    echo "ID: {$article->id} | Title: {$article->title} | College: " . ($article->college_slug ?? 'NULL') . "\n";
}
