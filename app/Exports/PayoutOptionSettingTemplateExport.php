<?php

namespace App\Exports;

use App\Models\Language;
use App\Models\PayoutOptionSetting;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PayoutOptionSettingTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /** @var \App\Models\PayoutOptionSetting|null */
    protected $existingData;

    public function __construct($format = 'single_column', $languages = null, $existingData = null)
    {
        $this->format = $format;
        $this->languages = $languages ? collect($languages) : null;
        $this->existingData = $existingData;
    }

    public static function getTranslatableFieldsWithDefaults(): array
    {
        $fields = [
            'bank_detail_heading','mobile_indicate_required_field_label','main_heading','paypal_detail_heading','web_bank_transfer_description','web_paypal_transfer_description','web_interac_transfer_description','wallet_intro_line1','wallet_intro_line2','interac_detail_heading','interac_autodeposit_info_paragraph','processing_fee_text','save_payout_method_btn','bank_detail_info_paragraph','bank_funds_note','paypal_detail_info_paragraph','paypal_fee_heading','paypal_fee_proximaride_text','paypal_fee_receiving_text','paypal_fee_example_text','refund_footer_paragraph','interac_autodeposit_label','interac_autodeposit_tooltip','interac_autodeposit_text_before','interac_autodeposit_highlight','interac_autodeposit_text_after','interac_email_label','interac_email_confirm_label','interac_email_placeholder','interac_email_confirm_placeholder','paypal_email_confirm_label','paypal_email_confirm_placeholder','web_payout_method_label','web_payout_method_placeholder','bank_name_label','bank_name_placeholder','bank_title_label','bank_title_placeholder','account_number_label','account_number_placeholder','branch_label','branch_placeholder','address_label','address_placeholder','admin_sent_amount_placeholder','set_default_checkbox_label','verify_button_text','paypal_account_heading','mobile_paypal_indicate_required_label','paypal_email_label','paypal_email_placeholder','paypal_set_default_checkbox_label','institution_number_label','institution_number_placeholder','branch_address_label','branch_number_label','branch_number_placeholder','branch_address_placeholder','account_address_placeholder','bank_account_heading','update_btn_label','save_btn_label','bank_error','institute_no_error','branch_error','branch_address_error','branch_no_error','bank_title_error','acc_no_error','address_error'
        ];
        return array_fill_keys($fields, '');
    }

    public function collection(): Collection
    {
        if ($this->format === 'single_column') return $this->singleColumnFormat();
        if ($this->format === 'all_languages') return $this->allLanguagesFormat();
        return $this->multiColumnFormat();
    }

    protected function singleColumnFormat(): Collection
    {
        $fields = static::getTranslatableFieldsWithDefaults();
        $data = [];
        foreach ($fields as $field => $default) {
            $data[] = ['field_name' => $field, 'translation_value' => $default];
        }
        return new Collection($data);
    }

    protected function allLanguagesFormat(): Collection
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $fields = static::getTranslatableFieldsWithDefaults();
        $detailsByLang = [];
        if ($this->existingData && $this->existingData->relationLoaded('payoutOptionSettingDetail')) {
            foreach ($this->existingData->payoutOptionSettingDetail as $d) {
                $detailsByLang[$d->language_id] = $d;
            }
        }
        $rows = [];
        foreach ($fields as $fieldKey => $defaultValue) {
            $row = [$fieldKey];
            foreach ($languages as $lang) {
                $detail = $detailsByLang[$lang->id] ?? null;
                $value = $detail && isset($detail->$fieldKey) ? ($detail->$fieldKey ?? '') : $defaultValue;
                $row[] = $value;
            }
            $rows[] = $row;
        }
        return collect($rows);
    }

    protected function multiColumnFormat(): Collection
    {
        $fields = static::getTranslatableFieldsWithDefaults();
        return new Collection([$fields]);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') return ['Field Name', 'Translation Value'];
        if ($this->format === 'all_languages') {
            $languages = $this->languages ?? Language::orderBy('id')->get();
            return array_merge(['Field Name'], $languages->pluck('name')->toArray());
        }
        return array_map(fn ($f) => ucwords(str_replace('_', ' ', $f)), array_keys(static::getTranslatableFieldsWithDefaults()));
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        if ($this->format === 'single_column') return ['A' => 40, 'B' => 80];
        if ($this->format === 'all_languages') {
            $totalCols = ($this->languages ?? Language::orderBy('id')->get())->count() + 1;
            $widths = [];
            for ($colIndex = 1; $colIndex <= $totalCols; $colIndex++) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $widths[$col] = $colIndex === 1 ? 40 : 25;
            }
            return $widths;
        }
        return ['A' => 40, 'B' => 25];
    }
}


