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
            'already_reveiwed_label' => $this->already_reveiwed_label,
            'already_reviewed_label' => $this->already_reviewed_label,
            'passenger_review_heading' => $this->passenger_review_heading,
            'passenger_review_criteria_heading' => $this->passenger_review_criteria_heading,
            'passenger_review_condition_label' => $this->passenger_review_condition_label,
            'passenger_review_conscious_label' => $this->passenger_review_conscious_label,
            'passenger_review_comfort_label' => $this->passenger_review_comfort_label,
            'passenger_review_communication_label' => $this->passenger_review_communication_label,
            'passenger_review_attitude_label' => $this->passenger_review_attitude_label,
            'passenger_review_hygiene_label' => $this->passenger_review_hygiene_label,
            'passenger_review_respect_label' => $this->passenger_review_respect_label,
            'passenger_review_safety_label' => $this->passenger_review_safety_label,
            'passenger_review_timeliness_label' => $this->passenger_review_timeliness_label,
            'my_review_setting' => new MyDriverSettingResource($this->whenLoaded('myReviewSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
