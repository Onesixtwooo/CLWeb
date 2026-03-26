<?php
$file = 'storage/logs/laravel.log';
if (!file_exists($file)) {
    echo "Log file not found.";
    exit;
}
$f = fopen($file, 'r');
if (!$f) {
    echo "Failed to open log file.";
    exit;
}
fseek($f, -4000, SEEK_END);
$content = fread($f, 4000);
file_put_contents('recent_log.txt', $content);
echo "Extracted log to recent_log.txt";
