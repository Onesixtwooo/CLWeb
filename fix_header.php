<?php

$lines = file('d:/htdocs/CLSU/resources/views/includes/college-header.blade.php');

$before = array_slice($lines, 0, 24);
$after = array_slice($lines, 25); // Everything after line 24 (the broken </button> space)

$mid = [
    "        <nav class=\"navbar navbar-expand-md navbar-dark engineering-navbar\">\n",
    "            <div class=\"container\">\n",
    "                <a href=\"{{ route('college.show', \$collegeSlug ?? 'engineering') }}\" class=\"navbar-brand d-flex align-items-center gap-2 logo\">\n",
    "                    <div class=\"logo-box retro-badge\">\n",
    "                        <img src=\"{{ \$collegeLogoUrl }}\" alt=\"{{ \$collegeName }}\" class=\"logo-image\">\n",
    "                    </div>\n",
    "                    <div class=\"logo-text d-flex flex-column\">\n",
    "                        <h2 class=\"retro-heading mb-0\">\n",
    "                            <span class=\"logo-full-text\">{{ strtoupper(\$collegeName) }}</span>\n",
    "                        </h2>\n",
    "                        <p class=\"retro-subtitle\">\n",
    "                            <span>{{ \$collegeName }}, Central Luzon State University</span>\n",
    "                        </p>\n",
    "                    </div>\n",
    "                </a>\n",
    "\n",
    "                <button class=\"navbar-toggler\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#mainNavbar\"\n",
    "                        aria-controls=\"mainNavbar\" aria-expanded=\"false\" aria-label=\"Toggle navigation\">\n",
    "                    <span class=\"navbar-toggler-icon\"></span>\n",
    "                </button>\n"
];

$output = array_merge($before, $mid, $after);
file_put_contents('d:/htdocs/CLSU/resources/views/includes/college-header.blade.php', implode("", $output));
echo "Fixed";
