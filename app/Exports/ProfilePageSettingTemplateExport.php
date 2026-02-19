<?php

namespace App\Exports;

use App\Models\Language;
use App\Models\ProfilePageSetting;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProfilePageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnWidths
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /** @var \App\Models\ProfilePageSetting|null */
    protected $existingData;

    /**
     * @param string $format - 'single_column', 'multi_column', or 'all_languages'
     * @param \Illuminate\Support\Collection|array|null $languages - For all_languages format
     * @param \App\Models\ProfilePageSetting|null $existingData - For all_languages (with profilePageSettingDetail loaded)
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
            'name' => 'Profile',
            'profile_setting_label' => 'Profile Settings',
            'my_wallet_label' => 'My Wallet',
            'main_heading' => 'Profile',
            'payment_options_label' => 'Payment Options',
            'payout_options_label' => 'Payout Options',
            'my_reviews_label' => 'My Reviews',
            'terms_condition_label' => 'Terms & Conditions',
            'privacy_policy_label' => 'Privacy Policy',
            'terms_of_use_label' => 'Terms of Use',
            'refund_policy_label' => 'Refund Policy',
            'cancellation_policy_label' => 'Cancellation Policy',
            'dispute_policy_label' => 'Dispute Policy',
            'contact_proximaride_label' => 'Contact ProximaRide',
            'logout_label' => 'Logout',
            'colse_your_contact_label' => 'Close Your Account',
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
        if ($this->existingData && $this->existingData->relationLoaded('profilePageSettingDetail')) {
            foreach ($this->existingData->profilePageSettingDetail as $d) {
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


