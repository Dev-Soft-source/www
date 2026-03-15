<?php

namespace App\Imports;

use App\Models\PayoutOptionSetting;
use App\Models\PayoutOptionSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PayoutOptionSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When null, import expects all_languages format (Field Name + one column per language). */
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    protected function fields(): array
    {
        return [
            'bank_detail_heading','mobile_indicate_required_field_label','main_heading','paypal_detail_heading','web_bank_transfer_description','web_paypal_transfer_description','web_interac_transfer_description','wallet_intro_line1','wallet_intro_line2','interac_detail_heading','interac_autodeposit_info_paragraph','processing_fee_text','save_payout_method_btn','bank_detail_info_paragraph','bank_funds_note','paypal_detail_info_paragraph','paypal_fee_heading','paypal_fee_proximaride_text','paypal_fee_receiving_text','paypal_fee_example_text','refund_footer_paragraph','interac_autodeposit_label','interac_autodeposit_tooltip','interac_autodeposit_text_before','interac_autodeposit_highlight','interac_autodeposit_text_after','interac_email_label','interac_email_confirm_label','interac_email_placeholder','interac_email_confirm_placeholder','paypal_email_confirm_label','paypal_email_confirm_placeholder','web_payout_method_label','web_payout_method_placeholder','bank_name_label','bank_name_placeholder','bank_title_label','bank_title_placeholder','account_number_label','account_number_placeholder','branch_label','branch_placeholder','address_label','address_placeholder','admin_sent_amount_placeholder','set_default_checkbox_label','verify_button_text','paypal_account_heading','mobile_paypal_indicate_required_label','paypal_email_label','paypal_email_placeholder','paypal_set_default_checkbox_label','institution_number_label','institution_number_placeholder','branch_address_label','branch_number_label','branch_number_placeholder','branch_address_placeholder','account_address_placeholder','bank_account_heading','update_btn_label','save_btn_label','bank_error','institute_no_error','branch_error','branch_address_error','branch_no_error','bank_title_error','acc_no_error','address_error'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = PayoutOptionSetting::first() ?? PayoutOptionSetting::create([]);
        if ($rows->isEmpty()) {
            Log::warning('No rows found in Payout Option Excel file');
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        $isAllLanguages = $this->languageId === null
            && (in_array('field_name', $keys) || in_array('field name', $keys))
            && count($keys) > 1;

        if ($isAllLanguages) {
            $this->processAllLanguagesFormat($setting, $rows);
            Log::info('Payout Option Settings Excel import (all languages) completed successfully');
            return;
        }

        $isSingle = in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        $data = [];
        if ($isSingle && $this->languageId !== null) {
            foreach ($rows as $row) {
                $k = strtolower(trim($row['field_name'] ?? ''));
                if (!$k || !in_array($k, $this->fields())) continue;
                $data[$k] = $row['translation_value'] ?? $row['value'] ?? null;
            }
            $this->applyData($setting, $data);
        } elseif ($this->languageId !== null) {
            $data = $firstRow->toArray();
            $this->applyData($setting, $data);
        }
    }

    protected function processAllLanguagesFormat(PayoutOptionSetting $setting, Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());
        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : (in_array('field name', $headers) ? 'field name' : 'Field Name');
        $languageColumns = array_diff($headers, [$fieldNameKey]);
        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(fn ($lang) => [Str::lower($lang->name) => $lang->id])->toArray();
        $validFields = $this->fields();

        foreach ($rows as $row) {
            $row = $row->toArray();
            $fieldName = isset($row[$fieldNameKey]) ? strtolower(trim((string) $row[$fieldNameKey])) : null;
            if (empty($fieldName) || !in_array($fieldName, $validFields, true)) {
                continue;
            }
            foreach ($languageColumns as $col) {
                $langKey = Str::lower(trim($col));
                if (!isset($nameToId[$langKey])) {
                    continue;
                }
                $languageId = $nameToId[$langKey];
                $value = $row[$col] ?? null;
                $detail = PayoutOptionSettingDetail::firstOrCreate(
                    [
                        'payout_opt_setting_id' => $setting->id,
                        'language_id' => $languageId,
                    ],
                    []
                );
                $detail->$fieldName = $value;
                $detail->save();
            }
        }
    }

    protected function applyData(PayoutOptionSetting $setting, array $data): void
    {
        $payload = [
            'payout_opt_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fields() as $f) {
            $payload[$f] = $data[$f] ?? null;
        }
        PayoutOptionSettingDetail::updateOrCreate(
            ['payout_opt_setting_id' => $setting->id, 'language_id' => $this->languageId],
            $payload
        );
    }

    public function rules(): array
    {
        if ($this->languageId === null) {
            return [];
        }
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') {
            return [];
        }
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


