@extends('admin.layout')

@section('title', 'Dashboard')

@push('styles')
<style>
    .stat-card {
        position: relative;
        overflow: hidden;
    }
    .stat-card .stat-accent {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
    }
    .completeness-bar {
        height: 6px;
        border-radius: 3px;
        background: var(--admin-border);
        overflow: hidden;
    }
    .completeness-bar-fill {
        height: 100%;
        border-radius: 3px;
        transition: width 0.6s ease;
    }
    .completeness-low { background: #ef4444; }
    .completeness-mid { background: #f59e0b; }
    .completeness-high { background: #22c55e; }
    .college-stat-row {
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid var(--admin-border);
        transition: background 0.15s ease;
    }
    .college-stat-row:last-child { border-bottom: none; }
    .college-stat-row:hover { background: var(--admin-accent-soft); }
    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
    }
</style>
@endpush

@section('content')
    <h1 class="admin-page-title">Dashboard</h1>

    {{-- Stat Cards Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="admin-card stat-card h-100">
                <div class="stat-accent" style="background: linear-gradient(90deg, var(--admin-accent), transparent);"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <span class="text-uppercase small fw-600 text-muted" style="letter-spacing: 0.05em; font-size: 0.6875rem;">Articles</span>
                        <span class="rounded-3 p-2" style="background: var(--admin-accent-soft);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--admin-accent)" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </span>
                    </div>
                    <p class="fs-2 fw-700 mb-1" style="letter-spacing: -0.02em;">{{ $articlesCount }}</p>
                    <a href="{{ route('admin.articles.index') }}" class="text-decoration-none small fw-500" style="color: var(--admin-accent);">Manage →</a>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-card stat-card h-100">
                <div class="stat-accent" style="background: linear-gradient(90deg, #8b5cf6, transparent);"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <span class="text-uppercase small fw-600 text-muted" style="letter-spacing: 0.05em; font-size: 0.6875rem;">Announcements</span>
                        <span class="rounded-3 p-2" style="background: rgba(139, 92, 246, 0.12);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2"><path d="M10 2v7.31"/><path d="M14 9.3V1.99"/><path d="M8.5 2h7"/><path d="M14 9.3a6 6 0 1 1-4 0"/><path d="M5.52 16h12.96"/></svg>
                        </span>
                    </div>
                    <p class="fs-2 fw-700 mb-1" style="letter-spacing: -0.02em;">{{ $announcementsCount }}</p>
                    <a href="{{ route('admin.announcements.index') }}" class="text-decoration-none small fw-500" style="color: #8b5cf6;">Manage →</a>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="admin-card stat-card h-100">
                <div class="stat-accent" style="background: linear-gradient(90deg, #06b6d4, transparent);"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <span class="text-uppercase small fw-600 text-muted" style="letter-spacing: 0.05em; font-size: 0.6875rem;">Faculty</span>
                        <span class="rounded-3 p-2" style="background: rgba(6, 182, 212, 0.12);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </span>
                    </div>
                    <p class="fs-2 fw-700 mb-1" style="letter-spacing: -0.02em;">{{ $facultyCount }}</p>
                    @if(auth()->user() && auth()->user()->isBoundedToDepartment())
                        <span class="text-muted small">in your department</span>
                    @elseif(auth()->user() && auth()->user()->isBoundedToCollege() && auth()->user()->college_slug)
                        <span class="text-muted small">in your college</span>
                    @else
                        <span class="text-muted small">across {{ $totalColleges }} colleges</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Content Completeness --}}
        <div class="col-lg-7">
            <div class="admin-card">
                <div class="admin-card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--admin-accent)" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>Content Completeness</span>
                    </div>
                    <span class="text-muted small">{{ count($collegeStats) }} colleges</span>
                </div>
                <div class="card-body p-0">
                    @forelse ($collegeStats as $stat)
                        <div class="college-stat-row">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                    <a href="{{ route('admin.colleges.show', ['college' => $stat['slug']]) }}" class="fw-500 text-decoration-none" style="color: var(--admin-text);">{{ $stat['name'] }}</a>
                                    <div class="d-flex gap-2 mt-1">
                                        <span class="stat-badge" style="background: rgba(6,182,212,0.1); color: #06b6d4;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                            {{ $stat['faculty'] }} faculty
                                        </span>
                                        @if (!empty($stat['faculty_label']))
                                            <span class="text-muted small align-self-center">{{ $stat['faculty_label'] }}</span>
                                        @endif
                                        <span class="stat-badge" style="background: rgba(139,92,246,0.1); color: #8b5cf6;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                                            {{ $stat['departments'] }} depts
                                        </span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="fw-700" style="font-size: 1.125rem; color: {{ $stat['percent'] >= 70 ? '#22c55e' : ($stat['percent'] >= 40 ? '#f59e0b' : '#ef4444') }};">{{ $stat['percent'] }}%</span>
                                    <div class="text-muted small">{{ $stat['filled'] }}/{{ $stat['total'] }} sections</div>
                                </div>
                            </div>
                            <div class="completeness-bar">
                                <div class="completeness-bar-fill {{ $stat['percent'] >= 70 ? 'completeness-high' : ($stat['percent'] >= 40 ? 'completeness-mid' : 'completeness-low') }}" style="width: {{ $stat['percent'] }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">No colleges found.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Articles --}}
        <div class="col-lg-5">
            <div class="admin-card">
                <div class="admin-card-header d-flex align-items-center justify-content-between">
                    <span>Recent Articles</span>
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-sm btn-link text-decoration-none fw-500" style="color: var(--admin-accent);">View all</a>
                </div>
                <div class="card-body p-0">
                    @if ($recentArticles->isEmpty())
                        <div class="p-5 text-center">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px; background: var(--admin-accent-soft);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--admin-accent)" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </div>
                            <p class="text-muted mb-2">No articles yet.</p>
                            <a href="{{ route('admin.articles.create') }}" class="btn btn-admin-primary btn-sm">Create your first article</a>
                        </div>
                    @else
                        @foreach ($recentArticles as $article)
                            <div class="d-flex align-items-center justify-content-between px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div style="min-width: 0;">
                                    <div class="fw-500 text-truncate" style="max-width: 250px;">{{ $article->title }}</div>
                                    <div class="text-muted small">{{ $article->date_formatted }}</div>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                    <span class="badge rounded-pill px-2 py-1" style="background: var(--admin-accent-soft); color: var(--admin-accent); font-weight: 500; font-size: 0.6875rem;">{{ $article->type }}</span>
                                @if(auth()->user()?->canManageArticle($article))
                                    <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0" style="font-size: 0.75rem;">Edit</a>
                                @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="admin-card mt-4">
                <div class="admin-card-header">
                    <span>Quick Actions</span>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.articles.create') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -1px; margin-right: 3px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            New Article
                        </a>
                        <a href="{{ route('admin.announcements.create') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -1px; margin-right: 3px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            New Announcement
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
