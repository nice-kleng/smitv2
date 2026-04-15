<?php

namespace App\Exports;

use Modules\Inventory\Models\MasterBarang;
use Modules\Inventory\Models\StokClosing;
use Modules\Inventory\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LaporanStokExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $bulan;
    protected $tahun;
    protected $kategori;
    protected $rowNumber = 0;

    public function __construct($bulan, $tahun, $kategori = null)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->kategori = $kategori;
    }

    public function collection()
    {
        $periode = sprintf('%d-%02d', $this->tahun, $this->bulan);

        // Cek apakah periode ini sudah di-closing
        $hasClosing = StokClosing::where('periode', $periode)
            ->when(isset($this->kategori) && $this->kategori != '', function ($query) {
                $query->whereHas('stok.barang.kategori', function ($q) {
                    $q->where('id', $this->kategori);
                });
            })
            ->where('is_closed', true)
            ->exists();

        if ($hasClosing) {
            return $this->getDataFromClosing($periode, $this->kategori);
        } else {
            return $this->getDataRealtime($this->kategori);
        }
    }

    private function getDataFromClosing($periode, $kategori = null)
    {
        $query = StokClosing::with(['stok.barang.satuan', 'stok.barang.kategori'])
            ->when(isset($kategori) && $kategori != '', function ($query) use ($kategori) {
                $query->whereHas('stok.barang.kategori', function ($q) use ($kategori) {
                    $q->where('id', $kategori);
                });
            })
            ->where('periode', $periode)
            ->where('is_closed', true);

        // Filter berdasarkan PU user
        if (Auth::check()) {
            if (Auth::user()->pu_kd === 'it') {
                $query->whereHas('stok.barang', function ($q) {
                    $q->where('is_elektronik', true);
                });
            }

            if (Auth::user()->pu_kd === 'log') {
                $query->whereHas('stok.barang', function ($q) {
                    $q->where('is_elektronik', false);
                });
            }
        }

        $closings = $query->get();
        $laporanData = collect();

        foreach ($closings as $closing) {
            $laporanData->push((object)[
                'kode_barang' => $closing->stok->barang->kode_barang ?? '-',
                'nama_barang' => $closing->stok->barang->nama_barang ?? '-',
                'satuan' => $closing->stok->barang->satuan->nama ?? '-',
                'kategori' => $closing->stok->barang->kategori->nama ?? '-',
                'stok_awal' => $closing->stok_awal,
                'stok_masuk' => $closing->stok_masuk ?? 0,
                'stok_keluar' => $closing->stok_keluar ?? 0,
                'stok_akhir' => $closing->stok_fisik ?? $closing->stok_akhir_sistem,
                'harga_per_unit' => $closing->harga_per_unit,
                'total_nilai_akhir' => $closing->total_nilai_akhir,
            ]);
        }

        return $laporanData;
    }

    private function getDataRealtime($kategori = null)
    {
        $startDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->endOfMonth();

        // Query barang dengan filter PU
        $barangs = MasterBarang::with(['satuan', 'kategori', 'stoks']);

        if (Auth::check()) {
            if (Auth::user()->pu_kd === 'it') {
                $barangs->where('is_elektronik', true);
            }

            if (Auth::user()->pu_kd === 'log') {
                $barangs->where('is_elektronik', false);
            }
        }

        if (isset($kategori) && $kategori != '') {
            $barangs->where('kategori_id', $kategori);
        }

        $laporanData = collect();

        foreach ($barangs->get() as $barang) {
            // Ambil harga dari stok pertama
            $hargaPerUnit = $barang->stoks->first()?->harga ?? 0;

            // Stok saat ini dari tabel stok
            $stokSekarang = $barang->stoks->sum('stok');

            // Stok masuk di bulan ini
            $stokMasuk = Transaksi::where('jenis', '1')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('stok', function ($q) use ($barang) {
                    $q->where('master_barang_id', $barang->id);
                })
                ->sum('jumlah');

            // Stok keluar di bulan ini
            $stokKeluar = Transaksi::where('jenis', '0')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('stok', function ($q) use ($barang) {
                    $q->where('master_barang_id', $barang->id);
                })
                ->sum('jumlah');

            // Hitung stok akhir bulan sebelumnya
            $stokAkhirBulanSebelumnya = $stokSekarang - $stokMasuk + $stokKeluar;

            // Hitung stok akhir bulan ini
            $stokAkhirBulanIni = $stokAkhirBulanSebelumnya + $stokMasuk - $stokKeluar;

            // Hitung nilai
            $totalNilaiAkhir = $stokAkhirBulanIni * $hargaPerUnit;

            $laporanData->push((object)[
                'kode_barang' => $barang->kode_barang ?? '-',
                'nama_barang' => $barang->nama_barang ?? '-',
                'satuan' => $barang->satuan->nama ?? '-',
                'kategori' => $barang->kategori->nama ?? '-',
                'stok_awal' => $stokAkhirBulanSebelumnya,
                'stok_masuk' => $stokMasuk ?? 0,
                'stok_keluar' => $stokKeluar ?? 0,
                'stok_akhir' => $stokAkhirBulanIni,
                'harga_per_unit' => $hargaPerUnit,
                'total_nilai_akhir' => $totalNilaiAkhir,
            ]);
        }

        return $laporanData;
    }

    public function collectionOld() {}

    public function headings(): array
    {
        return [
            ['LAPORAN STOK BARANG'],
            ['Periode: ' . $this->getIndonesianMonth($this->bulan) . ' ' . $this->tahun],
            ['Tanggal Cetak: ' . Carbon::now()->translatedFormat('d F Y H:i:s')],
            [], // Baris kosong
            [
                'No',
                'Kode Barang',
                'Nama Barang',
                'Satuan',
                'Kategori',
                'Stok Awal',
                'Stok Masuk',
                'Stok Keluar',
                'Stok Akhir',
                'Harga/Unit',
                'Total Nilai'
            ]
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $row->kode_barang,
            $row->nama_barang,
            $row->satuan,
            $row->kategori,
            $row->stok_awal,
            $row->stok_masuk,
            $row->stok_keluar,
            $row->stok_akhir,
            $row->harga_per_unit,
            $row->total_nilai_akhir,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells untuk judul
        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');
        $sheet->mergeCells('A3:K3');

        // Style judul
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A2:K3')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Style header tabel
        $sheet->getStyle('A5:K5')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Border untuk data
        $lastRow = $this->rowNumber + 5;
        $sheet->getStyle('A5:K' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Alignment untuk kolom angka
        $sheet->getStyle('F6:K' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Format currency untuk kolom harga dan nilai
        $sheet->getStyle('J6:K' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
            'number' => [
                'formatCode' => '_-* #,##0.00_-;-* #,##0.00_-;_-* "-"_-;_-@_-',
            ],
        ]);

        return [];
    }

    public function title(): string
    {
        return 'Laporan Stok ' . $this->getIndonesianMonth($this->bulan) . ' ' . $this->tahun;
    }

    private function getIndonesianMonth($month)
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        return $months[$month] ?? '';
    }
}
