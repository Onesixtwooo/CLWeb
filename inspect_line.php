<?php
$file = 'd:/htdocs/CLSU/resources/views/includes/college-header.blade.php';
$lines = file($file);
$line85 = $lines[84]; // 0-indexed
echo "Line 85: " . bin2hex($line85) . "\n";
echo "Content: " . $line85 . "\n";
?>
