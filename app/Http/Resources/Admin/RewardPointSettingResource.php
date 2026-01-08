<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class RewardPointSettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'point' => $this->point,
            'reward_point_setting_detail' => RewardPointSettingDetailResource::collection($this->whenLoaded('rewardPointSettingDetail')),
        ];
    }
}
