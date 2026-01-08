<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MobileFindRideSettingDetailResource extends JsonResource
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
            'find_ride_page_setting_id' => $this->find_ride_setting_id,
            'language_id' => $this->language_id,
            'main_heading' => $this->main_heading,
            'search_section_from_label' => $this->search_section_from_label,
            'search_section_from_placeholder' => $this->search_section_from_placeholder,
            'search_section_to_label' => $this->search_section_to_label,
            'search_section_to_placeholder' => $this->search_section_to_placeholder,
            'search_section_keyword_label' => $this->search_section_keyword_label,
            'search_section_date_placeholder' => $this->search_section_date_placeholder,
            'search_section_button_label' => $this->search_section_button_label,
            'search_section_recent_searches' => $this->search_section_recent_searches,
            'card_section_at_label' => $this->card_section_at_label,
            'card_section_per_seat' => $this->card_section_per_seat,
            'card_section_age' => $this->card_section_age,
            'card_section_driven' => $this->card_section_driven,
            'card_section_review' => $this->card_section_review,
            'filter_section_heading' => $this->filter_section_heading,
            'filter1_driver_heading' => $this->filter1_driver_heading,
            'driver_age_label' => $this->driver_age_label,
            'driver_age_placeholder' => $this->driver_age_placeholder,
            'driver_rating_label' => $this->driver_rating_label,
            'driver_rating_placeholder' => $this->driver_rating_placeholder,
            'driver_phone_access_label' => $this->driver_phone_access_label,
            'driver_know_label' => $this->driver_know_label,
            'driver_know_placeholder' => $this->driver_know_placeholder,
            'filter2_passengers_heading' => $this->filter2_passengers_heading,
            'passengers_rating_label' => $this->passengers_rating_label,
            'passengers_rating_placeholder' => $this->passengers_rating_placeholder,
            'filter3_payment_methods_heading' => $this->filter3_payment_methods_heading,
            'payment_methods_option1' => $this->payment_methods_option1,
            'filter4_vehicle_heading' => $this->filter4_vehicle_heading,
            'vehicle_type_placeholder' => $this->vehicle_type_placeholder,
            'ride_preferences_label' => $this->ride_preferences_label,
            'luggage_label' => $this->luggage_label,
            'smoking_label' => $this->smoking_label,
            'pets_allowed_label' => $this->pets_allowed_label,
            'clear_button_label' => $this->clear_button_label,
            'apply_button_label' => $this->apply_button_label,
            'mobile_find_ride_setting' => new MobileFindRideSettingResource($this->whenLoaded('mobileFindRideSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
