<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationsPageSettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'notifications_page_setting_detail' => NotificationsPageSettingDetailResource::collection($this->whenLoaded('notificationsPageSettingDetail')),
        ];
    }
}
