<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DisputePageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        if ($this->format === 'single_column') return ['Field Name', 'Translation Value'];
        return array_map(fn($f) => ucwords(str_replace('_', ' ', $f)), $this->getFields());
    }

    protected function getFields(): array
    {
        return [
            'name',
            'meta_keywords',
            'meta_description',
            'main_heading',
            'main_text',
        ];
    }
}


