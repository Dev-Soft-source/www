<?php

namespace App\Exports;

use App\Models\Language;
use App\Models\Step2PageSetting;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Step2PageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnWidths
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /** @var Step2PageSetting|null */
    protected $existingData;

    public static function getTranslatableFieldsWithDefaults(): array
    {
        return [
            'name' => 'Step 2',
            'meta_keywords' => 'step 2, photo',
            'meta_description' => 'Step 2 of 5',
            'main_heading' => 'Upload photo',
            'photo_error' => 'Please upload a photo',
            'photo_placeholder' => 'Choose photo',
            'mobile_photo_label' => 'Photo',
            'mobile_choose_file_label' => 'Choose file',
            'photo_label' => 'Photo',
            'skip_button_label' => 'Skip',
            'next_button_label' => 'Next',
            'logout_button_label' => 'Logout',
            'sub_heading_text' => 'Add your profile photo',
        ];
    }

    public function __construct($format = 'single_column', $languages = null, $existingData = null)
    {
        $this->format = $format;
        $this->languages = $languages ? collect($languages) : null;
        $this->existingData = $existingData;
    }

    public function collection(): Collection
    {
        $fields = array_keys(static::getTranslatableFieldsWithDefaults());
        if ($this->format === 'all_languages') {
            return $this->allLanguagesFormat();
        }
        if ($this->format === 'single_column') {
            $defaults = static::getTranslatableFieldsWithDefaults();
            $rows = [];
            foreach ($fields as $field) {
                $rows[] = ['field_name' => $field, 'translation_value' => $defaults[$field] ?? ''];
            }
            return new Collection($rows);
        }
        $row = array_fill_keys($fields, '');
        return new Collection([$row]);
    }

    protected function allLanguagesFormat(): Collection
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $fieldsWithDefaults = static::getTranslatableFieldsWithDefaults();
        $detailsByLang = [];
        if ($this->existingData && $this->existingData->relationLoaded('step2PageSettingDetail')) {
            foreach ($this->existingData->step2PageSettingDetail as $d) {
                $detailsByLang[$d->language_id] = $d;
            }
        }

        $rows = [];
        foreach ($fieldsWithDefaults as $fieldKey => $defaultValue) {
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

    public function headings(): array
    {
        if ($this->format === 'single_column') {
            return ['Field Name', 'Translation Value'];
        }
        if ($this->format === 'all_languages') {
            $languages = $this->languages ?? Language::orderBy('id')->get();
            return array_merge(['Field Name'], $languages->pluck('name')->toArray());
        }
        $fields = array_keys(static::getTranslatableFieldsWithDefaults());
        return array_map(fn($f) => ucwords(str_replace('_', ' ', $f)), $fields);
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
            return ['A' => 40, 'B' => 50];
        }
        if ($this->format === 'all_languages') {
            $totalCols = ($this->languages ?? Language::orderBy('id')->get())->count() + 1;
            $widths = [];
            for ($colIndex = 1; $colIndex <= $totalCols; $colIndex++) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $widths[$col] = $colIndex === 1 ? 40 : 30;
            }
            return $widths;
        }
        return ['A' => 25];
    }
}
