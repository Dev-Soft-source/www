<?php

namespace App\Services;

use App\Models\MyStudentCardSettingDetail;

class MyStudentCardSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['mobile_indicate_required_field_label.mobile_indicate_required_field_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_indicate_required_field_label.mobile_indicate_required_field_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['student_card_description_text.student_card_description_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['student_card_description_text.student_card_description_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['student_card_image_placeholder.student_card_image_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['student_card_image_placeholder.student_card_image_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['choose_file_image_placeholder.choose_file_image_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['choose_file_image_placeholder.choose_file_image_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_image_type_placeholder.mobile_image_type_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_image_type_placeholder.mobile_image_type_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['month_placeholder.month_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['month_placeholder.month_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['year_placeholder.year_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['year_placeholder.year_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['upload_button_text.upload_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['upload_button_text.upload_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['update_button_text.update_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['update_button_text.update_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['upload_new_image_btn_label.upload_new_image_btn_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['upload_new_image_btn_label.upload_new_image_btn_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($myStudentCardSetting, $language, $request)
    {
        // dd($myStudentCardSetting);
        return [
            'student_card_setting_id' => $myStudentCardSetting->id,
            'language_id' => $language->id,
            'mobile_indicate_required_field_label' => $this->data($request, $language, 'mobile_indicate_required_field_label'),
            'student_card_description_text' => $this->data($request, $language, 'student_card_description_text'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'student_card_image_placeholder' => $this->data($request, $language, 'student_card_image_placeholder'),
            'choose_file_image_placeholder' => $this->data($request, $language, 'choose_file_image_placeholder'),
            'mobile_image_type_placeholder' => $this->data($request, $language, 'mobile_image_type_placeholder'),
            'expiry_date_label' => $this->data($request, $language, 'expiry_date_label'),
            'month_placeholder' => $this->data($request, $language, 'month_placeholder'),
            'year_placeholder' => $this->data($request, $language, 'year_placeholder'),
            'upload_button_text' => $this->data($request, $language, 'upload_button_text'),
            'update_button_text' => $this->data($request, $language, 'update_button_text'),
            'upload_new_image_btn_label' => $this->data($request, $language, 'upload_new_image_btn_label'),
        ];
    }

    public function update($myStudentCardSetting, $language, $request)
    {
        $fields = $this->fields($myStudentCardSetting, $language, $request);
        $myStudentCardSettingDetail = MyStudentCardSettingDetail::whereStudentCardSettingId($myStudentCardSetting->id)->whereLanguageId($language->id)->exists();
        if(!$myStudentCardSettingDetail){
            $fields = $this->fields($myStudentCardSetting, $language, $request);
            MyStudentCardSettingDetail::create($fields);
        }
        else{
            MyStudentCardSettingDetail::whereStudentCardSettingId($myStudentCardSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
