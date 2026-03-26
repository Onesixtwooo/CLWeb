<?php
$files = [
    'd:\\htdocs\\CLSU\\resources\\views\\admin\\extensions\\form.blade.php',
    'd:\\htdocs\\CLSU\\resources\\views\\admin\\trainings\\form.blade.php',
    'd:\\htdocs\\CLSU\\resources\\views\\admin\\accreditation\\create.blade.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);

    // Robust regex to find any two adjacent file inputs with the same name attribute
    // and remove the second one.
    $content = preg_replace('/(<input type="file" name="([^"]+)"[^>]*>)\s*<input type="file" name="\\2"[^>]*>/s', '$1', $content);

    file_put_contents($file, $content);
    echo "Fixed $file\n";
}
echo "Done\n";
