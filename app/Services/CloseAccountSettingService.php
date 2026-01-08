<?php

namespace App\Services;

use App\Models\CloseAccountSettingDetail;

class CloseAccountSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['warning_text.warning_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['warning_text.warning_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_indicate_required_field_label.mobile_indicate_required_field_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_indicate_required_field_label.mobile_indicate_required_field_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['closing_account_label.closing_account_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['closing_account_label.closing_account_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['apply_reason_label.apply_reason_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['apply_reason_label.apply_reason_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reason_label.reason_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reason_label.reason_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['customer_service_checkbox_label.customer_service_checkbox_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['customer_service_checkbox_label.customer_service_checkbox_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['dont_use_checkbox_label.dont_use_checkbox_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['dont_use_checkbox_label.dont_use_checkbox_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['another_account_checkbox_label.another_account_checkbox_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['another_account_checkbox_label.another_account_checkbox_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['did_not_get_booking_checkbox_label.did_not_get_booking_checkbox_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['did_not_get_booking_checkbox_label.did_not_get_booking_checkbox_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['did_not_find_ride_checkbox_label.did_not_find_ride_checkbox_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['did_not_find_ride_checkbox_label.did_not_find_ride_checkbox_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['did_not_find_destination_checkbox_label.did_not_find_destination_checkbox_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['did_not_find_destination_checkbox_label.did_not_find_destination_checkbox_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['other_checkbox_label.other_checkbox_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['other_checkbox_label.other_checkbox_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['recommend_heading.recommend_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['recommend_heading.recommend_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['yes_checkbox_label.yes_checkbox_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['yes_checkbox_label.yes_checkbox_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_checkbox_label.no_checkbox_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_checkbox_label.no_checkbox_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['prefer_not_checkbox_label.prefer_not_checkbox_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['prefer_not_checkbox_label.prefer_not_checkbox_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['why_closing_account_label.why_closing_account_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['why_closing_account_label.why_closing_account_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['why_closing_account_placeholder.why_closing_account_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['why_closing_account_placeholder.why_closing_account_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['improve_label.improve_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['improve_label.improve_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['improve_placeholder.improve_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['improve_placeholder.improve_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['close_my_account_checkbox.close_my_account_checkbox_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['close_my_account_checkbox.close_my_account_checkbox_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['close_my_account_checkbox_error.close_my_account_checkbox_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['close_my_account_checkbox_error.close_my_account_checkbox_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['close_account_button_text.close_account_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['close_account_button_text.close_account_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['difficulties_making_receiving_payments_label.difficulties_making_receiving_payments_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['difficulties_making_receiving_payments_label.difficulties_making_receiving_payments_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
    
                $validationRule = array_merge($validationRule, ['web_closing_account_reason_label.web_closing_account_reason_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['web_closing_account_reason_label.web_closing_account_reason_label' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['web_irreversible_label.web_irreversible_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['web_irreversible_label.web_irreversible_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['close_account_sure_message_text.close_account_sure_message_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['close_account_sure_message_text.close_account_sure_message_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['close_it_button_label.close_it_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['close_it_button_label.close_it_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['take_me_back_button_label.take_me_back_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['take_me_back_button_label.take_me_back_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($closeAccountSetting, $language, $request)
    {
        return [
            'close_acc_setting_id' => $closeAccountSetting->id,
            'language_id' => $language->id,
            'warning_text' => $this->data($request, $language, 'warning_text'),
            'mobile_indicate_required_field_label' => $this->data($request, $language, 'mobile_indicate_required_field_label'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'closing_account_label' => $this->data($request, $language, 'closing_account_label'),
            'apply_reason_label' => $this->data($request, $language, 'apply_reason_label'),
            'reason_label' => $this->data($request, $language, 'reason_label'),
            'not_say_checkbox_label' => $this->data($request, $language, 'not_say_checkbox_label'),
            'check_box_validation_message' => $this->data($request, $language, 'check_box_validation_message'),
            'customer_service_checkbox_label' => $this->data($request, $language, 'customer_service_checkbox_label'),
            'technical_issue_checkbox_label' => $this->data($request, $language, 'technical_issue_checkbox_label'),
            'dont_use_checkbox_label' => $this->data($request, $language, 'dont_use_checkbox_label'),
            'another_account_checkbox_label' => $this->data($request, $language, 'another_account_checkbox_label'),
            'did_not_get_booking_checkbox_label' => $this->data($request, $language, 'did_not_get_booking_checkbox_label'),
            'did_not_find_ride_checkbox_label' => $this->data($request, $language, 'did_not_find_ride_checkbox_label'),
            'did_not_find_destination_checkbox_label' => $this->data($request, $language, 'did_not_find_destination_checkbox_label'),
            'other_checkbox_label' => $this->data($request, $language, 'other_checkbox_label'),
            'recommend_heading' => $this->data($request, $language, 'recommend_heading'),
            'yes_checkbox_label' => $this->data($request, $language, 'yes_checkbox_label'),
            'no_checkbox_label' => $this->data($request, $language, 'no_checkbox_label'),
            'prefer_not_checkbox_label' => $this->data($request, $language, 'prefer_not_checkbox_label'),
            'why_closing_account_label' => $this->data($request, $language, 'why_closing_account_label'),
            'why_closing_account_placeholder' => $this->data($request, $language, 'why_closing_account_placeholder'),
            'improve_label' => $this->data($request, $language, 'improve_label'),
            'improve_placeholder' => $this->data($request, $language, 'improve_placeholder'),
            'close_my_account_checkbox' => $this->data($request, $language, 'close_my_account_checkbox'),
            'close_my_account_checkbox_error' => $this->data($request, $language, 'close_my_account_checkbox_error'),
            'close_account_button_text' => $this->data($request, $language, 'close_account_button_text'),
            'difficulties_making_receiving_payments_label' => $this->data($request, $language, 'difficulties_making_receiving_payments_label'),
            'take_me_back_button_label' => $this->data($request, $language, 'take_me_back_button_label'),
            'close_it_button_label' => $this->data($request, $language, 'close_it_button_label'),
            'close_account_sure_message_text' => $this->data($request, $language, 'close_account_sure_message_text'),
            'web_irreversible_label' => $this->data($request, $language, 'web_irreversible_label'),
            'web_closing_account_reason_label' => $this->data($request, $language, 'web_closing_account_reason_label'),
        ];
    }

    public function update($closeAccountSetting, $language, $request)
    {
        $fields = $this->fields($closeAccountSetting, $language, $request);
        $closeAccountSettingDetail = CloseAccountSettingDetail::whereCloseAccSettingId($closeAccountSetting->id)->whereLanguageId($language->id)->exists();
        if(!$closeAccountSettingDetail){
            $fields = $this->fields($closeAccountSetting, $language, $request);
            CloseAccountSettingDetail::create($fields);
        }
        else{
            CloseAccountSettingDetail::whereCloseAccSettingId($closeAccountSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
