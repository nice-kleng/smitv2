<?php

namespace Modules\Inventory\Models;

use App\Models\Ruangan;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Inventory\Database\Factories\PengajuanFactory;

class Pengajuan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['kode_pengajuan', 'pu', 'unit_id', 'barang_id', 'harga', 'harga_approved', 'jumlah', 'jumlah_approved', 'dikeluarkan_pada', 'tanggal_pengajuan', 'tanggal_approved', 'tanggal_realisasi', 'approved_id', 'status', 'jenis_pengajuan', 'memo', 'disposisi', 'keterangan', 'keterangan_peninjauan', 'created_id', 'updated_id'];

    // protected static function newFactory(): PengajuanFactory
    // {
    //     // return PengajuanFactory::new();
    // }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function barang()
    {
        return $this->belongsTo(MasterBarang::class, 'barang_id');
    }

    public function approved()
    {
        return $this->belongsTo(User::class, 'approved_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_id');
    }

    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_id');
    }
}
