<?php

namespace App\Services;

use App\Models\SignupPageSettingDetail;

class SignupPageSettingService
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
                $validationRule = array_merge($validationRule, ['or_label.or_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['or_label.or_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
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
                $validationRule = array_merge($validationRule, ['email_label.email_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['email_label.email_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['email_error.email_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['email_error.email_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['password_label.password_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['password_label.password_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['password_error.password_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['password_error.password_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['confirm_password_label.confirm_password_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['confirm_password_label.confirm_password_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['confirm_password_error.confirm_password_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['confirm_password_error.confirm_password_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['phone_number_label.phone_number_label_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['phone_number_label.phone_number_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['phone_number_option1.phone_number_option1_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['phone_number_option1.phone_number_option1_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['phone_number_option2.phone_number_option2_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['phone_number_option2.phone_number_option2_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['agree_terms_label.agree_terms_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['agree_terms_label.agree_terms_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['button_label.button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['button_label.button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['after_button_label.after_button_label_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['after_button_label.after_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['signin_label.signin_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['signin_label.signin_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['app_main_heading.app_main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['app_main_heading.app_main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['app_agree_terms_part1_label.app_agree_terms_part1_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['app_agree_terms_part1_label.app_agree_terms_part1_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['app_agree_terms_link1_label.app_agree_terms_link1_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['app_agree_terms_link1_label.app_agree_terms_link1_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['app_agree_terms_link2_label.app_agree_terms_link2_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['app_agree_terms_link2_label.app_agree_terms_link2_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['app_agree_terms_part2_label.app_agree_terms_part2_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['app_agree_terms_part2_label.app_agree_terms_part2_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['app_agree_terms_link3_label.app_agree_terms_link3_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['app_agree_terms_link3_label.app_agree_terms_link3_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['app_agree_terms_part3_label.app_agree_terms_part3_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['app_agree_terms_part3_label.app_agree_terms_part3_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_account_label.no_account_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_account_label.no_account_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['signin_link_label.signin_link_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['signin_link_label.signin_link_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['now_label.now_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['now_label.now_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['language_label.language_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['language_label.language_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['agree_terms_error.agree_terms_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['agree_terms_error.agree_terms_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($signupPageSetting, $language, $request)
    {
        return [
            'signup_page_setting_id' => $signupPageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'or_label' => $this->data($request, $language, 'or_label'),
            'required_label' => $this->data($request, $language, 'required_label'),
            'first_name_label' => $this->data($request, $language, 'first_name_label'),
            'first_name_error' => $this->data($request, $language, 'first_name_error'),
            'first_name_placeholder' => $this->data($request, $language, 'first_name_placeholder'),
            'last_name_label' => $this->data($request, $language, 'last_name_label'),
            'last_name_error' => $this->data($request, $language, 'last_name_error'),
            'last_name_placeholder' => $this->data($request, $language, 'last_name_placeholder'),
            'email_label' => $this->data($request, $language, 'email_label'),
            'email_error' => $this->data($request, $language, 'email_error'),
            'email_placeholder' => $this->data($request, $language, 'email_placeholder'),
            'password_label' => $this->data($request, $language, 'password_label'),
            'password_error' => $this->data($request, $language, 'password_error'),
            'password_placeholder' => $this->data($request, $language, 'password_placeholder'),
            'confirm_password_label' => $this->data($request, $language, 'confirm_password_label'),
            'confirm_password_error' => $this->data($request, $language, 'confirm_password_error'),
            'confirm_password_placeholder' => $this->data($request, $language, 'confirm_password_placeholder'),
            'agree_terms_error' => $this->data($request, $language, 'agree_terms_error'),
            'phone_number_label' => $this->data($request, $language, 'phone_number_label'),
            'phone_number_option1' => $this->data($request, $language, 'phone_number_option1'),
            'phone_number_option2' => $this->data($request, $language, 'phone_number_option2'),
            'agree_terms_label' => $this->data($request, $language, 'agree_terms_label'),
            'button_label' => $this->data($request, $language, 'button_label'),
            'after_button_label' => $this->data($request, $language, 'after_button_label'),
            'signin_label' => $this->data($request, $language, 'signin_label'),
            'app_main_heading' => $this->data($request, $language, 'app_main_heading'),
            'app_agree_terms_part1_label' => $this->data($request, $language, 'app_agree_terms_part1_label'),
            'app_agree_terms_link1_label' => $this->data($request, $language, 'app_agree_terms_link1_label'),
            'app_agree_terms_link2_label' => $this->data($request, $language, 'app_agree_terms_link2_label'),
            'app_agree_terms_part2_label' => $this->data($request, $language, 'app_agree_terms_part2_label'),
            'app_agree_terms_link3_label' => $this->data($request, $language, 'app_agree_terms_link3_label'),
            'app_agree_terms_part3_label' => $this->data($request, $language, 'app_agree_terms_part3_label'),
            'no_account_label' => $this->data($request, $language, 'no_account_label'),
            'signin_link_label' => $this->data($request, $language, 'signin_link_label'),
            'now_label' => $this->data($request, $language, 'now_label'),
            'language_label' => $this->data($request, $language, 'language_label'),
        ];
    }

    public function update($signupPageSetting, $language, $request)
    {
        $fields = $this->fields($signupPageSetting, $language, $request);
        $signupPageSettingDetail = SignupPageSettingDetail::whereSignupPageSettingId($signupPageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$signupPageSettingDetail){
            $fields = $this->fields($signupPageSetting, $language, $request);
        SignupPageSettingDetail::create($fields);
        }
        else{
            SignupPageSettingDetail::whereSignupPageSettingId($signupPageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
