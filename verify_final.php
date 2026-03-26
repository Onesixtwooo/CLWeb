<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;

try {
    $disk = Storage::disk('google');
    
    echo "Testing URL retrieval for 'test_file.txt'...\n";
    $url = $disk->url('test_file.txt');
    echo "URL: $url\n";
    
    echo "Testing file existence for 'test_file.txt'...\n";
    $exists = $disk->exists('test_file.txt');
    echo "Exists: " . ($exists ? 'YES' : 'NO') . "\n";
    
    echo "Testing file listing...\n";
    $files = $disk->files();
    echo "Found " . count($files) . " files.\n";
    if (count($files) > 0) {
        echo "First file: " . $files[0] . "\n";
    }

} catch (Exception $e) {
    echo "Caught: " . get_class($e) . ": " . $e->getMessage() . "\n";
}
