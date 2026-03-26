<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Faculty;
use App\Models\CollegeOrganization;

$fac = Faculty::first();
if ($fac) {
    echo "Faculty Department column content: " . $fac->department . "\n";
    print_r($fac->toArray());
} else {
    echo "No faculty found!\n";
}

$org = CollegeOrganization::first();
if ($org && $org->department) {
    echo "Org Department details: " . get_class($org->department) . "\n";
    print_r($org->department->toArray());
}
