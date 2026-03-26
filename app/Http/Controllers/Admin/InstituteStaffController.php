<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstituteStaff;
use App\Models\CollegeInstitute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InstituteStaffController extends Controller
{
    public function create(Request $request): View
    {
        $instituteId = $request->query('institute');
        $returnCollege = $request->query('return_college');
        $user = $request->user();

        $institute = $instituteId ? CollegeInstitute::findOrFail($instituteId) : null;
        $collegeSlug = $institute?->college_slug;

        if (! $collegeSlug && $user->isBoundedToCollege()) {
            $collegeSlug = $user->college_slug;
        }

        if (! $collegeSlug && is_string($returnCollege) && $returnCollege !== '') {
            $collegeSlug = $returnCollege;
        }

        $this->authorizeCollege($collegeSlug);

        $returnCollege = $returnCollege ?: $collegeSlug;
        $institutes = $collegeSlug
            ? CollegeInstitute::where('college_slug', $collegeSlug)->orderBy('name')->get()
            : collect();

        return view('admin.institutes.staff.create', compact('institute', 'institutes', 'collegeSlug', 'returnCollege'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'institute_id' => ['nullable', 'exists:college_institutes,id'],
            'college_slug' => ['nullable', 'string', 'max:80'],
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $institute = ! empty($data['institute_id']) ? CollegeInstitute::findOrFail($data['institute_id']) : null;
        $collegeSlug = $institute?->college_slug ?? ($data['college_slug'] ?? $request->input('return_college'));
        $this->authorizeCollege($collegeSlug);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['college_slug'] = $collegeSlug;
        
        if ($request->hasFile('photo')) {
            $data['photo'] = $this->storeStaffPhoto($request->file('photo'), $collegeSlug);
        }

        InstituteStaff::create($data);

        $returnCollege = $request->input('return_college', $collegeSlug);
        return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'faculty'])
            ->with('success', 'Institute staff member added successfully.');
    }

    public function edit(Request $request, InstituteStaff $instituteStaff): View
    {
        $institute = $instituteStaff->institute;
        $collegeSlug = $institute?->college_slug ?? $instituteStaff->college_slug;
        $this->authorizeCollege($collegeSlug);

        $returnCollege = $request->query('return_college', $collegeSlug);
        $institutes = $collegeSlug
            ? CollegeInstitute::where('college_slug', $collegeSlug)->orderBy('name')->get()
            : collect();

        return view('admin.institutes.staff.edit', compact('instituteStaff', 'institute', 'institutes', 'collegeSlug', 'returnCollege'));
    }

    public function update(Request $request, InstituteStaff $instituteStaff): RedirectResponse
    {
        $existingInstitute = $instituteStaff->institute;
        $existingCollegeSlug = $existingInstitute?->college_slug ?? $instituteStaff->college_slug;
        $this->authorizeCollege($existingCollegeSlug);

        $data = $request->validate([
            'institute_id' => ['nullable', 'exists:college_institutes,id'],
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $institute = ! empty($data['institute_id']) ? CollegeInstitute::findOrFail($data['institute_id']) : null;
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['college_slug'] = $institute?->college_slug ?? $existingCollegeSlug;

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->storeStaffPhoto($request->file('photo'), $data['college_slug']);
        }

        $instituteStaff->update($data);

        $returnCollege = $request->input('return_college', $data['college_slug']);
        return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'faculty'])
            ->with('success', 'Institute staff member updated successfully.');
    }

    public function destroy(Request $request, InstituteStaff $instituteStaff): RedirectResponse
    {
        $institute = $instituteStaff->institute;
        $collegeSlug = $institute?->college_slug ?? $instituteStaff->college_slug;
        $this->authorizeCollege($collegeSlug);
        
        $returnCollege = $request->input('return_college', $collegeSlug);
        $instituteStaff->delete();

        return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'faculty'])
            ->with('success', 'Institute staff member deleted successfully.');
    }

    private function authorizeCollege(?string $collegeSlug): void
    {
        if (! request()->user()->canAccessCollege($collegeSlug)) {
            abort(403, 'You do not have access to this institute staff record.');
        }
    }

    private function storeStaffPhoto(\Illuminate\Http\UploadedFile $file, ?string $collegeSlug): string
    {
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $name = Str::random(16);
        
        if ($collegeSlug) {
            $name = $collegeSlug . '__' . $name;
        }

        $name .= '.' . $ext;
        
        $path = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs('faculty', $file, $name);
        return \Illuminate\Support\Facades\Storage::disk('google')->url($path);
    }
}
