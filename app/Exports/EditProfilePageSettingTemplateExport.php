<?php

namespace App\Exports;

use App\Models\EditProfilePageSetting;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class EditProfilePageSettingTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /** @var \App\Models\EditProfilePageSetting|null */
    protected $existingData;

    /**
     * @param string $format - 'single_column', 'multi_column', or 'all_languages'
     * @param \Illuminate\Support\Collection|array|null $languages
     * @param \App\Models\EditProfilePageSetting|null $existingData - with editProfilePageSettingDetail loaded
     */
    public function __construct($format = 'single_column', $languages = null, $existingData = null)
    {
        $this->format = $format;
        $this->languages = $languages ? collect($languages) : null;
        $this->existingData = $existingData;
    }

    public static function getTranslatableFieldsWithDefaults(): array
    {
        $fields = [
            'name', 'min_bio_label', 'passenger_driven_label', 'main_heading', 'rides_taken_label',
            'km_shared_label', 'review_label', 'reply_label', 'link_review_label', 'review_heading',
            'edit_profile_text', 'first_name_label', 'first_name_placeholder', 'last_name_label', 'last_name_placeholder',
            'gender_label', 'male_label', 'female_label', 'prefer_no_to_say_label', 'dob_label', 'dob_placeholder',
            'country_label', 'country_placeholder', 'state_label', 'state_placeholder', 'city_label', 'city_placeholder',
            'address_label', 'address_placeholder', 'zip_label', 'mini_bio_label', 'mini_bio_placeholder',
            'govt_id_label', 'govt_id_text', 'image_placeholder', 'choose_file_placeholder', 'image_option_placeholder',
            'new_image_button_text', 'save_button_text', 'joined_label', 'passenger_label', 'vehicle_info_label',
            'year_old_label', 'replied_label', 'response_label', 'reply_heading_label', 'reply_placeholder',
            'reply_submit_button_label', 'profile_label',
        ];
        return array_fill_keys($fields, '');
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
        $data = [];
        foreach ($fields as $field => $default) {
            $data[] = ['field_name' => $field, 'translation_value' => $default];
        }
        return new Collection($data);
    }

    protected function allLanguagesFormat(): Collection
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $fields = static::getTranslatableFieldsWithDefaults();
        $detailsByLang = [];
        if ($this->existingData && $this->existingData->relationLoaded('editProfilePageSettingDetail')) {
            foreach ($this->existingData->editProfilePageSettingDetail as $d) {
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

    protected function multiColumnFormat(): Collection
    {
        $fields = static::getTranslatableFieldsWithDefaults();
        return new Collection([$fields]);
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
        return array_map(fn ($f) => ucwords(str_replace('_', ' ', $f)), array_keys(static::getTranslatableFieldsWithDefaults()));
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
            return ['A' => 30, 'B' => 80];
        }
        if ($this->format === 'all_languages') {
            $totalCols = ($this->languages ?? Language::orderBy('id')->get())->count() + 1;
            $widths = [];
            for ($colIndex = 1; $colIndex <= $totalCols; $colIndex++) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $widths[$col] = $colIndex === 1 ? 30 : 25;
            }
            return $widths;
        }
        return ['A' => 30, 'B' => 25];
    }
}
