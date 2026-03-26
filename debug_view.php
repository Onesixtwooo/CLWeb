<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "Simulating Request and Capturing Output...\n";

$request = Illuminate\Http\Request::create('/college/veterinary-science-and-medicine', 'GET');
$response = $kernel->handle($request);

$content = $response->getContent();

if (preg_match('/<!-- DEBUG: Articles Count: (\d+) -->/', $content, $matches)) {
    echo "FOUND DEBUG COMMENT! Count: " . $matches[1] . "\n";
} else {
    echo "DEBUG COMMENT NOT FOUND in output.\n";
    // echo substr($content, 0, 500); // Dump start of content if needed
}

// Check for "No news or announcements" text
if (strpos($content, 'No news or announcements available') !== false) {
    echo "Page says: No news available.\n";
} else {
    echo "Page does NOT say 'No news available' - likely showing articles.\n";
}
