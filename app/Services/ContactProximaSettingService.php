<?php

namespace App\Services;

use App\Models\ContactProximaRideSettingDetail;

class ContactProximaSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['your_full_name_label.your_full_name_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['your_full_name_label.your_full_name_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_indicate_required_field_label.mobile_indicate_required_field_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_indicate_required_field_label.mobile_indicate_required_field_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['your_phone_label.your_phone_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['your_phone_label.your_phone_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['submit_button_text.submit_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['submit_button_text.submit_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($contactUsSetting, $language, $request)
    {
        return [
            'contact_pr_setting_id' => $contactUsSetting->id,
            'language_id' => $language->id,
            'your_full_name_label' => $this->data($request, $language, 'your_full_name_label'),
            'mobile_indicate_required_field_label' => $this->data($request, $language, 'mobile_indicate_required_field_label'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            // 'your_full_name_placeholder' => $this->data($request, $language, 'your_full_name_placeholder'),
            'your_phone_label' => $this->data($request, $language, 'your_phone_label'),
            // 'your_phone_placeholder' => $this->data($request, $language, 'your_phone_placeholder'),
            'your_email_address_label' => $this->data($request, $language, 'your_email_address_label'),
            // 'your_email_address_placeholder' => $this->data($request, $language, 'your_email_address_placeholder'),
            'your_message_label' => $this->data($request, $language, 'your_message_label'),
            'submit_button_text' => $this->data($request, $language, 'submit_button_text'),
        ];
    }

    public function update($contactUsSetting, $language, $request)
    {
        $fields = $this->fields($contactUsSetting, $language, $request);
        $contactUsSettingDetail = ContactProximaRideSettingDetail::whereContactPrSettingId($contactUsSetting->id)->whereLanguageId($language->id)->exists();
        if(!$contactUsSettingDetail){
            $fields = $this->fields($contactUsSetting, $language, $request);
            ContactProximaRideSettingDetail::create($fields);
        }
        else{
            ContactProximaRideSettingDetail::whereContactPrSettingId($contactUsSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
