<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MyReviewSettingDetailResource extends JsonResource
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
            'my_review_setting_id' => $this->my_review_setting_id,
            'language_id' => $this->language_id,
            'review_left_label' => $this->review_left_label,
            'review_received_label' => $this->review_received_label,
            'response_label' => $this->response_label,
            'main_heading' => $this->main_heading,
            'replied_label' => $this->replied_label,
            'reply_label' => $this->reply_label,
            'no_more_data_label' => $this->no_more_data_label,
            'no_left_message' => $this->no_left_message,
            'no_received_message' => $this->no_received_message,
            'reply_submit_button_label' => $this->reply_submit_button_label,
            'reply_placeholder' => $this->reply_placeholder,
            'reply_heading_label' => $this->reply_heading_label,
            'see_all_review_label' => $this->see_all_review_label,
            'review_label' => $this->review_label,
            'my_review_setting' => new MyDriverSettingResource($this->whenLoaded('myReviewSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
