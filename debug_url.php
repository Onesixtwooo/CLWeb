<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;

try {
    $disk = Storage::disk('google');
    echo "Disk class: " . get_class($disk) . "\n";
    
    $refl = new ReflectionClass($disk);
    $adapterProp = $refl->getProperty('adapter');
    $adapterProp->setAccessible(true);
    $adapter = $adapterProp->getValue($disk);
    
    echo "Adapter class: " . get_class($adapter) . "\n";
    
    $methods = get_class_methods($adapter);
    echo "Is 'url' in methods? " . (in_array('url', $methods) ? 'YES' : 'NO') . "\n";
    echo "Is 'getUrl' in methods? " . (in_array('getUrl', $methods) ? 'YES' : 'NO') . "\n";
    
    if (in_array('url', $methods)) {
        echo "Attempting to call \$adapter->url('test'): " . $adapter->url('test') . "\n";
    }

    echo "Attempting Storage::disk('google')->url('test_id')...\n";
    echo "URL: " . $disk->url('test_id') . "\n";

} catch (Exception $e) {
    echo "Caught: " . get_class($e) . ": " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . " in " . $e->getFile() . "\n";
}
