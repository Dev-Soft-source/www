<?php

namespace App\Services;

use App\Models\MyReviewSettingDetail;

class MyReviewSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['review_left_label.review_left_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['review_left_label.review_left_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['review_received_label.review_received_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['review_received_label.review_received_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['replied_label.replied_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['replied_label.replied_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['response_label.response_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['response_label.response_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['reply_label.reply_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['reply_label.reply_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_left_message.no_left_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_left_message.no_left_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_received_message.no_received_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_received_message.no_received_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['see_all_review_label.see_all_review_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['see_all_review_label.see_all_review_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($myReviewSetting, $language, $request)
    {
        // dd($myReviewSetting);
        return [
            'my_review_setting_id' => $myReviewSetting->id,
            'language_id' => $language->id,
            'review_left_label' => $this->data($request, $language, 'review_left_label'),
            'review_received_label' => $this->data($request, $language, 'review_received_label'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'replied_label' => $this->data($request, $language, 'replied_label'),
            'response_label' => $this->data($request, $language, 'response_label'),
            'reply_label' => $this->data($request, $language, 'reply_label'),
            'no_more_data_label' => $this->data($request, $language, 'no_more_data_label'),
            'no_left_message' => $this->data($request, $language, 'no_left_message'),
            'no_received_message' => $this->data($request, $language, 'no_received_message'),
            'reply_heading_label' => $this->data($request, $language, 'reply_heading_label'),
            'reply_placeholder' => $this->data($request, $language, 'reply_placeholder'),
            'see_all_review_label' => $this->data($request, $language, 'see_all_review_label'),
            'reply_submit_button_label' => $this->data($request, $language, 'reply_submit_button_label'),
            'review_label' => $this->data($request, $language, 'review_label'),
        ];
    }

    public function update($myReviewSetting, $language, $request)
    {
        $fields = $this->fields($myReviewSetting, $language, $request);
        $myReviewSettingDetail = MyReviewSettingDetail::whereMyReviewSettingId($myReviewSetting->id)->whereLanguageId($language->id)->exists();
        if(!$myReviewSettingDetail){
            $fields = $this->fields($myReviewSetting, $language, $request);
            MyReviewSettingDetail::create($fields);
        }
        else{
            MyReviewSettingDetail::whereMyReviewSettingId($myReviewSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
