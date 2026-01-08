<?php

namespace App\Services;

use App\Models\ProfilePageSettingDetail;

class ProfilePageSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['name.name_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['name.name_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['profile_setting_label.profile_setting_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['profile_setting_label.profile_setting_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['my_wallet_label.my_wallet_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['my_wallet_label.my_wallet_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['payment_options_label.payment_options_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['payment_options_label.payment_options_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['payout_options_label.payout_options_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['payout_options_label.payout_options_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['my_reviews_label.my_reviews_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['my_reviews_label.my_reviews_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['privacy_policy_label.privacy_policy_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['privacy_policy_label.privacy_policy_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['refund_policy_label.refund_policy_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['refund_policy_label.refund_policy_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancellation_policy_label.cancellation_policy_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancellation_policy_label.cancellation_policy_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['dispute_policy_label.dispute_policy_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['dispute_policy_label.dispute_policy_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['contact_proximaride_label.contact_proximaride_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['contact_proximaride_label.contact_proximaride_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['logout_label.logout_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['logout_label.logout_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['colse_your_contact_label.colse_your_contact_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['colse_your_contact_label.colse_your_contact_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($profilePageSetting, $language, $request)
    {
        return [
            'profile_page_setting_id' => $profilePageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'profile_setting_label' => $this->data($request, $language, 'profile_setting_label'),
            'my_wallet_label' => $this->data($request, $language, 'my_wallet_label'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'payment_options_label' => $this->data($request, $language, 'payment_options_label'),
            'payout_options_label' => $this->data($request, $language, 'payout_options_label'),
            'my_reviews_label' => $this->data($request, $language, 'my_reviews_label'),
            'terms_condition_label' => $this->data($request, $language, 'terms_condition_label'),
            'privacy_policy_label' => $this->data($request, $language, 'privacy_policy_label'),
            'terms_of_use_label' => $this->data($request, $language, 'terms_of_use_label'),
            'refund_policy_label' => $this->data($request, $language, 'refund_policy_label'),
            'cancellation_policy_label' => $this->data($request, $language, 'cancellation_policy_label'),
            'dispute_policy_label' => $this->data($request, $language, 'dispute_policy_label'),
            'contact_proximaride_label' => $this->data($request, $language, 'contact_proximaride_label'),
            'logout_label' => $this->data($request, $language, 'logout_label'),
            'colse_your_contact_label' => $this->data($request, $language, 'colse_your_contact_label'),
            // 'profile_page_setting' => $this->data($request, $language, 'profile_page_setting'),
        ];
    }

    public function update($profilePageSetting, $language, $request)
    {
        $fields = $this->fields($profilePageSetting, $language, $request);
        $profilePageSettingDetail = ProfilePageSettingDetail::whereProfilePageSettingId($profilePageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$profilePageSettingDetail){
            $fields = $this->fields($profilePageSetting, $language, $request);
            ProfilePageSettingDetail::create($fields);
        }
        else{
            ProfilePageSettingDetail::whereProfilePageSettingId($profilePageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
