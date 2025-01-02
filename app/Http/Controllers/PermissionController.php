<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('group_name')
            ->orderBy('name')
            ->get()
            ->groupBy('modules');

        return view('settings.permission.index', compact('permissions'));
    }

    public function create()
    {
        $modules = ['Admin', 'Inventory', 'Helpdesk']; // Sesuaikan dengan modul yang ada
        $actions = ['view', 'create', 'edit', 'delete', 'manage'];

        return view('settings.permission.form', compact('modules', 'actions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'modules' => 'required|string|max:255',
            'group_name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            Permission::create($validated);

            return redirect()
                ->route('settings.permission.index')
                ->with('success', 'Permission berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan permission');
        }
    }

    public function generateForModule(Request $request)
    {
        $validated = $request->validate([
            'module' => 'required|string',
            'actions' => 'required|array',
            'actions.*' => 'string'
        ]);

        try {
            $created = 0;
            foreach ($validated['actions'] as $action) {
                $name = $action . '-' . Str::slug($validated['module']);
                Permission::firstOrCreate(
                    ['name' => $name],
                    [
                        'group_name' => $validated['module'],
                        'description' => ucfirst($action) . ' ' . ucfirst($validated['module'])
                    ]
                );
                $created++;
            }

            return redirect()
                ->route('settings.permission.index')
                ->with('success', $created . ' permission berhasil dibuat');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat generate permission');
        }
    }

    public function edit(Permission $permission)
    {
        $modules = ['Admin', 'Inventory', 'Helpdesk'];
        $actions = ['view', 'create', 'edit', 'delete', 'manage'];

        return view('settings.permission.form', compact('permission', 'modules', 'actions'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'modules' => 'required|string|max:255',
            'group_name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $permission->update($validated);

            return redirect()
                ->route('settings.permission.index')
                ->with('success', 'Permission berhasil diupdate');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat update permission');
        }
    }

    public function destroy(Permission $permission)
    {
        try {
            // Cek apakah permission masih digunakan oleh role
            if ($permission->roles()->count() > 0) {
                return back()->with('error', 'Permission masih digunakan oleh role');
            }

            // Cek apakah permission masih digunakan di menu
            if ($permission->menus()->count() > 0) {
                return back()->with('error', 'Permission masih digunakan di menu');
            }

            $permission->delete();

            return redirect()
                ->route('settings.permission.index')
                ->with('success', 'Permission berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus permission');
        }
    }
}
