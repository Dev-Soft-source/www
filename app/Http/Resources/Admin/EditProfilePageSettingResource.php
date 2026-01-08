<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class EditProfilePageSettingResource extends JsonResource
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
            'id' => $this->id ?? null,
            'created_at' => $this->created_at ?? null,
            'edit_profile_page_setting_detail' => $this->when(
                $this->relationLoaded('editProfilePageSettingDetail'),
                EditProfilePageSettingDetailResource::collection($this->editProfilePageSettingDetail ?? [])
            ),
        ];
    }
}
