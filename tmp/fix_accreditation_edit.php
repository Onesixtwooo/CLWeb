<?php
$file = 'd:\\htdocs\\CLSU\\resources\\views\\admin\\accreditation\\edit.blade.php';
if (!file_exists($file)) die("File not found");
$content = file_get_contents($file);

// 1. Remove Styles
$content = preg_replace('/@push\(\'styles\'\)\s*<style>.*?── Media Picker Modal ──.*?<\/style>\s*@endpush/s', '', $content);

// 2. Remove Preview & Hidden Input
$content = preg_replace('/\{\{-- Media library picked preview --\}\}\s*<div id="mpPickedPreview".*?<\/div>\s*<input type="hidden" name="media_image" id="mpPickedPath" value="">/s', '', $content);

// 3. Remove Button & Text
$content = preg_replace('/<div class="d-flex gap-2">\s*<button type="button" class="btn btn-outline-secondary btn-sm" id="openMediaPicker">.*?<\/button>\s*<\/div>\s*<span class="text-muted small">or upload directly:<\/span>/s', '', $content);

file_put_contents($file, $content);
echo "Done\n";
