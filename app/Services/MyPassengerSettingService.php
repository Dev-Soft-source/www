<?php

namespace App\Services;

use App\Models\MyPassengerSettingDetail;

class MyPassengerSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['chat_passenger_btn_label.chat_passenger_btn_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['chat_passenger_btn_label.chat_passenger_btn_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['total_amount_label.total_amount_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['total_amount_label.total_amount_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['my_fare_label.my_fare_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['my_fare_label.my_fare_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_fee_label.booking_fee_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_fee_label.booking_fee_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['review_profile_label.review_profile_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['review_profile_label.review_profile_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['age.age_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['age.age_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['gender.gender_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['gender.gender_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['co_passenger_main_heading.co_passenger_main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['co_passenger_main_heading.co_passenger_main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['web_back_button_label.web_back_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['web_back_button_label.web_back_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_show_passenger_label.no_show_passenger_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_show_passenger_label.no_show_passenger_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['web_i_reviewed_label.web_i_reviewed_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['web_i_reviewed_label.web_i_reviewed_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['web_reviewd_label.web_reviewd_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['web_reviewd_label.web_reviewd_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['revert_no_show_passenger_label.revert_no_show_passenger_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['revert_no_show_passenger_label.revert_no_show_passenger_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($myPassengerSetting, $language, $request)
    {
        // dd($myPassengerSetting);
        return [
            'my_passenger_setting_id' => $myPassengerSetting->id,
            'language_id' => $language->id,
            'remove_ride_btn_label' => $this->data($request, $language, 'remove_ride_btn_label'),
            'chat_passenger_btn_label' => $this->data($request, $language, 'chat_passenger_btn_label'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'total_amount_label' => $this->data($request, $language, 'total_amount_label'),
            'my_fare_label' => $this->data($request, $language, 'my_fare_label'),
            'booking_fee_label' => $this->data($request, $language, 'booking_fee_label'),
            'seat_booked_label' => $this->data($request, $language, 'seat_booked_label'),
            'review_profile_label' => $this->data($request, $language, 'review_profile_label'),
            'age' => $this->data($request, $language, 'age'),
            'gender' => $this->data($request, $language, 'gender'),
            'co_passenger_main_heading' => $this->data($request, $language, 'co_passenger_main_heading'),
            'web_back_button_label' => $this->data($request, $language, 'web_back_button_label'),
            'no_show_passenger_label' => $this->data($request, $language, 'no_show_passenger_label'),
            'web_i_reviewed_label' => $this->data($request, $language, 'web_i_reviewed_label'),
            'web_reviewd_label' => $this->data($request, $language, 'web_reviewd_label'),
            'revert_no_show_passenger_label' => $this->data($request, $language, 'revert_no_show_passenger_label'),
        ];
    }

    public function update($myPassengerSetting, $language, $request)
    {
        $fields = $this->fields($myPassengerSetting, $language, $request);
        $myPassengerSettingDetail = MyPassengerSettingDetail::whereMyPassengerSettingId($myPassengerSetting->id)->whereLanguageId($language->id)->exists();
        if(!$myPassengerSettingDetail){
            $fields = $this->fields($myPassengerSetting, $language, $request);
            MyPassengerSettingDetail::create($fields);
        }
        else{
            MyPassengerSettingDetail::whereMyPassengerSettingId($myPassengerSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
