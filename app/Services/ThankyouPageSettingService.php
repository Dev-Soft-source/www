<?php

namespace App\Services;

use App\Models\ThankyouPageSettingDetail;

class ThankyouPageSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['name.name_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['name.name_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($thankyouPageSetting, $language, $request)
    {
        return [
            'thankyou_page_setting_id' => $thankyouPageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'forget_close_btn_label' => $this->data($request, $language, 'forget_close_btn_label'),
            'forget_password_message' => $this->data($request, $language, 'forget_password_message'),
            'rest_password_btn_label' => $this->data($request, $language, 'rest_password_btn_label'),
            'good_bye_btn_label' => $this->data($request, $language, 'good_bye_btn_label'),
            'close_account_message' => $this->data($request, $language, 'close_account_message'),
            'account_close_heading' => $this->data($request, $language, 'account_close_heading'),
            'login_btn_label' => $this->data($request, $language, 'login_btn_label'),
            'done_btn_label' => $this->data($request, $language, 'done_btn_label'),
            'instant_booking_message' => $this->data($request, $language, 'instant_booking_message'),
            'manual_booking_message' => $this->data($request, $language, 'manual_booking_message'),
            'top_up_message' => $this->data($request, $language, 'top_up_message'),
            'welcome_page_title' => $this->data($request, $language, 'welcome_page_title'),
            'welcome_greeting' => $this->data($request, $language, 'welcome_greeting'),
            'welcome_paragraph_1' => $this->data($request, $language, 'welcome_paragraph_1'),
            'welcome_paragraph_2' => $this->data($request, $language, 'welcome_paragraph_2'),
            'welcome_paragraph_3' => $this->data($request, $language, 'welcome_paragraph_3'),
            'welcome_paragraph_4' => $this->data($request, $language, 'welcome_paragraph_4'),
            'welcome_paragraph_5' => $this->data($request, $language, 'welcome_paragraph_5'),
            'welcome_complete_profile_btn' => $this->data($request, $language, 'welcome_complete_profile_btn'),
            'welcome_closing_line1' => $this->data($request, $language, 'welcome_closing_line1'),
            'welcome_closing_line2' => $this->data($request, $language, 'welcome_closing_line2'),
            'welcome_closing_team_text' => $this->data($request, $language, 'welcome_closing_team_text'),
            'welcome_footer_help_contact' => $this->data($request, $language, 'welcome_footer_help_contact'),
            'welcome_footer_terms_use' => $this->data($request, $language, 'welcome_footer_terms_use'),
            'welcome_footer_coffee_on_wall' => $this->data($request, $language, 'welcome_footer_coffee_on_wall'),
        ];
    }

    public function update($thankyouPageSetting, $language, $request)
    {
        $fields = $this->fields($thankyouPageSetting, $language, $request);
        $thankyouPageSettingDetail = ThankyouPageSettingDetail::whereThankyouPageSettingId($thankyouPageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$thankyouPageSettingDetail){
            ThankyouPageSettingDetail::create($fields);
        }
        else{
            ThankyouPageSettingDetail::whereThankyouPageSettingId($thankyouPageSetting->id)->whereLanguageId($language->id)->first()?->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
