<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Modules\Inventory\Models\MasterBarang;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MasterBarangExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return MasterBarang::with('satuan', 'kategori')
            ->when(Auth::user()->hasRole('admin'), function ($query) {
                $query->where('pu', Auth::user()->pu_kd);
            })
            ->orderBy('nama_barang', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Barang',
            'Nama Barang',
            'Satuan',
            'Kategori',
            'Jenis',
            'Is Elektronik',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->nama_barang,
            $row->satuan->nama_satuan ?? '-',
            $row->kategori->nama_kategori ?? '-',
            $row->jenis ? 'Inventaris' : 'BHP',
            $row->is_elektronik ? 'Ya' : 'Tidak',
            $row->keterangan ?? '-'
        ];
    }

    public function title(): string
    {
        return 'Data Master Barang';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4682B4'] // Steel Blue color
                ],
                'font' => [
                    'color' => ['rgb' => 'FFFFFF']
                ],
            ],
            'A1:G' . $sheet->getHighestRow() => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
            ],
        ];
    }
}
