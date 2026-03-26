<?php
$files = [
    'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\edit-section.blade.php',
    'd:\\htdocs\\CLSU\\resources\\views\\admin\\colleges\\edit-department-section.blade.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);

    // 1. Collapse "+ Add Item" or "+ Add X" buttons
    // Matches: <button ... id="add-*-btn" ...>+ Add Something</button>
    // OR <button ... class="...add-*-btn...">+ Add Something</button>
    $content = preg_replace_callback(
        '/(<button[^>]+(?:id|class)="[^"]*add-[^"]*"[^>]*>)\s*\+\s*Add\s+([^<]+)(<\/button>)/i',
        function ($matches) {
            $tagOpen = $matches[1];
            $text = trim($matches[2]);
            $tagClose = $matches[3];
            $titleAttr = ' title="Add ' . htmlspecialchars($text) . '"';
            // Inject title if not already present
            if (strpos($tagOpen, 'title=') === false) {
                $tagOpen = rtrim($tagOpen, '>') . $titleAttr . '>';
            }
            return $tagOpen . '<i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add ' . $text . '</span>' . $tagClose;
        },
        $content
    );

    // 2. Collapse "× Remove" or "&times; Remove" buttons
    $content = preg_replace_callback(
        '/(<button[^>]+class="[^"]*remove-[^"]*"[^>]*>)\s*(?:&times;|×)\s*Remove\s+([^<]*)(<\/button>)/i',
        function ($matches) {
            $tagOpen = $matches[1];
            $extraText = trim($matches[2]);
            $tagClose = $matches[3];
            $titleAttr = ' title="Remove' . ($extraText ? ' ' . htmlspecialchars($extraText) : '') . '"';
            if (strpos($tagOpen, 'title=') === false) {
                $tagOpen = rtrim($tagOpen, '>') . $titleAttr . '>';
            }
            return $tagOpen . '<i class="bi bi-trash"></i> <span class="d-none d-md-inline">Remove' . ($extraText ? ' ' . $extraText : '') . '</span>' . $tagClose;
        },
        $content
    );

    // 3. Fallback for inline generic Remove buttons without label suffix
    $content = preg_replace_callback(
        '/(<button[^>]+class="[^"]*remove-[^"]*"[^>]*>)\s*(?:&times;|×)\s*Remove(<\/button>)/i',
        function ($matches) {
            $tagOpen = $matches[1];
            $tagClose = $matches[2];
            $titleAttr = ' title="Remove"';
            if (strpos($tagOpen, 'title=') === false) {
                $tagOpen = rtrim($tagOpen, '>') . $titleAttr . '>';
            }
            return $tagOpen . '<i class="bi bi-trash"></i> <span class="d-none d-md-inline">Remove</span>' . $tagClose;
        },
        $content
    );

    // 4. Collapse "+ Add Custom Link" or static "+ Add item" text buttons
    $content = preg_replace_callback(
        '/(<button[^>]+(?:id|class)="[^"]*add-[^"]*"[^>]*>)\s*\+\s*Add\s+([^<]+)(<\/button>)/i', // redundant maybe, check variations
        function ($matches) { return $matches[0]; }, // handled
        $content
    );

    file_put_contents($file, $content);
    echo "Updated buttons in " . basename($file) . "\n";
}
