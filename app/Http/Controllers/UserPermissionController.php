<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Login;
use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserPermissionController extends Controller
{
    /**
     * Display a listing of users with their permissions
     */
    public function index()
    {
        $users = Login::active()->get();
        $permissions = Permission::orderBy('module')->orderBy('name')->get();

        return view('UserPermission.index', compact('users', 'permissions'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get();
        $companies = Company::where('status', 1)->orderBy('company_name')->get();
        $branches = Branch::where('status', 1)->orderBy('branch_name')->get();
        return view('UserPermission.create', compact('permissions', 'companies', 'branches'));
    }

    /**
     * Store a newly created user in storage
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', Rule::unique('login', 'email')->where(fn ($q) => $q->where('is_deleted', 0))],
            'mobile' => ['nullable', 'string', 'max:15', Rule::unique('login', 'mobile')->where(fn ($q) => $q->where('is_deleted', 0))],
            'password' => 'required|string|min:4',
            'role' => 'required|string|in:super_admin,admin,manager,operator,accountant,viewer',
            'company' => 'nullable|exists:companies,id',
            'branch' => 'nullable|array',
            'branch.*' => 'exists:branches,id',
            'status' => 'boolean',
        ]);

        $user = Login::create([
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => $request->password, // In production, hash the password
            'role' => $request->role,
            'company' => $request->company,
            'branch' => $request->branch ? implode(',', $request->branch) : null,
            'status' => $request->has('status'),
            'created_by' => session('loginId'),
        ]);

        // Create matching User model for permissions
        $userModel = User::create([
            'name' => $request->email,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role to User model
        $role = Role::where('name', $request->role)->first();
        if ($role) {
            $userModel->roles()->sync([$role->id]);
        }

        return redirect()->route('user-permissions.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user's permissions
     */
    public function edit($id)
    {
        $user = Login::active()->findOrFail($id);
        $companies = Company::where('status', 1)->orderBy('company_name')->get();
        $branches = Branch::where('status', 1)->orderBy('branch_name')->get();

        return view('UserPermission.edit', compact('user', 'companies', 'branches'));
    }

    /**
     * Show the authorization list (index of all users for permissions)
     */
    public function authorization()
    {
        $users = Login::active()->get();

        return view('UserPermission.authorization', compact('users'));
    }

    /**
     * Show permission authorization for a specific user
     */
    public function authorizeUser($id)
    {
        $user = Login::active()->findOrFail($id);
        $userModel = User::where('email', $user->email)->first();

        $menus = Menu::orderBy('order')->get();
        $userMenuIds = $userModel ? $userModel->menus->pluck('id')->toArray() : [];

        return view('UserPermission.authorize', compact('user', 'menus', 'userMenuIds'));
    }

    /**
     * Update the specified user's permissions
     */
    public function update(Request $request, $id)
    {
        $login = Login::active()->findOrFail($id);

        // Authorize page only updates menus
        if ($request->input('redirect_to') === 'authorize') {
            $request->validate(['menus' => 'nullable|array']);

            $userModel = User::where('email', $login->email)->first();
            if ($userModel) {
                $userModel->menus()->sync($request->menus ?? []);
            }

            return redirect()->route('user-permissions.authorize', $id)
                ->with('success', 'User menus updated successfully.');
        }

        // Full update from edit page
        $request->validate([
            'email' => ['required', 'email', Rule::unique('login', 'email')->ignore($id)->where(fn ($q) => $q->where('is_deleted', 0))],
            'mobile' => ['nullable', 'string', 'max:15', Rule::unique('login', 'mobile')->ignore($id)->where(fn ($q) => $q->where('is_deleted', 0))],
            'password' => 'nullable|string|min:4',
            'role' => 'required|string|in:super_admin,admin,manager,operator,accountant,viewer',
            'company' => 'nullable|exists:companies,id',
            'branch' => 'nullable|array',
            'branch.*' => 'exists:branches,id',
            'status' => 'required|in:0,1',
        ]);

        $originalEmail = $login->email;

        $updateData = [
            'email' => $request->email,
            'mobile' => $request->mobile,
            'role' => $request->role,
            'company' => $request->company,
            'branch' => $request->branch ? implode(',', $request->branch) : null,
            'status' => $request->status,
            'updated_by' => session('loginId'),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }

        $login->update($updateData);

        // Update User model name/email if changed
        $userModel = User::where('email', $originalEmail)->first();
        if (!$userModel) {
            $userModel = User::create([
                'name' => $request->email,
                'email' => $request->email,
                'password' => Hash::make($request->password ?: 'password'),
            ]);
        } elseif ($request->email !== $originalEmail) {
            $userModel->update(['name' => $request->email, 'email' => $request->email]);
        }

        // Sync role to User model
        $role = Role::where('name', $request->role)->first();
        if ($role) {
            $userModel->roles()->sync([$role->id]);
        }

        if ($request->input('redirect_to') === 'index') {
            return redirect()->route('user-permissions.index')
                ->with('success', 'User updated successfully.');
        }

        return redirect()->route('user-permissions.edit', $id)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage
     */
    public function destroy($id)
    {
        $login = Login::findOrFail($id);

        // Don't allow deleting yourself
        if ($login->id == session('loginId')) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Mark as deleted instead of hard delete
        $login->update(['is_deleted' => 1, 'deleted_at' => now()]);

        return redirect()->route('user-permissions.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user status
     */
    public function toggleStatus($id)
    {
        $login = Login::active()->findOrFail($id);
        $login->update(['status' => !$login->status]);

        return back()->with('success', 'User status updated successfully.');
    }
}
