<?php

namespace App\Services;

use App\Models\LogoutSettingDetail;

class LogoutSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['confirmation_message_heading.confirmation_message_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['confirmation_message_heading.confirmation_message_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['confirmation_no_label.confirmation_no_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['confirmation_no_label.confirmation_no_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['confirmation_yes_label.confirmation_yes_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['confirmation_yes_label.confirmation_yes_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($logoutSetting, $language, $request)
    {
        return [
            'logout_setting_id' => $logoutSetting->id,
            'language_id' => $language->id,
            'confirmation_message_heading' => $this->data($request, $language, 'confirmation_message_heading'),
            'confirmation_no_label' => $this->data($request, $language, 'confirmation_no_label'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'confirmation_yes_label' => $this->data($request, $language, 'confirmation_yes_label'),
            ];
    }

    public function update($logoutSetting, $language, $request)
    {
        $fields = $this->fields($logoutSetting, $language, $request);
        $logoutSettingDetail = LogoutSettingDetail::whereLogoutSettingId($logoutSetting->id)->whereLanguageId($language->id)->exists();
        if(!$logoutSettingDetail){
            $fields = $this->fields($logoutSetting, $language, $request);
            LogoutSettingDetail::create($fields);
        }
        else{
            LogoutSettingDetail::whereLogoutSettingId($logoutSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
