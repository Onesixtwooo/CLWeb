<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. Superadmin only.'], 403);
            }
            return redirect()->route('admin.dashboard')
                ->with('error', 'You do not have permission to manage users.');
        }

        return $next($request);
    }
}
