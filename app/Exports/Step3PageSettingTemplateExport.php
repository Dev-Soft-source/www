<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class Step3PageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        // Include UI-requested fields; some may be non-persisted but are shown in template for completeness
        return [
            'name','meta_keywords','meta_description','main_heading','main_label','required_label',
            'make_label','make_error','make_placeholder',
            'model_label','model_error','model_placeholder',
            'vehicle_type_label','vehicle_type_error','vehicle_type_placeholder',
            'color_label','color_error',
            'license_label','license_error',
            'year_label','year_error',
            'fuel_label','fuel_error','electric_option_label','hybrid_option_label','gas_option_label',
            'driver_license_label','driver_license_error','mobile_driver_choose_file_label',
            'photo_label','photo_error','photo_detail_label','mobile_photo_choose_file_label',
            'skip_button_label','skip_vehicle_info','skip_license','next_button_label',
            'vehicle_type_placeholder','logout_button_label','sub_heading','sub_main_label','vehicle_section_heading','liecense_section_heading'
        ];
    }
}


