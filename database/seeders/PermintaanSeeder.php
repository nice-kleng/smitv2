<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Models\Permintaan;
use Modules\Inventory\Models\MasterBarang;
use App\Models\Unit;
use App\Models\Ruangan;
use App\Models\User;
use Carbon\Carbon;

class PermintaanSeeder extends Seeder
{
    public function run(): void
    {
        // Get some random IDs for relationships
        $barang = MasterBarang::pluck('id');

        // Create 20 sample permintaan
        for ($i = 1; $i <= 20; $i++) {
            // Status values as strings: '0'=draft, '1'=pending, '2'=approved, '3'=completed
            $status = '3';
            $tanggal_permintaan = Carbon::now()->subDays(rand(1, 30));

            Permintaan::create([
                'kode_permintaan' => 'REQ-' . date('Ymd') . str_pad($i, 4, '0', STR_PAD_LEFT),
                'pu' => ['log', 'it'][array_rand(['log', 'it'])],
                'barang_id' => $barang->random(),
                'jumlah' => rand(1, 10),
                'jumlah_approve' => $status == 2 ? rand(1, 10) : 0,
                'tanggal_permintaan' => $tanggal_permintaan,
                'tanggal_approve' => $status >= 2 ? $tanggal_permintaan->addDays(rand(1, 5)) : null,
                'status' => '3',
                'keterangan' => 'Sample permintaan barang #' . $i,
                'penerima' => $status == 3 ? 'Penerima ' . $i : null,
                'unit_id' => 5,
                'ruangan_id' => 6,
                'approve_id' => $status >= 2 ? rand(2,3) : null,
                'created_id' => 6,
                'updated_id' => 6,
            ]);
        }
    }
}
