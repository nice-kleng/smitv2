<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Inventory\Models\HistoryInventaris;
use Modules\Inventory\Models\Inventory;
use Modules\Inventory\Models\MasterBarang;

class InventoryImport implements ToModel, WithHeadingRow
{
    // public function collection(Collection $rows)
    // {
    //     foreach ($rows as $row) {
    //         Inventory::create([
    //             'kode_barang' => $row['kode_inventaris'],
    //             'no_barang' => $row['no_barang'] ?? null,
    //             'barang_id' => $row['barang_id'],
    //             'ruangan_id' => $row['ruangan_id'],
    //             'harga_beli' => $row['harga_beli'],
    //             'satuan' => $row['satuan'] ?? null,
    //             'merk' => $row['merk'] ?? null,
    //             'type' => $row['type'] ?? null,
    //             'serial_number' => $row['serial_number'] ?? null,
    //             'spesifikasi' => $row['spesifikasi'] ?? null,
    //             'tahun_pengadaan' => $row['tahun_pengadaan'],
    //             'status' => $row['status'] ?? '2',
    //             'catatan' => $row['catatan'] ?? null,
    //             'kepemilikan' => $row['kepemilikan'] ?? null
    //         ]);
    //     }
    // }

    public function model(array $row)
    {
        $inventory =  Inventory::create([
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

        return new HistoryInventaris([
            'inventory_id' => $inventory->id,
            'unit_id' => $inventory->ruangan->unit->id,
            'ruangan_id' => $inventory->ruangan_id,
            'kondisi' => '2',
            'tanggal_mutasi' => now(),
            'keterangan' => 'Import Data',
            'created_id' => auth()->id(),
            'updated_id' => auth()->id()
        ]);
    }
}
