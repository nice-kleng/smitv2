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
                'permission_name' => 'manage-settings',
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
                'permission_name' => 'manage-pengajuan',
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
            [
                'name' => 'Data Inventaris',
                'icon' => 'fas fa-cart-plus',
                'route' => 'inventory.index',
                'module' => 'inventory',
                'permission_name' => 'manage-inventory',
                'parent_id' => null,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Jenis Aduan',
                'icon' => 'fas fa-bullhorn',
                'route' => 'inventory.helpdesk.jenis-aduan.index',
                'module' => 'helpdesk',
                'permission_name' => 'manage-jenis-pengaduan',
                'parent_id' => null,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Data Pengaduan',
                'icon' => 'fas fa-mail-bulk',
                'route' => 'inventory.helpdesk.ticket.index',
                'module' => 'helpdesk',
                'permission_name' => 'manage-data-pengaduan',
                'parent_id' => null,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Rekap Service Luar',
                'icon' => 'fas fa-tools',
                'route' => 'inventory.helpdesk.ticket.rekapService',
                'module' => 'helpdesk',
                'permission_name' => 'manage-rekap-service',
                'parent_id' => null,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Riwayat Service',
                'icon' => 'fas fa-history',
                'route' => 'inventory.helpdesk.ticket.riwayat-service-teknisi',
                'module' => 'helpdesk',
                'permission_name' => 'riwayat-service-teknisi',
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
