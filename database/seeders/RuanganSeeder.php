<?php

namespace Database\Seeders;

use App\Models\Ruangan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ruangan::create([
            'nama_ruangan' => 'Kantor',
            'kode_ruangan' => 'Ktr',
            'unit_id' => 1,
        ]);

        Ruangan::create([
            'nama_ruangan' => 'Gudang',
            'kode_ruangan' => 'Gdg',
            'unit_id' => 1,
        ]);

        Ruangan::create([
            'nama_ruangan' => 'Kantor',
            'kode_ruangan' => 'Ktr',
            'unit_id' => 2,
        ]);

        Ruangan::create([
            'nama_ruangan' => 'Kantor',
            'kode_ruangan' => 'Ktr',
            'unit_id' => 3,
        ]);

        Ruangan::create([
            'nama_ruangan' => 'Kantor',
            'kode_ruangan' => 'Ktr',
            'unit_id' => 4,
        ]);

        Ruangan::create([
            'nama_ruangan' => 'Kantor',
            'kode_ruangan' => 'Ktr',
            'unit_id' => 5,
        ]);
    }
}
