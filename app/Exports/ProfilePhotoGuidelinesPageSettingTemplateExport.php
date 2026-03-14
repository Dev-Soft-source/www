<?php

namespace App\Exports;

use App\Models\Language;
use App\Models\ProfilePhotoGuidelinesPageSetting;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfilePhotoGuidelinesPageSettingTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $format;
    protected $languages;
    protected $existingData;

    public function __construct($format = 'single_column', $languages = null, $existingData = null)
    {
        $this->format = $format;
        $this->languages = $languages ? collect($languages) : null;
        $this->existingData = $existingData;
    }

    public static function getTranslatableFieldsWithDefaults(): array
    {
        return array_fill_keys(['name', 'meta_keywords', 'meta_description', 'main_heading', 'main_text', 'example_label'], '');
    }

    public function collection(): Collection
    {
        if ($this->format === 'single_column') {
            return $this->singleColumnFormat();
        }
        if ($this->format === 'all_languages') {
            return $this->allLanguagesFormat();
        }

        return $this->multiColumnFormat();
    }

    protected function singleColumnFormat(): Collection
    {
        $fields = static::getTranslatableFieldsWithDefaults();
        $rows = [];
        foreach ($fields as $field => $default) {
            $rows[] = ['field_name' => $field, 'translation_value' => $default];
        }

        return new Collection($rows);
    }

    protected function allLanguagesFormat(): Collection
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $fields = static::getTranslatableFieldsWithDefaults();
        $detailsByLang = [];
        if ($this->existingData && $this->existingData->relationLoaded('profilePhotoGuidelinesPageSettingDetail')) {
            foreach ($this->existingData->profilePhotoGuidelinesPageSettingDetail as $detail) {
                $detailsByLang[$detail->language_id] = $detail;
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

    protected function multiColumnFormat(): Collection
    {
        return new Collection([static::getTranslatableFieldsWithDefaults()]);
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

        return array_map(fn ($field) => ucwords(str_replace('_', ' ', $field)), array_keys(static::getTranslatableFieldsWithDefaults()));
    }

    public function styles(Worksheet $sheet): array
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
                $widths[$col] = 25;
            }
            return $widths;
        }

        return ['A' => 25, 'B' => 25];
    }
}
