<?php

namespace App\Imports;

use App\Models\SignupPageSetting;
use App\Models\SignupPageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SignupPageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function fields(): array
    {
        return [
            'name','meta_keywords','meta_description','main_heading','or_label','required_label',
            'first_name_label','first_name_error','first_name_placeholder',
            'last_name_label','last_name_error','last_name_placeholder',
            'email_label','email_error','email_placeholder',
            'password_label','password_error','password_placeholder',
            'confirm_password_label','confirm_password_error','confirm_password_placeholder',
            'agree_terms_error','phone_number_label','phone_number_option1','phone_number_option2',
            'agree_terms_label','button_label','after_button_label','signin_label',
            'app_main_heading','app_agree_terms_part1_label','app_agree_terms_link1_label','app_agree_terms_link2_label','app_agree_terms_part2_label','app_agree_terms_link3_label','app_agree_terms_part3_label',
            'no_account_label','signin_link_label','now_label','language_label'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = SignupPageSetting::first() ?? SignupPageSetting::create([]);
        if ($rows->isEmpty()) return;
        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());
        $isSingle = isset($keys[0]) && in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

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

    public function rules(): array
    {
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


