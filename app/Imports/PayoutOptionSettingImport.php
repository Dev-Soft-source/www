<?php

namespace App\Imports;

use App\Models\PayoutOptionSetting;
use App\Models\PayoutOptionSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PayoutOptionSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function fields(): array
    {
        return [
            'bank_detail_heading','mobile_indicate_required_field_label','main_heading','paypal_detail_heading','web_bank_transfer_description','web_paypal_transfer_description','web_payout_method_label','web_payout_method_placeholder','bank_name_label','bank_name_placeholder','bank_title_label','bank_title_placeholder','account_number_label','account_number_placeholder','branch_label','branch_placeholder','address_label','address_placeholder','admin_sent_amount_placeholder','set_default_checkbox_label','verify_button_text','paypal_account_heading','mobile_paypal_indicate_required_label','paypal_email_label','paypal_email_placeholder','paypal_set_default_checkbox_label','institution_number_label','institution_number_placeholder','branch_address_label','branch_number_label','branch_number_placeholder','branch_address_placeholder','account_address_placeholder','bank_account_heading','update_btn_label','save_btn_label','bank_error','institute_no_error','branch_error','branch_address_error','branch_no_error','bank_title_error','acc_no_error','address_error'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = PayoutOptionSetting::first() ?? PayoutOptionSetting::create([]);
        if ($rows->isEmpty()) return;
        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());
        $isSingle = isset($keys[0]) && in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        $data = [];
        if ($isSingle) {
            foreach ($rows as $row) {
                $k = strtolower(trim($row['field_name'] ?? ''));
                if (!$k || !in_array($k, $this->fields())) continue;
                $data[$k] = $row['translation_value'] ?? $row['value'] ?? null;
            }
        } else {
            $data = $firstRow->toArray();
        }

        $payload = [
            'payout_opt_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fields() as $f) { $payload[$f] = $data[$f] ?? null; }

        PayoutOptionSettingDetail::updateOrCreate(
            ['payout_opt_setting_id' => $setting->id, 'language_id' => $this->languageId],
            $payload
        );
    }

    public function rules(): array
    {
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') return [];
        return [
            'bank_detail_heading' => 'required|string',
            'mobile_indicate_required_field_label' => 'required|string',
            'main_heading' => 'required|string',
            'paypal_detail_heading' => 'required|string',
            'web_bank_transfer_description' => 'required|string',
            'web_payout_method_label' => 'required|string',
            'web_payout_method_placeholder' => 'required|string',
            'bank_name_label' => 'required|string',
            'bank_name_placeholder' => 'required|string',
            'bank_title_label' => 'required|string',
            'bank_title_placeholder' => 'required|string',
            'account_number_label' => 'required|string',
            'account_number_placeholder' => 'required|string',
            'branch_label' => 'required|string',
            'branch_placeholder' => 'required|string',
            'address_label' => 'required|string',
            'address_placeholder' => 'required|string',
        ];
    }
}


