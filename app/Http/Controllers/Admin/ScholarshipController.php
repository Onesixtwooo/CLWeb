<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ScholarshipController extends Controller
{
    /**
     * Get the college slug for the current user, or from the route.
     */
    private function resolveCollege(Request $request, ?string $college = null): string
    {
        $user = $request->user();

        // If college param provided, validate access
        if ($college) {
            // Allow _global for superadmins (global scholarships)
            if ($college === '_global') {
                if (!$user || !$user->isSuperAdmin()) {
                    abort(403, 'Only superadmins can manage global scholarships.');
                }
                return '_global';
            }

            $colleges = CollegeController::getColleges();
            if (!isset($colleges[$college])) {
                abort(404, 'College not found.');
            }
            if ($user && !$user->canAccessCollege($college)) {
                abort(403, 'You do not have access to this college.');
            }
            return $college;
        }

        // Fall back to user's bounded college
        if ($user && $user->isBoundedToCollege()) {
            return $user->college_slug;
        }

        abort(404, 'College not specified.');
    }

    public function index(Request $request, string $college): View
    {
        $college = $this->resolveCollege($request, $college);
        $colleges = CollegeController::getColleges();
        $isGlobalView = ($college === '_global');

        $collegeName = $colleges[$college] ?? ($isGlobalView ? 'All Scholarships' : $college);

        $query = Scholarship::query();

        if (!$isGlobalView) {
            $query->whereIn('college_slug', [$college, '_global']);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $query->orderByRaw("CASE WHEN college_slug = '_global' THEN 0 ELSE 1 END")
            ->orderBy('college_slug')
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc');

        $scholarships = $query->paginate(20)->withQueryString();

        return view('admin.scholarships.index', compact('scholarships', 'college', 'collegeName', 'colleges', 'isGlobalView'));
    }

    public function create(Request $request, string $college): View
    {
        $college = $this->resolveCollege($request, $college);
        $colleges = CollegeController::getColleges();
        $collegeName = $colleges[$college] ?? $college;

        return view('admin.scholarships.form', [
            'scholarship' => null,
            'college' => $college,
            'collegeName' => $collegeName,
        ]);
    }

    public function store(Request $request, string $college): RedirectResponse
    {
        $college = $this->resolveCollege($request, $college);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'qualifications' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'process' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'media_image' => ['nullable', 'string'],
        ]);

        $user = $request->user();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_scholarship_' . Str::slug($validated['title']) . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs('scholarships/' . Str::slug($validated['title']), $file, $filename);
            if ($imagePath) {
                $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
            }
        } elseif ($request->filled('media_image')) {
            $cleanPath = ltrim($request->input('media_image'), '/');
            if (\Illuminate\Support\Facades\Storage::disk('google')->exists($cleanPath)) {
                $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->url($cleanPath);
            }
        }

        $maxSort = Scholarship::where('college_slug', $college)->max('sort_order') ?? 0;

        Scholarship::create([
            'college_slug' => $college,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'qualifications' => $validated['qualifications'],
            'requirements' => $validated['requirements'],
            'process' => $validated['process'],
            'benefits' => $validated['benefits'],
            'image' => $imagePath,
            'added_by' => $user->isSuperAdmin() ? 'superadmin' : 'admin',
            'user_id' => $user->id,
            'sort_order' => $maxSort + 1,
        ]);

        $redirectRoute = $college === '_global'
            ? route('admin.scholarships.index')
            : route('admin.colleges.scholarships.index', ['college' => $college]);

        return redirect($redirectRoute)->with('success', 'Scholarship created successfully.');
    }

    public function edit(Request $request, string $college, Scholarship $scholarship): View
    {
        $college = $this->resolveCollege($request, $college);
        $colleges = CollegeController::getColleges();
        $collegeName = $colleges[$college] ?? $college;

        $user = $request->user();
        if ($scholarship->isLockedFor($user)) {
            abort(403, 'This scholarship was added by a Super Admin and cannot be edited.');
        }

        return view('admin.scholarships.form', compact('scholarship', 'college', 'collegeName'));
    }

    public function editGlobal(Request $request, Scholarship $scholarship): View
    {
        return $this->edit($request, '_global', $scholarship);
    }

    public function update(Request $request, string $college, Scholarship $scholarship): RedirectResponse
    {
        $college = $this->resolveCollege($request, $college);

        $user = $request->user();
        if ($scholarship->isLockedFor($user)) {
            abort(403, 'This scholarship was added by a Super Admin and cannot be edited.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'qualifications' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'process' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'media_image' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if it was on google
            if ($scholarship->image && str_contains($scholarship->image, 'drive.google.com') || str_contains($scholarship->image, 'googleusercontent.com')) {
                // Extract ID or handled by disk
                // For now, we'll just overwrite/upload new
            } elseif ($scholarship->image && file_exists(public_path($scholarship->image))) {
                unlink(public_path($scholarship->image));
            }

            $file = $request->file('image');
            $filename = time() . '_scholarship_' . Str::slug($validated['title']) . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $imagePath = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs('scholarships/' . Str::slug($scholarship->title), $file, $filename);
            if ($imagePath) {
                $validated['image'] = \Illuminate\Support\Facades\Storage::disk('google')->url($imagePath);
            }
        } elseif ($request->filled('media_image')) {
            $cleanPath = ltrim($request->input('media_image'), '/');
            if (\Illuminate\Support\Facades\Storage::disk('google')->exists($cleanPath)) {
                $validated['image'] = \Illuminate\Support\Facades\Storage::disk('google')->url($cleanPath);
            } else {
                unset($validated['image']);
            }
        } else {
            unset($validated['image']); // Keep existing
        }

        $scholarship->update($validated);

        $redirectRoute = $college === '_global'
            ? route('admin.scholarships.index')
            : route('admin.colleges.scholarships.index', ['college' => $college]);

        return redirect($redirectRoute)->with('success', 'Scholarship updated successfully.');
    }

    public function updateGlobal(Request $request, Scholarship $scholarship): RedirectResponse
    {
        return $this->update($request, '_global', $scholarship);
    }

    public function destroy(Request $request, string $college, Scholarship $scholarship): RedirectResponse
    {
        $college = $this->resolveCollege($request, $college);

        $user = $request->user();
        if ($scholarship->isLockedFor($user)) {
            abort(403, 'This scholarship was added by a Super Admin and cannot be deleted.');
        }

        // Delete image file
        if ($scholarship->image) {
            if (str_contains($scholarship->image, 'drive.google.com') || str_contains($scholarship->image, 'googleusercontent.com')) {
                // Handle google delete if we have the ID
            } elseif (file_exists(public_path($scholarship->image))) {
                unlink(public_path($scholarship->image));
            }
        }

        $scholarship->delete();

        $redirectRoute = $college === '_global'
            ? route('admin.scholarships.index')
            : route('admin.colleges.scholarships.index', ['college' => $college]);

        return redirect($redirectRoute)->with('success', 'Scholarship deleted successfully.');
    }

    public function destroyGlobal(Request $request, Scholarship $scholarship): RedirectResponse
    {
        return $this->destroy($request, '_global', $scholarship);
    }
}
