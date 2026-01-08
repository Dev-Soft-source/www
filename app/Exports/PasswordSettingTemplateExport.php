<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PasswordSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        // Exact order required on the page
        return [
            'main_heading',
            'mobile_indicate_required_field_label',
            'password_description_text',
            'current_password_label',
            'current_password_placeholder',
            'current_password_error',
            'new_password_label',
            'new_password_placeholder',
            'new_password_error',
            'confirm_new_password_label',
            'confirm_new_password_placeholder',
            'confirm_new_password_error',
            'mobile_password_description_text',
            'update_button_text',
        ];
    }
}


