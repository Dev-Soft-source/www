<?php

namespace App\Services;

use App\Models\PayoutOptionSettingDetail;

class PayoutSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['bank_detail_heading.bank_detail_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['bank_detail_heading.bank_detail_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_indicate_required_field_label.mobile_indicate_required_field_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_indicate_required_field_label.mobile_indicate_required_field_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['paypal_detail_heading.paypal_detail_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['paypal_detail_heading.paypal_detail_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['web_bank_transfer_description.web_bank_transfer_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['web_bank_transfer_description.web_bank_transfer_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['web_payout_method_label.web_payout_method_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['web_payout_method_label.web_payout_method_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['web_payout_method_placeholder.web_payout_method_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['web_payout_method_placeholder.web_payout_method_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['bank_name_label.bank_name_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['bank_name_label.bank_name_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['bank_name_placeholder.bank_name_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['bank_name_placeholder.bank_name_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['bank_title_label.bank_title_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['bank_title_label.bank_title_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['bank_title_placeholder.bank_title_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['bank_title_placeholder.bank_title_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['account_number_label.account_number_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['account_number_label.account_number_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['account_number_placeholder.account_number_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['account_number_placeholder.account_number_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['branch_label.branch_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['branch_label.branch_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['branch_placeholder.branch_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['branch_placeholder.branch_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['address_label.address_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['address_label.address_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['address_placeholder.address_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['address_placeholder.address_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['admin_sent_amount_placeholder.admin_sent_amount_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['admin_sent_amount_placeholder.admin_sent_amount_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['set_default_checkbox_label.set_default_checkbox_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['set_default_checkbox_label.set_default_checkbox_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['verify_button_text.verify_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['verify_button_text.verify_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['paypal_account_heading.paypal_account_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['paypal_account_heading.paypal_account_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['paypal_account_sub_heading.paypal_account_sub_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['paypal_account_sub_heading.paypal_account_sub_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_paypal_indicate_required_label.mobile_paypal_indicate_required_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_paypal_indicate_required_label.mobile_paypal_indicate_required_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['paypal_email_label.paypal_email_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['paypal_email_label.paypal_email_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['paypal_email_placeholder.paypal_email_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['paypal_email_placeholder.paypal_email_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['paypal_set_default_checkbox_label.paypal_set_default_checkbox_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['paypal_set_default_checkbox_label.paypal_set_default_checkbox_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['institution_number_label.institution_number_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['institution_number_label.institution_number_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['institution_number_placeholder.institution_number_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['institution_number_placeholder.institution_number_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['branch_address_label.branch_address_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['branch_address_label.branch_address_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['branch_number_label.branch_number_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['branch_number_label.branch_number_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['branch_address_placeholder.branch_address_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['branch_address_placeholder.branch_address_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['account_address_placeholder.account_address_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['account_address_placeholder.account_address_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($payoutSetting, $language, $request)
    {
        return [
            'payout_opt_setting_id' => $payoutSetting->id,
            'language_id' => $language->id,
            'bank_detail_heading' => $this->data($request, $language, 'bank_detail_heading'),
            'mobile_indicate_required_field_label' => $this->data($request, $language, 'mobile_indicate_required_field_label'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'paypal_detail_heading' => $this->data($request, $language, 'paypal_detail_heading'),
            'web_bank_transfer_description' => $this->data($request, $language, 'web_bank_transfer_description'),
            'web_paypal_transfer_description' => $this->data($request, $language, 'web_paypal_transfer_description'),
            'web_payout_method_label' => $this->data($request, $language, 'web_payout_method_label'),
            'web_payout_method_placeholder' => $this->data($request, $language, 'web_payout_method_placeholder'),
            'bank_name_label' => $this->data($request, $language, 'bank_name_label'),
            'bank_name_placeholder' => $this->data($request, $language, 'bank_name_placeholder'),
            'bank_title_label' => $this->data($request, $language, 'bank_title_label'),
            'bank_title_placeholder' => $this->data($request, $language, 'bank_title_placeholder'),
            'account_number_label' => $this->data($request, $language, 'account_number_label'),
            'account_number_placeholder' => $this->data($request, $language, 'account_number_placeholder'),
            'branch_label' => $this->data($request, $language, 'branch_label'),
            'branch_placeholder' => $this->data($request, $language, 'branch_placeholder'),
            'address_label' => $this->data($request, $language, 'address_label'),
            'address_placeholder' => $this->data($request, $language, 'address_placeholder'),
            'admin_sent_amount_placeholder' => $this->data($request, $language, 'admin_sent_amount_placeholder'),
            'set_default_checkbox_label' => $this->data($request, $language, 'set_default_checkbox_label'),
            'verify_button_text' => $this->data($request, $language, 'verify_button_text'),
            'paypal_account_heading' => $this->data($request, $language, 'paypal_account_heading'),
            'paypal_account_sub_heading' => $this->data($request, $language, 'paypal_account_sub_heading'),
            'mobile_paypal_indicate_required_label' => $this->data($request, $language, 'mobile_paypal_indicate_required_label'),
            'paypal_email_label' => $this->data($request, $language, 'paypal_email_label'),
            'paypal_email_placeholder' => $this->data($request, $language, 'paypal_email_placeholder'),
            'paypal_set_default_checkbox_label' => $this->data($request, $language, 'paypal_set_default_checkbox_label'),
            'bank_account_heading' => $this->data($request, $language, 'bank_account_heading'),
            'update_btn_label' => $this->data($request, $language, 'update_btn_label'),
            'save_btn_label' => $this->data($request, $language, 'save_btn_label'),

            'account_address_placeholder' => $this->data($request, $language, 'account_address_placeholder'),
            'branch_address_placeholder' => $this->data($request, $language, 'branch_address_placeholder'),
            'branch_number_label' => $this->data($request, $language, 'branch_number_label'),
            'branch_address_label' => $this->data($request, $language, 'branch_address_label'),
            'institution_number_label' => $this->data($request, $language, 'institution_number_label'),
            'institution_number_placeholder' => $this->data($request, $language, 'institution_number_placeholder'),
            'branch_number_placeholder' => $this->data($request, $language, 'branch_number_placeholder'),


            'bank_error' => $this->data($request, $language, 'bank_error'),
            'institute_no_error' => $this->data($request, $language, 'institute_no_error'),
            'branch_error' => $this->data($request, $language, 'branch_error'),
            'branch_address_error' => $this->data($request, $language, 'branch_address_error'),
            'branch_no_error' => $this->data($request, $language, 'branch_no_error'),
            'bank_title_error' => $this->data($request, $language, 'bank_title_error'),
            'acc_no_error' => $this->data($request, $language, 'acc_no_error'),
            'address_error' => $this->data($request, $language, 'address_error'),

        ];
    }

    public function update($payoutSetting, $language, $request)
    {
        $fields = $this->fields($payoutSetting, $language, $request);
        $payoutSettingDetail = PayoutOptionSettingDetail::wherePayoutOptSettingId($payoutSetting->id)->whereLanguageId($language->id)->exists();
        if(!$payoutSettingDetail){
            $fields = $this->fields($payoutSetting, $language, $request);
            PayoutOptionSettingDetail::create($fields);
        }
        else{
            PayoutOptionSettingDetail::wherePayoutOptSettingId($payoutSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
