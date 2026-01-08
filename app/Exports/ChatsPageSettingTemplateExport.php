<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class ChatsPageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $format;

    public function __construct($format = 'single_column')
    {
        $this->format = $format;
    }

    public function collection(): Collection
    {
        if ($this->format === 'single_column') {
            return $this->singleColumnFormat();
        }
        
        return $this->multiColumnFormat();
    }

    protected function singleColumnFormat()
    {
        $fields = $this->getFields();
        $data = [];
        foreach ($fields as $field) {
            $data[] = ['field_name' => $field, 'translation_value' => ''];
        }
        return new Collection($data);
    }

    protected function multiColumnFormat()
    {
        $fields = $this->getFields();
        $rowData = array_fill_keys($fields, '');
        return new Collection([$rowData]);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') {
            return ['Field Name', 'Translation Value'];
        }
        
        return array_map(function($field) {
            return ucwords(str_replace('_', ' ', $field));
        }, $this->getFields());
    }

    protected function getFields(): array
    {
        return [
            'name',
            'meta_keywords',
            'meta_description',
            'main_heading',
            'old_messages_heading',
            'no_messages_label',
            'old_chat_page_main_heading',
            'old_chat_page_no_messages_label',
            'notification_page_main_heading',
            'notification_page_no_messages_label',
            'navigation_my_trip_label',
            'navigation_chat_label',
            'navigation_my_profile_label',
            'exit_app_label',
            'notification_filter_btn_label',
            'notification_confirm_message',
            'notification_delete_text',
            'type_message_placeholder',
            'delete_messages_label',
        ];
    }
}

