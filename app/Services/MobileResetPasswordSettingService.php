<?php

namespace App\Services;

use App\Models\MobileResetPasswordSettingDetail;

class MobileResetPasswordSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['main_label.main_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['main_label.main_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['password_label.password_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['password_label.password_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['confirm_password_label.confirm_password_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['confirm_password_label.confirm_password_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['button_label.button_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['button_label.button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($resetPasswordPageSetting, $language, $request)
    {
        return [
            'reset_page_id' => $resetPasswordPageSetting->id,
            'language_id' => $language->id,
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'main_label' => $this->data($request, $language, 'main_label'),
            'password_label' => $this->data($request, $language, 'password_label'),
            'password_placeholder' => $this->data($request, $language, 'password_placeholder'),
            'confirm_password_label' => $this->data($request, $language, 'confirm_password_label'),
            'confirm_password_placeholder' => $this->data($request, $language, 'confirm_password_placeholder'),
            'button_label' => $this->data($request, $language, 'button_label'),
        ];
    }

    public function update($resetPasswordPageSetting, $language, $request)
    {
        $fields = $this->fields($resetPasswordPageSetting, $language, $request);
        $resetPasswordPageSettingDetail = MobileResetPasswordSettingDetail::whereResetPageId($resetPasswordPageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$resetPasswordPageSettingDetail){
            $fields = $this->fields($resetPasswordPageSetting, $language, $request);
            MobileResetPasswordSettingDetail::create($fields);
        }
        else{
            MobileResetPasswordSettingDetail::whereResetPageId($resetPasswordPageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
