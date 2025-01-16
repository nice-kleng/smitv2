<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Inventory\Database\Factories\JenisAduanFactory;

class JenisAduan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['nama_jenis'];

    // protected static function newFactory(): JenisAduanFactory
    // {
    //     // return JenisAduanFactory::new();
    // }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'jenis_aduan_id');
    }
}
