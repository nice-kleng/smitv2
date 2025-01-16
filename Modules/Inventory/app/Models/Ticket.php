<?php

namespace Modules\Inventory\Models;

use App\Models\LogBook;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
// use Modules\Inventory\Database\Factories\TicketFactory;

class Ticket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'kd_ticket',
        'inventaris_id',
        'teknisi_id',
        'ruangan_id',
        'detail_aduan',
        'jenis_aduan_id',
        'tindak_lanjut',
        'status',
        'keterangan',
        'keterangan_perbaikan',
        'tanggal_perbaikan',
    ];

    // protected static function newFactory(): TicketFactory
    // {
    //     // return TicketFactory::new();
    // }


    public function keteranganPerbaikan(): Attribute
    {
        $keteranganPerbaikan = [
            '0' => '-',
            '1' => 'Perbaikan Sendiri',
            '2' => 'Pemeliharaan',
            '3' => 'Perbaikan dan Pemeliharaan',
            '4' => 'Service Luar',
        ];

        return Attribute::make(
            get: function ($value) use ($keteranganPerbaikan) {
                return $keteranganPerbaikan[$value];
            },
        );
    }

    public function inventaris()
    {
        return $this->belongsTo(Inventory::class, 'inventaris_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    public function jenisAduan()
    {
        return $this->belongsTo(JenisAduan::class, 'jenis_aduan_id');
    }

    public function logBooks()
    {
        return $this->hasOne(LogBook::class, 'service_id');
    }
}
