<?php

namespace App\Services;

use App\Models\ChatsPageSettingDetail;

class ChatsPageSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['name.name_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['name.name_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['meta_keywords.meta_keywords_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['meta_keywords.meta_keywords_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['meta_description.meta_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['meta_description.meta_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['old_messages_heading.old_messages_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['old_messages_heading.old_messages_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_messages_label.no_messages_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_messages_label.no_messages_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['old_chat_page_main_heading.old_chat_page_main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['old_chat_page_main_heading.old_chat_page_main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['old_chat_page_no_messages_label.old_chat_page_no_messages_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['old_chat_page_no_messages_label.old_chat_page_no_messages_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['notification_page_main_heading.notification_page_main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['notification_page_main_heading.notification_page_main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['notification_page_no_messages_label.notification_page_no_messages_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['notification_page_no_messages_label.notification_page_no_messages_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['notification_filter_btn_label.notification_filter_btn_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['notification_filter_btn_label.notification_filter_btn_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['notification_confirm_message.notification_confirm_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['notification_confirm_message.notification_confirm_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);


                $validationRule = array_merge($validationRule, ['notification_delete_text.notification_delete_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['notification_delete_text.notification_delete_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['type_message_placeholder.type_message_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['type_message_placeholder.type_message_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['delete_messages_label.delete_messages_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['delete_messages_label.delete_messages_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($chatsPageSetting, $language, $request)
    {
        return [
            'chats_page_setting_id' => $chatsPageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'old_messages_heading' => $this->data($request, $language, 'old_messages_heading'),
            'no_messages_label' => $this->data($request, $language, 'no_messages_label'),
            'old_chat_page_main_heading' => $this->data($request, $language, 'old_chat_page_main_heading'),
            'old_chat_page_no_messages_label' => $this->data($request, $language, 'old_chat_page_no_messages_label'),
            'notification_page_main_heading' => $this->data($request, $language, 'notification_page_main_heading'),
            'notification_page_no_messages_label' => $this->data($request, $language, 'notification_page_no_messages_label'),
            'navigation_my_trip_label' => $this->data($request, $language, 'navigation_my_trip_label'),
            'navigation_chat_label' => $this->data($request, $language, 'navigation_chat_label'),
            'navigation_my_profile_label' => $this->data($request, $language, 'navigation_my_profile_label'),
            'exit_app_label' => $this->data($request, $language, 'exit_app_label'),
            'notification_filter_btn_label' => $this->data($request, $language, 'notification_filter_btn_label'),
            'notification_confirm_message' => $this->data($request, $language, 'notification_confirm_message'),
            'notification_delete_text' => $this->data($request, $language, 'notification_delete_text'),
            'type_message_placeholder' => $this->data($request, $language, 'type_message_placeholder'),
            'delete_messages_label' => $this->data($request, $language, 'delete_messages_label'),
        ];
    }

    public function update($chatsPageSetting, $language, $request)
    {
        $fields = $this->fields($chatsPageSetting, $language, $request);
        $chatsPageSettingDetail = ChatsPageSettingDetail::whereChatsPageSettingId($chatsPageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$chatsPageSettingDetail){
            $fields = $this->fields($chatsPageSetting, $language, $request);
            ChatsPageSettingDetail::create($fields);
        }
        else{
            ChatsPageSettingDetail::whereChatsPageSettingId($chatsPageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
