<?php

namespace App\Services;

use App\Models\MyVehicleSettingDetail;

class MyVehicleSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];
        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['edit_vehicle_button_text.edit_vehicle_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['edit_vehicle_button_text.edit_vehicle_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['remove_vehicle_button_text.remove_vehicle_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['remove_vehicle_button_text.remove_vehicle_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['main_heading.main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['main_heading.main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['add_main_heading.add_main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['add_main_heading.add_main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['edit_main_heading.edit_main_heading_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['edit_main_heading.edit_main_heading_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['mobile_indicate_field_label.mobile_indicate_field_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['mobile_indicate_field_label.mobile_indicate_field_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['make_placeholder.make_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['make_placeholder.make_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['model_label.model_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['model_label.model_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['model_error.model_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['model_error.model_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['model_placeholder.model_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['model_placeholder.model_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['vehicle_type_label.vehicle_type_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['vehicle_type_label.vehicle_type_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['vehicle_type_error.vehicle_type_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['vehicle_type_error.vehicle_type_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['vehicle_type_placeholder.vehicle_type_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['vehicle_type_placeholder.vehicle_type_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['fuel_label.fuel_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['fuel_label.fuel_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['fuel_error.fuel_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['fuel_error.fuel_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['electric_checkbox_label.electric_checkbox_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['electric_checkbox_label.electric_checkbox_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['hybrid_checkbox_label.hybrid_checkbox_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['hybrid_checkbox_label.hybrid_checkbox_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['gas_checkbox_label.gas_checkbox_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['gas_checkbox_label.gas_checkbox_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['set_primary_vehicle_label.set_primary_vehicle_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['set_primary_vehicle_label.set_primary_vehicle_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['set_primary_error.set_primary_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['set_primary_error.set_primary_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['yes_checkbox_label.yes_checkbox_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['yes_checkbox_label.yes_checkbox_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['no_checkbox_label.no_checkbox_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['no_checkbox_label.no_checkbox_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['image_description_label.image_description_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['image_description_label.image_description_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['upload_profile_photo_image_placeholder.upload_profile_photo_image_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['upload_profile_photo_image_placeholder.upload_profile_photo_image_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['choose_file_image_placeholder.choose_file_image_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['choose_file_image_placeholder.choose_file_image_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['images_option_placeholder.images_option_placeholder_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['images_option_placeholder.images_option_placeholder_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['add_vehicle_button_text.add_vehicle_button_text_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['add_vehicle_button_text.add_vehicle_button_text_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['car_photo_label.car_photo_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['car_photo_label.car_photo_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['photo_error.photo_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['photo_error.photo_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['make_error.make_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['make_error.make_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['color_error.color_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['color_error.color_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['license_error.license_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['license_error.license_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['year_error.year_error_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['year_error.year_error_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['delete_photo_message.delete_photo_message_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['delete_photo_message.delete_photo_message_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['edit_photo_label.edit_photo_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['edit_photo_label.edit_photo_label_' . $language->id . '.required' => 'This field in ' . $language->name . ' is required.']);
            }
        }
        return ['validation_rules' => $validationRule, 'error_messages' => $errorMessages, 'nice_names' => $niceNames];
    }

    public function fields($myVehicleSetting, $language, $request)
    {
        return [
            'my_vehicle_setting_id' => $myVehicleSetting->id,
            'language_id' => $language->id,
            'edit_vehicle_button_text' => $this->data($request, $language, 'edit_vehicle_button_text'),
            'remove_vehicle_button_text' => $this->data($request, $language, 'remove_vehicle_button_text'),
            'main_heading' => $this->data($request, $language, 'main_heading'),
            'add_main_heading' => $this->data($request, $language, 'add_main_heading'),
            'edit_main_heading' => $this->data($request, $language, 'edit_main_heading'),
            'mobile_indicate_field_label' => $this->data($request, $language, 'mobile_indicate_field_label'),
            'make_label' => $this->data($request, $language, 'make_label'),
            'make_error' => $this->data($request, $language, 'make_error'),
            'make_placeholder' => $this->data($request, $language, 'make_placeholder'),
            'model_label' => $this->data($request, $language, 'model_label'),
            'model_error' => $this->data($request, $language, 'model_error'),
            'model_placeholder' => $this->data($request, $language, 'model_placeholder'),
            'license_plate_number_label' => $this->data($request, $language, 'license_plate_number_label'),
            'license_error' => $this->data($request, $language, 'license_error'),
            'license_plate_number_placeholder' => $this->data($request, $language, 'license_plate_number_placeholder'),
            'color_label' => $this->data($request, $language, 'color_label'),
            'color_error' => $this->data($request, $language, 'color_error'),
            'color_placeholder' => $this->data($request, $language, 'color_placeholder'),
            'year_label' => $this->data($request, $language, 'year_label'),
            'year_error' => $this->data($request, $language, 'year_error'),
            'year_placeholder' => $this->data($request, $language, 'year_placeholder'),
            'vehicle_type_label' => $this->data($request, $language, 'vehicle_type_label'),
            'vehicle_type_error' => $this->data($request, $language, 'vehicle_type_error'),
            'vehicle_type_placeholder' => $this->data($request, $language, 'vehicle_type_placeholder'),
            'fuel_label' => $this->data($request, $language, 'fuel_label'),
            'fuel_error' => $this->data($request, $language, 'fuel_error'),
            'electric_checkbox_label' => $this->data($request, $language, 'electric_checkbox_label'),
            'hybrid_checkbox_label' => $this->data($request, $language, 'hybrid_checkbox_label'),
            'gas_checkbox_label' => $this->data($request, $language, 'gas_checkbox_label'),
            'set_primary_vehicle_label' => $this->data($request, $language, 'set_primary_vehicle_label'),
            'set_primary_error' => $this->data($request, $language, 'set_primary_error'),
            'yes_checkbox_label' => $this->data($request, $language, 'yes_checkbox_label'),
            'no_checkbox_label' => $this->data($request, $language, 'no_checkbox_label'),
            'image_description_label' => $this->data($request, $language, 'image_description_label'),
            'upload_profile_photo_image_placeholder' => $this->data($request, $language, 'upload_profile_photo_image_placeholder'),
            'choose_file_image_placeholder' => $this->data($request, $language, 'choose_file_image_placeholder'),
            'images_option_placeholder' => $this->data($request, $language, 'images_option_placeholder'),
            'car_photo_label' => $this->data($request, $language, 'car_photo_label'),
            'photo_error' => $this->data($request, $language, 'photo_error'),
            'add_vehicle_button_text' => $this->data($request, $language, 'add_vehicle_button_text'),
            'remove_car_photo_label' => $this->data($request, $language, 'remove_car_photo_label'),
            'update_vehicle_button_text' => $this->data($request, $language, 'update_vehicle_button_text'),
            'no_vehicle_message' => $this->data($request, $language, 'no_vehicle_message'),
            'delete_photo_message' => $this->data($request, $language, 'delete_photo_message'),
            'edit_photo_label' => $this->data($request, $language, 'edit_photo_label'),
        ];
    }

    public function update($myVehicleSetting, $language, $request)
    {
        $fields = $this->fields($myVehicleSetting, $language, $request);
        $myVehicleSettingDetail = MyVehicleSettingDetail::whereMyVehicleSettingId($myVehicleSetting->id)->whereLanguageId($language->id)->exists();
        if(!$myVehicleSettingDetail){
            $fields = $this->fields($myVehicleSetting, $language, $request);
            MyVehicleSettingDetail::create($fields);
        }
        else{
            MyVehicleSettingDetail::whereMyVehicleSettingId($myVehicleSetting->id)->whereLanguageId($language->id)->update($fields);
        }
        return true;
    }

    function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id]) ? $request[$name][$name . '_' . $language->id] : null;
    }
}
