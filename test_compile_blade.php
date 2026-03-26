<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Blade;

$viewPath = 'd:\\htdocs\\CLSU\\resources\\views\\includes\\college-header.blade.php';
$content = file_get_contents($viewPath);
$compiled = Blade::compileString($content);
file_put_contents('d:\\htdocs\\CLSU\\compiled_view.php', $compiled);
echo "Compiled output written to compiled_view.php\n";
