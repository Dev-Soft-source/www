<?php

namespace App\Imports;

use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\FindRidePageSetting;
use App\Models\FindRidePageSettingDetail;
use App\Models\PostRidePageSetting;
use App\Models\PostRidePageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LuggageOptionsSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function fieldsList(): array
    {
        return [
            'luggage_option1','luggage_option1_tooltip','luggage_option1_icon',
            'luggage_option2','luggage_option2_tooltip','luggage_option2_icon',
            'luggage_option3','luggage_option3_tooltip','luggage_option3_icon',
            'luggage_option4','luggage_option4_tooltip','luggage_option4_icon',
            'luggage_option5','luggage_option5_tooltip','luggage_option5_icon',
            'luggage_option5_label',
        ];
    }

    public function collection(Collection $rows)
    {
        $postRide = PostRidePageSetting::first() ?? PostRidePageSetting::create([]);
        $findRide = FindRidePageSetting::first() ?? FindRidePageSetting::create([]);

        // ensure features settings exist
        $slugs = [
            1 => 'no_luggage',
            2 => 'small_luggage',
            3 => 'medium_luggage',
            4 => 'large_luggage',
            5 => 'xl_luggage',
        ];
        $features = [];
        foreach ($slugs as $i => $slug) {
            $features[$i] = FeaturesSetting::firstOrCreate(['slug' => $slug]);
        }

        if ($rows->isEmpty()) return;
        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());
        $isSingleColumn = in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        if ($isSingleColumn) {
            $data = [];
            foreach ($rows as $row) {
                $name = strtolower(trim($row['field_name'] ?? ''));
                if (!$name || !in_array($name, $this->fieldsList())) continue;
                $data[$name] = $row['translation_value'] ?? $row['value'] ?? null;
            }
            $this->applyData($features, $postRide, $findRide, $data);
        } else {
            $this->applyData($features, $postRide, $findRide, $firstRow->toArray());
        }
    }

    protected function applyData(array $features, $postRide, $findRide, array $data): void
    {
        $langId = $this->languageId;
        // Update feature names and icons per option
        for ($i = 1; $i <= 5; $i++) {
            $nameKey = "luggage_option{$i}";
            $iconKey = "luggage_option{$i}_icon";
            $name = $data[$nameKey] ?? null;
            $icon = $data[$iconKey] ?? null;
            if ($name !== null || $icon !== null) {
                FeaturesSettingDetail::updateOrCreate(
                    [
                        'features_setting_id' => $features[$i]->id,
                        'language_id' => $langId,
                    ],
                    [
                        'features_setting_id' => $features[$i]->id,
                        'language_id' => $langId,
                        'name' => $name,
                        'icon' => $icon,
                    ]
                );
            }
        }

        // Update tooltips and label in PostRide
        $postFields = [
            'luggage_option1_tooltip','luggage_option2_tooltip','luggage_option3_tooltip','luggage_option4_tooltip','luggage_option5_tooltip','luggage_option5_label'
        ];
        $payload = [
            'post_ride_page_setting_id' => $postRide->id,
            'language_id' => $langId,
        ];
        foreach ($postFields as $k) { $payload[$k] = $data[$k] ?? null; }
        PostRidePageSettingDetail::updateOrCreate([
            'post_ride_page_setting_id' => $postRide->id,
            'language_id' => $langId,
        ], $payload);

        // Update label in FindRide
        FindRidePageSettingDetail::updateOrCreate([
            'find_ride_page_setting_id' => $findRide->id ?? 0,
            'language_id' => $langId,
        ], [
            'find_ride_page_setting_id' => $findRide->id,
            'language_id' => $langId,
            'luggage_option5_label' => $data['luggage_option5_label'] ?? null,
        ]);
    }

    public function rules(): array
    {
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') return [];
        return [
            'luggage_option1' => 'required|string',
            'luggage_option2' => 'required|string',
            'luggage_option3' => 'required|string',
            'luggage_option4' => 'required|string',
            'luggage_option5' => 'required|string',
            'luggage_option5_label' => 'required|string',
        ];
    }
}


