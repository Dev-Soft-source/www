<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfilePhotoGuidelinesPageSettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'profile_photo_guidelines_page_setting_detail' => ProfilePhotoGuidelinesPageSettingDetailResource::collection($this->whenLoaded('profilePhotoGuidelinesPageSettingDetail')),
        ];
    }
}
