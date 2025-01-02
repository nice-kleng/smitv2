<?php

namespace Modules\Inventory\Models;

use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

// use Modules\Inventory\Database\Factories\PermintaanFactory;

class Permintaan extends Model
{
    use HasFactory, LogsActivity;


    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['kode_permintaan', 'pu', 'barang_id', 'jumlah', 'jumlah_approve', 'tanggal_permintaan', 'tanggal_approve', 'status', 'keterangan', 'penerima', 'ruangan_id', 'approve_id', 'created_id', 'updated_id'];

    // protected static function newFactory(): PermintaanFactory
    // {
    //     // return PermintaanFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getStatusAttributeLabel()
    {
        $status = [
            '0' => 'Proses',
            '1' => 'Ditolak',
            '2' => 'Disetujui',
            '3' => 'Diambil',
        ];

        return $status[$this->status];
    }

    public function barang()
    {
        return $this->belongsTo(MasterBarang::class, 'barang_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function approve()
    {
        return $this->belongsTo(User::class, 'approve_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_id');
    }

    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_id');
    }

    public function transaksi()
    {
        return $this->hasOne(Transaksi::class, 'permintaan_id');
    }
}
