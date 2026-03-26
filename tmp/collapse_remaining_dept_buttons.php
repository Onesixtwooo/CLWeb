<?php
$file = 'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\show-department.blade.php';
if (!file_exists($file)) {
    die("File not found: $file\n");
}
$content = file_get_contents($file);

// 1. Collapse "Add Alumnus"
$content = str_replace(
    'class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">',
    'class="btn btn-primary btn-sm" title="Add Alumnus"><i class="bi bi-mortarboard"></i> <span class="d-none d-md-inline">',
    $content
);
// Fix tail for "Add Alumnus"
$content = str_replace(
    '+ Add Alumnus</a>',
    'Add Alumnus</span></a>',
    $content
);

// 2. Collapse "Alumni Roster"
$content = str_replace(
    'class="btn btn-admin-primary btn-sm btn-action">',
    'class="btn btn-admin-primary btn-sm btn-action" title="Alumni Roster"><i class="bi bi-journal-text"></i> <span class="d-none d-md-inline">',
    $content
);
// Fix tail for "Alumni Roster"
$content = str_replace(
    'Alumni Roster</a>',
    'Alumni Roster</span></a>',
    $content
);

// 3. Collapse "Back to Departments"
$content = str_replace(
    'class="btn btn-outline-secondary btn-sm mb-3" title="Back to Departments">',
    'class="btn btn-outline-secondary btn-sm mb-3" title="Back to Departments"><i class="bi bi-arrow-left"></i> <span class="d-none d-md-inline">',
    $content
);
// Fix tail for "Back to Departments"
$content = str_replace(
    'Back to Departments</a>',
    'Back to Departments</span></a>',
    $content
);

// 4. Collapse "Edit Department Info"
$content = str_replace(
    'class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editDepartmentInfoModal" title="Edit Department Info">',
    'class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editDepartmentInfoModal" title="Edit Department Info"><i class="bi bi-pencil-square"></i> <span class="d-none d-md-inline">',
    $content
);
// Fix tail for "Edit Department Info"
$content = str_replace(
    'Edit Info</button>',
    'Edit Info</span></button>',
    $content
);

file_put_contents($file, $content);
echo "Successfully updated remaining department buttons\n";
