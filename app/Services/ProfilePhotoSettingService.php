<?php

namespace App\Services;

use App\Models\ProfilePhotoSettingDetail;

class ProfilePhotoSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['name.name_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['name.name_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_upload_photo_tooltip.mobile_upload_photo_tooltip_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_upload_photo_tooltip.mobile_upload_photo_tooltip_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_upload_new_image_button_text.mobile_upload_new_image_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_upload_new_image_button_text.mobile_upload_new_image_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['save_button_text.save_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['save_button_text.save_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['upload_profile_photo_placeholder.upload_profile_photo_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['upload_profile_photo_placeholder.upload_profile_photo_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['choose_file_placeholder.choose_file_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['choose_file_placeholder.choose_file_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['images_option_placeholder.images_option_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['images_option_placeholder.images_option_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['photo_error.photo_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['photo_error.photo_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_indicate_required_field_label.mobile_indicate_required_field_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_indicate_required_field_label.mobile_indicate_required_field_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['sub_heading_text.sub_heading_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['sub_heading_text.sub_heading_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($profilePhotoSetting, $language, $request)
    {
        return [
            'profile_photo_setting_id' => $profilePhotoSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'mobile_upload_photo_tooltip' => $this->data($request, $language, 'mobile_upload_photo_tooltip'),
            'mobile_upload_new_image_button_text' => $this->data($request, $language, 'mobile_upload_new_image_button_text'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'save_button_text' => $this->data($request, $language, 'save_button_text'),
            'upload_profile_photo_placeholder' => $this->data($request, $language, 'upload_profile_photo_placeholder'),
            'choose_file_placeholder' => $this->data($request, $language, 'choose_file_placeholder'),
            'images_option_placeholder' => $this->data($request, $language, 'images_option_placeholder'),
            'photo_error' => $this->data($request, $language, 'photo_error'),
            'mobile_indicate_required_field_label' => $this->data($request, $language, 'mobile_indicate_required_field_label'),
            'sub_heading_text' => $this->data($request, $language, 'sub_heading_text'),

        ];
    }

    public function update($profilePhotoSetting, $language, $request)
    {
        $fields = $this->fields($profilePhotoSetting, $language, $request);
        $profilePhotoSettingDetail = ProfilePhotoSettingDetail::whereProfilePhotoSettingId($profilePhotoSetting->id)->whereLanguageId($language->id)->exists();
        if(!$profilePhotoSettingDetail){
            $fields = $this->fields($profilePhotoSetting, $language, $request);
            ProfilePhotoSettingDetail::create($fields);
        }
        else{
            ProfilePhotoSettingDetail::whereProfilePhotoSettingId($profilePhotoSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
