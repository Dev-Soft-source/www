<?php

namespace App\Services;

use App\Models\MyEmailSettingDetail;

class MyEmailSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['email_description_text.email_description_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['email_description_text.email_description_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['email_label.email_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['email_label.email_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['update_button_text.update_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['update_button_text.update_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($myEmailSetting, $language, $request)
    {
        return [
            'email_address_setting_id' => $myEmailSetting->id,
            'language_id' => $language->id,
            'email_description_text' => $this->data($request, $language, 'email_description_text'),
            'email_label' => $this->data($request, $language, 'email_label'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            // 'email_placeholder' => $this->data($request, $language, 'email_placeholder'),
            'update_button_text' => $this->data($request, $language, 'update_button_text'),
            'save_btn_label' => $this->data($request, $language, 'save_btn_label'),
            'confirm_email_label' => $this->data($request, $language, 'confirm_email_label'),
            'new_email_label' => $this->data($request, $language, 'new_email_label'),
            'current_email_label' => $this->data($request, $language, 'current_email_label'),

        ];
    }

    public function update($myEmailSetting, $language, $request)
    {
        $fields = $this->fields($myEmailSetting, $language, $request);
        $myEmailSettingDetail = MyEmailSettingDetail::whereEmailAddressSettingId($myEmailSetting->id)->whereLanguageId($language->id)->exists();
        if(!$myEmailSettingDetail){
            $fields = $this->fields($myEmailSetting, $language, $request);
            MyEmailSettingDetail::create($fields);
        }
        else{
            MyEmailSettingDetail::whereEmailAddressSettingId($myEmailSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
