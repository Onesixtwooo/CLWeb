    <style>
    .engineering-top-header {
        background: #009639 !important;
    }
    @media (max-width: 767px) {
        .logo-full-text { font-size: 1.1rem !important; line-height: 1.2; display: block; }
        .logo-text p { font-size: 0.8rem !important; margin-bottom: 0 !important; }
        .logo-box { width: 40px !important; height: 40px !important; padding: 0.4rem !important; }
        .engineering-navbar { padding: 0.5rem 0.75rem !important; }
    }
    @media (max-width: 576px) {
        .logo-full-text { font-size: 0.95rem !important; }
        .retro-subtitle { font-size: 0.75rem !important; }
    }
    </style>

<!-- Loader screen -->
    <div id="engineering-loader" class="engineering-loader" aria-hidden="false" aria-label="Loading <?php echo e($collegeName); ?>">
        <div class="engineering-loader-inner">
            <img src="<?php echo e($collegeLogoUrl); ?>" alt="<?php echo e($collegeName); ?>" class="engineering-loader-logo">
            <p class="engineering-loader-title"><?php echo e($collegeName); ?></p>
            <p class="engineering-loader-subtitle">Central Luzon State University</p>
            <div class="engineering-loader-spinner"></div>
        </div>
    </div>

    <!-- Fixed header wrapper: top bar + main nav so both stay visible -->
    <div class="engineering-header-wrapper">
        <!-- Top header bar (main contact bar above main header) -->
        <?php echo $__env->make('partials.college-top-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Main header (hides on scroll; top bar above remains) -->
        <div class="engineering-nav-outer">
        <header class="header engineering-header">
        <nav class="navbar navbar-expand-md navbar-dark engineering-navbar">
            <div class="container">
                <a href="<?php echo e(route('college.show', $collegeSlug ?? 'engineering')); ?>" class="navbar-brand d-flex align-items-center gap-2 logo">
                    <div class="logo-box retro-badge">
                        <img src="<?php echo e($collegeLogoUrl); ?>" alt="<?php echo e($collegeName); ?>" class="logo-image">
                    </div>
                    <div class="logo-text d-flex flex-column">
                        <h2 class="retro-heading mb-0">
                            <span class="logo-full-text"><?php echo e(strtoupper($collegeName)); ?></span>
                        </h2>
                        <p class="retro-subtitle">
                            <span class="d-inline d-md-none">CLSU</span>
                            <span class="d-none d-md-inline">Central Luzon State University</span>
                        </p>
                    </div>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-md-0 align-items-md-center">
                        <li class="nav-item">
                            <a href="<?php echo e(route('college.show', $collegeSlug ?? 'engineering')); ?>" class="nav-link">Home</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="aboutDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                About
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
                                <li><a href="<?php echo e(route('college.show', $collegeSlug ?? 'engineering')); ?>#about" class="dropdown-item">About the College</a></li>
                                <li><a href="<?php echo e(route('college.faculty', $collegeSlug ?? 'engineering')); ?>" class="dropdown-item">Faculty & Staff</a></li>
                                <li><a href="<?php echo e(route('college.testimonials', $collegeSlug ?? 'engineering')); ?>" class="dropdown-item">Testimonials</a></li>
                                <li><a href="<?php echo e(route('college.accreditation', $collegeSlug ?? 'engineering')); ?>" class="dropdown-item">Accreditation</a></li>
                                <li><a href="<?php echo e(route('college.organizations', $collegeSlug ?? 'engineering')); ?>" class="dropdown-item">Student Organizations</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="departmentsDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                Departments
                            </a>
                            <?php
                                // Ensure departments are available
                                $headerDepartments = $departments ?? \App\Models\CollegeDepartment::where('college_slug', $collegeSlug ?? 'engineering')->orderBy('sort_order')->orderBy('name')->get();
                            ?>
                            <ul class="dropdown-menu" aria-labelledby="departmentsDropdown">
                                <?php $__empty_1 = true; $__currentLoopData = $headerDepartments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <li><a href="<?php echo e(route('college.department.show', ['college' => $collegeSlug ?? 'engineering', 'department' => $department])); ?>" class="dropdown-item"><?php echo e($department->name); ?></a></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <li><a href="#" class="dropdown-item text-muted">No departments available</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php if(isset($scholarshipsSection) && $scholarshipsSection && $scholarshipsSection->is_visible && !empty($scholarshipsSection->items)): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('college.show', $collegeSlug ?? 'engineering')); ?>#scholarships" class="nav-link">Scholarships</a>
                        </li>
                        <?php endif; ?>
                        <?php if(isset($institutes) && $institutes->isNotEmpty() && (!isset($institutesSection) || $institutesSection->is_visible)): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('college.show', $collegeSlug ?? 'engineering')); ?>#institutes" class="nav-link">Institutes</a>
                        </li>
                        <?php endif; ?>
                        <?php if(isset($extensionSection) && $extensionSection->is_visible && !empty($extensionSection->items)): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('college.show', $collegeSlug ?? 'engineering')); ?>#extension" class="nav-link">Extension</a>
                        </li>
                        <?php endif; ?>
                        <?php if(isset($trainingSection) && $trainingSection->is_visible && !empty($trainingSection->items)): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('college.show', $collegeSlug ?? 'engineering')); ?>#training" class="nav-link">Training</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
        </header>
        </div>
    </div>
