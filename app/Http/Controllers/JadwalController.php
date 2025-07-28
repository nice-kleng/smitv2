<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Shift;
use App\Models\User;
use App\Services\ScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class JadwalController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $schedules = Jadwal::with(['user', 'shift'])
                ->select(['id', 'user_id', 'shift_id', 'start_date', 'end_date', 'week_number', 'year', 'status', 'created_at']);

            return DataTables::of($schedules)
                ->addIndexColumn()
                ->addColumn('staff_name', function ($row) {
                    return $row->user->name ?? '-';
                })
                ->addColumn('shift_name', function ($row) {
                    return $row->shift->name ?? '-';
                })
                ->addColumn('period', function ($row) {
                    return Carbon::parse($row->start_date)->format('d/m/Y') . ' - ' . Carbon::parse($row->end_date)->format('d/m/Y');
                })
                ->addColumn('week_info', function ($row) {
                    return 'Week ' . $row->week_number . ', ' . $row->year;
                })
                ->addColumn('status_badge', function ($row) {
                    $badgeClass = $row->status === 'active' ? 'success' : 'secondary';
                    return '<span class="badge bg-' . $badgeClass . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<button type="button" class="btn btn-sm btn-info" onclick="viewSchedule(' . $row->id . ')" title="View Details"><i class="fas fa-eye"></i></button>';
                    $btn .= '<button type="button" class="btn btn-sm btn-warning" onclick="editSchedule(' . $row->id . ')" title="Edit"><i class="fas fa-edit"></i></button>';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger" onclick="deleteSchedule(' . $row->id . ')" title="Delete"><i class="fas fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('jadwal.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $staff = User::where('pu_kd', 'it')
            ->orderBy('name')
            ->get();

        $shifts = $this->scheduleService->getAvailableShifts();

        return view('jadwal.create', compact('staff', 'shifts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Log incoming request for debugging
            Log::info('Schedule creation request', [
                'request_data' => $request->all(),
                'user_id' => auth()->id() ?? 'guest'
            ]);

            // Enhanced validation
            $validator = Validator::make($request->all(), [
                'staff_schedules' => 'required|array|min:1',
                'staff_schedules.*.user_id' => 'required|integer|exists:users,id',
                'staff_schedules.*.shift_id' => 'required|integer|exists:shifts,id',
                'start_date' => 'required|date|after_or_equal:today',
                'months' => 'required|integer|min:1|max:12'
            ], [
                'staff_schedules.required' => 'Harap pilih minimal satu staff untuk dijadwalkan',
                'staff_schedules.min' => 'Harap pilih minimal satu staff untuk dijadwalkan',
                'staff_schedules.*.user_id.required' => 'Staff harus dipilih',
                'staff_schedules.*.user_id.integer' => 'ID Staff harus berupa angka',
                'staff_schedules.*.user_id.exists' => 'Staff yang dipilih tidak valid',
                'staff_schedules.*.shift_id.required' => 'Shift harus dipilih untuk setiap staff',
                'staff_schedules.*.shift_id.integer' => 'ID Shift harus berupa angka',
                'staff_schedules.*.shift_id.exists' => 'Shift yang dipilih tidak valid',
                'start_date.required' => 'Tanggal mulai harus diisi',
                'start_date.date' => 'Format tanggal mulai tidak valid',
                'start_date.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini',
                'months.required' => 'Durasi bulan harus diisi',
                'months.integer' => 'Durasi bulan harus berupa angka',
                'months.min' => 'Durasi minimal 1 bulan',
                'months.max' => 'Durasi maksimal 12 bulan'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed', ['errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Additional business logic validation
            $validationErrors = $this->scheduleService->validateSchedule($request->staff_schedules);
            if (!empty($validationErrors)) {
                Log::warning('Business validation failed', ['errors' => $validationErrors]);
                return response()->json([
                    'success' => false,
                    'message' => implode('. ', $validationErrors)
                ], 422);
            }

            // Validate date format
            try {
                $startDate = Carbon::parse($request->start_date);
                if ($startDate->isPast() && !$startDate->isToday()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tanggal mulai tidak boleh di masa lalu'
                    ], 422);
                }
            } catch (\Exception $e) {
                Log::error('Date parsing error', ['date' => $request->start_date, 'error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Format tanggal tidak valid'
                ], 422);
            }

            $months = (int)$request->months;
            // Check for existing schedules in the date range
            $endDate = $startDate->copy()->addMonths($months);
            $staffIds = array_column($request->staff_schedules, 'user_id');

            $existingSchedules = Jadwal::whereIn('user_id', $staffIds)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->where('start_date', '<=', $startDate->format('Y-m-d'))
                                ->where('end_date', '>=', $endDate->format('Y-m-d'));
                        });
                })
                ->with('user')
                ->get();

            if ($existingSchedules->count() > 0) {
                $conflictingStaff = $existingSchedules->pluck('user.name')->unique()->implode(', ');
                return response()->json([
                    'success' => false,
                    'message' => "Sudah ada jadwal untuk staff: {$conflictingStaff} pada periode yang dipilih"
                ], 422);
            }

            // Generate schedules
            $schedules = $this->scheduleService->generateScheduleForMonths(
                $request->staff_schedules,
                $request->start_date,
                $months
            );

            Log::info('Schedule created successfully', [
                'staff_count' => count($request->staff_schedules),
                'weeks_generated' => count($schedules),
                'start_date' => $request->start_date,
                'months' => $months
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil dibuat untuk ' . count($request->staff_schedules) . ' staff selama ' . $months . ' bulan',
                'data' => $schedules,
                'summary' => [
                    'total_weeks' => count($schedules),
                    'total_staff' => count($request->staff_schedules),
                    'start_date' => $startDate->format('d/m/Y'),
                    'end_date' => $endDate->format('d/m/Y')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating schedule', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat jadwal: ' . $e->getMessage(),
                'debug' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Jadwal $jadwal)
    {
        $jadwal->load(['user', 'shift', 'scheduleDetails']);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $jadwal
            ]);
        }

        return view('jadwal.show', compact('jadwal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jadwal $jadwal)
    {
        $staff = User::where('pu_kd', 'it')
            ->orderBy('name')
            ->get();

        $shifts = Shift::orderBy('start_time')->get();

        return view('jadwal.edit', compact('jadwal', 'staff', 'shifts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jadwal $jadwal)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive,completed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $jadwal->update($request->all());

            // Regenerate schedule details if dates changed
            if ($jadwal->wasChanged(['start_date', 'end_date', 'shift_id'])) {
                if (method_exists($jadwal, 'generateScheduleDetails')) {
                    $jadwal->scheduleDetails()->delete();
                    $jadwal->generateScheduleDetails();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating schedule', [
                'jadwal_id' => $jadwal->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jadwal $jadwal)
    {
        try {
            // Delete related schedule details first
            if (method_exists($jadwal, 'scheduleDetails')) {
                $jadwal->scheduleDetails()->delete();
            }

            $jadwal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting schedule', [
                'jadwal_id' => $jadwal->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get shifts data for AJAX requests
     */
    public function getShifts()
    {
        try {
            $shifts = $this->scheduleService->getAvailableShifts();

            return response()->json([
                'success' => true,
                'data' => $shifts
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting shifts', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data shift'
            ], 500);
        }
    }

    /**
     * Preview schedule rotation before creating
     */
    public function previewSchedule(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'staff_schedules' => 'required|array|min:1',
                'staff_schedules.*.user_id' => 'required|integer|exists:users,id',
                'staff_schedules.*.shift_id' => 'required|integer|exists:shifts,id',
                'start_date' => 'required|date',
                'weeks' => 'integer|min:1|max:12'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid',
                    'errors' => $validator->errors()
                ], 422);
            }

            $weeks = $request->weeks ?? 4;
            $preview = $this->scheduleService->previewScheduleRotation(
                $request->staff_schedules,
                $request->start_date,
                $weeks
            );

            return response()->json([
                'success' => true,
                'data' => $preview
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating schedule preview', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat preview jadwal: ' . $e->getMessage()
            ], 500);
        }
    }
}
