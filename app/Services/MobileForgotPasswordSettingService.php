<?php

namespace App\Services;

use App\Models\MobileForgotPasswordSettingDetail;

class MobileForgotPasswordSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['main_label.main_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['main_label.main_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['button_label.button_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['button_label.button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($forgotPasswordPageSetting, $language, $request)
    {
        return [
            'forgot_page_id' => $forgotPasswordPageSetting->id,
            'language_id' => $language->id,
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'main_label' => $this->data($request, $language, 'main_label'),
            'button_label' => $this->data($request, $language, 'button_label'),
        ];
    }

    public function update($forgotPasswordPageSetting, $language, $request)
    {
        $fields = $this->fields($forgotPasswordPageSetting, $language, $request);
        $forgotPasswordPageSettingDetail = MobileForgotPasswordSettingDetail::whereForgotPageId($forgotPasswordPageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$forgotPasswordPageSettingDetail){
            $fields = $this->fields($forgotPasswordPageSetting, $language, $request);
            MobileForgotPasswordSettingDetail::create($fields);
        }
        else{
            MobileForgotPasswordSettingDetail::whereForgotPageId($forgotPasswordPageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
