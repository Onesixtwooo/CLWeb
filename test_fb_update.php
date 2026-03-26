<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FacebookConfig;

$row = FacebookConfig::first();
if (!$row) {
    die("No configs found.\n");
}

echo "Before Update Token: " . substr($row->access_token, 0, 10) . "...\n";

$validated = [
    'page_id' => '1061969980324250',
    'access_token' => 'EAAbGL_TEST_UPDATE',
    'fetch_limit' => 5,
    'article_category' => null,
    'article_author' => 'College of Engineering',
    'entity_type' => 'college',
    'entity_id' => 'engineering',
    'page_name' => 'College of Engineering',
    'is_active' => true
];

$success = $row->update($validated);
echo "Eloquent Update Success: " . ($success ? 'TRUE' : 'FALSE') . "\n";

$check = FacebookConfig::find($row->id);
echo "After Update Token: " . substr($check->access_token, 0, 10) . "...\n";
