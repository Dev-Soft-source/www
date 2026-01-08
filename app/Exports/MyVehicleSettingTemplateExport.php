<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MyVehicleSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $format;

    public function __construct($format = 'single_column')
    {
        $this->format = $format;
    }

    public function collection(): Collection
    {
        $fields = $this->getFields();
        if ($this->format === 'single_column') {
            $rows = [];
            foreach ($fields as $field) {
                $rows[] = ['field_name' => $field, 'translation_value' => ''];
            }
            return new Collection($rows);
        }
        $row = array_fill_keys($fields, '');
        return new Collection([$row]);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') return ['Field Name', 'Translation Value'];
        return array_map(fn($f) => ucwords(str_replace('_', ' ', $f)), $this->getFields());
    }

    protected function getFields(): array
    {
        return [
            'edit_vehicle_button_text','remove_vehicle_button_text','main_heading','add_main_heading','edit_main_heading','mobile_indicate_field_label','make_label','make_error','make_placeholder','model_label','model_error','model_placeholder','license_plate_number_label','license_error','license_plate_number_placeholder','color_label','color_error','color_placeholder','year_label','year_error','year_placeholder','vehicle_type_label','vehicle_type_error','vehicle_type_placeholder','fuel_label','fuel_error','electric_checkbox_label','hybrid_checkbox_label','gas_checkbox_label','set_primary_vehicle_label','set_primary_error','yes_checkbox_label','no_checkbox_label','image_description_label','upload_profile_photo_image_placeholder','choose_file_image_placeholder','images_option_placeholder','car_photo_label','photo_error','add_vehicle_button_text','remove_car_photo_label','update_vehicle_button_text','no_vehicle_message','delete_photo_message','edit_photo_label'
        ];
    }
}


