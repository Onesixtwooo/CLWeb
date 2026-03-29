<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacebookConfig;
use App\Models\College;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use Illuminate\Routing\Controllers\HasMiddleware;

class FacebookConfigController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            function ($request, $next) {
                $user = auth()->user();
                $settingKey = 'facebook_integration_enabled' . ($user && $user->college_slug ? '_' . $user->college_slug : '');
                
                if (\App\Models\Setting::get($settingKey, '1') == '0') {
                    return redirect()->route('admin.settings.index')
                        ->with('error', 'Facebook integration is disabled for your college.');
                }
                return $next($request);
            },
        ];
    }

    public function index(): View
    {
        $user = auth()->user();
        $query = FacebookConfig::query();

        if ($user->isBoundedToCollege()) {
            $query->where('entity_type', 'college')
                  ->where('entity_id', $user->college_slug);
        }

        $configs = $query->get();
        $colleges = College::all()->keyBy('slug');

        return view('admin.facebook.index', compact('configs', 'colleges'));
    }

    public function create(): View
    {
        $colleges = College::orderBy('name')->get();
        $entityTypes = ['college'];

        return view('admin.facebook.create', compact('colleges', 'entityTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $isBounded = $user->isBoundedToCollege();

        $validated = $request->validate([
            'entity_type' => [$isBounded ? 'nullable' : 'required', 'in:college'],
            'entity_id' => [$isBounded ? 'nullable' : 'required', 'string'],
            'page_name' => [$isBounded ? 'nullable' : 'required', 'string', 'max:255'],
            'page_id' => ['required', 'string', 'max:255'],
            'access_token' => ['required', 'string'],
            'fetch_limit' => ['required', 'integer', 'min:1', 'max:100'],
            'article_category' => ['nullable', 'string', 'max:255'],
            'article_author' => ['nullable', 'string', 'max:255'],
        ]);

        if (str_contains($validated['access_token'], '...')) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'access_token' => 'Your Access Token is truncated (contains "..."). Please copy the FULL 200+ character string from Meta instead!'
            ]);
        }

        if ($isBounded) {
            $validated['entity_type'] = 'college';
            $validated['entity_id'] = $user->college_slug;
            $validated['page_name'] = \App\Http\Controllers\Admin\CollegeController::getColleges()[$user->college_slug] ?? $user->college_slug;
        }

        $validated['is_active'] = $request->has('is_active');

        \Illuminate\Support\Facades\Log::info('FacebookConfig Store $validated array:', $validated);

        FacebookConfig::create($validated);

        return redirect()->route('admin.facebook.index')
                       ->with('success', 'Facebook configuration created successfully.');
    }

    public function edit(FacebookConfig $facebook): View
    {
        $colleges = College::orderBy('name')->get();
        $entityTypes = ['college'];

        return view('admin.facebook.edit', ['facebookConfig' => $facebook, 'colleges' => $colleges, 'entityTypes' => $entityTypes]);
    }

    public function update(Request $request, FacebookConfig $facebook): RedirectResponse
    {
        $user = auth()->user();
        $isBounded = $user->isBoundedToCollege();

        $validated = $request->validate([
            'entity_type' => [$isBounded ? 'nullable' : 'required', 'in:college'],
            'entity_id' => [$isBounded ? 'nullable' : 'required', 'string'],
            'page_name' => [$isBounded ? 'nullable' : 'required', 'string', 'max:255'],
            'page_id' => ['required', 'string', 'max:255'],
            'access_token' => ['required', 'string'],
            'fetch_limit' => ['required', 'integer', 'min:1', 'max:100'],
            'article_category' => ['nullable', 'string', 'max:255'],
            'article_author' => ['nullable', 'string', 'max:255'],
        ]);

        if (str_contains($validated['access_token'], '...')) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'access_token' => 'Your Access Token is truncated (contains "..."). Please copy the FULL 200+ character string from Meta instead!'
            ]);
        }

        if ($isBounded) {
            $validated['entity_type'] = 'college';
            $validated['entity_id'] = $user->college_slug;
            $validated['page_name'] = \App\Http\Controllers\Admin\CollegeController::getColleges()[$user->college_slug] ?? $user->college_slug;
        }

        $validated['is_active'] = $request->has('is_active');

        \Illuminate\Support\Facades\Log::info('FacebookConfig Update $validated array:', $validated);

        // Force raw query builder update to bypass any Eloquent state caches
        \Illuminate\Support\Facades\DB::table('facebook_configs')
            ->where('id', $facebook->id)
            ->update($validated);

        return redirect()->route('admin.facebook.index')
                       ->with('success', 'Facebook configuration updated successfully.');
    }

    public function destroy(FacebookConfig $facebook): RedirectResponse
    {
        $facebook->delete();

        return redirect()->route('admin.facebook.index')
                       ->with('success', 'Facebook configuration deleted successfully.');
    }

    /**
     * Sync Facebook posts manually.
     */
    public function sync(): RedirectResponse
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('facebook:fetch-posts', ['--use-db' => true]);
            return redirect()->route('admin.facebook.index')
                           ->with('success', 'Facebook posts synchronized successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.facebook.index')
                           ->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }
}
