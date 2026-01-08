<?php

namespace App\Services;

use App\Models\Step2PageSettingDetail;

class Step2PageSettingService
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
                $validationRule = array_merge($validationRule, ['photo_error.photo_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['photo_error.photo_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['photo_placeholder.photo_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['photo_placeholder.photo_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_photo_label.mobile_photo_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_photo_label.mobile_photo_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_choose_file_label.mobile_choose_file_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_choose_file_label.mobile_choose_file_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['photo_label.photo_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['photo_label.photo_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['skip_button_label.skip_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['skip_button_label.skip_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['next_button_label.next_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['next_button_label.next_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['logout_button_label.logout_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['logout_button_label.logout_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['sub_heading_text.sub_heading_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['sub_heading_text.sub_heading_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($step2PageSetting, $language, $request)
    {
        return [
            'step2_page_setting_id' => $step2PageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'photo_error' => $this->data($request, $language, 'photo_error'),
            'photo_placeholder' => $this->data($request, $language, 'photo_placeholder'),
            'mobile_photo_label' => $this->data($request, $language, 'mobile_photo_label'),
            'mobile_choose_file_label' => $this->data($request, $language, 'mobile_choose_file_label'),
            'photo_label' => $this->data($request, $language, 'photo_label'),
            'skip_button_label' => $this->data($request, $language, 'skip_button_label'),
            'next_button_label' => $this->data($request, $language, 'next_button_label'),
            'logout_button_label' => $this->data($request, $language, 'logout_button_label'),
            'sub_heading_text' => $this->data($request, $language, 'sub_heading_text'),
        ];
    }

    public function update($step2PageSetting, $language, $request)
    {
        $fields = $this->fields($step2PageSetting, $language, $request);
        $step2PageSettingDetail = Step2PageSettingDetail::whereStep2PageSettingId($step2PageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$step2PageSettingDetail){
            $fields = $this->fields($step2PageSetting, $language, $request);
        Step2PageSettingDetail::create($fields);
        }
        else{
            Step2PageSettingDetail::whereStep2PageSettingId($step2PageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
