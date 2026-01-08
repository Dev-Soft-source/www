<?php

namespace App\Services;

use App\Models\MobileLoginSettingDetail;

class MobileLoginSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['email_label.email_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['email_label.email_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['password_label.password_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['password_label.password_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['submit_button_label.submit_button_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['submit_button_label.submit_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['forgot_password_label.forgot_password_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['forgot_password_label.forgot_password_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['or_label.or_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['or_label.or_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['signup_label.signup_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['signup_label.signup_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($loginPageSetting, $language, $request)
    {
        return [
            'mobile_login_setting_id' => $loginPageSetting->id,
            'language_id' => $language->id,
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'email_label' => $this->data($request, $language, 'email_label'),
            'email_placeholder' => $this->data($request, $language, 'email_placeholder'),
            'password_label' => $this->data($request, $language, 'password_label'),
            'password_placeholder' => $this->data($request, $language, 'password_placeholder'),
            'submit_button_label' => $this->data($request, $language, 'submit_button_label'),
            'forgot_password_label' => $this->data($request, $language, 'forgot_password_label'),
            'or_label' => $this->data($request, $language, 'or_label'),
            'signup_label' => $this->data($request, $language, 'signup_label'),
        ];
    }

    public function update($loginPageSetting, $language, $request)
    {
        $fields = $this->fields($loginPageSetting, $language, $request);
        $loginPageSettingDetail = MobileLoginSettingDetail::whereMobileLoginSettingId($loginPageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$loginPageSettingDetail){
            $fields = $this->fields($loginPageSetting, $language, $request);
            MobileLoginSettingDetail::create($fields);
        }
        else{
            MobileLoginSettingDetail::whereMobileLoginSettingId($loginPageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
