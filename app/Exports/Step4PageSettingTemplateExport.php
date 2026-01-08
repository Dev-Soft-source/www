<?php

namespace App\Exports;

use App\Models\Step5PageSetting;
use App\Models\Step5PageSettingDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class Step4PageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $format;

    public function __construct($format = 'single_column')
    {
        $this->format = $format;
    }

    protected function fields(): array
    {
        return [
            'name','meta_keywords','meta_description','main_heading',
            'main_label','sub_main_label','required_label','driver_license_label',
            'driver_license_error','driver_license_sub_label','photo_detail_label',
            'mobile_photo_choose_file_label','skip_license','next_button_label','liecense_section_heading'
        ];
    }

    public function collection(): Collection
    {
        $fields = $this->fields();

        // pull existing values (default language if available)
        $values = [];
        if ($setting = Step5PageSetting::with('step5PageSettingDetail')->first()) {
            $detail = optional($setting->step5PageSettingDetail)->first();
            if ($detail) {
                foreach ($fields as $f) { $values[$f] = $detail->{$f} ?? ''; }
            }
        }

        if ($this->format === 'single_column') {
            $rows = [];
            foreach ($fields as $field) {
                $rows[] = ['field_name' => $field, 'translation_value' => $values[$field] ?? ''];
            }
            return new Collection($rows);
        }

        $row = [];
        foreach ($fields as $f) { $row[$f] = $values[$f] ?? ''; }
        return new Collection([$row]);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') return ['Field Name', 'Translation Value'];
        return array_map(fn($f) => ucwords(str_replace('_', ' ', $f)), $this->fields());
    }
}
