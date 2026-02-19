<?php

namespace App\Imports;

use App\Models\PaymentSetting;
use App\Models\PaymentSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PaymentOptionSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When null, import expects all_languages format (Field Name + one column per language). */
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    protected function sheetFields(): array
    {
        // Accept friendly fields from sheet (includes non-persisted 'name')
        return [
            'name','mobile_default_card_tab','mobile_card_name_label','main_heading','mobile_card_number_label','mobile_expiry_date_label','delete_card_button_text','add_new_card_button_text','no_payment_message','set_primary_card_label','select_card_type_text'
        ];
    }

    protected function persistableFields(): array
    {
        // Actual DB columns in payment_option_setting_detail (exclude 'name')
        return [
            'mobile_default_card_tab','mobile_card_name_label','main_heading','mobile_card_number_label','mobile_expiry_date_label','delete_card_button_text','add_new_card_button_text','no_payment_message','set_primary_card_label','select_card_type_text'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = PaymentSetting::first() ?? PaymentSetting::create([]);
        if ($rows->isEmpty()) {
            Log::warning('No rows found in Payment Option Excel file');
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        $isAllLanguages = $this->languageId === null
            && (in_array('field_name', $keys) || in_array('field name', $keys))
            && count($keys) > 1;

        if ($isAllLanguages) {
            $this->processAllLanguagesFormat($setting, $rows);
            Log::info('Payment Option Settings Excel import (all languages) completed successfully');
            return;
        }

        $isSingleColumn = in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        if ($isSingleColumn && $this->languageId !== null) {
            $data = [];
            foreach ($rows as $row) {
                $name = strtolower(trim($row['field_name'] ?? ''));
                if (!$name || !in_array($name, $this->sheetFields())) continue;
                $data[$name] = $row['translation_value'] ?? $row['value'] ?? null;
            }
            $this->applyData($setting, $data);
        } elseif ($this->languageId !== null) {
            $this->applyData($setting, $firstRow->toArray());
        }
    }

    protected function processAllLanguagesFormat(PaymentSetting $setting, Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());
        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : (in_array('field name', $headers) ? 'field name' : 'Field Name');
        $languageColumns = array_diff($headers, [$fieldNameKey]);
        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(fn ($lang) => [Str::lower($lang->name) => $lang->id])->toArray();
        $validFields = $this->sheetFields();

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
                $detail = PaymentSettingDetail::firstOrCreate(
                    [
                        'payment_opt_setting_id' => $setting->id,
                        'language_id' => $languageId,
                    ],
                    []
                );
                if (in_array($fieldName, $this->persistableFields(), true)) {
                    $detail->$fieldName = $value;
                    $detail->save();
                }
            }
        }
    }

    protected function applyData($setting, array $data): void
    {
        $payload = [
            'payment_opt_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->persistableFields() as $f) { $payload[$f] = $data[$f] ?? null; }

        PaymentSettingDetail::updateOrCreate(
            [
                'payment_opt_setting_id' => $setting->id,
                'language_id' => $this->languageId,
            ],
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
            'mobile_default_card_tab' => 'required|string',
            'mobile_card_name_label' => 'required|string',
            'main_heading' => 'required|string',
            'mobile_card_number_label' => 'required|string',
            'mobile_expiry_date_label' => 'required|string',
            'delete_card_button_text' => 'required|string',
            'add_new_card_button_text' => 'required|string',
            'no_payment_message' => 'required|string',
            'set_primary_card_label' => 'required|string',
        ];
    }
}


