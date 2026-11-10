<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Modules\Inventory\Models\Inventory;
use Modules\Inventory\Models\HistoryInventaris;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class InventoryExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected array $filters = [];

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Inventory::with('barang.kategori', 'ruangan.unit', 'historyMutasi');

        // Filter unit
        if (!empty($this->filters['unit_id'])) {
            $ruanganIds = \App\Models\Ruangan::where('unit_id', $this->filters['unit_id'])->pluck('id');
            $query->whereIn('ruangan_id', $ruanganIds);
        }

        // Filter ruangan
        if (!empty($this->filters['ruangan_id'])) {
            $query->where('ruangan_id', $this->filters['ruangan_id']);
        }

        // Filter kondisi (berdasarkan history mutasi terbaru per inventaris)
        if (!empty($this->filters['kondisi'])) {
            $latestHistoryIds = HistoryInventaris::selectRaw('MAX(id) as id')
                ->groupBy('inventory_id')
                ->pluck('id');

            $filteredInventoryIds = HistoryInventaris::whereIn('id', $latestHistoryIds)
                ->where('kondisi', $this->filters['kondisi'])
                ->pluck('inventory_id');

            $query->whereIn('id', $filteredInventoryIds);
        }

        if (Auth::user()->hasAnyRole(['superadmin', 'admin'])) {
            $query->whereHas('barang', function ($q) {
                $q->where('pu', Auth::user()->pu_kd);
            });
        } else {
            $query->where('ruangan_id', Auth::user()->ruangan_id);
        }

        return $query->orderBy('kode_barang', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'Kode Inventaris',
            'Nama Alias',
            'Nama Barang',
            'Merk',
            'Type',
            'Nomor Seri',
            'Kategori',
            'Tahun Pegadaan',
            'Unit',
            'Ruangan',
            'Harga Beli',
            'Kondisi',
            'Status',
            'Kepemilikan',
        ];
    }

    public function map($row): array
    {
        $kondisi = $row->historyMutasi->sortByDesc('created_at')->first();
        $kondisiText = $kondisi ? $kondisi->kondisi : 'Belum ada kondisi';

        if ($row->penghapus_id) {
            $status = 'Dihapus';
        } else {
            $status = match ((string) $row->getRawOriginal('status')) {
                '0' => 'Telah Dihapuskan',
                '1' => 'Perlu Dihapuskan',
                '2' => 'Aktif',
                default => 'Tidak Diketahui',
            };
        }

        return [
            $row->kode_barang,
            $row->nama_alias ?? '-',
            $row->barang->nama_barang ?? '-',
            $row->merk ?? '-',
            $row->type ?? '-',
            $row->serial_number ?? '-',
            $row->barang->kategori->nama_kategori ?? '-',
            $row->tahun_pengadaan ?? '-',
            $row->ruangan->unit->nama_unit ?? '-',
            $row->ruangan->nama_ruangan ?? '-',
            $row->harga_beli ?? '-',
            $kondisiText,
            $status,
            $row->kepemilikan ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Data Inventaris';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4682B4'],
                ],
                'font' => [
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
            'A1:N' . $sheet->getHighestRow() => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
