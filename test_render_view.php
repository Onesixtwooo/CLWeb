<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\View;

try {
    echo view('includes.college-header', [
        'collegeName' => 'College of Engineering',
        'collegeLogoUrl' => 'https://example.com/logo.png',
        'collegeSlug' => 'engineering',
        'collegeShortName' => 'COE',
        'departments' => collect()
    ])->render();
    echo "\nRendered successfully!\n";
} catch (\Throwable $e) {
    $errorLog = "Exception: " . $e->getMessage() . "\n";
    $errorLog .= "File: " . $e->getFile() . "\n";
    $errorLog .= "Line: " . $e->getLine() . "\n";
    $errorLog .= "Trace: " . $e->getTraceAsString() . "\n";
    file_put_contents('d:\\htdocs\\CLSU\\render_error.log', $errorLog);
    echo "Error written to render_error.log\n";
}
