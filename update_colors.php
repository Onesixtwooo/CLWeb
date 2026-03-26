<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\Setting;

$app = require 'bootstrap/app.php';
$app->makeWith(\Illuminate\Contracts\Console\Kernel::class)
    ->bootstrap();

// Update to green colors (defaults from SettingsController)
Setting::set('admin_header_color_engineering', '#0d6e42');  // Dark green (default)
Setting::set('admin_sidebar_color_engineering', '#009639');   // Green (default)

echo "✓ Engineering header color updated to: #0d6e42 (dark green)\n";
echo "✓ Engineering sidebar color updated to: #009639 (green)\n";
echo "\nThe header should now display in green when you reload the page.\n";
