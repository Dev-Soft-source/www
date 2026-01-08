<?php

namespace App\Services;

use App\Models\Step3PageSettingDetail;

class Step3PageSettingService
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
                $validationRule = array_merge($validationRule, ['main_label.main_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_label.main_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['required_label.required_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['required_label.required_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['make_label.make_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['make_label.make_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['make_error.make_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['make_error.make_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['model_label.model_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['model_label.model_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['model_error.model_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['model_error.model_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['vehicle_type_label.vehicle_type_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['vehicle_type_label.vehicle_type_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['vehicle_type_error.vehicle_type_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['vehicle_type_error.vehicle_type_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['color_label.color_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['color_label.color_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['color_error.color_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['color_error.color_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['license_label.license_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['license_label.license_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['license_error.license_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['license_error.license_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['year_label.year_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['year_label.year_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['year_error.year_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['year_error.year_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['fuel_label.fuel_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['fuel_label.fuel_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['fuel_error.fuel_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['fuel_error.fuel_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['electric_option_label.electric_option_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['electric_option_label.electric_option_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['hybrid_option_label.hybrid_option_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['hybrid_option_label.hybrid_option_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['gas_option_label.gas_option_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['gas_option_label.gas_option_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['driver_license_label.driver_license_label_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['driver_license_label.driver_license_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_license_error.driver_license_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_license_error.driver_license_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                // $validationRule = array_merge($validationRule, ['driver_license_sub_label.driver_license_sub_label_' . $language->id => ['required', 'string']]);
                // $errorMessages = array_merge($errorMessages, ['driver_license_sub_label.driver_license_sub_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_driver_choose_file_label.mobile_driver_choose_file_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_driver_choose_file_label.mobile_driver_choose_file_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['photo_label.photo_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['photo_label.photo_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['photo_error.photo_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['photo_error.photo_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['photo_detail_label.photo_detail_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['photo_detail_label.photo_detail_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_photo_choose_file_label.mobile_photo_choose_file_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_photo_choose_file_label.mobile_photo_choose_file_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['skip_button_label.skip_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['skip_button_label.skip_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['next_button_label.next_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['next_button_label.next_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['logout_button_label.logout_button_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['logout_button_label.logout_button_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);

                $validationRule = array_merge($validationRule, ['sub_heading.sub_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['sub_heading.sub_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['sub_main_label.sub_main_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['sub_main_label.sub_main_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['liecense_section_heading.liecense_section_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['liecense_section_heading.liecense_section_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['vehicle_section_heading.vehicle_section_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['vehicle_section_heading.vehicle_section_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['skip_license.skip_license_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['skip_license.skip_license_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                
                $validationRule = array_merge($validationRule, ['skip_vehicle_info.skip_vehicle_info_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['skip_vehicle_info.skip_vehicle_info_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($step3PageSetting, $language, $request)
    {
        return [
            'step3_page_setting_id' => $step3PageSetting->id,
            'language_id' => $language->id,
            'name' => $this->data($request, $language, 'name'),
            'meta_keywords' => $this->data($request, $language, 'meta_keywords'),
            'meta_description' => $this->data($request, $language, 'meta_description'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'main_label' => $this->data($request, $language, 'main_label'),
            'required_label' => $this->data($request, $language, 'required_label'),
            'make_label' => $this->data($request, $language, 'make_label'),
            'make_error' => $this->data($request, $language, 'make_error'),
            'make_placeholder' => $this->data($request, $language, 'make_placeholder'),
            'model_label' => $this->data($request, $language, 'model_label'),
            'model_error' => $this->data($request, $language, 'model_error'),
            'model_placeholder' => $this->data($request, $language, 'model_placeholder'),
            'vehicle_type_label' => $this->data($request, $language, 'vehicle_type_label'),
            'vehicle_type_error' => $this->data($request, $language, 'vehicle_type_error'),
            'color_label' => $this->data($request, $language, 'color_label'),
            'color_error' => $this->data($request, $language, 'color_error'),
            'license_label' => $this->data($request, $language, 'license_label'),
            'license_error' => $this->data($request, $language, 'license_error'),
            'year_label' => $this->data($request, $language, 'year_label'),
            'year_error' => $this->data($request, $language, 'year_error'),
            'fuel_label' => $this->data($request, $language, 'fuel_label'),
            'fuel_error' => $this->data($request, $language, 'fuel_error'),
            'electric_option_label' => $this->data($request, $language, 'electric_option_label'),
            'hybrid_option_label' => $this->data($request, $language, 'hybrid_option_label'),
            'gas_option_label' => $this->data($request, $language, 'gas_option_label'),
            // 'driver_license_label' => $this->data($request, $language, 'driver_license_label'),
            'driver_license_error' => $this->data($request, $language, 'driver_license_error'),
            'mobile_driver_choose_file_label' => $this->data($request, $language, 'mobile_driver_choose_file_label'),
            'photo_label' => $this->data($request, $language, 'photo_label'),
            'photo_error' => $this->data($request, $language, 'photo_error'),
            'photo_detail_label' => $this->data($request, $language, 'photo_detail_label'),
            'mobile_photo_choose_file_label' => $this->data($request, $language, 'mobile_photo_choose_file_label'),
            'skip_button_label' => $this->data($request, $language, 'skip_button_label'),
            'next_button_label' => $this->data($request, $language, 'next_button_label'),
            'logout_button_label' => $this->data($request, $language, 'logout_button_label'),
            'vehicle_type_placeholder' => $this->data($request, $language, 'vehicle_type_placeholder'),
            'sub_heading' => $this->data($request, $language, 'sub_heading'),
            'sub_main_label' => $this->data($request, $language, 'sub_main_label'),
            'liecense_section_heading' => $this->data($request, $language, 'liecense_section_heading'),
            'vehicle_section_heading' => $this->data($request, $language, 'vehicle_section_heading'),
            'skip_vehicle_info' => $this->data($request, $language, 'skip_vehicle_info'),
            'skip_license' => $this->data($request, $language, 'skip_license'),
        ];
    }

    public function update($step3PageSetting, $language, $request)
    {
        $fields = $this->fields($step3PageSetting, $language, $request);
        $step3PageSettingDetail = Step3PageSettingDetail::whereStep3PageSettingId($step3PageSetting->id)->whereLanguageId($language->id)->exists();
        if(!$step3PageSettingDetail){
            $fields = $this->fields($step3PageSetting, $language, $request);
        Step3PageSettingDetail::create($fields);
        }
        else{
            Step3PageSettingDetail::whereStep3PageSettingId($step3PageSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
