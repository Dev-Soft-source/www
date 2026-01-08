<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SelectLocationPageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
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
            'select_origin_label','search_origin_label','no_origin_label',
            'select_destination_label','search_destination_label','no_destination_label',
            'select_country_label','search_country_label','no_country_label',
            'select_state_label','select_state_first_label','search_state_label','no_state_label',
            'select_city_label','search_city_label','no_city_label'
        ];
    }
}


