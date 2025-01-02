<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('settings.role.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('group_name')->get();
        return view('settings.role.form', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role = Role::create(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions']);

        return redirect()
            ->route('settings.role.index')
            ->with('success', 'Role berhasil ditambahkan');
    }

    public function edit(Role $role)
    {
        if ($role->name === 'superadmin') {
            return redirect()
                ->route('settings.role.index')
                ->with('error', 'Role superadmin tidak dapat diubah');
        }

        $permissions = Permission::orderBy('group_name')->get();
        return view('settings.role.form', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'superadmin') {
            return redirect()
                ->route('settings.role.index')
                ->with('error', 'Role superadmin tidak dapat diubah');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions']);

        return redirect()
            ->route('settings.role.index')
            ->with('success', 'Role berhasil diupdate');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'superadmin') {
            return redirect()
                ->route('settings.role.index')
                ->with('error', 'Role superadmin tidak dapat dihapus');
        }

        // Cek apakah role masih digunakan
        if ($role->users()->count() > 0) {
            return redirect()
                ->route('settings.role.index')
                ->with('error', 'Role masih digunakan oleh user');
        }

        $role->delete();

        return redirect()
            ->route('settings.role.index')
            ->with('success', 'Role berhasil dihapus');
    }
}
