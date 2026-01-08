<?php

namespace App\Http\Resources\Admin;

use App\Models\FeaturesSettingDetail;
use Illuminate\Http\Resources\Json\JsonResource;

class PostRidePageSettingSubDetailResource extends JsonResource
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
            'post_ride_page_setting_id' => $this->post_ride_page_id,
            'language_id' => $this->language_id,
            'city_not_fount_contact_text' => $this->city_not_fount_contact_text,
            'language' => new LanguageResource($this->whenLoaded('language')),
            'extra_care_popup_eligible_text' => $this->extra_care_popup_eligible_text,
            'feilds_required_text' => $this->feilds_required_text,

        ];
    }
}
