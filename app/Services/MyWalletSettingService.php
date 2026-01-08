<?php

namespace App\Services;

use App\Models\MyWalletSettingDetail;

class MyWalletSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['card_heading.card_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['card_heading.card_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_heading.passenger_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_heading.passenger_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_heading.driver_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_heading.driver_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['balance_heading.balance_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['balance_heading.balance_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_my_ride_heading.passenger_my_ride_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_my_ride_heading.passenger_my_ride_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_my_ride_from_label.passenger_my_ride_from_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_my_ride_from_label.passenger_my_ride_from_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_my_ride_date_label.passenger_my_ride_date_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_my_ride_date_label.passenger_my_ride_date_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_my_ride_booking_fee_label.passenger_my_ride_booking_fee_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_my_ride_booking_fee_label.passenger_my_ride_booking_fee_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_my_ride_fare_label.passenger_my_ride_fare_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_my_ride_fare_label.passenger_my_ride_fare_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_my_ride_total_amount_label.passenger_my_ride_total_amount_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_my_ride_total_amount_label.passenger_my_ride_total_amount_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_my_reward_heading.passenger_my_reward_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_my_reward_heading.passenger_my_reward_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_my_reward_description.passenger_my_reward_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_my_reward_description.passenger_my_reward_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_my_ride_to_label.passenger_my_ride_to_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_my_ride_to_label.passenger_my_ride_to_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_my_reward_reward_table_label.passenger_my_reward_reward_table_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_my_reward_reward_table_label.passenger_my_reward_reward_table_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_my_reward_to_label.passenger_my_reward_to_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_my_reward_to_label.passenger_my_reward_to_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_paid_out_heading.driver_paid_out_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_paid_out_heading.driver_paid_out_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_availabe_heading.driver_availabe_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_availabe_heading.driver_availabe_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_paid_ride_id_label.driver_paid_ride_id_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_paid_ride_id_label.driver_paid_ride_id_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_paid_from_label.driver_paid_from_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_paid_from_label.driver_paid_from_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_paid_to_label.driver_paid_to_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_paid_to_label.driver_paid_to_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_paid_paid_out_date_label.driver_paid_paid_out_date_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_paid_paid_out_date_label.driver_paid_paid_out_date_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_paid_total_amount_label.driver_paid_total_amount_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_paid_total_amount_label.driver_paid_total_amount_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_available_ride_id_label.driver_available_ride_id_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_available_ride_id_label.driver_available_ride_id_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_available_from_label.driver_available_from_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_available_from_label.driver_available_from_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_available_to_label.driver_available_to_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_available_to_label.driver_available_to_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_available_date_label.driver_available_date_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_available_date_label.driver_available_date_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_available_total_amount_label.driver_available_total_amount_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_available_total_amount_label.driver_available_total_amount_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_pending_heading.driver_pending_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_pending_heading.driver_pending_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_pending_data_description.driver_pending_data_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_pending_data_description.driver_pending_data_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_reward_heading.driver_reward_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_reward_heading.driver_reward_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_reward_description.driver_reward_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_reward_description.driver_reward_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_reward_points_table_label.driver_reward_points_table_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_reward_points_table_label.driver_reward_points_table_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_reward_reward_table_label.driver_reward_reward_table_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_reward_reward_table_label.driver_reward_reward_table_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_reward_to_label.driver_reward_to_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_reward_to_label.driver_reward_to_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['balance_id_label.balance_id_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['balance_id_label.balance_id_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['balance_amount_label.balance_amount_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['balance_amount_label.balance_amount_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['balance_date_label.balance_date_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['balance_date_label.balance_date_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['balance_buy_more_button_text.balance_buy_more_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['balance_buy_more_button_text.balance_buy_more_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['passenger_my_reward_description1.passenger_my_reward_description1_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_my_reward_description1.passenger_my_reward_description1_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['driver_my_reward_description1.driver_my_reward_description1_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_my_reward_description1.driver_my_reward_description1_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['claim_my_reward_button_text.claim_my_reward_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['claim_my_reward_button_text.claim_my_reward_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($myWalletSetting, $language, $request)
    {
        return [
            'wallet_setting_id' => $myWalletSetting->id,
            'language_id' => $language->id,
            'card_heading' => $this->data($request, $language, 'card_heading'),
            'passenger_heading' => $this->data($request, $language, 'passenger_heading'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'driver_heading' => $this->data($request, $language, 'driver_heading'),
            'balance_heading' => $this->data($request, $language, 'balance_heading'),
            'passenger_my_ride_heading' => $this->data($request, $language, 'passenger_my_ride_heading'),
            'passenger_ride_id_label' => $this->data($request, $language, 'passenger_ride_id_label'),
            'passenger_my_ride_from_label' => $this->data($request, $language, 'passenger_my_ride_from_label'),
            'passenger_my_ride_date_label' => $this->data($request, $language, 'passenger_my_ride_date_label'),
            'passenger_my_ride_booking_fee_label' => $this->data($request, $language, 'passenger_my_ride_booking_fee_label'),
            'passenger_my_ride_fare_label' => $this->data($request, $language, 'passenger_my_ride_fare_label'),
            'passenger_my_ride_total_amount_label' => $this->data($request, $language, 'passenger_my_ride_total_amount_label'),
            'passenger_my_reward_heading' => $this->data($request, $language, 'passenger_my_reward_heading'),
            'passenger_my_reward_description' => $this->data($request, $language, 'passenger_my_reward_description'),
            'passenger_my_ride_to_label' => $this->data($request, $language, 'passenger_my_ride_to_label'),
            'passenger_my_reward_points_table_label' => $this->data($request, $language, 'passenger_my_reward_points_table_label'),
            'passenger_my_reward_reward_table_label' => $this->data($request, $language, 'passenger_my_reward_reward_table_label'),
            'passenger_my_reward_to_label' => $this->data($request, $language, 'passenger_my_reward_to_label'),
            'driver_paid_out_heading' => $this->data($request, $language, 'driver_paid_out_heading'),
            'driver_availabe_heading' => $this->data($request, $language, 'driver_availabe_heading'),
            'driver_paid_ride_id_label' => $this->data($request, $language, 'driver_paid_ride_id_label'),
            'driver_paid_from_label' => $this->data($request, $language, 'driver_paid_from_label'),
            'driver_paid_to_label' => $this->data($request, $language, 'driver_paid_to_label'),
            'driver_paid_paid_out_date_label' => $this->data($request, $language, 'driver_paid_paid_out_date_label'),
            'driver_paid_total_amount_label' => $this->data($request, $language, 'driver_paid_total_amount_label'),
            'driver_available_ride_id_label' => $this->data($request, $language, 'driver_available_ride_id_label'),
            'driver_available_from_label' => $this->data($request, $language, 'driver_available_from_label'),
            'driver_available_to_label' => $this->data($request, $language, 'driver_available_to_label'),
            'driver_available_date_label' => $this->data($request, $language, 'driver_available_date_label'),
            'driver_available_total_amount_label' => $this->data($request, $language, 'driver_available_total_amount_label'),
            'driver_pending_heading' => $this->data($request, $language, 'driver_pending_heading'),
            'driver_pending_data_description' => $this->data($request, $language, 'driver_pending_data_description'),
            'driver_reward_heading' => $this->data($request, $language, 'driver_reward_heading'),
            'driver_reward_description' => $this->data($request, $language, 'driver_reward_description'),
            'driver_reward_points_table_label' => $this->data($request, $language, 'driver_reward_points_table_label'),
            'driver_reward_reward_table_label' => $this->data($request, $language, 'driver_reward_reward_table_label'),
            'driver_reward_to_label' => $this->data($request, $language, 'driver_reward_to_label'),
            'balance_id_label' => $this->data($request, $language, 'balance_id_label'),
            'balance_amount_label' => $this->data($request, $language, 'balance_amount_label'),
            'balance_date_label' => $this->data($request, $language, 'balance_date_label'),
            'balance_buy_more_button_text' => $this->data($request, $language, 'balance_buy_more_button_text'),
            'no_more_data_message' => $this->data($request, $language, 'no_more_data_message'),
            'no_my_ride_message' => $this->data($request, $language, 'no_my_ride_message'),
            'no_reward_found_message' => $this->data($request, $language, 'no_reward_found_message'),
            'no_paid_out_message' => $this->data($request, $language, 'no_paid_out_message'),
            'no_balance_found_message' => $this->data($request, $language, 'no_balance_found_message'),
            'request_transfer_label' => $this->data($request, $language, 'request_transfer_label'),
            'driver_pending_date_label' => $this->data($request, $language, 'driver_pending_date_label'),
            'no_pending_found_message' => $this->data($request, $language, 'no_pending_found_message'),
            'no_driver_found_message' => $this->data($request, $language, 'no_driver_found_message'),
            'ride_fare_main_heading' => $this->data($request, $language, 'ride_fare_main_heading'),
            'top_up_main_heading' => $this->data($request, $language, 'top_up_main_heading'),
            'purchase_top_up_label' => $this->data($request, $language, 'purchase_top_up_label'),
            'purchase_top_up_placeholder' => $this->data($request, $language, 'purchase_top_up_placeholder'),
            'purchase_top_up_error' => $this->data($request, $language, 'purchase_top_up_error'),
            'pay_with_label' => $this->data($request, $language, 'pay_with_label'),
            'must_add_amount_toltip' => $this->data($request, $language, 'must_add_amount_toltip'),
            'passenger_label' => $this->data($request, $language, 'passenger_label'),
            'fare_label' => $this->data($request, $language, 'fare_label'),
            'booking_fee_label' => $this->data($request, $language, 'booking_fee_label'),
            'total_label' => $this->data($request, $language, 'total_label'),
            'credit_card_label' => $this->data($request, $language, 'credit_card_label'),
            'passenger_my_reward_description1' => $this->data($request, $language, 'passenger_my_reward_description1'),
            'driver_my_reward_description1' => $this->data($request, $language, 'driver_my_reward_description1'),
            'claim_my_reward_button_text' => $this->data($request, $language, 'claim_my_reward_button_text'),

        ];
    }

    public function update($myWalletSetting, $language, $request)
    {
        $fields = $this->fields($myWalletSetting, $language, $request);
        $myWalletSettingDetail = MyWalletSettingDetail::whereWalletSettingId($myWalletSetting->id)->whereLanguageId($language->id)->exists();
        if(!$myWalletSettingDetail){
            $fields = $this->fields($myWalletSetting, $language, $request);
            MyWalletSettingDetail::create($fields);
        }
        else{
            MyWalletSettingDetail::whereWalletSettingId($myWalletSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
