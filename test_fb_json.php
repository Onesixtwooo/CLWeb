<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FacebookConfig;

$first = FacebookConfig::first();
if ($first) {
    echo json_encode($first->getAttributes(), JSON_PRETTY_PRINT) . "\n";
} else {
    echo "No FacebookConfig row found.\n";
}
