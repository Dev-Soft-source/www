<?php

namespace App\Services;

use App\Models\SelectLocationSettingDetail;

class SelectLocationPageSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['select_origin_label.select_origin_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['select_origin_label.select_origin_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['search_origin_label.search_origin_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_origin_label.search_origin_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_origin_label.no_origin_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_origin_label.no_origin_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['select_destination_label.select_destination_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['select_destination_label.select_destination_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['search_destination_label.search_destination_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_destination_label.search_destination_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_destination_label.no_destination_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_destination_label.no_destination_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['select_country_label.select_country_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['select_country_label.select_country_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['search_country_label.search_country_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_country_label.search_country_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_country_label.no_country_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_country_label.no_country_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['select_state_label.select_state_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['select_state_label.select_state_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['select_state_first_label.select_state_first_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['select_state_first_label.select_state_first_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['search_state_label.search_state_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_state_label.search_state_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_state_label.no_state_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_state_label.no_state_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['select_city_label.select_city_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['select_city_label.select_city_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['search_city_label.search_city_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['search_city_label.search_city_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_city_label.no_city_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_city_label.no_city_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($selectLocationPageSetting, $language, $request)
    {
        return [
            'location_setting_id' => $selectLocationPageSetting->id,
            'language_id' => $language->id,
            'select_origin_label' => $this->data($request, $language, 'select_origin_label'),
            'search_origin_label' => $this->data($request, $language, 'search_origin_label'),
            'no_origin_label' => $this->data($request, $language, 'no_origin_label'),
            'select_destination_label' => $this->data($request, $language, 'select_destination_label'),
            'search_destination_label' => $this->data($request, $language, 'search_destination_label'),
            'no_destination_label' => $this->data($request, $language, 'no_destination_label'),
            'select_country_label' => $this->data($request, $language, 'select_country_label'),
            'search_country_label' => $this->data($request, $language, 'search_country_label'),
            'no_country_label' => $this->data($request, $language, 'no_country_label'),
            'select_state_label' => $this->data($request, $language, 'select_state_label'),
            'select_state_first_label' => $this->data($request, $language, 'select_state_first_label'),
            'search_state_label' => $this->data($request, $language, 'search_state_label'),
            'no_state_label' => $this->data($request, $language, 'no_state_label'),
            'select_city_label' => $this->data($request, $language, 'select_city_label'),
            'search_city_label' => $this->data($request, $language, 'search_city_label'),
            'no_city_label' => $this->data($request, $language, 'no_city_label'),
        ];
    }

    public function update($selectLocationPageSetting, $language, $request)
    {
        $fields = $this->fields($selectLocationPageSetting, $language, $request);
        $selectLocationPageSettingDetail = SelectLocationSettingDetail::whereLocationSettingId($selectLocationPageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$selectLocationPageSettingDetail){
            $fields = $this->fields($selectLocationPageSetting, $language, $request);
            SelectLocationSettingDetail::create($fields);
        }
        else{
            SelectLocationSettingDetail::whereLocationSettingId($selectLocationPageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
