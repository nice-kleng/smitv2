<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
        return $this->hasMany(JadwalDetail::class);
    }

    public function getFormattedPeriodAttribute()
    {
        return Carbon::parse($this->start_date)->format('d/m/Y') . ' - ' .
            Carbon::parse($this->end_date)->format('d/m/Y');
    }

    /**
     * Get week info
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
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate]);
    }

    // Helper method untuk generate detail jadwal harian
    // public function generateScheduleDetails()
    // {
    //     $shift = $this->shift;
    //     $workDays = $shift->work_days;

    //     $current = $this->start_date->copy();

    //     while ($current <= $this->end_date) {
    //         $dayName = strtolower($current->format('l'));

    //         if (in_array($dayName, $workDays)) {
    //             JadwalDetail::create([
    //                 'schedule_id' => $this->id,
    //                 'work_date' => $current,
    //                 'day_name' => $dayName
    //             ]);
    //         }

    //         $current->addDay();
    //     }
    // }

    public function generateScheduleDetails()
    {
        try {
            // Delete existing details
            $this->scheduleDetails()->delete();

            if (!$this->shift) {
                return;
            }

            $workDays = json_decode($this->shift->work_days, true) ?? [];
            $startDate = Carbon::parse($this->start_date);
            $endDate = Carbon::parse($this->end_date);

            $current = $startDate->copy();

            while ($current->lte($endDate)) {
                $dayName = strtolower($current->format('l')); // monday, tuesday, etc.

                // Check if current day is a working day
                if (in_array($dayName, array_map('strtolower', $workDays))) {
                    \App\Models\JadwalDetail::create([
                        'jadwal_id' => $this->id,
                        'date' => $current->format('Y-m-d'),
                        'day_name' => $dayName,
                        'start_time' => $this->shift->start_time,
                        'end_time' => $this->shift->end_time,
                        'status' => 'scheduled'
                    ]);
                }

                $current->addDay();
            }
        } catch (\Exception $e) {
            \Log::error('Error generating schedule details', [
                'jadwal_id' => $this->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
