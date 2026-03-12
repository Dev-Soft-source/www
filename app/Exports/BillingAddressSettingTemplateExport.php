<?php

namespace App\Exports;

use App\Models\BillingAddressSetting;
use App\Models\Language;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;

class BillingAddressSettingTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /** @var \App\Models\BillingAddressSetting|null */
    protected $existingData;

    /**
     * Constructor to set template format and optional data for all_languages
     * @param string $format - 'single_column', 'multi_column', or 'all_languages'
     * @param \Illuminate\Support\Collection|array|null $languages - For all_languages format
     * @param \App\Models\BillingAddressSetting|null $existingData - For all_languages format (with billingAddressSettingDetail loaded)
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
            'main_heading' => 'Add a New Card',
            'mobile_indicate_required_field_label' => '* Indicates required fields',
            'indicate_field_label' => '* Indicates required fields',
            'name_on_card_label' => "Cardholder's Name",
            'name_on_card_placeholder' => "Cardholder's Name",
            'card_name_placeholder' => "Cardholder's Name",
            'card_number_label' => 'Card Number',
            'card_number_placeholder' => 'Card Number',
            'mobile_card_type_label' => 'Card Type',
            'mobile_card_type_placholder' => 'Select',
            'select_card_type_text' => 'Select',
            'mobile_expiry_date_label' => 'Expiry Date',
            'mobile_month_placeholder' => 'Month',
            'mobile_year_placeholder' => 'Year',
            'web_expiry_month_label' => 'Expiry Month',
            'web_expiry_month_placeholder' => 'MM/YY',
            'expiry_month_placeholder' => 'MM/YY',
            'security_code_label' => 'CVV / CVC',
            'security_code_palceholder' => 'A 3- or 4-digit number printed on your card (usually on the back).',
            'cvc_placeholder' => 'A 3- or 4-digit number printed on your card (usually on the back).',
            'mobile_billing_address_label' => 'Billing Address',
            'mobile_street_name_label' => 'Street Address (number and name)',
            'mobile_street_name_placeholder' => '(including apartment or unit number if applicable:)',
            'mobile_house_number_label' => 'Apartment / Suite / Unit Number (optional)',
            'mobile_house_number_placeholder' => 'Apartment / Suite / Unit Number',
            'mobile_city_label' => 'City',
            'mobile_city_placeholder' => 'City',
            'mobile_province_label' => 'Province',
            'mobile_province_placeholder' => 'Province',
            'mobile_country_label' => 'Country',
            'mobile_country_placeholder' => 'Country',
            'mobile_postal_code_label' => 'Postal Code / ZIP Code',
            'mobile_postal_code_placeholder' => 'Postal Code / ZIP Code',
            'mobile_primary_card_placeholder' => 'Set as Primary Card',
            'save_button_text' => 'Add Card',
            'buy_btn_text' => 'Buy',
            'top_up_my_balance_head' => 'Top up my balance',
            'purchase_amount_label' => 'Purchase amount',
            'purchase_amount_placeholder' => 'Enter the amount you want to add',
            'delete_card_button_text' => 'Delete Card',
            'mobile_default_card_tab' => 'Primary Card',
            'set_primary_card_label' => 'Set as Primary Card',
            'delete_card_message' => 'Are you sure you want to delete this card?',
        ];
    }

    /**
     * @return Collection
     */
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

    /**
     * Single column format with field names and sample values
     */
    protected function singleColumnFormat()
    {
        $fields = static::getTranslatableFieldsWithDefaults();
        return collect($fields)->map(fn ($value, $key) => [$key, $value])->values();
    }

    /**
     * All-languages format: one row per field, first column = field name, then one column per language with values
     */
    protected function allLanguagesFormat()
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $fields = static::getTranslatableFieldsWithDefaults();
        $detailsByLang = [];
        if ($this->existingData && $this->existingData->relationLoaded('billingAddressSettingDetail')) {
            foreach ($this->existingData->billingAddressSettingDetail as $d) {
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

    /**
     * Multi-column format with all fields in header and one row for values
     */
    protected function multiColumnFormat()
    {
        $values = array_values(static::getTranslatableFieldsWithDefaults());
        return collect([$values]);
    }

    /**
     * Headings for the Excel file
     */
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

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Set column widths
     */
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
        $fields = static::getTranslatableFieldsWithDefaults();
        $widths = [];
        foreach (range(1, count($fields)) as $i) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $widths[$col] = 20;
        }
        return $widths;
    }
}
