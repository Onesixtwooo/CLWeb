<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\CollegeDepartment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $sort = $request->string('sort')->toString();
        $allowedSorts = ['latest', 'oldest', 'title_asc', 'title_desc'];

        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'latest';
        }

        $query = Announcement::query();
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

        match ($sort) {
            'oldest' => $query->orderByRaw('COALESCE(published_at, created_at) asc'),
            'title_asc' => $query->orderBy('title'),
            'title_desc' => $query->orderByDesc('title'),
            default => $query->orderByRaw('COALESCE(published_at, created_at) desc'),
        };

        $announcements = $query->paginate(15)->withQueryString();

        return view('admin.announcements.index', compact('announcements', 'sort'));
    }

    public function create(): View
    {
        $user = request()->user();
        $colleges = $user->isSuperAdmin() ? CollegeController::getColleges() : [];
        $departments = $user->isSuperAdmin()
            ? CollegeDepartment::query()->orderBy('college_slug')->orderBy('name')->get(['college_slug', 'name'])
            : CollegeDepartment::query()
                ->where('college_slug', $user->college_slug)
                ->orderBy('name')
                ->get(['college_slug', 'name']);

        return view('admin.announcements.create', compact('colleges', 'departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'author' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'college_slug' => ['nullable', 'string', 'max:80'],
            'department_name' => ['nullable', 'string', 'max:180'],
            'banner' => ['nullable', 'array'],
            'banner.*' => ['nullable', 'image', 'max:5120'],
            'banner_dark' => ['nullable', 'boolean'],
        ]);
        $user = $request->user();
        if ($user->isBoundedToCollege()) {
            $data['college_slug'] = $user->college_slug;
            if ($user->isBoundedToDepartment()) {
                $data['department_name'] = $user->department;
            } elseif (empty($data['department_name'])) {
                $data['department_name'] = null;
            }
        } elseif (empty($data['college_slug']) && $user->isSuperAdmin()) {
            $data['college_slug'] = null;
            $data['department_name'] = null;
        } elseif (empty($data['department_name'])) {
            $data['department_name'] = null;
        }
        $data['author'] = $data['author'] ?: $user->name;
        $data['slug'] = Announcement::generateUniqueSlug($data['title']);
        $data['user_id'] = $user->id;

        // Process direct uploads
        $allImages = [];
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
        $departments = $user->isSuperAdmin()
            ? CollegeDepartment::query()->orderBy('college_slug')->orderBy('name')->get(['college_slug', 'name'])
            : CollegeDepartment::query()
                ->where('college_slug', $user->college_slug)
                ->orderBy('name')
                ->get(['college_slug', 'name']);

        return view('admin.announcements.edit', compact('announcement', 'colleges', 'departments'));
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
            'department_name' => ['nullable', 'string', 'max:180'],
            'banner' => ['nullable', 'array'],
            'banner.*' => ['nullable', 'image', 'max:5120'],
            'banner_dark' => ['nullable', 'boolean'],
            'clear_images' => ['nullable', 'boolean'],
        ]);
        $user = $request->user();
        if ($user->isBoundedToCollege()) {
            $data['college_slug'] = $user->college_slug;
            if ($user->isBoundedToDepartment()) {
                $data['department_name'] = $user->department;
            } elseif (empty($data['department_name'])) {
                $data['department_name'] = null;
            }
        } elseif (empty($data['college_slug'])) {
            $data['college_slug'] = null;
            $data['department_name'] = null;
        } elseif (empty($data['department_name'])) {
            $data['department_name'] = null;
        }
        $data['author'] = $data['author'] ?: $announcement->author ?: $user->name;
        $data['slug'] = Announcement::generateUniqueSlug($data['title'], $announcement->id);

        // Process images
        $allImages = $announcement->images ?? ($announcement->image ? [$announcement->image] : []);
        if ($request->boolean('clear_images')) {
            $allImages = [];
        }

        $newImages = [];
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
