<?php

namespace App\Services;

use App\Models\EditProfilePageSettingDetail;

class EditProfileSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['min_bio_label.min_bio_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['min_bio_label.min_bio_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_driven_label.passenger_driven_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_driven_label.passenger_driven_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['rides_taken_label.rides_taken_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['rides_taken_label.rides_taken_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['km_shared_icon.km_shared_icon_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['km_shared_icon.km_shared_icon_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_driven_icon.passenger_driven_icon_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_driven_icon.passenger_driven_icon_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['rides_taken_icon.rides_taken_icon_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['rides_taken_icon.rides_taken_icon_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($editProfileSetting, $language, $request)
    {
        return [
            'edit_profile_id' => $editProfileSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'min_bio_label' => $this->data($request, $language, 'min_bio_label'),
            'passenger_driven_label' => $this->data($request, $language, 'passenger_driven_label'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'rides_taken_label' => $this->data($request, $language, 'rides_taken_label'),
            'km_shared_label' => $this->data($request, $language, 'km_shared_label'),
            'km_shared_icon' => $this->data($request, $language, 'km_shared_icon'),
            'passenger_driven_icon' => $this->data($request, $language, 'passenger_driven_icon'),
            'rides_taken_icon' => $this->data($request, $language, 'rides_taken_icon'),
            'review_label' => $this->data($request, $language, 'review_label'),
            'reply_label' => $this->data($request, $language, 'reply_label'),
            'link_review_label' => $this->data($request, $language, 'link_review_label'),
            'review_heading' => $this->data($request, $language, 'review_heading'),
            'edit_profile_text' => $this->data($request, $language, 'edit_profile_text'),
            'first_name_label' => $this->data($request, $language, 'first_name_label'),
            'first_name_placeholder' => $this->data($request, $language, 'first_name_placeholder'),
            'last_name_label' => $this->data($request, $language, 'last_name_label'),
            'last_name_placeholder' => $this->data($request, $language, 'last_name_placeholder'),
            'gender_label' => $this->data($request, $language, 'gender_label'),
            'male_label' => $this->data($request, $language, 'male_label'),
            'female_label' => $this->data($request, $language, 'female_label'),
            'prefer_no_to_say_label' => $this->data($request, $language, 'prefer_no_to_say_label'),
            'dob_label' => $this->data($request, $language, 'dob_label'),
            'dob_placeholder' => $this->data($request, $language, 'dob_placeholder'),
            'country_label' => $this->data($request, $language, 'country_label'),
            'country_placeholder' => $this->data($request, $language, 'country_placeholder'),
            'state_label' => $this->data($request, $language, 'state_label'),
            'state_placeholder' => $this->data($request, $language, 'state_placeholder'),
            'city_label' => $this->data($request, $language, 'city_label'),
            'city_placeholder' => $this->data($request, $language, 'city_placeholder'),
            'address_label' => $this->data($request, $language, 'address_label'),
            'address_placeholder' => $this->data($request, $language, 'address_placeholder'),
            'zip_label' => $this->data($request, $language, 'zip_label'),
            'mini_bio_label' => $this->data($request, $language, 'mini_bio_label'),
            'mini_bio_placeholder' => $this->data($request, $language, 'mini_bio_placeholder'),
            'govt_id_label' => $this->data($request, $language, 'govt_id_label'),
            'govt_id_text' => $this->data($request, $language, 'govt_id_text'),
            'image_placeholder' => $this->data($request, $language, 'image_placeholder'),
            'choose_file_placeholder' => $this->data($request, $language, 'choose_file_placeholder'),
            'image_option_placeholder' => $this->data($request, $language, 'image_option_placeholder'),
            'new_image_button_text' => $this->data($request, $language, 'new_image_button_text'),
            'save_button_text' => $this->data($request, $language, 'save_button_text'),
            'joined_label' => $this->data($request, $language, 'joined_label'),
            'passenger_label' => $this->data($request, $language, 'passenger_label'),
            'vehicle_info_label' => $this->data($request, $language, 'vehicle_info_label'),
            'year_old_label' => $this->data($request, $language, 'year_old_label'),
            'replied_label' => $this->data($request, $language, 'replied_label'),
            'response_label' => $this->data($request, $language, 'response_label'),
            'reply_heading_label' => $this->data($request, $language, 'reply_heading_label'),
            'reply_placeholder' => $this->data($request, $language, 'reply_placeholder'),
            'reply_submit_button_label' => $this->data($request, $language, 'reply_submit_button_label'),
            'profile_label' => $this->data($request, $language, 'profile_label'),
            ];
    }

    public function update($editProfileSetting, $language, $request)
    {
        $fields = $this->fields($editProfileSetting, $language, $request);
        $editProfileSettingDetail = EditProfilePageSettingDetail::whereEditProfileId($editProfileSetting->id)->whereLanguageId($language->id)->exists();
        if(!$editProfileSettingDetail){
            $fields = $this->fields($editProfileSetting, $language, $request);
            EditProfilePageSettingDetail::create($fields);
        }
        else{
            EditProfilePageSettingDetail::whereEditProfileId($editProfileSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
