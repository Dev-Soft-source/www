<?php

namespace App\Services;

use App\Models\NotificationsPageSettingDetail;

class NotificationsPageSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['info_bar_title.info_bar_title_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['info_bar_title.info_bar_title_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($notificationsPageSetting, $language, $request)
    {
        return [
            'notifications_page_setting_id' => $notificationsPageSetting->id,
            'language_id' => $language->id,
            'info_bar_title' => $this->data($request, $language, 'info_bar_title'),
            'info_paragraph_ride' => $this->data($request, $language, 'info_paragraph_ride'),
            'info_paragraph_inbox' => $this->data($request, $language, 'info_paragraph_inbox'),
            'info_paragraph_general' => $this->data($request, $language, 'info_paragraph_general'),
            'mark_all_as_read_button_label' => $this->data($request, $language, 'mark_all_as_read_button_label'),
            'unread_label' => $this->data($request, $language, 'unread_label'),
            'no_notifications_found_label' => $this->data($request, $language, 'no_notifications_found_label'),
            'caught_up_label' => $this->data($request, $language, 'caught_up_label'),
            'delete_button_label' => $this->data($request, $language, 'delete_button_label'),
        ];
    }

    public function update($notificationsPageSetting, $language, $request)
    {
        $fields = $this->fields($notificationsPageSetting, $language, $request);
        $notificationsPageSettingDetail = NotificationsPageSettingDetail::where('notifications_page_setting_id', $notificationsPageSetting->id)->where('language_id', $language->id)->exists();
        if(!$notificationsPageSettingDetail){
            NotificationsPageSettingDetail::create($fields);
        }
        else{
            NotificationsPageSettingDetail::where('notifications_page_setting_id', $notificationsPageSetting->id)->where('language_id', $language->id)->first()?->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
