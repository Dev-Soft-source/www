<?php

namespace App\Services;

use App\Models\FindRidePageSettingDetail;

class FindRidePageSettingService
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
                $validationRule = array_merge($validationRule, ['extra_care_ride_page_label.extra_care_ride_page_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['extra_care_ride_page_label.extra_care_ride_page_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['pink_ride_page_heading.pink_ride_page_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['pink_ride_page_heading.pink_ride_page_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['pink_ride_page_label.pink_ride_page_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['pink_ride_page_label.pink_ride_page_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['search_results_pink_ride_label.search_results_pink_ride_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_results_pink_ride_label.search_results_pink_ride_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['search_results_extra_care_ride_label.search_results_extra_care_ride_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_results_extra_care_ride_label.search_results_extra_care_ride_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['more_rides_pink_ride_label.more_rides_pink_ride_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['more_rides_pink_ride_label.more_rides_pink_ride_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['to_pink_ride_label.to_pink_ride_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['to_pink_ride_label.to_pink_ride_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['imp_pink_ride_label.imp_pink_ride_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['imp_pink_ride_label.imp_pink_ride_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['imp_extra_care_ride_label.imp_extra_care_ride_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['imp_extra_care_ride_label.imp_extra_care_ride_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['navbar_icon.navbar_icon_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['navbar_icon.navbar_icon_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['from_field_icon.from_field_icon_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['from_field_icon.from_field_icon_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['swap_field_icon.swap_field_icon_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['swap_field_icon.swap_field_icon_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['to_field_icon.to_field_icon_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['to_field_icon.to_field_icon_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['date_field_icon.date_field_icon_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['date_field_icon.date_field_icon_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['search_field_icon.search_field_icon_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_field_icon.search_field_icon_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['search_section_from_placeholder.search_section_from_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_section_from_placeholder.search_section_from_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['search_section_to_placeholder.search_section_to_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_section_to_placeholder.search_section_to_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['search_section_date_placeholder.search_section_date_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_section_date_placeholder.search_section_date_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['search_section_required_error.search_section_required_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_section_required_error.search_section_required_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['search_section_keyword_label.search_section_keyword_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_section_keyword_label.search_section_keyword_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['search_section_keyword_placeholder.search_section_keyword_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_section_keyword_placeholder.search_section_keyword_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['search_section_button_label.search_section_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_section_button_label.search_section_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['search_section_recent_searches.search_section_recent_searches_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_section_recent_searches.search_section_recent_searches_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['card_section_at_label.card_section_at_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['card_section_at_label.card_section_at_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['card_section_seats_left.card_section_seats_left_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['card_section_seats_left.card_section_seats_left_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['card_section_per_seat.card_section_per_seat_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['card_section_per_seat.card_section_per_seat_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['heading_ride_card_section.heading_ride_card_section_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['heading_ride_card_section.heading_ride_card_section_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['card_section_booked.card_section_booked_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['card_section_booked.card_section_booked_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['card_section_seats.card_section_seats_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['card_section_seats.card_section_seats_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['card_section_booking_fee.card_section_booking_fee_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['card_section_booking_fee.card_section_booking_fee_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['card_section_seats_fee.card_section_seats_fee_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['card_section_seats_fee.card_section_seats_fee_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['card_section_amount.card_section_amount_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['card_section_amount.card_section_amount_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['card_section_driver.card_section_driver_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['card_section_driver.card_section_driver_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['card_section_age.card_section_age_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['card_section_age.card_section_age_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['card_section_driven.card_section_driven_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['card_section_driven.card_section_driven_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['filter_section_heading.filter_section_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['filter_section_heading.filter_section_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['filter1_driver_heading.filter1_driver_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['filter1_driver_heading.filter1_driver_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_age_label.driver_age_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_age_label.driver_age_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_age_placeholder.driver_age_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_age_placeholder.driver_age_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_rating_label.driver_rating_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_rating_label.driver_rating_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_rating_placeholder.driver_rating_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_rating_placeholder.driver_rating_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_phone_access_label.driver_phone_access_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_phone_access_label.driver_phone_access_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_know_label.driver_know_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_know_label.driver_know_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['filter2_passengers_heading.filter2_passengers_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['filter2_passengers_heading.filter2_passengers_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passengers_rating_label.passengers_rating_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passengers_rating_label.passengers_rating_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passengers_rating_placeholder.passengers_rating_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passengers_rating_placeholder.passengers_rating_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['filter3_payment_methods_heading.filter3_payment_methods_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['filter3_payment_methods_heading.filter3_payment_methods_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['payment_methods_label.payment_methods_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['payment_methods_label.payment_methods_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['payment_methods_option1.payment_methods_option1_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['payment_methods_option1.payment_methods_option1_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['filter4_vehicle_heading.filter4_vehicle_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['filter4_vehicle_heading.filter4_vehicle_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['vehicle_type_label.vehicle_type_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['vehicle_type_label.vehicle_type_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['vehicle_type_placeholder.vehicle_type_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['vehicle_type_placeholder.vehicle_type_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['luggage_label.luggage_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['luggage_label.luggage_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['luggage_placeholder.luggage_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['luggage_placeholder.luggage_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['smoking_label.smoking_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['smoking_label.smoking_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['pets_allowed_label.pets_allowed_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['pets_allowed_label.pets_allowed_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['card_section_from_label.card_section_from_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['card_section_from_label.card_section_from_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['card_section_to_label.card_section_to_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['card_section_to_label.card_section_to_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['card_section_passengers.card_section_passengers_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['card_section_passengers.card_section_passengers_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['card_section_review.card_section_review_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['card_section_review.card_section_review_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['card_section_completed.card_section_completed_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['card_section_completed.card_section_completed_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['trips_card_section_seat_booked.trips_card_section_seat_booked_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['trips_card_section_seat_booked.trips_card_section_seat_booked_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['trips_card_section_seat_available.trips_card_section_seat_available_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['trips_card_section_seat_available.trips_card_section_seat_available_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['trips_card_section_review_driver.trips_card_section_review_driver_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['trips_card_section_review_driver.trips_card_section_review_driver_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['firm_cancellation_tooltip.firm_cancellation_tooltip_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['firm_cancellation_tooltip.firm_cancellation_tooltip_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['search_section_pink_ride_label.search_section_pink_ride_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_section_pink_ride_label.search_section_pink_ride_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['search_section_extra_care_label.search_section_extra_care_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_section_extra_care_label.search_section_extra_care_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['filter_search_btn_label.filter_search_btn_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['filter_search_btn_label.filter_search_btn_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['filter_close_btn_label.filter_close_btn_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['filter_close_btn_label.filter_close_btn_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($findRidePageSetting, $language, $request)
    {
        return [
            'find_ride_page_setting_id' => $findRidePageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'pink_ride_page_heading' => $this->data($request, $language, 'pink_ride_page_heading'),
            'extra_care_ride_page_label' => $this->data($request, $language, 'extra_care_ride_page_label'),
            'pink_ride_page_label' => $this->data($request, $language, 'pink_ride_page_label'),
            'search_results_pink_ride_label' => $this->data($request, $language, 'search_results_pink_ride_label'),
            'search_results_extra_care_ride_label' => $this->data($request, $language, 'search_results_extra_care_ride_label'),
            'more_rides_pink_ride_label' => $this->data($request, $language, 'more_rides_pink_ride_label'),
            'to_pink_ride_label' => $this->data($request, $language, 'to_pink_ride_label'),
            'imp_pink_ride_label' => $this->data($request, $language, 'imp_pink_ride_label'),
            'imp_extra_care_ride_label' => $this->data($request, $language, 'imp_extra_care_ride_label'),
            'navbar_icon' => $this->data($request, $language, 'navbar_icon'),
            'from_field_icon' => $this->data($request, $language, 'from_field_icon'),
            'swap_field_icon' => $this->data($request, $language, 'swap_field_icon'),
            'to_field_icon' => $this->data($request, $language, 'to_field_icon'),
            'date_field_icon' => $this->data($request, $language, 'date_field_icon'),
            'search_field_icon' => $this->data($request, $language, 'search_field_icon'),
            'search_section_from_placeholder' => $this->data($request, $language, 'search_section_from_placeholder'),
            'search_section_to_placeholder' => $this->data($request, $language, 'search_section_to_placeholder'),
            'search_section_date_placeholder' => $this->data($request, $language, 'search_section_date_placeholder'),
            'search_section_required_error' => $this->data($request, $language, 'search_section_required_error'),
            'search_section_keyword_label' => $this->data($request, $language, 'search_section_keyword_label'),
            'search_section_keyword_placeholder' => $this->data($request, $language, 'search_section_keyword_placeholder'),
            'search_section_button_label' => $this->data($request, $language, 'search_section_button_label'),
            'search_section_recent_searches' => $this->data($request, $language, 'search_section_recent_searches'),
            'card_section_from_label' => $this->data($request, $language, 'card_section_from_label'),
            'card_section_to_label' => $this->data($request, $language, 'card_section_to_label'),
            'card_section_at_label' => $this->data($request, $language, 'card_section_at_label'),
            'card_section_seats_left' => $this->data($request, $language, 'card_section_seats_left'),
            'card_section_per_seat' => $this->data($request, $language, 'card_section_per_seat'),
            'heading_ride_card_section' => $this->data($request, $language, 'heading_ride_card_section'),
            'card_section_booked' => $this->data($request, $language, 'card_section_booked'),
            'card_section_seats' => $this->data($request, $language, 'card_section_seats'),
            'card_section_booking_fee' => $this->data($request, $language, 'card_section_booking_fee'),
            'card_section_seats_fee' => $this->data($request, $language, 'card_section_seats_fee'),
            'card_section_amount' => $this->data($request, $language, 'card_section_amount'),
            'card_section_driver' => $this->data($request, $language, 'card_section_driver'),
            'card_section_age' => $this->data($request, $language, 'card_section_age'),
            'card_section_driven' => $this->data($request, $language, 'card_section_driven'),
            'card_section_passengers' => $this->data($request, $language, 'card_section_passengers'),
            'card_section_review' => $this->data($request, $language, 'card_section_review'),
            'card_section_completed' => $this->data($request, $language, 'card_section_completed'),
            'trips_card_section_seat_booked' => $this->data($request, $language, 'trips_card_section_seat_booked'),
            'trips_card_section_seat_available' => $this->data($request, $language, 'trips_card_section_seat_available'),
            'trips_card_section_review_driver' => $this->data($request, $language, 'trips_card_section_review_driver'),
            'firm_cancellation_tooltip' => $this->data($request, $language, 'firm_cancellation_tooltip'),
            'filter_section_heading' => $this->data($request, $language, 'filter_section_heading'),
            'filter1_driver_heading' => $this->data($request, $language, 'filter1_driver_heading'),
            'driver_age_label' => $this->data($request, $language, 'driver_age_label'),
            'driver_age_placeholder' => $this->data($request, $language, 'driver_age_placeholder'),
            'driver_rating_label' => $this->data($request, $language, 'driver_rating_label'),
            'driver_rating_placeholder' => $this->data($request, $language, 'driver_rating_placeholder'),
            'driver_phone_access_label' => $this->data($request, $language, 'driver_phone_access_label'),
            'driver_know_label' => $this->data($request, $language, 'driver_know_label'),
            'driver_know_placeholder' => $this->data($request, $language, 'driver_know_placeholder'),
            'filter2_passengers_heading' => $this->data($request, $language, 'filter2_passengers_heading'),
            'passengers_rating_label' => $this->data($request, $language, 'passengers_rating_label'),
            'passengers_rating_placeholder' => $this->data($request, $language, 'passengers_rating_placeholder'),
            'filter3_payment_methods_heading' => $this->data($request, $language, 'filter3_payment_methods_heading'),
            'apply_button_label' => $this->data($request, $language, 'apply_button_label'),
            'clear_button_label' => $this->data($request, $language, 'clear_button_label'),
            'payment_methods_label' => $this->data($request, $language, 'payment_methods_label'),
            'payment_methods_option1' => $this->data($request, $language, 'payment_methods_option1'),
            'filter4_vehicle_heading' => $this->data($request, $language, 'filter4_vehicle_heading'),
            'vehicle_type_label' => $this->data($request, $language, 'vehicle_type_label'),
            'vehicle_type_placeholder' => $this->data($request, $language, 'vehicle_type_placeholder'),
            'ride_features_option17' => $this->data($request, $language, 'ride_features_option17'),
            'luggage_label' => $this->data($request, $language, 'luggage_label'),
            'luggage_placeholder' => $this->data($request, $language, 'luggage_placeholder'),
            'smoking_label' => $this->data($request, $language, 'smoking_label'),
            'pets_allowed_label' => $this->data($request, $language, 'pets_allowed_label'),
            'card_section_cancelled' => $this->data($request, $language, 'card_section_cancelled'),
            'search_filter_all_label' => $this->data($request, $language, 'search_filter_all_label'),
            'search_filter_select_vehicle_label' => $this->data($request, $language, 'search_filter_select_vehicle_label'),
            'card_section_not_live' => $this->data($request, $language, 'card_section_not_live'),
            'card_section_booking_request' => $this->data($request, $language, 'card_section_booking_request'),
            'trips_card_section_reviewed' => $this->data($request, $language, 'trips_card_section_reviewed'),
            'card_section_no_review' => $this->data($request, $language, 'card_section_no_review'),
            'search_result_load_more_btn' => $this->data($request, $language, 'search_result_load_more_btn'),
            'search_result_no_more_data_message' => $this->data($request, $language, 'search_result_no_more_data_message'),
            'search_result_no_found_message' => $this->data($request, $language, 'search_result_no_found_message'),
            'search_result_label' => $this->data($request, $language, 'search_result_label'),
            'filter_what_label' => $this->data($request, $language, 'filter_what_label'),
            'search_and_above_label' => $this->data($request, $language, 'search_and_above_label'),
            'ride_preferences_label' => $this->data($request, $language, 'ride_preferences_label'),
            'search_section_pink_ride_label' => $this->data($request, $language, 'search_section_pink_ride_label'),
            'search_section_extra_care_label' => $this->data($request, $language, 'search_section_extra_care_label'),
            'filter_search_btn_label' => $this->data($request, $language, 'filter_search_btn_label'),
            'filter_close_btn_label' => $this->data($request, $language, 'filter_close_btn_label'),
            
            'hide_ride_popup_heading' => $this->data($request, $language, 'hide_ride_popup_heading'),
            'hide_ride_popup_text' => $this->data($request, $language, 'hide_ride_popup_text'),
            'hide_ride_popup_confirm_button' => $this->data($request, $language, 'hide_ride_popup_confirm_button'),
            'hide_ride_popup_take_me_back_button' => $this->data($request, $language, 'hide_ride_popup_take_me_back_button'),


        ];
    }

    public function update($findRidePageSetting, $language, $request)
    {
        $fields = $this->fields($findRidePageSetting, $language, $request);
        $findRidePageSettingDetail = FindRidePageSettingDetail::whereFindRidePageSettingId($findRidePageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$findRidePageSettingDetail){
            $fields = $this->fields($findRidePageSetting, $language, $request);
        FindRidePageSettingDetail::create($fields);
        }
        else{
            FindRidePageSettingDetail::whereFindRidePageSettingId($findRidePageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
