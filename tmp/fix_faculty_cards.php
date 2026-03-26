<?php
$files = [
    'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\show.blade.php',
    'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\show-department.blade.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "File not found: $file\n";
        continue;
    }
    $content = file_get_contents($file);
    
    // 1. Change card column sizing to 2 columns on mobile (col-6)
    $content = str_replace(
        'class="col-12 col-sm-6 col-lg-4 col-xl-3"',
        'class="col-6 col-sm-6 col-lg-4 col-xl-3"',
        $content
    );
    
    // 2. Remove flex-fill from Edit button and inject Pencil Icon
    $content = str_replace(
        'class="btn btn-sm btn-outline-light flex-fill" style="font-size: 0.75rem;">Edit</a>',
        'class="btn btn-sm btn-outline-light" style="font-size: 0.75rem;" title="Edit"><i class="bi bi-pencil"></i></a>',
        $content
    );
    
    // 3. Remove flex-fill from Delete Form container
    $content = str_replace(
        'class="flex-fill" onsubmit="return confirm(',
        ' class="" onsubmit="return confirm(', // remove flex-fill
        $content
    );
    
    // 4. Remove w-100 from Delete button and inject Trash Icon
    $content = str_replace(
        'class="btn btn-sm btn-outline-danger w-100" style="font-size: 0.75rem;">Delete</button>',
        'class="btn btn-sm btn-outline-danger" style="font-size: 0.75rem;" title="Delete"><i class="bi bi-trash"></i></button>',
        $content
    );

    file_put_contents($file, $content);
    echo "Updated $file\n";
}
echo "Successfully updated faculty cards and buttons\n";
