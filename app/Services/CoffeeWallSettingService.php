<?php

namespace App\Services;

use App\Models\CoffeeWallPageSettingDetail;

class CoffeeWallSettingService
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
                $validationRule = array_merge($validationRule, ['required_field_label.required_field_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['required_field_label.required_field_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_text.main_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_text.main_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['agree_terms_label.agree_terms_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['agree_terms_label.agree_terms_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['custom_amount_label.custom_amount_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['custom_amount_label.custom_amount_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['pay_button_label.pay_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['pay_button_label.pay_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['frequency_label.frequency_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['frequency_label.frequency_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['email_label.email_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['email_label.email_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['name_label.name_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['name_label.name_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['phone_label.phone_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['phone_label.phone_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['designation_label.designation_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['designation_label.designation_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['designation_option1.designation_option1_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['designation_option1.designation_option1_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['designation_option2.designation_option2_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['designation_option2.designation_option2_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['designation_option3.designation_option3_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['designation_option3.designation_option3_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['designation_option4.designation_option4_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['designation_option4.designation_option4_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($coffeeWallSetting, $language, $request)
    {
        return [
            'coffee_wall_setting_id' => $coffeeWallSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'required_field_label' => $this->data($request, $language, 'required_field_label'),
            'main_text' => $this->data($request, $language, 'main_text'),
            'agree_terms_label' => $this->data($request, $language, 'agree_terms_label'),
            'custom_amount_label' => $this->data($request, $language, 'custom_amount_label'),
            'pay_button_label' => $this->data($request, $language, 'pay_button_label'),
            'frequency_label' => $this->data($request, $language, 'frequency_label'),
            'email_label' => $this->data($request, $language, 'email_label'),
            'name_label' => $this->data($request, $language, 'name_label'),
            'phone_label' => $this->data($request, $language, 'phone_label'),
            'designation_label' => $this->data($request, $language, 'designation_label'),
            'designation_option1' => $this->data($request, $language, 'designation_option1'),
            'designation_option2' => $this->data($request, $language, 'designation_option2'),
            'designation_option3' => $this->data($request, $language, 'designation_option3'),
            'designation_option4' => $this->data($request, $language, 'designation_option4'),
            'monthly_label' => $this->data($request, $language, 'monthly_label'),
            'quarterly_label' => $this->data($request, $language, 'quarterly_label'),
            'semi_annually_label' => $this->data($request, $language, 'semi_annually_label'),
            'annually_label' => $this->data($request, $language, 'annually_label'),
        ];
    }

    public function update($coffeeWallSetting, $language, $request)
    {
        $fields = $this->fields($coffeeWallSetting, $language, $request);
        $coffeeWallSettingDetail = CoffeeWallPageSettingDetail::whereCoffeeWallSettingId($coffeeWallSetting->id)->whereLanguageId($language->id)->exists();
        if(!$coffeeWallSettingDetail){
            $fields = $this->fields($coffeeWallSetting, $language, $request);
            CoffeeWallPageSettingDetail::create($fields);
        }
        else{
            CoffeeWallPageSettingDetail::whereCoffeeWallSettingId($coffeeWallSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
