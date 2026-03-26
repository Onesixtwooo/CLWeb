<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Blade;

$file = 'd:/htdocs/CLSU/resources/views/college-blade.blade.php';
$content = file_get_contents($file);

try {
    $compiled = Blade::compileString($content);
    // Find where the error is
    // Let's print out the compiled output or save it to a file
    file_put_contents('compiled_view.php', $compiled);
    echo "Compiled successfully, checking with php -l\n";
    $output = [];
    $rv = 0;
    exec('php -l compiled_view.php', $output, $rv);
    echo implode("\n", $output);
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
