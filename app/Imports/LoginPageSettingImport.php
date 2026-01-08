<?php

namespace App\Imports;

use App\Models\LoginPageSetting;
use App\Models\LoginPageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LoginPageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function fieldsList(): array
    {
        return [
            'name','meta_keywords','meta_description','main_heading','continue_label','new_verification_email_btn_label','or_label','email_label','email_error','email_placeholder','password_label','password_error','password_placeholder','forgot_password_label','submit_button_label','signup_label','no_account_label','signup_link_label','now_label','language_label','protect_account_heading','protect_account_text','remember_me_text','close_modal_error_message'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = LoginPageSetting::first();
        if (!$setting) $setting = LoginPageSetting::create([]);
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
        $fieldName = strtolower(trim($fieldName));
        if (!in_array($fieldName, $this->fieldsList())) return;

        $detail = LoginPageSettingDetail::where('login_page_setting_id', $setting->id)
            ->where('language_id', $this->languageId)
            ->first();

        if ($detail) {
            $detail->$fieldName = $value;
            $detail->save();
        } else {
            LoginPageSettingDetail::create([
                'login_page_setting_id' => $setting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]);
        }
    }

    protected function processMultiColumnFormat($setting, $row)
    {
        $fields = [
            'login_page_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fieldsList() as $f) {
            $fields[$f] = $row[$f] ?? null;
        }

        LoginPageSettingDetail::updateOrCreate(
            [
                'login_page_setting_id' => $setting->id,
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
            'continue_label' => 'required|string',
            'or_label' => 'required|string',
            'email_label' => 'required|string',
            'email_error' => 'required|string',
            'password_label' => 'required|string',
            'password_error' => 'required|string',
            'forgot_password_label' => 'required|string',
            'submit_button_label' => 'required|string',
            'signup_label' => 'required|string',
            'no_account_label' => 'required|string',
            'signup_link_label' => 'required|string',
            'now_label' => 'required|string',
            'language_label' => 'required|string',
            'new_verification_email_btn_label' => 'required|string',
            'protect_account_heading' => 'required|string',
            'protect_account_text' => 'required|string',
            'remember_me_text' => 'required|string',
        ];
    }
}


