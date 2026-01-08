<?php

namespace App\Services;

use App\Models\Step1PageSettingDetail;

class Step1PageSettingService
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
                $validationRule = array_merge($validationRule, ['required_label.required_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['required_label.required_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['first_name_label.first_name_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['first_name_label.first_name_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['first_name_error.first_name_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['first_name_error.first_name_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['last_name_label.last_name_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['last_name_label.last_name_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['last_name_error.last_name_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['last_name_error.last_name_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['gender_label.gender_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['gender_label.gender_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['gender_error.gender_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['gender_error.gender_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['male_option_label.male_option_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['male_option_label.male_option_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['female_option_label.female_option_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['female_option_label.female_option_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['prefer_option_label.prefer_option_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['prefer_option_label.prefer_option_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['dob_label.dob_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['dob_label.dob_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['dob_error.dob_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['dob_error.dob_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['country_label.country_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['country_label.country_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['country_error.country_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['country_error.country_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['state_label.state_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['state_label.state_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['state_error.state_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['state_error.state_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['city_label.city_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['city_label.city_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['city_error.city_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['city_error.city_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['zip_code_label.zip_code_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['zip_code_label.zip_code_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['zip_code_error.zip_code_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['zip_code_error.zip_code_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['bio_label.bio_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['bio_label.bio_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['bio_error.bio_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['bio_error.bio_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['button_label.button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['button_label.button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['logout_button_label.logout_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['logout_button_label.logout_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['bio_placeholder.bio_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['bio_placeholder.bio_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($step1PageSetting, $language, $request)
    {
        return [
            'step1_page_setting_id' => $step1PageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'required_label' => $this->data($request, $language, 'required_label'),
            'first_name_label' => $this->data($request, $language, 'first_name_label'),
            'first_name_error' => $this->data($request, $language, 'first_name_error'),
            'last_name_label' => $this->data($request, $language, 'last_name_label'),
            'last_name_error' => $this->data($request, $language, 'last_name_error'),
            'gender_label' => $this->data($request, $language, 'gender_label'),
            'gender_error' => $this->data($request, $language, 'gender_error'),
            'male_option_label' => $this->data($request, $language, 'male_option_label'),
            'female_option_label' => $this->data($request, $language, 'female_option_label'),
            'prefer_option_label' => $this->data($request, $language, 'prefer_option_label'),
            'dob_label' => $this->data($request, $language, 'dob_label'),
            'dob_error' => $this->data($request, $language, 'dob_error'),
            'country_label' => $this->data($request, $language, 'country_label'),
            'country_error' => $this->data($request, $language, 'country_error'),
            'state_label' => $this->data($request, $language, 'state_label'),
            'state_error' => $this->data($request, $language, 'state_error'),
            'city_label' => $this->data($request, $language, 'city_label'),
            'city_error' => $this->data($request, $language, 'city_error'),
            'zip_code_label' => $this->data($request, $language, 'zip_code_label'),
            'zip_code_error' => $this->data($request, $language, 'zip_code_error'),
            'bio_label' => $this->data($request, $language, 'bio_label'),
            'bio_error' => $this->data($request, $language, 'bio_error'),
            'button_label' => $this->data($request, $language, 'button_label'),
            'logout_button_label' => $this->data($request, $language, 'logout_button_label'),
            'bio_placeholder' => $this->data($request, $language, 'bio_placeholder'),
        ];
    }

    public function update($step1PageSetting, $language, $request)
    {
        $fields = $this->fields($step1PageSetting, $language, $request);
        $step1PageSettingDetail = Step1PageSettingDetail::whereStep1PageSettingId($step1PageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$step1PageSettingDetail){
            $fields = $this->fields($step1PageSetting, $language, $request);
        Step1PageSettingDetail::create($fields);
        }
        else{
            Step1PageSettingDetail::whereStep1PageSettingId($step1PageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
