<?php
$file = 'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\show.blade.php';
if (!file_exists($file)) {
    die("File not found: $file\n");
}
$content = file_get_contents($file);

$count = 0;
$content = preg_replace_callback(
    '/<table class="table table-hover align-middle mb-0"/',
    function ($matches) use (&$count) {
        $count++;
        if ($count === 1) return '<table class="table table-hover align-middle mb-0 table-accreditation"';
        if ($count === 2) return '<table class="table table-hover align-middle mb-0 table-memberships"';
        if ($count === 3) return '<table class="table table-hover align-middle mb-0 table-organizations"';
        return $matches[0];
    },
    $content
);

if ($count < 3) {
    echo "Warning: Only found $count tables. Replacements might be partial.\n";
}

$css = "
    /* ── Responsive Tables to Cards for Mobile ── */
    @media (max-width: 991.98px) {
        .colleges-detail {
            padding: 1.25rem !important; /* Slightly reduce padding on mobile detail container */
        }
        .table-responsive {
            border: none !important;
            overflow-x: visible !important;
            background: none !important;
            box-shadow: none !important;
        }
        .table {
            display: block !important;
            width: 100% !important;
            border: none !important;
        }
        .table thead {
            display: none !important; /* Hide header rows */
        }
        .table tbody {
            display: block !important;
            width: 100% !important;
        }
        .table tbody tr {
            display: flex !important;
            flex-direction: column !important;
            background: var(--admin-surface) !important;
            border: 1px solid var(--admin-border) !important;
            border-radius: 12px !important;
            margin-bottom: 1rem !important;
            padding: 1.25rem !important;
            box-shadow: var(--admin-shadow) !important;
            width: 100% !important;
        }
        .table tbody td {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 100% !important;
            padding: 0.625rem 0 !important;
            border: none !important;
            border-bottom: 1px solid rgba(0,0,0,0.03) !important;
            text-align: right !important;
        }
        .table tbody td:last-child {
            border-bottom: none !important;
            padding-top: 1rem !important;
            justify-content: flex-start !important;
        }
        .table tbody td::before {
            font-weight: 600 !important;
            color: var(--admin-text-muted) !important;
            font-size: 0.8125rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            text-align: left !important;
            margin-right: 1rem !important;
        }

        /* Title Card Styling for first cell */
        .table tbody td:nth-of-type(1) {
            justify-content: flex-start !important;
            font-size: 1.1rem !important;
            font-weight: 700 !important;
            border-bottom: 1px solid var(--admin-border) !important;
            padding-bottom: 0.75rem !important;
            margin-bottom: 0.25rem !important;
        }
        
        .table-accreditation td:nth-of-type(1)::before { content: ''; }
        .table-accreditation td:nth-of-type(2)::before { content: 'Level'; }
        .table-accreditation td:nth-of-type(3)::before { content: 'Scope'; }
        .table-accreditation td:nth-of-type(4)::before { content: 'Valid Until'; }
        .table-accreditation td:nth-of-type(5)::before { content: 'Status'; }
        .table-accreditation td:nth-of-type(6)::before { content: ''; }

        .table-memberships td:nth-of-type(1)::before { content: ''; }
        .table-memberships td:nth-of-type(2)::before { content: 'Type'; }
        .table-memberships td:nth-of-type(3)::before { content: 'Scope'; }
        .table-memberships td:nth-of-type(4)::before { content: 'Valid Until'; }
        .table-memberships td:nth-of-type(5)::before { content: 'Status'; }
        .table-memberships td:nth-of-type(6)::before { content: ''; }

        .table-organizations td:nth-of-type(1)::before { content: ''; }
        .table-organizations td:nth-of-type(2)::before { content: 'Scope'; }
        .table-organizations td:nth-of-type(3)::before { content: 'Adviser'; }
        .table-organizations td:nth-of-type(4)::before { content: 'Status'; }
        .table-organizations td:nth-of-type(5)::before { content: ''; }

        .table tbody td .d-flex.justify-content-end,
        .table tbody td .d-flex.justify-content-start {
            justify-content: flex-start !important;
            width: 100% !important;
            flex-wrap: wrap !important;
        }
    }
";

if (strpos($content, '/* ── Responsive Tables to Cards for Mobile ── */') === false) {
    // Find the last </style>
    $pos = strrpos($content, '</style>');
    if ($pos !== false) {
        $content = substr_replace($content, $css . "\n", $pos, 0);
    } else {
        echo "Could not find </style> in file.\n";
    }
} else {
    echo "CSS block already exists. Skipping append.\n";
}

file_put_contents($file, $content);
echo "Successfully updated tables and CSS in show.blade.php\n";
