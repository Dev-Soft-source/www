<?php

namespace App\Services;

use App\Models\ProfileSettingDetail;

class ProfileSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['profile_photo_label.profile_photo_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['profile_photo_label.profile_photo_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['my_vehicles_label.my_vehicles_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['my_vehicles_label.my_vehicles_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['password_label.password_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['password_label.password_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['my_phone_number_label.my_phone_number_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['my_phone_number_label.my_phone_number_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['my_email_address_label.my_email_address_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['my_email_address_label.my_email_address_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['my_driver_license_label.my_driver_license_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['my_driver_license_label.my_driver_license_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['my_student_card_label.my_student_card_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['my_student_card_label.my_student_card_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['referrals_label.referrals_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['referrals_label.referrals_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($profileSetting, $language, $request)
    {
        return [
            'profile_setting_id' => $profileSetting->id,
            'language_id' => $language->id,
            'profile_photo_label' => $this->data($request, $language, 'profile_photo_label'),
            'my_vehicles_label' => $this->data($request, $language, 'my_vehicles_label'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'password_label' => $this->data($request, $language, 'password_label'),
            'my_phone_number_label' => $this->data($request, $language, 'my_phone_number_label'),
            'my_email_address_label' => $this->data($request, $language, 'my_email_address_label'),
            'my_driver_license_label' => $this->data($request, $language, 'my_driver_license_label'),
            'my_student_card_label' => $this->data($request, $language, 'my_student_card_label'),
            'referrals_label' => $this->data($request, $language, 'referrals_label'),

        ];
    }

    public function update($profileSetting, $language, $request)
    {
        $fields = $this->fields($profileSetting, $language, $request);
        $profileSettingDetail = ProfileSettingDetail::whereProfileSettingId($profileSetting->id)->whereLanguageId($language->id)->exists();
        if(!$profileSettingDetail){
            $fields = $this->fields($profileSetting, $language, $request);
            ProfileSettingDetail::create($fields);
        }
        else{
            ProfileSettingDetail::whereProfileSettingId($profileSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
