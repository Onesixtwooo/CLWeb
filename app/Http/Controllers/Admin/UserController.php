<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollegeDepartment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    private const ROLES = [User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_EDITOR];

    public function index(Request $request): View
    {
        $user = $request->user();
        $query = User::query()->orderBy('name');
        
        // College admins can only see users in their college
        if ($user && $user->isAdmin() && !$user->isSuperAdmin()) {
            $query->where('college_slug', $user->college_slug);
        }
        
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }
        if ($request->filled('role') && in_array($request->role, self::ROLES, true)) {
            $query->where('role', $request->role);
        }
        $users = $query->paginate(15)->withQueryString();

        $rolePermissions = User::getRolePermissions();

        return view('admin.users.index', compact('users', 'rolePermissions'));
    }

    public function create(): View
    {
        $colleges = CollegeController::getColleges();

        // Load departments from the college_departments table, grouped by college slug
        $departmentsByCollege = [];
        foreach (array_keys($colleges) as $collegeSlug) {
            $departmentsByCollege[$collegeSlug] = CollegeDepartment::where('college_slug', $collegeSlug)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name'])
                ->toArray();
        }

        $organizationsByCollege = [];
        foreach (array_keys($colleges) as $collegeSlug) {
            $organizationsByCollege[$collegeSlug] = \App\Models\CollegeOrganization::where('college_slug', $collegeSlug)
                ->orderBy('name')
                ->get(['id', 'name', 'department_id'])
                ->toArray();
        }

        return view('admin.users.create', [
            'colleges' => $colleges,
            'departmentsByCollege' => $departmentsByCollege,
            'organizationsByCollege' => $organizationsByCollege,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $roles = implode(',', self::ROLES);
        $collegeSlugs = array_keys(CollegeController::getColleges());
        $request->merge(['college_slug' => $request->input('college_slug') ?: null]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:'.$roles],
            'college_slug' => [
                Rule::requiredIf(in_array($request->input('role'), [User::ROLE_ADMIN, User::ROLE_EDITOR], true)),
                'nullable',
                'in:'.implode(',', $collegeSlugs),
            ],
            'department' => ['nullable', 'string', 'max:120'],
            'organization_id' => ['nullable', 'exists:college_organizations,id'],
        ], [
            'college_slug.required' => 'Editor and admin must be assigned to a college.',
        ]);
        if ($data['role'] === User::ROLE_SUPERADMIN) {
            if (! $request->user()->isSuperAdmin()) {
                abort(403, 'Only a superadmin can create superadmin users.');
            }
            $data['college_slug'] = null;
            $data['department'] = null;
            $data['organization_id'] = null;
        }
        
        // College admins can only create users in their own college
        if ($request->user()->isAdmin() && !$request->user()->isSuperAdmin()) {
            if ($data['college_slug'] !== $request->user()->college_slug) {
                abort(403, 'You can only create users for your assigned college.');
            }
        }
        
        $data['password'] = bcrypt($data['password']);
        $data['is_admin'] = in_array($data['role'], [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true);
        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        $currentUser = request()->user();
        
        // College admins can only edit users in their college
        if ($currentUser && $currentUser->isAdmin() && !$currentUser->isSuperAdmin()) {
            if ($user->college_slug !== $currentUser->college_slug) {
                abort(403, 'You can only edit users in your assigned college.');
            }
        }
        
        $colleges = CollegeController::getColleges();

        // Load departments from the college_departments table, grouped by college slug
        $departmentsByCollege = [];
        foreach (array_keys($colleges) as $collegeSlug) {
            $departmentsByCollege[$collegeSlug] = CollegeDepartment::where('college_slug', $collegeSlug)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name'])
                ->toArray();
        }

        $organizationsByCollege = [];
        foreach (array_keys($colleges) as $collegeSlug) {
            $organizationsByCollege[$collegeSlug] = \App\Models\CollegeOrganization::where('college_slug', $collegeSlug)
                ->orderBy('name')
                ->get(['id', 'name', 'department_id'])
                ->toArray();
        }

        return view('admin.users.edit', [
            'user' => $user,
            'colleges' => $colleges,
            'departmentsByCollege' => $departmentsByCollege,
            'organizationsByCollege' => $organizationsByCollege,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $roles = implode(',', self::ROLES);
        $collegeSlugs = array_keys(CollegeController::getColleges());
        $request->merge(['college_slug' => $request->input('college_slug') ?: null]);
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:'.$roles],
            'college_slug' => [
                Rule::requiredIf(in_array($request->input('role'), [User::ROLE_ADMIN, User::ROLE_EDITOR], true)),
                'nullable',
                'in:'.implode(',', $collegeSlugs),
            ],
            'department' => ['nullable', 'string', 'max:120'],
            'organization_id' => ['nullable', 'exists:college_organizations,id'],
        ];
        if ($request->filled('password')) {
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }
        $data = $request->validate($rules, [
            'college_slug.required' => 'Editor and admin must be assigned to a college.',
        ]);
        if ($data['role'] === User::ROLE_SUPERADMIN && ! $request->user()->isSuperAdmin()) {
            abort(403, 'Only a superadmin can assign the superadmin role.');
        }
        
        // College admins can only edit users in their own college
        if ($request->user()->isAdmin() && !$request->user()->isSuperAdmin()) {
            if ($user->college_slug !== $request->user()->college_slug) {
                abort(403, 'You can only edit users in your assigned college.');
            }
            if ($data['college_slug'] !== $request->user()->college_slug) {
                abort(403, 'You cannot change a user to a different college.');
            }
        }
        
        if ($data['role'] === User::ROLE_SUPERADMIN) {
            $data['college_slug'] = null;
        }
        if (! empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $data['is_admin'] = in_array($data['role'], [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true);
        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }
        
        // College admins can only delete users in their college
        if ($request->user()->isAdmin() && !$request->user()->isSuperAdmin()) {
            if ($user->college_slug !== $request->user()->college_slug) {
                abort(403, 'You can only delete users in your assigned college.');
            }
        }
        
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function saveRolePermissions(Request $request): JsonResponse
    {
        // Only superadmins can change role permissions
        if (!$request->user()?->isSuperAdmin()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'role_type'   => ['required', 'string', 'in:college-admin,dept-admin,org-admin,editor'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['boolean'],
        ]);

        $current = User::getRolePermissions();
        $current[$validated['role_type']] = $validated['permissions'];

        Setting::set('role_permissions', $current);

        return response()->json(['success' => true, 'permissions' => $current]);
    }
}
