<?php

namespace App\Services;

use App\Models\ReferralPageSettingDetail;

class ReferralPageSettingService
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

                $validationRule = array_merge($validationRule, ['your_referral_url_label.your_referral_url_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['your_referral_url_label.your_referral_url_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['referral_description.referral_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['referral_description.referral_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['my_referral_text.my_referral_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['my_referral_text.my_referral_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['account_id_label.account_id_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['account_id_label.account_id_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['user_label.user_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['user_label.user_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['registered_on_label.registered_on_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['registered_on_label.registered_on_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_referral_user_found_message.no_referral_user_found_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_referral_user_found_message.no_referral_user_found_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($referralPageSetting, $language, $request)
    {
        return [
            'referral_page_setting_id' => $referralPageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'your_referral_url_label' => $this->data($request, $language, 'your_referral_url_label'),
            'referral_description' => $this->data($request, $language, 'referral_description'),
            'my_referral_text' => $this->data($request, $language, 'my_referral_text'),
            'account_id_label' => $this->data($request, $language, 'account_id_label'),
            'user_label' => $this->data($request, $language, 'user_label'),
            'registered_on_label' => $this->data($request, $language, 'registered_on_label'),
            'no_referral_user_found_message' => $this->data($request, $language, 'no_referral_user_found_message'),
        ];
    }

    public function update($referralPageSetting, $language, $request)
    {
        $fields = $this->fields($referralPageSetting, $language, $request);
        $referralPageSettingDetail = ReferralPageSettingDetail::whereReferralPageSettingId($referralPageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$referralPageSettingDetail){
            $fields = $this->fields($referralPageSetting, $language, $request);
            ReferralPageSettingDetail::create($fields);
        }
        else{
            ReferralPageSettingDetail::whereReferralPageSettingId($referralPageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
