<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollegeDepartment;
use App\Models\DepartmentLinkage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DepartmentLinkageController extends Controller
{
    private static function getColleges(): array
    {
        return CollegeController::getColleges();
    }

    private function resolveDepartment(string $college, string $departmentId, Request $request): CollegeDepartment
    {
        $colleges = self::getColleges();
        if (! isset($colleges[$college])) {
            abort(404, 'College not found.');
        }

        $user = $request->user();
        if ($user && ! $user->canAccessCollege($college)) {
            abort(403, 'You do not have access to this college.');
        }

        $department = CollegeDepartment::findByCollegeAndRouteKey($college, $departmentId);
        if (! $department) {
            abort(404, 'Department not found.');
        }

        if ($user && ! $user->canAccessDepartment($college, $department->name)) {
            abort(403, 'You do not have access to this department.');
        }

        return $department;
    }

    private function normalizeOptionalUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }
        $url = trim($url);
        if (! preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }
        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    private function handleImageUpload(Request $request, string $college, CollegeDepartment $department, string $partnerName, ?string $existing = null): ?string
    {
        if (! $request->hasFile('image')) {
            return $existing;
        }

        $file    = $request->file('image');
        $slug    = Str::slug($partnerName ?: 'partner');
        $fname   = time() . '_linkage_' . Str::slug($department->name) . '.' . $file->getClientOriginalExtension();
        $path    = Storage::disk('google')->putFileAs(
            "colleges/{$college}/departments/" . Str::slug($department->name) . "/linkages/{$slug}",
            $file,
            $fname
        );

        return $path ? Storage::disk('google')->url($path) : $existing;
    }

    /** GET /{college}/{department}/linkages/create */
    public function create(Request $request, string $college, string $department): View
    {
        $colleges   = self::getColleges();
        $dept       = $this->resolveDepartment($college, $department, $request);

        return view('admin.linkages.create', [
            'college'    => $college,
            'collegeName'=> $colleges[$college],
            'department' => $dept,
        ]);
    }

    /** POST /{college}/{department}/linkages */
    public function store(Request $request, string $college, string $department): RedirectResponse
    {
        $dept = $this->resolveDepartment($college, $department, $request);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'type'        => ['required', 'string', 'in:local,international'],
            'description' => ['nullable', 'string'],
            'url'         => ['nullable', 'string', 'max:500'],
            'image'       => ['nullable', 'image', 'max:2048'],
        ]);

        $data['url']   = $this->normalizeOptionalUrl($data['url'] ?? null);
        $imagePath     = $this->handleImageUpload($request, $college, $dept, $data['name']);
        $maxSortOrder  = $dept->linkages()->max('sort_order') ?? -1;

        DepartmentLinkage::create([
            'department_id' => $dept->id,
            'name'          => $data['name'],
            'type'          => $data['type'],
            'description'   => $data['description'] ?? '',
            'url'           => $data['url'],
            'image'         => $imagePath,
            'sort_order'    => $maxSortOrder + 1,
        ]);

        return redirect()
            ->route('admin.colleges.show-department', [
                'college'    => $college,
                'department' => $dept,
                'section'    => 'linkages',
            ])
            ->with('success', 'Partner added successfully.');
    }

    private function resolveLinkage(CollegeDepartment $department, string|int $linkage): DepartmentLinkage
    {
        $partner = DepartmentLinkage::findByDepartmentAndRouteKey($department->id, $linkage);

        if (! $partner) {
            abort(404, 'Partner not found.');
        }

        return $partner;
    }

    /** GET /{college}/{department}/linkages/{linkage}/edit */
    public function edit(Request $request, string $college, string $department, string $linkage): View
    {
        $colleges = self::getColleges();
        $dept     = $this->resolveDepartment($college, $department, $request);
        $partner  = $this->resolveLinkage($dept, $linkage);

        return view('admin.linkages.edit', [
            'college'    => $college,
            'collegeName'=> $colleges[$college],
            'department' => $dept,
            'partner'    => $partner,
        ]);
    }

    /** PUT /{college}/{department}/linkages/{linkage} */
    public function update(Request $request, string $college, string $department, string $linkage): RedirectResponse
    {
        $dept    = $this->resolveDepartment($college, $department, $request);
        $partner = $this->resolveLinkage($dept, $linkage);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'type'        => ['required', 'string', 'in:local,international'],
            'description' => ['nullable', 'string'],
            'url'         => ['nullable', 'string', 'max:500'],
            'image'       => ['nullable', 'image', 'max:2048'],
        ]);

        $data['url'] = $this->normalizeOptionalUrl($data['url'] ?? null);
        $imagePath   = $this->handleImageUpload($request, $college, $dept, $data['name'], $partner->image);

        $partner->update([
            'name'        => $data['name'],
            'type'        => $data['type'],
            'description' => $data['description'] ?? '',
            'url'         => $data['url'],
            'image'       => $imagePath,
        ]);

        return redirect()
            ->route('admin.colleges.show-department', [
                'college'    => $college,
                'department' => $dept,
                'section'    => 'linkages',
            ])
            ->with('success', 'Partner updated successfully.');
    }

    /** DELETE /{college}/{department}/linkages/{linkage} */
    public function destroy(Request $request, string $college, string $department, string $linkage): RedirectResponse
    {
        $dept    = $this->resolveDepartment($college, $department, $request);
        $partner = $this->resolveLinkage($dept, $linkage);
        $partner->delete();

        return redirect()
            ->route('admin.colleges.show-department', [
                'college'    => $college,
                'department' => $dept,
                'section'    => 'linkages',
            ])
            ->with('success', 'Partner removed successfully.');
    }
}
