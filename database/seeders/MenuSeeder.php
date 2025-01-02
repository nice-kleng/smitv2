<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menu = [
            [
                'name' => 'Pengaturan',
                'icon' => 'fas fa-cog',
                'route' => 'settings.index',
                'module' => 'admin',
                'permission_name' => 'manage-menu',
                'parent_id' => null,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Data Master',
                'icon' => 'fas fa-database',
                'route' => '',
                'module' => 'admin',
                'permission_name' => 'manage-data-master',
                'parent_id' => null,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Unit',
                'icon' => 'fas fa-laptop-house',
                'route' => 'master.unit.index',
                'module' => 'admin',
                'permission_name' => 'manage-unit',
                'parent_id' => 2,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Ruangan',
                'icon' => 'fas fa-door-closed',
                'route' => 'master.ruangan.index',
                'module' => 'admin',
                'permission_name' => 'manage-ruangan',
                'parent_id' => 2,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Kategori Barang',
                'icon' => 'fas fa-tags',
                'route' => 'master.kategoriBarang.index',
                'module' => 'admin',
                'permission_name' => 'manage-kategori-barang',
                'parent_id' => 2,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Satuan',
                'icon' => 'fas fa-balance-scale',
                'route' => 'master.satuan.index',
                'module' => 'admin',
                'permission_name' => 'manage-satuan',
                'parent_id' => 2,
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Barang',
                'icon' => 'fas fa-boxes',
                'route' => 'inventory.master_barang.index',
                'module' => 'inventory',
                'permission_name' => 'manage-master-barang',
                'parent_id' => null,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Permintaan',
                'icon' => 'fas fa-cart-plus',
                'route' => 'inventory.permintaan.index',
                'module' => 'inventory',
                'permission_name' => 'manage-permintaan',
                'parent_id' => null,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Pengajuan',
                'icon' => 'fas fa-clipboard-list',
                'route' => 'inventory.pengajuan.index',
                'module' => 'inventory',
                'permission_name' => 'crate-pengajuan',
                'parent_id' => null,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'History Permintaan',
                'icon' => 'fas fa-history',
                'route' => 'inventory.permintaan.history',
                'module' => 'inventory',
                'permission_name' => 'history-permintaan',
                'parent_id' => null,
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($menu as $item) {
            Menu::create($item);
        }
    }
}
