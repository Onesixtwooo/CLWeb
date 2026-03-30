<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollegeAccreditation;
use App\Models\CollegeDepartment;
use App\Models\DepartmentProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AccreditationController extends Controller
{
    private function accreditationCreateContext(Request $request, ?string $collegeSlug = null, bool $fromCollegeSection = false): array
    {
        $user = $request->user();
        $colleges = $user->isSuperAdmin() ? CollegeController::getColleges() : [];

        if ($fromCollegeSection && $user->isBoundedToCollege()) {
            $collegeSlug = $user->college_slug;
        }

        $programs = [];
        if ($collegeSlug) {
            $programs = DepartmentProgram::whereHas('department', function ($q) use ($collegeSlug) {
                $q->where('college_slug', $collegeSlug);
            })->orderBy('title')->get();
        }

        return [
            'colleges' => $colleges,
            'collegeSlug' => $collegeSlug,
            'fromCollegeSection' => $fromCollegeSection,
            'programs' => $programs,
        ];
    }

    private function resolveDepartmentSlug(?int $programId): ?string
    {
        if (! $programId) {
            return null;
        }

        $program = DepartmentProgram::with('department:id,name')->find($programId);

        return $program?->department?->name
            ? Str::slug($program->department->name)
            : null;
    }

    private function accreditationFolder(string $college, string $agency, ?string $departmentSlug = null): string
    {
        $baseFolder = 'colleges/' . $college;

        if ($departmentSlug) {
            $baseFolder .= '/' . $departmentSlug;
        }

        return $baseFolder . '/accrediation/' . Str::slug($agency);
    }

    private function storeAccreditationLogo(Request $request, string $college, string $agency, ?string $departmentSlug = null): ?string
    {
        if (! $request->hasFile('logo')) {
            return null;
        }

        $file = $request->file('logo');
        $filename = time() . '_' . Str::slug($agency) . '.' . $file->getClientOriginalExtension();

        return Storage::disk('google')->putFileAs($this->accreditationFolder($college, $agency, $departmentSlug), $file, $filename) ?: null;
    }

    private function managedAccreditationFolder(?string $logoPath, string $college, ?string $departmentSlug = null): ?string
    {
        if (! is_string($logoPath)) {
            return null;
        }

        $path = ltrim($logoPath, '/');
        $expectedPrefix = 'colleges/' . $college . '/';

        if (! str_starts_with($path, $expectedPrefix)) {
            return null;
        }

        $relativePath = substr($path, strlen($expectedPrefix));

        if ($departmentSlug) {
            if (! str_starts_with($relativePath, $departmentSlug . '/accrediation/')) {
                return null;
            }
        } elseif (! str_starts_with($relativePath, 'accrediation/')) {
            return null;
        }

        return dirname($path);
    }

    private function deleteAccreditationLogo(?string $logoPath, ?string $folderPath = null): void
    {
        if (empty($logoPath)) {
            return;
        }

        $drive = app(\App\Services\GoogleDriveService::class);

        if ($folderPath) {
            $drive->deleteFolder($folderPath);
            return;
        }

        $drive->delete($logoPath);
    }

    public function index(): RedirectResponse
    {
        return redirect()->route('admin.colleges.index');
    }

    public function createForCollege(Request $request, string $college): View
    {
        return view('admin.accreditation.create', $this->accreditationCreateContext($request, $college, true));
    }

    public function create(Request $request): View
    {
        $collegeSlug = $request->query('college');

        if ((string) $collegeSlug !== '') {
            return redirect()->route('admin.colleges.accreditations.create', ['college' => $collegeSlug]);
        }

        return view('admin.accreditation.create', $this->accreditationCreateContext($request));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'agency' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:255'],
            'program_id' => ['nullable', 'exists:department_programs,id'],
            'valid_until' => ['nullable', 'date'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'college_slug' => ['nullable', 'string', 'max:80'],
            'is_visible' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $returnCollege = $request->input('return_college');

        if ($user->isBoundedToCollege()) {
            $data['college_slug'] = $user->college_slug;
        } elseif (empty($data['college_slug']) && $returnCollege) {
            $data['college_slug'] = $returnCollege;
        }

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_visible'] = $request->has('is_visible');
        $departmentSlug = $this->resolveDepartmentSlug($data['program_id'] ? (int) $data['program_id'] : null);

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->storeAccreditationLogo($request, $data['college_slug'], $data['agency'], $departmentSlug);
        }

        CollegeAccreditation::create($data);

        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'accreditation'])
                ->with('success', 'Accreditation record added successfully.');
        }

        return redirect()->route('admin.colleges.index')->with('success', 'Accreditation record added successfully.');
    }

    public function edit(Request $request, CollegeAccreditation $accreditation): View
    {
        $this->authorizeCollege($accreditation->college_slug);
        $user = $request->user();
        $colleges = $user->isSuperAdmin() ? CollegeController::getColleges() : [];
        $returnCollege = $request->query('return_college', $accreditation->college_slug);

        $collegeSlug = $accreditation->college_slug;
        $programs = DepartmentProgram::whereHas('department', function($q) use ($collegeSlug) {
            $q->where('college_slug', $collegeSlug);
        })->orderBy('title')->get();

        return view('admin.accreditation.edit', compact('accreditation', 'colleges', 'returnCollege', 'programs'));
    }

    public function update(Request $request, CollegeAccreditation $accreditation): RedirectResponse
    {
        $this->authorizeCollege($accreditation->college_slug);
        $data = $request->validate([
            'agency' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:255'],
            'program_id' => ['nullable', 'exists:department_programs,id'],
            'valid_until' => ['nullable', 'date'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'college_slug' => ['nullable', 'string', 'max:80'],
            'is_visible' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        if ($user->isBoundedToCollege()) {
            $data['college_slug'] = $user->college_slug;
        }

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_visible'] = $request->has('is_visible');
        $departmentSlug = $this->resolveDepartmentSlug($data['program_id'] ? (int) $data['program_id'] : null);
        $oldDepartmentSlug = $this->resolveDepartmentSlug($accreditation->program_id);
        $oldLogo = $accreditation->logo;
        $oldFolder = $this->managedAccreditationFolder($oldLogo, $accreditation->college_slug ?? $data['college_slug'], $oldDepartmentSlug);

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->storeAccreditationLogo($request, $data['college_slug'], $data['agency'], $departmentSlug);

            if ($oldLogo && $oldLogo !== $data['logo']) {
                $newFolder = $this->managedAccreditationFolder($data['logo'], $data['college_slug'], $departmentSlug);
                $this->deleteAccreditationLogo($oldLogo, $oldFolder !== $newFolder ? $oldFolder : null);
            }
        }

        $accreditation->update($data);

        $returnCollege = $request->input('return_college');
        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'accreditation'])
                ->with('success', 'Accreditation record updated successfully.');
        }

        return redirect()->route('admin.colleges.index')->with('success', 'Accreditation record updated successfully.');
    }

    public function destroy(Request $request, CollegeAccreditation $accreditation): RedirectResponse
    {
        $this->authorizeCollege($accreditation->college_slug);
        $returnCollege = $request->input('return_college', $accreditation->college_slug);

        $this->deleteAccreditationLogo(
            $accreditation->logo,
            $this->managedAccreditationFolder(
                $accreditation->logo,
                $accreditation->college_slug,
                $this->resolveDepartmentSlug($accreditation->program_id)
            )
        );

        $accreditation->delete();

        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'accreditation'])
                ->with('success', 'Accreditation record deleted successfully.');
        }

        return redirect()->route('admin.colleges.index')->with('success', 'Accreditation record deleted successfully.');
    }

    private function authorizeCollege(?string $collegeSlug): void
    {
        if (! request()->user()->canAccessCollege($collegeSlug)) {
            abort(403, 'You do not have access to this record.');
        }
    }
}
