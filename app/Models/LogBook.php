<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\Ticket;

class LogBook extends Model
{
    protected $table = 'log_books';
    protected $fillable = ['user_id', 'kegiatan', 'keterangan', 'jenis', 'service_id'];

    public function staf()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Ticket::class);
    }
}
