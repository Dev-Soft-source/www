<?php

namespace App\Services;

use App\Models\MyPhoneSettingDetail;

class MyPhoneSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['unverified_number_label.unverified_number_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['unverified_number_label.unverified_number_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_verify_button_text.mobile_verify_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_verify_button_text.mobile_verify_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['web_send_verification_code_button_text.web_send_verification_code_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['web_send_verification_code_button_text.web_send_verification_code_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['delete_button_text.delete_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['delete_button_text.delete_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['country_code_placeholder.country_code_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['country_code_placeholder.country_code_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_phone_number_label.mobile_phone_number_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_phone_number_label.mobile_phone_number_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['phone_number_placeholder.phone_number_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['phone_number_placeholder.phone_number_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['save_phoneno_button_text.save_phoneno_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['save_phoneno_button_text.save_phoneno_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['send_verification_code_button_text.send_verification_code_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['send_verification_code_button_text.send_verification_code_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['add_another_phone_number_title.add_another_phone_number_title_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['add_another_phone_number_title.add_another_phone_number_title_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($myPhoneSetting, $language, $request)
    {
        // dd($myPhoneSetting);
        return [
            'phone_no_setting_id' => $myPhoneSetting->id,
            'language_id' => $language->id,
            'phone_no_description_text' => $this->data($request, $language, 'phone_no_description_text'),
            'unverified_number_label' => $this->data($request, $language, 'unverified_number_label'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'mobile_verify_button_text' => $this->data($request, $language, 'mobile_verify_button_text'),
            'web_send_verification_code_button_text' => $this->data($request, $language, 'web_send_verification_code_button_text'),
            'delete_button_text' => $this->data($request, $language, 'delete_button_text'),
            'mobile_country_code_label' => $this->data($request, $language, 'mobile_country_code_label'),
            'country_code_placeholder' => $this->data($request, $language, 'country_code_placeholder'),
            'mobile_phone_number_label' => $this->data($request, $language, 'mobile_phone_number_label'),
            'phone_number_placeholder' => $this->data($request, $language, 'phone_number_placeholder'),
            'save_phoneno_button_text' => $this->data($request, $language, 'save_phoneno_button_text'),
            'send_verification_code_button_text' => $this->data($request, $language, 'send_verification_code_button_text'),
            'verify_phone_number_heading' => $this->data($request, $language, 'verify_phone_number_heading'),
            'otp_code_description' => $this->data($request, $language, 'otp_code_description'),
            'enter_code_label' => $this->data($request, $language, 'enter_code_label'),
            'verify_phone_number_label' => $this->data($request, $language, 'verify_phone_number_label'),
            'second_text' => $this->data($request, $language, 'second_text'),
            'request_code_text' => $this->data($request, $language, 'request_code_text'),
            'resend_code_btn_label' => $this->data($request, $language, 'resend_code_btn_label'),
            'set_as_default_label' => $this->data($request, $language, 'set_as_default_label'),
            'default_verified_number_label' => $this->data($request, $language, 'default_verified_number_label'),
            'verified_number_label' => $this->data($request, $language, 'verified_number_label'),
            'phone_no_description_text1' => $this->data($request, $language, 'phone_no_description_text1'),
            'phone_number_label_web' => $this->data($request, $language, 'phone_number_label_web'),
            'country_code_label_web' => $this->data($request, $language, 'country_code_label_web'),
            'country_id_label_web' => $this->data($request, $language, 'country_id_label_web'),
            
            'add_another_phone_number_title' => $this->data($request, $language, 'add_another_phone_number_title'),

        ];
    }

    public function update($myPhoneSetting, $language, $request)
    {
        $fields = $this->fields($myPhoneSetting, $language, $request);
        $myPhoneSettingDetail = MyPhoneSettingDetail::wherePhoneNoSettingId($myPhoneSetting->id)->whereLanguageId($language->id)->exists();
        if(!$myPhoneSettingDetail){
            $fields = $this->fields($myPhoneSetting, $language, $request);
            MyPhoneSettingDetail::create($fields);
        }
        else{
            MyPhoneSettingDetail::wherePhoneNoSettingId($myPhoneSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
