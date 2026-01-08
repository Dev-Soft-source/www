<?php

namespace App\Services;

use App\Models\Step5PageSettingDetail;

class Step5PageSettingService
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
                $validationRule = array_merge($validationRule, ['sub_main_label.sub_main_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['sub_main_label.sub_main_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['required_label.required_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['required_label.required_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_license_label.driver_license_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_license_label.driver_license_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_license_error.driver_license_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_license_error.driver_license_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_license_sub_label.driver_license_sub_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_license_sub_label.driver_license_sub_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['photo_detail_label.photo_detail_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['photo_detail_label.photo_detail_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_photo_choose_file_label.mobile_photo_choose_file_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_photo_choose_file_label.mobile_photo_choose_file_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['skip_license.skip_license_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['skip_license.skip_license_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['next_button_label.next_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['next_button_label.next_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['liecense_section_heading.liecense_section_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['liecense_section_heading.liecense_section_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($step5PageSetting, $language, $request)
    {
        return [
            'step5_page_setting_id' => $step5PageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'main_label' => $this->data($request, $language, 'main_label'),
            'sub_main_label' => $this->data($request, $language, 'sub_main_label'),
            'required_label' => $this->data($request, $language, 'required_label'),
            'driver_license_label' => $this->data($request, $language, 'driver_license_label'),
            'driver_license_error' => $this->data($request, $language, 'driver_license_error'),
            'driver_license_sub_label' => $this->data($request, $language, 'driver_license_sub_label'),
            'photo_detail_label' => $this->data($request, $language, 'photo_detail_label'),
            'mobile_photo_choose_file_label' => $this->data($request, $language, 'mobile_photo_choose_file_label'),
            'skip_license' => $this->data($request, $language, 'skip_license'),
            'next_button_label' => $this->data($request, $language, 'next_button_label'),
            'liecense_section_heading' => $this->data($request, $language, 'liecense_section_heading'),
        ];
    }

    public function update($step5PageSetting, $language, $request)
    {
        $fields = $this->fields($step5PageSetting, $language, $request);
        
        $step5PageSettingDetail = Step5PageSettingDetail::whereStep5PageSettingId($step5PageSetting->id)->whereLanguageId($language->id)->exists();

        if (!$step5PageSettingDetail) {
            Step5PageSettingDetail::create($fields);
        } else {
            Step5PageSettingDetail::whereStep5PageSettingId($step5PageSetting->id)->whereLanguageId($language->id)->update($fields);
        }

        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}