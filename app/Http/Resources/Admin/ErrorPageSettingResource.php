<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ErrorPageSettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id ?? null,
            'created_at' => $this->created_at ?? null,
            'error_page_setting_detail' => $this->when(
                $this->relationLoaded('errorPageSettingDetail'),
                ErrorPageSettingDetailResource::collection($this->errorPageSettingDetail ?? [])
            ),
        ];
    }
}
