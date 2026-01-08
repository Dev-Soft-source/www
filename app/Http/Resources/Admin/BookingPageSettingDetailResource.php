<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingPageSettingDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'booking_page_setting_id' => $this->booking_page_setting_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'seats_available_label' => $this->seats_available_label,
            'cancellation_policy_label' => $this->cancellation_policy_label,
            'pricing_label' => $this->pricing_label,
            'seat_label' => $this->seat_label,
            'booking_fee_label' => $this->booking_fee_label,
            'booking_label' => $this->booking_label,
            'paypal_label' => $this->paypal_label,
            'co_passenger_label' => $this->co_passenger_label,
            'payment_method_label' => $this->payment_method_label,
            'luggage_label' => $this->luggage_label,
            'ride_features_label' => $this->ride_features_label,
            'required_fields' => $this->required_fields,
            'total_label' => $this->total_label,
            'message_to_driver_label' => $this->message_to_driver_label,
            'message_driver_placeholder' => $this->message_driver_placeholder,
            'book_seat_button_label' => $this->book_seat_button_label,
            'like_to_pay_label' => $this->like_to_pay_label,
            'credit_card_label' => $this->credit_card_label,
            'select_card_label' => $this->select_card_label,
            'add_card_label' => $this->add_card_label,
            'pay_label' => $this->pay_label,
            'coffee_from_wall_label' => $this->coffee_from_wall_label,
            'coffee_from_wall_tooltip' => $this->coffee_from_wall_tooltip,
            'payable_amount_label' => $this->payable_amount_label,
            'coffee_from_amount_wall_tooltip' => $this->coffee_from_amount_wall_tooltip,
            'seats_available_info_text' => $this->seats_available_info_text,
            'tax_label' => $this->tax_label,
            'booking_disclaimer_on_time' => $this->booking_disclaimer_on_time,
            'booking_disclaimer_pink_ride' => $this->booking_disclaimer_pink_ride,
            'booking_disclaimer_extra_care_ride' => $this->booking_disclaimer_extra_care_ride,
            'booking_disclaimer_firm' => $this->booking_disclaimer_firm,
            'booking_term_agree_text' => $this->booking_term_agree_text,
            'booking_pink_ride_term_agree_text' => $this->booking_pink_ride_term_agree_text,
            'booking_extra_care_ride_term_agree_text' => $this->booking_extra_care_ride_term_agree_text,
            'firm_cancellation_label_price_section' => $this->firm_cancellation_label_price_section,
            'firm_discount_label_price_section' => $this->firm_discount_label_price_section,
            'firm_your_price_label_price_section' => $this->firm_your_price_label_price_section,
            'booking_cancellation_limit_exceed' => $this->booking_cancellation_limit_exceed,
            'language' => $this->when($this->relationLoaded('language'), function() {
                return [
                    'id' => $this->language->id ?? null,
                    'name' => $this->language->name ?? null,
                ];
            }),
        ];
    }
}
