<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\MediaSetting;
use App\Models\MediaSettingDetail;
use Illuminate\Database\Seeder;

class MediaSettingSeeder extends Seeder
{
    public function run(): void
    {
        $mediaSetting = MediaSetting::firstOrCreate([]);

        $languages = Language::all();

        foreach ($languages as $language) {
            MediaSettingDetail::updateOrCreate(
                [
                    'media_setting_id' => $mediaSetting->id,
                    'language_id'      => $language->id,
                ],
                [
                    'name'                      => 'Media page (' . $language->name . ')',
                    'meta_keywords'             => null,
                    'meta_description'          => null,
                    'main_heading'              => 'Media',
                    'read_article_button_label' => 'Read article',
                    'agency_label'              => 'Agency',
                    'added_by_label'            => 'Added by',
                ]
            );
        }
    }
}

