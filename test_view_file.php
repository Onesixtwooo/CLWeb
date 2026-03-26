<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$compiler = app('blade.compiler');
$file = 'd:/htdocs/CLSU/resources/views/college-blade.blade.php';

try {
    $compiler->compile($file);
    $compiledPath = $compiler->getCompiledPath($file);
    echo "Compiled Path: " . $compiledPath . "\n";
    $output = [];
    $rv = 0;
    exec('php -l ' . escapeshellarg($compiledPath), $output, $rv);
    echo implode("\n", $output);
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
