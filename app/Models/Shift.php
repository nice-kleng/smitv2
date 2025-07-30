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
    ];

    public function schedules()
    {
        return $this->hasMany(Jadwal::class);
    }
}
