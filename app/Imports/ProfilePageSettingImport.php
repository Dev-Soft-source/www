<?php

namespace App\Imports;

use App\Models\ProfilePageSetting;
use App\Models\ProfilePageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProfilePageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    protected function fields(): array
    {
        return [
            'name','profile_setting_label','my_wallet_label','main_heading','payment_options_label','payout_options_label','my_reviews_label','terms_condition_label','privacy_policy_label','terms_of_use_label','refund_policy_label','cancellation_policy_label','dispute_policy_label','contact_proximaride_label','logout_label','colse_your_contact_label'
        ];
    }

    public function collection(Collection $rows)
    {
        $setting = ProfilePageSetting::first() ?? ProfilePageSetting::create([]);
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
            'profile_page_setting_id' => $setting->id,
            'language_id' => $this->languageId,
        ];
        foreach ($this->fields() as $f) { $payload[$f] = $data[$f] ?? null; }

        ProfilePageSettingDetail::updateOrCreate(
            ['profile_page_setting_id' => $setting->id, 'language_id' => $this->languageId],
            $payload
        );
    }

    public function rules(): array
    {
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
            'logout_label' => 'required|string',
            'colse_your_contact_label' => 'required|string',
        ];
    }
}


