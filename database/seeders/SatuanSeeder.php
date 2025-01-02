<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama_satuan' => 'Unit', 'pu' => 'it'],
            ['nama_satuan' => 'Pcs', 'pu' => 'it'],
            ['nama_satuan' => 'Rim', 'pu' => 'log'],
            ['nama_satuan' => 'Box', 'pu' => 'log'],
            ['nama_satuan' => 'Lembar', 'pu' => 'log'],
            ['nama_satuan' => 'Pack', 'pu' => 'log'],
            ['nama_satuan' => 'Lusin', 'pu' => 'log'],
            ['nama_satuan' => 'Set', 'pu' => 'it'],
            ['nama_satuan' => 'Roll', 'pu' => 'log'],
            ['nama_satuan' => 'Meter', 'pu' => 'log']
        ];

        foreach ($data as $item) {
            \App\Models\Satuan::create($item);
        }
    }
}
