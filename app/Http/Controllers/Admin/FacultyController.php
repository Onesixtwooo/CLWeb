<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollegeDepartment;
use App\Models\Faculty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FacultyController extends Controller
{
    public function index(Request $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('admin.colleges.index');
    }

    public function create(Request $request): View|RedirectResponse
    {
        $collegeSlug = $request->query('college');
        if ((string) $collegeSlug !== '') {
            return redirect()->route('admin.faculty.create-college', ['college' => $collegeSlug] + $request->except('college'));
        }

        return $this->renderCreateView($request, null, false);
    }

    public function createForCollege(Request $request, string $college): View|RedirectResponse
    {
        return $this->renderCreateView($request, $college, true);
    }

    public function createForDepartment(Request $request, string $college, string $department): View|RedirectResponse
    {
        $departmentModel = CollegeDepartment::findByCollegeAndRouteKey($college, $department);
        if (! $departmentModel) {
            abort(404, 'Department not found.');
        }

        $request->query->set('department', $departmentModel->name);
        $request->query->set('return_department', $departmentModel->getRouteKey());

        return $this->renderCreateView($request, $college, true);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'college_slug' => ['nullable', 'string', 'max:80'],
        ]);
        $user = $request->user();
        $returnCollege = $request->input('return_college');
        $returnDepartment = $request->input('return_department');
        if ($user->isBoundedToCollege()) {
            $data['college_slug'] = $user->college_slug;
        } elseif (empty($data['college_slug']) && $returnCollege && in_array($returnCollege, array_keys(CollegeController::getColleges()), true)) {
            $data['college_slug'] = $returnCollege;
        } elseif (empty($data['college_slug']) && $user->isSuperAdmin()) {
            $data['college_slug'] = null;
        }
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['user_id'] = $user->id;
        $data['photo'] = null;
        if ($request->hasFile('photo')) {
            $data['photo'] = $this->storeFacultyPhoto($request->file('photo'), $data['college_slug'], $data['department'] ?? null);
        }
        Faculty::create($data);

        if ($returnCollege && $returnDepartment) {
            return redirect()->route('admin.colleges.show-department', [
                'college' => $returnCollege,
                'department' => $returnDepartment,
                'section' => 'faculty',
            ])->with('success', 'Faculty member added successfully.');
        }
        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'faculty'])->with('success', 'Faculty member added successfully.');
        }
        return redirect()->route('admin.faculty.index')->with('success', 'Faculty member added successfully.');
    }

    public function edit(Request $request, Faculty $faculty): View
    {
        $this->authorizeCollege($faculty->college_slug);
        $user = $request->user();
        $colleges = $user->isSuperAdmin() ? CollegeController::getColleges() : [];
        $returnCollege = $request->query('return_college', $faculty->college_slug);
        $returnDepartment = $request->query('return_department');
        
        $departments = \App\Models\CollegeDepartment::orderBy('name')->get();

        return view('admin.faculty.edit', compact('faculty', 'colleges', 'returnCollege', 'returnDepartment', 'departments'));
    }

    public function update(Request $request, Faculty $faculty): RedirectResponse
    {
        $this->authorizeCollege($faculty->college_slug);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'college_slug' => ['nullable', 'string', 'max:80'],
        ]);
        $user = $request->user();
        if ($user->isBoundedToCollege()) {
            $data['college_slug'] = $user->college_slug;
        }
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        if ($request->hasFile('photo')) {
            $data['photo'] = $this->storeFacultyPhoto($request->file('photo'), $data['college_slug'], $data['department'] ?? null);
        } else {
            unset($data['photo']);
        }
        $faculty->update($data);

        $returnCollege = $request->input('return_college');
        $returnDepartment = $request->input('return_department');
        if ($returnCollege && $returnDepartment) {
            return redirect()->route('admin.colleges.show-department', [
                'college' => $returnCollege,
                'department' => $returnDepartment,
                'section' => 'faculty',
            ])->with('success', 'Faculty member updated successfully.');
        }
        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'faculty'])->with('success', 'Faculty member updated successfully.');
        }
        return redirect()->route('admin.faculty.index')->with('success', 'Faculty member updated successfully.');
    }

    public function destroy(Request $request, Faculty $faculty): RedirectResponse
    {
        $this->authorizeCollege($faculty->college_slug);
        $returnCollege = $request->input('return_college', $faculty->college_slug);
        $returnDepartment = $request->input('return_department');
        $faculty->delete();

        if ($returnCollege && $returnDepartment) {
            return redirect()->route('admin.colleges.show-department', [
                'college' => $returnCollege,
                'department' => $returnDepartment,
                'section' => 'faculty',
            ])->with('success', 'Faculty member deleted successfully.');
        }
        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'faculty'])->with('success', 'Faculty member deleted successfully.');
        }
        return redirect()->route('admin.faculty.index')->with('success', 'Faculty member deleted successfully.');
    }

    private function authorizeCollege(?string $collegeSlug): void
    {
        if (! request()->user()->canAccessCollege($collegeSlug)) {
            abort(403, 'You do not have access to this faculty record.');
        }
    }

    private function renderCreateView(Request $request, ?string $collegeSlug, bool $fromCollegeSection): View|RedirectResponse
    {
        $user = $request->user();
        $colleges = $user->isSuperAdmin() ? CollegeController::getColleges() : [];

        if ($fromCollegeSection && $user->isBoundedToCollege()) {
            $collegeSlug = $user->college_slug;
        }

        if ($collegeSlug !== null && ! array_key_exists($collegeSlug, CollegeController::getColleges())) {
            abort(404);
        }

        $departmentsQuery = \App\Models\CollegeDepartment::query()->orderBy('name');
        if ($collegeSlug) {
            $departmentsQuery->where('college_slug', $collegeSlug);
        }

        $departments = $departmentsQuery->get();
        $selectedDepartment = $this->resolveSelectedDepartment($request, $departments, $collegeSlug);
        $returnDepartment = $request->query('return_department');

        return view('admin.faculty.create', compact(
            'colleges',
            'collegeSlug',
            'fromCollegeSection',
            'departments',
            'selectedDepartment',
            'returnDepartment'
        ));
    }

    private function resolveSelectedDepartment(Request $request, Collection $departments, ?string $collegeSlug): ?string
    {
        $selectedDepartment = $request->query('department');
        if ((string) $selectedDepartment === '') {
            return null;
        }

        $matchingDepartment = $departments->firstWhere('name', $selectedDepartment);
        if ($matchingDepartment) {
            return $matchingDepartment->name;
        }

        if (! $collegeSlug) {
            $matchingDepartment = \App\Models\CollegeDepartment::where('name', $selectedDepartment)->first();
            if ($matchingDepartment) {
                return $matchingDepartment->name;
            }
        }

        return null;
    }

    private function storeFacultyPhoto(\Illuminate\Http\UploadedFile $file, ?string $collegeSlug, ?string $department = null): string
    {
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $name = Str::slug($originalName) . '_' . time() . '.' . $ext;
        
        $folder = 'faculty';
        if ($collegeSlug && $department) {
            $folder = "colleges/{$collegeSlug}/departments/" . Str::slug($department) . '/faculty';
        } elseif ($collegeSlug) {
            $folder = "colleges/{$collegeSlug}/faculty";
        }

        $path = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs($folder, $file, $name);

        return $path;
    }
}
