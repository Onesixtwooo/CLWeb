<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Faculty;
use App\Models\CollegeOrganization;

$fac = Faculty::first();
if ($fac) {
    echo "FACULTY:\n";
    foreach ($fac->getAttributes() as $k => $v) {
        if (!is_string($v) || strlen($v) < 100) {
            echo "$k => $v\n";
        } else {
            echo "$k => [long string]\n";
        }
    }
} else {
    echo "No faculty found!\n";
}

$org = CollegeOrganization::whereNotNull('department_id')->first();
if ($org && $org->department) {
    echo "\nORG DEPARTMENT details:\n";
    print_r($org->department->getAttributes());
}
