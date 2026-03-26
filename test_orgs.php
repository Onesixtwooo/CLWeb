<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$orgs = \App\Models\CollegeOrganization::all();
foreach ($orgs as $org) {
    echo $org->id . " - Name: " . $org->name . "\n";
    echo "  Acronym: " . $org->acronym . "\n";
    echo "  Department ID: " . $org->department_id . "\n";
    echo "  Logo: " . $org->logo . "\n";
    echo "  Is Visible: " . ($org->is_visible ? 'Yes' : 'No') . "\n";
    echo "---------------------------\n";
}
