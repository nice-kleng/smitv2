<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Jadwal extends Model
{
    protected $fillable = [
        'user_id',
        'shift_id',
        'start_date',
        'end_date',
        'week_number',
        'year',
        'status',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function scheduleDetails()
    {
        return $this->hasMany(JadwalDetail::class, 'schedule_id');
    }

    // public function calculateWeekEndBasedOnWorkDays($startWeek, $workDays)
    // {
    //     try {
    //         if (empty($workDays) || !is_array($workDays)) {
    //             // Default to Saturday if no work_days defined
    //             return $startWeek->copy()->endOfWeek(Carbon::SATURDAY);
    //         }

    //         $dayMapping = [
    //             'monday' => Carbon::MONDAY,
    //             'tuesday' => Carbon::TUESDAY,
    //             'wednesday' => Carbon::WEDNESDAY,
    //             'thursday' => Carbon::THURSDAY,
    //             'friday' => Carbon::FRIDAY,
    //             'saturday' => Carbon::SATURDAY,
    //             'sunday' => Carbon::SUNDAY
    //         ];

    //         // Convert work_days ke nomor hari dan urutkan
    //         $workDayNumbers = [];
    //         foreach ($workDays as $day) {
    //             $dayLower = strtolower(trim($day));
    //             if (isset($dayMapping[$dayLower])) {
    //                 $workDayNumbers[] = $dayMapping[$dayLower];
    //             }
    //         }

    //         if (empty($workDayNumbers)) {
    //             // Default to Saturday if no valid work_days
    //             return $startWeek->copy()->endOfWeek(Carbon::SATURDAY);
    //         }

    //         // Urutkan hari kerja
    //         sort($workDayNumbers);


    //         // Ambil hari kerja terakhir
    //         $lastWorkDay = max($workDayNumbers);

    //         // Hitung tanggal berdasarkan hari kerja terakhir dalam minggu
    //         $weekEnd = $startWeek->copy()->startOfWeek(Carbon::MONDAY);

    //         // Set ke hari kerja terakhir
    //         while ($weekEnd->dayOfWeek !== $lastWorkDay) {
    //             $weekEnd->addDay();
    //         }

    //         return $weekEnd;
    //     } catch (\Exception $e) {
    //         Log::error('Error calculating week end based on work_days', [
    //             'error' => $e->getMessage(),
    //             'work_days' => $workDays
    //         ]);

    //         // Return default Saturday if error occurs
    //         return $startWeek->copy()->endOfWeek(Carbon::SATURDAY);
    //     }
    // }

    public function calculateWeekEndBasedOnWorkDays($startWeek, $workDays)
    {
        try {
            if (empty($workDays) || !is_array($workDays)) {
                // Default to Friday if no work_days defined (Monday-Friday)
                return $startWeek->copy()->startOfWeek(Carbon::MONDAY)->addDays(4); // Friday
            }

            $dayMapping = [
                'monday' => Carbon::MONDAY,
                'tuesday' => Carbon::TUESDAY,
                'wednesday' => Carbon::WEDNESDAY,
                'thursday' => Carbon::THURSDAY,
                'friday' => Carbon::FRIDAY,
                'saturday' => Carbon::SATURDAY,
                'sunday' => Carbon::SUNDAY
            ];

            // Convert work_days ke nomor hari dan urutkan
            $workDayNumbers = [];
            foreach ($workDays as $day) {
                $dayLower = strtolower(trim($day));
                if (isset($dayMapping[$dayLower])) {
                    $workDayNumbers[] = $dayMapping[$dayLower];
                }
            }

            if (empty($workDayNumbers)) {
                // Default to Friday if no valid work_days
                return $startWeek->copy()->startOfWeek(Carbon::MONDAY)->addDays(4); // Friday
            }

            // Urutkan hari kerja
            sort($workDayNumbers);

            // Ambil hari kerja terakhir dalam seminggu
            $lastWorkDay = max($workDayNumbers);

            // Mulai dari awal minggu (Senin)
            $weekStart = $startWeek->copy()->startOfWeek(Carbon::MONDAY);

            // Hitung end_date berdasarkan hari kerja terakhir
            $weekEnd = $weekStart->copy();

            // Set ke hari kerja terakhir dalam minggu tersebut
            while ($weekEnd->dayOfWeek !== $lastWorkDay) {
                $weekEnd->addDay();
            }

            Log::info('Calculated week end based on work days', [
                'start_week' => $startWeek->format('Y-m-d'),
                'work_days' => $workDays,
                'last_work_day' => $lastWorkDay,
                'calculated_end' => $weekEnd->format('Y-m-d')
            ]);

            return $weekEnd;
        } catch (\Exception $e) {
            Log::error('Error calculating week end based on work_days', [
                'error' => $e->getMessage(),
                'work_days' => $workDays,
                'start_week' => $startWeek->format('Y-m-d')
            ]);

            // Return default Friday if error occurs
            return $startWeek->copy()->startOfWeek(Carbon::MONDAY)->addDays(4);
        }
    }

    public function generateScheduleDetails()
    {
        try {
            // Delete existing schedule details first
            $this->scheduleDetails()->delete();

            if (!$this->shift) {
                Log::warning('Cannot generate schedule details: shift not found', [
                    'jadwal_id' => $this->id,
                    'shift_id' => $this->shift_id
                ]);
                return false;
            }

            $startDate = Carbon::parse($this->start_date);
            $endDate = Carbon::parse($this->end_date);
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

            // Get work days from shift and make sure it's a proper array
            $workDays = [];
            if ($this->shift->work_days) {
                // Handle case when it's a JSON string within a string
                $decodedDays = is_string($this->shift->work_days) ? json_decode($this->shift->work_days, true) : $this->shift->work_days;

                // If still a string (like "["monday","tuesday"]"), decode again
                if (is_string($decodedDays)) {
                    $decodedDays = json_decode($decodedDays, true);
                }

                // Now we should have a proper array, convert to lowercase
                if (is_array($decodedDays)) {
                    $workDays = array_map('strtolower', $decodedDays);
                }
            }

            Log::info('Generating schedule details', [
                'jadwal_id' => $this->id,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'shift_name' => $this->shift->name,
                'work_days' => $workDays
            ]);

            // while ($currentDate->lte($endDate)) {
            //     $currentDayName = strtolower($dayNames[$currentDate->dayOfWeek]);

            //     // Only create schedule detail if it's a work day
            //     if (in_array($currentDayName, $workDays)) {
            //         $detail = new JadwalDetail([
            //             'schedule_id' => $this->id,
            //             'work_date' => $currentDate->format('Y-m-d'),
            //             'day_name' => $currentDayName,
            //             'attendance_status' => 'pending'
            //         ]);

            //         $details[] = $detail;
            //         $detail->save();
            //     }

            //     $currentDate->addDay();
            // }

            // Log::info('Schedule details generated successfully', [
            //     'details_count' => count($details)
            // ]);

            // return $details;

            while ($currentDate->lte($endDate)) {
                $dayOfWeek = $currentDate->dayOfWeek;
                $dayName = $dayNames[$dayOfWeek];

                // Create schedule detail for each day
                $detail = JadwalDetail::create([
                    'schedule_id' => $this->id,
                    'work_date' => $currentDate->format('Y-m-d'),
                    'day_name' => $dayName,
                    'actual_start_time' => null, // Will be filled when employee checks in
                    'actual_end_time' => null,   // Will be filled when employee checks out
                    'attendance_status' => 'scheduled', // Default status
                    'notes' => null
                ]);

                $details[] = $detail;
                $currentDate->addDay();
            }

            Log::info('Schedule details generated successfully', [
                'jadwal_id' => $this->id,
                'details_count' => count($details)
            ]);

            return $details;
        } catch (\Exception $e) {
            Log::error('Error generating schedule details', [
                'jadwal_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception('Gagal membuat detail jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Get schedule details with shift information
     */
    public function getDetailedSchedule()
    {
        return $this->scheduleDetails()
            ->select('jadwal_details.*')
            ->with(['jadwal.shift'])
            ->orderBy('work_date')
            ->get()
            ->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'work_date' => $detail->work_date,
                    'day_name' => $detail->day_name_indonesian,
                    'shift_name' => $this->shift->name ?? '-',
                    'shift_start' => $this->shift->start_time ?? '-',
                    'shift_end' => $this->shift->end_time ?? '-',
                    'actual_start_time' => $detail->actual_start_time,
                    'actual_end_time' => $detail->actual_end_time,
                    'attendance_status' => $detail->attendance_status,
                    'notes' => $detail->notes
                ];
            });
    }

    /**
     * Update schedule details when schedule is modified
     */
    public function updateScheduleDetails()
    {
        // Only regenerate if dates or shift changed
        if ($this->isDirty(['start_date', 'end_date', 'shift_id'])) {
            return $this->generateScheduleDetails();
        }

        return true;
    }

    /**
     * Check if this schedule conflicts with another schedule
     */
    public function hasConflictWith($otherSchedule)
    {
        if ($this->user_id !== $otherSchedule->user_id) {
            return false;
        }

        $thisStart = Carbon::parse($this->start_date);
        $thisEnd = Carbon::parse($this->end_date);
        $otherStart = Carbon::parse($otherSchedule->start_date);
        $otherEnd = Carbon::parse($otherSchedule->end_date);

        // Check for date overlap
        return $thisStart->lte($otherEnd) && $thisEnd->gte($otherStart);
    }

    /**
     * Get formatted period string
     */
    public function getFormattedPeriodAttribute()
    {
        return Carbon::parse($this->start_date)->format('d/m/Y') . ' - ' .
            Carbon::parse($this->end_date)->format('d/m/Y');
    }

    /**
     * Get week info string
     */
    public function getWeekInfoAttribute()
    {
        return 'Week ' . $this->week_number . ', ' . $this->year;
    }

    /**
     * Scope for active schedules
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($subQ) use ($startDate, $endDate) {
                    $subQ->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        });
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically generate schedule details after creating
        // static::created(function ($jadwal) {
        //     try {
        //         $jadwal->generateScheduleDetails();
        //     } catch (\Exception $e) {
        //         Log::error('Failed to generate schedule details on creation', [
        //             'jadwal_id' => $jadwal->id,
        //             'error' => $e->getMessage()
        //         ]);
        //     }
        // });

        // Clean up schedule details before deleting
        static::deleting(function ($jadwal) {
            try {
                $jadwal->scheduleDetails()->delete();
            } catch (\Exception $e) {
                Log::error('Failed to delete schedule details', [
                    'jadwal_id' => $jadwal->id,
                    'error' => $e->getMessage()
                ]);
            }
        });
    }
}
