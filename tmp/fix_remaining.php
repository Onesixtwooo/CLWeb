<?php
$files = [
    'd:\\htdocs\\CLSU\\resources\\views\\admin\\membership\\create.blade.php',
    'd:\\htdocs\\CLSU\\resources\\views\\admin\\membership\\edit.blade.php',
    'd:\\htdocs\\CLSU\\resources\\views\\admin\\organizations\\create.blade.php',
    'd:\\htdocs\\CLSU\\resources\\views\\admin\\organizations\\edit.blade.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "File not found: $file\n";
        continue;
    }
    $content = file_get_contents($file);

    // 1. Remove hidden input
    $content = preg_replace('/<input type="hidden" name="media_image" id="media_image_input">/s', '', $content);

    // 2. Remove button and "or upload directly" text inside d-flex
    $content = preg_replace('/<button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="openMediaLibrary\(\)">.*?<\/button>\s*<div class="text-muted align-self-center mx-2">or upload directly:<\/div>/s', '', $content);

    // 3. Remove include
    $content = preg_replace('/@include\(\'includes\.media-modal\'\)/s', '', $content);

    // 4. Remove openMediaLibrary function using balanced braces regex
    $content = preg_replace('/function\s+openMediaLibrary\s*\(\s*\)\s*\{(?:[^{}]|(?R))*\}/s', '', $content);

    file_put_contents($file, $content);
    echo "Fixed $file\n";
}
echo "All done\n";
