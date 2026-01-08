<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MyDriverSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
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
            $data = [];
            foreach ($fields as $field) {
                $data[] = ['field_name' => $field, 'translation_value' => ''];
            }
            return new Collection($data);
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
            'mobile_indicate_required_field_label','driver_license_description_text','main_heading','driver_license_label','web_upload_image_placeholder','mobile_driver_license_image_placeholder','mobile_choose_file_image_placeholder','mobile_image_type_placeholder','upload_button_text','upload_new_image_btn_label','update_button_text'
        ];
    }
}

