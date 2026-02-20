<?php

namespace App\Exports;

use App\Models\CancellationPageSetting;
use App\Models\Language;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;

class CancellationPageSettingTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /** @var \App\Models\CancellationPageSetting|null */
    protected $existingData;

    /**
     * @param string $format - 'single_column', 'multi_column', or 'all_languages'
     * @param \Illuminate\Support\Collection|array|null $languages
     * @param \App\Models\CancellationPageSetting|null $existingData - with cancellationPageSettingDetail loaded
     */
    public function __construct($format = 'single_column', $languages = null, $existingData = null)
    {
        $this->format = $format;
        $this->languages = $languages ? collect($languages) : null;
        $this->existingData = $existingData;
    }

    public static function getTranslatableFieldsWithDefaults(): array
    {
        return [
            'name' => 'Cancellation Policy',
            'meta_keywords' => 'cancellation, policy, refund',
            'meta_description' => 'Learn about our cancellation policy',
            'main_heading' => 'Cancellation Policy',
            'main_text' => 'Please read our cancellation policy carefully before booking.',
        ];
    }

    public function collection()
    {
        if ($this->format === 'single_column') {
            return $this->singleColumnFormat();
        }
        if ($this->format === 'all_languages') {
            return $this->allLanguagesFormat();
        }
        return $this->multiColumnFormat();
    }

    protected function singleColumnFormat()
    {
        $fields = static::getTranslatableFieldsWithDefaults();
        return collect($fields)->map(fn ($value, $key) => [$key, $value])->values();
    }

    protected function allLanguagesFormat()
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $fields = static::getTranslatableFieldsWithDefaults();
        $detailsByLang = [];
        if ($this->existingData && $this->existingData->relationLoaded('cancellationPageSettingDetail')) {
            foreach ($this->existingData->cancellationPageSettingDetail as $d) {
                $detailsByLang[$d->language_id] = $d;
            }
        }
        $rows = [];
        foreach ($fields as $fieldKey => $defaultValue) {
            $row = [$fieldKey];
            foreach ($languages as $lang) {
                $detail = $detailsByLang[$lang->id] ?? null;
                $value = $detail && isset($detail->$fieldKey) ? ($detail->$fieldKey ?? '') : $defaultValue;
                $row[] = $value;
            }
            $rows[] = $row;
        }
        return collect($rows);
    }

    protected function multiColumnFormat()
    {
        return collect([array_values(static::getTranslatableFieldsWithDefaults())]);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') {
            return ['Field Name', 'Translation Value'];
        }
        if ($this->format === 'all_languages') {
            $languages = $this->languages ?? Language::orderBy('id')->get();
            return array_merge(['Field Name'], $languages->pluck('name')->toArray());
        }
        return array_keys(static::getTranslatableFieldsWithDefaults());
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
        if ($this->format === 'all_languages') {
            $totalCols = ($this->languages ?? Language::orderBy('id')->get())->count() + 1;
            $widths = [];
            for ($colIndex = 1; $colIndex <= $totalCols; $colIndex++) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $widths[$col] = $colIndex === 1 ? 25 : 30;
            }
            return $widths;
        }
        return ['A' => 25, 'B' => 30, 'C' => 40, 'D' => 25, 'E' => 80];
    }
}
