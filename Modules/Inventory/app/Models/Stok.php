<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Inventory\Database\Factories\StokFactory;

class Stok extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['master_barang_id', 'stok', 'harga', 'keterangan'];

    // protected static function newFactory(): StokFactory
    // {
    //     // return StokFactory::new();
    // }

    public function barang()
    {
        return $this->belongsTo(MasterBarang::class, 'master_barang_id');
    }
}
