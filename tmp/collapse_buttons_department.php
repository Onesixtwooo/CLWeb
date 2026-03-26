<?php
$fileDept = 'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\show-department.blade.php';
$fileInst = 'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\show-institute.blade.php';

if (file_exists($fileDept)) {
    $content = file_get_contents($fileDept);
    $content = str_replace(
        '<i class="fas fa-edit me-1"></i> Edit section details',
        '<i class="fas fa-edit me-1"></i> <span class="d-none d-md-inline">Edit section details</span>',
        $content
    );
    $content = str_replace(
        '<i class="fas fa-edit me-1"></i> Edit',
        '<i class="fas fa-edit me-1"></i> <span class="d-none d-md-inline">Edit</span>',
        $content
    );
    file_put_contents($fileDept, $content);
    echo "Updated show-department.blade.php\n";
}

if (file_exists($fileInst)) {
    $content = file_get_contents($fileInst);
    
    // Replace "Edit Section"
    $content = str_replace(
        '<i class="bi bi-pencil me-1"></i> Edit Section',
        '<i class="bi bi-pencil me-1"></i> <span class="d-none d-md-inline">Edit Section</span>',
        $content
    );
    
    // Manage Banners (Line 253)
    $content = str_replace(
        'Manage Banners</a>',
        '<i class="bi bi-images"></i> <span class="d-none d-md-inline">Manage Banners</span></a>',
        $content
    );

    file_put_contents($fileInst, $content);
    echo "Updated show-institute.blade.php\n";
}
