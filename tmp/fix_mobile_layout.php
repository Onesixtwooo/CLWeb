<?php
$files = [
    'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\show.blade.php',
    'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\show-department.blade.php',
    'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\show-institute.blade.php'
];

$mediaQuery = "
    /* Responsive Adjustments for Mobile View */
    @media (max-width: 991.98px) {
        .colleges-layout {
            flex-direction: column !important;
            gap: 1.5rem !important;
        }
        .colleges-section-list {
            width: 100% !important;
            margin-right: 0 !important;
            margin-bottom: 1.5rem !important;
        }
        .colleges-header-bar {
            flex-direction: column !important;
            align-items: flex-start !important;
            padding: 1rem !important;
            margin: -1rem -1.25rem 1.5rem -1.25rem !important;
        }
        .colleges-header-actions {
            margin-left: 0 !important;
            width: 100% !important;
            flex-wrap: wrap !important;
            gap: 0.5rem !important;
            margin-top: 0.5rem !important;
        }
        .colleges-breadcrumb {
            width: 100% !important;
            padding: 0.5rem 0 !important;
            display: block !important;
        }
    }
";

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "File not found: $file\n";
        continue;
    }
    $content = file_get_contents($file);
    if (strpos($content, '@media (max-width: 991.98px)') !== false) {
        echo "Media query already exists in " . basename($file) . "\n";
        continue;
    }
    
    // Find the last </style>
    $pos = strrpos($content, '</style>');
    if ($pos !== false) {
        $content = substr_replace($content, $mediaQuery . "\n", $pos, 0);
        file_put_contents($file, $content);
        echo "Updated " . basename($file) . "\n";
    } else {
        echo "Could not find </style> in " . basename($file) . "\n";
    }
}
