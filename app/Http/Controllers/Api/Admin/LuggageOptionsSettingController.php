<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\FindRidePageSetting;
use App\Models\FindRidePageSettingDetail;
use App\Models\PostRidePageSetting;
use App\Models\PostRidePageSettingDetail;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Imports\LuggageOptionsSettingImport;
use App\Exports\LuggageOptionsSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class LuggageOptionsSettingController extends Controller
{
    use StatusResponser;

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['luggage_option1.luggage_option1_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['luggage_option1.luggage_option1_' . $language->id . '.required' => 'Luggage option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['luggage_option2.luggage_option2_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['luggage_option2.luggage_option2_' . $language->id . '.required' => 'Luggage option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['luggage_option3.luggage_option3_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['luggage_option3.luggage_option3_' . $language->id . '.required' => 'Luggage option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['luggage_option4.luggage_option4_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['luggage_option4.luggage_option4_' . $language->id . '.required' => 'Luggage option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['luggage_option5.luggage_option5_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['luggage_option5.luggage_option5_' . $language->id . '.required' => 'Luggage option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['luggage_option5_label.luggage_option5_label_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['luggage_option5_label.luggage_option5_label_' . $language->id . '.required' => 'Luggage option in ' . $language->name . ' is required.']);
            }
        }

        $this->validate(
            $request,
            $validationRule,
            $errorMessages,
        );

        $luggageSetting1 = FeaturesSetting::whereSlug('no_luggage')->first();
        if (!$luggageSetting1) {
            $luggageSetting1 = FeaturesSetting::create([
                'slug' => 'no_luggage',
            ]);
        }
        foreach ($languages as $language) {
            $luggageOption1 = FeaturesSettingDetail::whereFeaturesSettingId($luggageSetting1->id)->whereLanguageId($language->id)->first();
            if ($luggageOption1) {
                $luggageOption1->update([
                    'name' => $request['luggage_option1']['luggage_option1_' . $language->id],
                    'icon' => $request['luggage_option1_icon']['luggage_option1_icon_' . $language->id],
                ]);
            } else {
                $luggageOption1 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $luggageSetting1->id,
                    'name' => $request['luggage_option1']['luggage_option1_' . $language->id],
                    'icon' => $request['luggage_option1_icon']['luggage_option1_icon_' . $language->id],
                ]);
            }
        }

        $luggageSetting2 = FeaturesSetting::whereSlug('small_luggage')->first();
        if (!$luggageSetting2) {
            $luggageSetting2 = FeaturesSetting::create([
                'slug' => 'small_luggage',
            ]);
        }
        foreach ($languages as $language) {
            $luggageOption2 = FeaturesSettingDetail::whereFeaturesSettingId($luggageSetting2->id)->whereLanguageId($language->id)->first();
            if ($luggageOption2) {
                $luggageOption2->update([
                    'name' => $request['luggage_option2']['luggage_option2_' . $language->id],
                    'icon' => $request['luggage_option2_icon']['luggage_option2_icon_' . $language->id],
                ]);
            } else {
                $luggageOption2 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $luggageSetting2->id,
                    'name' => $request['luggage_option2']['luggage_option2_' . $language->id],
                    'icon' => $request['luggage_option2_icon']['luggage_option2_icon_' . $language->id],
                ]);
            }
        }

        $luggageSetting3 = FeaturesSetting::whereSlug('medium_luggage')->first();
        if (!$luggageSetting3) {
            $luggageSetting3 = FeaturesSetting::create([
                'slug' => 'medium_luggage',
            ]);
        }
        foreach ($languages as $language) {
            $luggageOption3 = FeaturesSettingDetail::whereFeaturesSettingId($luggageSetting3->id)->whereLanguageId($language->id)->first();
            if ($luggageOption3) {
                $luggageOption3->update([
                    'name' => $request['luggage_option3']['luggage_option3_' . $language->id],
                    'icon' => $request['luggage_option3_icon']['luggage_option3_icon_' . $language->id],
                ]);
            } else {
                $luggageOption3 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $luggageSetting3->id,
                    'name' => $request['luggage_option3']['luggage_option3_' . $language->id],
                    'icon' => $request['luggage_option3_icon']['luggage_option3_icon_' . $language->id],
                ]);
            }
        }
        $luggageSetting4 = FeaturesSetting::whereSlug('large_luggage')->first();
        if (!$luggageSetting4) {
            $luggageSetting4 = FeaturesSetting::create([
                'slug' => 'large_luggage',
            ]);
        }
        foreach ($languages as $language) {
            $luggageOption4 = FeaturesSettingDetail::whereFeaturesSettingId($luggageSetting4->id)->whereLanguageId($language->id)->first();
            if ($luggageOption4) {
                $luggageOption4->update([
                    'name' => $request['luggage_option4']['luggage_option4_' . $language->id],
                    'icon' => $request['luggage_option4_icon']['luggage_option4_icon_' . $language->id],
                ]);
            } else {
                $luggageOption4 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $luggageSetting4->id,
                    'name' => $request['luggage_option4']['luggage_option4_' . $language->id],
                    'icon' => $request['luggage_option4_icon']['luggage_option4_icon_' . $language->id],
                ]);
            }
        }

        $luggageSetting5 = FeaturesSetting::whereSlug('xl_luggage')->first();
        if (!$luggageSetting5) {
            $luggageSetting5 = FeaturesSetting::create([
                'slug' => 'xl_luggage',
            ]);
        }
        foreach ($languages as $language) {
            $luggageOption5 = FeaturesSettingDetail::whereFeaturesSettingId($luggageSetting5->id)->whereLanguageId($language->id)->first();
            if ($luggageOption5) {
                $luggageOption5->update([
                    'name' => $request['luggage_option5']['luggage_option5_' . $language->id],
                    'icon' => $request['luggage_option5_icon']['luggage_option5_icon_' . $language->id],
                ]);
            } else {
                $luggageOption5 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $luggageSetting5->id,
                    'name' => $request['luggage_option5']['luggage_option5_' . $language->id],
                    'icon' => $request['luggage_option5_icon']['luggage_option5_icon_' . $language->id],
                ]);
            }
        }

        $postRidePageSetting = PostRidePageSetting::first();
        if (!$postRidePageSetting) {
            $postRidePageSetting = PostRidePageSetting::create([]);
        }
        foreach ($languages as $language) {
            $postRidePageSettingDetail = PostRidePageSettingDetail::whereLanguageId($language->id)->first();
            if (!$postRidePageSettingDetail) {
                $postRidePageSettingDetail = PostRidePageSettingDetail::create([
                    'post_ride_page_setting_id' => $postRidePageSetting->id,
                    'language_id' => $language->id,
                    'luggage_option1' => $luggageSetting1->id,
                    'luggage_option1_tooltip' => $request['luggage_option1_tooltip']['luggage_option1_tooltip_' . $language->id],
                    'luggage_option2' => $luggageSetting2->id,
                    'luggage_option2_tooltip' => $request['luggage_option2_tooltip']['luggage_option2_tooltip_' . $language->id],
                    'luggage_option3' => $luggageSetting3->id,
                    'luggage_option3_tooltip' => $request['luggage_option3_tooltip']['luggage_option3_tooltip_' . $language->id],
                    'luggage_option4' => $luggageSetting4->id,
                    'luggage_option4_tooltip' => $request['luggage_option4_tooltip']['luggage_option4_tooltip_' . $language->id],
                    'luggage_option5' => $luggageSetting5->id,
                    'luggage_option5_tooltip' => $request['luggage_option5_tooltip']['luggage_option5_tooltip_' . $language->id],
                    'luggage_option5_label' => $request['luggage_option5_label']['luggage_option5_label_' . $language->id],
                ]);
            } else {
                PostRidePageSettingDetail::whereLanguageId($language->id)->update([
                    'luggage_option1' => $luggageSetting1->id,
                    'luggage_option1_tooltip' => $request['luggage_option1_tooltip']['luggage_option1_tooltip_' . $language->id],
                    'luggage_option2' => $luggageSetting2->id,
                    'luggage_option2_tooltip' => $request['luggage_option2_tooltip']['luggage_option2_tooltip_' . $language->id],
                    'luggage_option3' => $luggageSetting3->id,
                    'luggage_option3_tooltip' => $request['luggage_option3_tooltip']['luggage_option3_tooltip_' . $language->id],
                    'luggage_option4' => $luggageSetting4->id,
                    'luggage_option4_tooltip' => $request['luggage_option4_tooltip']['luggage_option4_tooltip_' . $language->id],
                    'luggage_option5' => $luggageSetting5->id,
                    'luggage_option5_tooltip' => $request['luggage_option5_tooltip']['luggage_option5_tooltip_' . $language->id],
                    'luggage_option5_label' => $request['luggage_option5_label']['luggage_option5_label_' . $language->id],
                ]);
            }
        }

        $findRidePageSetting = FindRidePageSetting::first();
        if (!$findRidePageSetting) {
            $findRidePageSetting = FindRidePageSetting::create([]);
        }
        foreach ($languages as $language) {
            FindRidePageSettingDetail::whereLanguageId($language->id)->update([
                'luggage_option1' => $luggageSetting1->id,
                'luggage_option2' => $luggageSetting2->id,
                'luggage_option3' => $luggageSetting3->id,
                'luggage_option4' => $luggageSetting4->id,
                'luggage_option5' => $luggageSetting5->id,
                'luggage_option5_label' => $request['luggage_option5_label']['luggage_option5_label_' . $language->id],
            ]);
        }

        if ($postRidePageSetting && $findRidePageSetting) {
            return $this->successResponse([], "Luggage options setting updated successfully.");
        }

        return $this->errorResponse();
    }

    public function uploadExcel(Request $request)
    {
        try {
            $request->validate([
                'language_id' => 'required|exists:languages,id',
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            ]);

            $language = Language::find($request->language_id);
            if (!$language) return $this->errorResponse('Language not found', 404);

            try {
                Excel::import(new LuggageOptionsSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Luggage options settings for {$language->name} uploaded successfully from Excel.");
            } catch (ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors in Excel file',
                    'errors' => array_map(fn($f) => [
                        'row' => $f->row(),
                        'attribute' => $f->attribute(),
                        'errors' => $f->errors(),
                    ], $e->failures()),
                ], 422);
            }
        } catch (\Exception $e) {
            Log::error('Luggage Options Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new LuggageOptionsSettingTemplateExport($request->get('format', 'single_column')),
                'luggage_options_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Luggage Options template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
