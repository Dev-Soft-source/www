<?php

namespace App\Services;

use App\Models\MobilePostRideSettingDetail;

class MobilePostRideSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['post_arrived_again_label.post_arrived_again_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['post_arrived_again_label.post_arrived_again_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['ride_info_heading.ride_info_heading_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['ride_info_heading.ride_info_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['from_label.from_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['from_label.from_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['to_label.to_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['to_label.to_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['pick_up_label.pick_up_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['pick_up_label.pick_up_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['drop_off_label.drop_off_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['drop_off_label.drop_off_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['date_time_label.date_time_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['date_time_label.date_time_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['at_label.at_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['at_label.at_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['recurring_label.recurring_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['recurring_label.recurring_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['meeting_drop_off_description_label.meeting_drop_off_description_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['meeting_drop_off_description_label.meeting_drop_off_description_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['seats_label.seats_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['seats_label.seats_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['seats_middle_label.seats_middle_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['seats_middle_label.seats_middle_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['seats_back_label.seats_back_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['seats_back_label.seats_back_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['vehicle_label.vehicle_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['vehicle_label.vehicle_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['skip_label.skip_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['skip_label.skip_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['add_vehicle_label.add_vehicle_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['add_vehicle_label.add_vehicle_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['make_label.make_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['make_label.make_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['model_label.model_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['model_label.model_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['type_label.type_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['type_label.type_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['year_label.year_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['year_label.year_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['color_label.color_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['color_label.color_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['liscense_label.liscense_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['liscense_label.liscense_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['electric_car_label.electric_car_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['electric_car_label.electric_car_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['hybrid_car_label.hybrid_car_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['hybrid_car_label.hybrid_car_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['preferences_label.preferences_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['preferences_label.preferences_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['smoking_label.smoking_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['smoking_label.smoking_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['animals_label.animals_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['animals_label.animals_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['booking_label.booking_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['booking_label.booking_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['booking_option1.booking_option1_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['booking_option1.booking_option1_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['booking_option2.booking_option2_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['booking_option2.booking_option2_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['luggage_label.luggage_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['luggage_label.luggage_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['luggage_checkbox_label1.luggage_checkbox_label1_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['luggage_checkbox_label1.luggage_checkbox_label1_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['luggage_checkbox_label2.luggage_checkbox_label2_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['luggage_checkbox_label2.luggage_checkbox_label2_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['price_payment_heading.price_payment_heading_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['price_payment_heading.price_payment_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['price_per_seat_label.price_per_seat_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['price_per_seat_label.price_per_seat_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['payment_methods_label.payment_methods_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['payment_methods_label.payment_methods_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['anything_to_add_label.anything_to_add_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['anything_to_add_label.anything_to_add_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['disclaimers_label.disclaimers_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['disclaimers_label.disclaimers_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['disclaimers_description.disclaimers_description_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['disclaimers_description.disclaimers_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['agree_terms_label.agree_terms_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['agree_terms_label.agree_terms_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            $validationRule = array_merge($validationRule, ['submit_button_label.submit_button_label_' . $language->id => ['required', 'string']]);
            $errorMessages = array_merge($errorMessages, ['submit_button_label.submit_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($postRidePageSetting, $language, $request)
    {
        return [
            'post_ride_setting_id' => $postRidePageSetting->id,
            'language_id' => $language->id,
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'post_arrived_again_label' => $this->data($request, $language, 'post_arrived_again_label'),
            'ride_info_heading' => $this->data($request, $language, 'ride_info_heading'),
            'from_label' => $this->data($request, $language, 'from_label'),
            'from_placeholder' => $this->data($request, $language, 'from_placeholder'),
            'to_label' => $this->data($request, $language, 'to_label'),
            'to_placeholder' => $this->data($request, $language, 'to_placeholder'),
            'pick_up_label' => $this->data($request, $language, 'pick_up_label'),
            'pick_up_placeholder' => $this->data($request, $language, 'pick_up_placeholder'),
            'drop_off_label' => $this->data($request, $language, 'drop_off_label'),
            'drop_off_placeholder' => $this->data($request, $language, 'drop_off_placeholder'),
            'date_time_label' => $this->data($request, $language, 'date_time_label'),
            'at_label' => $this->data($request, $language, 'at_label'),
            'recurring_label' => $this->data($request, $language, 'recurring_label'),
            'recurring_type_label' => $this->data($request, $language, 'recurring_type_label'),
            'recurring_trips_label' => $this->data($request, $language, 'recurring_trips_label'),
            'meeting_drop_off_description_label' => $this->data($request, $language, 'meeting_drop_off_description_label'),
            'meeting_drop_off_description_placeholder' => $this->data($request, $language, 'meeting_drop_off_description_placeholder'),
            'seats_label' => $this->data($request, $language, 'seats_label'),
            'seats_middle_label' => $this->data($request, $language, 'seats_middle_label'),
            'seats_back_label' => $this->data($request, $language, 'seats_back_label'),
            'vehicle_label' => $this->data($request, $language, 'vehicle_label'),
            'skip_label' => $this->data($request, $language, 'skip_label'),
            'add_vehicle_label' => $this->data($request, $language, 'add_vehicle_label'),
            'make_label' => $this->data($request, $language, 'make_label'),
            'make_placeholder' => $this->data($request, $language, 'make_placeholder'),
            'model_label' => $this->data($request, $language, 'model_label'),
            'model_placeholder' => $this->data($request, $language, 'model_placeholder'),
            'type_label' => $this->data($request, $language, 'type_label'),
            'year_label' => $this->data($request, $language, 'year_label'),
            'color_label' => $this->data($request, $language, 'color_label'),
            'liscense_label' => $this->data($request, $language, 'liscense_label'),
            'electric_car_label' => $this->data($request, $language, 'electric_car_label'),
            'hybrid_car_label' => $this->data($request, $language, 'hybrid_car_label'),
            'car_photo_label' => $this->data($request, $language, 'car_photo_label'),
            'smoking_label' => $this->data($request, $language, 'smoking_label'),
            'animals_label' => $this->data($request, $language, 'animals_label'),
            'preferences_label' => $this->data($request, $language, 'preferences_label'),
            'booking_label' => $this->data($request, $language, 'booking_label'),
            'booking_option1' => $this->data($request, $language, 'booking_option1'),
            'booking_option2' => $this->data($request, $language, 'booking_option2'),
            'luggage_label' => $this->data($request, $language, 'luggage_label'),
            'luggage_checkbox_label1' => $this->data($request, $language, 'luggage_checkbox_label1'),
            'luggage_checkbox_label2' => $this->data($request, $language, 'luggage_checkbox_label2'),
            'price_payment_heading' => $this->data($request, $language, 'price_payment_heading'),
            'price_per_seat_label' => $this->data($request, $language, 'price_per_seat_label'),
            'payment_methods_label' => $this->data($request, $language, 'payment_methods_label'),
            'anything_to_add_label' => $this->data($request, $language, 'anything_to_add_label'),
            'anything_to_add_placeholder' => $this->data($request, $language, 'anything_to_add_placeholder'),
            'disclaimers_label' => $this->data($request, $language, 'disclaimers_label'),
            'disclaimers_description' => $this->data($request, $language, 'disclaimers_description'),
            'agree_terms_label' => $this->data($request, $language, 'agree_terms_label'),
            'submit_button_label' => $this->data($request, $language, 'submit_button_label'),
        ];
    }

    public function update($postRidePageSetting, $language, $request)
    {
        $fields = $this->fields($postRidePageSetting, $language, $request);
        $postRidePageSettingDetail = MobilePostRideSettingDetail::wherePostRideSettingId($postRidePageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$postRidePageSettingDetail){
            $fields = $this->fields($postRidePageSetting, $language, $request);
            MobilePostRideSettingDetail::create($fields);
        }
        else{
            MobilePostRideSettingDetail::wherePostRideSettingId($postRidePageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
