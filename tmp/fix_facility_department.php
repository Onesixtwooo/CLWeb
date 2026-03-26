<?php
$file = 'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\show.blade.php';
if (file_exists($file)) {
    $content = file_get_contents($file);
    
    // 1. Add facility
    $content = str_replace(
        'Add facility</a>',
        '<i class="bi bi-building-plus"></i> <span class="d-none d-md-inline">Add facility</span></a>',
        $content
    );
    
    // 2. Add department
    $content = str_replace(
        'Add department</button>',
        '<i class="bi bi-folder-plus"></i> <span class="d-none d-md-inline">Add department</span></button>',
        $content
    );

    // 3. Departments layout header & row adjustments
    // Replace width: 180px with col-12 col-md-auto
    $content = str_replace(
        '<div class="col-auto" style="width: 180px;">',
        '<div class="col-12 col-md-auto">',
        $content
    );

    // Adjust alignment inside actions container on mobile for row items
    $content = str_replace(
        'justify-content-end">',
        'justify-content-start justify-content-md-end mt-2 mt-md-0">',
        $content
    );

    file_put_contents($file, $content);
    echo "Updated show.blade.php: buttons collapsed and layout adjusted.\n";
} else {
    echo "File not found: $file\n";
}
