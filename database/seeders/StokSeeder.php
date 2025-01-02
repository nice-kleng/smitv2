<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Models\Stok;

class StokSeeder extends Seeder
{
    public function run(): void
    {
        $barangs = \Modules\Inventory\Models\MasterBarang::all();

        foreach ($barangs as $barang) {
            $stokCount = rand(1, 5); // Random 1-5 stock entries per item

            for ($i = 0; $i < $stokCount; $i++) {
                Stok::create([
                    'master_barang_id' => $barang->id,
                    'stok' => rand(1, 100),
                    'harga' => rand(10000, 10000000),
                    'keterangan' => 'Stok per ' . date('Y-m-d')
                ]);
            }
        }
    }
}
