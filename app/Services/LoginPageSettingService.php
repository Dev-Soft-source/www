<?php

namespace App\Services;

use App\Models\LoginPageSettingDetail;

class LoginPageSettingService
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
                $validationRule = array_merge($validationRule, ['continue_label.continue_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['continue_label.continue_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['or_label.or_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['or_label.or_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['email_label.email_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['email_label.email_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['email_error.email_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['email_error.email_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['password_label.password_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['password_label.password_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['password_error.password_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['password_error.password_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['forgot_password_label.forgot_password_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['forgot_password_label.forgot_password_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['submit_button_label.submit_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['submit_button_label.submit_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['signup_label.signup_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['signup_label.signup_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_account_label.no_account_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_account_label.no_account_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['signup_link_label.signup_link_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['signup_link_label.signup_link_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['now_label.now_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['now_label.now_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['language_label.language_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['language_label.language_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['new_verification_email_btn_label.new_verification_email_btn_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['new_verification_email_btn_label.new_verification_email_btn_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['protect_account_heading.protect_account_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['protect_account_heading.protect_account_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['protect_account_text.protect_account_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['protect_account_text.protect_account_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['remember_me_text.remember_me_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['remember_me_text.remember_me_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['close_modal_error_message.close_modal_error_message_' . $language->id => ['nullable', 'string']]);
                $errorMessages = array_merge($errorMessages, ['close_modal_error_message.close_modal_error_message_' . $language->id . '.nullable' => 'This field in ' . $language->name . ' is required.']);

            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($loginPageSetting, $language, $request)
    {
        return [
            'login_page_setting_id' => $loginPageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'continue_label' => $this->data($request, $language, 'continue_label'),
            'new_verification_email_btn_label' => $this->data($request, $language, 'new_verification_email_btn_label'),
            'or_label' => $this->data($request, $language, 'or_label'),
            'email_label' => $this->data($request, $language, 'email_label'),
            'email_error' => $this->data($request, $language, 'email_error'),
            'email_placeholder' => $this->data($request, $language, 'email_placeholder'),
            'password_label' => $this->data($request, $language, 'password_label'),
            'password_error' => $this->data($request, $language, 'password_error'),
            'password_placeholder' => $this->data($request, $language, 'password_placeholder'),
            'forgot_password_label' => $this->data($request, $language, 'forgot_password_label'),
            'submit_button_label' => $this->data($request, $language, 'submit_button_label'),
            'signup_label' => $this->data($request, $language, 'signup_label'),
            'no_account_label' => $this->data($request, $language, 'no_account_label'),
            'signup_link_label' => $this->data($request, $language, 'signup_link_label'),
            'now_label' => $this->data($request, $language, 'now_label'),
            'language_label' => $this->data($request, $language, 'language_label'),
            'protect_account_heading' => $this->data($request, $language, 'protect_account_heading'),
            'protect_account_text' => $this->data($request, $language, 'protect_account_text'),
            'remember_me_text' => $this->data($request, $language, 'remember_me_text'),
            'close_modal_error_message' => $this->data($request, $language, 'close_modal_error_message'),
        ];
    }

    public function update($loginPageSetting, $language, $request)
    {
        $fields = $this->fields($loginPageSetting, $language, $request);
        $loginPageSettingDetail = LoginPageSettingDetail::whereLoginPageSettingId($loginPageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$loginPageSettingDetail){
            $fields = $this->fields($loginPageSetting, $language, $request);
        LoginPageSettingDetail::create($fields);
        }
        else{
            LoginPageSettingDetail::whereLoginPageSettingId($loginPageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
