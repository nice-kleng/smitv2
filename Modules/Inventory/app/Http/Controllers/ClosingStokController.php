<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KategoriBarang;
use Illuminate\Http\Request;
use Modules\Inventory\Models\StokClosing;
use Modules\Inventory\Models\MasterBarang;
use Modules\Inventory\Models\Stok;
use Modules\Inventory\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClosingStokController extends Controller
{
    public function index()
    {
        // Ambil periode unik dengan MAX(closed_at) untuk menghindari duplikat
        $closings = StokClosing::select(
            'periode',
            DB::raw('MAX(is_closed) as is_closed'),
            DB::raw('MAX(closed_by) as closed_by'),
            DB::raw('MAX(closed_at) as closed_at'),
            DB::raw('COUNT(*) as total_items'),
            DB::raw('SUM(total_nilai_akhir) as total_nilai')
        )
            ->groupBy('periode')
            ->orderBy('periode', 'desc')
            ->paginate(12);

        // Transform untuk mendapatkan relasi closedBy
        $closings->getCollection()->transform(function ($item) {
            $item->closedBy = $item->closed_by ? \App\Models\User::find($item->closed_by) : null;
            return $item;
        });

        return view('inventory::closing.index', compact('closings'));
    }

    public function create()
    {
        // Default ke bulan lalu
        $bulanLalu = Carbon::now()->subMonth();

        return view('inventory::closing.create', [
            'defaultBulan' => $bulanLalu->month,
            'defaultTahun' => $bulanLalu->year,
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2000',
        ]);

        $periode = sprintf('%d-%02d', $request->tahun, $request->bulan);

        // Cek apakah sudah ada dan sudah closed
        $existingClosed = StokClosing::where('periode', $periode)
            ->where('is_closed', true)
            ->exists();

        if ($existingClosed) {
            return redirect()->route('inventory.closing.index')
                ->with('error', "Periode {$periode} sudah di-closing dan tidak dapat diubah!");
        }

        // Cek apakah bulan sebelumnya sudah closing
        $periodeBefore = Carbon::createFromFormat('Y-m', $periode)->subMonth()->format('Y-m');
        $beforeNotClosed = StokClosing::where('periode', $periodeBefore)
            ->where('is_closed', false)
            ->exists();

        if ($beforeNotClosed) {
            return redirect()->route('inventory.closing.create')
                ->with('warning', "Periode sebelumnya ({$periodeBefore}) belum di-closing!");
        }

        // Generate data closing
        $data = $this->generateClosingData($request->bulan, $request->tahun);

        return view('inventory::closing.form', [
            'periode' => $periode,
            'periodeFormat' => $this->getIndonesianMonth($request->bulan) . ' ' . $request->tahun,
            'data' => $data,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ]);
    }

    private function generateClosingData($bulan, $tahun)
    {
        $periode = sprintf('%d-%02d', $tahun, $bulan);
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        // Periode bulan sebelumnya
        $periodeBefore = Carbon::createFromFormat('Y-m', $periode)->subMonth()->format('Y-m');

        // Filter stok berdasarkan PU user (via barang)
        $stoks = Stok::with(['barang.satuan', 'barang.kategori']);

        if (Auth::user()->pu_kd === 'it') {
            $stoks->whereHas('barang', function ($q) {
                $q->where('is_elektronik', true);
            });
        }

        if (Auth::user()->pu_kd === 'log') {
            $stoks->whereHas('barang', function ($q) {
                $q->where('is_elektronik', false);
            });
        }

        $data = [];

        foreach ($stoks->get() as $stok) {
            // Cek apakah sudah ada draft
            $existing = StokClosing::where('periode', $periode)
                ->where('stok_id', $stok->id)
                ->first();

            if ($existing) {
                // Jika sudah ada, ambil dari database
                $data[] = $existing;
                continue;
            }

            // Ambil stok awal dari closing bulan sebelumnya
            $closingBefore = StokClosing::where('periode', $periodeBefore)
                ->where('stok_id', $stok->id)
                ->first();

            $stokAwal = $closingBefore ? $closingBefore->stok_akhir_sistem : $stok->stok;

            // Hitung transaksi untuk STOK INI saja
            $stokMasuk = Transaksi::where('stok_id', $stok->id)
                ->where('jenis', '1')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('jumlah');

            $stokKeluar = Transaksi::where('stok_id', $stok->id)
                ->where('jenis', '0')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('jumlah');

            // Hitung stok akhir sistem
            $stokAkhirSistem = $stokAwal + $stokMasuk - $stokKeluar;

            // Ambil harga dari tabel stok
            $hargaPerUnit = $stok->harga;

            // Hitung nilai
            $totalNilaiAwal = $stokAwal * $hargaPerUnit;
            $totalNilaiMasuk = $stokMasuk * $hargaPerUnit;
            $totalNilaiKeluar = $stokKeluar * $hargaPerUnit;
            $totalNilaiAkhir = $stokAkhirSistem * $hargaPerUnit;

            $data[] = (object)[
                'id' => null,
                'stok_id' => $stok->id,
                'stok' => $stok,
                'periode' => $periode,
                'stok_awal' => $stokAwal,
                'stok_masuk' => $stokMasuk,
                'stok_keluar' => $stokKeluar,
                'stok_akhir_sistem' => $stokAkhirSistem,
                'harga_per_unit' => $hargaPerUnit,
                'total_nilai_awal' => $totalNilaiAwal,
                'total_nilai_masuk' => $totalNilaiMasuk,
                'total_nilai_keluar' => $totalNilaiKeluar,
                'total_nilai_akhir' => $totalNilaiAkhir,
                'stok_fisik' => null,
                'selisih' => null,
                'keterangan' => null,
                'is_closed' => false,
            ];
        }

        return collect($data);
    }

    public function saveDraft(Request $request)
    {
        $request->validate([
            'periode' => 'required|string',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2000',
            'items' => 'required|array',
            'items.*.stok_id' => 'required|exists:stoks,id',
            'items.*.stok_fisik' => 'nullable|integer|min:0',
            'items.*.keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Regenerate data untuk memastikan akurasi
            $generatedData = $this->generateClosingData($request->bulan, $request->tahun);
            $dataMap = $generatedData->keyBy('stok_id');

            foreach ($request->items as $item) {
                $stokId = $item['stok_id'];

                // Ambil data yang sudah digenerate
                if (!isset($dataMap[$stokId])) {
                    continue;
                }

                $generated = $dataMap[$stokId];

                $stokFisik = isset($item['stok_fisik']) && $item['stok_fisik'] !== '' ? (int)$item['stok_fisik'] : null;
                $selisih = null;
                $totalNilaiAkhir = $generated->total_nilai_akhir;

                // Hitung selisih jika ada stok fisik
                if ($stokFisik !== null) {
                    $selisih = $stokFisik - $generated->stok_akhir_sistem;
                    $totalNilaiAkhir = $stokFisik * $generated->harga_per_unit;
                }

                $data = [
                    'periode' => $request->periode,
                    'stok_id' => $stokId,
                    'stok_awal' => $generated->stok_awal,
                    'stok_masuk' => $generated->stok_masuk,
                    'stok_keluar' => $generated->stok_keluar,
                    'stok_akhir_sistem' => $generated->stok_akhir_sistem,
                    'harga_per_unit' => $generated->harga_per_unit,
                    'total_nilai_awal' => $generated->total_nilai_awal,
                    'total_nilai_masuk' => $generated->total_nilai_masuk,
                    'total_nilai_keluar' => $generated->total_nilai_keluar,
                    'total_nilai_akhir' => $totalNilaiAkhir,
                    'stok_fisik' => $stokFisik,
                    'selisih' => $selisih,
                    'keterangan' => $item['keterangan'] ?? null,
                    'is_closed' => false,
                ];

                StokClosing::updateOrCreate(
                    [
                        'periode' => $request->periode,
                        'stok_id' => $stokId,
                    ],
                    $data
                );
            }

            DB::commit();

            return redirect()->route('inventory.closing.generate')
                ->with('success', 'Draft berhasil disimpan!')
                ->with('bulan', $request->bulan)
                ->with('tahun', $request->tahun);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('inventory.closing.index')
                ->with('error', 'Gagal menyimpan draft: ' . $e->getMessage());
        }
    }

    public function prosesClosed(Request $request)
    {
        $request->validate([
            'periode' => 'required|string',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2000',
            'items' => 'required|array',
            'items.*.stok_id' => 'required|exists:stoks,id',
            'items.*.stok_fisik' => 'nullable|integer|min:0',
            'items.*.keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Regenerate data
            $generatedData = $this->generateClosingData($request->bulan, $request->tahun);
            $dataMap = $generatedData->keyBy('stok_id');

            $itemsSaved = 0;
            foreach ($request->items as $item) {
                $stokId = $item['stok_id'];

                if (!isset($dataMap[$stokId])) {
                    continue;
                }

                $generated = $dataMap[$stokId];

                $stokFisik = isset($item['stok_fisik']) && $item['stok_fisik'] !== '' ? (int)$item['stok_fisik'] : null;
                $selisih = null;
                $totalNilaiAkhir = $generated->total_nilai_akhir;

                if ($stokFisik !== null) {
                    $selisih = $stokFisik - $generated->stok_akhir_sistem;
                    $totalNilaiAkhir = $stokFisik * $generated->harga_per_unit;
                }

                $data = [
                    'periode' => $request->periode,
                    'stok_id' => $stokId,
                    'stok_awal' => $generated->stok_awal,
                    'stok_masuk' => $generated->stok_masuk,
                    'stok_keluar' => $generated->stok_keluar,
                    'stok_akhir_sistem' => $generated->stok_akhir_sistem,
                    'harga_per_unit' => $generated->harga_per_unit,
                    'total_nilai_awal' => $generated->total_nilai_awal,
                    'total_nilai_masuk' => $generated->total_nilai_masuk,
                    'total_nilai_keluar' => $generated->total_nilai_keluar,
                    'total_nilai_akhir' => $totalNilaiAkhir,
                    'stok_fisik' => $stokFisik,
                    'selisih' => $selisih,
                    'keterangan' => $item['keterangan'] ?? null,
                    'is_closed' => false,
                ];

                StokClosing::updateOrCreate(
                    [
                        'periode' => $request->periode,
                        'stok_id' => $stokId,
                    ],
                    $data
                );
                $itemsSaved++;
            }

            // Validasi
            $itemsWithoutKeterangan = StokClosing::where('periode', $request->periode)
                ->whereNotNull('selisih')
                ->where('selisih', '!=', 0)
                ->where(function ($q) {
                    $q->whereNull('keterangan')->orWhere('keterangan', '');
                })
                ->count();

            if ($itemsWithoutKeterangan > 0) {
                DB::rollBack();
                return redirect()->route('inventory.closing.generate')
                    ->with('error', "Ada {$itemsWithoutKeterangan} stok dengan selisih yang belum diberi keterangan!")
                    ->with('bulan', $request->bulan)
                    ->with('tahun', $request->tahun);
            }

            // Set is_closed = true
            $closings = StokClosing::where('periode', $request->periode)->get();
            foreach ($closings as $closing) {
                $closing->prosesClosed(Auth::id());
            }

            DB::commit();
            return redirect()->route('inventory.closing.index')
                ->with('success', "Closing stok periode {$request->periode} berhasil diproses! ({$itemsSaved} items)");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Closing error: ' . $e->getMessage());
            return redirect()->route('inventory.closing.index')
                ->with('error', 'Gagal proses closing: ' . $e->getMessage());
        }
    }

    public function detail($periode, Request $request)
    {
        $kategoris = KategoriBarang::when(true, function ($query) {
            if (Auth::user()->pu_kd === 'it') {
                $query->whereHas('masterBarang', function ($q) {
                    $q->where('is_elektronik', true);
                });
            }

            if (Auth::user()->pu_kd === 'log') {
                $query->whereHas('masterBarang', function ($q) {
                    $q->where('is_elektronik', false);
                });
            }
        })->orderBy('nama_kategori')->get();
        $closings = StokClosing::with(['stok.barang.satuan', 'stok.barang.kategori', 'closedBy'])
            ->when(Auth::user()->pu_kd === 'it', function ($query) {
                $query->whereHas('stok.barang', function ($q) {
                    $q->where('is_elektronik', true);
                });
            })
            ->when(Auth::user()->pu_kd === 'log', function ($query) {
                $query->whereHas('stok.barang', function ($q) {
                    $q->where('is_elektronik', false);
                });
            })
            ->when(isset($request->kategori) && $request->kategori != '', function ($query) use ($request) {
                $query->whereHas('stok.barang.kategori', function ($q) use ($request) {
                    $q->where('id', $request->kategori);
                });
            })
            ->where('periode', $periode)
            ->get();

        if ($closings->isEmpty()) {
            abort(404, 'Data closing tidak ditemukan');
        }

        $isClosed = $closings->first()->is_closed;

        return view('inventory::closing.detail', [
            'periode' => $periode,
            'closings' => $closings,
            'isClosed' => $isClosed,
            'kategoris' => $kategoris,
            'selectedKategori' => $request->kategori ?? '',
        ]);
    }

    public function reopen(Request $request, $periode)
    {
        $request->validate([
            'reopen_reason' => 'required|string|min:10',
        ]);

        DB::beginTransaction();
        try {
            $closings = StokClosing::where('periode', $periode)
                ->where('is_closed', true)
                ->get();

            if ($closings->isEmpty()) {
                return redirect()->back()->with('error', 'Data closing tidak ditemukan atau belum di-closing!');
            }

            foreach ($closings as $closing) {
                $closing->prosesReopen(Auth::id(), $request->reopen_reason);
            }

            DB::commit();
            return redirect()->back()->with('success', "Periode {$periode} berhasil dibuka kembali!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuka kembali: ' . $e->getMessage());
        }
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
