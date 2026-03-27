<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FacilityController extends Controller
{
    public function index(Request $request): RedirectResponse
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
            $departments = \App\Models\CollegeDepartment::where('college_slug', $collegeSlug)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->pluck('name', 'name')
                ->toArray();
        }

        return view('admin.facilities.create', compact('colleges', 'collegeSlug', 'fromCollegeSection', 'departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'department_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'college_slug' => ['nullable', 'string', 'max:80'],
        ]);
        $user = $request->user();
        $returnCollege = $request->input('return_college');
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
            $data['photo'] = $this->storeFacilityPhoto($request->file('photo'), $data['name'], $data['college_slug'] ?? null);
        }
        Facility::create($data);

        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'facilities'])->with('success', 'Facility added successfully.');
        }
        return redirect()->route('admin.facilities.index')->with('success', 'Facility added successfully.');
    }

    public function edit(Request $request, Facility $facility): View
    {
        $this->authorizeCollege($facility->college_slug);
        $user = $request->user();
        $colleges = $user->isSuperAdmin() ? CollegeController::getColleges() : [];
        $returnCollege = $request->query('return_college', $facility->college_slug);

        $departments = [];
        if ($facility->college_slug) {
            $departments = \App\Models\CollegeDepartment::where('college_slug', $facility->college_slug)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->pluck('name', 'name')
                ->toArray();
        }

        return view('admin.facilities.edit', compact('facility', 'colleges', 'returnCollege', 'departments'));
    }

    public function update(Request $request, Facility $facility): RedirectResponse
    {
        $this->authorizeCollege($facility->college_slug);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'department_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'college_slug' => ['nullable', 'string', 'max:80'],
            'gallery.*' => ['nullable', 'image', 'max:2048'],
        ]);
        $user = $request->user();
        if ($user->isBoundedToCollege()) {
            $data['college_slug'] = $user->college_slug;
        }
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        if ($request->boolean('remove_photo')) {
            $data['photo'] = null;
        } elseif ($request->hasFile('photo')) {
            $data['photo'] = $this->storeFacilityPhoto($request->file('photo'), $data['name'], $data['college_slug'] ?? $facility->college_slug);
        } else {
            unset($data['photo']);
        }
        $facility->update($data);

        // Handle Gallery Uploads
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $path = $this->storeFacilityPhoto($file, $facility->name, $data['college_slug'] ?? $facility->college_slug);
                $facility->images()->create([
                    'image_path' => $path,
                ]);
            }
        }

        $returnCollege = $request->input('return_college');
        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'facilities'])->with('success', 'Facility updated successfully.');
        }
        return redirect()->route('admin.facilities.index')->with('success', 'Facility updated successfully.');
    }

    public function destroy(Request $request, Facility $facility): RedirectResponse
    {
        $this->authorizeCollege($facility->college_slug);
        $returnCollege = $request->input('return_college', $facility->college_slug);

        $googleDrive = app(\App\Services\GoogleDriveService::class);
        $parentId = null;

        // Delete Main Photo
        if ($facility->photo) {
            $parentId = $googleDrive->getParentIdOfFile($facility->photo);
            $googleDrive->delete($facility->photo);
        }

        // Delete Gallery Images
        foreach ($facility->images as $image) {
            if ($image->image_path) {
                if (!$parentId) {
                    $parentId = $googleDrive->getParentIdOfFile($image->image_path);
                }
                $googleDrive->delete($image->image_path);
            }
            $image->delete();
        }

        $facility->delete();

        if ($parentId) {
            $googleDrive->deleteFolderIfEmpty($parentId);
        }

        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'facilities'])->with('success', 'Facility deleted successfully.');
        }
        return redirect()->route('admin.facilities.index')->with('success', 'Facility deleted successfully.');
    }

    public function destroyImage(Request $request, \App\Models\FacilityImage $facilityImage): RedirectResponse
    {
        $facility = $facilityImage->facility;
        $this->authorizeCollege($facility->college_slug);

        // Delete file if exists
        $path = $facilityImage->image_path;
        if (str_contains($path, 'drive.google.com') || str_contains($path, 'googleusercontent.com')) {
            $googleDrive = app(\App\Services\GoogleDriveService::class);
            $parentId = $googleDrive->getParentIdOfFile($path);
            $googleDrive->delete($path);
            
            if ($parentId) {
                $googleDrive->deleteFolderIfEmpty($parentId);
            }
        } elseif (file_exists(public_path('images/' . $path))) {
            unlink(public_path('images/' . $path));
        }

        $facilityImage->delete();

        return back()->with('success', 'Image removed from gallery.');
    }

    private function authorizeCollege(?string $collegeSlug): void
    {
        if (! request()->user()->canAccessCollege($collegeSlug)) {
            abort(403, 'You do not have access to this facility record.');
        }
    }

    private function storeFacilityPhoto(\Illuminate\Http\UploadedFile $file, string $facilityName, ?string $collegeSlug = null): string
    {
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $name = Str::random(16);

        $user = request()->user();
        if ($user && $user->isBoundedToCollege() && $user->college_slug) {
            $name = $user->college_slug . '__' . $name;
        }

        $name .= '.' . $ext;

        $facilitySlug = Str::slug($facilityName);
        $college = $collegeSlug ?: 'global';
        $directory = "colleges/{$college}/facilities/{$facilitySlug}";

        $path = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs($directory, $file, $name);
        return \Illuminate\Support\Facades\Storage::disk('google')->url($path);
    }
}
