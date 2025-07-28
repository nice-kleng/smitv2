<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class JadwalDetail extends Model
{
    protected $fillable = [
        'schedule_id',
        'work_date',
        'day_name',
        'actual_start_time',
        'actual_end_time',
        'attendance_status',
        'notes'
    ];

    // protected $casts = [
    //     'work_date' => 'date',
    //     'actual_start_time' => 'datetime:H:i',
    //     'actual_end_time' => 'datetime:H:i'
    // ];

    protected $dates = [
        'date',
        'created_at',
        'updated_at'
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->format('d/m/Y');
    }

    /**
     * Get formatted time range
     */
    public function getFormattedTimeAttribute()
    {
        return Carbon::parse($this->start_time)->format('H:i') . ' - ' .
            Carbon::parse($this->end_time)->format('H:i');
    }

    /**
     * Get day name in Indonesian
     */
    public function getDayNameIndonesianAttribute()
    {
        $days = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu'
        ];

        return $days[strtolower($this->day_name)] ?? $this->day_name;
    }

    /**
     * Scope for specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope for active status
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'scheduled');
    }
}
