<?php

namespace App\Exports;

use App\Models\HasilTopsis;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RankingExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize,
    WithEvents
{
    protected $bulan;
    protected $tahun;
    protected $periodeLabel;
    protected $idKaryawan; // Jika null = export semua, jika ada = export pribadi
    protected $divisiFilter; // Filter divisi
    protected $search; // Search keyword

    public function __construct($bulan, $tahun, $periodeLabel, $idKaryawan = null, $divisiFilter = '', $search = '')
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->periodeLabel = $periodeLabel;
        $this->idKaryawan = $idKaryawan;
        $this->divisiFilter = $divisiFilter;
        $this->search = $search;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = HasilTopsis::with('karyawan')
            ->byPeriode($this->bulan, $this->tahun)
            ->orderedByRanking();

        // Jika export pribadi, filter hanya karyawan tertentu
        if ($this->idKaryawan) {
            $query->where('id_karyawan', $this->idKaryawan);
        }

        // Filter by divisi_filter field
        if (!empty($this->divisiFilter)) {
            $query->where('divisi_filter', $this->divisiFilter);
        }

        // Filter by search (nama atau NIK)
        if (!empty($this->search)) {
            $query->whereHas('karyawan', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('nik', 'like', '%' . $this->search . '%');
            });
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Rank',
            'NIK',
            'Nama Karyawan',
            'Divisi',
            'Jabatan',
            'Skor TOPSIS',
            'Persentase',
            'D+ (Positif)',
            'D- (Negatif)',
            'Tanggal Generate',
        ];
    }

    /**
     * @var HasilTopsis $hasil
     */
    public function map($hasil): array
    {
        return [
            $hasil->ranking,
            $hasil->karyawan->nik,
            $hasil->karyawan->nama,
            $hasil->karyawan->divisi,
            $hasil->karyawan->jabatan,
            number_format($hasil->skor_topsis, 6),
            number_format($hasil->skor_topsis * 100, 2) . '%',
            number_format($hasil->jarak_ideal_positif, 6),
            number_format($hasil->jarak_ideal_negatif, 6),
            $hasil->tanggal_generate->format('d/m/Y H:i'),
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Ranking ' . $this->periodeLabel;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Add borders to all cells
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);

                // Center align rank column
                $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Center align skor columns
                $sheet->getStyle('F2:I' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Set header row height
                $sheet->getRowDimension(1)->setRowHeight(25);

                // Highlight top 3 ranks
                for ($row = 2; $row <= $highestRow; $row++) {
                    $rank = $sheet->getCell('A' . $row)->getValue();

                    if ($rank == 1) {
                        // Gold for rank 1
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FDE68A'],
                            ],
                            'font' => ['bold' => true],
                        ]);
                    } elseif ($rank == 2) {
                        // Silver for rank 2
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'E5E7EB'],
                            ],
                            'font' => ['bold' => true],
                        ]);
                    } elseif ($rank == 3) {
                        // Bronze for rank 3
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FED7AA'],
                            ],
                            'font' => ['bold' => true],
                        ]);
                    }
                }

                // Freeze first row
                $sheet->freezePane('A2');
            },
        ];
    }
}
