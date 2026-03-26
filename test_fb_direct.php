<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FacebookConfig;

// Wipes table first to isolate
FacebookConfig::truncate();

$row = FacebookConfig::create([
    'entity_type' => 'college',
    'entity_id' => 'engineering',
    'page_id' => '1061969980324250',
    'access_token' => 'EAAb...',
    'page_name' => 'College of Engineering'
]);

$check = FacebookConfig::find($row->id);
echo "Eloquent Created ID: " . $row->id . "\n";
echo "DB Column entity_id value: '" . $check->getAttributes()['entity_id'] . "'\n";
