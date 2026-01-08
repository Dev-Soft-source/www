<?php

namespace App\Services;

use App\Models\StudentPageSettingDetail;

class StudentPageSettingService
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
                $validationRule = array_merge($validationRule, ['sub_heading.sub_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['sub_heading.sub_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['page_image.page_image_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['page_image.page_image_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['page_description.page_description_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['page_description.page_description_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($studentPageSetting, $language, $request)
    {
        return [
            'student_page_setting_id' => $studentPageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'sub_heading' => $this->data($request, $language, 'sub_heading'),
            'page_image' => $this->data($request, $language, 'page_image'),
            'page_description' => $this->data($request, $language, 'page_description'),
        ];
    }

    public function update($studentPageSetting, $language, $request)
    {
        $fields = $this->fields($studentPageSetting, $language, $request);
        $studentPageSettingDetail = StudentPageSettingDetail::whereStudentPageSettingId($studentPageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$studentPageSettingDetail){
            $fields = $this->fields($studentPageSetting, $language, $request);
        StudentPageSettingDetail::create($fields);
        }
        else{
            StudentPageSettingDetail::whereStudentPageSettingId($studentPageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
