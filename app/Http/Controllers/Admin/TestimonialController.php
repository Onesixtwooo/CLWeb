<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollegeTestimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TestimonialController extends Controller
{
    public function index(): RedirectResponse
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

        return view('admin.testimonials.create', compact('colleges', 'collegeSlug', 'fromCollegeSection'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'degree' => ['nullable', 'string', 'max:255'],
            'quote' => ['required', 'string'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'college_slug' => ['nullable', 'string', 'max:80'],
            'is_visible' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $returnCollege = $request->input('return_college');

        if ($user->isBoundedToCollege()) {
            $data['college_slug'] = $user->college_slug;
        } elseif (empty($data['college_slug']) && $returnCollege) {
            $data['college_slug'] = $returnCollege;
        }

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_visible'] = $request->has('is_visible');

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . Str::slug($data['name']) . '.' . $file->getClientOriginalExtension();
            $path = Storage::disk('google')->putFileAs('testimonials', $file, $filename);
            $data['photo'] = Storage::disk('google')->url($path);
        }

        CollegeTestimonial::create($data);

        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'testimonials'])
                ->with('success', 'Testimonial added successfully.');
        }

        return redirect()->route('admin.colleges.index')->with('success', 'Testimonial added successfully.');
    }

    public function edit(Request $request, CollegeTestimonial $testimonial): View
    {
        $this->authorizeCollege($testimonial->college_slug);
        $user = $request->user();
        $colleges = $user->isSuperAdmin() ? CollegeController::getColleges() : [];
        $returnCollege = $request->query('return_college', $testimonial->college_slug);

        return view('admin.testimonials.edit', compact('testimonial', 'colleges', 'returnCollege'));
    }

    public function update(Request $request, CollegeTestimonial $testimonial): RedirectResponse
    {
        $this->authorizeCollege($testimonial->college_slug);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'degree' => ['nullable', 'string', 'max:255'],
            'quote' => ['required', 'string'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'college_slug' => ['nullable', 'string', 'max:80'],
            'is_visible' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        if ($user->isBoundedToCollege()) {
            $data['college_slug'] = $user->college_slug;
        }

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_visible'] = $request->has('is_visible');

        if ($request->hasFile('photo')) {
            // Delete old photo if exists on local
            if ($testimonial->photo && !str_contains($testimonial->photo, 'drive.google.com')) {
                $oldPath = str_replace(Storage::url(''), '', $testimonial->photo);
                Storage::disk('public')->delete($oldPath);
            }
            $file = $request->file('photo');
            $filename = time() . '_' . Str::slug($data['name']) . '.' . $file->getClientOriginalExtension();
            $path = Storage::disk('google')->putFileAs('testimonials', $file, $filename);
            $data['photo'] = Storage::disk('google')->url($path);
        }

        $testimonial->update($data);

        $returnCollege = $request->input('return_college');
        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'testimonials'])
                ->with('success', 'Testimonial updated successfully.');
        }

        return redirect()->route('admin.colleges.index')->with('success', 'Testimonial updated successfully.');
    }

    public function destroy(Request $request, CollegeTestimonial $testimonial): RedirectResponse
    {
        $this->authorizeCollege($testimonial->college_slug);
        $returnCollege = $request->input('return_college', $testimonial->college_slug);
        
        if ($testimonial->photo) {
            $oldPath = str_replace(Storage::url(''), '', $testimonial->photo);
            Storage::disk('public')->delete($oldPath);
        }
        
        $testimonial->delete();

        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'testimonials'])
                ->with('success', 'Testimonial deleted successfully.');
        }

        return redirect()->route('admin.colleges.index')->with('success', 'Testimonial deleted successfully.');
    }

    private function authorizeCollege(?string $collegeSlug): void
    {
        if (! request()->user()->canAccessCollege($collegeSlug)) {
            abort(403, 'You do not have access to this testimonial.');
        }
    }
}
