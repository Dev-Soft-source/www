<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class RewardPointSettingDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'reward_point_setting_id' => $this->reward_point_setting_id,
            'language_id' => $this->language_id,
            'reward_name' => $this->reward_name,
            'rewardPointSetting' => new RewardPointSettingResource($this->whenLoaded('rewardPointSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}