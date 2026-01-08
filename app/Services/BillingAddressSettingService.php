<?php

namespace App\Services;

use App\Models\BillingAddressSettingDetail;

class BillingAddressSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['name_on_card_label.name_on_card_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['name_on_card_label.name_on_card_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_indicate_required_field_label.mobile_indicate_required_field_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_indicate_required_field_label.mobile_indicate_required_field_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['card_number_label.card_number_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['card_number_placeholder.card_number_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['mobile_card_type_placholder.mobile_card_type_placholder_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['mobile_card_type_placholder.mobile_card_type_placholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['web_expiry_month_label.web_expiry_month_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['web_expiry_month_label.web_expiry_month_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['security_code_label.security_code_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['security_code_label.security_code_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['security_code_palceholder.security_code_palceholder_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['security_code_palceholder.security_code_palceholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_billing_address_label.mobile_billing_address_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_billing_address_label.mobile_billing_address_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_street_name_label.mobile_street_name_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_street_name_label.mobile_street_name_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['mobile_street_name_placeholder.mobile_street_name_placeholder_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['mobile_street_name_placeholder.mobile_street_name_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_house_number_label.mobile_house_number_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_house_number_label.mobile_house_number_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['mobile_house_number_placeholder.mobile_house_number_placeholder_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['mobile_house_number_placeholder.mobile_house_number_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_city_label.mobile_city_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_city_label.mobile_city_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_province_label.mobile_province_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_province_label.mobile_province_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['mobile_card_type_label.mobile_card_type_label_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['mobile_card_type_label.mobile_card_type_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_expiry_date_label.mobile_expiry_date_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_expiry_date_label.mobile_expiry_date_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_country_label.mobile_country_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_country_label.mobile_country_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_postal_code_label.mobile_postal_code_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_postal_code_label.mobile_postal_code_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['mobile_primary_card_placeholder.mobile_primary_card_placeholder_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['mobile_primary_card_placeholder.mobile_primary_card_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['save_button_text.save_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['save_button_text.save_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['indicate_field_label.indicate_field_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['indicate_field_label.indicate_field_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                // $validationRule = array_merge($validationRule, ['expiry_month_placeholder.expiry_month_placeholder_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['expiry_month_placeholder.expiry_month_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                // $validationRule = array_merge($validationRule, ['card_number_placeholder.card_number_placeholder_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['card_number_placeholder.card_number_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                // $validationRule = array_merge($validationRule, ['cvc_placeholder.cvc_placeholder_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['cvc_placeholder.cvc_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                // $validationRule = array_merge($validationRule, ['card_name_placeholder.card_name_placeholder_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['card_name_placeholder.card_name_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
            // $validationRule = array_merge($validationRule, ['edit_card_button_text.edit_card_button_text_' . $language->id => ['required', 'string']]);
            // $errorMessages = array_merge($errorMessages, ['edit_card_button_text.edit_card_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($billingAddressSetting, $language, $request)
    {
        return [
            'billing_add_setting_id' => $billingAddressSetting->id,
            'language_id' => $language->id,
            'name_on_card_label' => $this->data($request, $language, 'name_on_card_label'),
            'mobile_indicate_required_field_label' => $this->data($request, $language, 'mobile_indicate_required_field_label'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'name_on_card_placeholder' => $this->data($request, $language, 'name_on_card_placeholder'),
            'card_number_label' => $this->data($request, $language, 'card_number_label'),
            'card_number_placeholder' => $this->data($request, $language, 'card_number_placeholder'),
            'mobile_card_type_label' => $this->data($request, $language, 'mobile_card_type_label'),
            'mobile_card_type_placholder' => $this->data($request, $language, 'mobile_card_type_placholder'),
            'mobile_expiry_date_label' => $this->data($request, $language, 'mobile_expiry_date_label'),
            'mobile_month_placeholder' => $this->data($request, $language, 'mobile_month_placeholder'),
            'mobile_year_placeholder' => $this->data($request, $language, 'mobile_year_placeholder'),
            'web_expiry_month_label' => $this->data($request, $language, 'web_expiry_month_label'),
            'web_expiry_month_placeholder' => $this->data($request, $language, 'web_expiry_month_placeholder'),
            'security_code_label' => $this->data($request, $language, 'security_code_label'),
            'security_code_palceholder' => $this->data($request, $language, 'security_code_palceholder'),
            'mobile_billing_address_label' => $this->data($request, $language, 'mobile_billing_address_label'),
            'mobile_street_name_label' => $this->data($request, $language, 'mobile_street_name_label'),
            'mobile_street_name_placeholder' => $this->data($request, $language, 'mobile_street_name_placeholder'),
            'mobile_house_number_label' => $this->data($request, $language, 'mobile_house_number_label'),
            'mobile_house_number_placeholder' => $this->data($request, $language, 'mobile_house_number_placeholder'),
            'mobile_city_label' => $this->data($request, $language, 'mobile_city_label'),
            'mobile_city_placeholder' => $this->data($request, $language, 'mobile_city_placeholder'),
            'mobile_province_label' => $this->data($request, $language, 'mobile_province_label'),
            'mobile_province_placeholder' => $this->data($request, $language, 'mobile_province_placeholder'),
            'mobile_country_label' => $this->data($request, $language, 'mobile_country_label'),
            'mobile_country_placeholder' => $this->data($request, $language, 'mobile_country_placeholder'),
            'mobile_postal_code_label' => $this->data($request, $language, 'mobile_postal_code_label'),
            'mobile_postal_code_placeholder' => $this->data($request, $language, 'mobile_postal_code_placeholder'),
            'mobile_primary_card_placeholder' => $this->data($request, $language, 'mobile_primary_card_placeholder'),
            'save_button_text' => $this->data($request, $language, 'save_button_text'),
            'indicate_field_label' => $this->data($request, $language, 'indicate_field_label'),
            'select_card_type_text' => $this->data($request, $language, 'select_card_type_text'),
            'expiry_month_placeholder' => $this->data($request, $language, 'expiry_month_placeholder'),
            'card_number_placeholder' => $this->data($request, $language, 'card_number_placeholder'),
            'cvc_placeholder' => $this->data($request, $language, 'cvc_placeholder'),
            'card_name_placeholder' => $this->data($request, $language, 'card_name_placeholder'),
            // 'edit_card_button_text' => $this->data($request, $language, 'edit_card_button_text'),
        ];
    }

    public function update($billingAddressSetting, $language, $request)
    {
        $fields = $this->fields($billingAddressSetting, $language, $request);
        $billingAddressSettingDetail = BillingAddressSettingDetail::whereBillingAddSettingId($billingAddressSetting->id)->whereLanguageId($language->id)->exists();
        if(!$billingAddressSettingDetail){
            $fields = $this->fields($billingAddressSetting, $language, $request);
            BillingAddressSettingDetail::create($fields);
        }
        else{
            BillingAddressSettingDetail::whereBillingAddSettingId($billingAddressSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
