<?php
$directory = 'd:/htdocs/CLSU/resources/views';

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$count = 0;
foreach ($iterator as $file) {
    if ($file->isFile() && pathinfo($file->getFilename(), PATHINFO_EXTENSION) === 'php') {
        $filepath = $file->getRealPath();
        $content = file_get_contents($filepath);
        if (strpos($content, 'engineering-featured-video') !== false) {
            $new_content = str_replace('engineering-featured-video', 'college-featured-video', $content);
            file_put_contents($filepath, $new_content);
            echo "Updated: $filepath\n";
            $count++;
        }
    }
}
echo "Total updated: $count\n";
