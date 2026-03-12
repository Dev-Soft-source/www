<?php

namespace App\Imports;

use App\Models\BillingAddressSetting;
use App\Models\BillingAddressSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BillingAddressSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When set, import is for a single language. When null, import is all_languages format. */
    protected $languageId;

    protected $errors = [];

    /**
     * @param int|null $languageId - When null, Excel is expected in all_languages format (Field Name + one column per language). When set, single-language format.
     */
    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $billingAddressSetting = BillingAddressSetting::first();
        if (!$billingAddressSetting) {
            $billingAddressSetting = BillingAddressSetting::create([]);
        }

        if ($rows->isEmpty()) {
            Log::warning('No rows found in Excel file');
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        // All-languages format: first column is "field_name" (or "field name") and rest are language columns
        $isAllLanguages = $this->languageId === null
            && (in_array('field_name', $keys) || in_array('field name', $keys))
            && count($keys) > 1;

        if ($isAllLanguages) {
            $this->processAllLanguagesFormat($billingAddressSetting, $rows);
            Log::info('Excel import (all languages) completed successfully');
            return;
        }

        // Single column format: field_name + value / translation_value
        $isSingleColumn = in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        if ($isSingleColumn && $this->languageId !== null) {
            foreach ($rows as $row) {
                $this->processSingleColumnFormat($billingAddressSetting, $row);
            }
        } else {
            // Multi-column format - one row of values, headers = field names
            if ($this->languageId !== null) {
                $this->processMultiColumnFormat($billingAddressSetting, $firstRow);
            }
        }

        Log::info('Excel import completed successfully');
    }

    /**
     * Process all_languages format: each row = one field, columns = Field Name, then one per language (by header name).
     */
    protected function processAllLanguagesFormat(BillingAddressSetting $billingAddressSetting, Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());

        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : 'field name';
        $languageColumns = array_diff($headers, [$fieldNameKey]);

        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(function ($lang) {
            return [Str::lower($lang->name) => $lang->id];
        })->toArray();

        $validFields = array_keys(\App\Exports\BillingAddressSettingTemplateExport::getTranslatableFieldsWithDefaults());

        foreach ($rows as $row) {
            $row = $row->toArray();
            $fieldName = $row[$fieldNameKey] ?? null;
            if (empty($fieldName) || !in_array($fieldName, $validFields, true)) {
                continue;
            }

            foreach ($languageColumns as $col) {
                $langKey = Str::lower(trim($col));
                if (!isset($nameToId[$langKey])) {
                    continue;
                }
                $languageId = $nameToId[$langKey];
                $value = $row[$col] ?? null;

                $detail = BillingAddressSettingDetail::firstOrCreate(
                    [
                        'billing_add_setting_id' => $billingAddressSetting->id,
                        'language_id' => $languageId,
                    ],
                    [$fieldName => $value]
                );

                if ($detail->wasRecentlyCreated === false) {
                    $detail->$fieldName = $value;
                    $detail->save();
                }
            }
        }
    }

    protected function processSingleColumnFormat($billingAddressSetting, $row)
    {
        $fieldName = $row['field_name'] ?? null;
        $value = $row['translation_value'] ?? $row['value'] ?? null;

        if (empty($fieldName)) {
            return;
        }

        $detail = BillingAddressSettingDetail::where('billing_add_setting_id', $billingAddressSetting->id)
            ->where('language_id', $this->languageId)
            ->first();

        if ($detail) {
            $detail->$fieldName = $value;
            $detail->save();
        } else {
            BillingAddressSettingDetail::create([
                'billing_add_setting_id' => $billingAddressSetting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]);
        }
    }

    protected function processMultiColumnFormat($billingAddressSetting, $row)
    {
        $fields = [
            'billing_add_setting_id' => $billingAddressSetting->id,
            'language_id' => $this->languageId,
            'main_heading' => $row['main_heading'] ?? null,
            'mobile_indicate_required_field_label' => $row['mobile_indicate_required_field_label'] ?? null,
            'indicate_field_label' => $row['indicate_field_label'] ?? null,
            'name_on_card_label' => $row['name_on_card_label'] ?? null,
            'name_on_card_placeholder' => $row['name_on_card_placeholder'] ?? null,
            'card_name_placeholder' => $row['card_name_placeholder'] ?? null,
            'card_number_label' => $row['card_number_label'] ?? null,
            'card_number_placeholder' => $row['card_number_placeholder'] ?? null,
            'mobile_card_type_label' => $row['mobile_card_type_label'] ?? null,
            'mobile_card_type_placholder' => $row['mobile_card_type_placholder'] ?? null,
            'select_card_type_text' => $row['select_card_type_text'] ?? null,
            'mobile_expiry_date_label' => $row['mobile_expiry_date_label'] ?? null,
            'mobile_month_placeholder' => $row['mobile_month_placeholder'] ?? null,
            'mobile_year_placeholder' => $row['mobile_year_placeholder'] ?? null,
            'web_expiry_month_label' => $row['web_expiry_month_label'] ?? null,
            'web_expiry_month_placeholder' => $row['web_expiry_month_placeholder'] ?? null,
            'expiry_month_placeholder' => $row['expiry_month_placeholder'] ?? null,
            'security_code_label' => $row['security_code_label'] ?? null,
            'security_code_palceholder' => $row['security_code_palceholder'] ?? null,
            'cvc_placeholder' => $row['cvc_placeholder'] ?? null,
            'mobile_billing_address_label' => $row['mobile_billing_address_label'] ?? null,
            'mobile_street_name_label' => $row['mobile_street_name_label'] ?? null,
            'mobile_street_name_placeholder' => $row['mobile_street_name_placeholder'] ?? null,
            'mobile_house_number_label' => $row['mobile_house_number_label'] ?? null,
            'mobile_house_number_placeholder' => $row['mobile_house_number_placeholder'] ?? null,
            'mobile_city_label' => $row['mobile_city_label'] ?? null,
            'mobile_city_placeholder' => $row['mobile_city_placeholder'] ?? null,
            'mobile_province_label' => $row['mobile_province_label'] ?? null,
            'mobile_province_placeholder' => $row['mobile_province_placeholder'] ?? null,
            'mobile_country_label' => $row['mobile_country_label'] ?? null,
            'mobile_country_placeholder' => $row['mobile_country_placeholder'] ?? null,
            'mobile_postal_code_label' => $row['mobile_postal_code_label'] ?? null,
            'mobile_postal_code_placeholder' => $row['mobile_postal_code_placeholder'] ?? null,
            'delete_card_button_text' => $row['delete_card_button_text'] ?? null,
            'mobile_default_card_tab' => $row['mobile_default_card_tab'] ?? null,
            'set_primary_card_label' => $row['set_primary_card_label'] ?? null,
            'delete_card_message' => $row['delete_card_message'] ?? null,
            'mobile_primary_card_placeholder' => $row['mobile_primary_card_placeholder'] ?? null,
            'save_button_text' => $row['save_button_text'] ?? null,
            'buy_btn_text' => $row['buy_btn_text'] ?? null,
            'top_up_my_balance_head' => $row['top_up_my_balance_head'] ?? null,
            'purchase_amount_label' => $row['purchase_amount_label'] ?? null,
            'purchase_amount_placeholder' => $row['purchase_amount_placeholder'] ?? null,
        ];

        BillingAddressSettingDetail::updateOrCreate(
            [
                'billing_add_setting_id' => $billingAddressSetting->id,
                'language_id' => $this->languageId,
            ],
            $fields
        );
    }

    public function rules(): array
    {
        if ($this->languageId === null) {
            return [];
        }
        $language = Language::find($this->languageId);
        if (!$language) {
            return [];
        }

        $rules = [];
        if ($language->is_default == '1') {
            $rules = [
                'name_on_card_label' => 'required|string',
                'mobile_indicate_required_field_label' => 'required|string',
                'main_heading' => 'required|string',
                'card_number_label' => 'required|string',
                'web_expiry_month_label' => 'required|string',
                'security_code_label' => 'required|string',
                'mobile_billing_address_label' => 'required|string',
                'mobile_street_name_label' => 'required|string',
                'mobile_house_number_label' => 'required|string',
                'mobile_city_label' => 'required|string',
                'mobile_province_label' => 'required|string',
                'mobile_expiry_date_label' => 'required|string',
                'mobile_country_label' => 'required|string',
                'mobile_postal_code_label' => 'required|string',
                'save_button_text' => 'required|string',
                'buy_btn_text' => 'required|string',
                'top_up_my_balance_head' => 'required|string',
                'purchase_amount_label' => 'required|string',
                'purchase_amount_placeholder' => 'required|string',
                'indicate_field_label' => 'required|string',
            ];
        }
        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'name_on_card_label.required' => 'Name on Card Label is required',
            'mobile_indicate_required_field_label.required' => 'Indicate Required Field Label is required',
            'main_heading.required' => 'Main Heading is required',
            'card_number_label.required' => 'Card Number Label is required',
            'web_expiry_month_label.required' => 'Expiry Month Label is required',
            'security_code_label.required' => 'Security Code Label is required',
            'mobile_billing_address_label.required' => 'Billing Address Label is required',
            'mobile_street_name_label.required' => 'Street Name Label is required',
            'mobile_house_number_label.required' => 'House Number Label is required',
            'mobile_city_label.required' => 'City Label is required',
            'mobile_province_label.required' => 'Province Label is required',
            'mobile_expiry_date_label.required' => 'Expiry Date Label is required',
            'mobile_country_label.required' => 'Country Label is required',
            'mobile_postal_code_label.required' => 'Postal Code Label is required',
            'save_button_text.required' => 'Save Button Text is required',
            'buy_btn_text.required' => 'Buy Button Text is required',
            'top_up_my_balance_head.required' => 'Top Up My Balance Heading is required',
            'purchase_amount_label.required' => 'Purchase Amount Label is required',
            'purchase_amount_placeholder.required' => 'Purchase Amount Placeholder is required',
            'indicate_field_label.required' => 'Indicate Field Label is required',
        ];
    }
}
