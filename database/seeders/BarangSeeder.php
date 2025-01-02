<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Models\MasterBarang;

class BarangSeeder extends Seeder
{
    private function generateInitials($name)
    {
        // Split the name into words
        $words = explode(' ', strtoupper($name));
        $initials = '';

        // Get the first letter of each word
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= $word[0];
            }
        }

        return $initials;
    }

    public function run(): void
    {
        $items = [
            // Laptop & Komputer (kategori_id: 1)
            ['nama_barang' => 'Laptop Dell Latitude', 'kategori_id' => 1, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'it'],
            ['nama_barang' => 'Laptop HP Probook', 'kategori_id' => 1, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'it'],
            ['nama_barang' => 'PC Desktop Lenovo', 'kategori_id' => 1, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'it'],
            ['nama_barang' => 'Monitor LED Samsung', 'kategori_id' => 1, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'it'],
            ['nama_barang' => 'All-in-One PC HP', 'kategori_id' => 1, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'it'],

            // Printer & Scanner (kategori_id: 2)
            ['nama_barang' => 'Printer Epson L3150', 'kategori_id' => 2, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'it'],
            ['nama_barang' => 'Scanner Brother', 'kategori_id' => 2, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'it'],
            ['nama_barang' => 'Printer HP LaserJet', 'kategori_id' => 2, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'it'],
            ['nama_barang' => 'Scanner Epson', 'kategori_id' => 2, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'it'],
            ['nama_barang' => 'Printer Canon Pixma', 'kategori_id' => 2, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'it'],

            // Perangkat Jaringan (kategori_id: 3)
            ['nama_barang' => 'Router Mikrotik', 'kategori_id' => 3, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'it'],
            ['nama_barang' => 'Switch Hub TP-Link', 'kategori_id' => 3, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'it'],
            ['nama_barang' => 'Access Point Unifi', 'kategori_id' => 3, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'it'],
            ['nama_barang' => 'Kabel LAN Cat6', 'kategori_id' => 3, 'satuan_id' => 3, 'jenis' => '0', 'pu' => 'it'],
            ['nama_barang' => 'Network Card', 'kategori_id' => 3, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'it'],

            // Aksesoris Komputer (kategori_id: 4)
            ['nama_barang' => 'Mouse Wireless Logitech', 'kategori_id' => 4, 'satuan_id' => 1, 'jenis' => '0', 'pu' => 'it'],
            ['nama_barang' => 'Keyboard Mechanical', 'kategori_id' => 4, 'satuan_id' => 1, 'jenis' => '0', 'pu' => 'it'],
            ['nama_barang' => 'Webcam Logitech', 'kategori_id' => 4, 'satuan_id' => 1, 'jenis' => '0', 'pu' => 'it'],
            ['nama_barang' => 'USB Hub', 'kategori_id' => 4, 'satuan_id' => 1, 'jenis' => '0', 'pu' => 'it'],
            ['nama_barang' => 'Extended Monitor Stand', 'kategori_id' => 4, 'satuan_id' => 1, 'jenis' => '0', 'pu' => 'it'],

            // Software & Lisensi (kategori_id: 5)
            ['nama_barang' => 'Windows 10 Pro License', 'kategori_id' => 5, 'satuan_id' => 1, 'jenis' => '0', 'pu' => 'it'],
            ['nama_barang' => 'Office 365 License', 'kategori_id' => 5, 'satuan_id' => 1, 'jenis' => '0', 'pu' => 'it'],
            ['nama_barang' => 'Adobe Creative Cloud', 'kategori_id' => 5, 'satuan_id' => 1, 'jenis' => '0', 'pu' => 'it'],
            ['nama_barang' => 'Antivirus License', 'kategori_id' => 5, 'satuan_id' => 1, 'jenis' => '0', 'pu' => 'it'],
            ['nama_barang' => 'AutoCAD License', 'kategori_id' => 5, 'satuan_id' => 1, 'jenis' => '0', 'pu' => 'it'],

            // Alat Tulis (kategori_id: 6)
            ['nama_barang' => 'Pulpen', 'kategori_id' => 6, 'satuan_id' => 2, 'jenis' => '0', 'pu' => 'log'],
            ['nama_barang' => 'Pensil', 'kategori_id' => 6, 'satuan_id' => 2, 'jenis' => '0', 'pu' => 'log'],
            ['nama_barang' => 'Spidol', 'kategori_id' => 6, 'satuan_id' => 2, 'jenis' => '0', 'pu' => 'log'],
            ['nama_barang' => 'Penghapus', 'kategori_id' => 6, 'satuan_id' => 2, 'jenis' => '0', 'pu' => 'log'],
            ['nama_barang' => 'Tip-X', 'kategori_id' => 6, 'satuan_id' => 2, 'jenis' => '0', 'pu' => 'log'],

            // Kertas & Amplop (kategori_id: 7)
            ['nama_barang' => 'Kertas HVS A4', 'kategori_id' => 7, 'satuan_id' => 4, 'jenis' => '0', 'pu' => 'log'],
            ['nama_barang' => 'Kertas F4', 'kategori_id' => 7, 'satuan_id' => 4, 'jenis' => '0', 'pu' => 'log'],
            ['nama_barang' => 'Amplop Putih', 'kategori_id' => 7, 'satuan_id' => 2, 'jenis' => '0', 'pu' => 'log'],
            ['nama_barang' => 'Amplop Coklat', 'kategori_id' => 7, 'satuan_id' => 2, 'jenis' => '0', 'pu' => 'log'],
            ['nama_barang' => 'Kertas Label', 'kategori_id' => 7, 'satuan_id' => 4, 'jenis' => '0', 'pu' => 'log'],

            // Furnitur Kantor (kategori_id: 8)
            ['nama_barang' => 'Kursi Kantor', 'kategori_id' => 8, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'log'],
            ['nama_barang' => 'Meja Kerja', 'kategori_id' => 8, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'log'],
            ['nama_barang' => 'Lemari Arsip', 'kategori_id' => 8, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'log'],
            ['nama_barang' => 'Rak Dokumen', 'kategori_id' => 8, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'log'],
            ['nama_barang' => 'Meja Rapat', 'kategori_id' => 8, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'log'],

            // Perlengkapan Arsip (kategori_id: 9)
            ['nama_barang' => 'Map Plastik', 'kategori_id' => 9, 'satuan_id' => 2, 'jenis' => '0', 'pu' => 'log'],
            ['nama_barang' => 'Ordner', 'kategori_id' => 9, 'satuan_id' => 2, 'jenis' => '0', 'pu' => 'log'],
            ['nama_barang' => 'Box File', 'kategori_id' => 9, 'satuan_id' => 2, 'jenis' => '0', 'pu' => 'log'],
            ['nama_barang' => 'Binder Clip', 'kategori_id' => 9, 'satuan_id' => 2, 'jenis' => '0', 'pu' => 'log'],
            ['nama_barang' => 'Paper Clip', 'kategori_id' => 9, 'satuan_id' => 2, 'jenis' => '0', 'pu' => 'log'],

            // Peralatan Presentasi (kategori_id: 10)
            ['nama_barang' => 'Proyektor', 'kategori_id' => 10, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'it'],
            ['nama_barang' => 'Layar Proyektor', 'kategori_id' => 10, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'it'],
            ['nama_barang' => 'Pointer Laser', 'kategori_id' => 10, 'satuan_id' => 1, 'jenis' => '0', 'pu' => 'it'],
            ['nama_barang' => 'Wireless Presenter', 'kategori_id' => 10, 'satuan_id' => 1, 'jenis' => '0', 'pu' => 'it'],
            ['nama_barang' => 'Whiteboard Digital', 'kategori_id' => 10, 'satuan_id' => 1, 'jenis' => '1', 'pu' => 'it'],

            // Kebersihan (kategori_id: 11)
            ['nama_barang' => 'Sapu', 'kategori_id' => 11, 'satuan_id' => 1, 'jenis' => '0', 'pu' => 'log'],
            ['nama_barang' => 'Pel Lantai', 'kategori_id' => 11, 'satuan_id' => 1, 'jenis' => '0', 'pu' => 'log'],
            ['nama_barang' => 'Pembersih Kaca', 'kategori_id' => 11, 'satuan_id' => 2, 'jenis' => '0', 'pu' => 'log'],
            ['nama_barang' => 'Tissue', 'kategori_id' => 11, 'satuan_id' => 2, 'jenis' => '0', 'pu' => 'log'],
            ['nama_barang' => 'Tempat Sampah', 'kategori_id' => 11, 'satuan_id' => 1, 'jenis' => '0', 'pu' => 'log'],
        ];

        // Generate items with initials as codes
        $data = [];
        $usedCodes = [];

        for ($i = 1; $i <= 100; $i++) {
            $item = $items[($i - 1) % count($items)];
            $baseInitials = $this->generateInitials($item['nama_barang']);

            // Add number suffix if code already exists
            $counter = 1;
            $finalCode = $baseInitials;
            while (in_array($finalCode, $usedCodes)) {
                $finalCode = $baseInitials . $counter;
                $counter++;
            }
            $usedCodes[] = $finalCode;

            $data[] = [
                'kode_barang' => $finalCode,
                'nama_barang' => $item['nama_barang'] . ' ' . $i,
                'satuan_id' => $item['satuan_id'],
                'kategori_id' => $item['kategori_id'],
                'jenis' => $item['jenis'],
                'pu' => $item['pu'],
                'keterangan' => 'Keterangan untuk ' . $item['nama_barang'] . ' ' . $i
            ];
        }

        foreach ($data as $item) {
            MasterBarang::create($item);
        }
    }
}
