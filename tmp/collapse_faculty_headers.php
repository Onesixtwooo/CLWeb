<?php
$file = 'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\show.blade.php';
if (file_exists($file)) {
    $content = file_get_contents($file);
    $content = str_replace(
        'Add faculty member</a>',
        '<i class="bi bi-person-plus"></i> <span class="d-none d-md-inline">Add faculty member</span></a>',
        $content
    );
    $content = str_replace(
        'Add Institute Staff',
        '<i class="bi bi-person-badge"></i> <span class="d-none d-md-inline">Add Institute Staff</span>',
        $content
    );
    file_put_contents($file, $content);
}

$fileDept = 'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\show-department.blade.php';
if (file_exists($fileDept)) {
    $contentDept = file_get_contents($fileDept);
    $contentDept = str_replace(
        'Add Faculty Member</a>',
        '<i class="bi bi-person-plus"></i> <span class="d-none d-md-inline">Add Faculty Member</span></a>',
        $contentDept
    );
    file_put_contents($fileDept, $contentDept);
}

echo "Successfully collapsed faculty header buttons\n";
