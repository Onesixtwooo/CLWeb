<?php
$file = 'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\show-department.blade.php';
if (!file_exists($file)) {
    die("File not found: $file\n");
}
$content = file_get_contents($file);

// 1. Swap broken FontAwesome edit icons with Bootstrap Icons (as planned)
$content = str_replace(
    '<i class="fas fa-edit me-1"></i>',
    '<i class="bi bi-pencil-square me-1"></i>',
    $content
);

// 2. Collapse "Add Membership"
$content = str_replace(
    'class="btn btn-admin-primary btn-sm rounded-pill px-3">Add Membership</a>',
    'class="btn btn-admin-primary btn-sm" title="Add Membership"><i class="bi bi-person-check"></i> <span class="d-none d-md-inline">Add Membership</span></a>',
    $content
);

// 3. Collapse "Add Organization"
$content = str_replace(
    'class="btn btn-admin-primary btn-sm rounded-pill px-3">Add Organization</a>',
    'class="btn btn-admin-primary btn-sm" title="Add Organization"><i class="bi bi-people"></i> <span class="d-none d-md-inline">Add Organization</span></a>',
    $content
);

// 4. Collapse "Add Partner" (Linkages)
$content = str_replace(
    'class="btn btn-admin-primary btn-sm">Add Partner</a>',
    'class="btn btn-admin-primary btn-sm" title="Add Partner"><i class="bi bi-handshake"></i> <span class="d-none d-md-inline">Add Partner</span></a>',
    $content
);

// 5. Collapse "Add Facility"
$content = str_replace(
    'class="btn btn-admin-primary btn-sm">Add Facility</a>',
    'class="btn btn-admin-primary btn-sm" title="Add Facility"><i class="bi bi-building-plus"></i> <span class="d-none d-md-inline">Add Facility</span></a>',
    $content
);

// 6. Collapse "Edit banner"
$content = str_replace(
    'class="btn btn-outline-secondary btn-sm">Edit banner</a>',
    'class="btn btn-outline-secondary btn-sm" title="Edit banner"><i class="bi bi-image"></i> <span class="d-none d-md-inline">Edit banner</span></a>',
    $content
);

// 7. Collapse "Edit card image"
$content = str_replace(
    'class="btn btn-outline-secondary btn-sm">Edit card image</a>',
    'class="btn btn-outline-secondary btn-sm" title="Edit card image"><i class="bi bi-image"></i> <span class="d-none d-md-inline">Edit card image</span></a>',
    $content
);

// 8. Collapse Add/Outcomes
$content = str_replace(
    'class="btn btn-admin-primary btn-sm">Add</a>',
    'class="btn btn-admin-primary btn-sm" title="Add"><i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add</span></a>',
    $content
);
$content = str_replace(
    'class="btn btn-outline-secondary btn-sm">Edit outcomes</a>',
    'class="btn btn-outline-secondary btn-sm" title="Edit Outcomes"><i class="bi bi-list-check"></i> <span class="d-none d-md-inline">Edit outcomes</span></a>',
    $content
);

file_put_contents($file, $content);
echo "Successfully updated all department buttons and icons\n";
