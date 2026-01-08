<?php

namespace App\Services;

use App\Models\DriverPageSettingDetail;

class DriverPageSettingService
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
                $validationRule = array_merge($validationRule, ['sub_heading.sub_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['sub_heading.sub_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['page_description.page_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['page_description.page_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($driverPageSetting, $language, $request)
    {
        return [
            'driver_page_setting_id' => $driverPageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'sub_heading' => $this->data($request, $language, 'sub_heading'),
            'page_description' => $this->data($request, $language, 'page_description'),
        ];
    }

    public function update($driverPageSetting, $language, $request)
    {
        $fields = $this->fields($driverPageSetting, $language, $request);
        $driverPageSettingDetail = DriverPageSettingDetail::whereDriverPageSettingId($driverPageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$driverPageSettingDetail){
            $fields = $this->fields($driverPageSetting, $language, $request);
        DriverPageSettingDetail::create($fields);
        }
        else{
            DriverPageSettingDetail::whereDriverPageSettingId($driverPageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
