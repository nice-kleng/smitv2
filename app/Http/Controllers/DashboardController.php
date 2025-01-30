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
            $roles = Role::all(); // Mengambil semua role yang ada di sistem
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
        $isAdminOrSuperadmin = $user->roles->contains('name', 'superadmin');

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
                return [
                    'totalPengeluaranBulanIni' => Pengajuan::whereMonth('tanggal_approved', now()->month)->whereYear('tanggal_approved', now()->year)->where(
                        'status',
                        '2'
                    )->sum('harga_approved'),
                    'pengajuanBaru' => Pengajuan::where('status', '0')->groupBy('kode_pengajuan')->count(),
                    // 'recentTransactions' => \App\Models\Transaction::latest()->take(5)->get(),
                    // 'pendingRequests' => \App\Models\BudgetRequest::where('status', 'pending')->get(),
                ];

            case 'admin':
                $query = Permintaan::query();

                // Jika bukan admin/superadmin, filter berdasarkan PU
                if (!$isAdminOrSuperadmin) {
                    $query->where('pu', $user->pu_kd);
                }

                // Clone query untuk digunakan pada perhitungan lainnya
                $unitQuery = clone $query;
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

                // Ambil data permintaan per bulan dan update array bulanDalamSetahun
                $dataPermintaan = $grafikQuery
                    ->selectRaw('MONTH(tanggal_permintaan) as bulan')
                    ->selectRaw('COUNT(DISTINCT kode_permintaan) as total_permintaan')
                    ->whereYear('tanggal_permintaan', now()->year)
                    ->groupBy('bulan')
                    ->get();

                foreach ($dataPermintaan as $item) {
                    $bulanDalamSetahun[$item->bulan]['total'] = $item->total_permintaan;
                }

                return [
                    'totalPermintaan' => $query->groupBy('kode_permintaan')->count(),
                    'totalPermintaanMenungguDiambil' => $query->where('status', '2')->groupBy('kode_permintaan')->count(),
                    'totalPermintaanBaru' => $query->where('status', '0')->groupBy('kode_permintaan')->count(),
                    'unitPermintaanTerbanyak' => $unitQuery->select('unit_id')
                        ->selectRaw('COUNT(DISTINCT kode_permintaan) as total_permintaan')
                        ->groupBy('unit_id')
                        ->with('unit')
                        ->orderByRaw('COUNT(DISTINCT kode_permintaan) DESC')
                        ->first(),
                    // Data untuk grafik permintaan per bulan tahun ini
                    'grafikPermintaan' => array_values($bulanDalamSetahun),
                ];

            case 'teknisi':
                $query = Ticket::query();

                // Jika bukan admin/superadmin, filter berdasarkan teknisi
                // if (!$isAdminOrSuperadmin) {
                //     $query->where('teknisi_id', $user->id);
                // }

                // Clone query untuk digunakan pada perhitungan lainnya
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

                return [
                    'completedTickets' => $query->where('status', '1')->count(),
                    'newTickets' => $query->where('status', '0')->count(),
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
                ];

            case 'unit':
                $inventory = Inventory::query();
                $permintaan = Permintaan::query();

                if (!$isAdminOrSuperadmin) {
                    $inventory->where('ruangan_id', Auth::user()->ruangan_id);
                    $permintaan->where('unit_id', Auth::user()->unit_id);
                }

                return [
                    'invetarisRuangan' => $inventory->count(),
                    'permintaanDalamProses' => $permintaan->where('status', '0')->count(),
                ];

                // case 'superadmin':
                //     return [
                //         'totalUsers' => \App\Models\User::count(),
                //         'totalRoles' => Role::count(),
                //         'totalUnits' => \App\Models\Unit::count(),
                //         'recentActivities' => \Spatie\Activitylog\Models\Activity::latest()->take(10)->get(),
                //         'systemStats' => $this->getSystemStats(),
                //     ];

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
}
