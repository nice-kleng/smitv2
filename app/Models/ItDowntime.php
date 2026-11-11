<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItDowntime extends Model
{
    protected $fillable = [
        'sistem',
        'kategori',
        'mulai_at',
        'selesai_at',
        'status',
        'dampak',
        'penyebab',
        'tindakan',
        'pic',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'mulai_at' => 'datetime',
        'selesai_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
