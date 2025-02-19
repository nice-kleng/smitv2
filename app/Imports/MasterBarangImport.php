<?php

namespace App\Imports;

use App\Models\KategoriBarang;
use App\Models\Satuan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithValidation;
use Modules\Inventory\Models\MasterBarang;

class MasterBarangImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Cari atau buat kategori baru
        $kategori = KategoriBarang::firstOrCreate(
            ['nama_kategori' => $row['kategori_id']],
            ['pu' => auth()->user()->pu_kd]
        );

        // Cari atau buat satuan baru
        $satuan = Satuan::firstOrCreate(
            ['nama_satuan' => $row['satuan_id']],
            ['pu' => auth()->user()->pu_kd]
        );

        return new MasterBarang([
            'kode_barang' => $row['kode_barang'],
            'nama_barang' => $row['nama_barang'],
            'kategori_id' => $kategori->id,
            'satuan_id' => $satuan->id,
            'pu' => auth()->user()->pu_kd,
            'jenis' => $row['jenis'] ?? 'barang',
            'is_elektronik' => $row['is_elektronik'] ?? 0,
            'keterangan' => $row['keterangan'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_barang' => 'required|unique:master_barangs,kode_barang',
            'nama_barang' => 'required',
            'kategori_id' => 'required',
            'satuan_id' => 'required',
        ];
    }
}
