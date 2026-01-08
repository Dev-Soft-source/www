<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfilePhotoSettingDetailResource extends JsonResource
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
            'profile_photo_setting_id' => $this->profile_photo_setting_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'mobile_upload_photo_tooltip' => $this->mobile_upload_photo_tooltip,
            'mobile_upload_new_image_button_text' => $this->mobile_upload_new_image_button_text,
            'main_heading' => $this->main_heading,
            'sub_heading_text' => $this->sub_heading_text,
            'save_button_text' => $this->save_button_text,
            'upload_profile_photo_placeholder' => $this->upload_profile_photo_placeholder,
            'choose_file_placeholder' => $this->choose_file_placeholder,
            'mobile_indicate_required_field_label' => $this->mobile_indicate_required_field_label,
            'images_option_placeholder' => $this->images_option_placeholder,
            'photo_error' => $this->photo_error,
            'profile_photo_setting' => new ProfilePhotoSettingResource($this->whenLoaded('profilePhotoSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
