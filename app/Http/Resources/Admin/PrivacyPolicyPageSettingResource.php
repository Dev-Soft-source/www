<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PrivacyPolicyPageSettingResource extends JsonResource
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
            'privacy_policy_page_setting_detail' => PrivacyPolicyPageSettingDetailResource::collection($this->whenLoaded('privacyPolicyPageSettingDetail')),
        ];
    }
}
