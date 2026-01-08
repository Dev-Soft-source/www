<?php

namespace App\Services;

use App\Models\ResetPasswordPageSettingDetail;

class ResetPasswordPageSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['name.name_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['name.name_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['meta_keywords.meta_keywords_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['meta_keywords.meta_keywords_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['meta_description.meta_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['meta_description.meta_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_label.main_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_label.main_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['password_label.password_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['password_label.password_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['password_error.password_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['password_error.password_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['confirm_password_label.confirm_password_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['confirm_password_label.confirm_password_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['confirm_password_error.confirm_password_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['confirm_password_error.confirm_password_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['button_label.button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['button_label.button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($resetPasswordPageSetting, $language, $request)
    {
        return [
            'reset_pass_page_id' => $resetPasswordPageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'main_label' => $this->data($request, $language, 'main_label'),
            'password_label' => $this->data($request, $language, 'password_label'),
            'password_error' => $this->data($request, $language, 'password_error'),
            'password_placeholder' => $this->data($request, $language, 'password_placeholder'),
            'confirm_password_label' => $this->data($request, $language, 'confirm_password_label'),
            'confirm_password_error' => $this->data($request, $language, 'confirm_password_error'),
            'confirm_password_placeholder' => $this->data($request, $language, 'confirm_password_placeholder'),
            'button_label' => $this->data($request, $language, 'button_label'),
        ];
    }

    public function update($resetPasswordPageSetting, $language, $request)
    {
        $fields = $this->fields($resetPasswordPageSetting, $language, $request);
        $resetPasswordPageSettingDetail = ResetPasswordPageSettingDetail::whereResetPassPageId($resetPasswordPageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$resetPasswordPageSettingDetail){
            $fields = $this->fields($resetPasswordPageSetting, $language, $request);
            ResetPasswordPageSettingDetail::create($fields);
        }
        else{
            ResetPasswordPageSettingDetail::whereResetPassPageId($resetPasswordPageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
