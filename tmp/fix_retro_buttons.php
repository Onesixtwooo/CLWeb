<?php
$file = 'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\show.blade.php';
if (file_exists($file)) {
    $content = file_get_contents($file);
    
    // 1. + Add Item
    $content = str_replace(
        '+ Add Item</a>',
        '<i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add Item</span></a>',
        $content
    );

    // 2. Create your first item
    $content = str_replace(
        'Create your first item</a>',
        '<i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Create your first item</span></a>',
        $content
    );

    // 3. Edit video
    $content = str_replace(
        'Edit video</a>',
        '<i class="bi bi-pencil-square"></i> <span class="d-none d-md-inline">Edit video</span></a>',
        $content
    );

    file_put_contents($file, $content);
    echo "Updated retro and video buttons in show.blade.php\n";
} else {
    echo "File not found: $file\n";
}
