<?php

use App\Http\Controllers\CollegePageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Enable logging to stdout or file
Log::setDefaultDriver('single');

$request = Request::create('/college/engineering', 'GET');
$controller = new CollegePageController();

try {
    $view = $controller->show($request, 'engineering');
    $content = $view->render();
    
    // Check if the content contains the overview body or the fallback text
    if (strpos($content, 'As a center of higher learning') !== false) {
        echo "SUCCESS: Found overview text in output.\n";
    } else {
        echo "FAILURE: Did not find overview text.\n";
    }

    if (strpos($content, 'is one of the colleges in the Central Luzon State University') !== false) {
        echo "FOUND FALLBACK TEXT.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
