<?php

namespace App\Services;

use App\Models\PostRidePageSettingDetail;
use App\Models\PostRidePageSettingSubDetail;

class PostRidePageSettingService
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
                $validationRule = array_merge($validationRule, ['gas_car_label.gas_car_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['gas_car_label.gas_car_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['preferences_label.preferences_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['preferences_label.preferences_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['smoking_label.smoking_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['smoking_label.smoking_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['animals_label.animals_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['animals_label.animals_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['features_label.features_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['features_label.features_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_label.booking_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_label.booking_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['max_back_seats_label.max_back_seats_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['max_back_seats_label.max_back_seats_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['luggage_label.luggage_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['luggage_label.luggage_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['luggage_checkbox_label1.luggage_checkbox_label1_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['luggage_checkbox_label1.luggage_checkbox_label1_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['luggage_checkbox_label1_tooltip.luggage_checkbox_label1_tooltip_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['luggage_checkbox_label1_tooltip.luggage_checkbox_label1_tooltip_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
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
                $validationRule = array_merge($validationRule, ['recurring_type_label.recurring_type_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['recurring_type_label.recurring_type_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['recurring_trips_label.recurring_trips_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['recurring_trips_label.recurring_trips_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['existing_label.existing_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['existing_label.existing_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['car_type_label.car_type_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['car_type_label.car_type_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancellation_policy_label.cancellation_policy_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancellation_policy_label.cancellation_policy_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['app_disclaimers_description1.app_disclaimers_description1_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['app_disclaimers_description1.app_disclaimers_description1_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['app_disclaimers_description2.app_disclaimers_description2_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['app_disclaimers_description2.app_disclaimers_description2_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['app_disclaimers_description3.app_disclaimers_description3_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['app_disclaimers_description3.app_disclaimers_description3_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['app_disclaimers_description4.app_disclaimers_description4_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['app_disclaimers_description4.app_disclaimers_description4_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['recurring_trips_placeholder.recurring_trips_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['recurring_trips_placeholder.recurring_trips_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading_update.main_heading_update_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading_update.main_heading_update_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_agree_terms_label.mobile_agree_terms_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_agree_terms_label.mobile_agree_terms_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_term_of_service_label.mobile_term_of_service_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_term_of_service_label.mobile_term_of_service_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_agree_terms_and_label.mobile_agree_terms_and_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_agree_terms_and_label.mobile_agree_terms_and_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_term_of_use_label.mobile_term_of_use_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_term_of_use_label.mobile_term_of_use_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['update_button_label.update_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['update_button_label.update_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['indicates_required_field_text.indicates_required_field_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['indicates_required_field_text.indicates_required_field_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['navbar_icon.navbar_icon_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['navbar_icon.navbar_icon_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['repost_ride_btn_label.repost_ride_btn_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['repost_ride_btn_label.repost_ride_btn_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['city_not_in_record.city_not_in_record_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['city_not_in_record.city_not_in_record_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                  $validationRule = array_merge($validationRule, ['city_not_fount_contact_text.city_not_fount_contact_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['city_not_fount_contact_text.city_not_fount_contact_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
               
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($postRidePageSetting, $language, $request)
    {
        return [
            'post_ride_page_setting_id' => $postRidePageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
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
            'recurring_trips_placeholder' => $this->data($request, $language, 'recurring_trips_placeholder'),
            'meeting_drop_off_description_label' => $this->data($request, $language, 'meeting_drop_off_description_label'),
            'meeting_drop_off_description_placeholder' => $this->data($request, $language, 'meeting_drop_off_description_placeholder'),
            'seats_label' => $this->data($request, $language, 'seats_label'),
            'seats_middle_label' => $this->data($request, $language, 'seats_middle_label'),
            'seats_back_label' => $this->data($request, $language, 'seats_back_label'),
            'vehicle_label' => $this->data($request, $language, 'vehicle_label'),
            'skip_label' => $this->data($request, $language, 'skip_label'),
            'add_vehicle_label' => $this->data($request, $language, 'add_vehicle_label'),
            'existing_label' => $this->data($request, $language, 'existing_label'),
            'make_label' => $this->data($request, $language, 'make_label'),
            'make_placeholder' => $this->data($request, $language, 'make_placeholder'),
            'model_label' => $this->data($request, $language, 'model_label'),
            'model_placeholder' => $this->data($request, $language, 'model_placeholder'),
            'type_label' => $this->data($request, $language, 'type_label'),
            'year_label' => $this->data($request, $language, 'year_label'),
            'color_label' => $this->data($request, $language, 'color_label'),
            'liscense_label' => $this->data($request, $language, 'liscense_label'),
            'car_type_label' => $this->data($request, $language, 'car_type_label'),
            'electric_car_label' => $this->data($request, $language, 'electric_car_label'),
            'hybrid_car_label' => $this->data($request, $language, 'hybrid_car_label'),
            'gas_car_label' => $this->data($request, $language, 'gas_car_label'),
            'preferences_label' => $this->data($request, $language, 'preferences_label'),
            'smoking_label' => $this->data($request, $language, 'smoking_label'),
            'animals_label' => $this->data($request, $language, 'animals_label'),
            'features_label' => $this->data($request, $language, 'features_label'),
            'features_option17' => $this->data($request, $language, 'features_option17'),
            'booking_label' => $this->data($request, $language, 'booking_label'),
            'max_back_seats_label' => $this->data($request, $language, 'max_back_seats_label'),
            'luggage_label' => $this->data($request, $language, 'luggage_label'),
            'luggage_checkbox_label1' => $this->data($request, $language, 'luggage_checkbox_label1'),
            'luggage_checkbox_label1_tooltip' => $this->data($request, $language, 'luggage_checkbox_label1_tooltip'),
            'price_payment_heading' => $this->data($request, $language, 'price_payment_heading'),
            'price_per_seat_label' => $this->data($request, $language, 'price_per_seat_label'),
            'payment_methods_label' => $this->data($request, $language, 'payment_methods_label'),
            'cancellation_policy_label' => $this->data($request, $language, 'cancellation_policy_label'),
            'anything_to_add_label' => $this->data($request, $language, 'anything_to_add_label'),
            'anything_to_add_placeholder' => $this->data($request, $language, 'anything_to_add_placeholder'),
            'disclaimers_label' => $this->data($request, $language, 'disclaimers_label'),
            'app_disclaimers_description1' => $this->data($request, $language, 'app_disclaimers_description1'),
            'app_disclaimers_description2' => $this->data($request, $language, 'app_disclaimers_description2'),
            'app_disclaimers_description3' => $this->data($request, $language, 'app_disclaimers_description3'),
            'app_disclaimers_description4' => $this->data($request, $language, 'app_disclaimers_description4'),
            'disclaimers_description' => $this->data($request, $language, 'disclaimers_description'),
            'agree_terms_label' => $this->data($request, $language, 'agree_terms_label'),
            'submit_button_label' => $this->data($request, $language, 'submit_button_label'),
            'main_heading_update' => $this->data($request, $language, 'main_heading_update'),
            'mobile_agree_terms_label' => $this->data($request, $language, 'mobile_agree_terms_label'),
            'mobile_term_of_service_label' => $this->data($request, $language, 'mobile_term_of_service_label'),
            'mobile_agree_terms_and_label' => $this->data($request, $language, 'mobile_agree_terms_and_label'),
            'mobile_term_of_use_label' => $this->data($request, $language, 'mobile_term_of_use_label'),
            'update_button_label' => $this->data($request, $language, 'update_button_label'),
            'indicates_required_field_text' => $this->data($request, $language, 'indicates_required_field_text'),
            'navbar_icon' => $this->data($request, $language, 'navbar_icon'),
            'repost_ride_btn_label' => $this->data($request, $language, 'repost_ride_btn_label'),
            'city_not_in_record' => $this->data($request, $language, 'city_not_in_record'),
            'pink_ride_tooltip_only_text' => $this->data($request, $language, 'pink_ride_tooltip_only_text'),
            'pink_ride_tooltip_female_text' => $this->data($request, $language, 'pink_ride_tooltip_female_text'),
            'pink_ride_tooltip_complete_profile_text' => $this->data($request, $language, 'pink_ride_tooltip_complete_profile_text'),
            'pink_ride_tooltip_driver_text' => $this->data($request, $language, 'pink_ride_tooltip_driver_text'),
            'pink_ride_tooltip_with_text' => $this->data($request, $language, 'pink_ride_tooltip_with_text'),
            'pink_ride_tooltip_phone_number_text' => $this->data($request, $language, 'pink_ride_tooltip_phone_number_text'),
            'pink_ride_tooltip_email_text' => $this->data($request, $language, 'pink_ride_tooltip_email_text'),
            'pink_ride_tooltip_driver_license_text' => $this->data($request, $language, 'pink_ride_tooltip_driver_license_text'),
            'pink_ride_tooltip_verified_text' => $this->data($request, $language, 'pink_ride_tooltip_verified_text'),
            'pink_ride_tooltip_select_this_ride_text' => $this->data($request, $language, 'pink_ride_tooltip_select_this_ride_text'),
            'extra_care_tooltip_driver_review_text' => $this->data($request, $language, 'extra_care_tooltip_driver_review_text'),
            'extra_care_tooltip_greater_age_text' => $this->data($request, $language, 'extra_care_tooltip_greater_age_text'),
            'extra_care_tooltip_greater_text' => $this->data($request, $language, 'extra_care_tooltip_greater_text'),
            'extra_care_tooltip_eligible_text' => $this->data($request, $language, 'extra_care_tooltip_eligible_text'),
            'extra_care_tooltip_complete_profile_text' => $this->data($request, $language, 'extra_care_tooltip_complete_profile_text'),
            'extra_care_tooltip_verified_text' => $this->data($request, $language, 'extra_care_tooltip_verified_text'),
            'extra_care_tooltip_driver_license_text' => $this->data($request, $language, 'extra_care_tooltip_driver_license_text'),
            'extra_care_tooltip_phone_number_text' => $this->data($request, $language, 'extra_care_tooltip_phone_number_text'),
            'extra_care_tooltip_email_text' => $this->data($request, $language, 'extra_care_tooltip_email_text'),
            'extra_care_tooltip_and_his_text' => $this->data($request, $language, 'extra_care_tooltip_and_his_text'),
            'select_vehicle_type' => $this->data($request, $language, 'select_vehicle_type'),
            'vehicle_type_placeholder' => $this->data($request, $language, 'vehicle_type_placeholder'),
            'seat_text' => $this->data($request, $language, 'seat_text'),
            'recurring_type_select_placeholder' => $this->data($request, $language, 'recurring_type_select_placeholder'),
            'recurring_type_daily_label' => $this->data($request, $language, 'recurring_type_daily_label'),
            'recurring_type_weekly_label' => $this->data($request, $language, 'recurring_type_weekly_label'),
            'post_ride_again_main_heading' => $this->data($request, $language, 'post_ride_again_main_heading'),
            'upcoming_label' => $this->data($request, $language, 'upcoming_label'),
            'completed_label' => $this->data($request, $language, 'completed_label'),
            'cancelled_label' => $this->data($request, $language, 'cancelled_label'),
            'cancelled_ride_no_found_message' => $this->data($request, $language, 'cancelled_ride_no_found_message'),
            'completed_ride_no_found_message' => $this->data($request, $language, 'completed_ride_no_found_message'),
            'upcoming_ride_no_found_message' => $this->data($request, $language, 'upcoming_ride_no_found_message'),
            
            'extra_care_tooltip_admin_enable_text' => $this->data($request, $language, 'extra_care_tooltip_admin_enable_text'),
            'extra_care_tooltip_admin_disable_text' => $this->data($request, $language, 'extra_care_tooltip_admin_disable_text'),
            'pink_ride_tooltip_admin_enable_text' => $this->data($request, $language, 'pink_ride_tooltip_admin_enable_text'),
            'pink_ride_tooltip_admin_disable_text' => $this->data($request, $language, 'pink_ride_tooltip_admin_disable_text')

        ];
    }
    
    
    public function subFields($postRidePageSetting, $language, $request)
    {
        return [
            'post_ride_page_id' => $postRidePageSetting->id,
            'language_id' => $language->id,
            'city_not_fount_contact_text' => $this->data($request, $language, 'city_not_fount_contact_text'),
            'extra_care_popup_eligible_text' => $this->data($request, $language, 'extra_care_popup_eligible_text'),
            'feilds_required_text' => $this->data($request, $language, 'feilds_required_text'),

           
        ];
    }

public function update($postRidePageSetting, $language, $request)
    {
        // dd($postRidePageSetting);
        $fields = $this->fields($postRidePageSetting, $language, $request);
        $subFields = $this->subFields($postRidePageSetting, $language, $request);
        $postRidePageSettingDetail = PostRidePageSettingDetail::wherePostRidePageSettingId($postRidePageSetting->id)->whereLanguageId($language->id)->exists();
        $postRidePageSettingSubDetail = PostRidePageSettingSubDetail::wherePostRidePageId($postRidePageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$postRidePageSettingDetail){
            $fields = $this->fields($postRidePageSetting, $language, $request);
        PostRidePageSettingDetail::create($fields);
        }
        else{
            PostRidePageSettingDetail::wherePostRidePageSettingId($postRidePageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        
        if(!$postRidePageSettingSubDetail){
            $fields = $this->subFields($postRidePageSetting, $language, $request);
        PostRidePageSettingSubDetail::create($fields);
        }
        else{
            PostRidePageSettingSubDetail::where('post_ride_page_id', $postRidePageSetting->id)->whereLanguageId($language->id)->update($subFields);
        }
        return true;
    }

    function data($request, $language, $name)
{
    if ($language && isset($request[$name][$name . '_' . $language->id])) {
        return $request[$name][$name . '_' . $language->id];
    }
    return null;
}

}
