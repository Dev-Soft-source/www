<?php

namespace App\Services;

use App\Models\MobileFindRideSettingDetail;

class MobileFindRideSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['search_section_from_label.search_section_from_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['search_section_from_label.search_section_from_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['search_section_to_label.search_section_to_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['search_section_to_label.search_section_to_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['search_section_keyword_label.search_section_keyword_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['search_section_keyword_label.search_section_keyword_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['search_section_date_placeholder.search_section_date_placeholder_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['search_section_date_placeholder.search_section_date_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['search_section_button_label.search_section_button_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['search_section_button_label.search_section_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['search_section_recent_searches.search_section_recent_searches_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['search_section_recent_searches.search_section_recent_searches_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['card_section_at_label.card_section_at_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['card_section_at_label.card_section_at_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['card_section_per_seat.card_section_per_seat_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['card_section_per_seat.card_section_per_seat_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['card_section_age.card_section_age_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['card_section_age.card_section_age_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['card_section_driven.card_section_driven_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['card_section_driven.card_section_driven_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['card_section_review.card_section_review_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['card_section_review.card_section_review_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
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
            $validationRule = array_merge($validationRule, ['payment_methods_option1.payment_methods_option1_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['payment_methods_option1.payment_methods_option1_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['filter4_vehicle_heading.filter4_vehicle_heading_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['filter4_vehicle_heading.filter4_vehicle_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['vehicle_type_placeholder.vehicle_type_placeholder_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['vehicle_type_placeholder.vehicle_type_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['ride_preferences_label.ride_preferences_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['ride_preferences_label.ride_preferences_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['luggage_label.luggage_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['luggage_label.luggage_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['smoking_label.smoking_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['smoking_label.smoking_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['pets_allowed_label.pets_allowed_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['pets_allowed_label.pets_allowed_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['clear_button_label.clear_button_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['clear_button_label.clear_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['apply_button_label.apply_button_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['apply_button_label.apply_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($findRidePageSetting, $language, $request)
    {
        return [
            'find_ride_setting_id' => $findRidePageSetting->id,
            'language_id' => $language->id,
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'search_section_from_label' => $this->data($request, $language, 'search_section_from_label'),
            'search_section_from_placeholder' => $this->data($request, $language, 'search_section_from_placeholder'),
            'search_section_to_label' => $this->data($request, $language, 'search_section_to_label'),
            'search_section_to_placeholder' => $this->data($request, $language, 'search_section_to_placeholder'),
            'search_section_keyword_label' => $this->data($request, $language, 'search_section_keyword_label'),
            'search_section_date_placeholder' => $this->data($request, $language, 'search_section_date_placeholder'),
            'search_section_button_label' => $this->data($request, $language, 'search_section_button_label'),
            'search_section_recent_searches' => $this->data($request, $language, 'search_section_recent_searches'),
            'card_section_at_label' => $this->data($request, $language, 'card_section_at_label'),
            'card_section_per_seat' => $this->data($request, $language, 'card_section_per_seat'),
            'card_section_age' => $this->data($request, $language, 'card_section_age'),
            'card_section_driven' => $this->data($request, $language, 'card_section_driven'),
            'card_section_review' => $this->data($request, $language, 'card_section_review'),
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
            'payment_methods_option1' => $this->data($request, $language, 'payment_methods_option1'),
            'filter4_vehicle_heading' => $this->data($request, $language, 'filter4_vehicle_heading'),
            'vehicle_type_placeholder' => $this->data($request, $language, 'vehicle_type_placeholder'),
            'ride_preferences_label' => $this->data($request, $language, 'ride_preferences_label'),
            'luggage_label' => $this->data($request, $language, 'luggage_label'),
            'smoking_label' => $this->data($request, $language, 'smoking_label'),
            'pets_allowed_label' => $this->data($request, $language, 'pets_allowed_label'),
            'clear_button_label' => $this->data($request, $language, 'clear_button_label'),
            'apply_button_label' => $this->data($request, $language, 'apply_button_label'),
            'card_section_no_review' => $this->data($request, $language, 'card_section_no_review'),
        ];
    }

    public function update($findRidePageSetting, $language, $request)
    {
        $fields = $this->fields($findRidePageSetting, $language, $request);
        $findRidePageSettingDetail = MobileFindRideSettingDetail::whereFindRideSettingId($findRidePageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$findRidePageSettingDetail){
            $fields = $this->fields($findRidePageSetting, $language, $request);
            MobileFindRideSettingDetail::create($fields);
        }
        else{
            MobileFindRideSettingDetail::whereFindRideSettingId($findRidePageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
