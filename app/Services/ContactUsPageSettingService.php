<?php

namespace App\Services;

use App\Models\ContactUsPageSettingDetail;

class ContactUsPageSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                // $validationRule = array_merge($validationRule, ['name.name_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['name.name_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['meta_keywords.meta_keywords_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['meta_keywords.meta_keywords_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['meta_description.meta_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['meta_description.meta_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mailing_address_label.mailing_address_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mailing_address_label.mailing_address_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['telephone_label.telephone_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['telephone_label.telephone_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['name_email_placeholder.name_email_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['name_email_placeholder.name_email_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['message_placeholder.message_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['message_placeholder.message_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['submit_button_label.submit_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['submit_button_label.submit_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['required_feilds_text.required_feilds_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['required_feilds_text.required_feilds_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
            //     $validationRule = array_merge($validationRule, ['placeholder_name.placeholder_name_' . $language->id => ['required', 'string']]);
            //     $errorMessages = array_merge($errorMessages, ['placeholder_name.placeholder_name_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['placeholder_email.placeholder_email_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['placeholder_email.placeholder_email_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['placeholder_phone.placeholder_phone_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['placeholder_phone.placeholder_phone_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['placeholder_message.placeholder_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['placeholder_message.placeholder_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($contactUsPageSetting, $language, $request)
    {
        return [
            'contact_page_setting_id' => $contactUsPageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'mailing_address_label' => $this->data($request, $language, 'mailing_address_label'),
            'telephone_label' => $this->data($request, $language, 'telephone_label'),
            'name_email_placeholder' => $this->data($request, $language, 'name_email_placeholder'),
            'message_placeholder' => $this->data($request, $language, 'message_placeholder'),
            'submit_button_label' => $this->data($request, $language, 'submit_button_label'),
            'required_feilds_text' => $this->data($request, $language, 'required_feilds_text'),

            'placeholder_name' => $this->data($request, $language, 'placeholder_name'),
            'placeholder_email' => $this->data($request, $language, 'placeholder_email'),
            'placeholder_phone' => $this->data($request, $language, 'placeholder_phone'),
            'placeholder_message' => $this->data($request, $language, 'placeholder_message'),
        ];
    }

    public function update($contactUsPageSetting, $language, $request)
    {
        $fields = $this->fields($contactUsPageSetting, $language, $request);
        $contactUsPageSettingDetail = ContactUsPageSettingDetail::whereContactPageSettingId($contactUsPageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$contactUsPageSettingDetail){
            $fields = $this->fields($contactUsPageSetting, $language, $request);
        ContactUsPageSettingDetail::create($fields);
        }
        else{
            ContactUsPageSettingDetail::whereContactPageSettingId($contactUsPageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
