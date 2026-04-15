<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add permission
        DB::table('permissions')->insert([
            'name' => 'manage-portal',
            'guard_name' => 'web',
            'modules' => 'Admin',
            'group_name' => 'Portal',
            'description' => 'Manage Portal Links',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add menu entry for superadmin
        DB::table('menus')->insert([
            'name' => 'Portal Links',
            'icon' => 'fas fa-globe',
            'route' => 'settings.portal-link.index',
            'module' => 'admin',
            'permission_name' => 'manage-portal',
            'parent_id' => null,
            'order' => 3,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('menus')->where('route', 'settings.portal-link.index')->delete();
        DB::table('permissions')->where('name', 'manage-portal')->delete();
    }
};
