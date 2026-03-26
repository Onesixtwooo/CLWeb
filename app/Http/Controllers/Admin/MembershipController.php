<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollegeMembership;
use App\Models\CollegeDepartment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MembershipController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.colleges.index');
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $colleges = $user->isSuperAdmin() ? CollegeController::getColleges() : [];
        $collegeSlug = $request->query('college');
        $fromCollegeSection = (string) $collegeSlug !== '';
        
        if ($fromCollegeSection && $user->isBoundedToCollege()) {
            $collegeSlug = $user->college_slug;
        }

        $departments = [];
        if ($collegeSlug) {
            $departments = CollegeDepartment::where('college_slug', $collegeSlug)->orderBy('name')->get();
        }

        return view('admin.membership.create', compact('colleges', 'collegeSlug', 'fromCollegeSection', 'departments'));
    }

    public function createDepartment(Request $request, string $college, string $department): View
    {
        $this->authorizeCollege($college);

        $departmentModel = CollegeDepartment::findByCollegeAndRouteKey($college, $department);
        if (! $departmentModel) {
            abort(404, 'Department not found.');
        }

        return view('admin.membership.create', [
            'colleges' => [],
            'collegeSlug' => $college,
            'fromCollegeSection' => false,
            'departments' => collect([$departmentModel]),
            'departmentContext' => $departmentModel,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'organization' => ['required', 'string', 'max:255'],
            'membership_type' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'department_id' => ['nullable', 'exists:college_departments,id'],
            'valid_until' => ['nullable', 'date'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'media_image' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'college_slug' => ['nullable', 'string', 'max:80'],
            'is_visible' => ['nullable', 'boolean'],
            'return_department' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $returnCollege = $request->input('return_college');
        $returnDepartment = $request->input('return_department');

        if ($user->isBoundedToCollege()) {
            $data['college_slug'] = $user->college_slug;
        } elseif (empty($data['college_slug']) && $returnCollege) {
            $data['college_slug'] = $returnCollege;
        }

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_visible'] = $request->has('is_visible');

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . Str::slug($data['organization']) . '.' . $file->getClientOriginalExtension();
            $path = Storage::disk('google')->putFileAs('membership', $file, $filename);
            $data['logo'] = Storage::disk('google')->url($path);
        } elseif ($request->filled('media_image')) {
            $data['logo'] = $request->input('media_image');
        }

        CollegeMembership::create($data);

        if ($returnCollege && $returnDepartment) {
            return redirect()->route('admin.colleges.show-department', [
                'college' => $returnCollege,
                'department' => $returnDepartment,
                'section' => 'membership',
            ])->with('success', 'Membership record added successfully.');
        }

        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'membership'])
                ->with('success', 'Membership record added successfully.');
        }

        return redirect()->route('admin.colleges.index')->with('success', 'Membership record added successfully.');
    }

    public function edit(Request $request, CollegeMembership $membership): View
    {
        $this->authorizeCollege($membership->college_slug);
        $user = $request->user();
        $colleges = $user->isSuperAdmin() ? CollegeController::getColleges() : [];
        $returnCollege = $request->query('return_college', $membership->college_slug);

        $collegeSlug = $membership->college_slug;
        $departments = CollegeDepartment::where('college_slug', $collegeSlug)->orderBy('name')->get();

        return view('admin.membership.edit', compact('membership', 'colleges', 'returnCollege', 'departments'));
    }

    public function editDepartment(Request $request, string $college, string $department, CollegeMembership $membership): View
    {
        $this->authorizeCollege($college);

        $departmentModel = CollegeDepartment::findByCollegeAndRouteKey($college, $department);
        if (! $departmentModel) {
            abort(404, 'Department not found.');
        }

        if ($membership->college_slug !== $college || (int) $membership->department_id !== (int) $departmentModel->id) {
            abort(404, 'Membership record not found for this department.');
        }

        return view('admin.membership.edit', [
            'membership' => $membership,
            'colleges' => [],
            'returnCollege' => $college,
            'departments' => collect([$departmentModel]),
            'departmentContext' => $departmentModel,
        ]);
    }

    public function update(Request $request, CollegeMembership $membership): RedirectResponse
    {
        $this->authorizeCollege($membership->college_slug);
        
        $data = $request->validate([
            'organization' => ['required', 'string', 'max:255'],
            'membership_type' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'department_id' => ['nullable', 'exists:college_departments,id'],
            'valid_until' => ['nullable', 'date'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'media_image' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_visible' => ['nullable', 'boolean'],
            'return_department' => ['nullable', 'string', 'max:255'],
        ]);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_visible'] = $request->has('is_visible');

        if ($request->hasFile('logo')) {
            // Delete OLD local image if exists
            if ($membership->logo && !str_starts_with($membership->logo, 'http') && !str_starts_with($membership->logo, 'images/') && Storage::disk('public')->exists($membership->logo)) {
                Storage::disk('public')->delete($membership->logo);
            }
            $file = $request->file('logo');
            $filename = time() . '_' . Str::slug($data['organization']) . '.' . $file->getClientOriginalExtension();
            $path = Storage::disk('google')->putFileAs('membership', $file, $filename);
            $data['logo'] = Storage::disk('google')->url($path);
        } elseif ($request->filled('media_image')) {
            // If using media library, we don't automatically delete the old file since it might be from media library too
            $data['logo'] = $request->input('media_image');
        }

        $membership->update($data);

        $returnCollege = $request->input('return_college');
        $returnDepartment = $request->input('return_department');
        if ($returnCollege && $returnDepartment) {
            return redirect()->route('admin.colleges.show-department', [
                'college' => $returnCollege,
                'department' => $returnDepartment,
                'section' => 'membership',
            ])->with('success', 'Membership record updated successfully.');
        }

        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'membership'])
                ->with('success', 'Membership record updated successfully.');
        }

        return redirect()->route('admin.colleges.index')->with('success', 'Membership record updated successfully.');
    }

    public function destroy(Request $request, CollegeMembership $membership): RedirectResponse
    {
        $this->authorizeCollege($membership->college_slug);
        
        // Only delete file if it's an upload (not media library)
        if ($membership->logo && !str_starts_with($membership->logo, 'images/') && Storage::disk('public')->exists($membership->logo)) {
            Storage::disk('public')->delete($membership->logo);
        }

        $membership->delete();

        $returnCollege = $request->input('return_college');
        $returnDepartment = $request->input('return_department');
        if ($returnCollege && $returnDepartment) {
            return redirect()->route('admin.colleges.show-department', [
                'college' => $returnCollege,
                'department' => $returnDepartment,
                'section' => 'membership',
            ])->with('success', 'Membership record deleted successfully.');
        }

        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'membership'])
                ->with('success', 'Membership record deleted successfully.');
        }

        return redirect()->route('admin.colleges.index')->with('success', 'Membership record deleted successfully.');
    }

    protected function authorizeCollege(?string $collegeSlug): void
    {
        $user = auth()->user();
        if ($user && !$user->canAccessCollege($collegeSlug)) {
            abort(403, 'Unauthorized.');
        }
    }
}
