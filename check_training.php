<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use App\Models\CollegeSection;
use App\Models\CollegeTraining;

$college = 'agriculture'; // Assuming from screenshot

echo "Checking Training for $college:\n";

$section = CollegeSection::where('college_slug', $college)->where('section_slug', 'training')->first();
if ($section) {
    echo "Section Found:\n";
    echo "- is_visible: " . ($section->is_visible ? 'True' : 'False') . "\n";
    echo "- is_draft: " . ($section->is_draft ? 'True' : 'False') . "\n";
    echo "- publish_at: " . ($section->publish_at ? $section->publish_at->toDateTimeString() : 'Null') . "\n";
} else {
    echo "Section NOT Found\n";
}

$items = CollegeTraining::where('college_slug', $college)->get();
echo "Items count: " . $items->count() . "\n";
foreach ($items as $item) {
    echo "- Item: {$item->title}, is_visible: " . ($item->is_visible ? 'True' : 'False') . "\n";
}
