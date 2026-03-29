<?php

namespace App\Services;

use App\Models\MediaSettingDetail;

class MediaSettingService
{
    public function validation($languages, $validationRule, $errorMessages)
    {
        $niceNames = [];

        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, [
                    'main_heading.main_heading_' . $language->id => ['required', 'string'],
                ]);
                $errorMessages = array_merge($errorMessages, [
                    'main_heading.main_heading_' . $language->id . '.required' =>
                        'This field in ' . $language->name . ' is required.',
                ]);

                $validationRule = array_merge($validationRule, [
                    'read_article_button_label.read_article_button_label_' . $language->id => ['required', 'string'],
                ]);
                $errorMessages = array_merge($errorMessages, [
                    'read_article_button_label.read_article_button_label_' . $language->id . '.required' =>
                        'This field in ' . $language->name . ' is required.',
                ]);

                $validationRule = array_merge($validationRule, [
                    'agency_label.agency_label_' . $language->id => ['required', 'string'],
                ]);
                $errorMessages = array_merge($errorMessages, [
                    'agency_label.agency_label_' . $language->id . '.required' =>
                        'This field in ' . $language->name . ' is required.',
                ]);

                $validationRule = array_merge($validationRule, [
                    'added_by_label.added_by_label_' . $language->id => ['required', 'string'],
                ]);
                $errorMessages = array_merge($errorMessages, [
                    'added_by_label.added_by_label_' . $language->id . '.required' =>
                        'This field in ' . $language->name . ' is required.',
                ]);
            }
        }

        return [
            'validation_rules' => $validationRule,
            'error_messages'   => $errorMessages,
            'nice_names'       => $niceNames,
        ];
    }

    public function fields($mediaSetting, $language, $request)
    {
        return [
            'media_setting_id'          => $mediaSetting->id,
            'language_id'               => $language->id,
            'name'                      => $this->data($request, $language, 'name'),
            'meta_keywords'             => $this->data($request, $language, 'meta_keywords'),
            'meta_description'          => $this->data($request, $language, 'meta_description'),
            'main_heading'              => $this->data($request, $language, 'main_heading'),
            'read_article_button_label' => $this->data($request, $language, 'read_article_button_label'),
            'agency_label'              => $this->data($request, $language, 'agency_label'),
            'added_by_label'            => $this->data($request, $language, 'added_by_label'),
        ];
    }

    public function update($mediaSetting, $language, $request)
    {
        $fields = $this->fields($mediaSetting, $language, $request);

        $exists = MediaSettingDetail::whereMediaSettingId($mediaSetting->id)
            ->whereLanguageId($language->id)
            ->exists();

        if (!$exists) {
            MediaSettingDetail::create($fields);
        } else {
            MediaSettingDetail::whereMediaSettingId($mediaSetting->id)
                ->whereLanguageId($language->id)
                ->first()?->update($fields);
        }

        return true;
    }

    protected function data($request, $language, $name)
    {
        return isset($request[$name][$name . '_' . $language->id])
            ? $request[$name][$name . '_' . $language->id]
            : null;
    }
}

