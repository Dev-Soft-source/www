<?php

namespace App\Services;

use App\Models\BookingPageSettingDetail;

class BookingPageSettingService
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
                $validationRule = array_merge($validationRule, ['seats_available_label.seats_available_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['seats_available_label.seats_available_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);


                $validationRule = array_merge($validationRule, ['seats_available_info_text.seats_available_info_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['seats_available_info_text.seats_available_info_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['cancellation_policy_label.cancellation_policy_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancellation_policy_label.cancellation_policy_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['pricing_label.pricing_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['pricing_label.pricing_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['seat_label.seat_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['seat_label.seat_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_fee_label.booking_fee_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_fee_label.booking_fee_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_label.booking_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_label.booking_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['paypal_label.paypal_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['paypal_label.paypal_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['ride_features_label.ride_features_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['ride_features_label.ride_features_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['required_fields.required_fields_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['required_fields.required_fields_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['total_label.total_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['total_label.total_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['message_to_driver_label.message_to_driver_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['message_to_driver_label.message_to_driver_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['message_driver_placeholder.message_driver_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['message_driver_placeholder.message_driver_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['book_seat_button_label.book_seat_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['book_seat_button_label.book_seat_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['like_to_pay_label.like_to_pay_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['like_to_pay_label.like_to_pay_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['credit_card_label.credit_card_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['credit_card_label.credit_card_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['select_card_label.select_card_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['select_card_label.select_card_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['add_card_label.add_card_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['add_card_label.add_card_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['pay_label.pay_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['pay_label.pay_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['luggage_label.luggage_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['luggage_label.luggage_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['payment_method_label.payment_method_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['payment_method_label.payment_method_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['co_passenger_label.co_passenger_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['co_passenger_label.co_passenger_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['coffee_from_wall_label.coffee_from_wall_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['coffee_from_wall_label.coffee_from_wall_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['coffee_from_wall_tooltip.coffee_from_wall_tooltip_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['coffee_from_wall_tooltip.coffee_from_wall_tooltip_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['payable_amount_label.payable_amount_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['payable_amount_label.payable_amount_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['coffee_from_amount_wall_tooltip.coffee_from_amount_wall_tooltip_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['coffee_from_amount_wall_tooltip.coffee_from_amount_wall_tooltip_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['tax_label.tax_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['tax_label.tax_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['booking_disclaimer_on_time.booking_disclaimer_on_time_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_disclaimer_on_time.booking_disclaimer_on_time_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_disclaimer_pink_ride.booking_disclaimer_pink_ride_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_disclaimer_pink_ride.booking_disclaimer_pink_ride_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_disclaimer_extra_care_ride.booking_disclaimer_extra_care_ride_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_disclaimer_extra_care_ride.booking_disclaimer_extra_care_ride_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_disclaimer_firm.booking_disclaimer_firm_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_disclaimer_firm.booking_disclaimer_firm_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_term_agree_text.booking_term_agree_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_term_agree_text.booking_term_agree_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_pink_ride_term_agree_text.booking_pink_ride_term_agree_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_pink_ride_term_agree_text.booking_pink_ride_term_agree_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_extra_care_ride_term_agree_text.booking_extra_care_ride_term_agree_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_extra_care_ride_term_agree_text.booking_extra_care_ride_term_agree_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['firm_cancellation_label_price_section.firm_cancellation_label_price_section_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['firm_cancellation_label_price_section.firm_cancellation_label_price_section_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['firm_discount_label_price_section.firm_discount_label_price_section_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['firm_discount_label_price_section.firm_discount_label_price_section_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['firm_your_price_label_price_section.firm_your_price_label_price_section_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['firm_your_price_label_price_section.firm_your_price_label_price_section_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['booking_cancellation_limit_exceed.booking_cancellation_limit_exceed_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_cancellation_limit_exceed.booking_cancellation_limit_exceed_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($bookingPageSetting, $language, $request)
    {
        return [
            'booking_page_setting_id' => $bookingPageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'seats_available_label' => $this->data($request, $language, 'seats_available_label'),
            'seats_available_info_text' => $this->data($request, $language, 'seats_available_info_text'),
            'cancellation_policy_label' => $this->data($request, $language, 'cancellation_policy_label'),
            'pricing_label' => $this->data($request, $language, 'pricing_label'),
            'seat_label' => $this->data($request, $language, 'seat_label'),
            'booking_fee_label' => $this->data($request, $language, 'booking_fee_label'),
            'booking_label' => $this->data($request, $language, 'booking_label'),
            'paypal_label' => $this->data($request, $language, 'paypal_label'),
            'ride_features_label' => $this->data($request, $language, 'ride_features_label'),
            'required_fields' => $this->data($request, $language, 'required_fields'),
            'total_label' => $this->data($request, $language, 'total_label'),
            'message_to_driver_label' => $this->data($request, $language, 'message_to_driver_label'),
            'message_driver_placeholder' => $this->data($request, $language, 'message_driver_placeholder'),
            'book_seat_button_label' => $this->data($request, $language, 'book_seat_button_label'),
            'like_to_pay_label' => $this->data($request, $language, 'like_to_pay_label'),
            'credit_card_label' => $this->data($request, $language, 'credit_card_label'),
            'select_card_label' => $this->data($request, $language, 'select_card_label'),
            'add_card_label' => $this->data($request, $language, 'add_card_label'),
            'pay_label' => $this->data($request, $language, 'pay_label'),
            'co_passenger_label' => $this->data($request, $language, 'co_passenger_label'),
            'payment_method_label' => $this->data($request, $language, 'payment_method_label'),
            'luggage_label' => $this->data($request, $language, 'luggage_label'),
            'coffee_from_wall_label' => $this->data($request, $language, 'coffee_from_wall_label'),
            'coffee_from_wall_tooltip' => $this->data($request, $language, 'coffee_from_wall_tooltip'),
            'payable_amount_label' => $this->data($request, $language, 'payable_amount_label'),
            'coffee_from_amount_wall_tooltip' => $this->data($request, $language, 'coffee_from_amount_wall_tooltip'),
            'tax_label' => $this->data($request, $language, 'tax_label'),
            'booking_disclaimer_on_time' => $this->data($request, $language, 'booking_disclaimer_on_time'),
            'booking_disclaimer_pink_ride' => $this->data($request, $language, 'booking_disclaimer_pink_ride'),
            'booking_disclaimer_extra_care_ride' => $this->data($request, $language, 'booking_disclaimer_extra_care_ride'),
            'booking_disclaimer_firm' => $this->data($request, $language, 'booking_disclaimer_firm'),
            'booking_term_agree_text' => $this->data($request, $language, 'booking_term_agree_text'),
            'booking_pink_ride_term_agree_text' => $this->data($request, $language, 'booking_pink_ride_term_agree_text'),
            'booking_extra_care_ride_term_agree_text' => $this->data($request, $language, 'booking_extra_care_ride_term_agree_text'),
            'firm_cancellation_label_price_section' => $this->data($request, $language, 'firm_cancellation_label_price_section'),
            'firm_discount_label_price_section' => $this->data($request, $language, 'firm_discount_label_price_section'),
            'firm_your_price_label_price_section' => $this->data($request, $language, 'firm_your_price_label_price_section'),
            'booking_cancellation_limit_exceed' => $this->data($request, $language, 'booking_cancellation_limit_exceed'),
        ];
    }

    public function update($bookingPageSetting, $language, $request)
    {
        $fields = $this->fields($bookingPageSetting, $language, $request);
        $bookingPageSettingDetail = BookingPageSettingDetail::whereBookingPageSettingId($bookingPageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$bookingPageSettingDetail){
            $fields = $this->fields($bookingPageSetting, $language, $request);
        BookingPageSettingDetail::create($fields);
        }
        else{
            BookingPageSettingDetail::whereBookingPageSettingId($bookingPageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
