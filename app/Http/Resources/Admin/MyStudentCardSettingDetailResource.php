<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MyStudentCardSettingDetailResource extends JsonResource
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
            'student_card_setting_id' => $this->student_card_setting_id,
            'language_id' => $this->language_id,
            'main_heading' => $this->main_heading,
            'mobile_indicate_required_field_label' => $this->mobile_indicate_required_field_label,
            'student_card_description_text' => $this->student_card_description_text,
            'student_card_image_placeholder' => $this->student_card_image_placeholder,
            'choose_file_image_placeholder' => $this->choose_file_image_placeholder,
            'mobile_image_type_placeholder' => $this->mobile_image_type_placeholder,
            'expiry_date_label' => $this->expiry_date_label,
            'month_placeholder' => $this->month_placeholder,
            'year_placeholder' => $this->year_placeholder,
            'upload_button_text' => $this->upload_button_text,
            'update_button_text' => $this->update_button_text,
            'upload_new_image_btn_label' => $this->upload_new_image_btn_label,
            'my_student_card_setting' => new MyStudentCardSettingResource($this->whenLoaded('myStudentCardSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
