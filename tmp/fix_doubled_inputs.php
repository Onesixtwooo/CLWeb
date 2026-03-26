<?php
$files = [
    'd:\\htdocs\\CLSU\\resources\\views\\admin\\extensions\\form.blade.php',
    'd:\\htdocs\\CLSU\\resources\\views\\admin\\trainings\\form.blade.php',
    'd:\\htdocs\\CLSU\\resources\\views\\admin\\accreditation\\create.blade.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);

    // Regex to remove the first of two sequential identical input tags
    // Matches: <input [attributes]> \s* <input [attributes_identical_or_similar]>
    // We can just remove the first one if we find two adjacent file inputs.
    
    // For extensions/trainings:
    $content = preg_replace('/(<input type="file" name="image" class="form-control form-control-sm[^>]+>)\s*\1/s', '$1', $content);

    // For accreditation:
    $content = preg_replace('/(<input type="file" name="logo" id="logo" class="form-control[^>]+>)\s*\1/s', '$1', $content);

    file_put_contents($file, $content);
    echo "Fixed $file\n";
}
echo "Done\n";
