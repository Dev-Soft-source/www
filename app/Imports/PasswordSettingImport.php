<?php

namespace App\Imports;

use App\Models\PasswordSetting;
use App\Models\PasswordSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PasswordSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When null, import expects all_languages format (Field Name + one column per language). */
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    protected function sheetFields(): array
    {
        // Exact fields the sheet should contain (no 'name') and in required order
        return [
            'main_heading',
            'mobile_indicate_required_field_label',
            'password_description_text',
            'current_password_label',
            'current_password_placeholder',
            'current_password_error',
            'new_password_label',
            'new_password_placeholder',
            'new_password_error',
            'confirm_new_password_label',
            'confirm_new_password_placeholder',
            'confirm_new_password_error',
            'mobile_password_description_text',
            'update_button_text',
        ];
    }

    protected function persistableFields(): array
    {
        // Actual DB columns in password_setting_detail, matching the sheet fields
        return [
            'main_heading',
            'mobile_indicate_required_field_label',
            'password_description_text',
            'current_password_label',
            'current_password_placeholder',
            'current_password_error',
            'new_password_label',
            'new_password_placeholder',
            'new_password_error',
            'confirm_new_password_label',
            'confirm_new_password_placeholder',
            'confirm_new_password_error',
            'mobile_password_description_text',
            'update_button_text',
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = PasswordSetting::first() ?? PasswordSetting::create([]);
        if ($rows->isEmpty()) {
            Log::warning('No rows found in Password Excel file');
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        $isAllLanguages = $this->languageId === null
            && (in_array('field_name', $keys) || in_array('field name', $keys))
            && count($keys) > 1;

        if ($isAllLanguages) {
            $this->processAllLanguagesFormat($setting, $rows);
            Log::info('Password Settings Excel import (all languages) completed successfully');
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

    protected function processAllLanguagesFormat(PasswordSetting $setting, Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());
        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : (in_array('field name', $headers) ? 'field name' : 'Field Name');
        $languageColumns = array_diff($headers, [$fieldNameKey]);
        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(fn ($lang) => [Str::lower($lang->name) => $lang->id])->toArray();
        $validFields = $this->persistableFields();

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
                $detail = PasswordSettingDetail::firstOrCreate(
                    [
                        'password_setting_id' => $setting->id,
                        'language_id' => $languageId,
                    ],
                    []
                );
                $detail->$fieldName = $value;
                $detail->save();
            }
        }
    }

    protected function applyData($setting, array $data): void
    {
        $payload = [
            'password_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->persistableFields() as $f) { $payload[$f] = $data[$f] ?? null; }

        PasswordSettingDetail::updateOrCreate(
            [
                'password_setting_id' => $setting->id,
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
            'password_description_text' => 'required|string',
            'mobile_indicate_required_field_label' => 'required|string',
            'main_heading' => 'required|string',
            'current_password_label' => 'required|string',
            'new_password_label' => 'required|string',
            'confirm_new_password_label' => 'required|string',
            'update_button_text' => 'required|string',
        ];
    }
}


