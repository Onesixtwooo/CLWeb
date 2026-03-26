<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;

try {
    echo "Testing Google Drive Upload...\n";
    $disk = Storage::disk('google');
    $content = "Test File Content " . date('Y-m-d H:i:s');
    $path = "test_" . time() . ".txt";
    
    // Note: Our custom adapter doesn't return the ID from put yet, 
    // but putFileAs might work if we implement it.
    
    $result = $disk->put($path, $content);
    echo "Upload Result: " . ($result ? "Success" : "Failed") . "\n";
    
    if ($result) {
        $url = $disk->url($path);
        echo "File URL: " . $url . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
