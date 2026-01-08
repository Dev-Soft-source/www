<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class TermsAndConditionPageSettingResource extends JsonResource
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
            'created_at' => $this->created_at,
            'terms_and_condition_page_setting_detail' => TermsAndConditionPageSettingDetailResource::collection($this->whenLoaded('termsAndConditionPageSettingDetail')),
        ];
    }
}
