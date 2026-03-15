<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationsPageSettingDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'notifications_page_setting_id' => $this->notifications_page_setting_id,
            'language_id' => $this->language_id,
            'info_bar_title' => $this->info_bar_title,
            'info_paragraph_ride' => $this->info_paragraph_ride,
            'info_paragraph_inbox' => $this->info_paragraph_inbox,
            'info_paragraph_general' => $this->info_paragraph_general,
            'mark_all_as_read_button_label' => $this->mark_all_as_read_button_label,
            'unread_label' => $this->unread_label,
            'no_notifications_found_label' => $this->no_notifications_found_label,
            'caught_up_label' => $this->caught_up_label,
            'delete_button_label' => $this->delete_button_label,
            'language' => $this->when($this->relationLoaded('language'), function() {
                return [
                    'id' => $this->language->id ?? null,
                    'name' => $this->language->name ?? null,
                ];
            }),
        ];
    }
}
