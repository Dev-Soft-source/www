<?php

namespace App\Imports;

use App\Models\ReferralPageSetting;
use App\Models\ReferralPageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ReferralPageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    public function collection(Collection $rows)
    {
        Log::info('Starting Referral Page Settings Excel import for language ID: ' . $this->languageId);

        $setting = ReferralPageSetting::first();
        if (!$setting) {
            $setting = ReferralPageSetting::create([]);
        }

        if ($rows->isEmpty()) return;

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());
        $isSingleColumn = isset($keys[0]) && (in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys)));

        if ($isSingleColumn) {
            foreach ($rows as $row) $this->processSingleColumnFormat($setting, $row);
        } else {
            $this->processMultiColumnFormat($setting, $firstRow);
        }
    }

    protected function processSingleColumnFormat($setting, $row)
    {
        $fieldName = $row['field_name'] ?? null;
        $value = $row['translation_value'] ?? $row['value'] ?? null;
        if (empty($fieldName) || $value === null || $value === '') return;

        $detail = ReferralPageSettingDetail::where('referral_page_setting_id', $setting->id)
            ->where('language_id', $this->languageId)
            ->first();

        if ($detail) {
            $detail->$fieldName = $value;
            $detail->save();
        } else {
            ReferralPageSettingDetail::create([
                'referral_page_setting_id' => $setting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]);
        }
    }

    protected function processMultiColumnFormat($setting, $row)
    {
        $fields = [
            'referral_page_setting_id' => $setting->id,
            'language_id' => $this->languageId,
            'name' => $row['name'] ?? null,
            'meta_keywords' => $row['meta_keywords'] ?? null,
            'meta_description' => $row['meta_description'] ?? null,
            'main_heading' => $row['main_heading'] ?? null,
            'your_referral_url_label' => $row['your_referral_url_label'] ?? null,
            'referral_description' => $row['referral_description'] ?? null,
            'my_referral_text' => $row['my_referral_text'] ?? null,
            'account_id_label' => $row['account_id_label'] ?? null,
            'user_label' => $row['user_label'] ?? null,
            'registered_on_label' => $row['registered_on_label'] ?? null,
            'no_referral_user_found_message' => $row['no_referral_user_found_message'] ?? null,
        ];

        ReferralPageSettingDetail::updateOrCreate(
            [
                'referral_page_setting_id' => $setting->id,
                'language_id' => $this->languageId,
            ],
            $fields
        );
    }

    public function rules(): array
    {
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') return [];
        return [
            'name' => 'required|string',
            'meta_keywords' => 'required|string',
            'meta_description' => 'required|string',
            'main_heading' => 'required|string',
            'your_referral_url_label' => 'required|string',
            'referral_description' => 'required|string',
            'my_referral_text' => 'required|string',
            'account_id_label' => 'required|string',
            'user_label' => 'required|string',
            'registered_on_label' => 'required|string',
            'no_referral_user_found_message' => 'required|string',
        ];
    }
}


