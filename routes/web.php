<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CollegeController;
use App\Http\Controllers\Admin\CollegeDownloadController;
use App\Http\Controllers\Admin\ExtensionController;
use App\Http\Controllers\Admin\TrainingController;
use App\Http\Controllers\Admin\ScholarshipController;
use App\Http\Controllers\CollegePageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\AccreditationController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\InstituteController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\DepartmentLinkageController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Models\Faculty;
use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\WelcomeController::class, 'index']);
Route::get('media/proxy/{fileId}', [App\Http\Controllers\Admin\MediaController::class, 'proxy'])->where('fileId', '[a-zA-Z0-9\-_]+')->name('media.proxy.public');

// Facebook Webhook Endpoint for Meta
Route::match(['get', 'post'], 'facebook/webhook', [\App\Http\Controllers\FacebookWebhookController::class, 'handle']);

// Privacy Policy URL for Meta App Dashboard
Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/about/history', function () {
    return view('history');
})->name('history');

Route::get('/about/brand-guidelines', function () {
    return view('brand-guidelines');
})->name('brand-guidelines');

Route::get('/about/campus-life', function () {
    return view('campus-life');
})->name('campus-life');

Route::get('/about/offices', function () {
    return view('offices');
})->name('offices');

Route::get('/about/university-officials', function () {
    return view('university-officials');
})->name('university-officials');

Route::get('/about/organizational-structure', function () {
    return view('organizational-structure');
})->name('organizational-structure');

Route::get('/news', function () {
    return view('news');
})->name('news');

Route::get('/coming-soon', function () {
    return view('coming-soon');
})->name('coming.soon');

Route::get('/university-modern', function () {
    return view('university');
})->name('university.modern');

Route::get('/college/{college}', [CollegePageController::class, 'show'])->name('college.show');
Route::get('/college/{college}/explore', function ($college) {
    return redirect()->route('college.facilities', ['college' => $college]);
})->name('college.explore');
Route::redirect('/college-of-engineering', '/college/engineering', 301)->name('college.engineering');

Route::get('/college/{college}/testimonials', [CollegePageController::class, 'testimonials'])->name('college.testimonials');
Route::get('/college/{college}/accreditation', [CollegePageController::class, 'accreditation'])->name('college.accreditation');
Route::get('/college/{college}/downloads', [CollegePageController::class, 'downloads'])->name('college.downloads');
Route::get('/college/{college}/downloads/{download}/file', [CollegePageController::class, 'downloadFile'])->name('college.downloads.file');

Route::redirect('/college-of-engineering/testimonials', '/college/engineering/testimonials', 301)->name('college.engineering.testimonials');


Route::get('/college/{college}/faculty', [CollegePageController::class, 'faculty'])->name('college.faculty');
Route::get('/college/{college}/training', [CollegePageController::class, 'training'])->name('college.training');
Route::get('/college/{college}/training/{slug}', [CollegePageController::class, 'showTraining'])->name('college.training.show');
Route::get('/college/{college}/scholarship/{slug}', [CollegePageController::class, 'showScholarship'])->name('college.scholarship.show');
Route::get('/college/{college}/scholarships', [CollegePageController::class, 'scholarships'])->name('college.scholarships');
Route::get('/college/{college}/facilities', [CollegePageController::class, 'facilities'])->name('college.facilities');
Route::get('/college/{college}/organizations', [CollegePageController::class, 'organizations'])->name('college.organizations');
Route::get('/college/{college}/organizations/{organization}', [CollegePageController::class, 'showOrganization'])->name('college.organization.show');
Route::get('/college/{college}/organizations/{organization}/album/{index}', [CollegePageController::class, 'showOrganizationAlbum'])->name('college.organization.album');
Route::get('/college/{college}/facilities/{facility}', [App\Http\Controllers\FacilityController::class, 'show'])->name('college.facility.show');
Route::get('/college/{college}/departments/{department}', [App\Http\Controllers\DepartmentPageController::class, 'show'])->name('college.department.show');
Route::get('/college/{college}/{sectionSlug}/{institute}', [App\Http\Controllers\InstitutePageController::class, 'show'])->name('college.institute.show')->where('institute', '[0-9]+');

Route::redirect('/college-of-engineering/faculty', '/college/engineering/faculty', 301)->name('college.engineering.faculty');

Route::redirect('/college-of-engineering/information-technology', '/college/engineering/departments/information-technology', 301)->name('department.it');

