<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\MasterBarang;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Satuan extends Model
{
    use LogsActivity;
    protected $fillable = ['nama_satuan', 'pu'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function masterBarang()
    {
        return $this->hasMany(MasterBarang::class);
    }
}
