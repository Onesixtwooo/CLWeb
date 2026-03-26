<?php
use Illuminate\Support\Facades\Storage;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// List current directories inside articles
try {
    $dirsBefore = Storage::disk('google')->allDirectories("articles");
    echo "Directories in articles (Before):\n";
    print_r($dirsBefore);
    
    echo "Creating articles/test_dir2...\n";
    Storage::disk('google')->makeDirectory("articles/test_dir2");
    
    $dirsAfterMake = Storage::disk('google')->allDirectories("articles");
    echo "Directories after creation:\n";
    print_r($dirsAfterMake);
    
    echo "Deleting via deleteDirectory('articles/test_dir2')...\n";
    Storage::disk('google')->deleteDirectory("articles/test_dir2");

    $dirsAfterDel = Storage::disk('google')->allDirectories("articles");
    echo "Directories after deleteDirectory:\n";
    print_r($dirsAfterDel);
    
    echo "Creating articles/test_dir3...\n";
    Storage::disk('google')->makeDirectory("articles/test_dir3");
    
    echo "Deleting via delete('articles/test_dir3')...\n";
    Storage::disk('google')->delete("articles/test_dir3");

    $dirsAfterDel3 = Storage::disk('google')->allDirectories("articles");
    echo "Directories after delete:\n";
    print_r($dirsAfterDel3);

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
