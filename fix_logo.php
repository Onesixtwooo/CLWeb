<?php
$filepath = 'd:/htdocs/CLSU/resources/views/admin/colleges/show.blade.php';
$content = file_get_contents($filepath);

$target = 'src="@if(!empty($content[\'adminLogoPath\']))@php preg_match(\'/[?&]id=([^&]+)/\', $content[\'adminLogoPath\'], $_lm); echo isset($_lm[1]) ? route(\'admin.media.proxy\', [\'fileId\' => $_lm[1]]) : $content[\'adminLogoPath\']; @endphp@endif"';
$replace = 'src="@if(!empty($content[\'adminLogoPath\']))@php preg_match(\'/[?&]id=([^&]+)/\', $content[\'adminLogoPath\'], $_lm); echo isset($_lm[1]) ? route(\'admin.media.proxy\', [\'fileId\' => $_lm[1]]) : asset($content[\'adminLogoPath\']); @endphp@endif"';

if (strpos($content, $target) !== false) {
    $content = str_replace($target, $replace, $content);
    file_put_contents($filepath, $content);
    echo "Fixed";
} else {
    echo "NotFound";
}
