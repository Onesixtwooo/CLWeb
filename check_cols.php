<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$cols = Illuminate\Support\Facades\Schema::getColumnListing('college_departments');
$alumniCols = array_filter($cols, function($c) { return strpos($c, 'alumni') !== false; });
print_r($alumniCols);
