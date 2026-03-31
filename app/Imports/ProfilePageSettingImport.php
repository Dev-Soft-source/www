<?php

namespace App\Imports;

use App\Models\ProfilePageSetting;
use App\Models\ProfilePageSettingDetail;
use App\Models\Language;
use App\Exports\ProfilePageSettingTemplateExport;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProfilePageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When set, import is for a single language. When null, import is all_languages format. */
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    protected function fields(): array
    {
        return array_keys(ProfilePageSettingTemplateExport::getTranslatableFieldsWithDefaults());
    }

    public function collection(Collection $rows)
    {
        $setting = ProfilePageSetting::first() ?? ProfilePageSetting::create([]);
        if ($rows->isEmpty()) return;

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        // All-languages format: first column is field_name, rest are language columns
        $isAllLanguages = $this->languageId === null
            && (in_array('field_name', $keys) || in_array('field name', $keys))
            && count($keys) > 1;

        if ($isAllLanguages) {
            $this->processAllLanguagesFormat($setting, $rows);
            return;
        }

        $isSingle = in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        $data = [];
        if ($isSingle) {
            foreach ($rows as $row) {
                $k = strtolower(trim($row['field_name'] ?? ''));
                if (!in_array($k, $this->fields())) continue;
                $data[$k] = $row['translation_value'] ?? $row['value'] ?? null;
            }
        } else {
            $data = $firstRow->toArray();
        }

        $payload = [
            'profile_page_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fields() as $f) { $payload[$f] = $data[$f] ?? null; }

        ProfilePageSettingDetail::updateOrCreate(
            ['profile_page_setting_id' => $setting->id, 'language_id' => $this->languageId],
            $payload
        );
    }

    /**
     * Process all_languages format: each row = one field, columns = Field Name, then one per language (by header name).
     */
    protected function processAllLanguagesFormat(ProfilePageSetting $setting, Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());

        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : 'field name';
        $languageColumns = array_diff($headers, [$fieldNameKey]);

        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(function ($lang) {
            return [Str::lower($lang->name) => $lang->id];
        })->toArray();

        $validFields = $this->fields();

        foreach ($rows as $row) {
            $row = $row->toArray();
            $fieldName = $row[$fieldNameKey] ?? null;
            if (empty($fieldName) || !in_array($fieldName, $validFields, true)) {
                continue;
            }

            foreach ($languageColumns as $col) {
                $langKey = Str::lower(trim((string) $col));
                if (!isset($nameToId[$langKey])) {
                    continue;
                }
                $languageId = $nameToId[$langKey];
                $value = $row[$col] ?? null;

                $detail = ProfilePageSettingDetail::firstOrCreate(
                    [
                        'profile_page_setting_id' => $setting->id,
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

    public function rules(): array
    {
        if ($this->languageId === null) {
            return [];
        }
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') return [];
        return [
            'name' => 'required|string',
            'profile_setting_label' => 'required|string',
            'my_wallet_label' => 'required|string',
            'main_heading' => 'required|string',
            'payment_options_label' => 'required|string',
            'payout_options_label' => 'required|string',
            'my_reviews_label' => 'required|string',
            'privacy_policy_label' => 'required|string',
            'refund_policy_label' => 'required|string',
            'cancellation_policy_label' => 'required|string',
            'dispute_policy_label' => 'required|string',
            'contact_proximaride_label' => 'required|string',
            'coffee_on_wall_label' => 'required|string',
            'logout_label' => 'required|string',
            'colse_your_contact_label' => 'required|string',
        ];
    }
}


