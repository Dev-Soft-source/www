<?php

namespace App\Imports;

use App\Models\ChatsPageSetting;
use App\Models\ChatsPageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ChatsPageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $languageId;

    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    public function collection(Collection $rows)
    {
        Log::info('Starting Chats Page Settings Excel import for language ID: ' . $this->languageId);
        
        $chatsPageSetting = ChatsPageSetting::first();
        if (!$chatsPageSetting) {
            $chatsPageSetting = ChatsPageSetting::create([]);
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
                $this->processSingleColumnFormat($chatsPageSetting, $row);
            }
        } else {
            $this->processMultiColumnFormat($chatsPageSetting, $firstRow);
        }
    }

    protected function processSingleColumnFormat($chatsPageSetting, $row)
    {
        $fieldName = $row['field_name'] ?? null;
        $value = $row['translation_value'] ?? $row['value'] ?? null;

        if (empty($fieldName) || empty($value)) {
            return;
        }

        $detail = ChatsPageSettingDetail::where('chats_page_setting_id', $chatsPageSetting->id)
            ->where('language_id', $this->languageId)
            ->first();

        if ($detail) {
            $detail->$fieldName = $value;
            $detail->save();
        } else {
            ChatsPageSettingDetail::create([
                'chats_page_setting_id' => $chatsPageSetting->id,
                'language_id' => $this->languageId,
                $fieldName => $value,
            ]);
        }
    }

    protected function processMultiColumnFormat($chatsPageSetting, $row)
    {
        $fields = [
            'chats_page_setting_id' => $chatsPageSetting->id,
            'language_id' => $this->languageId,
            'name' => $row['name'] ?? null,
            'meta_keywords' => $row['meta_keywords'] ?? null,
            'meta_description' => $row['meta_description'] ?? null,
            'main_heading' => $row['main_heading'] ?? null,
            'old_messages_heading' => $row['old_messages_heading'] ?? null,
            'no_messages_label' => $row['no_messages_label'] ?? null,
            'old_chat_page_main_heading' => $row['old_chat_page_main_heading'] ?? null,
            'old_chat_page_no_messages_label' => $row['old_chat_page_no_messages_label'] ?? null,
            'notification_page_main_heading' => $row['notification_page_main_heading'] ?? null,
            'notification_page_no_messages_label' => $row['notification_page_no_messages_label'] ?? null,
            'navigation_my_trip_label' => $row['navigation_my_trip_label'] ?? null,
            'navigation_chat_label' => $row['navigation_chat_label'] ?? null,
            'navigation_my_profile_label' => $row['navigation_my_profile_label'] ?? null,
            'exit_app_label' => $row['exit_app_label'] ?? null,
            'notification_filter_btn_label' => $row['notification_filter_btn_label'] ?? null,
            'notification_confirm_message' => $row['notification_confirm_message'] ?? null,
            'notification_delete_text' => $row['notification_delete_text'] ?? null,
            'type_message_placeholder' => $row['type_message_placeholder'] ?? null,
            'delete_messages_label' => $row['delete_messages_label'] ?? null,
        ];

        ChatsPageSettingDetail::updateOrCreate(
            [
                'chats_page_setting_id' => $chatsPageSetting->id,
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
            'name' => 'required|string',
            'meta_keywords' => 'required|string',
            'meta_description' => 'required|string',
            'main_heading' => 'required|string',
            'old_messages_heading' => 'required|string',
            'no_messages_label' => 'required|string',
            'old_chat_page_main_heading' => 'required|string',
            'old_chat_page_no_messages_label' => 'required|string',
            'notification_page_main_heading' => 'required|string',
            'notification_page_no_messages_label' => 'required|string',
            'notification_filter_btn_label' => 'required|string',
            'notification_confirm_message' => 'required|string',
            'notification_delete_text' => 'required|string',
            'type_message_placeholder' => 'required|string',
            'delete_messages_label' => 'required|string',
        ];
    }
}

