<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Announcement::query()->latest('published_at');
        $user = $request->user();
        if ($user && $user->isBoundedToDepartment()) {
            $query->where('college_slug', $user->college_slug)
                ->whereHas('user', function ($q) use ($user) {
                    $q->where('college_slug', $user->college_slug)
                        ->where('department', $user->department);
                });
        } elseif ($user && $user->isBoundedToCollege()) {
            $query->where(function ($q) use ($user) {
                $q->where('college_slug', $user->college_slug)
                  ->orWhereNull('college_slug');
            });
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('body', 'like', '%'.$request->search.'%');
            });
        }
        $announcements = $query->paginate(15)->withQueryString();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create(): View
    {
        $user = request()->user();
        $colleges = $user->isSuperAdmin() ? CollegeController::getColleges() : [];
        return view('admin.announcements.create', compact('colleges'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'author' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'college_slug' => ['nullable', 'string', 'max:80'],
            'banner' => ['nullable', 'array'],
            'banner.*' => ['nullable', 'image', 'max:5120'],
            'media_images' => ['nullable', 'array'],
            'media_images.*' => ['nullable', 'string'],
            'banner_dark' => ['nullable', 'boolean'],
        ]);
        $user = $request->user();
        if ($user->isBoundedToCollege()) {
            $data['college_slug'] = $user->college_slug;
        } elseif (empty($data['college_slug']) && $user->isSuperAdmin()) {
            $data['college_slug'] = null;
        }
        $data['author'] = $data['author'] ?: $user->name;
        $data['slug'] = Str::slug($data['title']);
        while (Announcement::where('slug', $data['slug'])->exists()) {
            $data['slug'] = Str::slug($data['title']).'-'.strtolower(Str::random(4));
        }
        $data['user_id'] = $user->id;

        // Process images
        $allImages = [];
        // 1. Files from media library
        if ($request->filled('media_images')) {
            foreach ($request->input('media_images') as $path) {
                if (\Illuminate\Support\Facades\Storage::disk('google')->exists($path)) {
                    $allImages[] = \Illuminate\Support\Facades\Storage::disk('google')->url($path);
                }
            }
        }
        // 2. Direct uploads
        if ($request->hasFile('banner')) {
            foreach ($request->file('banner') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $filename = Str::slug($originalName) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs('announcements/' . Str::slug($data['title']), $file, $filename);
                if ($path) {
                    $allImages[] = \Illuminate\Support\Facades\Storage::disk('google')->url($path);
                }
            }
        }

        if (!empty($allImages)) {
            $data['image'] = $allImages[0];
            $data['images'] = $allImages;
        }

        Announcement::create($data);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement created successfully.');
    }

    public function edit(Announcement $announcement): View
    {
        $this->authorizeManage($announcement->college_slug);
        $user = request()->user();
        $colleges = $user->isSuperAdmin() ? CollegeController::getColleges() : [];
        return view('admin.announcements.edit', compact('announcement', 'colleges'));
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $this->authorizeManage($announcement->college_slug);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'author' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'college_slug' => ['nullable', 'string', 'max:80'],
            'banner' => ['nullable', 'array'],
            'banner.*' => ['nullable', 'image', 'max:5120'],
            'media_images' => ['nullable', 'array'],
            'media_images.*' => ['nullable', 'string'],
            'banner_dark' => ['nullable', 'boolean'],
            'clear_images' => ['nullable', 'boolean'],
        ]);
        $user = $request->user();
        if ($user->isBoundedToCollege()) {
            $data['college_slug'] = $user->college_slug;
        }
        $data['author'] = $data['author'] ?: $announcement->author ?: $user->name;

        // Process images
        $allImages = $announcement->images ?? ($announcement->image ? [$announcement->image] : []);
        if ($request->boolean('clear_images')) {
            $allImages = [];
        }

        $newImages = [];
        // 1. Files from media library
        if ($request->filled('media_images')) {
            foreach ($request->input('media_images') as $path) {
                if (\Illuminate\Support\Facades\Storage::disk('google')->exists($path)) {
                    $newImages[] = \Illuminate\Support\Facades\Storage::disk('google')->url($path);
                }
            }
        }
        // 2. Direct uploads
        if ($request->hasFile('banner')) {
            foreach ($request->file('banner') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $filename = Str::slug($originalName) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs('announcements/' . Str::slug($data['title']), $file, $filename);
                if ($path) {
                    $newImages[] = \Illuminate\Support\Facades\Storage::disk('google')->url($path);
                }
            }
        }

        if (!empty($newImages)) {
            $allImages = array_merge($allImages, $newImages);
        }

        if (!empty($allImages)) {
            $data['image'] = $allImages[0];
            $data['images'] = $allImages;
        } elseif ($request->boolean('clear_images')) {
            $data['image'] = null;
            $data['images'] = null;
        }

        $announcement->update($data);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $this->authorizeManage($announcement->college_slug);
        $announcement->delete();
        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted successfully.');
    }

    private function authorizeManage(?string $collegeSlug): void
    {
        $announcement = request()->route('announcement');

        if ($announcement instanceof Announcement) {
            if (! request()->user()->canManageAnnouncement($announcement)) {
                abort(403, 'You do not have permission to manage this announcement.');
            }

            return;
        }

        if (! request()->user()->canManageCollegeContent($collegeSlug)) {
            abort(403, 'You do not have permission to manage this announcement.');
        }
    }
}
