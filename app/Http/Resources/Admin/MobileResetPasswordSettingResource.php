<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MobileResetPasswordSettingResource extends JsonResource
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
            'mobile_reset_password_setting_detail' => MobileResetPasswordSettingDetailResource::collection($this->whenLoaded('mobileResetPasswordSettingDetail')),
        ];
    }
}
