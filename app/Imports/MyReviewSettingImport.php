<?php

namespace App\Imports;

use App\Models\MyReviewSetting;
use App\Models\MyReviewSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MyReviewSettingImport implements ToCollection, WithHeadingRow, WithValidation
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
            'review_left_label','review_received_label','main_heading','replied_label','response_label','reply_label','no_more_data_label','no_left_message','no_received_message','reply_heading_label','reply_placeholder','see_all_review_label','reply_submit_button_label','review_label',
            'passenger_review_heading','passenger_review_criteria_heading','passenger_review_condition_label','passenger_review_conscious_label','passenger_review_comfort_label','passenger_review_communication_label','passenger_review_attitude_label','passenger_review_hygiene_label','passenger_review_respect_label','passenger_review_safety_label','passenger_review_timeliness_label',
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = MyReviewSetting::first() ?? MyReviewSetting::create([]);
        if ($rows->isEmpty()) {
            Log::warning('No rows found in My Review Excel file');
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        $isAllLanguages = $this->languageId === null
            && (in_array('field_name', $keys) || in_array('field name', $keys))
            && count($keys) > 1;

        if ($isAllLanguages) {
            $this->processAllLanguagesFormat($setting, $rows);
            Log::info('My Review Settings Excel import (all languages) completed successfully');
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

    protected function processAllLanguagesFormat(MyReviewSetting $setting, Collection $rows): void
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
                $detail = MyReviewSettingDetail::firstOrCreate(
                    [
                        'my_review_setting_id' => $setting->id,
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

        MyReviewSettingDetail::updateOrCreate(
            [
                'my_review_setting_id' => $setting->id,
                'language_id' => $this->languageId,
            ],
            [
                'my_review_setting_id' => $setting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]
        );
    }

    protected function processMultiColumnFormat($setting, $row)
    {
        $fields = [
            'my_review_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fieldsList() as $f) {
            $fields[$f] = $row[$f] ?? null;
        }

        MyReviewSettingDetail::updateOrCreate(
            [
                'my_review_setting_id' => $setting->id,
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
            'review_left_label' => 'required|string',
            'review_received_label' => 'required|string',
            'main_heading' => 'required|string',
            'replied_label' => 'required|string',
            'response_label' => 'required|string',
            'reply_label' => 'required|string',
            'no_left_message' => 'required|string',
            'no_received_message' => 'required|string',
            'see_all_review_label' => 'required|string',
        ];
    }
}


