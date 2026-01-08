<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MyVehicleSettingDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'my_vehicle_setting_id' => $this->my_vehicle_setting_id,
            'language_id' => $this->language_id,
            'main_heading' => $this->main_heading,
            'edit_vehicle_button_text' => $this->edit_vehicle_button_text,
            'remove_vehicle_button_text' => $this->remove_vehicle_button_text,
            'add_main_heading' => $this->add_main_heading,
            'edit_main_heading' => $this->edit_main_heading,
            'mobile_indicate_field_label' => $this->mobile_indicate_field_label,
            'make_label' => $this->make_label,
            'make_error' => $this->make_error,
            'color_error' => $this->color_error,
            'make_placeholder' => $this->make_placeholder,
            'model_label' => $this->model_label,
            'model_error' => $this->model_error,
            'model_placeholder' => $this->model_placeholder,
            'license_plate_number_label' => $this->license_plate_number_label,
            'license_error' => $this->license_error,
            'year_error' => $this->year_error,
            'license_plate_number_placeholder' => $this->license_plate_number_placeholder,
            'color_label' => $this->color_label,
            'color_placeholder' => $this->color_placeholder,
            'year_label' => $this->year_label,
            'year_placeholder' => $this->year_placeholder,
            'vehicle_type_label' => $this->vehicle_type_label,
            'vehicle_type_error' => $this->vehicle_type_error,
            'vehicle_type_placeholder' => $this->vehicle_type_placeholder,
            'fuel_label' => $this->fuel_label,
            'fuel_error' => $this->fuel_error,
            'electric_checkbox_label' => $this->electric_checkbox_label,
            'hybrid_checkbox_label' => $this->hybrid_checkbox_label,
            'gas_checkbox_label' => $this->gas_checkbox_label,
            'set_primary_vehicle_label' => $this->set_primary_vehicle_label,
            'set_primary_error' => $this->set_primary_error,
            'yes_checkbox_label' => $this->yes_checkbox_label,
            'no_checkbox_label' => $this->no_checkbox_label,
            'image_description_label' => $this->image_description_label,
            'upload_profile_photo_image_placeholder' => $this->upload_profile_photo_image_placeholder,
            'choose_file_image_placeholder' => $this->choose_file_image_placeholder,
            'images_option_placeholder' => $this->images_option_placeholder,
            'add_vehicle_button_text' => $this->add_vehicle_button_text,
            'car_photo_label' => $this->car_photo_label,
            'photo_error' => $this->photo_error,
            'remove_car_photo_label' => $this->remove_car_photo_label,
            'update_vehicle_button_text' => $this->update_vehicle_button_text,
            'no_vehicle_message' => $this->no_vehicle_message,
            'delete_photo_message' => $this->delete_photo_message,
            'edit_photo_label' => $this->edit_photo_label,
            'my_vehicle_setting' => new MyVehicleSettingResource($this->whenLoaded('myVehicleSetting')),
            'language' => new LanguageResource($this->whenLoaded('language')),
        ];
    }
}
