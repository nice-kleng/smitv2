<?php

namespace App\Services;

use App\Models\Jadwal;
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

            // $endDate = $startDate->copy()->addMonths((int) $months);
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

                while ($currentWeek->lt($endDate)) {
                    $weekEnd = $currentWeek->copy()->endOfWeek(Carbon::SATURDAY);
                    $weekSchedules = [];

                    foreach ($staffShiftMapping as $mapping) {
                        $userId = $mapping['user_id'];
                        $shiftId = $mapping['shift_id'];

                        // Check if schedule already exists for this week
                        $existingSchedule = Jadwal::where('user_id', $userId)
                            ->where('start_date', '>=', $currentWeek->format('Y-m-d'))
                            ->where('start_date', '<=', $weekEnd->format('Y-m-d'))
                            ->first();

                        if (!$existingSchedule) {
                            // Create schedule record
                            $jadwalRecord = Jadwal::create([
                                'user_id' => $userId,
                                'shift_id' => $shiftId,
                                'start_date' => $currentWeek->format('Y-m-d'),
                                'end_date' => $weekEnd->format('Y-m-d'),
                                'week_number' => $currentWeek->weekOfYear,
                                'year' => $currentWeek->year,
                                'status' => 'active'
                            ]);

                            // Generate detail jadwal harian
                            if (method_exists($jadwalRecord, 'generateScheduleDetails')) {
                                $jadwalRecord->generateScheduleDetails();
                            }

                            Log::info('Created schedule', [
                                'user_id' => $userId,
                                'shift_id' => $shiftId,
                                'week' => $currentWeek->format('Y-m-d')
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
            Log::info('Generating schedule with parameters', [
                'start_date' => $startDate,
                'months' => $months,
                'months_type' => gettype($months)
            ]);
        } catch (\Exception $e) {
            Log::error('Error in generateScheduleForMonths', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
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

        return $newMapping;
    }

    /**
     * Legacy rotation method for backward compatibility
     */
    private function rotateShifts($currentMapping, $availableShifts)
    {
        return $this->rotateShiftsImproved(
            array_map(function ($userId, $shiftId) {
                return ['user_id' => $userId, 'shift_id' => $shiftId];
            }, array_keys($currentMapping), array_values($currentMapping))
        );
    }

    public function getScheduleByDateRange($startDate, $endDate)
    {
        try {
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');

            return Jadwal::with(['user', 'shift', 'scheduleDetails'])
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
                $weekSchedules = [];

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
                    }
                }

                $preview[] = [
                    'week' => $currentWeek->format('Y-m-d'),
                    'week_number' => $i + 1,
                    'schedules' => $weekSchedules
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
}
