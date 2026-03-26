<?php
$file = 'd:\\htdocs\\CLSU\\resources\\views\\admin\\organizations\\show.blade.php';
if (!file_exists($file)) {
    die("File not found\n");
}
$content = file_get_contents($file);

// 1. Collapse "Back to Albums"
$content = str_replace(
    '<i class="bi bi-arrow-left me-1"></i> Back to Albums',
    '<i class="bi bi-arrow-left"></i> <span class="d-none d-md-inline">Back to Albums</span>',
    $content
);

// 2. Collapse "Batch Upload"
$content = str_replace(
    '<i class="bi bi-images me-1"></i> Batch Upload',
    '<i class="bi bi-images"></i> <span class="d-none d-md-inline">Batch Upload</span>',
    $content
);

// 3. Collapse "Add {{ $itemLabel }}" or "Add Album" or dynamic Add Item
$content = preg_replace(
    '/<i class="bi bi-plus-lg me-1"><\/i>\s*Add\s+([^<]+)/i',
    '<i class="bi bi-plus-lg"></i> <span class="d-none d-md-inline">Add $1</span>',
    $content
);

// 4. Collapse "Add Adviser"
$content = str_replace(
    '<i class="bi bi-person-plus-fill me-1"></i> Add Adviser',
    '<i class="bi bi-person-plus-fill"></i> <span class="d-none d-md-inline">Add Adviser</span>',
    $content
);

// 5. Collapse "Edit section details"
$content = str_replace(
    '<i class="bi bi-pencil-square me-1"></i> Edit section details',
    '<i class="bi bi-pencil-square"></i> <span class="d-none d-md-inline">Edit section details</span>',
    $content
);

file_put_contents($file, $content);
echo "Successfully updated buttons to be responsive in show.blade.php\n";
