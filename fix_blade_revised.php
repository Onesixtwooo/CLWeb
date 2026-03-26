<?php
$filePath = 'd:\htdocs\CLSU\resources\views\admin\colleges\edit-department-section.blade.php';
$content = file_get_contents($filePath);

// 1. Remove the broken @if chain structure
// Look for the end of the isEditAlumnusEdit block and the start of isLinkagesEdit
// The specific problematic section is:
//                         @endif
// @elseif ($isLinkagesEdit)

// We use regex to be safe about white spaces and newlines
$p1 = '/(@endif\s*)@elseif\s*\(\s*\\\$isLinkagesEdit\s*\)/';
if (preg_match($p1, $content, $matches)) {
    echo "Found broken Linkages chain. Fixing...\n";
    $content = preg_replace($p1, '@elseif ($isLinkagesEdit)', $content);
}

// 2. Fix remaining labels
$content = str_replace('Testimonials details', 'Alumni section details', $content);
$content = str_replace('Manage Testimonial Roster', 'Manage Alumni Roster', $content);
$content = str_replace('Edit Testimonials Details', 'Edit Alumni Section', $content);

// 3. Fix Success messages in Controller (we can do this in another step if needed, but let's try to find it in the blade if it's there as text)
// The controller is likely to be updated separately.

file_put_contents($filePath, $content);
echo "Done.\n";
