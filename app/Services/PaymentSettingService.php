<?php

namespace App\Services;

use App\Models\PaymentSettingDetail;

class PaymentSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['mobile_default_card_tab.mobile_default_card_tab_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_default_card_tab.mobile_default_card_tab_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_card_name_label.mobile_card_name_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_card_name_label.mobile_card_name_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_card_number_label.mobile_card_number_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_card_number_label.mobile_card_number_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_expiry_date_label.mobile_expiry_date_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_expiry_date_label.mobile_expiry_date_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['delete_card_button_text.delete_card_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['delete_card_button_text.delete_card_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['add_new_card_button_text.add_new_card_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['add_new_card_button_text.add_new_card_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['set_primary_card_label.set_primary_card_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['set_primary_card_label.set_primary_card_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_payment_message.no_payment_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_payment_message.no_payment_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($paymentSetting, $language, $request)
    {
        return [
            'payment_opt_setting_id' => $paymentSetting->id,
            'language_id' => $language->id,
            'mobile_default_card_tab' => $this->data($request, $language, 'mobile_default_card_tab'),
            'mobile_card_name_label' => $this->data($request, $language, 'mobile_card_name_label'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'mobile_card_number_label' => $this->data($request, $language, 'mobile_card_number_label'),
            'mobile_expiry_date_label' => $this->data($request, $language, 'mobile_expiry_date_label'),
            'delete_card_button_text' => $this->data($request, $language, 'delete_card_button_text'),
            'add_new_card_button_text' => $this->data($request, $language, 'add_new_card_button_text'),
            'no_payment_message' => $this->data($request, $language, 'no_payment_message'),
            'set_primary_card_label' => $this->data($request, $language, 'set_primary_card_label'),
            'select_card_type_text' => $this->data($request, $language, 'select_card_type_text'),
        ];
    }

    public function update($paymentSetting, $language, $request)
    {
        $fields = $this->fields( $paymentSetting, $language, $request);
        $paymentSettingDetail = PaymentSettingDetail::wherePaymentOptSettingId($paymentSetting->id)->whereLanguageId($language->id)->exists();
        if(!$paymentSettingDetail){
            $fields = $this->fields($paymentSetting, $language, $request);
            PaymentSettingDetail::create($fields);
        }
        else{
            PaymentSettingDetail::wherePaymentOptSettingId($paymentSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
