<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unit::create([
            'kode_unit' => 'IT',
            'nama_unit' => 'IT & PDE',
        ]);

        Unit::create([
            'kode_unit' => 'LG',
            'nama_unit' => 'Logistik',
        ]);

        Unit::create([
            'kode_unit' => 'UM',
            'nama_unit' => 'Umum',
        ]);

        Unit::create([
            'kode_unit' => 'KU',
            'nama_unit' => 'Keuangan',
        ]);

        Unit::create([
            'kode_unit' => 'PM',
            'nama_unit' => 'Pemasaran',
        ]);
    }
}
