<?php

namespace App\Exports;

use Modules\Inventory\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TicketExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    protected $filters;
    protected $rowNumber = 0;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Ticket::with(['inventaris.barang', 'ruangan.unit', 'jenisAduan'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (!empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }

        if (!empty($this->filters['jenis_aduan'])) {
            $query->where('jenis_aduan_id', $this->filters['jenis_aduan']);
        }

        if (!empty($this->filters['ruangan'])) {
            $query->where('ruangan_id', $this->filters['ruangan']);
        }

        return $query->get();
    }

    /**
     * @return array
     */
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
            'Detail Aduan',
            'Tindakan',
            'Keterangan Perbaikan',
            'Tanggal Selesai'
        ];
    }

    /**
     * @param mixed $ticket
     * @return array
     */
    public function map($ticket): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $ticket->kd_ticket ?? '-',
            $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-',
            $ticket->ruangan && $ticket->ruangan->unit ? $ticket->ruangan->unit->nama_unit : '-',
            $ticket->ruangan ? $ticket->ruangan->nama_ruangan : '-',
            $ticket->inventaris ? ($ticket->inventaris->kode_barang ?? $ticket->inventaris->no_barang ?? '-') : '-',
            $ticket->inventaris && $ticket->inventaris->barang ? $ticket->inventaris->barang->nama_barang : '-',
            $ticket->jenisAduan ? $ticket->jenisAduan->nama_jenis : '-',
            $ticket->detail_aduan ?? '-',
            $ticket->tindak_lanjut ?? '-',
            $ticket->keterangan_perbaikan ?? '-',
            $ticket->tanggal_perbaikan ? \Carbon\Carbon::parse($ticket->tanggal_perbaikan)->format('d/m/Y') : '-'
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Laporan Ticket';
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Generate dynamic title
                $title = $this->generateTitle();

                // Insert 2 rows untuk title dan spacing
                $sheet->insertNewRowBefore(1, 2);

                // Set title di row 1
                $sheet->setCellValue('A1', $title);
                $sheet->mergeCells('A1:L1');

                // Style title
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => '000000']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Row 2 kosong untuk spacing

                // Header sekarang di row 3
                $sheet->getStyle('A3:L3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4472C4']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ],
                    ],
                ]);

                // Set row height
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(5);
                $sheet->getRowDimension(3)->setRowHeight(25);

                // Auto size columns
                foreach (range('A', 'J') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Add borders to all data (starting from row 4)
                $lastRow = $this->rowNumber + 3; // +3 karena title(1) + spacing(2) + header(3)

                if ($lastRow > 3) {
                    $sheet->getStyle('A3:L' . $lastRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000']
                            ],
                        ],
                    ]);

                    // Center align kolom No
                    $sheet->getStyle('A4:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Center align kolom Tanggal Pengaduan dan Tanggal Selesai
                    $sheet->getStyle('C4:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('L4:L' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }

    /**
     * Generate dynamic title based on filters
     * @return string
     */
    private function generateTitle(): string
    {
        $title = 'LAPORAN DATA TICKET PENGADUAN';
        $filterParts = [];

        // Add date range if exists
        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $startDate = \Carbon\Carbon::parse($this->filters['start_date'])->format('d/m/Y');
            $endDate = \Carbon\Carbon::parse($this->filters['end_date'])->format('d/m/Y');
            $filterParts[] = "($startDate - $endDate)";
        } elseif (!empty($this->filters['start_date'])) {
            $startDate = \Carbon\Carbon::parse($this->filters['start_date'])->format('d/m/Y');
            $filterParts[] = "(Dari $startDate)";
        }

        // Add ruangan if exists
        if (!empty($this->filters['ruangan'])) {
            $ruangan = \App\Models\Ruangan::with('unit')->find($this->filters['ruangan']);
            if ($ruangan) {
                $filterParts[] = "RUANGAN: {$ruangan->unit->nama_unit}-{$ruangan->nama_ruangan}";
            }
        }

        // Add jenis aduan if exists
        if (!empty($this->filters['jenis_aduan'])) {
            $jenisAduan = \Modules\Inventory\Models\JenisAduan::find($this->filters['jenis_aduan']);
            if ($jenisAduan) {
                $filterParts[] = "JENIS ADUAN: {$jenisAduan->nama_jenis}";
            }
        }

        // Combine title with filters
        if (!empty($filterParts)) {
            $title .= ' ' . implode(' - ', $filterParts);
        }

        return $title;
    }
}
