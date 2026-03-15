<?php

namespace App\Imports;

use App\Models\ErrorPageSetting;
use App\Models\ErrorPageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ErrorPageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    protected function fieldsList(): array
    {
        return [
            'error_404_heading', 'error_404_paragraph_1', 'error_404_paragraph_2',
            'error_404_back_home_btn', 'error_404_contact_btn',
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = ErrorPageSetting::first() ?? ErrorPageSetting::create([]);
        if ($rows->isEmpty()) {
            Log::warning('No rows in Error Page Excel file');
            return;
        }
        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());
        $isAllLanguages = $this->languageId === null
            && (in_array('field_name', $keys) || in_array('field name', $keys))
            && count($keys) > 1;
        if ($isAllLanguages) {
            $this->processAllLanguagesFormat($setting, $rows);
            return;
        }
        $isSingleColumn = in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));
        if ($isSingleColumn && $this->languageId !== null) {
            foreach ($rows as $row) {
                $this->processSingleColumnFormat($setting, $row);
            }
        } elseif ($this->languageId !== null) {
            $this->processMultiColumnFormat($setting, $firstRow);
        }
    }

    protected function processAllLanguagesFormat(ErrorPageSetting $setting, Collection $rows): void
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
                $detail = ErrorPageSettingDetail::firstOrCreate(
                    ['error_page_setting_id' => $setting->id, 'language_id' => $languageId],
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
        if (empty($fieldName)) {
            return;
        }
        $fieldName = strtolower(trim($fieldName));
        if (!in_array($fieldName, $this->fieldsList())) {
            return;
        }
        ErrorPageSettingDetail::updateOrCreate(
            ['error_page_setting_id' => $setting->id, 'language_id' => $this->languageId],
            ['error_page_setting_id' => $setting->id, 'language_id' => $this->languageId, $fieldName => $value]
        );
    }

    protected function processMultiColumnFormat($setting, $row)
    {
        $fields = [
            'error_page_setting_id' => $setting->id,
            'language_id' => $this->languageId,
            'error_404_heading' => $row['error_404_heading'] ?? null,
            'error_404_paragraph_1' => $row['error_404_paragraph_1'] ?? null,
            'error_404_paragraph_2' => $row['error_404_paragraph_2'] ?? null,
            'error_404_back_home_btn' => $row['error_404_back_home_btn'] ?? null,
            'error_404_contact_btn' => $row['error_404_contact_btn'] ?? null,
        ];
        ErrorPageSettingDetail::updateOrCreate(
            ['error_page_setting_id' => $setting->id, 'language_id' => $this->languageId],
            $fields
        );
    }

    public function rules(): array
    {
        return [];
    }
}
