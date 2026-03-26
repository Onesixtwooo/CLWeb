<?php
$path = 'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\edit-department-section.blade.php';
$content = file_get_contents($path);

$parts = preg_split('/(@elseif \(\$isAwardsEdit\))/s', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

if (count($parts) >= 3) {
    file_put_contents('d:\\htdocs\\CLSU\\part2.txt', $parts[2]);
    echo "Part 2 dumped\n";
} else {
    echo "Could not find part 2\n";
}
