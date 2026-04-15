<?php

namespace Modules\Inventory\Http\Controllers;

use App\Exports\LaporanStokExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Models\MasterBarang;
use Modules\Inventory\Models\StokClosing;
use Modules\Inventory\Models\Transaksi;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LaporanStokController extends Controller
{
    public function index()
    {
        return view('inventory::laporan.stok.index');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2000',
        ]);

        $data = $this->getLaporanData($request->bulan, $request->tahun);

        return view('inventory::laporan.stok.preview', $data);
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2000',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $bulanName = $this->getIndonesianMonth($bulan);
        $filename = 'Laporan_Stok_' . $bulanName . '_' . $tahun . '.xlsx';

        return Excel::download(new LaporanStokExport($bulan, $tahun, $request->kategori), $filename);
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2000',
        ]);

        $data = $this->getLaporanData($request->bulan, $request->tahun, $request->kategori);

        // Set paper size dan orientation
        $pdf = Pdf::loadView('inventory::laporan.stok.pdf', $data)
            ->setPaper('a4', 'landscape');

        $bulanName = $this->getIndonesianMonth($request->bulan);
        $filename = 'Laporan_Stok_' . $bulanName . '_' . $request->tahun . '.pdf';

        return $pdf->stream($filename);
    }

    private function getLaporanData($bulan, $tahun, $kategori = null)
    {
        $periode = sprintf('%d-%02d', $tahun, $bulan);

        // Cek apakah periode ini sudah di-closing
        $hasClosing = StokClosing::where('periode', $periode)
            ->when(isset($kategori) && $kategori != '', function ($query) use ($kategori) {
                $query->whereHas('stok.barang.kategori', function ($q) use ($kategori) {
                    $q->where('id', $kategori);
                });
            })
            ->where('is_closed', true)
            ->exists();

        if ($hasClosing) {
            // Jika sudah closing, ambil dari tabel closing (SUPER CEPAT!)
            return $this->getLaporanFromClosing($bulan, $tahun, $periode, $kategori);
        } else {
            // Jika belum closing, hitung realtime (cara lama)
            return $this->getLaporanRealtime($bulan, $tahun, $periode, $kategori);
        }
    }

    private function getLaporanFromClosing($bulan, $tahun, $periode, $kategori = null)
    {
        // Query barang dengan filter PU
        $query = StokClosing::with(['stok.barang.satuan', 'stok.barang.kategori'])
            ->where('periode', $periode)
            ->when(isset($kategori) && $kategori != '', function ($query) use ($kategori) {
                $query->whereHas('stok.barang.kategori', function ($q) use ($kategori) {
                    $q->where('id', $kategori);
                });
            })
            ->where('is_closed', true);

        // Filter berdasarkan PU user
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

        $closings = $query->get();

        $laporanData = [];

        foreach ($closings as $closing) {
            $laporanData[] = [
                'kode_barang' => $closing->stok->barang->kode_barang ?? '-',
                'nama_barang' => $closing->stok->barang->nama_barang ?? '-',
                'satuan' => $closing->stok->barang->satuan->nama_satuan ?? '-',
                'kategori' => $closing->stok->barang->kategori->nama_kategori ?? '-',
                'batch_keterangan' => $closing->stok->keterangan ?? '-',
                'stok_awal' => $closing->stok_awal,
                'stok_masuk' => $closing->stok_masuk,
                'stok_keluar' => $closing->stok_keluar,
                'stok_akhir' => $closing->stok_fisik ?? $closing->stok_akhir_sistem,
                'harga_per_unit' => $closing->harga_per_unit,
                'total_nilai_awal' => $closing->total_nilai_awal,
                'total_nilai_masuk' => $closing->total_nilai_masuk,
                'total_nilai_keluar' => $closing->total_nilai_keluar,
                'total_nilai_akhir' => $closing->total_nilai_akhir,
            ];
        }

        return [
            'laporan' => $laporanData,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'periode' => $this->getIndonesianMonth($bulan) . ' ' . $tahun,
            'tanggal_cetak' => Carbon::now()->translatedFormat('d F Y H:i:s'),
            'is_from_closing' => true,
            'closed_at' => $closings->first()->closed_at ?? null,
        ];
    }

    private function getLaporanRealtime($bulan, $tahun, $periode, $kategori = null)
    {
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        // Query barang dengan filter PU
        $barangs = MasterBarang::with(['satuan', 'kategori', 'stoks']);

        if (Auth::user()->pu_kd === 'it') {
            $barangs->where('is_elektronik', true);
        }

        if (Auth::user()->pu_kd === 'log') {
            $barangs->where('is_elektronik', false);
        }

        if (isset($kategori) && $kategori != '') {
            $barangs->where('kategori_id', $kategori);
        }

        $laporanData = [];

        foreach ($barangs->get() as $barang) {
            // Ambil harga dari stok pertama (untuk realtime gunakan harga terakhir)
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
            $totalNilaiAwal = $stokAkhirBulanSebelumnya * $hargaPerUnit;
            $totalNilaiMasuk = $stokMasuk * $hargaPerUnit;
            $totalNilaiKeluar = $stokKeluar * $hargaPerUnit;
            $totalNilaiAkhir = $stokAkhirBulanIni * $hargaPerUnit;

            $laporanData[] = [
                'kode_barang' => $barang->kode_barang ?? '-',
                'nama_barang' => $barang->nama_barang ?? '-',
                'satuan' => $barang->satuan->nama ?? '-',
                'kategori' => $barang->kategori->nama ?? '-',
                'batch_keterangan' => '-',
                'stok_awal' => $stokAkhirBulanSebelumnya,
                'stok_masuk' => $stokMasuk,
                'stok_keluar' => $stokKeluar,
                'stok_akhir' => $stokAkhirBulanIni,
                'harga_per_unit' => $hargaPerUnit,
                'total_nilai_awal' => $totalNilaiAwal,
                'total_nilai_masuk' => $totalNilaiMasuk,
                'total_nilai_keluar' => $totalNilaiKeluar,
                'total_nilai_akhir' => $totalNilaiAkhir,
            ];
        }

        return [
            'laporan' => $laporanData,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'periode' => $this->getIndonesianMonth($bulan) . ' ' . $tahun,
            'tanggal_cetak' => Carbon::now()->translatedFormat('d F Y H:i:s'),
            'is_from_closing' => false,
        ];
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
