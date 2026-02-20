<?php

namespace App\Imports;

use App\Exports\SignupPageSettingTemplateExport;
use App\Models\SignupPageSetting;
use App\Models\SignupPageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SignupPageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When set, import is for a single language. When null, import is all_languages format. */
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    protected function fields(): array
    {
        return array_keys(SignupPageSettingTemplateExport::getTranslatableFieldsWithDefaults());
    }

    public function collection(Collection $rows)
    {
        $setting = SignupPageSetting::first() ?? SignupPageSetting::create([]);
        if ($rows->isEmpty()) return;

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

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
            'signup_page_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fields() as $f) { $payload[$f] = $data[$f] ?? null; }

        SignupPageSettingDetail::updateOrCreate(
            ['signup_page_setting_id' => $setting->id, 'language_id' => $this->languageId],
            $payload
        );
    }

    protected function processAllLanguagesFormat(SignupPageSetting $setting, Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());
        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : 'field name';
        $languageColumns = array_diff($headers, [$fieldNameKey]);

        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(fn($lang) => [Str::lower($lang->name) => $lang->id])->toArray();
        $validFields = $this->fields();

        foreach ($rows as $row) {
            $row = $row->toArray();
            $fieldName = $row[$fieldNameKey] ?? null;
            if (empty($fieldName) || !in_array($fieldName, $validFields, true)) continue;

            foreach ($languageColumns as $col) {
                $langKey = Str::lower(trim((string) $col));
                if (!isset($nameToId[$langKey])) continue;
                $languageId = $nameToId[$langKey];
                $value = $row[$col] ?? null;

                $detail = SignupPageSettingDetail::firstOrCreate(
                    ['signup_page_setting_id' => $setting->id, 'language_id' => $languageId],
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
        if ($this->languageId === null) return [];
        $language = Language::find($this->languageId);
        if (!$language || $language->is_default != '1') return [];
        return [
            'name' => 'required|string',
            'meta_keywords' => 'required|string',
            'meta_description' => 'required|string',
            'main_heading' => 'required|string',
            'or_label' => 'required|string',
            'required_label' => 'required|string',
            'first_name_label' => 'required|string',
            'first_name_error' => 'required|string',
            'last_name_label' => 'required|string',
            'last_name_error' => 'required|string',
            'email_label' => 'required|string',
            'email_error' => 'required|string',
            'password_label' => 'required|string',
            'password_error' => 'required|string',
            'confirm_password_label' => 'required|string',
            'confirm_password_error' => 'required|string',
            'agree_terms_label' => 'required|string',
            'button_label' => 'required|string',
            'signin_label' => 'required|string',
            'app_main_heading' => 'required|string',
            'app_agree_terms_part1_label' => 'required|string',
            'app_agree_terms_link1_label' => 'required|string',
            'app_agree_terms_link2_label' => 'required|string',
            'app_agree_terms_part2_label' => 'required|string',
            'app_agree_terms_link3_label' => 'required|string',
            'app_agree_terms_part3_label' => 'required|string',
            'no_account_label' => 'required|string',
            'signin_link_label' => 'required|string',
            'now_label' => 'required|string',
            'language_label' => 'required|string',
        ];
    }
}
