<?php
$file = 'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\show.blade.php';
if (!file_exists($file)) {
    die("File not found: $file\n");
}
$content = file_get_contents($file);

// 1. Edit section details
$content = str_replace(
    'Edit section details</a>',
    '<i class="bi bi-pencil-square"></i> <span class="d-none d-md-inline">Edit section details</span></a>',
    $content
);

// 2. Add activity buttons (Accreditation, Memberships use 'Record' but Trainings/Extension use 'Activity')
$content = str_replace(
    '+ Add activity</a>',
    '<i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add activity</span></a>',
    $content
);

// 3. Edit extension items
$content = str_replace(
    'Edit extension items</a>',
    '<i class="bi bi-list-task"></i> <span class="d-none d-md-inline">Edit extension items</span></a>',
    $content
);

// 4. Edit activities (Trainings list back link)
$content = str_replace(
    'Edit activities</a>',
    '<i class="bi bi-list-task"></i> <span class="d-none d-md-inline">Edit activities</span></a>',
    $content
);

// 5. Add Accreditation / Membership Record buttons (so they are uniform)
$content = str_replace(
    'Add Accreditation Record</a>',
    '<i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add Accreditation Record</span></a>',
    $content
);
$content = str_replace(
    'Add Membership Record</a>',
    '<i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add Membership Record</span></a>',
    $content
);
$content = str_replace(
    'Add Organization Record</a>',
    '<i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add Organization Record</span></a>',
    $content
);

file_put_contents($file, $content);
echo "Successfully collapsed buttons to icons in show.blade.php\n";
