<?php

namespace App\Services;

use App\Models\TripsPageSettingDetail;

class TripsPageSettingService
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
                $validationRule = array_merge($validationRule, ['passenger_trips_heading.passenger_trips_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_trips_heading.passenger_trips_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_rides_heading.driver_rides_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_rides_heading.driver_rides_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['upcoming_label.upcoming_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['upcoming_label.upcoming_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_upcoming_trips_label.no_upcoming_trips_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_upcoming_trips_label.no_upcoming_trips_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_upcoming_rides_label.no_upcoming_rides_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_upcoming_rides_label.no_upcoming_rides_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['completed_label.completed_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['completed_label.completed_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_completed_trips_label.no_completed_trips_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_completed_trips_label.no_completed_trips_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_completed_rides_label.no_completed_rides_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_completed_rides_label.no_completed_rides_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancelled_label.cancelled_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancelled_label.cancelled_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_cancelled_trips_label.no_cancelled_trips_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_cancelled_trips_label.no_cancelled_trips_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_cancelled_rides_label.no_cancelled_rides_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_cancelled_rides_label.no_cancelled_rides_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['cancel_message_title.cancel_message_title_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancel_message_title.cancel_message_title_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancel_booking_confirm_message.cancel_booking_confirm_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancel_booking_confirm_message.cancel_booking_confirm_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_cancel_btn_yes_label.booking_cancel_btn_yes_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_cancel_btn_yes_label.booking_cancel_btn_yes_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_cancel_btn_no_label.booking_cancel_btn_no_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_cancel_btn_no_label.booking_cancel_btn_no_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['cancel_booking_confirm_firm_message.cancel_booking_confirm_firm_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancel_booking_confirm_firm_message.cancel_booking_confirm_firm_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancel_booking_confirm_48_hour_message.cancel_booking_confirm_48_hour_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancel_booking_confirm_48_hour_message.cancel_booking_confirm_48_hour_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancel_booking_confirm_12_to_48_hour_message.cancel_booking_confirm_12_to_48_hour_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancel_booking_confirm_12_to_48_hour_message.cancel_booking_confirm_12_to_48_hour_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancel_booking_confirm_less_12_hour_message.cancel_booking_confirm_less_12_hour_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancel_booking_confirm_less_12_hour_message.cancel_booking_confirm_less_12_hour_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($tripsPageSetting, $language, $request)
    {
        return [
            'trips_page_setting_id' => $tripsPageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'passenger_trips_heading' => $this->data($request, $language, 'passenger_trips_heading'),
            'driver_rides_heading' => $this->data($request, $language, 'driver_rides_heading'),
            'upcoming_label' => $this->data($request, $language, 'upcoming_label'),
            'no_upcoming_trips_label' => $this->data($request, $language, 'no_upcoming_trips_label'),
            'no_upcoming_rides_label' => $this->data($request, $language, 'no_upcoming_rides_label'),
            'completed_label' => $this->data($request, $language, 'completed_label'),
            'no_completed_trips_label' => $this->data($request, $language, 'no_completed_trips_label'),
            'no_completed_rides_label' => $this->data($request, $language, 'no_completed_rides_label'),
            'cancelled_label' => $this->data($request, $language, 'cancelled_label'),
            'no_cancelled_trips_label' => $this->data($request, $language, 'no_cancelled_trips_label'),
            'no_cancelled_rides_label' => $this->data($request, $language, 'no_cancelled_rides_label'),
            'timeliness_label' => $this->data($request, $language, 'timeliness_label'),
            'safety_label' => $this->data($request, $language, 'safety_label'),
            'respect_and_courtesy_label' => $this->data($request, $language, 'respect_and_courtesy_label'),
            'personal_hygiene_label' => $this->data($request, $language, 'personal_hygiene_label'),
            'overall_attitude_label' => $this->data($request, $language, 'overall_attitude_label'),
            'communication_label' => $this->data($request, $language, 'communication_label'),
            'comfort_label' => $this->data($request, $language, 'comfort_label'),
            'conscious_passenger_wellness_label' => $this->data($request, $language, 'conscious_passenger_wellness_label'),
            'condition_label' => $this->data($request, $language, 'condition_label'),
            'review_criteria_label' => $this->data($request, $language, 'review_criteria_label'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'average_label' => $this->data($request, $language, 'average_label'),
            'load_more_trips_label' => $this->data($request, $language, 'load_more_trips_label'),
            'no_more_data_message' => $this->data($request, $language, 'no_more_data_message'),
            'load_more_rides_label' => $this->data($request, $language, 'load_more_rides_label'),
            'review_passengers_review_label' => $this->data($request, $language, 'review_passengers_review_label'),
            'review_passengers_i_review_label' => $this->data($request, $language, 'review_passengers_i_review_label'),
            'review_passengers_heading' => $this->data($request, $language, 'review_passengers_heading'),
            'passenger_cancel_ride_btn_label' => $this->data($request, $language, 'passenger_cancel_ride_btn_label'),
            'booking_cancel_btn_label' => $this->data($request, $language, 'booking_cancel_btn_label'),
            'cancel_booking_trip_placeholder' => $this->data($request, $language, 'cancel_booking_trip_placeholder'),
            'cancel_all_feilds_are_required' => $this->data($request, $language, 'cancel_all_feilds_are_required'),
            'cancel_ride_label' => $this->data($request, $language, 'cancel_ride_label'),
            'cancel_ride_placeholder' => $this->data($request, $language, 'cancel_ride_placeholder'),
            'cancel_seat_label' => $this->data($request, $language, 'cancel_seat_label'),
            'number_of_seat_booked' => $this->data($request, $language, 'number_of_seat_booked'),
            'cancel_booking_heading' => $this->data($request, $language, 'cancel_booking_heading'),
            'cancel_booking_main_heading' => $this->data($request, $language, 'cancel_booking_main_heading'),
            'cancel_ride_setting' => $this->data($request, $language, 'cancel_ride_setting'),
            'tell_passenger_why_label' => $this->data($request, $language, 'tell_passenger_why_label'),
            'tell_passenger_why_placeholder' => $this->data($request, $language, 'tell_passenger_why_placeholder'),
            'Confirm_cancel_ride' => $this->data($request, $language, 'Confirm_cancel_ride'),
            'remove_from_this_ride_message' => $this->data($request, $language, 'remove_from_this_ride_message'),
            'remove_passenger_and_block_message' => $this->data($request, $language, 'remove_passenger_and_block_message'),
            'remove_day_label' => $this->data($request, $language, 'remove_day_label'),
            'remove_day_error' => $this->data($request, $language, 'remove_day_error'),
            'driver_remove_reason_placeholder' => $this->data($request, $language, 'driver_remove_reason_placeholder'),
            'passenger_remove_reason_placeholder' => $this->data($request, $language, 'passenger_remove_reason_placeholder'),
            'passenger_review_heading' => $this->data($request, $language, 'passenger_review_heading'),
            'driver_review_heading' => $this->data($request, $language, 'driver_review_heading'),
            'passenger_review_placeholder' => $this->data($request, $language, 'passenger_review_placeholder'),
            'driver_review_placeholder' => $this->data($request, $language, 'driver_review_placeholder'),
            'review_submit_btn_label' => $this->data($request, $language, 'review_submit_btn_label'),
            'remove_passenger_heading' => $this->data($request, $language, 'remove_passenger_heading'),
            'remove_passenger_text' => $this->data($request, $language, 'remove_passenger_text'),
            'block_temporarily_label' => $this->data($request, $language, 'block_temporarily_label'),
            'block_permanently_label' => $this->data($request, $language, 'block_permanently_label'),
            'remove_day_placeholder' => $this->data($request, $language, 'remove_day_placeholder'),
            'driver_remove_reason_label' => $this->data($request, $language, 'driver_remove_reason_label'),
            'driver_remove_reason_error' => $this->data($request, $language, 'driver_remove_reason_error'),
            'passenger_remove_reason_label' => $this->data($request, $language, 'passenger_remove_reason_label'),
            'passenger_remove_reason_error' => $this->data($request, $language, 'passenger_remove_reason_error'),
            'passenger_cancel_sure_message' => $this->data($request, $language, 'passenger_cancel_sure_message'),
            'cancel_message_title' => $this->data($request, $language, 'cancel_message_title'),
            'cancel_booking_confirm_message' => $this->data($request, $language, 'cancel_booking_confirm_message'),
            'booking_cancel_btn_yes_label' => $this->data($request, $language, 'booking_cancel_btn_yes_label'),
            'booking_cancel_btn_no_label' => $this->data($request, $language, 'booking_cancel_btn_no_label'),
            'cancel_booking_confirm_firm_message' => $this->data($request, $language, 'cancel_booking_confirm_firm_message'),
            'cancel_booking_confirm_48_hour_message' => $this->data($request, $language, 'cancel_booking_confirm_48_hour_message'),
            'cancel_booking_confirm_12_to_48_hour_message' => $this->data($request, $language, 'cancel_booking_confirm_12_to_48_hour_message'),
            'cancel_booking_confirm_less_12_hour_message' => $this->data($request, $language, 'cancel_booking_confirm_less_12_hour_message'),

        ];
    }

    public function update($tripsPageSetting, $language, $request)
    {
        $fields = $this->fields($tripsPageSetting, $language, $request);
        $tripsPageSettingDetail = TripsPageSettingDetail::whereTripsPageSettingId($tripsPageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$tripsPageSettingDetail){
            $fields = $this->fields($tripsPageSetting, $language, $request);
        TripsPageSettingDetail::create($fields);
        }
        else{
            TripsPageSettingDetail::whereTripsPageSettingId($tripsPageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
