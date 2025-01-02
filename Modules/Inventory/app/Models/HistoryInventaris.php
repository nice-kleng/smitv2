<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Inventory\Database\Factories\HistoryInventarisFactory;

class HistoryInventaris extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['inventory_id', 'unit_id', 'ruangan_id', 'kondisi', 'tanggal_mutasi', 'keterangan', 'created_id', 'updated_id'];

    public function getKondisiAttribute($value)
    {
        $kondisi = [
            '0' => 'Rusak',
            '1' => 'Kurang Baik',
            '2' => 'Baik'
        ];

        return $kondisi[$value];
    }

    // protected static function newFactory(): HistoryInventarisFactory
    // {
    //     // return HistoryInventarisFactory::new();
    // }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
