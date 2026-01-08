<?php

namespace App\Imports;

use App\Models\MyVehicleSetting;
use App\Models\MyVehicleSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MyVehicleSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function fieldsList(): array
    {
        return [
            'edit_vehicle_button_text','remove_vehicle_button_text','main_heading','add_main_heading','edit_main_heading','mobile_indicate_field_label','make_label','make_error','make_placeholder','model_label','model_error','model_placeholder','license_plate_number_label','license_error','license_plate_number_placeholder','color_label','color_error','color_placeholder','year_label','year_error','year_placeholder','vehicle_type_label','vehicle_type_error','vehicle_type_placeholder','fuel_label','fuel_error','electric_checkbox_label','hybrid_checkbox_label','gas_checkbox_label','set_primary_vehicle_label','set_primary_error','yes_checkbox_label','no_checkbox_label','image_description_label','upload_profile_photo_image_placeholder','choose_file_image_placeholder','images_option_placeholder','car_photo_label','photo_error','add_vehicle_button_text','remove_car_photo_label','update_vehicle_button_text','no_vehicle_message','delete_photo_message','edit_photo_label'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = MyVehicleSetting::first() ?? MyVehicleSetting::create([]);
        if ($rows->isEmpty()) return;

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());
        $isSingleColumn = isset($keys[0]) && (in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys)));

        if ($isSingleColumn) {
            $data = [];
            foreach ($rows as $row) {
                $name = strtolower(trim($row['field_name'] ?? ''));
                if (!$name || !in_array($name, $this->fieldsList())) continue;
                $data[$name] = $row['translation_value'] ?? $row['value'] ?? null;
            }
            $this->applyData($setting, $data);
        } else {
            $this->applyData($setting, $firstRow->toArray());
        }
    }

    protected function applyData($setting, array $data): void
    {
        $payload = [
            'my_vehicle_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fieldsList() as $f) { $payload[$f] = $data[$f] ?? null; }

        MyVehicleSettingDetail::updateOrCreate(
            [
                'my_vehicle_setting_id' => $setting->id,
                'language_id' => $this->languageId,
            ],
            $payload
        );
    }

    public function rules(): array
    {
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') return [];
        return [
            'edit_vehicle_button_text' => 'required|string',
            'remove_vehicle_button_text' => 'required|string',
            'main_heading' => 'required|string',
            'add_main_heading' => 'required|string',
            'edit_main_heading' => 'required|string',
            'mobile_indicate_field_label' => 'required|string',
            'make_placeholder' => 'required|string',
            'model_label' => 'required|string',
            'model_error' => 'required|string',
            'model_placeholder' => 'required|string',
            'vehicle_type_label' => 'required|string',
            'vehicle_type_error' => 'required|string',
            'vehicle_type_placeholder' => 'required|string',
            'fuel_label' => 'required|string',
            'fuel_error' => 'required|string',
            'image_description_label' => 'required|string',
            'upload_profile_photo_image_placeholder' => 'required|string',
            'choose_file_image_placeholder' => 'required|string',
            'images_option_placeholder' => 'required|string',
            'add_vehicle_button_text' => 'required|string',
            'car_photo_label' => 'required|string',
            'photo_error' => 'required|string',
            'make_error' => 'required|string',
            'color_error' => 'required|string',
            'license_error' => 'required|string',
            'year_error' => 'required|string',
            'delete_photo_message' => 'required|string',
            'edit_photo_label' => 'required|string',
        ];
    }
}


