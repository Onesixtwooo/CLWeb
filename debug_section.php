<?php

use App\Models\CollegeSection;

// Load Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$collegeSlug = 'engineering';
$sectionSlug = 'overview';

$output = "";
$output .= "Checking for College: $collegeSlug, Section: $sectionSlug\n";

$section = CollegeSection::where('college_slug', $collegeSlug)
    ->whereIn('section_slug', ['overview', 'Overview'])
    ->latest('updated_at')
    ->first();

if ($section) {
    $output .= "Found section!\n";
    $output .= "ID: " . $section->id . "\n";
    $output .= "Title: " . $section->title . "\n";
    $output .= "Body Length: " . strlen($section->body) . "\n";
    $output .= "Body Preview: " . substr($section->body, 0, 100) . "\n";
} else {
    $output .= "No section found.\n";
}

$allSections = CollegeSection::where('college_slug', $collegeSlug)->get();
$output .= "\nAll sections for $collegeSlug:\n";
foreach ($allSections as $s) {
    $output .= "- Slug: {$s->section_slug}, Title: {$s->title}\n";
}

file_put_contents(__DIR__.'/debug_output.txt', $output);
echo "Debug script completed.\n";
