<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    echo "Adding missing columns...\n";
    Schema::table('announcements', function (Blueprint $table) {
        if (!Schema::hasColumn('announcements', 'images')) {
            $table->json('images')->nullable()->after('image');
            echo "Added 'images' column.\n";
        }
        if (!Schema::hasColumn('announcements', 'banner_dark')) {
            $table->boolean('banner_dark')->default(false)->after('images');
            echo "Added 'banner_dark' column.\n";
        }
    });
    echo "Done.\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
