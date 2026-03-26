<?php
$file = 'd:\\htdocs\\CLSU\\resources\\views\\admin\\organizations\\show.blade.php';
if (!file_exists($file)) {
    die("File not found\n");
}
$content = file_get_contents($file);

$insert = '<script>
    window.livePreviewUrl = "{{ route(\'college.organization.show\', [\'college\' => $collegeSlug, \'organization\' => $organization]) }}";
    @if (request()->has(\'section\'))
        window.livePreviewUrl += "{{ request()->query(\'section\') }}" ? "?section={{ request()->query(\'section\') }}" : "";
    @endif
    @if (request()->has(\'album\'))
        window.livePreviewUrl += (window.livePreviewUrl.includes(\'?\') ? \'&\' : \'?\') + "album={{ request()->query(\'album\') }}";
    @endif
';

// Make sure we only replace it once to avoid duplication if run multiple times
if (strpos($content, 'window.livePreviewUrl') === false) {
    $content = str_replace('<script>', $insert, $content);
    file_put_contents($file, $content);
    echo "Successfully inserted livePreviewUrl\n";
} else {
    echo "livePreviewUrl already exists in file\n";
}
