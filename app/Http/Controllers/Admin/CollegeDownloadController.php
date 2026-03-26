<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollegeDownload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CollegeDownloadController extends Controller
{
    private function downloadFolder(string $college, string $title): string
    {
        return 'colleges/' . $college . '/files/' . Str::slug($title);
    }

    private function storeDownloadFile(Request $request, string $college, string $title): ?string
    {
        if (! $request->hasFile('file')) {
            return null;
        }

        $file = $request->file('file');
        $filename = time() . '_file_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

        return Storage::disk('google')->putFileAs($this->downloadFolder($college, $title), $file, $filename) ?: null;
    }

    private function managedDownloadFolder(?string $filePath, string $college): ?string
    {
        if (! is_string($filePath) || ! str_starts_with(ltrim($filePath, '/'), 'colleges/' . $college . '/')) {
            return null;
        }

        return dirname(ltrim($filePath, '/'));
    }

    private function deleteDownloadAsset(?string $filePath, ?string $folderPath = null): void
    {
        if (empty($filePath)) {
            return;
        }

        $drive = app(\App\Services\GoogleDriveService::class);

        if ($folderPath) {
            $drive->deleteFolder($folderPath);
            return;
        }

        $drive->delete($filePath);
    }

    private function resolveCollege(Request $request, ?string $college = null): string
    {
        $user = $request->user();
        if ($college) {
            $colleges = CollegeController::getColleges();
            if (! isset($colleges[$college])) {
                abort(404, 'College not found.');
            }
            if ($user && ! $user->canAccessCollege($college)) {
                abort(403, 'You do not have access to this college.');
            }

            return $college;
        }

        if ($user && $user->isBoundedToCollege()) {
            return $user->college_slug;
        }

        abort(404, 'College not specified.');
    }

    public function index(Request $request, string $college): View
    {
        $college = $this->resolveCollege($request, $college);
        $colleges = CollegeController::getColleges();

        $downloads = CollegeDownload::where('college_slug', $college)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.downloads.index', [
            'downloads' => $downloads,
            'college' => $college,
            'collegeName' => $colleges[$college] ?? $college,
            'sections' => CollegeController::getSections(),
            'currentSection' => 'downloads',
        ]);
    }

    public function create(Request $request, string $college): View
    {
        $college = $this->resolveCollege($request, $college);
        $colleges = CollegeController::getColleges();

        return view('admin.downloads.form', [
            'download' => null,
            'college' => $college,
            'collegeName' => $colleges[$college] ?? $college,
        ]);
    }

    public function store(Request $request, string $college): RedirectResponse
    {
        $college = $this->resolveCollege($request, $college);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['required', 'file', 'mimes:pdf,docx,xlsx', 'max:10240'],
            'is_visible' => ['nullable'],
        ]);

        $file = $request->file('file');
        $path = $this->storeDownloadFile($request, $college, $validated['title']);

        if (! $path) {
            return back()->withInput()->with('error', 'File upload failed.');
        }

        $maxSort = CollegeDownload::where('college_slug', $college)->max('sort_order') ?? 0;

        CollegeDownload::create([
            'college_slug' => $college,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'sort_order' => $maxSort + 1,
            'is_visible' => $request->boolean('is_visible', true),
        ]);

        return redirect()
            ->route('admin.colleges.show', ['college' => $college, 'section' => 'downloads'])
            ->with('success', 'Download resource added successfully.');
    }

    public function edit(Request $request, string $college, CollegeDownload $download): View
    {
        $college = $this->resolveCollege($request, $college);
        if ($download->college_slug !== $college) {
            abort(404, 'Download not found.');
        }

        $colleges = CollegeController::getColleges();

        return view('admin.downloads.form', [
            'download' => $download,
            'college' => $college,
            'collegeName' => $colleges[$college] ?? $college,
        ]);
    }

    public function update(Request $request, string $college, CollegeDownload $download): RedirectResponse
    {
        $college = $this->resolveCollege($request, $college);
        if ($download->college_slug !== $college) {
            abort(404, 'Download not found.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf,docx,xlsx', 'max:10240'],
            'is_visible' => ['nullable'],
        ]);

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_visible' => $request->boolean('is_visible'),
        ];
        $oldPath = $download->file_path;
        $oldFolder = $this->managedDownloadFolder($oldPath, $college);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $this->storeDownloadFile($request, $college, $validated['title']);

            if (! $path) {
                return back()->withInput()->with('error', 'File upload failed.');
            }

            $newFolder = $this->managedDownloadFolder($path, $college);
            if ($oldPath && $oldPath !== $path) {
                $this->deleteDownloadAsset($oldPath, $oldFolder !== $newFolder ? $oldFolder : null);
            }

            $data['file_name'] = $file->getClientOriginalName();
            $data['file_path'] = $path;
            $data['mime_type'] = $file->getClientMimeType();
            $data['file_size'] = $file->getSize();
        }

        $download->update($data);

        return redirect()
            ->route('admin.colleges.show', ['college' => $college, 'section' => 'downloads'])
            ->with('success', 'Download resource updated successfully.');
    }

    public function destroy(Request $request, string $college, CollegeDownload $download): RedirectResponse
    {
        $college = $this->resolveCollege($request, $college);
        if ($download->college_slug !== $college) {
            abort(404, 'Download not found.');
        }

        $this->deleteDownloadAsset($download->file_path, $this->managedDownloadFolder($download->file_path, $college));

        $download->delete();

        return redirect()
            ->route('admin.colleges.show', ['college' => $college, 'section' => 'downloads'])
            ->with('success', 'Download resource deleted successfully.');
    }
}
