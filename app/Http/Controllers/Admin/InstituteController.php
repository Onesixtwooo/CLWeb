<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollegeInstitute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

use Illuminate\Support\Facades\Log;

class InstituteController extends Controller
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

        return view('admin.institutes.create', compact('colleges', 'collegeSlug', 'fromCollegeSection'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
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
        $data['photo'] = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . Str::slug($data['name']) . '.' . $file->getClientOriginalExtension();
            $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs('institutes', $file, $filename);
            if ($imagePath) {
                $data['logo'] = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                $data['photo'] = 'institutes/' . $filename; // Keep legacy
            }
        }
        CollegeInstitute::create($data);

        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'institutes'])->with('success', 'Institute added successfully.');
        }
        return redirect()->route('admin.institutes.index')->with('success', 'Institute added successfully.');
    }

    public function edit(Request $request, CollegeInstitute $institute): View
    {
        $this->authorizeCollege($institute->college_slug);
        $user = $request->user();
        $colleges = $user->isSuperAdmin() ? CollegeController::getColleges() : [];
        $returnCollege = $request->query('return_college', $institute->college_slug);

        return view('admin.institutes.edit', compact('institute', 'colleges', 'returnCollege'));
    }

    public function update(Request $request, CollegeInstitute $institute): RedirectResponse
    {
        $this->authorizeCollege($institute->college_slug);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
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
            // Delete old logo if it was on google or local
            if (!empty($institute->logo)) {
                if (str_contains($institute->logo, 'drive.google.com') || str_contains($institute->logo, 'googleusercontent.com')) {
                    // Handle google delete
                } elseif (file_exists(public_path($institute->logo))) {
                    @unlink(public_path($institute->logo));
                }
            }

            $file = $request->file('photo');
            $filename = time() . '_' . Str::slug($institute->name) . '.' . $file->getClientOriginalExtension();
            $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs('institutes', $file, $filename);
            
            if ($imagePath) {
                $data['logo'] = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
                $data['photo'] = 'institutes/' . $filename; // Keep for legacy
            }
            
            Log::info('InstituteController: Logo uploaded to Google Drive', ['logo' => $data['logo']]);
        } else {
            unset($data['photo']);
            unset($data['logo']);
            // Backfill logo if it's missing but photo exists
            if (empty($institute->logo) && !empty($institute->photo)) {
                $data['logo'] = '/images/' . $institute->photo; // Add leading slash
                Log::info('InstituteController: Backfilling logo from existing photo', ['logo' => $data['logo']]);
            }
        }
        
        $updated = $institute->update($data);
        Log::info('InstituteController: Update result', ['updated' => $updated, 'institute_logo' => $institute->fresh()->logo]);

        $returnCollege = $request->input('return_college');
        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'institutes'])->with('success', 'Institute updated successfully.');
        }
        return redirect()->route('admin.institutes.index')->with('success', 'Institute updated successfully.');
    }

    public function destroy(Request $request, CollegeInstitute $institute): RedirectResponse
    {
        $this->authorizeCollege($institute->college_slug);
        $returnCollege = $request->input('return_college', $institute->college_slug);
        $institute->delete();

        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'institutes'])->with('success', 'Institute deleted successfully.');
        }
        return redirect()->route('admin.institutes.index')->with('success', 'Institute deleted successfully.');
    }

    private function authorizeCollege(?string $collegeSlug): void
    {
        if (! request()->user()->canAccessCollege($collegeSlug)) {
            abort(403, 'You do not have access to this institute record.');
        }
    }

    private function storeInstitutePhoto(\Illuminate\Http\UploadedFile $file): string
    {
        $dir = public_path('images/institutes');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $name = Str::random(16) . '.' . $ext;
        $file->move($dir, $name);
        return 'institutes/' . $name;
    }
}
