<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileSettingDetailResource extends JsonResource
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
            'profile_setting_id' => $this->profile_setting_id,
            'language_id' => $this->language_id,
            'profile_photo_label' => $this->profile_photo_label,
            'my_vehicles_label' => $this->my_vehicles_label,
            'main_heading' => $this->main_heading,
            'password_label' => $this->password_label,
            'my_phone_number_label' => $this->my_phone_number_label,
            'my_email_address_label' => $this->my_email_address_label,
            'my_driver_license_label' => $this->my_driver_license_label,
            'my_student_card_label' => $this->my_student_card_label,
            'referrals_label' => $this->referrals_label,
            'profile_setting' => new ProfileSettingResource($this->whenLoaded('profileSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
