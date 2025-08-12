<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\JadwalDetail;
use App\Models\User;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScheduleService
{
    public function generateScheduleForMonths($staffSchedules, $startDate, $months = 3)
    {
        try {
            // Validasi input
            if (empty($staffSchedules) || !is_array($staffSchedules)) {
                throw new \Exception('Harap pilih staff dan tentukan shift untuk masing-masing staff');
            }

            // Clean and validate input data
            $cleanedSchedules = [];
            foreach ($staffSchedules as $schedule) {
                if (
                    isset($schedule['user_id']) && isset($schedule['shift_id']) &&
                    !empty($schedule['user_id']) && !empty($schedule['shift_id'])
                ) {
                    $cleanedSchedules[] = [
                        'user_id' => (int) $schedule['user_id'],
                        'shift_id' => (int) $schedule['shift_id']
                    ];
                }
            }

            if (empty($cleanedSchedules)) {
                throw new \Exception('Tidak ada data staff dan shift yang valid');
            }

            // Extract staff IDs and validate
            $staffIds = array_column($cleanedSchedules, 'user_id');
            $staff = User::whereIn('id', $staffIds)
                ->where('pu_kd', 'it')
                ->get()
                ->keyBy('id');

            if ($staff->count() !== count($staffIds)) {
                throw new \Exception('Beberapa staff yang dipilih tidak valid atau tidak dapat dijadwalkan');
            }

            // Validate shifts
            $shiftIds = array_column($cleanedSchedules, 'shift_id');
            $shifts = Shift::whereIn('id', $shiftIds)->get()->keyBy('id');

            if ($shifts->count() !== count($shiftIds)) {
                throw new \Exception('Beberapa shift yang dipilih tidak valid');
            }

            // Validate for duplicate staff
            if (count($staffIds) !== count(array_unique($staffIds))) {
                throw new \Exception('Tidak boleh ada staff yang sama dipilih lebih dari sekali');
            }

            // Parse and validate start date
            try {
                $startDate = Carbon::parse($startDate)->startOfWeek(Carbon::MONDAY);
            } catch (\Exception $e) {
                throw new \Exception('Format tanggal mulai tidak valid');
            }

            if (!is_numeric($months) || $months < 1 || $months > 12) {
                throw new \Exception('Durasi bulan harus antara 1-12');
            }

            $endDate = $startDate->copy()->addMonths($months);

            Log::info('Generating schedule', [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'months' => $months,
                'staff_count' => count($cleanedSchedules)
            ]);

            $schedules = [];
            $currentWeek = $startDate->copy();
            $weekCounter = 0;

            // Prepare initial staff-shift mapping dengan urutan yang benar
            $staffShiftMapping = [];
            foreach ($cleanedSchedules as $schedule) {
                $staffShiftMapping[] = [
                    'user_id' => $schedule['user_id'],
                    'shift_id' => $schedule['shift_id']
                ];
            }

            // Use database transaction
            return DB::transaction(function () use ($staffShiftMapping, $currentWeek, $endDate, $staff, $shifts, &$schedules, &$weekCounter) {

                // while ($currentWeek->lt($endDate)) {
                //     // Get shift's work days for determining week end
                //     $shift = $shifts[$staffShiftMapping[0]['shift_id']];
                //     // $decodedDays = is_string($shift->work_days) ? json_decode($shift->work_days, true) : $shift->work_days;

                //     // if (is_string($decodedDays)) {
                //     //     $decodedDays = json_decode($decodedDays, true);
                //     // }

                //     // // Now we should have a proper array, convert to lowercase
                //     // if (is_array($decodedDays)) {
                //     //     $workDays = array_map('strtolower', $decodedDays);
                //     // }

                //     // Create temporary Jadwal instance to use calculateWeekEndBasedOnWorkDays
                //     $workDays = $shift->work_days;
                //     $tempJadwal = new Jadwal();
                //     $weekEnd = $tempJadwal->calculateWeekEndBasedOnWorkDays($currentWeek, $workDays);
                //     $weekSchedules = [];
                //     foreach ($staffShiftMapping as $mapping) {
                //         $userId = $mapping['user_id'];
                //         $shiftId = $mapping['shift_id'];
                //         // dd($userId, $currentWeek->format('Y-m-d'), $workDays, $weekEnd->format('Y-m-d'));

                //         // Check if schedule already exists for this week
                //         $existingSchedule = Jadwal::where('user_id', $userId)
                //             ->where('start_date', '>=', $currentWeek->format('Y-m-d'))
                //             ->where('start_date', '<=', $weekEnd->format('Y-m-d'))
                //             ->first();

                //         if (!$existingSchedule) {
                //             // Create schedule record
                //             $jadwalRecord = Jadwal::create([
                //                 'user_id' => $userId,
                //                 'shift_id' => $shiftId,
                //                 'start_date' => $currentWeek->format('Y-m-d'),
                //                 'end_date' => $weekEnd->format('Y-m-d'),
                //                 'week_number' => $currentWeek->weekOfYear,
                //                 'year' => $currentWeek->year,
                //                 'status' => 'active'
                //             ]);

                //             // Generate daily schedule details
                //             $this->generateDailyScheduleDetails($jadwalRecord);

                //             Log::info('Created schedule with details', [
                //                 'user_id' => $userId,
                //                 'shift_id' => $shiftId,
                //                 'week' => $currentWeek->format('Y-m-d'),
                //                 'details_count' => $jadwalRecord->scheduleDetails()->count()
                //             ]);
                //         }

                //         $weekSchedules[] = [
                //             'user_id' => $userId,
                //             'user_name' => $staff[$userId]->name,
                //             'shift_id' => $shiftId,
                //             'shift_name' => $shifts[$shiftId]->name
                //         ];
                //     }

                //     $schedules[] = [
                //         'week' => $currentWeek->format('Y-m-d'),
                //         'week_number' => $weekCounter + 1,
                //         'schedules' => $weekSchedules
                //     ];

                //     // Rotate shifts for next week using improved logic
                //     $staffShiftMapping = $this->rotateShiftsImproved($staffShiftMapping);

                //     $currentWeek->addWeek();
                //     $weekCounter++;
                // }
                while ($currentWeek->lt($endDate)) {
                    // Get shift's work days for determining week end
                    $shift = $shifts[$staffShiftMapping[0]['shift_id']];

                    // Handle work_days parsing with better error handling
                    $workDays = [];
                    if ($shift->work_days) {
                        if (is_string($shift->work_days)) {
                            $decodedDays = json_decode($shift->work_days, true);
                            if (is_array($decodedDays)) {
                                $workDays = array_map('strtolower', $decodedDays);
                            }
                        } elseif (is_array($shift->work_days)) {
                            $workDays = array_map('strtolower', $shift->work_days);
                        }
                    }

                    // Default to Monday-Friday if no work_days
                    if (empty($workDays)) {
                        $workDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                    }

                    Log::info('Processing week with work days', [
                        'week_start' => $currentWeek->format('Y-m-d'),
                        'shift_id' => $shift->id,
                        'work_days' => $workDays
                    ]);

                    // Create temporary Jadwal instance to use calculateWeekEndBasedOnWorkDays
                    $tempJadwal = new Jadwal();
                    $weekEnd = $tempJadwal->calculateWeekEndBasedOnWorkDays($currentWeek, $workDays);

                    $weekSchedules = [];
                    foreach ($staffShiftMapping as $mapping) {
                        $userId = $mapping['user_id'];
                        $shiftId = $mapping['shift_id'];

                        // Get the actual shift for this staff member
                        $currentShift = $shifts[$shiftId];

                        // Parse work_days for this specific shift
                        $currentWorkDays = [];
                        if ($currentShift->work_days) {
                            if (is_string($currentShift->work_days)) {
                                $decodedDays = json_decode($currentShift->work_days, true);
                                if (is_array($decodedDays)) {
                                    $currentWorkDays = array_map('strtolower', $decodedDays);
                                }
                            } elseif (is_array($currentShift->work_days)) {
                                $currentWorkDays = array_map('strtolower', $currentShift->work_days);
                            }
                        }

                        // Default to Monday-Friday if no work_days
                        if (empty($currentWorkDays)) {
                            $currentWorkDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                        }

                        // Calculate end date specifically for this shift
                        $shiftWeekEnd = $tempJadwal->calculateWeekEndBasedOnWorkDays($currentWeek, $currentWorkDays);

                        // Check if schedule already exists for this week
                        $existingSchedule = Jadwal::where('user_id', $userId)
                            ->where('start_date', '>=', $currentWeek->format('Y-m-d'))
                            ->where('start_date', '<=', $shiftWeekEnd->format('Y-m-d'))
                            ->first();

                        if (!$existingSchedule) {
                            // Create schedule record with correct end date
                            $jadwalRecord = Jadwal::create([
                                'user_id' => $userId,
                                'shift_id' => $shiftId,
                                'start_date' => $currentWeek->format('Y-m-d'),
                                'end_date' => $shiftWeekEnd->format('Y-m-d'), // Use shift-specific end date
                                'week_number' => $currentWeek->weekOfYear,
                                'year' => $currentWeek->year,
                                'status' => 'active'
                            ]);

                            // Generate daily schedule details
                            $this->generateDailyScheduleDetails($jadwalRecord);

                            Log::info('Created schedule with shift-specific end date', [
                                'user_id' => $userId,
                                'shift_id' => $shiftId,
                                'week_start' => $currentWeek->format('Y-m-d'),
                                'week_end' => $shiftWeekEnd->format('Y-m-d'),
                                'work_days' => $currentWorkDays,
                                'details_count' => $jadwalRecord->scheduleDetails()->count()
                            ]);
                        }

                        $weekSchedules[] = [
                            'user_id' => $userId,
                            'user_name' => $staff[$userId]->name,
                            'shift_id' => $shiftId,
                            'shift_name' => $shifts[$shiftId]->name
                        ];
                    }

                    $schedules[] = [
                        'week' => $currentWeek->format('Y-m-d'),
                        'week_number' => $weekCounter + 1,
                        'schedules' => $weekSchedules
                    ];

                    // Rotate shifts for next week using improved logic
                    $staffShiftMapping = $this->rotateShiftsImproved($staffShiftMapping);

                    $currentWeek->addWeek();
                    $weekCounter++;
                }

                return $schedules;
            });
        } catch (\Exception $e) {
            Log::error('Error in generateScheduleForMonths', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Generate daily schedule details for a weekly schedule
     */
    public function generateDailyScheduleDetails(Jadwal $jadwal)
    {
        try {
            // Delete existing details first
            $jadwal->scheduleDetails()->delete();

            if (!$jadwal->shift) {
                throw new \Exception('Shift tidak ditemukan untuk jadwal ID: ' . $jadwal->id);
            }

            $startDate = Carbon::parse($jadwal->start_date);
            $endDate = Carbon::parse($jadwal->end_date);
            $currentDate = $startDate->copy();

            $details = [];
            $dayNames = [
                0 => 'sunday',
                1 => 'monday',
                2 => 'tuesday',
                3 => 'wednesday',
                4 => 'thursday',
                5 => 'friday',
                6 => 'saturday'
            ];

            while ($currentDate->lte($endDate)) {
                $dayOfWeek = $currentDate->dayOfWeek;
                $dayName = $dayNames[$dayOfWeek];

                // Create schedule detail for each day
                $detail = JadwalDetail::create([
                    'schedule_id' => $jadwal->id,
                    'work_date' => $currentDate->format('Y-m-d'),
                    'day_name' => $dayName,
                    'actual_start_time' => null,
                    'actual_end_time' => null,
                    'attendance_status' => 'scheduled',
                    'notes' => null
                ]);

                $details[] = $detail;

                Log::debug('Created schedule detail', [
                    'schedule_id' => $jadwal->id,
                    'work_date' => $currentDate->format('Y-m-d'),
                    'day_name' => $dayName
                ]);

                $currentDate->addDay();
            }

            Log::info('Generated daily schedule details', [
                'jadwal_id' => $jadwal->id,
                'user_id' => $jadwal->user_id,
                'details_count' => count($details),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d')
            ]);

            return $details;
        } catch (\Exception $e) {
            Log::error('Error generating daily schedule details', [
                'jadwal_id' => $jadwal->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception('Gagal membuat detail jadwal harian: ' . $e->getMessage());
        }
    }

    /**
     * Improved shift rotation logic
     * Rotates shifts among staff for the next week
     */
    private function rotateShiftsImproved($currentMapping)
    {
        if (count($currentMapping) <= 1) {
            return $currentMapping;
        }

        // Extract shift IDs
        $shiftIds = array_column($currentMapping, 'shift_id');

        // Rotate shifts: move last shift to first position
        $lastShift = array_pop($shiftIds);
        array_unshift($shiftIds, $lastShift);

        // Create new mapping with rotated shifts
        $newMapping = [];
        foreach ($currentMapping as $index => $mapping) {
            $newMapping[] = [
                'user_id' => $mapping['user_id'],
                'shift_id' => $shiftIds[$index]
            ];
        }

        // dd($newMapping);

        return $newMapping;
    }

    /**
     * Get schedule with details by date range
     */
    public function getScheduleByDateRange($startDate, $endDate)
    {
        try {
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');

            return Jadwal::with(['user', 'shift', 'scheduleDetails' => function ($query) {
                $query->orderBy('work_date');
            }])
                ->whereBetween('start_date', [$startDate, $endDate])
                ->orderBy('start_date')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error getting schedule by date range', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal mengambil data jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Get daily schedule details for a specific date
     */
    public function getDailySchedule($date, $userId = null)
    {
        try {
            $query = JadwalDetail::with(['jadwal.user', 'jadwal.shift'])
                ->forDate($date);

            if ($userId) {
                $query->whereHas('jadwal', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                });
            }

            return $query->get();
        } catch (\Exception $e) {
            Log::error('Error getting daily schedule', [
                'date' => $date,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal mengambil jadwal harian: ' . $e->getMessage());
        }
    }

    /**
     * Update schedule detail attendance
     */
    public function updateAttendance($detailId, $checkInTime = null, $checkOutTime = null, $notes = null)
    {
        try {
            $detail = JadwalDetail::findOrFail($detailId);

            if ($checkInTime) {
                $detail->checkIn($checkInTime);
            }

            if ($checkOutTime) {
                $detail->checkOut($checkOutTime);
            }

            if ($notes !== null) {
                $detail->notes = $notes;
                $detail->save();
            }

            Log::info('Updated attendance', [
                'detail_id' => $detailId,
                'check_in' => $checkInTime,
                'check_out' => $checkOutTime,
                'status' => $detail->attendance_status
            ]);

            return $detail;
        } catch (\Exception $e) {
            Log::error('Error updating attendance', [
                'detail_id' => $detailId,
                'log_service' => true,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal memperbarui kehadiran: ' . $e->getMessage());
        }
    }

    /**
     * Get available shifts for scheduling
     */
    public function getAvailableShifts()
    {
        return Shift::orderBy('start_time')->get();
    }

    /**
     * Validate schedule before creation
     */
    public function validateSchedule($staffSchedules)
    {
        $errors = [];

        if (empty($staffSchedules) || !is_array($staffSchedules)) {
            $errors[] = 'Harap pilih minimal satu staff untuk dijadwalkan';
            return $errors;
        }

        // Clean data first
        $validSchedules = [];
        foreach ($staffSchedules as $schedule) {
            if (
                isset($schedule['user_id']) && isset($schedule['shift_id']) &&
                !empty($schedule['user_id']) && !empty($schedule['shift_id'])
            ) {
                $validSchedules[] = $schedule;
            }
        }

        if (empty($validSchedules)) {
            $errors[] = 'Tidak ada data staff dan shift yang valid';
            return $errors;
        }

        // Check for duplicate staff
        $userIds = array_column($validSchedules, 'user_id');
        if (count($userIds) !== count(array_unique($userIds))) {
            $errors[] = 'Tidak boleh ada staff yang sama dipilih lebih dari sekali';
        }

        // Validate staff existence and availability
        try {
            $staff = User::whereIn('id', $userIds)
                ->where('pu_kd', 'it')
                ->get();

            if ($staff->count() !== count($userIds)) {
                $errors[] = 'Beberapa staff yang dipilih tidak valid atau tidak dapat dijadwalkan';
            }
        } catch (\Exception $e) {
            $errors[] = 'Gagal memvalidasi data staff';
        }

        // Validate shift existence
        try {
            $shiftIds = array_column($validSchedules, 'shift_id');
            $shifts = Shift::whereIn('id', $shiftIds)->get();
            if ($shifts->count() !== count($shiftIds)) {
                $errors[] = 'Beberapa shift yang dipilih tidak valid';
            }
        } catch (\Exception $e) {
            $errors[] = 'Gagal memvalidasi data shift';
        }

        return $errors;
    }

    /**
     * Preview schedule rotation without saving to database
     */
    public function previewScheduleRotation($staffSchedules, $startDate, $weeks = 4)
    {
        try {
            if (empty($staffSchedules)) {
                return [];
            }

            $startDate = Carbon::parse($startDate)->startOfWeek(Carbon::MONDAY);
            $staff = User::whereIn('id', array_column($staffSchedules, 'user_id'))->get()->keyBy('id');
            $shifts = Shift::whereIn('id', array_column($staffSchedules, 'shift_id'))->get()->keyBy('id');

            $preview = [];
            $currentWeek = $startDate->copy();

            // Prepare initial mapping
            $staffShiftMapping = [];
            foreach ($staffSchedules as $schedule) {
                if (!empty($schedule['user_id']) && !empty($schedule['shift_id'])) {
                    $staffShiftMapping[] = [
                        'user_id' => (int) $schedule['user_id'],
                        'shift_id' => (int) $schedule['shift_id']
                    ];
                }
            }

            for ($i = 0; $i < $weeks; $i++) {
                $weekEnd = $currentWeek->copy()->endOfWeek(Carbon::SATURDAY);
                $weekSchedules = [];
                $dailyDetails = [];

                foreach ($staffShiftMapping as $mapping) {
                    $userId = $mapping['user_id'];
                    $shiftId = $mapping['shift_id'];

                    if (isset($staff[$userId]) && isset($shifts[$shiftId])) {
                        $weekSchedules[] = [
                            'user_id' => $userId,
                            'user_name' => $staff[$userId]->name,
                            'shift_id' => $shiftId,
                            'shift_name' => $shifts[$shiftId]->name
                        ];

                        // Generate daily preview
                        $currentDate = $currentWeek->copy();
                        $userDailyDetails = [];

                        while ($currentDate->lte($weekEnd)) {
                            $dayNames = [
                                0 => 'Minggu',
                                1 => 'Senin',
                                2 => 'Selasa',
                                3 => 'Rabu',
                                4 => 'Kamis',
                                5 => 'Jumat',
                                6 => 'Sabtu'
                            ];

                            $userDailyDetails[] = [
                                'date' => $currentDate->format('Y-m-d'),
                                'day_name' => $dayNames[$currentDate->dayOfWeek],
                                'shift_time' => $shifts[$shiftId]->start_time . ' - ' . $shifts[$shiftId]->end_time
                            ];

                            $currentDate->addDay();
                        }

                        $dailyDetails[$userId] = $userDailyDetails;
                    }
                }

                $preview[] = [
                    'week' => $currentWeek->format('Y-m-d'),
                    'week_end' => $weekEnd->format('Y-m-d'),
                    'week_number' => $i + 1,
                    'schedules' => $weekSchedules,
                    'daily_details' => $dailyDetails
                ];

                // Rotate for next week
                $staffShiftMapping = $this->rotateShiftsImproved($staffShiftMapping);
                $currentWeek->addWeek();
            }

            return $preview;
        } catch (\Exception $e) {
            Log::error('Error in preview schedule rotation', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get attendance summary for a date range
     */
    public function getAttendanceSummary($startDate, $endDate, $userId = null)
    {
        try {
            $query = JadwalDetail::with(['jadwal.user', 'jadwal.shift'])
                ->dateRange($startDate, $endDate);

            if ($userId) {
                $query->whereHas('jadwal', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                });
            }

            $details = $query->get();

            $summary = [
                'total_scheduled' => $details->count(),
                'present' => $details->where('attendance_status', 'present')->count(),
                'absent' => $details->where('attendance_status', 'absent')->count(),
                'late' => $details->where('attendance_status', 'late')->count(),
                'early_leave' => $details->where('attendance_status', 'early_leave')->count(),
                'overtime' => $details->where('attendance_status', 'overtime')->count(),
                'attendance_rate' => 0
            ];

            if ($summary['total_scheduled'] > 0) {
                $presentCount = $summary['present'] + $summary['late'] + $summary['early_leave'] + $summary['overtime'];
                $summary['attendance_rate'] = round(($presentCount / $summary['total_scheduled']) * 100, 2);
            }

            return $summary;
        } catch (\Exception $e) {
            Log::error('Error getting attendance summary', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal mengambil ringkasan kehadiran: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update schedule details
     */
    public function bulkUpdateScheduleDetails($updates)
    {
        try {
            DB::beginTransaction();

            $updated = [];

            foreach ($updates as $update) {
                if (!isset($update['detail_id'])) {
                    continue;
                }

                $detail = JadwalDetail::find($update['detail_id']);
                if (!$detail) {
                    continue;
                }

                if (isset($update['check_in_time'])) {
                    $detail->checkIn($update['check_in_time']);
                }

                if (isset($update['check_out_time'])) {
                    $detail->checkOut($update['check_out_time']);
                }

                if (isset($update['notes'])) {
                    $detail->notes = $update['notes'];
                    $detail->save();
                }

                if (isset($update['attendance_status'])) {
                    $detail->attendance_status = $update['attendance_status'];
                    $detail->save();
                }

                $updated[] = $detail;
            }

            DB::commit();

            Log::info('Bulk updated schedule details', [
                'count' => count($updated)
            ]);

            return $updated;
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error in bulk update schedule details', [
                'error' => $e->getMessage()
            ]);

            throw new \Exception('Gagal memperbarui detail jadwal secara massal: ' . $e->getMessage());
        }
    }

    /**
     * Delete schedule and its details
     */
    public function deleteScheduleWithDetails($jadwalId)
    {
        try {
            DB::beginTransaction();

            $jadwal = Jadwal::findOrFail($jadwalId);

            // Delete schedule details first
            $detailsCount = $jadwal->scheduleDetails()->count();
            $jadwal->scheduleDetails()->delete();

            // Delete the main schedule
            $jadwal->delete();

            DB::commit();

            Log::info('Deleted schedule with details', [
                'jadwal_id' => $jadwalId,
                'details_deleted' => $detailsCount
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error deleting schedule with details', [
                'jadwal_id' => $jadwalId,
                'error' => $e->getMessage()
            ]);

            throw new \Exception('Gagal menghapus jadwal dan detailnya: ' . $e->getMessage());
        }
    }
}
