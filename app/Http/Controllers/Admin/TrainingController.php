<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollegeTraining;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TrainingController extends Controller
{
    private function activityFolder(string $college, string $title): string
    {
        return 'colleges/' . $college . '/training/' . Str::slug($title);
    }

    private function storeTrainingImage(Request $request, string $college, string $title): ?string
    {
        if (!$request->hasFile('image')) {
            return null;
        }

        $file = $request->file('image');
        $folder = $this->activityFolder($college, $title);
        $filename = time() . '_train_' . Str::slug($title) . '.' . $file->getClientOriginalExtension();

        return Storage::disk('google')->putFileAs($folder, $file, $filename) ?: null;
    }

    private function managedTrainingFolder(?string $imagePath): ?string
    {
        if (!is_string($imagePath) || !str_starts_with(ltrim($imagePath, '/'), 'colleges/')) {
            return null;
        }

        $path = ltrim($imagePath, '/');
        if (!str_contains($path, '/training/')) {
            return null;
        }

        return dirname($path);
    }

    private function deleteTrainingAsset(?string $imagePath, bool $deleteParentFolder = false): void
    {
        if (empty($imagePath)) {
            return;
        }

        $drive = app(\App\Services\GoogleDriveService::class);
        $managedFolder = $this->managedTrainingFolder($imagePath);

        if ($managedFolder && $deleteParentFolder) {
            $drive->deleteFolder($managedFolder);
            return;
        }

        $drive->delete($imagePath);
    }

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

        $trainings = CollegeTraining::where('college_slug', $college)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.trainings.index', compact('trainings', 'college', 'collegeName'));
    }

    public function create(Request $request, string $college): View
    {
        $college = $this->resolveCollege($request, $college);
        $colleges = CollegeController::getColleges();
        $collegeName = $colleges[$college] ?? $college;

        return view('admin.trainings.form', [
            'training' => null,
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
            $imagePath = $this->storeTrainingImage($request, $college, $validated['title']);
        }

        $maxSort = CollegeTraining::where('college_slug', $college)->max('sort_order') ?? 0;

        CollegeTraining::create([
            'college_slug' => $college,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image' => $imagePath,
            'sort_order' => $maxSort + 1,
        ]);

        return redirect()->route('admin.colleges.show', ['college' => $college, 'section' => 'training'])
            ->with('success', 'Training activity added successfully.');
    }

    public function edit(Request $request, string $college, CollegeTraining $training): View
    {
        $college = $this->resolveCollege($request, $college);
        $colleges = CollegeController::getColleges();
        $collegeName = $colleges[$college] ?? $college;

        return view('admin.trainings.form', compact('training', 'college', 'collegeName'));
    }

    public function update(Request $request, string $college, CollegeTraining $training): RedirectResponse
    {
        $college = $this->resolveCollege($request, $college);
        $oldImage = $training->image;
        $oldFolder = $this->managedTrainingFolder($oldImage);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $this->storeTrainingImage($request, $college, $validated['title']);

            if (!empty($validated['image']) && $oldImage && $oldImage !== $validated['image']) {
                $newFolder = $this->managedTrainingFolder($validated['image']);
                $deleteParentFolder = $oldFolder !== null && $oldFolder !== $newFolder;
                $this->deleteTrainingAsset($oldImage, $deleteParentFolder);
            }
        }

        $training->update($validated);

        return redirect()->route('admin.colleges.show', ['college' => $college, 'section' => 'training'])
            ->with('success', 'Training activity updated successfully.');
    }

    public function destroy(Request $request, string $college, CollegeTraining $training): RedirectResponse
    {
        $college = $this->resolveCollege($request, $college);
        $this->deleteTrainingAsset($training->image, $this->managedTrainingFolder($training->image) !== null);
        $training->delete();

        return redirect()->route('admin.colleges.show', ['college' => $college, 'section' => 'training'])
            ->with('success', 'Training activity deleted successfully.');
    }
}
