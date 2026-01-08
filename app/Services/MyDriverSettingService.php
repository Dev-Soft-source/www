<?php

namespace App\Services;

use App\Models\DriverSettingDetail;

class MyDriverSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['driver_license_description_text.driver_license_description_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_license_description_text.driver_license_description_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_license_label.driver_license_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_license_label.driver_license_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['web_upload_image_placeholder.web_upload_image_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['web_upload_image_placeholder.web_upload_image_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_driver_license_image_placeholder.mobile_driver_license_image_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_driver_license_image_placeholder.mobile_driver_license_image_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_image_type_placeholder.mobile_image_type_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_image_type_placeholder.mobile_image_type_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($myDriverSetting, $language, $request)
    {
        return [
            'driver_lic_setting_id' => $myDriverSetting->id,
            'language_id' => $language->id,
            'mobile_indicate_required_field_label' => $this->data($request, $language, 'mobile_indicate_required_field_label'),
            'driver_license_description_text' => $this->data($request, $language, 'driver_license_description_text'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'driver_license_label' => $this->data($request, $language, 'driver_license_label'),
            'web_upload_image_placeholder' => $this->data($request, $language, 'web_upload_image_placeholder'),
            'mobile_driver_license_image_placeholder' => $this->data($request, $language, 'mobile_driver_license_image_placeholder'),
            'mobile_choose_file_image_placeholder' => $this->data($request, $language, 'mobile_choose_file_image_placeholder'),
            'mobile_image_type_placeholder' => $this->data($request, $language, 'mobile_image_type_placeholder'),
            'upload_button_text' => $this->data($request, $language, 'upload_button_text'),
            'upload_new_image_btn_label' => $this->data($request, $language, 'upload_new_image_btn_label'),
            'update_button_text' => $this->data($request, $language, 'update_button_text'),

        ];
    }

    public function update($myDriverSetting, $language, $request)
    {
        $fields = $this->fields($myDriverSetting, $language, $request);
        $myDriverSettingDetail = DriverSettingDetail::whereDriverLicSettingId($myDriverSetting->id)->whereLanguageId($language->id)->exists();
        if(!$myDriverSettingDetail){
            $fields = $this->fields($myDriverSetting, $language, $request);
            DriverSettingDetail::create($fields);
        }
        else{
            DriverSettingDetail::whereDriverLicSettingId($myDriverSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
