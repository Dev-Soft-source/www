<?php

namespace App\Services;

use App\Models\TermsOfUsePageSettingDetail;

class TermsOfUsePageSettingService
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
                $validationRule = array_merge($validationRule, ['main_text.main_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_text.main_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($termsOfUsePageSetting, $language, $request)
    {
        return [
            'terms_use_page_id' => $termsOfUsePageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'main_text' => $this->data($request, $language, 'main_text'),
        ];
    }

    public function update($termsOfUsePageSetting, $language, $request)
    {
        $fields = $this->fields($termsOfUsePageSetting, $language, $request);
        $termsOfUsePageSettingDetail = TermsOfUsePageSettingDetail::whereTermsUsePageId($termsOfUsePageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$termsOfUsePageSettingDetail){
            $fields = $this->fields($termsOfUsePageSetting, $language, $request);
        TermsOfUsePageSettingDetail::create($fields);
        }
        else{
            TermsOfUsePageSettingDetail::whereTermsUsePageId($termsOfUsePageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
