<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'work_days',
        'description',
    ];

    protected $casts = [
        'work_days' => 'array',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i'
    ];

    public function schedules()
    {
        return $this->hasMany(Jadwal::class);
    }
}
