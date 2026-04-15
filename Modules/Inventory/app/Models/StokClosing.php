<?php

namespace Modules\Inventory\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class StokClosing extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'periode',
        'stok_id',
        'stok_awal',
        'stok_masuk',
        'stok_keluar',
        'stok_akhir_sistem',
        'harga_per_unit',
        'total_nilai_awal',
        'total_nilai_masuk',
        'total_nilai_keluar',
        'total_nilai_akhir',
        'stok_fisik',
        'selisih',
        'keterangan',
        'is_closed',
        'closed_by',
        'closed_at',
        'reopened_by',
        'reopened_at',
        'reopen_reason'
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'closed_at' => 'datetime',
        'reopened_at' => 'datetime',
        'harga_per_unit' => 'decimal:2',
        'total_nilai_awal' => 'decimal:2',
        'total_nilai_masuk' => 'decimal:2',
        'total_nilai_keluar' => 'decimal:2',
        'total_nilai_akhir' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function stok()
    {
        return $this->belongsTo(Stok::class, 'stok_id');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function reopenedBy()
    {
        return $this->belongsTo(User::class, 'reopened_by');
    }

    // Accessors
    public function getNamaBarangAttribute()
    {
        return $this->stok->barang->nama_barang ?? '-';
    }

    public function getKodeBarangAttribute()
    {
        return $this->stok->barang->kode_barang ?? '-';
    }

    public function getSatuanAttribute()
    {
        return $this->stok->barang->satuan->nama ?? '-';
    }

    public function getKategoriAttribute()
    {
        return $this->stok->barang->kategori->nama ?? '-';
    }

    public function getBatchKeteranganAttribute()
    {
        return $this->stok->keterangan ?? '-';
    }

    public function getStatusLabelAttribute()
    {
        return $this->is_closed ? 'CLOSED' : 'DRAFT';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->is_closed
            ? '<span class="badge badge-success">🔒 CLOSED</span>'
            : '<span class="badge badge-warning">⚠️ DRAFT</span>';
    }

    public function getPeriodeFormatAttribute()
    {
        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        [$year, $month] = explode('-', $this->periode);
        return $months[$month] . ' ' . $year;
    }

    public function getHasSelisihAttribute()
    {
        return $this->stok_fisik !== null && $this->selisih != 0;
    }

    // Scopes
    public function scopeClosed($query)
    {
        return $query->where('is_closed', true);
    }

    public function scopeDraft($query)
    {
        return $query->where('is_closed', false);
    }

    public function scopePeriode($query, $periode)
    {
        return $query->where('periode', $periode);
    }

    // Methods
    public function hitungStokAkhir()
    {
        $this->stok_akhir_sistem = $this->stok_awal + $this->stok_masuk - $this->stok_keluar;
        return $this->stok_akhir_sistem;
    }

    public function hitungSelisih()
    {
        if ($this->stok_fisik !== null) {
            $this->selisih = $this->stok_fisik - $this->stok_akhir_sistem;
        }
        return $this->selisih;
    }

    public function hitungNilai()
    {
        $this->total_nilai_awal = $this->stok_awal * $this->harga_per_unit;
        $this->total_nilai_masuk = $this->stok_masuk * $this->harga_per_unit;
        $this->total_nilai_keluar = $this->stok_keluar * $this->harga_per_unit;
        $this->total_nilai_akhir = ($this->stok_fisik ?? $this->stok_akhir_sistem) * $this->harga_per_unit;
    }

    public function prosesClosed($userId)
    {
        $this->is_closed = true;
        $this->closed_by = $userId;
        $this->closed_at = now();
        $this->save();
    }

    public function prosesReopen($userId, $reason)
    {
        $this->is_closed = false;
        $this->reopened_by = $userId;
        $this->reopened_at = now();
        $this->reopen_reason = $reason;
        $this->save();
    }
}
