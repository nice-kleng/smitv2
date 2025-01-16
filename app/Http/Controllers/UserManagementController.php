<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Models\Unit;
use Spatie\Permission\Models\Permission;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        $units = Unit::all();
        return view('settings.users.index', compact('users', 'roles', 'units'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('settings.user.form', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:225',
            'email' => 'required|string|email|max:225|unique:users',
            'password' => 'required|string|min:8',
            'roles' => 'required|array',
            'ruangan_id' => 'required|exists:ruangans,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'ruangan_id' => $validated['ruangan_id'],
            'pu_kd' => $request->pu_kd,
        ]);

        $user->syncRoles($validated['roles']);

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        if (request()->ajax()) {
            return response()->json($user->load('roles'));
        }
        return view('settings.user.form', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'roles' => 'required|array',
            'ruangan_id' => 'required|exists:units,id'
        ]);

        dd($validated);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'ruangan_id' => $validated['ruangan_id']
        ]);

        if ($validated['password']) {
            $user->update(['password' => bcrypt($validated['password'])]);
        }

        $user->syncRoles($validated['roles']);

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'User berhasil diupdate');
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('superadmin')) {
            return response()->json([
                'message' => 'Tidak dapat menghapus user superadmin'
            ], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User berhasil dihapus']);
    }

    public function getRoles(User $user)
    {
        return response()->json([
            'roles' => $user->roles->pluck('id')
        ]);
    }

    public function updateRoles(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => 'required|array'
        ]);

        if ($user->hasRole('superadmin') && !in_array('superadmin', $validated['roles'])) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus role superadmin');
        }

        $user->syncRoles($validated['roles']);

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'Role user berhasil diupdate');
    }

    // Method tambahan untuk melihat permissions user
    // public function permissions(User $user)
    // {
    //     $permissions = $user->getAllPermissions();
    //     return view('settings.user.permissions', compact('user', 'permissions'));
    // }

    public function getPermissions(User $user)
    {
        $permissions = Permission::all()->groupBy('modules');
        return view('settings.users.permissions', compact('user', 'permissions'));
    }

    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'required|array'
        ]);

        $user->syncPermissions($request->permissions);

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'Permissions user berhasil diupdate');
    }
}
