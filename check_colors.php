<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\Setting;

$app = require 'bootstrap/app.php';
$app->makeWith(\Illuminate\Contracts\Console\Kernel::class)
    ->bootstrap();

// Check engineering colors
$headerColor = Setting::get('admin_header_color_engineering', null);
$sidebarColor = Setting::get('admin_sidebar_color_engineering', null);

echo "Engineering Header Color: " . ($headerColor ?: 'NOT SET (will use default: #0d6e42)') . "\n";
echo "Engineering Sidebar Color: " . ($sidebarColor ?: 'NOT SET (will use default: #009639)') . "\n";

// Check all settings with engineering
echo "\n\nAll engineering-related settings:\n";
$settings = \DB::table('settings')->where('key', 'like', '%engineering%')->get();
foreach ($settings as $setting) {
    echo $setting->key . " => " . $setting->value . "\n";
}
