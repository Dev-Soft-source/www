<?php

namespace App\Services;

use App\Models\ForgotPasswordPageSettingDetail;

class ForgotPasswordPageSettingService
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
                $validationRule = array_merge($validationRule, ['email_error.email_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['email_error.email_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['button_label.button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['button_label.button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($forgotPasswordPageSetting, $language, $request)
    {
        return [
            'forgot_pass_page_id' => $forgotPasswordPageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'main_label' => $this->data($request, $language, 'main_label'),
            'email_error' => $this->data($request, $language, 'email_error'),
            'button_label' => $this->data($request, $language, 'button_label'),
        ];
    }

    public function update($forgotPasswordPageSetting, $language, $request)
    {
        $fields = $this->fields($forgotPasswordPageSetting, $language, $request);
        $forgotPasswordPageSettingDetail = ForgotPasswordPageSettingDetail::whereForgotPassPageId($forgotPasswordPageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$forgotPasswordPageSettingDetail){
            $fields = $this->fields($forgotPasswordPageSetting, $language, $request);
        ForgotPasswordPageSettingDetail::create($fields);
        }
        else{
            ForgotPasswordPageSettingDetail::whereForgotPassPageId($forgotPasswordPageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