Route::get('/college/{college}/news-announcement-board', [App\Http\Controllers\NewsBoardController::class, 'index'])->name('news.announcement.board');

Route::get('/college/{college}/news-announcement-board/article/{slug}', [App\Http\Controllers\ArticlePageController::class, 'show'])->name('news.announcement.detail');

Route::get('/college/{college}/news-announcement-board/announcement/{slug}', [App\Http\Controllers\AnnouncementPageController::class, 'show'])->name('announcement.detail');


/* Admin (CMS) */
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login']);
    });
    Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('search', [SearchController::class, 'search'])->name('search');

        // Users - accessible by superadmins and college admins (permissions handled in controller)
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('users/roles/permissions', [UserController::class, 'saveRolePermissions'])->name('users.roles.permissions');
        Route::resource('articles', ArticleController::class)->except(['show']);
        Route::resource('announcements', AnnouncementController::class)->except(['show']);
        Route::get('{college}/{department}/faculty/create', [FacultyController::class, 'createForDepartment'])->name('faculty.create-department');
        Route::get('{college}/faculty/create', [FacultyController::class, 'createForCollege'])->name('faculty.create-college');
        Route::resource('faculty', FacultyController::class)->except(['show', 'create']);
        Route::get('faculty/create', [FacultyController::class, 'create'])->name('faculty.create');
        Route::resource('facilities', FacilityController::class)->except(['show']);
        Route::delete('facilities/images/{facilityImage}', [FacilityController::class, 'destroyImage'])->name('facilities.images.destroy');
        Route::resource('institutes', InstituteController::class)->except(['show']);
        Route::resource('institute-staff', \App\Http\Controllers\Admin\InstituteStaffController::class)->except(['show', 'index']);
        Route::get('faqs/{college}/create', [FaqController::class, 'create'])->name('faqs.create-college');
        Route::resource('faqs', FaqController::class)->except(['show']);
        Route::resource('testimonials', TestimonialController::class)->except(['show']);
        Route::resource('accreditations', AccreditationController::class)->except(['show']);
        Route::get('{college}/{department}/memberships/create', [MembershipController::class, 'createDepartment'])->name('colleges.create-department-membership');
        Route::get('{college}/{department}/memberships/{membership}/edit', [MembershipController::class, 'editDepartment'])->name('colleges.edit-department-membership');
        Route::resource('memberships', MembershipController::class)->except(['show']);
        Route::get('organizations/{organization}/show', [OrganizationController::class, 'showLegacy'])->name('organizations.show-legacy');
        Route::get('organizations/{organization}/sections/{section}/edit', [OrganizationController::class, 'editSection'])->name('organizations.edit-section');
        Route::put('organizations/{organization}/sections/{section}', [OrganizationController::class, 'updateSection'])->name('organizations.update-section');
        Route::post('organizations/{organization}/sections', [OrganizationController::class, 'addSection'])->name('organizations.add-section');
        Route::delete('organizations/{organization}/sections/{section}', [OrganizationController::class, 'deleteSection'])->name('organizations.delete-section');

        // Individual item management within sections
        Route::post('organizations/{organization}/sections/{section}/items', [OrganizationController::class, 'storeItem'])->name('organizations.store-item');
        Route::post('organizations/{organization}/sections/{section}/items/batch', [OrganizationController::class, 'storeBatchItems'])->name('organizations.store-batch-items');
        Route::post('organizations/{organization}/sections/{section}/items/{index}/move', [OrganizationController::class, 'moveItem'])->name('organizations.move-item');
        Route::post('organizations/{organization}/sections/{section}/items/reorder', [OrganizationController::class, 'reorderItems'])->name('organizations.reorder-items');
        Route::put('organizations/{organization}/sections/{section}/items/{index}', [OrganizationController::class, 'updateItem'])->name('organizations.update-item');
        Route::delete('organizations/{organization}/sections/{section}/items/{index}', [OrganizationController::class, 'deleteItem'])->name('organizations.delete-item');

        Route::get('{college}/{department}/organizations/create', [OrganizationController::class, 'createForDepartment'])->name('organizations.create-department');
        Route::resource('organizations', OrganizationController::class)->except(['show', 'index']);
        Route::get('organizations/{college}/{organization}/edit', [OrganizationController::class, 'editScoped'])->name('organizations.edit-scoped');
        Route::get('organizations/{college}/{organization}/gallery/{album}', [OrganizationController::class, 'showGalleryAlbum'])->name('organizations.gallery-album');
        Route::get('organizations/{college}/{organization}/sections/{section}', [OrganizationController::class, 'showSection'])->name('organizations.show-section');
        Route::get('organizations/{college}/{organization}', [OrganizationController::class, 'show'])->name('organizations.show');

        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings/appearance', [SettingsController::class, 'updateAppearance'])->name('settings.appearance.update');
        Route::post('settings/google-drive', [SettingsController::class, 'updateGoogleDrive'])->name('settings.google-drive.update');
        Route::get('settings/google-drive/auth', [SettingsController::class, 'googleDriveAuth'])->name('settings.google-drive.auth');
        Route::get('settings/google-drive/callback', [SettingsController::class, 'googleDriveCallback'])->name('settings.google-drive.callback');
        Route::post('settings/email', [SettingsController::class, 'updateEmail'])->name('settings.email.update');
        Route::post('settings/president', [SettingsController::class, 'updatePresidentContact'])->name('settings.president.update');
        Route::put('settings/facebook', [SettingsController::class, 'updateFacebook'])->name('settings.facebook-update');
        Route::put('settings/college-facebook', [SettingsController::class, 'updateCollegeFacebookIntegration'])->name('settings.college-facebook.update');
        // Facebook Configuration
        Route::post('facebook/sync', [\App\Http\Controllers\Admin\FacebookConfigController::class, 'sync'])->name('facebook.sync');
        Route::resource('facebook', \App\Http\Controllers\Admin\FacebookConfigController::class)->except(['show']);
        // Colleges (admin) – URLs use /colleges with route names admin.colleges.*
        Route::get('colleges', [CollegeController::class, 'index'])->name('colleges.index');
        Route::post('colleges', [CollegeController::class, 'store'])->name('colleges.store');
        Route::get('colleges/{college}/edit', [CollegeController::class, 'editCollege'])->name('colleges.edit');
        Route::put('colleges/{college}', [CollegeController::class, 'updateCollege'])->name('colleges.update');
        Route::delete('colleges/{college}', [CollegeController::class, 'destroy'])->name('colleges.destroy');
        Route::get('colleges/{college}/{section}/edit', [CollegeController::class, 'edit'])->name('colleges.edit-section');
        Route::post('colleges/{college}/{section}/toggle-visibility', [CollegeController::class, 'toggleVisibility'])->name('colleges.toggle-visibility');
        Route::put('colleges/{college}/{section}', [CollegeController::class, 'update'])->name('colleges.update-section');
        // Global Scholarships CRUD (superadmin only, uses _global slug)
        Route::get('scholarships', [ScholarshipController::class, 'index'])->defaults('college', '_global')->name('scholarships.index');
        Route::get('scholarships/create', [ScholarshipController::class, 'create'])->defaults('college', '_global')->name('scholarships.create');
        Route::post('scholarships', [ScholarshipController::class, 'store'])->defaults('college', '_global')->name('scholarships.store');
        Route::get('scholarships/{scholarship}/edit', [ScholarshipController::class, 'editGlobal'])->name('scholarships.edit');
        Route::put('scholarships/{scholarship}', [ScholarshipController::class, 'updateGlobal'])->name('scholarships.update');
        Route::delete('scholarships/{scholarship}', [ScholarshipController::class, 'destroyGlobal'])->name('scholarships.destroy');
        // Scholarships CRUD under college (must be before the {section?} wildcard)
        Route::get('colleges/{college}/scholarships', [ScholarshipController::class, 'index'])->name('colleges.scholarships.index');
        Route::get('colleges/{college}/scholarships/create', [ScholarshipController::class, 'create'])->name('colleges.scholarships.create');
        Route::post('colleges/{college}/scholarships', [ScholarshipController::class, 'store'])->name('colleges.scholarships.store');
        Route::get('colleges/{college}/scholarships/{scholarship}/edit', [ScholarshipController::class, 'edit'])->name('colleges.scholarships.edit');
        Route::put('colleges/{college}/scholarships/{scholarship}', [ScholarshipController::class, 'update'])->name('colleges.scholarships.update');
        Route::delete('colleges/{college}/scholarships/{scholarship}', [ScholarshipController::class, 'destroy'])->name('colleges.scholarships.destroy');

        // Extensions CRUD
        Route::get('colleges/{college}/extensions', [ExtensionController::class, 'index'])->name('colleges.extensions.index');
        Route::get('colleges/{college}/extensions/create', [ExtensionController::class, 'create'])->name('colleges.extensions.create');
        Route::post('colleges/{college}/extensions', [ExtensionController::class, 'store'])->name('colleges.extensions.store');
        Route::get('colleges/{college}/extensions/{extension}/edit', [ExtensionController::class, 'edit'])->name('colleges.extensions.edit');
        Route::put('colleges/{college}/extensions/{extension}', [ExtensionController::class, 'update'])->name('colleges.extensions.update');
        Route::delete('colleges/{college}/extensions/{extension}', [ExtensionController::class, 'destroy'])->name('colleges.extensions.destroy');

        // Trainings CRUD
        Route::get('colleges/{college}/trainings', [TrainingController::class, 'index'])->name('colleges.trainings.index');
        Route::get('colleges/{college}/trainings/create', [TrainingController::class, 'create'])->name('colleges.trainings.create');
        Route::post('colleges/{college}/trainings', [TrainingController::class, 'store'])->name('colleges.trainings.store');
        Route::get('colleges/{college}/trainings/{training}/edit', [TrainingController::class, 'edit'])->name('colleges.trainings.edit');
        Route::put('colleges/{college}/trainings/{training}', [TrainingController::class, 'update'])->name('colleges.trainings.update');
        Route::delete('colleges/{college}/trainings/{training}', [TrainingController::class, 'destroy'])->name('colleges.trainings.destroy');
        Route::get('colleges/{college}/downloads', [CollegeDownloadController::class, 'index'])->name('colleges.downloads.index');
        Route::get('colleges/{college}/downloads/create', [CollegeDownloadController::class, 'create'])->name('colleges.downloads.create');
        Route::post('colleges/{college}/downloads', [CollegeDownloadController::class, 'store'])->name('colleges.downloads.store');
        Route::get('colleges/{college}/downloads/{download}/edit', [CollegeDownloadController::class, 'edit'])->name('colleges.downloads.edit');
        Route::put('colleges/{college}/downloads/{download}', [CollegeDownloadController::class, 'update'])->name('colleges.downloads.update');
        Route::delete('colleges/{college}/downloads/{download}', [CollegeDownloadController::class, 'destroy'])->name('colleges.downloads.destroy');
        Route::get('colleges/{college}/{section?}', [CollegeController::class, 'show'])->name('colleges.show');
        Route::post('colleges/{college}/appearance', [CollegeController::class, 'updateAppearance'])->name('colleges.appearance.update');
        // Department-level show/edit
        Route::get('{college}/{department}/objectives/curriculum/create', [CollegeController::class, 'createDepartmentCurriculum'])->name('colleges.create-department-curriculum');
        Route::get('{college}/{department}/objectives/curriculum/{curriculum}/edit', [CollegeController::class, 'editDepartmentCurriculum'])->name('colleges.edit-department-curriculum');
        Route::get('{college}/{department}/extension/items/create', [CollegeController::class, 'createDepartmentExtension'])->name('colleges.create-department-extension');
        Route::get('{college}/{department}/extension/items/{extension}/edit', [CollegeController::class, 'editDepartmentExtension'])->name('colleges.edit-department-extension');
        Route::get('{college}/{department}/training/items/create', [CollegeController::class, 'createDepartmentTraining'])->name('colleges.create-department-training');
        Route::get('{college}/{department}/training/items/{training}/edit', [CollegeController::class, 'editDepartmentTraining'])->name('colleges.edit-department-training');
        Route::get('{college}/{department}/programs/create', [CollegeController::class, 'createDepartmentProgram'])->name('colleges.create-department-program');
        Route::get('{college}/{department}/programs/{program}/edit', [CollegeController::class, 'editDepartmentProgram'])->name('colleges.edit-department-program');
        Route::get('{college}/{department}/facilities/create', [CollegeController::class, 'createDepartmentFacility'])->name('colleges.create-department-facility');
        Route::get('{college}/{department}/facilities/{facility}/edit', [CollegeController::class, 'editDepartmentFacility'])->name('colleges.edit-department-facility');
        Route::get('{college}/{department}/alumni/create', [CollegeController::class, 'createDepartmentAlumnus'])->name('colleges.create-department-alumnus');
        Route::get('{college}/{department}/alumni/{alumnus}/edit', [CollegeController::class, 'editDepartmentAlumnus'])->name('colleges.edit-department-alumnus');
        Route::get('{college}/{department}/objectives/create', [CollegeController::class, 'createDepartmentObjective'])->name('colleges.create-department-objective');
        Route::get('{college}/{department}/objectives/{objective}/edit', [CollegeController::class, 'editDepartmentObjective'])->name('colleges.edit-department-objective');
        Route::get('{college}/{department}/card-image/edit', [CollegeController::class, 'editDepartmentCardImage'])->name('colleges.edit-department-card-image');
        Route::get('{college}/{department}/retro/create', [CollegeController::class, 'createDepartmentRetro'])->name('colleges.create-department-retro');
        Route::get('{college}/{department}/retro/{retro}/edit', [CollegeController::class, 'editDepartmentRetro'])->name('colleges.edit-department-retro');
        Route::get('{college}/{department}/graduate-outcomes/create', [CollegeController::class, 'createDepartmentGraduateOutcome'])->name('colleges.create-department-graduate-outcome');
        Route::get('{college}/{department}/graduate-outcomes/{outcome}/edit', [CollegeController::class, 'editDepartmentGraduateOutcome'])->name('colleges.edit-department-graduate-outcome');
        Route::get('{college}/{department}/{section}', [CollegeController::class, 'showDepartment'])->name('colleges.show-department');
        Route::get('{college}/{department}/{section}/edit', [CollegeController::class, 'editDepartmentSection'])->name('colleges.edit-department-section');
        Route::get('{college}/departments/{department}/{section}', [CollegeController::class, 'redirectDepartmentShow'])->name('colleges.show-department-short-legacy');
        Route::get('{college}/departments/{department}/{section}/edit', [CollegeController::class, 'redirectDepartmentEditSection'])->name('colleges.edit-department-section-short-legacy-2');
        Route::get('colleges/{college}/departments/{department}', [CollegeController::class, 'redirectDepartmentShow'])->name('colleges.show-department-legacy');
        Route::get('colleges/{college}/departments/{department}/section/{section}', [CollegeController::class, 'redirectDepartmentShow'])->name('colleges.show-department-section-legacy');
        Route::get('colleges/{college}/departments/{department}/sections/{section}/edit', [CollegeController::class, 'redirectDepartmentEditSection'])->name('colleges.edit-department-section-legacy');
        Route::get('colleges/{college}/departments/{department}/section/{section}/edit', [CollegeController::class, 'redirectDepartmentEditSection'])->name('colleges.edit-department-section-short-legacy');
        Route::put('colleges/{college}/departments/{department}', [CollegeController::class, 'updateDepartment'])->name('colleges.update-department');
        // Department Linkage CRUD
        Route::get('colleges/{college}/departments/{department}/linkages/create', [DepartmentLinkageController::class, 'create'])->name('linkages.create');
        Route::post('colleges/{college}/departments/{department}/linkages', [DepartmentLinkageController::class, 'store'])->name('linkages.store');
        Route::get('colleges/{college}/departments/{department}/linkages/{linkage}/edit', [DepartmentLinkageController::class, 'edit'])->name('linkages.edit');
        Route::put('colleges/{college}/departments/{department}/linkages/{linkage}', [DepartmentLinkageController::class, 'update'])->name('linkages.update');
        Route::delete('colleges/{college}/departments/{department}/linkages/{linkage}', [DepartmentLinkageController::class, 'destroy'])->name('linkages.destroy');
        Route::delete('colleges/{college}/departments/{department}/partners/{partner}', [CollegeController::class, 'destroyLinkagePartner'])->name('colleges.destroy-linkage-partner');
        Route::delete('colleges/{college}/departments/{department}/facilities/{facility}', [CollegeController::class, 'destroyFacilityItem'])->name('colleges.destroy-facility-item');
        Route::delete('colleges/{college}/departments/{department}/alumni/{alumnus}', [CollegeController::class, 'destroyAlumnus'])->name('colleges.destroy-alumnus');

        // Institute-level show/edit
        Route::get('colleges/{college}/institutes/{institute}', [CollegeController::class, 'showInstitute'])->name('colleges.show-institute');
        Route::get('colleges/{college}/institutes/{institute}/sections/{section}/edit', [CollegeController::class, 'editInstituteSection'])->name('colleges.edit-institute-section');
        Route::put('colleges/{college}/institutes/{institute}', [CollegeController::class, 'updateInstitute'])->name('colleges.update-institute');

        Route::post('colleges/{college}/{section}', [CollegeController::class, 'show'])->name('colleges.show.post');

    });
});
