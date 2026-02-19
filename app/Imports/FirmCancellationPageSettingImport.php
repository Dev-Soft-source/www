<?php

namespace App\Imports;

use App\Models\FirmCancellationPageSetting;
use App\Models\FirmCancellationPageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class FirmCancellationPageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When null, import expects all_languages format (Field Name + one column per language). */
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    public function collection(Collection $rows)
    {
        $firmCancellationPageSetting = FirmCancellationPageSetting::first();
        if (!$firmCancellationPageSetting) {
            $firmCancellationPageSetting = FirmCancellationPageSetting::create([]);
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
            $this->processAllLanguagesFormat($firmCancellationPageSetting, $rows);
            Log::info('Firm Cancellation Page Settings Excel import (all languages) completed successfully');
            return;
        }

        $isSingleColumn = in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        if ($isSingleColumn && $this->languageId !== null) {
            foreach ($rows as $row) {
                $this->processSingleColumnFormat($firmCancellationPageSetting, $row);
            }
        } else {
            if ($this->languageId !== null) {
                $this->processMultiColumnFormat($firmCancellationPageSetting, $firstRow);
            }
        }
    }

    protected function processAllLanguagesFormat(FirmCancellationPageSetting $firmCancellationPageSetting, Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());
        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : 'field name';
        $languageColumns = array_diff($headers, [$fieldNameKey]);
        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(fn ($lang) => [Str::lower($lang->name) => $lang->id])->toArray();
        $validFields = array_keys(\App\Exports\FirmCancellationPageSettingTemplateExport::getTranslatableFieldsWithDefaults());

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
                $detail = FirmCancellationPageSettingDetail::firstOrCreate(
                    [
                        'firm_cancellation_id' => $firmCancellationPageSetting->id,
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

    protected function processSingleColumnFormat($firmCancellationPageSetting, $row)
    {
        $fieldName = $row['field_name'] ?? null;
        $value = $row['translation_value'] ?? $row['value'] ?? null;

        if (empty($fieldName) || empty($value)) {
            return;
        }

        $detail = FirmCancellationPageSettingDetail::where('firm_cancellation_id', $firmCancellationPageSetting->id)
            ->where('language_id', $this->languageId)
            ->first();

        if ($detail) {
            $detail->$fieldName = $value;
            $detail->save();
        } else {
            FirmCancellationPageSettingDetail::create([
                'firm_cancellation_id' => $firmCancellationPageSetting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]);
        }
    }

    protected function processMultiColumnFormat($firmCancellationPageSetting, $row)
    {
        $fields = [
            'firm_cancellation_id' => $firmCancellationPageSetting->id,
            'language_id' => $this->languageId,
            'name' => $row['name'] ?? null,
            'meta_keywords' => $row['meta_keywords'] ?? null,
            'meta_description' => $row['meta_description'] ?? null,
            'main_heading' => $row['main_heading'] ?? null,
            'main_text' => $row['main_text'] ?? null,
        ];

        FirmCancellationPageSettingDetail::updateOrCreate(
            [
                'firm_cancellation_id' => $firmCancellationPageSetting->id,
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
            'name' => 'required|string',
            'meta_keywords' => 'required|string',
            'meta_description' => 'required|string',
            'main_heading' => 'required|string',
            'main_text' => 'required|string',
        ];
    }
}

