<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Inventory\Database\Factories\TransaksiFactory;

class Transaksi extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['stok_id', 'jumlah', 'keterangan', 'jenis', 'permintaan_id', 'pengajuan_id', 'created_by', 'updated_by'];

    // protected static function newFactory(): TransaksiFactory
    // {
    //     // return TransaksiFactory::new();
    // }

    public function stok()
    {
        return $this->belongsTo(Stok::class);
    }

    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class);
    }
}
