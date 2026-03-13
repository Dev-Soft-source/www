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
                $validationRule = array_merge($validationRule, ['phone_label.phone_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['phone_label.phone_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['phone_error.phone_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['phone_error.phone_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['skip_button_label.skip_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['skip_button_label.skip_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['skip_phone_number_label.skip_phone_number_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['skip_phone_number_label.skip_phone_number_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['save_button_label.save_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['save_button_label.save_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['send_button_label.send_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['send_button_label.send_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['logout_button_label.logout_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['logout_button_label.logout_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['country_code_label.country_code_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['country_code_label.country_code_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['country_code_error.country_code_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['country_code_error.country_code_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['verify_button_label.verify_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['verify_button_label.verify_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['verify_code_label.verify_code_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['verify_code_label.verify_code_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['enter_code_label.enter_code_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['enter_code_label.enter_code_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['request_code_label.request_code_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['request_code_label.request_code_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['second_label.second_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['second_label.second_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['whatsapp_validation_title.whatsapp_validation_title_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['whatsapp_validation_title.whatsapp_validation_title_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['whatsapp_validation_message.whatsapp_validation_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['whatsapp_validation_message.whatsapp_validation_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['whatsapp_not_available_title.whatsapp_not_available_title_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['whatsapp_not_available_title.whatsapp_not_available_title_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['whatsapp_not_available_message.whatsapp_not_available_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['whatsapp_not_available_message.whatsapp_not_available_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['whatsapp_success_title.whatsapp_success_title_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['whatsapp_success_title.whatsapp_success_title_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['whatsapp_success_message.whatsapp_success_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['whatsapp_success_message.whatsapp_success_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['whatsapp_error_title.whatsapp_error_title_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['whatsapp_error_title.whatsapp_error_title_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['whatsapp_error_message.whatsapp_error_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['whatsapp_error_message.whatsapp_error_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['whatsapp_limit_title.whatsapp_limit_title_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['whatsapp_limit_title.whatsapp_limit_title_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['whatsapp_limit_message.whatsapp_limit_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['whatsapp_limit_message.whatsapp_limit_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['whatsapp_default_title.whatsapp_default_title_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['whatsapp_default_title.whatsapp_default_title_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['whatsapp_default_message.whatsapp_default_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['whatsapp_default_message.whatsapp_default_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
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
            'country_code_label' => $this->data($request, $language, 'country_code_label'),
            'country_code_error' => $this->data($request, $language, 'country_code_error'),
            'phone_label' => $this->data($request, $language, 'phone_label'),
            'phone_error' => $this->data($request, $language, 'phone_error'),
            'skip_button_label' => $this->data($request, $language, 'skip_button_label'),
            'skip_phone_number_label' => $this->data($request, $language, 'skip_phone_number_label'),
            'verify_button_label' => $this->data($request, $language, 'verify_button_label'),
            'verify_code_label' => $this->data($request, $language, 'verify_code_label'),
            'enter_code_label' => $this->data($request, $language, 'enter_code_label'),
            'request_code_label' => $this->data($request, $language, 'request_code_label'),
            'second_label' => $this->data($request, $language, 'second_label'),
            'save_button_label' => $this->data($request, $language, 'save_button_label'),
            'send_button_label' => $this->data($request, $language, 'send_button_label'),
            'logout_button_label' => $this->data($request, $language, 'logout_button_label'),
            'whatsapp_validation_title' => $this->data($request, $language, 'whatsapp_validation_title'),
            'whatsapp_validation_message' => $this->data($request, $language, 'whatsapp_validation_message'),
            'whatsapp_not_available_title' => $this->data($request, $language, 'whatsapp_not_available_title'),
            'whatsapp_not_available_message' => $this->data($request, $language, 'whatsapp_not_available_message'),
            'whatsapp_success_title' => $this->data($request, $language, 'whatsapp_success_title'),
            'whatsapp_success_message' => $this->data($request, $language, 'whatsapp_success_message'),
            'whatsapp_error_title' => $this->data($request, $language, 'whatsapp_error_title'),
            'whatsapp_error_message' => $this->data($request, $language, 'whatsapp_error_message'),
            'whatsapp_limit_title' => $this->data($request, $language, 'whatsapp_limit_title'),
            'whatsapp_limit_message' => $this->data($request, $language, 'whatsapp_limit_message'),
            'whatsapp_default_title' => $this->data($request, $language, 'whatsapp_default_title'),
            'whatsapp_default_message' => $this->data($request, $language, 'whatsapp_default_message'),
        ];
    }

    public function update($step5PageSetting, $language, $request)
    {
        $fields = $this->fields($step5PageSetting, $language, $request);
        $step5PageSettingDetail = Step5PageSettingDetail::whereStep5PageSettingId($step5PageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$step5PageSettingDetail){
            $fields = $this->fields($step5PageSetting, $language, $request);
        Step5PageSettingDetail::create($fields);
        }
        else{
            Step5PageSettingDetail::whereStep5PageSettingId($step5PageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
