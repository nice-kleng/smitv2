<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'superadmin']);
        Role::create(['name' => 'direktur']);
        Role::create(['name' => 'keuangan']);
        $admin = Role::create(['name' => 'admin']);
        Role::create(['name' => 'staff']);
        $unit = Role::create(['name' => 'unit']);

        $data = [
            [
                'name' => 'manage-settings',
                'modules' => 'Admin',
                'group_name' => 'settings',
                'description' => 'Manage settings',
            ],
            [
                'name' => 'manage-data-master',
                'modules' => 'Admin',
                'group_name' => 'Data Master',
                'description' => 'Manage data master',
            ],
            [
                'name' => 'manage-unit',
                'modules' => 'Admin',
                'group_name' => 'unit',
                'description' => 'Manage unit',
            ],
            [
                'name' => 'manage-ruangan',
                'modules' => 'Admin',
                'group_name' => 'ruangan',
                'description' => 'Manage ruangan',
            ],
            [
                'name' => 'manage-satuan',
                'modules' => 'Admin',
                'group_name' => 'satuan',
                'description' => 'Manage satuan',
            ],
            [
                'name' => 'manage-kategori-barang',
                'modules' => 'Admin',
                'group_name' => 'Kategori barang',
                'description' => 'Manage kategori barang',
            ],
            [
                'name' => 'manage-master-barang',
                'modules' => 'Inventory',
                'group_name' => 'Master Barang',
                'description' => 'Manage data master barang',
            ],
            [
                'name' => 'manage-pengajuan',
                'modules' => 'Inventory',
                'group_name' => 'Pengajuan',
                'description' => 'Create pengajuan barang',
            ],
            [
                'name' => 'manage-permintaan',
                'modules' => 'Inventory',
                'group_name' => 'Permintaan',
                'description' => 'Manage permintaan barang (untuk unit dan admin)',
            ],
            [
                'name' => 'approve-permintaan',
                'modules' => 'Inventory',
                'group_name' => 'Permintaan',
                'description' => 'Approve permintaan barang (untuk admin)',
            ],
            [
                'name' => 'history-permintaan',
                'modules' => 'Inventory',
                'group_name' => 'Permintaan',
                'description' => 'History permintaan barang (untuk unit dan admin)',
            ]
        ];

        foreach ($data as $item) {
            Permission::create($item);
        }

        $unit->givePermissionTo(['manage-pengajuan', 'manage-permintaan', 'history-permintaan']);
        $admin->givePermissionTo(['manage-data-master', 'manage-master-barang', 'manage-pengajuan', 'manage-permintaan', 'approve-permintaan', 'history-permintaan', 'manage-kategori-barang', 'manage-satuan']);
    }
}
