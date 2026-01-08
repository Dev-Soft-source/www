<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EditProfilePageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $format;

    public function __construct($format = 'single_column')
    {
        $this->format = $format;
    }

    public function collection(): Collection
    {
        if ($this->format === 'single_column') {
            $data = [];
            foreach ($this->getFields() as $field) {
                $data[] = ['field_name' => $field, 'translation_value' => ''];
            }
            return new Collection($data);
        }
        $row = array_fill_keys($this->getFields(), '');
        return new Collection([$row]);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') return ['Field Name', 'Translation Value'];
        return array_map(fn($f) => ucwords(str_replace('_', ' ', $f)), $this->getFields());
    }

    protected function getFields(): array
    {
        return [
            'name','min_bio_label','passenger_driven_label','main_heading','rides_taken_label','km_shared_label','review_label','reply_label','link_review_label','review_heading','edit_profile_text','first_name_label','first_name_placeholder','last_name_label','last_name_placeholder','gender_label','male_label','female_label','prefer_no_to_say_label','dob_label','dob_placeholder','country_label','country_placeholder','state_label','state_placeholder','city_label','city_placeholder','address_label','address_placeholder','zip_label','mini_bio_label','mini_bio_placeholder','govt_id_label','govt_id_text','image_placeholder','choose_file_placeholder','image_option_placeholder','new_image_button_text','save_button_text','joined_label','passenger_label','vehicle_info_label','year_old_label','replied_label','response_label','reply_heading_label','reply_placeholder','reply_submit_button_label','profile_label'
        ];
    }
}


