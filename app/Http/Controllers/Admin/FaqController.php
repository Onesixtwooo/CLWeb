<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollegeFaq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('admin.colleges.index');
    }

    public function create(Request $request, ?string $college = null): View|RedirectResponse
    {
        $user = $request->user();
        $colleges = $user->isSuperAdmin() ? CollegeController::getColleges() : [];
        $collegeSlug = $college ?? $request->query('college');
        $fromCollegeSection = (string) $collegeSlug !== '';
        if ($fromCollegeSection && $user->isBoundedToCollege()) {
            $collegeSlug = $user->college_slug;
        }

        if ($college === null && (string) $collegeSlug !== '') {
            return redirect()->route('admin.faqs.create-college', ['college' => $collegeSlug]);
        }

        return view('admin.faqs.create', compact('colleges', 'collegeSlug', 'fromCollegeSection'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'question' => ['required', 'string'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'college_slug' => ['nullable', 'string', 'max:80'],
            'is_visible' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $returnCollege = $request->input('return_college');

        if ($user->isBoundedToCollege()) {
            $data['college_slug'] = $user->college_slug;
        } elseif (empty($data['college_slug']) && $returnCollege && in_array($returnCollege, array_keys(CollegeController::getColleges()), true)) {
            $data['college_slug'] = $returnCollege;
        } elseif (empty($data['college_slug']) && $user->isSuperAdmin()) {
            // If superadmin doesn't select a college, maybe required?
            // For now, let's assume it's required if not bounded.
            // But validation above made it nullable.
        }

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_visible'] = $request->has('is_visible');

        CollegeFaq::create($data);

        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'faq'])->with('success', 'FAQ added successfully.');
        }

        // Fallback if no college context
        return redirect()->route('admin.colleges.index')->with('success', 'FAQ added successfully.');
    }

    public function edit(Request $request, CollegeFaq $faq): View
    {
        $this->authorizeCollege($faq->college_slug);
        $user = $request->user();
        $colleges = $user->isSuperAdmin() ? CollegeController::getColleges() : [];
        $returnCollege = $request->query('return_college', $faq->college_slug);

        return view('admin.faqs.edit', compact('faq', 'colleges', 'returnCollege'));
    }

    public function update(Request $request, CollegeFaq $faq): RedirectResponse
    {
        $this->authorizeCollege($faq->college_slug);
        $data = $request->validate([
            'question' => ['required', 'string'],
            'answer' => ['required', 'string'],
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

        $faq->update($data);

        $returnCollege = $request->input('return_college');
        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'faq'])->with('success', 'FAQ updated successfully.');
        }

        return redirect()->route('admin.colleges.index')->with('success', 'FAQ updated successfully.');
    }

    public function destroy(Request $request, CollegeFaq $faq): RedirectResponse
    {
        $this->authorizeCollege($faq->college_slug);
        $returnCollege = $request->input('return_college', $faq->college_slug);
        $faq->delete();

        if ($returnCollege) {
            return redirect()->route('admin.colleges.show', ['college' => $returnCollege, 'section' => 'faq'])->with('success', 'FAQ deleted successfully.');
        }

        return redirect()->route('admin.colleges.index')->with('success', 'FAQ deleted successfully.');
    }

    private function authorizeCollege(?string $collegeSlug): void
    {
        if (! request()->user()->canAccessCollege($collegeSlug)) {
            abort(403, 'You do not have access to this FAQ record.');
        }
    }
}
