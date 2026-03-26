<?php
$file = 'd:/htdocs/CLSU/resources/views/includes/college-header.blade.php';
$lines = file($file);
$line85 = $lines[84]; // 0-indexed
$len = strlen($line85);
$output = "";
for ($i = 0; $i < $len; $i++) {
    $char = $line85[$i];
    $hex = bin2hex($char);
    $output .= "$i: [$char] -> 0x$hex\n";
}
file_put_contents('d:/htdocs/CLSU/line85_dump.txt', $output);
echo "Dump complete\n";
?>
