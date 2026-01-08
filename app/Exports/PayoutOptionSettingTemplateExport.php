<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PayoutOptionSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
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
            'bank_detail_heading','mobile_indicate_required_field_label','main_heading','paypal_detail_heading','web_bank_transfer_description','web_paypal_transfer_description','web_payout_method_label','web_payout_method_placeholder','bank_name_label','bank_name_placeholder','bank_title_label','bank_title_placeholder','account_number_label','account_number_placeholder','branch_label','branch_placeholder','address_label','address_placeholder','admin_sent_amount_placeholder','set_default_checkbox_label','verify_button_text','paypal_account_heading','mobile_paypal_indicate_required_label','paypal_email_label','paypal_email_placeholder','paypal_set_default_checkbox_label','institution_number_label','institution_number_placeholder','branch_address_label','branch_number_label','branch_number_placeholder','branch_address_placeholder','account_address_placeholder','bank_account_heading','update_btn_label','save_btn_label','bank_error','institute_no_error','branch_error','branch_address_error','branch_no_error','bank_title_error','acc_no_error','address_error'
        ];
    }
}


