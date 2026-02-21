<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ManualExport implements FromArray, WithHeadings, WithStyles
{
    protected array $rows;
    protected array $headers;
    protected array $highlightRows;

    public function __construct(array $headers, array $rows, array $highlightRows = [])
    {
        $this->headers = $headers;
        $this->rows = $rows;
        $this->highlightRows = $highlightRows;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $styles = [];

        foreach ($this->highlightRows as $row) {
            $styles[(int) $row] = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFFFF00'],
                ],
            ];
        }

        return $styles;
    }
}
