<?php
$directory = 'd:/htdocs/CLSU/resources';

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$count = 0;
foreach ($iterator as $file) {
    if ($file->isFile() && (pathinfo($file->getFilename(), PATHINFO_EXTENSION) === 'php' || pathinfo($file->getFilename(), PATHINFO_EXTENSION) === 'css')) {
        $filepath = $file->getRealPath();
        $content = file_get_contents($filepath);
        if (strpos($content, 'engineering-page') !== false) {
            $new_content = preg_replace('/\bengineering-page\b/', 'college-page', $content);
            file_put_contents($filepath, $new_content);
            echo "Updated: $filepath\n";
            $count++;
        }
    }
}
echo "Total updated: $count\n";
