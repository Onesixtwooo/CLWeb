<?php

namespace App\View\Composers;

use App\Models\Article;
use Illuminate\View\View;

class CollegeFooterComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Try to get college slug from view data, or fallback to request route parameter
        $college = $view->getData()['collegeSlug'] ?? request()->route('college') ?? 'engineering';

        $recentPosts = Article::where(function ($query) use ($college) {
                $query->where('college_slug', $college)
                      ->orWhereNull('college_slug');
            })
            ->whereIn('type', ['news', 'announcement'])
            ->latest('published_at')
            ->take(4)
            ->get();

        $view->with('recentPosts', $recentPosts);
    }
}
