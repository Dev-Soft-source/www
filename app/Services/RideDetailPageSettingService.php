<?php

namespace App\Services;

use App\Models\RideDetailPageSettingDetail;

class RideDetailPageSettingService
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
                $validationRule = array_merge($validationRule, ['from_label.from_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['from_label.from_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['to_label.to_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['to_label.to_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['at_label.at_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['at_label.at_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['co_passenger_label.co_passenger_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['co_passenger_label.co_passenger_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['ride_co_passenger_heading.ride_co_passenger_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['ride_co_passenger_heading.ride_co_passenger_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['trip_co_passenger_heading.trip_co_passenger_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['trip_co_passenger_heading.trip_co_passenger_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['payment_method_label.payment_method_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['payment_method_label.payment_method_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['luggage_label.luggage_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['luggage_label.luggage_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['smoking_label.smoking_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['smoking_label.smoking_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['pets_label.pets_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['pets_label.pets_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['seats_left_label.seats_left_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['seats_left_label.seats_left_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['per_seat_label.per_seat_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['per_seat_label.per_seat_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['ride_features_label.ride_features_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['ride_features_label.ride_features_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['ride_seat_label.ride_seat_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['ride_seat_label.ride_seat_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['all_seats_booked_label.all_seats_booked_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['all_seats_booked_label.all_seats_booked_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['ride_canceller_by_driver.ride_canceller_by_driver_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['ride_canceller_by_driver.ride_canceller_by_driver_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['ride_completed_text.ride_completed_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['ride_completed_text.ride_completed_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['book_seat_btn_label.book_seat_btn_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['book_seat_btn_label.book_seat_btn_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_seat_available_label.no_seat_available_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_seat_available_label.no_seat_available_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_ride_found_message.no_ride_found_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_ride_found_message.no_ride_found_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancel_booking_btn_label.cancel_booking_btn_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancel_booking_btn_label.cancel_booking_btn_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancel_ride_btn_label.cancel_ride_btn_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancel_ride_btn_label.cancel_ride_btn_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancel_ride_confirmation.cancel_ride_confirmation_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancel_ride_confirmation.cancel_ride_confirmation_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancel_ride_yes_btn.cancel_ride_yes_btn_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancel_ride_yes_btn.cancel_ride_yes_btn_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancel_ride_no_btn.cancel_ride_no_btn_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancel_ride_no_btn.cancel_ride_no_btn_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['edit_ride_btn_label.edit_ride_btn_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['edit_ride_btn_label.edit_ride_btn_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['review_label.review_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['review_label.review_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_request_heading.booking_request_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_request_heading.booking_request_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['seat_requested_label.seat_requested_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['seat_requested_label.seat_requested_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['request_accept_label.request_accept_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['request_accept_label.request_accept_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['request_reject_label.request_reject_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['request_reject_label.request_reject_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['secured_cash_heading.secured_cash_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['secured_cash_heading.secured_cash_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['enter_code_label.enter_code_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['enter_code_label.enter_code_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_seat_booked_heading.mobile_seat_booked_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_seat_booked_heading.mobile_seat_booked_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_seat_booked_label.mobile_seat_booked_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_seat_booked_label.mobile_seat_booked_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_seat_fare_label.mobile_seat_fare_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_seat_fare_label.mobile_seat_fare_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_seat_booking_fee_label.mobile_seat_booking_fee_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_seat_booking_fee_label.mobile_seat_booking_fee_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_seat_total_amount_label.mobile_seat_total_amount_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_seat_total_amount_label.mobile_seat_total_amount_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['vehicle_info_label.vehicle_info_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['vehicle_info_label.vehicle_info_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_info_label.driver_info_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_info_label.driver_info_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['review_driver_info_label.review_driver_info_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['review_driver_info_label.review_driver_info_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['review_passanger_label.review_passanger_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['review_passanger_label.review_passanger_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                

                $validationRule = array_merge($validationRule, ['driver_chat_with.driver_chat_with_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_chat_with.driver_chat_with_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['driver_label.driver_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_label.driver_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancellation_policy.cancellation_policy_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancellation_policy.cancellation_policy_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passengers_driven_label.passengers_driven_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passengers_driven_label.passengers_driven_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_age_label.driver_age_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_age_label.driver_age_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_chat_heading.driver_chat_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_chat_heading.driver_chat_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_chat_label.driver_chat_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_chat_label.driver_chat_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_chat_button_label.driver_chat_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_chat_button_label.driver_chat_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_table_heading.booking_table_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_table_heading.booking_table_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_column_label.passenger_column_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_column_label.passenger_column_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['seat_booked_column_label.seat_booked_column_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['seat_booked_column_label.seat_booked_column_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['total_cost_column_label.total_cost_column_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['total_cost_column_label.total_cost_column_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booked_on_column_label.booked_on_column_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booked_on_column_label.booked_on_column_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['status_column_label.status_column_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['status_column_label.status_column_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_requested_status_label.booking_requested_status_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_requested_status_label.booking_requested_status_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['seat_booked_status_label.seat_booked_status_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['seat_booked_status_label.seat_booked_status_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_denied_status_label.booking_denied_status_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_denied_status_label.booking_denied_status_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['actions_column_label.actions_column_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['actions_column_label.actions_column_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['edit_button_actions_label.edit_button_actions_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['edit_button_actions_label.edit_button_actions_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['review_button_label.review_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['review_button_label.review_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['i_reviewed_label.i_reviewed_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['i_reviewed_label.i_reviewed_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['noon_label.noon_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['noon_label.noon_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['midnight_label.midnight_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['midnight_label.midnight_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_note_label.driver_note_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_note_label.driver_note_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['pickup_dropoff_info_heading.pickup_dropoff_info_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['pickup_dropoff_info_heading.pickup_dropoff_info_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['pickup_label.pickup_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['pickup_label.pickup_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['dropoff_label.dropoff_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['dropoff_label.dropoff_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['description_label.description_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['description_label.description_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_type_label.booking_type_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_type_label.booking_type_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancellation_policy_label.cancellation_policy_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancellation_policy_label.cancellation_policy_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['booking_request_main_heading.booking_request_main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_request_main_heading.booking_request_main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_age_label.passenger_age_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_age_label.passenger_age_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_gender_label.passenger_gender_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_gender_label.passenger_gender_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['seat_on_column_label.seat_on_column_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['seat_on_column_label.seat_on_column_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancellation_policy_tooltip.cancellation_policy_tooltip_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancellation_policy_tooltip.cancellation_policy_tooltip_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['cancellation_policy_tooltip_url.cancellation_policy_tooltip_url_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancellation_policy_tooltip_url.cancellation_policy_tooltip_url_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['firm_cancellation_confirm_poup_heading.firm_cancellation_confirm_poup_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['firm_cancellation_confirm_poup_heading.firm_cancellation_confirm_poup_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['firm_cancellation_confirm_poup_text.firm_cancellation_confirm_poup_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['firm_cancellation_confirm_poup_text.firm_cancellation_confirm_poup_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['firm_cancellation_confirm_poup_yes_label.firm_cancellation_confirm_poup_yes_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['firm_cancellation_confirm_poup_yes_label.firm_cancellation_confirm_poup_yes_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['firm_cancellation_confirm_poup_no_label.firm_cancellation_confirm_poup_no_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['firm_cancellation_confirm_poup_no_label.firm_cancellation_confirm_poup_no_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['firm_cancellation_confirm_poup_sub_text.firm_cancellation_confirm_poup_sub_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['firm_cancellation_confirm_poup_sub_text.firm_cancellation_confirm_poup_sub_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['instant_btn_label.instant_btn_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['instant_btn_label.instant_btn_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['chat_error_message.chat_error_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['chat_error_message.chat_error_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['empty_chat_placeholder.empty_chat_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['empty_chat_placeholder.empty_chat_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                
                $validationRule = array_merge($validationRule, ['verified_phone.verified_phone_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['verified_phone.verified_phone_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);


                $validationRule = array_merge($validationRule, ['verified_email.verified_email_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['verified_email.verified_email_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($rideDetailPageSetting, $language, $request)
    {
        return [
            'ride_detail_page_id' => $rideDetailPageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'from_label' => $this->data($request, $language, 'from_label'),
            'to_label' => $this->data($request, $language, 'to_label'),
            'at_label' => $this->data($request, $language, 'at_label'),
            'co_passenger_label' => $this->data($request, $language, 'co_passenger_label'),
            'ride_co_passenger_heading' => $this->data($request, $language, 'ride_co_passenger_heading'),
            'trip_co_passenger_heading' => $this->data($request, $language, 'trip_co_passenger_heading'),
            'payment_method_label' => $this->data($request, $language, 'payment_method_label'),
            'booking_type_label' => $this->data($request, $language, 'booking_type_label'),
            'cancellation_policy_label' => $this->data($request, $language, 'cancellation_policy_label'),
            'luggage_label' => $this->data($request, $language, 'luggage_label'),
            'smoking_label' => $this->data($request, $language, 'smoking_label'),
            'pets_label' => $this->data($request, $language, 'pets_label'),
            'seats_left_label' => $this->data($request, $language, 'seats_left_label'),
            'per_seat_label' => $this->data($request, $language, 'per_seat_label'),
            'pickup_dropoff_info_heading' => $this->data($request, $language, 'pickup_dropoff_info_heading'),
            'pickup_label' => $this->data($request, $language, 'pickup_label'),
            'dropoff_label' => $this->data($request, $language, 'dropoff_label'),
            'description_label' => $this->data($request, $language, 'description_label'),
            'ride_features_label' => $this->data($request, $language, 'ride_features_label'),
            'ride_seat_label' => $this->data($request, $language, 'ride_seat_label'),
            'all_seats_booked_label' => $this->data($request, $language, 'all_seats_booked_label'),
            'ride_canceller_by_driver' => $this->data($request, $language, 'ride_canceller_by_driver'),
            'ride_completed_text' => $this->data($request, $language, 'ride_completed_text'),
            'book_seat_btn_label' => $this->data($request, $language, 'book_seat_btn_label'),
            'no_seat_available_label' => $this->data($request, $language, 'no_seat_available_label'),
            'no_ride_found_message' => $this->data($request, $language, 'no_ride_found_message'),
            'cancel_booking_btn_label' => $this->data($request, $language, 'cancel_booking_btn_label'),
            'cancel_ride_btn_label' => $this->data($request, $language, 'cancel_ride_btn_label'),
            'cancel_ride_confirmation' => $this->data($request, $language, 'cancel_ride_confirmation'),
            'cancel_ride_yes_btn' => $this->data($request, $language, 'cancel_ride_yes_btn'),
            'cancel_ride_no_btn' => $this->data($request, $language, 'cancel_ride_no_btn'),
            'edit_ride_btn_label' => $this->data($request, $language, 'edit_ride_btn_label'),
            'review_label' => $this->data($request, $language, 'review_label'),
            'booking_request_heading' => $this->data($request, $language, 'booking_request_heading'),
            'seat_requested_label' => $this->data($request, $language, 'seat_requested_label'),
            'request_accept_label' => $this->data($request, $language, 'request_accept_label'),
            'request_reject_label' => $this->data($request, $language, 'request_reject_label'),
            'secured_cash_heading' => $this->data($request, $language, 'secured_cash_heading'),
            'enter_code_label' => $this->data($request, $language, 'enter_code_label'),
            'mobile_seat_booked_heading' => $this->data($request, $language, 'mobile_seat_booked_heading'),
            'mobile_seat_booked_label' => $this->data($request, $language, 'mobile_seat_booked_label'),
            'mobile_seat_fare_label' => $this->data($request, $language, 'mobile_seat_fare_label'),
            'mobile_seat_booking_fee_label' => $this->data($request, $language, 'mobile_seat_booking_fee_label'),
            'mobile_seat_total_amount_label' => $this->data($request, $language, 'mobile_seat_total_amount_label'),
            'vehicle_info_label' => $this->data($request, $language, 'vehicle_info_label'),
            'driver_info_label' => $this->data($request, $language, 'driver_info_label'),
            'review_driver_info_label' => $this->data($request, $language, 'review_driver_info_label'),
            'review_passanger_label' => $this->data($request, $language, 'review_passanger_label'),
            'driver_chat_with' => $this->data($request, $language, 'driver_chat_with'),
            'driver_label' => $this->data($request, $language, 'driver_label'),
            'cancellation_policy' => $this->data($request, $language, 'cancellation_policy'),
            'passengers_driven_label' => $this->data($request, $language, 'passengers_driven_label'),
            'driver_age_label' => $this->data($request, $language, 'driver_age_label'),
            'driver_chat_heading' => $this->data($request, $language, 'driver_chat_heading'),
            'driver_chat_label' => $this->data($request, $language, 'driver_chat_label'),
            'driver_chat_button_label' => $this->data($request, $language, 'driver_chat_button_label'),
            'booking_table_heading' => $this->data($request, $language, 'booking_table_heading'),
            'passenger_column_label' => $this->data($request, $language, 'passenger_column_label'),
            'seat_booked_column_label' => $this->data($request, $language, 'seat_booked_column_label'),
            'total_cost_column_label' => $this->data($request, $language, 'total_cost_column_label'),
            'booked_on_column_label' => $this->data($request, $language, 'booked_on_column_label'),
            'status_column_label' => $this->data($request, $language, 'status_column_label'),
            'booking_requested_status_label' => $this->data($request, $language, 'booking_requested_status_label'),
            'seat_booked_status_label' => $this->data($request, $language, 'seat_booked_status_label'),
            'booking_denied_status_label' => $this->data($request, $language, 'booking_denied_status_label'),
            'actions_column_label' => $this->data($request, $language, 'actions_column_label'),
            'edit_button_actions_label' => $this->data($request, $language, 'edit_button_actions_label'),
            'review_button_label' => $this->data($request, $language, 'review_button_label'),
            'i_reviewed_label' => $this->data($request, $language, 'i_reviewed_label'),
            'noon_label' => $this->data($request, $language, 'noon_label'),
            'midnight_label' => $this->data($request, $language, 'midnight_label'),
            'driver_note_label' => $this->data($request, $language, 'driver_note_label'),
            'trip_main_heading' => $this->data($request, $language, 'trip_main_heading'),
            'ride_main_heading' => $this->data($request, $language, 'ride_main_heading'),
            'discount_label' => $this->data($request, $language, 'discount_label'),
            'booking_request_main_heading' => $this->data($request, $language, 'booking_request_main_heading'),
            'passenger_age_label' => $this->data($request, $language, 'passenger_age_label'),
            'passenger_gender_label' => $this->data($request, $language, 'passenger_gender_label'),
            'seat_on_column_label' => $this->data($request, $language, 'seat_on_column_label'),
            'cancellation_policy_tooltip' => $this->data($request, $language, 'cancellation_policy_tooltip'),
            'cancellation_policy_tooltip_url' => $this->data($request, $language, 'cancellation_policy_tooltip_url'),

            'firm_cancellation_confirm_poup_heading' => $this->data($request, $language, 'firm_cancellation_confirm_poup_heading'),
            'firm_cancellation_confirm_poup_text' => $this->data($request, $language, 'firm_cancellation_confirm_poup_text'),
            'firm_cancellation_confirm_poup_yes_label' => $this->data($request, $language, 'firm_cancellation_confirm_poup_yes_label'),
            'firm_cancellation_confirm_poup_no_label' => $this->data($request, $language, 'firm_cancellation_confirm_poup_no_label'),
            'firm_cancellation_confirm_poup_sub_text' => $this->data($request, $language, 'firm_cancellation_confirm_poup_sub_text'),
            'instant_btn_label' => $this->data($request, $language, 'instant_btn_label'),
            'chat_error_message' => $this->data($request, $language, 'chat_error_message'),
            'empty_chat_placeholder' => $this->data($request, $language, 'empty_chat_placeholder'),
            'verified_email' => $this->data($request, $language, 'verified_email'),
            'verified_phone' => $this->data($request, $language, 'verified_phone'),


        ];
    }

    public function update($rideDetailPageSetting, $language, $request)
    {
        $fields = $this->fields($rideDetailPageSetting, $language, $request);
        $rideDetailPageSettingDetail = RideDetailPageSettingDetail::whereRideDetailPageId($rideDetailPageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$rideDetailPageSettingDetail){
            $fields = $this->fields($rideDetailPageSetting, $language, $request);
        RideDetailPageSettingDetail::create($fields);
        }
        else{
            RideDetailPageSettingDetail::whereRideDetailPageId($rideDetailPageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
