<?php

namespace App\Imports;

use App\Models\MyPhoneSetting;
use App\Models\MyPhoneSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MyPhoneSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When null, import expects all_languages format (Field Name + one column per language). */
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    protected function fieldsList(): array
    {
        return [
            'phone_no_description_text','unverified_number_label','main_heading','mobile_verify_button_text','web_send_verification_code_button_text','delete_button_text','mobile_country_code_label','country_code_placeholder','mobile_phone_number_label','phone_number_placeholder','save_phoneno_button_text','send_verification_code_button_text','verify_phone_number_heading','otp_code_description','enter_code_label','verify_phone_number_label','second_text','request_code_text','resend_code_btn_label','set_as_default_label','primary_number_label','remove_description','default_verified_number_label','verified_number_label','phone_no_description_text1','phone_number_label_web','country_code_label_web','country_id_label_web','add_another_phone_number_title'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = MyPhoneSetting::first() ?? MyPhoneSetting::create([]);
        if ($rows->isEmpty()) {
            Log::warning('No rows found in My Phone Excel file');
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        $isAllLanguages = $this->languageId === null
            && (in_array('field_name', $keys) || in_array('field name', $keys))
            && count($keys) > 1;

        if ($isAllLanguages) {
            $this->processAllLanguagesFormat($setting, $rows);
            Log::info('My Phone Settings Excel import (all languages) completed successfully');
            return;
        }

        $isSingleColumn = in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        if ($isSingleColumn && $this->languageId !== null) {
            foreach ($rows as $row) {
                $this->processSingleColumnFormat($setting, $row);
            }
        } else {
            if ($this->languageId !== null) {
                $this->processMultiColumnFormat($setting, $firstRow);
            }
        }
    }

    protected function processAllLanguagesFormat(MyPhoneSetting $setting, Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());
        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : 'field name';
        $languageColumns = array_diff($headers, [$fieldNameKey]);
        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(fn ($lang) => [Str::lower($lang->name) => $lang->id])->toArray();
        $validFields = $this->fieldsList();

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
                $detail = MyPhoneSettingDetail::firstOrCreate(
                    [
                        'phone_no_setting_id' => $setting->id,
                        'language_id' => $languageId,
                    ],
                    []
                );
                $detail->$fieldName = $value;
                $detail->save();
            }
        }
    }

    protected function processSingleColumnFormat($setting, $row)
    {
        $fieldName = $row['field_name'] ?? null;
        $value = $row['translation_value'] ?? $row['value'] ?? null;
        if (empty($fieldName) || $value === null || $value === '') return;
        $fieldName = strtolower(trim($fieldName));
        if (!in_array($fieldName, $this->fieldsList())) return;

        MyPhoneSettingDetail::updateOrCreate(
            [
                'phone_no_setting_id' => $setting->id,
                'language_id' => $this->languageId,
            ],
            [
                'phone_no_setting_id' => $setting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]
        );
    }

    protected function processMultiColumnFormat($setting, $row)
    {
        $fields = [
            'phone_no_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fieldsList() as $f) {
            $fields[$f] = $row[$f] ?? null;
        }

        MyPhoneSettingDetail::updateOrCreate(
            [
                'phone_no_setting_id' => $setting->id,
                'language_id' => $this->languageId,
            ],
            $fields
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
            'unverified_number_label' => 'required|string',
            'main_heading' => 'required|string',
            'mobile_verify_button_text' => 'required|string',
            'web_send_verification_code_button_text' => 'required|string',
            'delete_button_text' => 'required|string',
            'country_code_placeholder' => 'required|string',
            'mobile_phone_number_label' => 'required|string',
            'phone_number_placeholder' => 'required|string',
            'save_phoneno_button_text' => 'required|string',
            'send_verification_code_button_text' => 'required|string',
            'primary_number_label' => 'required|string',
            'remove_description' => 'required|string',
            'add_another_phone_number_title' => 'required|string',
        ];
    }
}


