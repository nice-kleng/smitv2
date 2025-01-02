<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama_kategori' => 'Laptop & Komputer', 'pu' => 'it'],
            ['nama_kategori' => 'Printer & Scanner', 'pu' => 'it'],
            ['nama_kategori' => 'Perangkat Jaringan', 'pu' => 'it'],
            ['nama_kategori' => 'Aksesoris Komputer', 'pu' => 'it'],
            ['nama_kategori' => 'Software & Lisensi', 'pu' => 'it'],
            ['nama_kategori' => 'Alat Tulis', 'pu' => 'log'],
            ['nama_kategori' => 'Kertas & Amplop', 'pu' => 'log'],
            ['nama_kategori' => 'Furnitur Kantor', 'pu' => 'log'],
            ['nama_kategori' => 'Perlengkapan Arsip', 'pu' => 'log'],
            ['nama_kategori' => 'Peralatan Presentasi', 'pu' => 'it'],
            ['nama_kategori' => 'Kebersihan', 'pu' => 'log'],
            ['nama_kategori' => 'Peralatan Listrik', 'pu' => 'it'],
            ['nama_kategori' => 'Perlengkapan Keamanan', 'pu' => 'log'],
            ['nama_kategori' => 'Mesin Kantor', 'pu' => 'it'],
            ['nama_kategori' => 'Perlengkapan Dapur', 'pu' => 'log']
        ];

        foreach ($data as $item) {
            \App\Models\KategoriBarang::create($item);
        }
    }
}
