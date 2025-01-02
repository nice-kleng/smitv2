<?php

namespace Modules\Inventory\Models;

use App\Models\KategoriBarang;
use App\Models\Satuan;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Inventory\Database\Factories\MasterBarangFactory;

class MasterBarang extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['kode_barang', 'nama_barang', 'satuan_id', 'kategori_id', 'pu', 'jenis', 'is_elektronik', 'keterangan'];

    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class);
    }

    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class);
    }

    public function stoks()
    {
        return $this->hasMany(Stok::class, 'master_barang_id');
    }

    public function permintaaan()
    {
        return $this->hasMany(Permintaan::class, 'barang_id');
    }
}
