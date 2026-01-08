<?php

namespace App\Imports;

use App\Models\FirmCancellationPageSetting;
use App\Models\FirmCancellationPageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class FirmCancellationPageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    public function collection(Collection $rows)
    {
        Log::info('Starting Firm Cancellation Page Settings Excel import for language ID: ' . $this->languageId);
        
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

        $isSingleColumn = isset($keys[0]) && 
                          (in_array('field_name', $keys) && 
                          (in_array('value', $keys) || in_array('translation_value', $keys)));

        if ($isSingleColumn) {
            foreach ($rows as $row) {
                $this->processSingleColumnFormat($firmCancellationPageSetting, $row);
            }
        } else {
            $this->processMultiColumnFormat($firmCancellationPageSetting, $firstRow);
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

