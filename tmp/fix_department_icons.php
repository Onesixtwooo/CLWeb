<?php
$file = 'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\show-department.blade.php';
if (file_exists($file)) {
    $content = file_get_contents($file);
    
    // Replace missing FontAwesome edit icons with Bootstrap Icons
    $content = str_replace(
        '<i class="fas fa-edit me-1"></i>',
        '<i class="bi bi-pencil-square me-1"></i>',
        $content
    );

    file_put_contents($file, $content);
    echo "Updated edit icons in show-department.blade.php\n";
} else {
    echo "File not found: $file\n";
}
