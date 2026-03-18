<?php

namespace App\Imports;

use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AllFeaturesSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $slug = trim((string) ($row['slug'] ?? ''));
            $languageName = trim((string) ($row['language'] ?? ''));
            $languageId = $row['language_id'] ?? null;

            if ($slug === '' || ($languageName === '' && !$languageId)) {
                continue;
            }

            $language = $languageId
                ? Language::find($languageId)
                : Language::where('name', $languageName)->first();

            if (!$language) {
                continue;
            }

            $setting = FeaturesSetting::firstOrCreate(['slug' => $slug]);

            FeaturesSettingDetail::updateOrCreate(
                [
                    'features_setting_id' => $setting->id,
                    'language_id' => $language->id,
                ],
                [
                    'name' => $row['name'] ?? null,
                    'tooltip' => $row['tooltip'] ?? null,
                    'icon' => $row['icon'] ?? null,
                ]
            );
        }
    }

    public function rules(): array
    {
        return [
            '*.slug' => ['required', 'string'],
            '*.language' => ['nullable', 'string'],
            '*.language_id' => ['nullable', 'integer', 'exists:languages,id'],
            '*.name' => ['nullable', 'string'],
            '*.tooltip' => ['nullable', 'string'],
            '*.icon' => ['nullable', 'string'],
        ];
    }
}

