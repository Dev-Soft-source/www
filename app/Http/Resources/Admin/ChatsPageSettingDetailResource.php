<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatsPageSettingDetailResource extends JsonResource
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
            'chats_page_setting_id' => $this->chats_page_setting_id,
            'language_id' => $this->language_id,
            'name' => $this->name,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'main_heading' => $this->main_heading,
            'old_messages_heading' => $this->old_messages_heading,
            'no_messages_label' => $this->no_messages_label,
            'old_chat_page_main_heading' => $this->old_chat_page_main_heading,
            'old_chat_page_no_messages_label' => $this->old_chat_page_no_messages_label,
            'notification_page_main_heading' => $this->notification_page_main_heading,
            'notification_page_no_messages_label' => $this->notification_page_no_messages_label,
            'navigation_my_profile_label' => $this->navigation_my_profile_label,
            'navigation_my_trip_label' => $this->navigation_my_trip_label,
            'navigation_chat_label' => $this->navigation_chat_label,
            'exit_app_label' => $this->exit_app_label,
            'notification_filter_btn_label' => $this->notification_filter_btn_label,
            'notification_confirm_message' => $this->notification_confirm_message,
            'notification_delete_text' => $this->notification_delete_text,
            'type_message_placeholder' => $this->type_message_placeholder,
            'delete_messages_label' => $this->delete_messages_label,
            'language' => $this->when($this->relationLoaded('language'), function() {
                return [
                    'id' => $this->language->id ?? null,
                    'name' => $this->language->name ?? null,
                ];
            }),
        ];
    }
}
