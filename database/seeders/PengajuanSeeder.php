<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Inventory\Models\Pengajuan;

class PengajuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            [
                'date' => '120224163000', // 12 Feb 2024 16:30:00
                'count' => 30,
                'unit_id' => 2,
                'pu' => 'log'
            ],
            [
                'date' => '130224143000', // 13 Feb 2024 14:30:00
                'count' => 20,
                'unit_id' => 2,
                'pu' => 'log'
            ],
            [
                'date' => '140224153000', // 14 Feb 2024 15:30:00
                'count' => 18,
                'unit_id' => 2,
                'pu' => 'log'
            ],
        ];

        foreach ($groups as $group) {
            $baseKode = 'PGJ-' . $group['date'];

            for ($i = 1; $i <= $group['count']; $i++) {
                Pengajuan::create([
                    'kode_pengajuan' => $baseKode . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'unit_id' => $group['unit_id'],
                    'pu' => $group['pu'],
                    'barang_id' => rand(1, 30),
                    'harga' => rand(10000, 1000000),
                    'jumlah' => rand(1, 10),
                    'jenis_pengajuan' => '1',
                    'tanggal_pengajuan' => Carbon::createFromFormat('dmyHis', $group['date'])->format('Y-m-d'),
                    'status' => '0',
                    'keterangan' => 'Pengajuan barang test ' . $i,
                    'created_id' => 3,
                    'updated_id' => 3,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
