<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class CloseAccountSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $format;

    public function __construct($format = 'single_column')
    {
        $this->format = $format;
    }

    public function collection(): Collection
    {
        if ($this->format === 'single_column') {
            return $this->singleColumnFormat();
        }
        
        return $this->multiColumnFormat();
    }

    protected function singleColumnFormat()
    {
        $fields = $this->getFields();
        $data = [];
        foreach ($fields as $field) {
            $data[] = ['field_name' => $field, 'translation_value' => ''];
        }
        return new Collection($data);
    }

    protected function multiColumnFormat()
    {
        $fields = $this->getFields();
        $rowData = array_fill_keys($fields, '');
        return new Collection([$rowData]);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') {
            return ['Field Name', 'Translation Value'];
        }
        
        return array_map(function($field) {
            return ucwords(str_replace('_', ' ', $field));
        }, $this->getFields());
    }

    protected function getFields(): array
    {
        return [
            'warning_text',
            'mobile_indicate_required_field_label',
            'main_heading',
            'closing_account_label',
            'apply_reason_label',
            'reason_label',
            'not_say_checkbox_label',
            'check_box_validation_message',
            'customer_service_checkbox_label',
            'technical_issue_checkbox_label',
            'dont_use_checkbox_label',
            'another_account_checkbox_label',
            'did_not_get_booking_checkbox_label',
            'did_not_find_ride_checkbox_label',
            'did_not_find_destination_checkbox_label',
            'other_checkbox_label',
            'recommend_heading',
            'yes_checkbox_label',
            'no_checkbox_label',
            'prefer_not_checkbox_label',
            'why_closing_account_label',
            'why_closing_account_placeholder',
            'improve_label',
            'improve_placeholder',
            'close_my_account_checkbox',
            'close_my_account_checkbox_error',
            'close_account_button_text',
            'difficulties_making_receiving_payments_label',
            'take_me_back_button_label',
            'close_it_button_label',
            'close_account_sure_message_text',
            'web_irreversible_label',
            'web_closing_account_reason_label',
        ];
    }
}

