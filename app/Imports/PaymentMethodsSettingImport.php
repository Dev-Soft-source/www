<?php

namespace App\Imports;

use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\PostRidePageSetting;
use App\Models\PostRidePageSettingDetail;
use App\Models\FindRidePageSetting;
use App\Models\FindRidePageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PaymentMethodsSettingImport implements ToCollection, WithHeadingRow, WithValidation
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
            // booking
            'booking_option1','booking_option1_tooltip','booking_option1_icon',
            'booking_option2','booking_option2_tooltip','booking_option2_icon',
            // cancellation
            'cancellation_policy_label1','cancellation_policy_label1_tooltip',
            'cancellation_policy_label2','cancellation_policy_label2_tooltip',
            // payment
            'payment_methods_option1','payment_methods_option1_tooltip','payment_methods_option1_icon',
            'payment_methods_option2','payment_methods_option2_tooltip','payment_methods_option2_icon',
            'payment_methods_option3','payment_methods_option3_tooltip','payment_methods_option3_icon',
            // vehicle type labels used on the page
            'vehicle_type_convertible_text','vehicle_type_hatchback_text','vehicle_type_coupe_text','vehicle_type_minivan_text','vehicle_type_sedan_text','vehicle_type_station_wagon_text','vehicle_type_suv_text','vehicle_type_truck_text','vehicle_type_van_text',
        ];
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            Log::warning('No rows found in Payment Methods Excel file');
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        $isAllLanguages = $this->languageId === null
            && (in_array('field_name', $keys) || in_array('field name', $keys))
            && count($keys) > 1;

        if ($isAllLanguages) {
            $this->processAllLanguagesFormat($rows);
            Log::info('Payment Methods Settings Excel import (all languages) completed successfully');
            return;
        }

        $isSingleColumn = in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        $data = [];
        if ($isSingleColumn && $this->languageId !== null) {
            foreach ($rows as $row) {
                $name = strtolower(trim($row['field_name'] ?? ''));
                if (!$name || !in_array($name, $this->fieldsList())) continue;
                $data[$name] = $row['translation_value'] ?? $row['value'] ?? null;
            }
            $this->applyData($data);
        } elseif ($this->languageId !== null) {
            $data = $firstRow->toArray();
            $this->applyData($data);
        }
    }

    protected function processAllLanguagesFormat(Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());
        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : (in_array('field name', $headers) ? 'field name' : 'Field Name');
        $languageColumns = array_diff($headers, [$fieldNameKey]);
        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(fn ($lang) => [Str::lower($lang->name) => $lang->id])->toArray();
        $validFields = $this->fieldsList();

        foreach ($languageColumns as $col) {
            $langKey = Str::lower(trim($col));
            if (!isset($nameToId[$langKey])) continue;
            $languageId = $nameToId[$langKey];
            $data = [];
            foreach ($rows as $row) {
                $row = $row->toArray();
                $fieldName = isset($row[$fieldNameKey]) ? strtolower(trim((string) $row[$fieldNameKey])) : null;
                if (empty($fieldName) || !in_array($fieldName, $validFields, true)) continue;
                $data[$fieldName] = $row[$col] ?? null;
            }
            $this->languageId = $languageId;
            $this->applyData($data);
        }
    }

    protected function applyData(array $data): void
    {
        $languageId = $this->languageId;

        // Ensure base records
        $postRide = PostRidePageSetting::first() ?? PostRidePageSetting::create([]);
        $findRide = FindRidePageSetting::first() ?? FindRidePageSetting::create([]);

        // Ensure feature groups exist
        $featureSlugs = [
            'standard','firm','instant','manual','cash','online','secured',
            'convertible','hatchback','coupe','minivan','sedan','station_wagon','suv','truck','van'
        ];
        $slugToId = [];
        foreach ($featureSlugs as $slug) {
            $fs = FeaturesSetting::firstOrCreate(['slug' => $slug]);
            $slugToId[$slug] = $fs->id;
        }

        // Helper to upsert FeaturesSettingDetail name/icon per language
        $upsertFeature = function(string $slug, ?string $name, ?string $icon = null) use ($languageId, $slugToId) {
            $detail = FeaturesSettingDetail::whereFeaturesSettingId($slugToId[$slug])
                ->whereLanguageId($languageId)->first();
            $attrs = ['name' => $name];
            if (!is_null($icon)) $attrs['icon'] = $icon;
            if ($detail) { $detail->update($attrs); }
            else { FeaturesSettingDetail::create(array_merge($attrs, ['language_id' => $languageId, 'features_setting_id' => $slugToId[$slug]])); }
        };

        // Cancellation labels (names only)
        $upsertFeature('standard', $data['cancellation_policy_label1'] ?? null);
        $upsertFeature('firm', $data['cancellation_policy_label2'] ?? null);

        // Booking methods (names + icons)
        $upsertFeature('instant', $data['booking_option1'] ?? null, $data['booking_option1_icon'] ?? null);
        $upsertFeature('manual', $data['booking_option2'] ?? null, $data['booking_option2_icon'] ?? null);

        // Payment methods (names + icons)
        $upsertFeature('cash', $data['payment_methods_option1'] ?? null, $data['payment_methods_option1_icon'] ?? null);
        $upsertFeature('online', $data['payment_methods_option2'] ?? null, $data['payment_methods_option2_icon'] ?? null);
        $upsertFeature('secured', $data['payment_methods_option3'] ?? null, $data['payment_methods_option3_icon'] ?? null);

        // Vehicle types (names only)
        $mapVehicles = [
            'convertible' => 'vehicle_type_convertible_text',
            'hatchback' => 'vehicle_type_hatchback_text',
            'coupe' => 'vehicle_type_coupe_text',
            'minivan' => 'vehicle_type_minivan_text',
            'sedan' => 'vehicle_type_sedan_text',
            'station_wagon' => 'vehicle_type_station_wagon_text',
            'suv' => 'vehicle_type_suv_text',
            'truck' => 'vehicle_type_truck_text',
            'van' => 'vehicle_type_van_text',
        ];
        foreach ($mapVehicles as $slug => $field) {
            if (array_key_exists($field, $data)) {
                $upsertFeature($slug, $data[$field] ?? null);
            }
        }

        // PostRidePageSettingDetail tooltips and references
        $postDetail = PostRidePageSettingDetail::where('language_id', $languageId)->first();
        $payload = [
            'post_ride_page_setting_id' => $postRide->id,
            'language_id' => $languageId,
            'cancellation_policy_label1' => $slugToId['standard'],
            'cancellation_policy_label1_tooltip' => $data['cancellation_policy_label1_tooltip'] ?? null,
            'cancellation_policy_label2' => $slugToId['firm'],
            'cancellation_policy_label2_tooltip' => $data['cancellation_policy_label2_tooltip'] ?? null,
            'booking_option1' => $slugToId['instant'],
            'booking_option1_tooltip' => $data['booking_option1_tooltip'] ?? null,
            'booking_option2' => $slugToId['manual'],
            'booking_option2_tooltip' => $data['booking_option2_tooltip'] ?? null,
            'payment_methods_option1' => $slugToId['cash'],
            'payment_methods_option1_tooltip' => $data['payment_methods_option1_tooltip'] ?? null,
            'payment_methods_option2' => $slugToId['online'],
            'payment_methods_option2_tooltip' => $data['payment_methods_option2_tooltip'] ?? null,
            'payment_methods_option3' => $slugToId['secured'],
            'payment_methods_option3_tooltip' => $data['payment_methods_option3_tooltip'] ?? null,
        ];
        if ($postDetail) { $postDetail->update($payload); }
        else { PostRidePageSettingDetail::create($payload); }

        // FindRidePageSettingDetail mapping of payment methods
        FindRidePageSettingDetail::where('language_id', $languageId)->update([
            'payment_methods_option2' => $slugToId['cash'],
            'payment_methods_option3' => $slugToId['online'],
            'payment_methods_option4' => $slugToId['secured'],
        ]);
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
            'booking_option1' => 'required|string',
            'booking_option2' => 'required|string',
            'cancellation_policy_label1' => 'required|string',
            'cancellation_policy_label2' => 'required|string',
            'payment_methods_option1' => 'required|string',
            'payment_methods_option2' => 'required|string',
            'payment_methods_option3' => 'required|string',
        ];
    }
}


