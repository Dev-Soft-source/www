<?php

namespace App\Imports;

use App\Models\BillingAddressSetting;
use App\Models\BillingAddressSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class BillingAddressSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;
    protected $errors = [];

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        
        // Get or create billing address setting
        $billingAddressSetting = BillingAddressSetting::first();
        if (!$billingAddressSetting) {
            $billingAddressSetting = BillingAddressSetting::create([]);
        }

        // Check format by looking at first row keys
        if ($rows->isEmpty()) {
            Log::warning('No rows found in Excel file');
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        // Single column format check: has 'field_name' and ('value' OR 'translation_value')
        $isSingleColumn = isset($keys[0]) && 
                          (in_array('field_name', $keys) && 
                          (in_array('value', $keys) || in_array('translation_value', $keys)));

        if ($isSingleColumn) {
            foreach ($rows as $index => $row) {
                $this->processSingleColumnFormat($billingAddressSetting, $row);
            }
        } else {
            Log::info('Detected MULTI COLUMN format');
            // Multi-column format - only process first data row
            $this->processMultiColumnFormat($billingAddressSetting, $firstRow);
        }
        
        Log::info('Excel import completed successfully');
    }

    protected function processSingleColumnFormat($billingAddressSetting, $row)
    {
        // Get field name
        $fieldName = $row['field_name'] ?? null;
        
        // Get value - check both 'value' and 'translation_value' (Laravel Excel converts "Translation Value" to "translation_value")
        $value = $row['translation_value'] ?? $row['value'] ?? null;

        // Skip if field name or value is empty
        if (empty($fieldName) || empty($value)) {
            Log::warning("Skipping row - Field: {$fieldName}, Value: {$value}");
            return;
        }


        // Get existing record or prepare for creation
        $detail = BillingAddressSettingDetail::where('billing_add_setting_id', $billingAddressSetting->id)
            ->where('language_id', $this->languageId)
            ->first();

        if ($detail) {
            // Update existing record - update only this specific field
            $detail->$fieldName = $value;
            $detail->save();
            Log::info("Updated existing record - Field: {$fieldName}");
        } else {
            // Create new record with this field
            BillingAddressSettingDetail::create([
                'billing_add_setting_id' => $billingAddressSetting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]);
            Log::info("Created new record with field: {$fieldName}");
        }
    }

    protected function processMultiColumnFormat($billingAddressSetting, $row)
    {
        // Map all fields (exact order matching export template)
        $fields = [
            'billing_add_setting_id' => $billingAddressSetting->id,
            'language_id' => $this->languageId,
            
            // Main heading
            'main_heading' => $row['main_heading'] ?? null,
            
            // Required field indicators
            'mobile_indicate_required_field_label' => $row['mobile_indicate_required_field_label'] ?? null,
            'indicate_field_label' => $row['indicate_field_label'] ?? null,
            
            // Card name fields
            'name_on_card_label' => $row['name_on_card_label'] ?? null,
            'name_on_card_placeholder' => $row['name_on_card_placeholder'] ?? null,
            'card_name_placeholder' => $row['card_name_placeholder'] ?? null,
            
            // Card number fields
            'card_number_label' => $row['card_number_label'] ?? null,
            'card_number_placeholder' => $row['card_number_placeholder'] ?? null,
            
            // Card type fields
            'mobile_card_type_label' => $row['mobile_card_type_label'] ?? null,
            'mobile_card_type_placholder' => $row['mobile_card_type_placholder'] ?? null,
            'select_card_type_text' => $row['select_card_type_text'] ?? null,
            
            // Expiry date fields (Mobile)
            'mobile_expiry_date_label' => $row['mobile_expiry_date_label'] ?? null,
            'mobile_month_placeholder' => $row['mobile_month_placeholder'] ?? null,
            'mobile_year_placeholder' => $row['mobile_year_placeholder'] ?? null,
            
            // Expiry date fields (Web)
            'web_expiry_month_label' => $row['web_expiry_month_label'] ?? null,
            'web_expiry_month_placeholder' => $row['web_expiry_month_placeholder'] ?? null,
            'expiry_month_placeholder' => $row['expiry_month_placeholder'] ?? null,
            
            // Security code (CVV/CVC)
            'security_code_label' => $row['security_code_label'] ?? null,
            'security_code_palceholder' => $row['security_code_palceholder'] ?? null,
            'cvc_placeholder' => $row['cvc_placeholder'] ?? null,
            
            // Billing address section
            'mobile_billing_address_label' => $row['mobile_billing_address_label'] ?? null,
            
            // Street address fields
            'mobile_street_name_label' => $row['mobile_street_name_label'] ?? null,
            'mobile_street_name_placeholder' => $row['mobile_street_name_placeholder'] ?? null,
            
            // House/Apartment number
            'mobile_house_number_label' => $row['mobile_house_number_label'] ?? null,
            'mobile_house_number_placeholder' => $row['mobile_house_number_placeholder'] ?? null,
            
            // City fields
            'mobile_city_label' => $row['mobile_city_label'] ?? null,
            'mobile_city_placeholder' => $row['mobile_city_placeholder'] ?? null,
            
            // Province/State fields
            'mobile_province_label' => $row['mobile_province_label'] ?? null,
            'mobile_province_placeholder' => $row['mobile_province_placeholder'] ?? null,
            
            // Country fields
            'mobile_country_label' => $row['mobile_country_label'] ?? null,
            'mobile_country_placeholder' => $row['mobile_country_placeholder'] ?? null,
            
            // Postal code fields
            'mobile_postal_code_label' => $row['mobile_postal_code_label'] ?? null,
            'mobile_postal_code_placeholder' => $row['mobile_postal_code_placeholder'] ?? null,
            
            // Primary card option
            'mobile_primary_card_placeholder' => $row['mobile_primary_card_placeholder'] ?? null,
            
            // Save button
            'save_button_text' => $row['save_button_text'] ?? null,
        ];

        // Update or create the billing address setting detail
        BillingAddressSettingDetail::updateOrCreate(
            [
                'billing_add_setting_id' => $billingAddressSetting->id,
                'language_id' => $this->languageId,
            ],
            $fields
        );
    }

    /**
     * Validation rules for Excel data
     */
    public function rules(): array
    {
        $language = Language::find($this->languageId);
        
        if (!$language) {
            return [];
        }

        // Required fields based on validation service
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
                'indicate_field_label' => 'required|string',
            ];
        }

        return $rules;
    }

    /**
     * Custom validation messages
     */
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
            'indicate_field_label.required' => 'Indicate Field Label is required',
        ];
    }
}

