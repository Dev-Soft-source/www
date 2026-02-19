<?php

namespace App\Imports;

use App\Models\ChatsPageSetting;
use App\Models\ChatsPageSettingDetail;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ChatsPageSettingImport implements ToCollection, WithHeadingRow, WithValidation
{
    /** @var int|null When null, import expects all_languages format (Field Name + one column per language). */
    protected $languageId;

    public function __construct($languageId = null)
    {
        $this->languageId = $languageId;
    }

    public function collection(Collection $rows)
    {
        $chatsPageSetting = ChatsPageSetting::first();
        if (!$chatsPageSetting) {
            $chatsPageSetting = ChatsPageSetting::create([]);
        }

        if ($rows->isEmpty()) {
            Log::warning('No rows found in Chats Excel file');
            return;
        }

        $firstRow = $rows->first();
        $keys = array_keys($firstRow->toArray());

        $isAllLanguages = $this->languageId === null
            && (in_array('field_name', $keys) || in_array('field name', $keys))
            && count($keys) > 1;

        if ($isAllLanguages) {
            $this->processAllLanguagesFormat($chatsPageSetting, $rows);
            Log::info('Chats Page Settings Excel import (all languages) completed successfully');
            return;
        }

        $isSingleColumn = in_array('field_name', $keys) && (in_array('value', $keys) || in_array('translation_value', $keys));

        if ($isSingleColumn && $this->languageId !== null) {
            foreach ($rows as $row) {
                $this->processSingleColumnFormat($chatsPageSetting, $row);
            }
        } else {
            if ($this->languageId !== null) {
                $this->processMultiColumnFormat($chatsPageSetting, $firstRow);
            }
        }
    }

    protected function processAllLanguagesFormat(ChatsPageSetting $chatsPageSetting, Collection $rows): void
    {
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());
        $fieldNameKey = in_array('field_name', $headers) ? 'field_name' : 'field name';
        $languageColumns = array_diff($headers, [$fieldNameKey]);
        $languages = Language::orderBy('id')->get();
        $nameToId = $languages->mapWithKeys(fn ($lang) => [Str::lower($lang->name) => $lang->id])->toArray();
        $validFields = array_keys(\App\Exports\ChatsPageSettingTemplateExport::getTranslatableFieldsWithDefaults());

        foreach ($rows as $row) {
            $row = $row->toArray();
            $fieldName = $row[$fieldNameKey] ?? null;
            if (empty($fieldName) || !in_array($fieldName, $validFields, true)) {
                continue;
            }
            foreach ($languageColumns as $col) {
                $langKey = Str::lower(trim($col));
                if (!isset($nameToId[$langKey])) {
                    continue;
                }
                $languageId = $nameToId[$langKey];
                $value = $row[$col] ?? null;
                $detail = ChatsPageSettingDetail::firstOrCreate(
                    [
                        'chats_page_setting_id' => $chatsPageSetting->id,
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
        if ($this->languageId === null) {
            return [];
        }
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

