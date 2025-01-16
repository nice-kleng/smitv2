<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Inventory\Database\Factories\HistoryServiceFactory;

class HistoryService extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['inventaris_id', 'tempat_service', 'kerusakan', 'biaya'];

    // protected static function newFactory(): HistoryServiceFactory
    // {
    //     // return HistoryServiceFactory::new();
    // }

    /**
     * Get the inventory that owns the history service.
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventaris_id');
    }
}
