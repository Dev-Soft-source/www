<?php

namespace App\Services;

use App\Models\PasswordSettingDetail;

class PasswordSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['password_description_text.password_description_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['password_description_text.password_description_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_indicate_required_field_label.mobile_indicate_required_field_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_indicate_required_field_label.mobile_indicate_required_field_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['current_password_label.current_password_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['current_password_label.current_password_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['new_password_label.new_password_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['new_password_label.new_password_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['confirm_new_password_label.confirm_new_password_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['confirm_new_password_label.confirm_new_password_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['update_button_text.update_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['update_button_text.update_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($passwordSetting, $language, $request)
    {
        return [
            'password_setting_id' => $passwordSetting->id,
            'language_id' => $language->id,
            'password_description_text' => $this->data($request, $language, 'password_description_text'),
            'mobile_indicate_required_field_label' => $this->data($request, $language, 'mobile_indicate_required_field_label'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'current_password_label' => $this->data($request, $language, 'current_password_label'),
            'current_password_placeholder' => $this->data($request, $language, 'current_password_placeholder'),
            'current_password_error' => $this->data($request, $language, 'current_password_error'),
            'new_password_label' => $this->data($request, $language, 'new_password_label'),
            'new_password_placeholder' => $this->data($request, $language, 'new_password_placeholder'),
            'new_password_error' => $this->data($request, $language, 'new_password_error'),
            'confirm_new_password_label' => $this->data($request, $language, 'confirm_new_password_label'),
            'confirm_new_password_placeholder' => $this->data($request, $language, 'confirm_new_password_placeholder'),
            'confirm_new_password_error' => $this->data($request, $language, 'confirm_new_password_error'),
            'update_button_text' => $this->data($request, $language, 'update_button_text'),
            'mobile_password_description_text' => $this->data($request, $language, 'mobile_password_description_text'),

        ];
    }

    public function update($passwordSetting, $language, $request)
    {
        $fields = $this->fields($passwordSetting, $language, $request);
        $passwordSettingDetail = PasswordSettingDetail::wherePasswordSettingId($passwordSetting->id)->whereLanguageId($language->id)->exists();
        if(!$passwordSettingDetail){
            $fields = $this->fields($passwordSetting, $language, $request);
            PasswordSettingDetail::create($fields);
        }
        else{
            PasswordSettingDetail::wherePasswordSettingId($passwordSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
