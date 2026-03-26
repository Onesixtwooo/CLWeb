<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Setting;

$rows = Setting::where('key', 'like', 'facebook_%')->get();
foreach ($rows as $r) {
    echo $r->key . ': ' . $r->value . PHP_EOL;
}
