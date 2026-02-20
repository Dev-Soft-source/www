<?php

namespace App\Exports;

use App\Models\CloseAccountSetting;
use App\Models\Language;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;

class CloseAccountSettingTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /** @var \App\Models\CloseAccountSetting|null */
    protected $existingData;

    /**
     * @param string $format - 'single_column', 'multi_column', or 'all_languages'
     * @param \Illuminate\Support\Collection|array|null $languages
     * @param \App\Models\CloseAccountSetting|null $existingData - with closeAccountSettingDetail loaded
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
            'warning_text' => 'Please read the following carefully.',
            'mobile_indicate_required_field_label' => 'Required field',
            'main_heading' => 'Close My Account',
            'closing_account_label' => 'Closing account',
            'apply_reason_label' => 'Reason',
            'reason_label' => 'Reason',
            'not_say_checkbox_label' => 'Prefer not to say',
            'check_box_validation_message' => 'Please select at least one option',
            'customer_service_checkbox_label' => 'Customer service',
            'technical_issue_checkbox_label' => 'Technical issue',
            'dont_use_checkbox_label' => "Don't use anymore",
            'another_account_checkbox_label' => 'Another account',
            'did_not_get_booking_checkbox_label' => 'Did not get booking',
            'did_not_find_ride_checkbox_label' => 'Did not find ride',
            'did_not_find_destination_checkbox_label' => 'Did not find destination',
            'other_checkbox_label' => 'Other',
            'recommend_heading' => 'Would you recommend us?',
            'yes_checkbox_label' => 'Yes',
            'no_checkbox_label' => 'No',
            'prefer_not_checkbox_label' => 'Prefer not to say',
            'why_closing_account_label' => 'Why are you closing your account?',
            'why_closing_account_placeholder' => 'Tell us more...',
            'improve_label' => 'How could we improve?',
            'improve_placeholder' => 'Your feedback...',
            'close_my_account_checkbox' => 'I understand and wish to close my account',
            'close_my_account_checkbox_error' => 'You must confirm to proceed',
            'close_account_button_text' => 'Close Account',
            'difficulties_making_receiving_payments_label' => 'Difficulties with payments',
            'take_me_back_button_label' => 'Take me back',
            'close_it_button_label' => 'Close it',
            'close_account_sure_message_text' => 'Are you sure you want to close your account?',
            'web_irreversible_label' => 'This action is irreversible',
            'web_closing_account_reason_label' => 'Reason for closing account',
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
        $data = [];
        foreach ($fields as $field => $default) {
            $data[] = ['field_name' => $field, 'translation_value' => $default];
        }
        return new Collection($data);
    }

    protected function allLanguagesFormat()
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $fields = static::getTranslatableFieldsWithDefaults();
        $detailsByLang = [];
        if ($this->existingData && $this->existingData->relationLoaded('closeAccountSettingDetail')) {
            foreach ($this->existingData->closeAccountSettingDetail as $d) {
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
        return array_map(fn ($field) => ucwords(str_replace('_', ' ', $field)), array_keys(static::getTranslatableFieldsWithDefaults()));
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
            return ['A' => 45, 'B' => 80];
        }
        if ($this->format === 'all_languages') {
            $totalCols = ($this->languages ?? Language::orderBy('id')->get())->count() + 1;
            $widths = [];
            for ($colIndex = 1; $colIndex <= $totalCols; $colIndex++) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $widths[$col] = $colIndex === 1 ? 45 : 30;
            }
            return $widths;
        }
        return ['A' => 45, 'B' => 30];
    }
}
