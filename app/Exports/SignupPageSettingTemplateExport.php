<?php

namespace App\Exports;

use App\Models\Language;
use App\Models\SignupPageSetting;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SignupPageSettingTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnWidths
{
    protected $format;

    /** @var \Illuminate\Support\Collection|null */
    protected $languages;

    /** @var SignupPageSetting|null */
    protected $existingData;

    public static function getTranslatableFieldsWithDefaults(): array
    {
        return [
            'name' => 'Signup',
            'meta_keywords' => 'signup, register',
            'meta_description' => 'Sign up page',
            'main_heading' => 'Create account',
            'or_label' => 'Or',
            'required_label' => 'Required',
            'first_name_label' => 'First name',
            'first_name_error' => 'Please enter first name',
            'first_name_placeholder' => 'First name',
            'last_name_label' => 'Last name',
            'last_name_error' => 'Please enter last name',
            'last_name_placeholder' => 'Last name',
            'email_label' => 'Email',
            'email_error' => 'Please enter valid email',
            'email_placeholder' => 'Email',
            'password_label' => 'Password',
            'password_error' => 'Please enter password',
            'password_placeholder' => 'Password',
            'confirm_password_label' => 'Confirm password',
            'confirm_password_error' => 'Passwords do not match',
            'confirm_password_placeholder' => 'Confirm password',
            'agree_terms_error' => 'You must agree to the terms',
            'phone_number_label' => 'Phone number',
            'phone_number_option1' => 'Option 1',
            'phone_number_option2' => 'Option 2',
            'agree_terms_label' => 'I agree to the terms',
            'button_label' => 'Sign up',
            'after_button_label' => 'After sign up',
            'signin_label' => 'Sign in',
            'app_main_heading' => 'Create account',
            'app_agree_terms_part1_label' => 'I agree to the',
            'app_agree_terms_link1_label' => 'terms',
            'app_agree_terms_link2_label' => 'and',
            'app_agree_terms_part2_label' => 'privacy policy',
            'app_agree_terms_link3_label' => 'privacy',
            'app_agree_terms_part3_label' => 'policy',
            'no_account_label' => "Don't have an account?",
            'signin_link_label' => 'Sign in',
            'now_label' => 'now',
            'language_label' => 'Language',
        ];
    }

    public function __construct($format = 'single_column', $languages = null, $existingData = null)
    {
        $this->format = $format;
        $this->languages = $languages ? collect($languages) : null;
        $this->existingData = $existingData;
    }

    public function collection(): Collection
    {
        $fields = array_keys(static::getTranslatableFieldsWithDefaults());
        if ($this->format === 'all_languages') {
            return $this->allLanguagesFormat();
        }
        if ($this->format === 'single_column') {
            $defaults = static::getTranslatableFieldsWithDefaults();
            $rows = [];
            foreach ($fields as $field) {
                $rows[] = ['field_name' => $field, 'translation_value' => $defaults[$field] ?? ''];
            }
            return new Collection($rows);
        }
        $row = array_fill_keys($fields, '');
        return new Collection([$row]);
    }

    protected function allLanguagesFormat(): Collection
    {
        $languages = $this->languages ?? Language::orderBy('id')->get();
        $fieldsWithDefaults = static::getTranslatableFieldsWithDefaults();
        $detailsByLang = [];
        if ($this->existingData && $this->existingData->relationLoaded('signupPageSettingDetail')) {
            foreach ($this->existingData->signupPageSettingDetail as $d) {
                $detailsByLang[$d->language_id] = $d;
            }
        }

        $rows = [];
        foreach ($fieldsWithDefaults as $fieldKey => $defaultValue) {
            $row = [$fieldKey];
            foreach ($languages as $lang) {
                $detail = $detailsByLang[$lang->id] ?? null;
                $value = $detail && isset($detail->$fieldKey) ? ($detail->$fieldKey ?? '') : $defaultValue;
                $row[] = $value;
            }
            $rows[] = $row;
        }
        return collect($rows);
    }

    public function headings(): array
    {
        if ($this->format === 'single_column') {
            return ['Field Name', 'Translation Value'];
        }
        if ($this->format === 'all_languages') {
            $languages = $this->languages ?? Language::orderBy('id')->get();
            return array_merge(['Field Name'], $languages->pluck('name')->toArray());
        }
        $fields = array_keys(static::getTranslatableFieldsWithDefaults());
        return array_map(fn($f) => ucwords(str_replace('_', ' ', $f)), $fields);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        if ($this->format === 'single_column') {
            return ['A' => 40, 'B' => 50];
        }
        if ($this->format === 'all_languages') {
            $totalCols = ($this->languages ?? Language::orderBy('id')->get())->count() + 1;
            $widths = [];
            for ($colIndex = 1; $colIndex <= $totalCols; $colIndex++) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $widths[$col] = $colIndex === 1 ? 40 : 30;
            }
            return $widths;
        }
        return ['A' => 25];
    }
}
