<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;

class FirmCancellationPageSettingTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $format;

    public function __construct($format = 'single_column')
    {
        $this->format = $format;
    }

    public function collection()
    {
        if ($this->format === 'single_column') {
            return collect([
                ['name', 'Firm Cancellation Policy'],
                ['meta_keywords', 'firm, cancellation, policy, non-refundable'],
                ['meta_description', 'Learn about our firm cancellation policy'],
                ['main_heading', 'Firm Cancellation Policy'],
                ['main_text', 'Please read our firm cancellation policy carefully.'],
            ]);
        }
        
        return collect([
            [
                'Firm Cancellation Policy',
                'firm, cancellation, policy, non-refundable',
                'Learn about our firm cancellation policy',
                'Firm Cancellation Policy',
                'Please read our firm cancellation policy carefully.',
            ]
        ]);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') {
            return ['Field Name', 'Translation Value'];
        }
        
        return ['name', 'meta_keywords', 'meta_description', 'main_heading', 'main_text'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        if ($this->format === 'single_column') {
            return ['A' => 25, 'B' => 80];
        }
        
        return ['A' => 25, 'B' => 30, 'C' => 40, 'D' => 25, 'E' => 80];
    }
}

