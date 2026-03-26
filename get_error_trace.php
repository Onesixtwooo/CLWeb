<?php
$logFile = 'd:/htdocs/CLSU/storage/logs/laravel.log';
if (!file_exists($logFile)) {
    echo "Log file not found\n";
    exit;
}

$content = file_get_contents($logFile);
$pattern = '/unexpected token \'(as|as)\'/i'; // Search for 'as'

// Find all matches
if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
    echo "Found " . count($matches[0]) . " matches\n";
    // Get the last match
    $lastMatch = end($matches[0]);
    $offset = $lastMatch[1];
    
    // Get 1000 characters before and after
    $start = max(0, $offset - 2000);
    $length = 4000;
    
    echo "--- Context around last match ---\n";
    echo substr($content, $start, $length);
    echo "\n--------------------------------\n";
} else {
    echo "No matching error found in log\n";
}
?>
