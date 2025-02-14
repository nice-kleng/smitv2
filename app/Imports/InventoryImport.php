<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Inventory\Models\Inventory;
use Modules\Inventory\Models\MasterBarang;

class InventoryImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // $barang = MasterBarang::where('kode_barang', $row['kode_barang'])->first();

            // if ($barang) {
            Inventory::create([
                'kode_barang' => $row['kode_inventaris'],
                'no_barang' => $row['no_barang'] ?? null,
                'barang_id' => $row['barang_id'],
                'ruangan_id' => $row['ruangan_id'],
                'harga_beli' => $row['harga_beli'],
                'satuan' => $row['satuan'] ?? null,
                'merk' => $row['merk'] ?? null,
                'type' => $row['type'] ?? null,
                'serial_number' => $row['serial_number'] ?? null,
                'spesifikasi' => $row['spesifikasi'] ?? null,
                'tahun_pengadaan' => $row['tahun_pengadaan'],
                'status' => $row['status'] ?? '2',
                'catatan' => $row['catatan'] ?? null,
                'kepemilikan' => $row['kepemilikan'] ?? null
            ]);
            // }
        }
    }
}
