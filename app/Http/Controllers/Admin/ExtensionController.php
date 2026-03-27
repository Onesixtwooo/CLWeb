<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollegeExtension;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ExtensionController extends Controller
{
    private function resolveCollege(Request $request, ?string $college = null): string
    {
        $user = $request->user();
        if ($college) {
            $colleges = CollegeController::getColleges();
            if (!isset($colleges[$college])) {
                abort(404, 'College not found.');
            }
            if ($user && !$user->canAccessCollege($college)) {
                abort(403, 'You do not have access to this college.');
            }
            return $college;
        }

        if ($user && $user->isBoundedToCollege()) {
            return $user->college_slug;
        }

        abort(404, 'College not specified.');
        return '';
    }

    public function index(Request $request, string $college): View
    {
        $college = $this->resolveCollege($request, $college);
        $colleges = CollegeController::getColleges();
        $collegeName = $colleges[$college] ?? $college;

        $extensions = CollegeExtension::where('college_slug', $college)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.extensions.index', compact('extensions', 'college', 'collegeName'));
    }

    public function create(Request $request, string $college): View
    {
        $college = $this->resolveCollege($request, $college);
        $colleges = CollegeController::getColleges();
        $collegeName = $colleges[$college] ?? $college;

        return view('admin.extensions.form', [
            'extension' => null,
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
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extensionSlug = Str::slug($validated['title']);
            $filename = time() . '_ext_' . $extensionSlug . '.' . $file->getClientOriginalExtension();
            $path = Storage::disk('google')->putFileAs("colleges/{$college}/extension/{$extensionSlug}", $file, $filename);
            if ($path) {
                $fileId = app(\App\Services\GoogleDriveService::class)->getFileId($path);
                $imagePath = 'media/proxy/' . ($fileId ?? $path);
            }
        }

        $maxSort = CollegeExtension::where('college_slug', $college)->max('sort_order') ?? 0;

        CollegeExtension::create([
            'college_slug' => $college,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image' => $imagePath,
            'sort_order' => $maxSort + 1,
        ]);

        return redirect()->route('admin.colleges.show', ['college' => $college, 'section' => 'extension'])
            ->with('success', 'Extension activity added successfully.');
    }

    public function edit(Request $request, string $college, CollegeExtension $extension): View
    {
        $college = $this->resolveCollege($request, $college);
        $colleges = CollegeController::getColleges();
        $collegeName = $colleges[$college] ?? $college;

        return view('admin.extensions.form', compact('extension', 'college', 'collegeName'));
    }

    public function update(Request $request, string $college, CollegeExtension $extension): RedirectResponse
    {
        $college = $this->resolveCollege($request, $college);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extensionSlug = Str::slug($validated['title']);
            $filename = time() . '_ext_' . $extensionSlug . '.' . $file->getClientOriginalExtension();
            $path = Storage::disk('google')->putFileAs("colleges/{$college}/extension/{$extensionSlug}", $file, $filename);
            if ($path) {
                $fileId = app(\App\Services\GoogleDriveService::class)->getFileId($path);
                $validated['image'] = 'media/proxy/' . ($fileId ?? $path);
            }
        }

        $extension->update($validated);

        return redirect()->route('admin.colleges.show', ['college' => $college, 'section' => 'extension'])
            ->with('success', 'Extension activity updated successfully.');
    }

    public function destroy(Request $request, string $college, CollegeExtension $extension): RedirectResponse
    {
        $college = $this->resolveCollege($request, $college);
        $extension->delete();

        return redirect()->route('admin.colleges.show', ['college' => $college, 'section' => 'extension'])
            ->with('success', 'Extension activity deleted successfully.');
    }
}
