<?php

$lines = file('d:/htdocs/CLSU/resources/views/college-blade.blade.php');
$departments = array_slice($lines, 296, 56);
$about = array_slice($lines, 353, 143);
$before = array_slice($lines, 0, 296);
$after = array_slice($lines, 496);

$newLines = array_merge($before, $about, ["\n"], $departments, $after);
file_put_contents('d:/htdocs/CLSU/resources/views/college-blade.blade.php', implode("", $newLines));
echo "Done";
