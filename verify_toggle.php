<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;

$college = 'engineering';
echo "Setting initially for engineering: " . Setting::get("facebook_integration_enabled_{$college}", '1') . "\n";

// Set to 0 and verify
Setting::set("facebook_integration_enabled_{$college}", '0');
echo "Setting after set 0: " . Setting::get("facebook_integration_enabled_{$college}", '1') . "\n";

// Revert
Setting::set("facebook_integration_enabled_{$college}", '1');
echo "Setting after set 1: " . Setting::get("facebook_integration_enabled_{$college}", '1') . "\n";
