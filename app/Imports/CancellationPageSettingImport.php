<?php

namespace App\Imports;

use App\Models\CancellationPageSetting;
use App\Models\CancellationPageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CancellationPageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When null, import expects all_languages format (Field Name + one column per language). */
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    public function collection(Collection $rows)
    {
        $cancellationPageSetting = CancellationPageSetting::first();
        if (!$cancellationPageSetting) {
            $cancellationPageSetting = CancellationPageSetting::create([]);
        }

        if ($rows->isEmpty()) {
            Log::warning('No rows found in Excel file');
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        $isAllLanguages = $this->languageId === null
            && (in_array('field_name', $keys) || in_array('field name', $keys))
            && count($keys) > 1;

        if ($isAllLanguages) {
            $this->processAllLanguagesFormat($cancellationPageSetting, $rows);
            Log::info('Cancellation Page Settings Excel import (all languages) completed successfully');
            return;
        }

        $isSingleColumn = in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        if ($isSingleColumn && $this->languageId !== null) {
            foreach ($rows as $row) {
                $this->processSingleColumnFormat($cancellationPageSetting, $row);
            }
        } else {
            if ($this->languageId !== null) {
                $this->processMultiColumnFormat($cancellationPageSetting, $firstRow);
            }
        }

        Log::info('Cancellation Page Settings Excel import completed successfully');
    }

    protected function processAllLanguagesFormat(CancellationPageSetting $cancellationPageSetting, Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());
        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : 'field name';
        $languageColumns = array_diff($headers, [$fieldNameKey]);
        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(fn ($lang) => [Str::lower($lang->name) => $lang->id])->toArray();
        $validFields = array_keys(\App\Exports\CancellationPageSettingTemplateExport::getTranslatableFieldsWithDefaults());

        foreach ($rows as $row) {
            $row = $row->toArray();
            $fieldName = $row[$fieldNameKey] ?? null;
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
                $detail = CancellationPageSettingDetail::firstOrCreate(
                    [
                        'cancellation_page_id' => $cancellationPageSetting->id,
                        'language_id' => $languageId,
                    ],
                    [$fieldName => $value]
                );
                if (!$detail->wasRecentlyCreated) {
                    $detail->$fieldName = $value;
                    $detail->save();
                }
            }
        }
    }

    protected function processSingleColumnFormat($cancellationPageSetting, $row)
    {
        $fieldName = $row['field_name'] ?? null;
        $value = $row['translation_value'] ?? $row['value'] ?? null;

        if (empty($fieldName) || empty($value)) {
            Log::warning("Skipping row - Field: {$fieldName}, Value: {$value}");
            return;
        }

        Log::info("Processing field: {$fieldName} = {$value}");

        $detail = CancellationPageSettingDetail::where('cancellation_page_id', $cancellationPageSetting->id)
            ->where('language_id', $this->languageId)
            ->first();

        if ($detail) {
            $detail->$fieldName = $value;
            $detail->save();
            Log::info("Updated existing record - Field: {$fieldName}");
        } else {
            CancellationPageSettingDetail::create([
                'cancellation_page_id' => $cancellationPageSetting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]);
            Log::info("Created new record with field: {$fieldName}");
        }
    }

    protected function processMultiColumnFormat($cancellationPageSetting, $row)
    {
        $fields = [
            'cancellation_page_id' => $cancellationPageSetting->id,
            'language_id' => $this->languageId,
            'name' => $row['name'] ?? null,
            'meta_keywords' => $row['meta_keywords'] ?? null,
            'meta_description' => $row['meta_description'] ?? null,
            'main_heading' => $row['main_heading'] ?? null,
            'main_text' => $row['main_text'] ?? null,
        ];

        CancellationPageSettingDetail::updateOrCreate(
            [
                'cancellation_page_id' => $cancellationPageSetting->id,
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
        if (!$language) {
            return [];
        }

        $rules = [];
        
        if ($language->is_default == '1') {
            $rules = [
                'name' => 'required|string',
                'meta_keywords' => 'required|string',
                'meta_description' => 'required|string',
                'main_heading' => 'required|string',
                'main_text' => 'required|string',
            ];
        }

        return $rules;
    }
}

