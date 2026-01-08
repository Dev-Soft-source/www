<?php

namespace App\Imports;

use App\Models\CancellationPageSetting;
use App\Models\CancellationPageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CancellationPageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    public function collection(Collection $rows)
    {
        Log::info('Starting Cancellation Page Settings Excel import for language ID: ' . $this->languageId);
        Log::info('Total rows to process: ' . $rows->count());
        
        // Get or create cancellation page setting
        $cancellationPageSetting = CancellationPageSetting::first();
        if (!$cancellationPageSetting) {
            $cancellationPageSetting = CancellationPageSetting::create([]);
        }

        Log::info('Cancellation Page Setting ID: ' . $cancellationPageSetting->id);

        // Check format by looking at first row keys
        if ($rows->isEmpty()) {
            Log::warning('No rows found in Excel file');
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());
        Log::info('Excel columns detected: ' . json_encode($keys));

        // Single column format check: has 'field_name' and ('value' OR 'translation_value')
        $isSingleColumn = isset($keys[0]) && 
                          (in_array('field_name', $keys) && 
                          (in_array('value', $keys) || in_array('translation_value', $keys)));

        if ($isSingleColumn) {
            Log::info('Detected SINGLE COLUMN format');
            foreach ($rows as $index => $row) {
                Log::info("Processing row {$index}: " . json_encode($row->toArray()));
                $this->processSingleColumnFormat($cancellationPageSetting, $row);
            }
        } else {
            Log::info('Detected MULTI COLUMN format');
            $this->processMultiColumnFormat($cancellationPageSetting, $firstRow);
        }
        
        Log::info('Cancellation Page Settings Excel import completed successfully');
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

