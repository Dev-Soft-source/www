<?php

namespace App\Exports;

use App\Models\Language;
use App\Models\ProfilePhotoSetting;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProfilePhotoSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnWidths
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /** @var \App\Models\ProfilePhotoSetting|null */
    protected $existingData;

    /**
     * @param string $format - 'single_column', 'multi_column', or 'all_languages'
     * @param \Illuminate\Support\Collection|array|null $languages - For all_languages format
     * @param \App\Models\ProfilePhotoSetting|null $existingData - For all_languages (with profilePhotoSettingDetail loaded)
     */
    public function __construct($format = 'single_column', $languages = null, $existingData = null)
    {
        $this->format = $format;
        $this->languages = $languages ? collect($languages) : null;
        $this->existingData = $existingData;
    }

    /**
     * Canonical list of translatable fields with default (English) sample values
     */
    public static function getTranslatableFieldsWithDefaults(): array
    {
        return [
            'name' => 'Profile Photo',
            'mobile_upload_photo_tooltip' => 'Upload your profile photo',
            'mobile_upload_new_image_button_text' => 'Upload New Image',
            'main_heading' => 'Profile Photo',
            'save_button_text' => 'Save',
            'upload_profile_photo_placeholder' => 'Upload your photo',
            'choose_file_placeholder' => 'Choose file',
            'images_option_placeholder' => 'Select image',
            'photo_error' => 'Invalid photo',
            'mobile_indicate_required_field_label' => '* Indicates required fields',
            'sub_heading_text' => 'Update your profile photo',
        ];
    }

    public function collection(): Collection
    {
        $fields = array_keys(static::getTranslatableFieldsWithDefaults());
        if ($this->format === 'single_column') {
            $defaults = static::getTranslatableFieldsWithDefaults();
            $rows = [];
            foreach ($fields as $field) {
                $rows[] = ['field_name' => $field, 'translation_value' => $defaults[$field] ?? ''];
            }
            return new Collection($rows);
        }
        if ($this->format === 'all_languages') {
            return $this->allLanguagesFormat();
        }
        $row = array_fill_keys($fields, '');
        return new Collection([$row]);
    }

    /**
     * All-languages format: one row per field, first column = field name, then one column per language
     */
    protected function allLanguagesFormat(): Collection
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $fieldsWithDefaults = static::getTranslatableFieldsWithDefaults();
        $detailsByLang = [];
        if ($this->existingData && $this->existingData->relationLoaded('profilePhotoSettingDetail')) {
            foreach ($this->existingData->profilePhotoSettingDetail as $d) {
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
        $fields = array_keys(static::getTranslatableFieldsWithDefaults());
        $widths = [];
        foreach (range(1, count($fields)) as $i) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $widths[$col] = 20;
        }
        return $widths;
    }
}


