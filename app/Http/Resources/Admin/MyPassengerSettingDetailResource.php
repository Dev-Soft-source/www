<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MyPassengerSettingDetailResource extends JsonResource
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
            'my_passenger_setting_id' => $this->my_passenger_setting_id,
            'language_id' => $this->language_id,
            'remove_ride_btn_label' => $this->remove_ride_btn_label,
            'chat_passenger_btn_label' => $this->chat_passenger_btn_label,
            'main_heading' => $this->main_heading,
            'total_amount_label' => $this->total_amount_label,
            'my_fare_label' => $this->my_fare_label,
            'booking_fee_label' => $this->booking_fee_label,
            'seat_booked_label' => $this->seat_booked_label,
            'review_profile_label' => $this->review_profile_label,
            'age' => $this->age,
            'gender' => $this->gender,
            'co_passenger_main_heading' => $this->co_passenger_main_heading,
            'web_back_button_label' => $this->web_back_button_label,
            'no_show_passenger_label' => $this->no_show_passenger_label,
            'revert_no_show_passenger_label' => $this->revert_no_show_passenger_label,
            'web_i_reviewed_label' => $this->web_i_reviewed_label,
            'web_reviewd_label' => $this->web_reviewd_label,
            'my_passenger_setting' => new MyPassengerSettingResource($this->whenLoaded('myPassengerSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
