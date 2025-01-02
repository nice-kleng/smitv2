<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class MenuManagementController extends Controller
{
    public function middleware($middleware, array $options = [])
    {
        return ['permission:manage-menu'];
    }

    public function index()
    {
        $menus = Menu::with(['children', 'permission', 'parent'])
            ->whereNull('parent_id')
            ->orderBy('module')
            ->orderBy('order')
            ->get();

        return view('settings.menu.index', compact('menus'));
    }

    public function create()
    {
        $parentMenus = Menu::parents()->get();
        $permissions = Permission::orderBy('name')->get();
        $modules = Menu::MODULES;

        return view('settings.menu.form', compact('parentMenus', 'permissions', 'modules'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateMenu($request);

        try {
            DB::transaction(function () use ($validated) {
                Menu::create($validated);
            });

            return redirect()
                ->route('settings.menu.index')
                ->with('success', 'Menu berhasil ditambahkan');
        } catch (\Exception $e) {
            dd($e);
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan menu');
        }
    }

    private function validateMenu(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'module' => 'required|string|in:' . implode(',', array_keys(Menu::MODULES)),
            'permission_name' => 'nullable|exists:permissions,name',
            'parent_id' => [
                'nullable',
                'exists:menus,id',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && Menu::find($value)->parent_id) {
                        $fail('Menu hanya bisa memiliki 1 level parent.');
                    }
                },
            ],
            'order' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);
    }

    public function edit(Menu $menu)
    {
        $parentMenus = Menu::whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->get();
        $permissions = Permission::all();
        $modules = Menu::MODULES;

        return view('settings.menu.form', compact('menu', 'parentMenus', 'permissions', 'modules'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'module' => 'required|string|max:255',
            'permission_name' => 'nullable|exists:permissions,name',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'required|integer',
            'is_active' => 'required|boolean',
        ]);

        $menu->update($validated);

        return redirect()
            ->route('settings.menu.index')
            ->with('success', 'Menu berhasil diubah');
    }

    public function destroy(Menu $menu)
    {
        // Hapus child menu terlebih dahulu
        $menu->children()->delete();
        $menu->delete();

        return redirect()
            ->route('settings.menu.index')
            ->with('success', 'Menu berhasil dihapus');
    }

    public function updateOrder(Request $request)
    {
        try {
            DB::beginTransaction();

            $orders = collect($request->orders);

            // Pisahkan parent dan child menu
            $parentMenus = $orders->where('is_parent', true);
            $childMenus = $orders->where('is_parent', false);

            // Validasi: pastikan parent hanya diurutkan dengan parent
            $parentIds = Menu::whereIn('id', $parentMenus->pluck('id'))->whereNull('parent_id')->count();
            if ($parentIds !== $parentMenus->count()) {
                throw new \Exception('Invalid menu order: Parent menu can only be sorted with other parent menus');
            }

            // Update parent menu orders
            foreach ($parentMenus as $item) {
                Menu::where('id', $item['id'])->update(['order' => $item['order']]);
            }

            // Update child menu orders
            foreach ($childMenus as $item) {
                $menu = Menu::find($item['id']);
                // Pastikan child menu tetap dengan parent yang sama
                if ($menu && $menu->parent_id) {
                    $menu->update(['order' => $item['order']]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Method untuk mendapatkan menu berdasarkan role user
    public function getMenusByRole()
    {
        $user = Auth::user();

        if ($user->hasRole('superadmin')) {
            return Menu::with('children')
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('module')
                ->orderBy('order')
                ->get();
        }

        $permissionNames = $user->getAllPermissions()->pluck('name')->toArray();

        return Menu::with(['children' => function ($query) use ($permissionNames) {
            $query->where('is_active', true)
                ->where(function ($q) use ($permissionNames) {
                    $q->whereNull('permission_name')
                        ->orWhereIn('permission_name', $permissionNames);
                });
        }])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->where(function ($query) use ($permissionNames) {
                $query->whereNull('permission_name')
                    ->orWhereIn('permission_name', $permissionNames);
            })
            ->orderBy('module')
            ->orderBy('order')
            ->get();
    }
}
