<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FacebookConfig;

$count = FacebookConfig::count();
FacebookConfig::truncate();
echo "Truncated {$count} existing configs on template table diagnostics.\n";
