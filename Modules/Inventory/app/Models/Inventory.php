<?php

namespace Modules\Inventory\Models;

use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Inventory\Database\Factories\InventoryFactory;

class Inventory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['kode_barang', 'no_barang', 'barang_id', 'ruangan_id', 'harga_beli', 'satuan', 'merk', 'type', 'serial_number', 'spesifikasi', 'tahun_pengadaan', 'status', 'catatan', 'kepemilikan', 'tgl_penghapusan', 'penghapus_id', 'alasan_penghapusan'];

    // protected static function newFactory(): InventoryFactory
    // {
    //     // return InventoryFactory::new();
    // }

    public function getStatusAttribute($value)
    {
        $status = [
            '0' => 'Telah Dihapuskan',
            '1' => 'Perlu Dihapuskan',
            '2' => 'Aktif'
        ];

        return $status[$value];
    }

    public function barang()
    {
        return $this->belongsTo(MasterBarang::class, 'barang_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function penghapus()
    {
        return $this->belongsTo(User::class, 'penghapus_id');
    }

    public function historyMutasi()
    {
        return $this->hasMany(HistoryInventaris::class, 'inventory_id');
    }
}
