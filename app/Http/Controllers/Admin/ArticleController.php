<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\CollegeDepartment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Article::query()->latest('published_at');

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

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('body', 'like', '%' . $request->search . '%');
            });
        }

        $articles = $query->paginate(15)->withQueryString();

        return view('admin.articles.index', compact('articles'));
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

        return view('admin.articles.create', compact('colleges', 'departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:news,announcement'],
            'body' => ['nullable', 'string'],
            'banner' => ['nullable', 'array', 'min:1'], // Now expects an array of files
            'banner.*' => ['image', 'max:2048'],
            'banner_dark' => ['boolean'],
            'category' => ['nullable', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'college_slug' => ['nullable', 'string', 'max:80'],
            'department_name' => ['nullable', 'string', 'max:180'],
        ]);

        $user = $request->user();
        if ($user->isBoundedToCollege()) {
            $validated['college_slug'] = $user->college_slug;
            if ($user->isBoundedToDepartment()) {
                $validated['department_name'] = $user->department;
            } elseif (empty($validated['department_name'])) {
                $validated['department_name'] = null;
            }
        } elseif (empty($validated['college_slug']) && $user->isSuperAdmin()) {
            $validated['college_slug'] = null;
            $validated['department_name'] = null;
        } elseif (empty($validated['department_name'])) {
            $validated['department_name'] = null;
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['user_id'] = $user->id;

        // Ensure unique slug
        $baseSlug = $validated['slug'];
        $count = 0;
        while (Article::where('slug', $validated['slug'])->exists()) {
            $count++;
            $validated['slug'] = $baseSlug . '-' . $count;
        }

        $imagePaths = [];
        if ($request->hasFile('banner')) {
            $files = $request->file('banner');

            foreach ($files as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeName = Str::slug($originalName);
                $timestamp = time();
                
                $filename = $safeName . '_' . $timestamp . '.' . $file->getClientOriginalExtension();
                
                // Add college prefix if bounded
                if ($user->isBoundedToCollege() && $user->college_slug) {
                    $filename = $user->college_slug . '__' . $filename;
                }

                $path = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("articles/{$validated['slug']}", $file, $filename);
                if ($path) {
                    $imagePaths[] = \Illuminate\Support\Facades\Storage::disk('google')->url($path);
                }
            }
        }

        if (!empty($imagePaths)) {
            $validated['banner'] = $imagePaths[0]; // Main banner is the first image
            $validated['images'] = $imagePaths;    // Store all images
        } else {
            $validated['banner'] = null;
            $validated['images'] = [];
        }

        Article::create($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Article created successfully.');
    }

    public function edit(Article $article): View
    {
        $this->authorizeManage($article->college_slug);
        $user = request()->user();
        $colleges = $user->isSuperAdmin() ? CollegeController::getColleges() : [];
        $departments = $user->isSuperAdmin()
            ? CollegeDepartment::query()->orderBy('college_slug')->orderBy('name')->get(['college_slug', 'name'])
            : CollegeDepartment::query()
                ->where('college_slug', $user->college_slug)
                ->orderBy('name')
                ->get(['college_slug', 'name']);

        return view('admin.articles.edit', compact('article', 'colleges', 'departments'));
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $this->authorizeManage($article->college_slug);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:articles,slug,' . $article->id],
            'type' => ['required', 'in:news,announcement'],
            'body' => ['nullable', 'string'],
            'banner' => ['nullable', 'array'],
            'banner.*' => ['image', 'max:2048'],
            'banner_dark' => ['boolean'],
            'category' => ['nullable', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'college_slug' => ['nullable', 'string', 'max:80'],
            'department_name' => ['nullable', 'string', 'max:180'],
        ]);

        $user = $request->user();
        if ($user->isBoundedToCollege()) {
            $validated['college_slug'] = $user->college_slug;
            if ($user->isBoundedToDepartment()) {
                $validated['department_name'] = $user->department;
            } elseif (empty($validated['department_name'])) {
                $validated['department_name'] = null;
            }
        } elseif (empty($validated['college_slug'])) {
            $validated['college_slug'] = null;
            $validated['department_name'] = null;
        } elseif (empty($validated['department_name'])) {
            $validated['department_name'] = null;
        }

        $currentImages = $article->images ?? [];
        if (is_string($currentImages)) {
             if (empty($currentImages) && $article->banner) {
                 $currentImages = [$article->banner];
             }
        }

        // Check if user wants to clear existing images
        if ($request->has('clear_images') && $request->clear_images == '1') {
            $currentImages = [];
        }

        $newImagePaths = [];

        // Handle traditional file uploads
        if ($request->hasFile('banner')) {
            $files = $request->file('banner');

            foreach ($files as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $filename = Str::slug($originalName) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = \Illuminate\Support\Facades\Storage::disk('google')->putFileAs("articles/{$article->slug}", $file, $filename);
                if ($path) {
                    $newImagePaths[] = \Illuminate\Support\Facades\Storage::disk('google')->url($path);
                }
            }
        }

        $allImages = array_merge($currentImages, $newImagePaths);
        
        $validated['images'] = $allImages;
        $validated['banner'] = !empty($allImages) ? $allImages[0] : null;

        $article->update($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Article updated successfully.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $this->authorizeManage($article->college_slug);

        // Delete associated files from Google Drive
        try {
            $folderPath = "articles/{$article->slug}";
            
            // Delete files file-by-file because some Drive drivers don't delete nested folders 
            $files = \Illuminate\Support\Facades\Storage::disk('google')->allFiles($folderPath);
            foreach ($files as $file) {
                \Illuminate\Support\Facades\Storage::disk('google')->delete($file);
            }
            
            // Delete folder directory wrapper itself
            \Illuminate\Support\Facades\Storage::disk('google')->deleteDirectory($folderPath);
            \Illuminate\Support\Facades\Storage::disk('google')->delete($folderPath);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Failed to delete article images from Google Drive: " . $e->getMessage());
        }

        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Article deleted successfully.');
    }

    private function authorizeManage(?string $collegeSlug): void
    {
        $article = request()->route('article');

        if ($article instanceof Article) {
            if (! request()->user()->canManageArticle($article)) {
                abort(403, 'You do not have permission to manage this article.');
            }

            return;
        }

        if (! request()->user()->canManageCollegeContent($collegeSlug)) {
            abort(403, 'You do not have permission to manage this article.');
        }
    }
}
