<?php

namespace App\Exports;

use App\Models\Language;
use App\Models\Step3PageSetting;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Step3PageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnWidths
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /** @var Step3PageSetting|null */
    protected $existingData;

    public static function getTranslatableFieldsWithDefaults(): array
    {
        return [
            'name' => 'Step 3',
            'meta_keywords' => 'step 3, vehicle',
            'meta_description' => 'Step 3 of 5',
            'main_heading' => 'Vehicle details',
            'main_label' => 'Details',
            'required_label' => 'Required',
            'make_label' => 'Make',
            'make_error' => 'Please enter make',
            'make_placeholder' => 'Make',
            'model_label' => 'Model',
            'model_error' => 'Please enter model',
            'model_placeholder' => 'Model',
            'vehicle_type_label' => 'Vehicle type',
            'vehicle_type_error' => 'Please select vehicle type',
            'vehicle_type_placeholder' => 'Select type',
            'color_label' => 'Color',
            'color_error' => 'Please enter color',
            'license_label' => 'License',
            'license_error' => 'Please enter license',
            'year_label' => 'Year',
            'year_error' => 'Please enter year',
            'fuel_label' => 'Fuel type',
            'fuel_error' => 'Please select fuel type',
            'electric_option_label' => 'Electric',
            'hybrid_option_label' => 'Hybrid',
            'gas_option_label' => 'Gas',
            'driver_license_error' => 'Please upload driver license',
            'mobile_driver_choose_file_label' => 'Choose file',
            'photo_label' => 'Photo',
            'photo_error' => 'Please upload photo',
            'photo_detail_label' => 'Photo details',
            'mobile_photo_choose_file_label' => 'Choose file',
            'skip_button_label' => 'Skip',
            'next_button_label' => 'Next',
            'logout_button_label' => 'Logout',
            'sub_heading' => 'Sub heading',
            'sub_main_label' => 'Sub main',
            'liecense_section_heading' => 'License',
            'vehicle_section_heading' => 'Vehicle',
            'skip_vehicle_info' => 'Skip vehicle info',
            'skip_license' => 'Skip license',
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
        if ($this->existingData && $this->existingData->relationLoaded('step3PageSettingDetail')) {
            foreach ($this->existingData->step3PageSettingDetail as $d) {
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
