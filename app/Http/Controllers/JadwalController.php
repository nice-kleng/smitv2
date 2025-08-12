<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\JadwalDetail;
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
    // public function index(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $schedules = Jadwal::with(['user', 'shift'])
    //             ->select(['id', 'user_id', 'shift_id', 'start_date', 'end_date', 'week_number', 'year', 'status', 'created_at']);

    //         return DataTables::of($schedules)
    //             ->addIndexColumn()
    //             ->addColumn('staff_name', function ($row) {
    //                 return $row->user->name ?? '-';
    //             })
    //             ->addColumn('shift_name', function ($row) {
    //                 return $row->shift->name ?? '-';
    //             })
    //             ->addColumn('period', function ($row) {
    //                 return Carbon::parse($row->start_date)->format('d/m/Y') . ' - ' . Carbon::parse($row->end_date)->format('d/m/Y');
    //             })
    //             ->addColumn('week_info', function ($row) {
    //                 return 'Week ' . $row->week_number . ', ' . $row->year;
    //             })
    //             ->addColumn('details_count', function ($row) {
    //                 $detailsCount = $row->scheduleDetails()->count();
    //                 return '<span class="badge bg-info">' . $detailsCount . ' hari</span>';
    //             })
    //             ->addColumn('status_badge', function ($row) {
    //                 $badgeClass = $row->status === 'active' ? 'success' : 'secondary';
    //                 return '<span class="badge bg-' . $badgeClass . '">' . ucfirst($row->status) . '</span>';
    //             })
    //             ->addColumn('action', function ($row) {
    //                 $btn = '<div class="btn-group" role="group">';
    //                 $btn .= '<button type="button" class="btn btn-sm btn-info" onclick="viewSchedule(' . $row->id . ')" title="View Details"><i class="fas fa-eye"></i></button>';
    //                 $btn .= '<button type="button" class="btn btn-sm btn-primary" onclick="viewScheduleDetails(' . $row->id . ')" title="View Daily Details"><i class="fas fa-calendar-day"></i></button>';
    //                 $btn .= '<button type="button" class="btn btn-sm btn-warning" onclick="editSchedule(' . $row->id . ')" title="Edit"><i class="fas fa-edit"></i></button>';
    //                 $btn .= '<button type="button" class="btn btn-sm btn-danger" onclick="deleteSchedule(' . $row->id . ')" title="Delete"><i class="fas fa-trash"></i></button>';
    //                 $btn .= '</div>';
    //                 return $btn;
    //             })
    //             ->rawColumns(['details_count', 'status_badge', 'action'])
    //             ->make(true);
    //     }

    //     return view('jadwal.index');
    // }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $schedules = Jadwal::with(['user', 'shift'])
                ->select(['id', 'user_id', 'shift_id', 'start_date', 'end_date', 'week_number', 'year', 'status', 'created_at']);

            // Apply filters
            if ($request->has('date_range') && !empty($request->date_range)) {
                $dateRange = explode(' to ', $request->date_range);
                if (count($dateRange) == 2) {
                    $schedules->whereBetween('start_date', [$dateRange[0], $dateRange[1]]);
                }
            }

            if ($request->has('staff_id') && !empty($request->staff_id)) {
                $schedules->where('user_id', $request->staff_id);
            }

            if ($request->has('status') && !empty($request->status)) {
                $schedules->where('status', $request->status);
            }

            if ($request->has('shift_id') && !empty($request->shift_id)) {
                $schedules->where('shift_id', $request->shift_id);
            }

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
                ->addColumn('details_count', function ($row) {
                    $detailsCount = $row->scheduleDetails()->count();
                    return '<span class="badge badge-info">' . $detailsCount . ' hari</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    $badgeClass = $row->status === 'active' ? 'success' : 'secondary';
                    return '<span class="badge badge-' . $badgeClass . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<button type="button" class="btn btn-sm btn-info" onclick="viewSchedule(' . $row->id . ')" title="View Details"><i class="fas fa-eye"></i></button>';
                    $btn .= '<button type="button" class="btn btn-sm btn-primary" onclick="viewScheduleDetails(' . $row->id . ')" title="View Daily Details"><i class="fas fa-calendar-day"></i></button>';
                    $btn .= '<button type="button" class="btn btn-sm btn-warning" onclick="editSchedule(' . $row->id . ')" title="Edit"><i class="fas fa-edit"></i></button>';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger" onclick="deleteSchedule(' . $row->id . ')" title="Delete"><i class="fas fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['details_count', 'status_badge', 'action'])
                ->make(true);
        }

        // Load data for view
        $staff = User::where('pu_kd', 'it')->orderBy('name')->get();
        $shifts = Shift::orderBy('start_time')->get();

        return view('jadwal.index', compact('staff', 'shifts'));
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

            // dd($request->staff_schedules, $request->start_date, $months);

            // Generate schedules with details
            $schedules = $this->scheduleService->generateScheduleForMonths(
                $request->staff_schedules,
                $request->start_date,
                $months
            );

            // Count total details created
            $totalDetails = Jadwal::whereIn('user_id', $staffIds)
                ->where('start_date', '>=', $startDate->format('Y-m-d'))
                ->where('end_date', '<=', $endDate->format('Y-m-d'))
                ->withCount('scheduleDetails')
                ->get()
                ->sum('schedule_details_count');

            Log::info('Schedule created successfully with details', [
                'staff_count' => count($request->staff_schedules),
                'weeks_generated' => count($schedules),
                'total_details' => $totalDetails,
                'start_date' => $request->start_date,
                'months' => $months
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil dibuat untuk ' . count($request->staff_schedules) . ' staff selama ' . $months . ' bulan dengan ' . $totalDetails . ' detail jadwal harian',
                'data' => $schedules,
                'summary' => [
                    'total_weeks' => count($schedules),
                    'total_staff' => count($request->staff_schedules),
                    'total_details' => $totalDetails,
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
        $jadwal->load(['user', 'shift', 'scheduleDetails' => function ($query) {
            $query->orderBy('work_date');
        }]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $jadwal,
                'detailed_schedule' => $jadwal->getDetailedSchedule()
            ]);
        }

        return view('jadwal.show', compact('jadwal'));
    }

    /**
     * Show schedule details for a specific schedule
     */
    public function showDetails(Jadwal $jadwal)
    {
        try {
            $details = $jadwal->scheduleDetails()
                ->orderBy('work_date')
                ->get()
                ->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'work_date' => $detail->formatted_date,
                        'day_name' => $detail->day_name_indonesian,
                        'shift_time' => $detail->formatted_shift_time,
                        'actual_time' => $detail->formatted_actual_time,
                        'attendance_status' => $detail->status_indonesian,
                        'status_class' => $detail->status_badge_class,
                        'notes' => $detail->notes,
                        'working_hours' => $detail->working_hours
                    ];
                });

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $details,
                    'schedule_info' => [
                        'staff_name' => $jadwal->user->name,
                        'shift_name' => $jadwal->shift->name,
                        'period' => $jadwal->formatted_period,
                        'week_info' => $jadwal->week_info
                    ]
                ]);
            }

            return view('jadwal.detail', compact('jadwal', 'details'));
        } catch (\Exception $e) {
            Log::error('Error showing schedule details', [
                'jadwal_id' => $jadwal->id,
                'error' => $e->getMessage()
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil detail jadwal: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal mengambil detail jadwal');
        }
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
            $originalData = [
                'start_date' => $jadwal->start_date,
                'end_date' => $jadwal->end_date,
                'shift_id' => $jadwal->shift_id
            ];

            $jadwal->update($request->all());

            // Regenerate schedule details if dates or shift changed
            if ($jadwal->wasChanged(['start_date', 'end_date', 'shift_id'])) {
                $jadwal->scheduleDetails()->delete();
                $this->scheduleService->generateDailyScheduleDetails($jadwal);

                Log::info('Regenerated schedule details after update', [
                    'jadwal_id' => $jadwal->id,
                    'original_data' => $originalData,
                    'new_data' => $request->only(['start_date', 'end_date', 'shift_id'])
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil diperbarui',
                'details_updated' => $jadwal->wasChanged(['start_date', 'end_date', 'shift_id'])
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
            $detailsCount = $jadwal->scheduleDetails()->count();

            // Use service method for proper cleanup
            $this->scheduleService->deleteScheduleWithDetails($jadwal->id);

            return response()->json([
                'success' => true,
                'message' => "Jadwal dan {$detailsCount} detail jadwal berhasil dihapus"
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
     * Update attendance for schedule detail
     */
    public function updateAttendance(Request $request, JadwalDetail $detail)
    {
        $validator = Validator::make($request->all(), [
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $checkInTime = $request->check_in_time ?
                Carbon::parse($detail->work_date)->format('Y-m-d') . ' ' . $request->check_in_time : null;

            $checkOutTime = $request->check_out_time ?
                Carbon::parse($detail->work_date)->format('Y-m-d') . ' ' . $request->check_out_time : null;

            $updatedDetail = $this->scheduleService->updateAttendance(
                $detail->id,
                $checkInTime,
                $checkOutTime,
                $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Kehadiran berhasil diperbarui',
                'data' => [
                    'id' => $updatedDetail->id,
                    'attendance_status' => $updatedDetail->status_indonesian,
                    'status_class' => $updatedDetail->status_badge_class,
                    'actual_time' => $updatedDetail->formatted_actual_time,
                    'working_hours' => $updatedDetail->working_hours,
                    'actual_start_time' => $updatedDetail->actual_start_time,
                    'actual_end_time' => $updatedDetail->actual_end_time,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating attendance', [
                'detail_id' => $detail->id,
                'jadwal_detail' => $detail->toArray(),
                'checkin_time' => $checkInTime,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui kehadiran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update attendance for schedule detail - FIXED VERSION
     */
    // public function updateAttendance(Request $request, JadwalDetail $detail)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'check_in_time' => 'nullable|date_format:H:i',
    //         'check_out_time' => 'nullable|date_format:H:i',
    //         'notes' => 'nullable|string|max:500'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Data tidak valid',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     try {
    //         // Build full datetime using work_date + time
    //         $checkInTime = null;
    //         $checkOutTime = null;

    //         if ($request->check_in_time) {
    //             $checkInTime = Carbon::parse($detail->work_date->format('Y-m-d') . ' ' . $request->check_in_time);
    //         }

    //         if ($request->check_out_time) {
    //             $checkOutTime = Carbon::parse($detail->work_date->format('Y-m-d') . ' ' . $request->check_out_time);
    //         }

    //         // Update the detail
    //         if ($checkInTime) {
    //             $detail->actual_start_time = $checkInTime;
    //         }

    //         if ($checkOutTime) {
    //             $detail->actual_end_time = $checkOutTime;
    //         }

    //         if ($request->has('notes')) {
    //             $detail->notes = $request->notes;
    //         }

    //         // Update attendance status automatically
    //         $detail->updateAttendanceStatus();

    //         Log::info('Updated attendance', [
    //             'detail_id' => $detail->id,
    //             'check_in' => $checkInTime ? $checkInTime->format('Y-m-d H:i:s') : null,
    //             'check_out' => $checkOutTime ? $checkOutTime->format('Y-m-d H:i:s') : null,
    //             'status' => $detail->attendance_status
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Kehadiran berhasil diperbarui',
    //             'data' => [
    //                 'id' => $detail->id,
    //                 'attendance_status' => $detail->status_indonesian,
    //                 'status_class' => $detail->status_badge_class,
    //                 'actual_time' => $detail->formatted_actual_time,
    //                 'working_hours' => $detail->working_hours,
    //                 'actual_start_time' => $detail->actual_start_time,
    //                 'actual_end_time' => $detail->actual_end_time
    //             ]
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Error updating attendance', [
    //             'detail_id' => $detail->id,
    //             'jadwal_detail' => $detail->toArray(),
    //             'check_in_time' => $request->check_in_time,
    //             'check_out_time' => $request->check_out_time,
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan saat memperbarui kehadiran: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Bulk update attendance for multiple details
     */
    public function bulkUpdateAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'updates' => 'required|array|min:1',
            'updates.*.detail_id' => 'required|integer|exists:jadwal_details,id',
            'updates.*.check_in_time' => 'nullable|date_format:H:i',
            'updates.*.check_out_time' => 'nullable|date_format:H:i',
            'updates.*.notes' => 'nullable|string|max:500',
            'updates.*.attendance_status' => 'nullable|in:scheduled,present,absent,late,early_leave,overtime'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Process updates to include full datetime
            $processedUpdates = [];
            foreach ($request->updates as $update) {
                $detail = JadwalDetail::find($update['detail_id']);
                if (!$detail) continue;

                $processedUpdate = ['detail_id' => $update['detail_id']];

                if (!empty($update['check_in_time'])) {
                    $processedUpdate['check_in_time'] = $detail->work_date . ' ' . $update['check_in_time'];
                }

                if (!empty($update['check_out_time'])) {
                    $processedUpdate['check_out_time'] = $detail->work_date . ' ' . $update['check_out_time'];
                }

                if (isset($update['notes'])) {
                    $processedUpdate['notes'] = $update['notes'];
                }

                if (isset($update['attendance_status'])) {
                    $processedUpdate['attendance_status'] = $update['attendance_status'];
                }

                $processedUpdates[] = $processedUpdate;
            }

            $updatedDetails = $this->scheduleService->bulkUpdateScheduleDetails($processedUpdates);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil memperbarui ' . count($updatedDetails) . ' data kehadiran',
                'data' => collect($updatedDetails)->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'attendance_status' => $detail->status_indonesian,
                        'status_class' => $detail->status_badge_class,
                        'actual_time' => $detail->formatted_actual_time,
                        'working_hours' => $detail->working_hours,
                        'actual_start_time' => $detail->actual_start_time,
                        'actual_end_time' => $detail->actual_end_time,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Error in bulk update attendance', [
                'updates_count' => count($request->updates),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui kehadiran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get daily schedule for a specific date
     */
    // public function getDailySchedule(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'date' => 'required|date',
    //         'user_id' => 'nullable|integer|exists:users,id'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Data tidak valid',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     try {
    //         $dailySchedule = $this->scheduleService->getDailySchedule(
    //             $request->date,
    //             $request->user_id
    //         );

    //         $formattedSchedule = $dailySchedule->map(function ($detail) {
    //             return [
    //                 'id' => $detail->id,
    //                 'staff_name' => $detail->jadwal->user->name,
    //                 'shift_name' => $detail->jadwal->shift->name,
    //                 'shift_time' => $detail->formatted_shift_time,
    //                 'actual_time' => $detail->formatted_actual_time,
    //                 'attendance_status' => $detail->status_indonesian,
    //                 'status_class' => $detail->status_badge_class,
    //                 'notes' => $detail->notes,
    //                 'working_hours' => $detail->working_hours
    //             ];
    //         });

    //         return response()->json([
    //             'success' => true,
    //             'data' => $formattedSchedule,
    //             'date' => Carbon::parse($request->date)->format('d/m/Y'),
    //             'total' => $formattedSchedule->count()
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Error getting daily schedule', [
    //             'date' => $request->date,
    //             'user_id' => $request->user_id,
    //             'error' => $e->getMessage()
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal mengambil jadwal harian: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function getDailySchedule(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start' => 'required|date',
                'end' => 'required|date|after_or_equal:start'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter tidak valid',
                    'errors' => $validator->errors()
                ], 422);
            }

            $startDate = Carbon::parse($request->start)->format('Y-m-d');
            $endDate = Carbon::parse($request->end)->format('Y-m-d');

            // Get schedule details dalam rentang tanggal
            $scheduleDetails = JadwalDetail::with(['jadwal.user', 'jadwal.shift'])
                ->whereBetween('work_date', [$startDate, $endDate])
                ->orderBy('work_date')
                ->get();

            $formattedSchedule = $scheduleDetails->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'staff_name' => $detail->jadwal->user->name ?? 'Unknown',
                    'shift_name' => $detail->jadwal->shift->name ?? 'Unknown',
                    'shift_time' => $this->getFormattedShiftTime($detail->jadwal->shift),
                    'work_date' => Carbon::parse($detail->work_date)->format('Y-m-d'),
                    'attendance_status' => $detail->attendance_status ?? 'scheduled',
                    'actual_start_time' => $detail->actual_start_time,
                    'actual_end_time' => $detail->actual_end_time,
                    'notes' => $detail->notes
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedSchedule
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting daily schedule for calendar', [
                'start' => $request->start ?? null,
                'end' => $request->end ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jadwal: ' . $e->getMessage()
            ], 500);
        }
    }
    private function getFormattedShiftTime($shift)
    {
        if (!$shift) {
            return 'N/A';
        }

        $startTime = Carbon::parse($shift->start_time)->format('H:i');
        $endTime = Carbon::parse($shift->end_time)->format('H:i');

        return $startTime . ' - ' . $endTime;
    }

    /**
     * Get attendance summary
     */
    public function getAttendanceSummary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'user_id' => 'nullable|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $summary = $this->scheduleService->getAttendanceSummary(
                $request->start_date,
                $request->end_date,
                $request->user_id
            );

            return response()->json([
                'success' => true,
                'data' => $summary,
                'period' => [
                    'start_date' => Carbon::parse($request->start_date)->format('d/m/Y'),
                    'end_date' => Carbon::parse($request->end_date)->format('d/m/Y')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting attendance summary', [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'user_id' => $request->user_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan kehadiran: ' . $e->getMessage()
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
                'data' => $preview,
                'summary' => [
                    'total_weeks' => count($preview),
                    'total_staff' => count($request->staff_schedules),
                    'start_date' => Carbon::parse($request->start_date)->format('d/m/Y')
                ]
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

    /**
     * Export schedule to various formats
     */
    public function exportSchedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,excel,csv',
            'user_id' => 'nullable|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // This would be implemented based on your export requirements
            // For now, just return the data that would be exported
            $schedules = $this->scheduleService->getScheduleByDateRange(
                $request->start_date,
                $request->end_date
            );

            if ($request->user_id) {
                $schedules = $schedules->where('user_id', $request->user_id);
            }

            $exportData = $schedules->map(function ($schedule) {
                return [
                    'staff_name' => $schedule->user->name,
                    'shift_name' => $schedule->shift->name,
                    'start_date' => $schedule->start_date->format('d/m/Y'),
                    'end_date' => $schedule->end_date->format('d/m/Y'),
                    'week_number' => $schedule->week_number,
                    'year' => $schedule->year,
                    'status' => $schedule->status,
                    'details_count' => $schedule->scheduleDetails->count()
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data siap untuk diekspor',
                'data' => $exportData,
                'format' => $request->format,
                'period' => [
                    'start_date' => Carbon::parse($request->start_date)->format('d/m/Y'),
                    'end_date' => Carbon::parse($request->end_date)->format('d/m/Y')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error exporting schedule', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor jadwal: ' . $e->getMessage()
            ], 500);
        }
    }
}
