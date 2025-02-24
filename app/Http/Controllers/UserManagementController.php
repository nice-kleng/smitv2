<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Unit;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): View
    {
        $users = User::with(['roles', 'ruangan', 'unit'])->get();
        $roles = Role::all();
        $units = Unit::all();
        return view('settings.users.index', compact('users', 'roles', 'units'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'roles' => ['required', 'array'],
            'ruangan_id' => ['required', 'exists:ruangans,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'pu_kd' => ['required', 'string', 'in:' . implode(',', array_keys(User::USER_PU))],
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'ruangan_id' => $validated['ruangan_id'],
            'unit_id' => $validated['unit_id'],
            'pu_kd' => $validated['pu_kd'] ?? 0,
        ]);

        $user->syncRoles($validated['roles']);

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): JsonResponse|View
    {
        if (request()->ajax()) {
            return response()->json($user->load(['roles', 'unit', 'ruangan']));
        }

        $roles = Role::all();
        return view('settings.user.form', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'roles' => ['required', 'array'],
            'ruangan_id' => ['required', 'exists:ruangans,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'pu_kd' => ['required', 'string', 'in:' . implode(',', array_keys(User::USER_PU))],
        ]);

        $userData = [
            'username' => $validated['username'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'ruangan_id' => $validated['ruangan_id'],
            'unit_id' => $validated['unit_id'],
            'pu_kd' => $validated['pu_kd'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);
        $user->syncRoles($validated['roles']);

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'User berhasil diupdate');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        if ($user->hasRole('superadmin')) {
            return response()->json([
                'message' => 'Tidak dapat menghapus user superadmin'
            ], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User berhasil dihapus']);
    }

    /**
     * Get the roles for the specified user.
     */
    public function getRoles(User $user): JsonResponse
    {
        return response()->json([
            'roles' => $user->roles->pluck('id')
        ]);
    }

    /**
     * Update roles for the specified user.
     */
    public function updateRoles(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'roles' => ['required', 'array']
        ]);

        if ($user->hasRole('superadmin') && !in_array('superadmin', $validated['roles'])) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus role superadmin');
        }

        $user->syncRoles($validated['roles']);

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'Role user berhasil diupdate');
    }

    /**
     * Get permissions for the specified user.
     */
    public function getPermissions(User $user): View
    {
        $permissions = Permission::all()->groupBy('modules');
        return view('settings.users.permissions', compact('user', 'permissions'));
    }

    /**
     * Update permissions for the specified user.
     */
    public function updatePermissions(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'permissions' => ['required', 'array']
        ]);

        $user->syncPermissions($validated['permissions']);

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'Permissions user berhasil diupdate');
    }
}
