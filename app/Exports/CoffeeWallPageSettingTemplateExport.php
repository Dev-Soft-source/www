<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CoffeeWallPageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $format;

    public function __construct($format = 'single_column')
    {
        $this->format = $format;
    }

    public function collection(): Collection
    {
        if ($this->format === 'single_column') {
            $data = [];
            foreach ($this->getFields() as $field) {
                $data[] = ['field_name' => $field, 'translation_value' => ''];
            }
            return new Collection($data);
        }

        $row = array_fill_keys($this->getFields(), '');
        return new Collection([$row]);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') {
            return ['Field Name', 'Translation Value'];
        }
        return array_map(function ($f) { return ucwords(str_replace('_', ' ', $f)); }, $this->getFields());
    }

    protected function getFields(): array
    {
        return [
            'name',
            'meta_keywords',
            'meta_description',
            'main_heading',
            'required_field_label',
            'main_text',
            'agree_terms_label',
            'custom_amount_label',
            'pay_button_label',
            'frequency_label',
            'email_label',
            'name_label',
            'phone_label',
            'designation_label',
            'designation_option1',
            'designation_option2',
            'designation_option3',
            'designation_option4',
            'monthly_label',
            'quarterly_label',
            'semi_annually_label',
            'annually_label',
        ];
    }
}


