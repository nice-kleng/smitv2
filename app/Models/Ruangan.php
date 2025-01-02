<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\Pengajuan;
use Modules\Inventory\Models\Permintaan;
use Spatie\Activitylog\LogOptions;

class Ruangan extends Model
{
    protected $fillable = ['unit_id', 'kode_ruangan', 'nama_ruangan'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function permintaan()
    {
        return $this->hasMany(Permintaan::class);
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
