<?php

namespace App\Services;

use App\Models\MobileSignupSettingDetail;

class MobileSignupSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['or_label.or_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['or_label.or_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['first_name_label.first_name_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['first_name_label.first_name_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['last_name_label.last_name_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['last_name_label.last_name_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['email_label.email_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['email_label.email_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['password_label.password_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['password_label.password_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['confirm_password_label.confirm_password_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['confirm_password_label.confirm_password_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['agree_terms_label.agree_terms_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['agree_terms_label.agree_terms_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['button_label.button_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['button_label.button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['signin_label.signin_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['signin_label.signin_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($signupPageSetting, $language, $request)
    {
        return [
            'mobile_signup_setting_id' => $signupPageSetting->id,
            'language_id' => $language->id,
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'or_label' => $this->data($request, $language, 'or_label'),
            'first_name_label' => $this->data($request, $language, 'first_name_label'),
            'first_name_placeholder' => $this->data($request, $language, 'first_name_placeholder'),
            'last_name_label' => $this->data($request, $language, 'last_name_label'),
            'last_name_placeholder' => $this->data($request, $language, 'last_name_placeholder'),
            'email_label' => $this->data($request, $language, 'email_label'),
            'email_placeholder' => $this->data($request, $language, 'email_placeholder'),
            'password_label' => $this->data($request, $language, 'password_label'),
            'password_placeholder' => $this->data($request, $language, 'password_placeholder'),
            'confirm_password_label' => $this->data($request, $language, 'confirm_password_label'),
            'confirm_password_placeholder' => $this->data($request, $language, 'confirm_password_placeholder'),
            'agree_terms_label' => $this->data($request, $language, 'agree_terms_label'),
            'button_label' => $this->data($request, $language, 'button_label'),
            'signin_label' => $this->data($request, $language, 'signin_label'),
        ];
    }

    public function update($signupPageSetting, $language, $request)
    {
        $fields = $this->fields($signupPageSetting, $language, $request);
        $signupPageSettingDetail = MobileSignupSettingDetail::whereMobileSignupSettingId($signupPageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$signupPageSettingDetail){
            $fields = $this->fields($signupPageSetting, $language, $request);
            MobileSignupSettingDetail::create($fields);
        }
        else{
            MobileSignupSettingDetail::whereMobileSignupSettingId($signupPageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
