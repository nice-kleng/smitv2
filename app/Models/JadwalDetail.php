<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class JadwalDetail extends Model
{
    protected $table = 'jadwal_details';

    protected $fillable = [
        'schedule_id',
        'work_date',
        'day_name',
        'actual_start_time',
        'actual_end_time',
        'attendance_status',
        'notes'
    ];

    protected $casts = [
        // 'work_date' => 'date',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime'
    ];

    // protected $dates = [
    //     'work_date',
    //     'actual_start_time',
    //     'actual_end_time',
    //     'created_at',
    //     'updated_at'
    // ];

    /**
     * Relationship with Jadwal
     */
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'schedule_id');
    }

    /**
     * Get formatted work date
     */
    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->work_date)->format('d/m/Y');
    }

    /**
     * Get formatted time range from shift
     */
    public function getFormattedShiftTimeAttribute()
    {
        if ($this->jadwal && $this->jadwal->shift) {
            $startTime = Carbon::parse($this->jadwal->shift->start_time)->format('H:i');
            $endTime = Carbon::parse($this->jadwal->shift->end_time)->format('H:i');
            return $startTime . ' - ' . $endTime;
        }
        return '-';
    }

    /**
     * Get formatted actual time range
     */
    public function getFormattedActualTimeAttribute()
    {
        if ($this->actual_start_time && $this->actual_end_time) {
            return $this->actual_start_time->format('H:i') . ' - ' . $this->actual_end_time->format('H:i');
        } elseif ($this->actual_start_time) {
            return $this->actual_start_time->format('H:i') . ' - (Belum checkout)';
        }
        return 'Belum absen';
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
     * Get attendance status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            'scheduled' => 'secondary',
            'present' => 'success',
            'absent' => 'danger',
            'late' => 'warning',
            'early_leave' => 'info',
            'overtime' => 'primary'
        ];

        return $classes[$this->attendance_status] ?? 'secondary';
    }

    /**
     * Get attendance status in Indonesian
     */
    public function getStatusIndonesianAttribute()
    {
        $statuses = [
            'scheduled' => 'Terjadwal',
            'present' => 'Hadir',
            'absent' => 'Tidak Hadir',
            'late' => 'Terlambat',
            'early_leave' => 'Pulang Cepat',
            'overtime' => 'Lembur'
        ];

        return $statuses[$this->attendance_status] ?? 'Tidak Diketahui';
    }

    /**
     * Check if employee is late based on shift start time
     */
    public function isLate()
    {
        if (!$this->actual_start_time || !$this->jadwal || !$this->jadwal->shift) {
            return false;
        }

        $workDate = $this->work_date instanceof Carbon ? $this->work_date : Carbon::parse($this->work_date);
        $shiftStart = Carbon::parse($workDate->format('Y-m-d') . ' ' . $this->jadwal->shift->start_time);
        return $this->actual_start_time->gt($shiftStart);
    }

    /**
     * Check if employee left early
     */
    public function isEarlyLeave()
    {
        if (!$this->actual_end_time || !$this->jadwal || !$this->jadwal->shift) {
            return false;
        }

        $workDate = $this->work_date instanceof Carbon ? $this->work_date : Carbon::parse($this->work_date);
        $shiftEnd = Carbon::parse($workDate->format('Y-m-d') . ' ' . $this->jadwal->shift->end_time);
        return $this->actual_end_time->lt($shiftEnd);
    }

    /**
     * Calculate working hours
     */
    public function getWorkingHoursAttribute()
    {
        if (!$this->actual_start_time || !$this->actual_end_time) {
            return 0;
        }

        $minutes = $this->actual_start_time->diffInMinutes($this->actual_end_time);
        return round($minutes / 60, 2); // hasil dibulatkan 2 desimal, misal 4.02
    }

    /**
     * Update attendance status based on actual times
     */
    public function updateAttendanceStatus()
    {
        if (!$this->actual_start_time) {
            $this->attendance_status = 'absent';
        } elseif ($this->isLate() && $this->isEarlyLeave()) {
            $this->attendance_status = 'late';
        } elseif ($this->isLate()) {
            $this->attendance_status = 'late';
        } elseif ($this->isEarlyLeave()) {
            $this->attendance_status = 'early_leave';
        } else {
            $this->attendance_status = 'present';
        }

        $this->save();
        return $this->attendance_status;
    }

    /**
     * Record check-in time
     */
    public function checkIn($time = null)
    {
        $this->actual_start_time = $time ? $time : now();
        $this->updateAttendanceStatus();
        return $this->save();
    }

    /**
     * Record check-out time
     */
    public function checkOut($time = null)
    {
        $this->actual_end_time = $time ? $time : now();
        $this->updateAttendanceStatus();
        return $this->save();
    }

    /**
     * Scope for specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('work_date', $date);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('work_date', [$startDate, $endDate]);
    }

    /**
     * Scope for scheduled status
     */
    public function scopeScheduled($query)
    {
        return $query->where('attendance_status', 'scheduled');
    }

    /**
     * Scope for present status
     */
    public function scopePresent($query)
    {
        return $query->where('attendance_status', 'present');
    }

    /**
     * Scope for absent status
     */
    public function scopeAbsent($query)
    {
        return $query->where('attendance_status', 'absent');
    }

    /**
     * Scope for today's schedule
     */
    public function scopeToday($query)
    {
        return $query->where('work_date', now()->format('Y-m-d'));
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically update attendance status when actual times are set
        static::saving(function ($jadwalDetail) {
            if ($jadwalDetail->isDirty(['actual_start_time', 'actual_end_time'])) {
                if ($jadwalDetail->actual_start_time && !$jadwalDetail->actual_end_time) {
                    // Only check-in recorded
                    $jadwalDetail->attendance_status = $jadwalDetail->isLate() ? 'late' : 'present';
                } elseif ($jadwalDetail->actual_start_time && $jadwalDetail->actual_end_time) {
                    // Both check-in and check-out recorded
                    if ($jadwalDetail->isLate() && $jadwalDetail->isEarlyLeave()) {
                        $jadwalDetail->attendance_status = 'late';
                    } elseif ($jadwalDetail->isLate()) {
                        $jadwalDetail->attendance_status = 'late';
                    } elseif ($jadwalDetail->isEarlyLeave()) {
                        $jadwalDetail->attendance_status = 'early_leave';
                    } else {
                        $jadwalDetail->attendance_status = 'present';
                    }
                }
            }
        });
    }
}
