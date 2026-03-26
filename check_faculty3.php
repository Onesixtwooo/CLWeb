<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Faculty;
use App\Models\CollegeOrganization;

$fac = Faculty::whereNotNull('department')->first();
if ($fac) {
    echo "Faculty ID: " . $fac->id . "\n";
    echo "Faculty Name: " . $fac->name . "\n";
    echo "Faculty Department: " . $fac->department . "\n";
} else {
    echo "No faculty found with department!\n";
}

$org = CollegeOrganization::whereNotNull('department_id')->first();
if ($org && $org->department) {
    echo "Org Name: " . $org->name . "\n";
    echo "Org Department ID: " . $org->department_id . "\n";
    echo "Org Department Name: " . $org->department->name . "\n";
}
echo "Done.\n";
