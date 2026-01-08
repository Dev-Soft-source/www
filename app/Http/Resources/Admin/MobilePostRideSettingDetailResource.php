<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MobilePostRideSettingDetailResource extends JsonResource
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
            'post_ride_page_setting_id' => $this->post_ride_setting_id,
            'language_id' => $this->language_id,
            'main_heading' => $this->main_heading,
            'post_arrived_again_label' => $this->post_arrived_again_label,
            'ride_info_heading' => $this->ride_info_heading,
            'from_label' => $this->from_label,
            'from_placeholder' => $this->from_placeholder,
            'to_label' => $this->to_label,
            'to_placeholder' => $this->to_placeholder,
            'pick_up_label' => $this->pick_up_label,
            'pick_up_placeholder' => $this->pick_up_placeholder,
            'drop_off_label' => $this->drop_off_label,
            'drop_off_placeholder' => $this->drop_off_placeholder,
            'date_time_label' => $this->date_time_label,
            'at_label' => $this->at_label,
            'recurring_label' => $this->recurring_label,
            'recurring_type_label' => $this->recurring_type_label,
            'recurring_trips_label' => $this->recurring_trips_label,
            'meeting_drop_off_description_label' => $this->meeting_drop_off_description_label,
            'meeting_drop_off_description_placeholder' => $this->meeting_drop_off_description_placeholder,
            'seats_label' => $this->seats_label,
            'seats_middle_label' => $this->seats_middle_label,
            'seats_back_label' => $this->seats_back_label,
            'vehicle_label' => $this->vehicle_label,
            'skip_label' => $this->skip_label,
            'add_vehicle_label' => $this->add_vehicle_label,
            'make_label' => $this->make_label,
            'make_placeholder' => $this->make_placeholder,
            'model_label' => $this->model_label,
            'model_placeholder' => $this->model_placeholder,
            'type_label' => $this->type_label,
            'year_label' => $this->year_label,
            'color_label' => $this->color_label,
            'liscense_label' => $this->liscense_label,
            'electric_car_label' => $this->electric_car_label,
            'hybrid_car_label' => $this->hybrid_car_label,
            'car_photo_label' => $this->car_photo_label,
            'preferences_label' => $this->preferences_label,
            'smoking_label' => $this->smoking_label,
            'animals_label' => $this->animals_label,
            'booking_label' => $this->booking_label,
            'booking_option1' => $this->booking_option1,
            'booking_option2' => $this->booking_option2,
            'luggage_label' => $this->luggage_label,
            'luggage_checkbox_label1' => $this->luggage_checkbox_label1,
            'luggage_checkbox_label2' => $this->luggage_checkbox_label2,
            'price_payment_heading' => $this->price_payment_heading,
            'price_per_seat_label' => $this->price_per_seat_label,
            'payment_methods_label' => $this->payment_methods_label,
            'anything_to_add_label' => $this->anything_to_add_label,
            'anything_to_add_placeholder' => $this->anything_to_add_placeholder,
            'disclaimers_label' => $this->disclaimers_label,
            'disclaimers_description' => $this->disclaimers_description,
            'agree_terms_label' => $this->agree_terms_label,
            'submit_button_label' => $this->submit_button_label,
            'mobile_post_ride_setting' => new MobilePostRideSettingResource($this->whenLoaded('mobilePostRideSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
