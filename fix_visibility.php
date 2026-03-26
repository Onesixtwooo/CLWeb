<?php
$file = 'd:/htdocs/CLSU/resources/views/college-blade.blade.php';
$content = file_get_contents($file);

$target1 = '@if($extensionSection && $extensionSection->is_visible && $extensions->isNotEmpty())';
$replace1 = '@if($extensions->isNotEmpty() && (!isset($extensionSection) || $extensionSection->is_visible))';

$target2 = '@if($trainingSection && $trainingSection->is_visible && $trainings->isNotEmpty())';
$replace2 = '@if($trainings->isNotEmpty() && (!isset($trainingSection) || $trainingSection->is_visible))';

$content = str_replace($target1, $replace1, $content);
$content = str_replace($target2, $replace2, $content);

file_put_contents($file, $content);
echo "Replacement Done\n";
