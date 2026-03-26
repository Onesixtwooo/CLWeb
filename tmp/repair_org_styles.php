<?php
$file = 'd:\\htdocs\\CLSU\\resources\\views\\admin\\organizations\\show.blade.php';
if (!file_exists($file)) {
    die("File not found\n");
}
$content = file_get_contents($file);

$newStyle = '    .colleges-header-bar {
        background: var(--admin-surface);
        border-bottom: 1px solid var(--admin-border);
        padding: 0.875rem 1.5rem;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 1rem;
        margin: -2rem -2.25rem 1.5rem -2.25rem;
        padding: 1rem 2.25rem;
    }
    .colleges-header-title {
        font-weight: 700;
        font-size: 1.25rem;
        letter-spacing: -0.02em;
        color: var(--admin-text);
        margin: 0;
    }
    .colleges-header-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-left: auto;
    }
    .colleges-layout {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        min-height: 480px;
    }
    .colleges-section-list {
        width: 100%;
        flex-shrink: 0;
        background: var(--admin-surface);
        border: 1px solid var(--admin-border);
        border-radius: var(--admin-radius-lg);
        overflow: hidden;
    }
    .colleges-section-list-header {
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid var(--admin-border);
        font-weight: 600;
        font-size: 0.8125rem;
        color: var(--admin-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .colleges-section-item {
        display: block;
        padding: 0.75rem 1.25rem;
        color: var(--admin-text);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9375rem;
        border-left: 3px solid transparent;
        transition: background 0.15s ease, border-color 0.15s ease;
    }
    @media (min-width: 768px) {
        .colleges-layout {
            flex-direction: row;
            gap: 0;
        }
        .colleges-section-list {
            width: 240px;
            margin-right: 1.5rem;
        }
    }
    .colleges-section-item:hover {
        background: var(--admin-accent-soft);
        color: var(--admin-accent);
    }
    .colleges-section-item.active {
        background: var(--admin-primary-light);
        color: var(--admin-primary);
        font-weight: 600;
        box-shadow: inset 3px 0 0 var(--admin-primary);
    }
    
    /* Item Management Styles */
    .group-hover-show {
        opacity: 0;
        transition: all 0.2s ease;
        pointer-events: none;
    }
    .group:hover .group-hover-show {
        opacity: 1;
        pointer-events: auto;
    }
    .item-card-actions {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        z-index: 10;
        display: flex;
        gap: 4px;
        background: rgba(255, 255, 255, 0.95);
        padding: 4px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border: 1px solid rgba(0,0,0,0.05);
    }
    .btn-xs-custom {
        padding: 2px 6px;
        font-size: 0.7rem;
    }
    .btn-item-action {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.75rem;
        font-weight: 600;
        font-size: 0.75rem;
        border-radius: 6px;
    }
    .btn-item-action i {
        font-size: 0.875rem;
    }
    .sortable-photo-card {
        cursor: grab;
    }
    .sortable-photo-card.dragging {
        opacity: 0.55;
        transform: scale(0.98);
    }
    .drag-handle {
        cursor: grab;
    }
    .colleges-detail {
        flex: 1;
        min-width: 0;
        background: var(--admin-surface);
        border: 1px solid var(--admin-border);
        border-radius: var(--admin-radius-lg);
        overflow: hidden;
        padding: 1.25rem 1rem;
    }
    @media (min-width: 768px) {
        .colleges-detail {
            padding: 1.75rem 2rem;
        }
    }
    .colleges-detail-title {
        font-weight: 700;
        font-size: 1.5rem;
        letter-spacing: -0.02em;
        color: var(--admin-text);
        margin-bottom: 1.25rem;
    }
    .colleges-detail-body {
        color: var(--admin-text);
        line-height: 1.65;
    }
    .colleges-detail-body p { margin-bottom: 1rem; }';

// Regex replace the entire <style> ... </style> block
$content = preg_replace('/<style>(.*?)<\/style>/s', "<style>\n" . $newStyle . "\n</style>", $content);

file_put_contents($file, $content);
echo "Style block completely repaired responsively.\n";
