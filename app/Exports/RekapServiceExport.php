<?php

namespace App\Exports;

use Carbon\Carbon;
use Modules\Inventory\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RekapServiceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tanggal_awal;
    protected $tanggal_akhir;
    protected $jenis_aduan;
    protected $ruangan;

    public function __construct($tanggal_awal, $tanggal_akhir, $jenis_aduan, $ruangan)
    {
        $this->tanggal_awal = $tanggal_awal;
        $this->tanggal_akhir = $tanggal_akhir;
        $this->jenis_aduan = $jenis_aduan;
        $this->ruangan = $ruangan;
    }

    public function collection()
    {
        $query = Ticket::with('inventaris.barang', 'ruangan.unit', 'jenisAduan')
            ->where('status', 1);

        if ($this->tanggal_awal) {
            if ($this->tanggal_akhir) {
                $query->whereBetween('created_at', [
                    $this->tanggal_awal . ' 00:00:00',
                    $this->tanggal_akhir . ' 23:59:59'
                ]);
            } else {
                $query->whereDate('created_at', $this->tanggal_awal);
            }
        }

        if ($this->jenis_aduan) {
            $query->where('jenis_aduan_id', $this->jenis_aduan);
        }

        if ($this->ruangan) {
            $query->where('ruangan_id', $this->ruangan);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Ticket',
            'Tanggal Pengaduan',
            'Unit',
            'Ruangan',
            'Kode Inventaris',
            'Nama Barang',
            'Jenis Aduan',
            'Keterangan Perbaikan',
            'Tanggal Selesai'
        ];
    }

    public function map($ticket): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $ticket->kd_ticket,
            Carbon::parse($ticket->created_at)->isoFormat('D MMMM Y HH:mm'),
            $ticket->ruangan->unit->nama_unit,
            $ticket->ruangan->nama_ruangan,
            $ticket->inventaris ? $ticket->inventaris->kode_barang : '-',
            $ticket->inventaris ? $ticket->inventaris->barang->nama_barang : '-',
            $ticket->jenisAduan ? $ticket->jenisAduan->nama_jenis : '-',
            $ticket->keterangan_perbaikan,
            $ticket->tanggal_perbaikan ? Carbon::parse($ticket->tanggal_perbaikan)->isoFormat('D MMMM Y HH:mm') : '-'
        ];
    }
}
