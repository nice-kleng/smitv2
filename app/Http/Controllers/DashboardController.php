<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Inventory;
use Modules\Inventory\Models\Pengajuan;
use Modules\Inventory\Models\Permintaan;
use Modules\Inventory\Models\Ticket;
use Modules\Inventory\Models\Transaksi;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $roles = $user->roles;

        // Jika user adalah superadmin atau admin, berikan akses ke semua role
        if ($roles->contains('name', 'superadmin') || $roles->contains('name', 'direktur')) {
            // $roles = Role::all(); // Mengambil semua role yang ada di sistem
            $roles = Role::whereNotIn('name', ['unit'])->get();
        }

        if ($roles->isEmpty()) {
            return redirect()->route('home')->with('error', 'Anda tidak memiliki akses ke dashboard.');
        }

        $dashboardData = [];

        foreach ($roles as $role) {
            $dashboardData[$role->name] = $this->getDashboardDataByRole($role->name);
        }

        return view('dashboard', [
            'roles' => $roles,
            'dashboardData' => $dashboardData
        ]);
    }

    private function getDashboardDataByRole($role)
    {
        $user = Auth::user();
        $isAdminOrSuperadmin = $user->roles->whereIn('name', ['superadmin', 'direktur'])->isNotEmpty();

        // Izinkan superadmin dan admin mengakses semua data
        if (!$isAdminOrSuperadmin && !$user->roles->contains('name', $role)) {
            return [];
        }

        switch ($role) {
                // case 'direktur':
                //     return [
                //         'totalUnit' => \App\Models\Unit::count(),
                //         'logBook' => \App\Models\LogBook::latest()->take(5)->get(),
                //     ];

            case 'keuangan':
                $bulanDalamSetahun = [];
                for ($i = 1; $i <= 12; $i++) {
                    $bulanDalamSetahun[$i] = [
                        'bulan' => date('F', mktime(0, 0, 0, $i, 1)),
                        'bulan_angka' => $i,
                        'total' => 0
                    ];
                }

                $dataPengajuan = Pengajuan::selectRaw('MONTH(tanggal_pengajuan) as bulan')
                    ->selectRaw('COUNT(DISTINCT SUBSTRING(kode_pengajuan, 1, 16)) as total_pengajuan')
                    ->whereYear('tanggal_pengajuan', now()->year)
                    ->groupBy('bulan')
                    ->orderBy('bulan')
                    ->get();

                foreach ($dataPengajuan as $item) {
                    $bulanDalamSetahun[$item->bulan]['total'] = $item->total_pengajuan;
                }

                return [
                    'totalPengeluaranBulanIni' => Pengajuan::whereMonth('tanggal_approved', now()->month)
                        ->whereYear('tanggal_approved', now()->year)
                        ->whereNotIn('status', ['0', '1'])
                        ->sum('harga_approved'),
                    'pengajuanBaru' => DB::table('pengajuans')
                        ->select(DB::raw('SUBSTRING(kode_pengajuan, 1, 16) as kode_prefix'))
                        ->where('status', '0')
                        ->distinct()
                        ->count(),
                    'grafikPengajuan' => array_values($bulanDalamSetahun),
                ];

            case 'admin':
                // Base query untuk permintaan
                $baseQuery = Permintaan::query();

                // Filter berdasarkan PU jika bukan admin/superadmin
                if (!$isAdminOrSuperadmin) {
                    $baseQuery->where('pu', $user->pu_kd);
                }

                // Hitung total permintaan (distinct kode_permintaan)
                $totalPermintaan = (clone $baseQuery)
                    ->distinct()
                    ->count('kode_permintaan');

                // Hitung permintaan menunggu diambil
                $totalPermintaanMenungguDiambil = (clone $baseQuery)
                    ->where('status', '2')
                    ->distinct()
                    ->count('kode_permintaan');

                // Hitung permintaan baru
                $totalPermintaanBaru = (clone $baseQuery)
                    ->where('status', '0')
                    ->distinct()
                    ->count('kode_permintaan');

                // Query untuk unit dengan permintaan terbanyak (5 teratas)
                $unitPermintaanTerbanyak = (clone $baseQuery)
                    ->select('unit_id', 'ruangan_id')
                    ->selectRaw('COUNT(DISTINCT kode_permintaan) as total_permintaan')
                    ->groupBy('unit_id', 'ruangan_id')
                    ->with(['unit', 'ruangan'])  // Load relasi unit dan ruangan
                    ->orderByRaw('COUNT(DISTINCT kode_permintaan) DESC')
                    ->take(5)
                    ->get();

                // Inisialisasi array bulan
                $bulanDalamSetahun = collect(range(1, 12))->mapWithKeys(function ($bulan) {
                    return [
                        $bulan => [
                            'bulan' => date('F', mktime(0, 0, 0, $bulan, 1)),
                            'bulan_angka' => $bulan,
                            'total' => 0
                        ]
                    ];
                })->toArray();

                // Query untuk data grafik permintaan per bulan
                $dataPermintaan = Permintaan::selectRaw('MONTH(tanggal_permintaan) as bulan')
                    ->selectRaw('COUNT(DISTINCT kode_permintaan) as total_permintaan')
                    ->whereYear('tanggal_permintaan', now()->year)
                    ->when(!$isAdminOrSuperadmin, function ($query) use ($user) {
                        return $query->where('pu', $user->pu_kd);
                    })
                    ->groupBy('bulan')
                    ->get();

                foreach ($dataPermintaan as $item) {
                    $bulanDalamSetahun[$item->bulan]['total'] = $item->total_permintaan;
                }

                return [
                    'totalPermintaan' => $totalPermintaan,
                    'totalPermintaanMenungguDiambil' => $totalPermintaanMenungguDiambil,
                    'totalPermintaanBaru' => $totalPermintaanBaru,
                    'unitPermintaanTerbanyak' => $unitPermintaanTerbanyak,
                    'grafikPermintaan' => array_values($bulanDalamSetahun),
                ];

            case 'teknisi':
                $query = Ticket::query();

                // Jika bukan admin/superadmin, filter berdasarkan teknisi
                // if (!$isAdminOrSuperadmin) {
                //     $query->where('teknisi_id', $user->id);
                // }
                $kategoriQuery = clone $query;
                $ruanganQuery = clone $query;
                $grafikQuery = clone $query;

                // Buat array untuk semua bulan dalam setahun
                $bulanDalamSetahun = [];
                for ($i = 1; $i <= 12; $i++) {
                    $bulanDalamSetahun[$i] = [
                        'bulan' => date('F', mktime(0, 0, 0, $i, 1)),
                        'bulan_angka' => $i,
                        'total' => 0
                    ];
                }

                // Ambil data kerusakan per bulan dan update array bulanDalamSetahun
                $dataKerusakan = $grafikQuery
                    // ->leftJoin('inventories', 'tickets.inventaris_id', '=', 'inventories.id')
                    // ->leftJoin('master_barangs', 'inventories.barang_id', '=', 'master_barangs.id')
                    // ->where('master_barangs.pu', 'it')
                    ->selectRaw('MONTH(tickets.created_at) as bulan')
                    ->selectRaw('COUNT(*) as total_ticket')
                    ->whereYear('tickets.created_at', now()->year)
                    ->groupBy('bulan')
                    ->get();

                foreach ($dataKerusakan as $item) {
                    $bulanDalamSetahun[$item->bulan]['total'] = $item->total_ticket;
                }

                // Query untuk mendapatkan latest history inventaris untuk barang IT
                $latestHistorySubquery = DB::table('history_inventaris')
                    ->join('inventories', 'history_inventaris.inventory_id', '=', 'inventories.id')
                    ->join('master_barangs', 'inventories.barang_id', '=', 'master_barangs.id')
                    ->where('master_barangs.pu', 'it')
                    ->select('history_inventaris.inventory_id', DB::raw('MAX(history_inventaris.id) as max_id'))
                    ->groupBy('history_inventaris.inventory_id');

                // Query untuk menghitung inventaris berdasarkan kondisi terakhir
                $inventoryStats = DB::table('history_inventaris as h')
                    ->joinSub($latestHistorySubquery, 'latest', function ($join) {
                        $join->on('h.id', '=', 'latest.max_id');
                    })
                    ->select('h.kondisi', DB::raw('COUNT(*) as total'))
                    ->groupBy('h.kondisi')
                    ->get()
                    ->keyBy('kondisi');

                // Setelah query $latestHistorySubquery yang sudah ada, tambahkan:
                $detailKondisiByKategori = DB::table('history_inventaris as h')
                    ->joinSub($latestHistorySubquery, 'latest', function ($join) {
                        $join->on('h.id', '=', 'latest.max_id');
                    })
                    ->join('inventories', 'h.inventory_id', '=', 'inventories.id')
                    ->join('master_barangs', 'inventories.barang_id', '=', 'master_barangs.id')
                    ->join('kategori_barangs', 'master_barangs.kategori_id', '=', 'kategori_barangs.id')
                    ->select(
                        'kategori_barangs.nama_kategori',
                        DB::raw('SUM(CASE WHEN h.kondisi = "0" THEN 1 ELSE 0 END) as rusak'),
                        DB::raw('SUM(CASE WHEN h.kondisi = "1" THEN 1 ELSE 0 END) as kurang_baik'),
                        DB::raw('SUM(CASE WHEN h.kondisi = "2" THEN 1 ELSE 0 END) as baik')
                    )
                    ->groupBy('kategori_barangs.nama_kategori')
                    ->get();

                return [
                    'completedTickets' => Ticket::where('status', '1')->count(),
                    'newTickets' => Ticket::where('status', '0')->count(),
                    'kategoriSeringRusak' => $kategoriQuery
                        ->join('inventories', 'tickets.inventaris_id', '=', 'inventories.id')
                        ->join('master_barangs', 'inventories.barang_id', '=', 'master_barangs.id')
                        ->join('kategori_barangs', 'master_barangs.kategori_id', '=', 'kategori_barangs.id')
                        ->where('master_barangs.pu', 'it')
                        ->select('kategori_barangs.id', 'kategori_barangs.nama_kategori')
                        ->selectRaw('COUNT(*) as total_kerusakan')
                        ->groupBy('kategori_barangs.id', 'kategori_barangs.nama_kategori')
                        ->orderByRaw('COUNT(*) DESC')
                        ->take(5)
                        ->get(),

                    'ruanganSeringRusak' => $ruanganQuery
                        ->join('ruangans', 'tickets.ruangan_id', '=', 'ruangans.id')
                        ->join('units', 'ruangans.unit_id', '=', 'units.id')
                        ->select('ruangans.id', 'ruangans.nama_ruangan', 'units.nama_unit')
                        ->selectRaw('COUNT(*) as total_kerusakan')
                        ->groupBy('ruangans.id', 'ruangans.nama_ruangan', 'units.nama_unit')
                        ->orderByRaw('COUNT(*) DESC')
                        ->take(5)
                        ->get(),

                    'totalInventarisRusak' => $inventoryStats->get('0', (object)['total' => 0])->total,
                    'totalInventarisKurangBaik' => $inventoryStats->get('1', (object)['total' => 0])->total,
                    'totalInventarisBaik' => $inventoryStats->get('2', (object)['total' => 0])->total,

                    'grafikKerusakan' => array_values($bulanDalamSetahun),
                    'detailKondisiByKategori' => $detailKondisiByKategori,
                ];

            case 'unit':
                $permintaanDalamProses = Permintaan::where('ruangan_id', Auth::user()->ruangan_id)->where('status', '0')->count();
                $permintaanApproved = Permintaan::where('ruangan_id', Auth::user()->ruangan_id)->where('status', '3')->count();
                $permintaanRejected = Permintaan::where('ruangan_id', Auth::user()->ruangan_id)->where('status', '1')->count();


                $grafik = Permintaan::query();
                $bulanDalamSetahun = [];
                for ($i = 1; $i <= 12; $i++) {
                    $bulanDalamSetahun[$i] = [
                        'bulan' => date('F', mktime(0, 0, 0, $i, 1)),
                        'bulan_angka' => $i,
                        'total' => 0
                    ];
                }

                $datapermintaan = $grafik->selectRaw('MONTH(permintaans.created_at) as bulan')
                    ->selectRaw('COUNT(*) as total_permintaan')
                    ->whereYear('permintaans.created_at', now()->year)
                    ->where('ruangan_id', Auth::user()->ruangan_id)
                    ->groupBy('bulan')
                    ->get();

                foreach ($datapermintaan as $item) {
                    $bulanDalamSetahun[$item->bulan]['total'] = $item->total_permintaan;
                }

                return [
                    'inventarisRuangan' => Inventory::where('ruangan_id', Auth::user()->ruangan_id)->count(),
                    'permintaanDalamProses' => $permintaanDalamProses,
                    'permintaanAccepted' => $permintaanApproved,
                    'permintaanRejected' => $permintaanRejected,
                    'grafikPermintaan' => array_values($bulanDalamSetahun),
                ];

            default:
                return [];
        }
    }

    private function getSystemStats()
    {
        try {
            return [
                'cpu' => function_exists('sys_getloadavg') ? sys_getloadavg()[0] : 0,
                'memory' => memory_get_usage(true),
                'storage' => disk_free_space(base_path()) ?? 0,
            ];
        } catch (\Exception $e) {
            return [
                'cpu' => 0,
                'memory' => 0,
                'storage' => 0,
            ];
        }
    }

    public function getDetailInventaris(Request $request)
    {
        $kategori = $request->kategori;
        $kondisi = $request->kondisi;

        $query = DB::table('history_inventaris as h')
            ->join(DB::raw('(
                SELECT inventory_id, MAX(id) as max_id
                FROM history_inventaris
                GROUP BY inventory_id
            ) latest'), function ($join) {
                $join->on('h.id', '=', 'latest.max_id');
            })
            ->join('inventories', 'h.inventory_id', '=', 'inventories.id')
            ->join('master_barangs', 'inventories.barang_id', '=', 'master_barangs.id')
            ->join('kategori_barangs', 'master_barangs.kategori_id', '=', 'kategori_barangs.id')
            ->join('ruangans', 'inventories.ruangan_id', '=', 'ruangans.id')
            ->join('units', 'ruangans.unit_id', '=', 'units.id')
            ->where('kategori_barangs.nama_kategori', $kategori)
            ->where('h.kondisi', $kondisi)
            ->select(
                'inventories.kode_barang',
                'inventories.no_barang',
                'inventories.merk',
                'inventories.type',
                'ruangans.nama_ruangan as ruangan',
                'units.nama_unit as unit',
            );

        return datatables()->of($query)->toJson();
    }
}
