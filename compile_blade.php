<?php
require 'd:/htdocs/CLSU/vendor/autoload.php';
$app = require_once 'd:/htdocs/CLSU/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$viewFile = 'd:/htdocs/CLSU/resources/views/includes/college-header.blade.php';
$view = file_get_contents($viewFile);
$compiler = app('blade.compiler');

try {
    $compiled = $compiler->compileString($view);
    file_put_contents('d:/htdocs/CLSU/compiled_header.php', $compiled);
    echo "Compilation complete. Output saved to compiled_header.php\n";
    
    // Test for syntax errors
    exec('php -l d:\\htdocs\\CLSU\\compiled_header.php', $output, $returnVar);
    echo implode("\n", $output) . "\n";
    echo "Exit code: $returnVar\n";
} catch (Exception $e) {
    echo "Error compiling: " . $e->getMessage() . "\n";
}
?>
