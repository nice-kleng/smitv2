<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Modules\Inventory\Models\Inventory;
use Modules\Inventory\Models\Permintaan;
use Modules\Inventory\Models\Ticket;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $roles = $user->roles;

        // Jika user adalah superadmin atau admin, berikan akses ke semua role
        if ($roles->contains('name', 'superadmin')) {
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
            case 'direktur':
                return [
                    'totalKaryawan' => \App\Models\User::count(),
                    'totalUnit' => \App\Models\Unit::count(),
                    'recentLogBooks' => \App\Models\LogBook::latest()->take(5)->get(),
                    'pendingApprovals' => \App\Models\LogBook::where('status', 'pending')->count(),
                ];

            case 'keuangan':
                return [
                    // 'totalAnggaran' => \App\Models\Budget::sum('amount'),
                    // 'recentTransactions' => \App\Models\Transaction::latest()->take(5)->get(),
                    // 'pendingRequests' => \App\Models\BudgetRequest::where('status', 'pending')->get(),
                ];

            case 'admin':
                $query = Permintaan::query();

                // Jika bukan admin/superadmin, filter berdasarkan PU
                if (!$isAdminOrSuperadmin) {
                    $query->where('pu', $user->pu_kd);
                }

                return [
                    'totalPermintaan' => $query->count(),
                    'totalPermintaanMenungguDiambil' => $query->where('status', '2')->count(),
                    'totalPermintaanBaru' => $query->where('status', '0')->count(),
                ];

            case 'teknisi':
                $query = Ticket::query();

                // Jika bukan admin/superadmin, filter berdasarkan teknisi
                if (!$isAdminOrSuperadmin) {
                    $query->where('teknisi_id', $user->id);
                }

                return [
                    'completedTickets' => $query->where('status', '1')->count(),
                    'newTickets' => $query->where('status', '0')->count(),
                ];

            case 'unit':
                $inventory = Inventory::query();
                $permintaan = Permintaan::query();

                if (!$isAdminOrSuperadmin) {
                    $inventory->where('unit_id', Auth::user()->unit_id);
                    $permintaan->where('unit_id', Auth::user()->unit_id);
                }

                return [
                    'invetarisRuangan' => $inventory->count(),
                    // 'invetarisUnit' => $inventory->count(),
                    'permintaanDalamProses' => $permintaan->where('status', '0')->count(),
                ];

            case 'superadmin':
                return [
                    'totalUsers' => \App\Models\User::count(),
                    'totalRoles' => Role::count(),
                    'totalUnits' => \App\Models\Unit::count(),
                    'recentActivities' => \Spatie\Activitylog\Models\Activity::latest()->take(10)->get(),
                    'systemStats' => $this->getSystemStats(),
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
}
