<?php

namespace App\Imports;

use App\Models\CloseAccountSetting;
use App\Models\CloseAccountSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CloseAccountSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    public function collection(Collection $rows)
    {
        Log::info('Starting Close Account Settings Excel import for language ID: ' . $this->languageId);
        
        $closeAccountSetting = CloseAccountSetting::first();
        if (!$closeAccountSetting) {
            $closeAccountSetting = CloseAccountSetting::create([]);
        }

        if ($rows->isEmpty()) {
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        $isSingleColumn = isset($keys[0]) && 
                          (in_array('field_name', $keys) && 
                          (in_array('value', $keys) || in_array('translation_value', $keys)));

        if ($isSingleColumn) {
            foreach ($rows as $row) {
                $this->processSingleColumnFormat($closeAccountSetting, $row);
            }
        } else {
            $this->processMultiColumnFormat($closeAccountSetting, $firstRow);
        }
    }

    protected function processSingleColumnFormat($closeAccountSetting, $row)
    {
        $fieldName = $row['field_name'] ?? null;
        $value = $row['translation_value'] ?? $row['value'] ?? null;

        if (empty($fieldName) || empty($value)) {
            return;
        }

        $detail = CloseAccountSettingDetail::where('close_acc_setting_id', $closeAccountSetting->id)
            ->where('language_id', $this->languageId)
            ->first();

        if ($detail) {
            $detail->$fieldName = $value;
            $detail->save();
        } else {
            CloseAccountSettingDetail::create([
                'close_acc_setting_id' => $closeAccountSetting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]);
        }
    }

    protected function processMultiColumnFormat($closeAccountSetting, $row)
    {
        $fields = [
            'close_acc_setting_id' => $closeAccountSetting->id,
            'language_id' => $this->languageId,
            'warning_text' => $row['warning_text'] ?? null,
            'mobile_indicate_required_field_label' => $row['mobile_indicate_required_field_label'] ?? null,
            'main_heading' => $row['main_heading'] ?? null,
            'closing_account_label' => $row['closing_account_label'] ?? null,
            'apply_reason_label' => $row['apply_reason_label'] ?? null,
            'reason_label' => $row['reason_label'] ?? null,
            'not_say_checkbox_label' => $row['not_say_checkbox_label'] ?? null,
            'check_box_validation_message' => $row['check_box_validation_message'] ?? null,
            'customer_service_checkbox_label' => $row['customer_service_checkbox_label'] ?? null,
            'technical_issue_checkbox_label' => $row['technical_issue_checkbox_label'] ?? null,
            'dont_use_checkbox_label' => $row['dont_use_checkbox_label'] ?? null,
            'another_account_checkbox_label' => $row['another_account_checkbox_label'] ?? null,
            'did_not_get_booking_checkbox_label' => $row['did_not_get_booking_checkbox_label'] ?? null,
            'did_not_find_ride_checkbox_label' => $row['did_not_find_ride_checkbox_label'] ?? null,
            'did_not_find_destination_checkbox_label' => $row['did_not_find_destination_checkbox_label'] ?? null,
            'other_checkbox_label' => $row['other_checkbox_label'] ?? null,
            'recommend_heading' => $row['recommend_heading'] ?? null,
            'yes_checkbox_label' => $row['yes_checkbox_label'] ?? null,
            'no_checkbox_label' => $row['no_checkbox_label'] ?? null,
            'prefer_not_checkbox_label' => $row['prefer_not_checkbox_label'] ?? null,
            'why_closing_account_label' => $row['why_closing_account_label'] ?? null,
            'why_closing_account_placeholder' => $row['why_closing_account_placeholder'] ?? null,
            'improve_label' => $row['improve_label'] ?? null,
            'improve_placeholder' => $row['improve_placeholder'] ?? null,
            'close_my_account_checkbox' => $row['close_my_account_checkbox'] ?? null,
            'close_my_account_checkbox_error' => $row['close_my_account_checkbox_error'] ?? null,
            'close_account_button_text' => $row['close_account_button_text'] ?? null,
            'difficulties_making_receiving_payments_label' => $row['difficulties_making_receiving_payments_label'] ?? null,
            'take_me_back_button_label' => $row['take_me_back_button_label'] ?? null,
            'close_it_button_label' => $row['close_it_button_label'] ?? null,
            'close_account_sure_message_text' => $row['close_account_sure_message_text'] ?? null,
            'web_irreversible_label' => $row['web_irreversible_label'] ?? null,
            'web_closing_account_reason_label' => $row['web_closing_account_reason_label'] ?? null,
        ];

        CloseAccountSettingDetail::updateOrCreate(
            [
                'close_acc_setting_id' => $closeAccountSetting->id,
                'language_id' => $this->languageId,
            ],
            $fields
        );
    }

    public function rules(): array
    {
        $language = Language::find($this->languageId);
        
        if (!$language || $language->is_default != '1') {
            return [];
        }

        return [
            'warning_text' => 'required|string',
            'mobile_indicate_required_field_label' => 'required|string',
            'main_heading' => 'required|string',
            'closing_account_label' => 'required|string',
            'apply_reason_label' => 'required|string',
            'reason_label' => 'required|string',
            'customer_service_checkbox_label' => 'required|string',
            'dont_use_checkbox_label' => 'required|string',
            'another_account_checkbox_label' => 'required|string',
            'did_not_get_booking_checkbox_label' => 'required|string',
            'did_not_find_ride_checkbox_label' => 'required|string',
            'did_not_find_destination_checkbox_label' => 'required|string',
            'other_checkbox_label' => 'required|string',
            'recommend_heading' => 'required|string',
            'yes_checkbox_label' => 'required|string',
            'no_checkbox_label' => 'required|string',
            'prefer_not_checkbox_label' => 'required|string',
            'why_closing_account_label' => 'required|string',
            'why_closing_account_placeholder' => 'required|string',
            'improve_label' => 'required|string',
            'improve_placeholder' => 'required|string',
            'close_my_account_checkbox' => 'required|string',
            'close_my_account_checkbox_error' => 'required|string',
            'close_account_button_text' => 'required|string',
            'difficulties_making_receiving_payments_label' => 'required|string',
            'web_closing_account_reason_label' => 'required|string',
            'web_irreversible_label' => 'required|string',
            'close_account_sure_message_text' => 'required|string',
            'close_it_button_label' => 'required|string',
            'take_me_back_button_label' => 'required|string',
        ];
    }
}

