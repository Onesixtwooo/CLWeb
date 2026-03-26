<?php
$logPath = 'd:\\htdocs\\CLSU\\storage\\logs\\laravel.log';
$copyPath = 'd:\\htdocs\\CLSU\\laravel_copy.log';
if (file_exists($logPath)) {
    copy($logPath, $copyPath);
    $lines = file($copyPath);
    if ($lines) {
        $lastLines = array_slice($lines, -20);
        echo implode("", $lastLines);
    } else {
        echo "Could not read lines of copy\n";
    }
} else {
    echo "Log file does not exist\n";
}
