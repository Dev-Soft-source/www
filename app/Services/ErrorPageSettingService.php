<?php

namespace App\Services;

use App\Models\ErrorPageSettingDetail;

class ErrorPageSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => []];
    }

    public function fields($errorPageSetting, $language, $request)
    {
        return [
            'error_page_setting_id' => $errorPageSetting->id,
            'language_id' => $language->id,
            'error_404_heading' => $this->data($request, $language, 'error_404_heading'),
            'error_404_paragraph_1' => $this->data($request, $language, 'error_404_paragraph_1'),
            'error_404_paragraph_2' => $this->data($request, $language, 'error_404_paragraph_2'),
            'error_404_back_home_btn' => $this->data($request, $language, 'error_404_back_home_btn'),
            'error_404_contact_btn' => $this->data($request, $language, 'error_404_contact_btn'),
        ];
    }

    public function update($errorPageSetting, $language, $request)
    {
        $fields = $this->fields($errorPageSetting, $language, $request);
        $exists = ErrorPageSettingDetail::where('error_page_setting_id', $errorPageSetting->id)
            ->where('language_id', $language->id)
            ->exists();
        if (!$exists) {
            ErrorPageSettingDetail::create($fields);
        } else {
            ErrorPageSettingDetail::where('error_page_setting_id', $errorPageSetting->id)
                ->where('language_id', $language->id)
                ->first()?->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
