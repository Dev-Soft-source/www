<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class EditProfilePageSettingDetailResource extends JsonResource
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
            'edit_profile_id' => $this->edit_profile_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'min_bio_label' => $this->min_bio_label,
            'main_heading' => $this->main_heading,
            'passenger_driven_label' => $this->passenger_driven_label,
            'rides_taken_label' => $this->rides_taken_label,
            'km_shared_label' => $this->km_shared_label,
            'km_shared_icon' => $this->km_shared_icon,
            'passenger_driven_icon' => $this->passenger_driven_icon,
            'rides_taken_icon' => $this->rides_taken_icon,
            'review_label' => $this->review_label,
            'reply_label' => $this->reply_label,
            'link_review_label' => $this->link_review_label,
            'review_heading' => $this->review_heading,
            'edit_profile_text' => $this->edit_profile_text,
            'first_name_label' => $this->first_name_label,
            'first_name_placeholder' => $this->first_name_placeholder,
            'last_name_label' => $this->last_name_label,
            'last_name_placeholder' => $this->last_name_placeholder,
            'gender_label' => $this->gender_label,
            'male_label' => $this->male_label,
            'female_label' => $this->female_label,
            'dob_label' => $this->dob_label,
            'dob_placeholder' => $this->dob_placeholder,
            'country_label' => $this->country_label,
            'country_placeholder' => $this->country_placeholder,
            'state_label' => $this->state_label,
            'state_placeholder' => $this->state_placeholder,
            'city_label' => $this->city_label,
            'city_placeholder' => $this->city_placeholder,
            'address_label' => $this->address_label,
            'address_placeholder' => $this->address_placeholder,
            'zip_label' => $this->zip_label,
            'mini_bio_label' => $this->mini_bio_label,
            'mini_bio_placeholder' => $this->mini_bio_placeholder,
            'govt_id_label' => $this->govt_id_label,
            'govt_id_text' => $this->govt_id_text,
            'image_placeholder' => $this->image_placeholder,
            'choose_file_placeholder' => $this->choose_file_placeholder,
            'image_option_placeholder' => $this->image_option_placeholder,
            'new_image_button_text' => $this->new_image_button_text,
            'save_button_text' => $this->save_button_text,
            'joined_label' => $this->joined_label,
            'passenger_label' => $this->passenger_label,
            'year_old_label' => $this->year_old_label,
            'vehicle_info_label' => $this->vehicle_info_label,
            'replied_label' => $this->replied_label,
            'response_label' => $this->response_label,
            'reply_heading_label' => $this->reply_heading_label,
            'reply_placeholder' => $this->reply_placeholder,
            'reply_submit_button_label' => $this->reply_submit_button_label,
            'profile_label' => $this->profile_label,
            'prefer_no_to_say_label' => $this->prefer_no_to_say_label,
            'language' => $this->when($this->relationLoaded('language'), function() {
                return [
                    'id' => $this->language->id ?? null,
                    'name' => $this->language->name ?? null,
                ];
            }),
        ];
    }
}
