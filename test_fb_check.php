<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FacebookConfig;

$configs = FacebookConfig::all();
echo "Total Configs found: " . $configs->count() . "\n";
foreach ($configs as $config) {
    echo "ID: {$config->id} | Type: {$config->entity_type} | Entity: {$config->entity_id} | Name: {$config->page_name} | PageID: {$config->page_id} | Active: " . ($config->is_active ? 'Yes' : 'No') . "\n";
}
