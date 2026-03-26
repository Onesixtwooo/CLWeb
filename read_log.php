<?php
$log = file_get_contents('d:/htdocs/CLSU/storage/logs/laravel.log');
// Find the last occurrence of "About images request data"
$pos = strrpos($log, "About images request data");
if ($pos !== false) {
    echo substr($log, $pos, 4000);
} else {
    echo "NOT FOUND";
}
