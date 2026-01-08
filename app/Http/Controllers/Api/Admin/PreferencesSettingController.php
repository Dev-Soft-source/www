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

class PreferencesSettingController extends Controller
{
    use StatusResponser;

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['smoking_option1.smoking_option1_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['smoking_option1.smoking_option1_' . $language->id . '.required' => 'Smoking option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['smoking_option2.smoking_option2_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['smoking_option2.smoking_option2_' . $language->id . '.required' => 'Smoking option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['animals_option1.animals_option1_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['animals_option1.animals_option1_' . $language->id . '.required' => 'Animal option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['animals_option2.animals_option2_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['animals_option2.animals_option2_' . $language->id . '.required' => 'Animal option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['animals_option3.animals_option3_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['animals_option3.animals_option3_' . $language->id . '.required' => 'Animal option in ' . $language->name . ' is required.']);
            }
        }

        $this->validate(
            $request,
            $validationRule,
            $errorMessages,
        );

        $smokingSetting1 = FeaturesSetting::whereSlug('no_smoking')->first();
        if (!$smokingSetting1) {
            $smokingSetting1 = FeaturesSetting::create([
                'slug' => 'no_smoking',
            ]);
        }
        foreach ($languages as $language) {
            $smokingOption1 = FeaturesSettingDetail::whereFeaturesSettingId($smokingSetting1->id)->whereLanguageId($language->id)->first();
            if ($smokingOption1) {
                $smokingOption1->update([
                    'name' => $request['smoking_option1']['smoking_option1_' . $language->id],
                    'icon' => $request['smoking_option1_icon']['smoking_option1_icon_' . $language->id],
                ]);
            } else {
                $smokingOption1 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $smokingSetting1->id,
                    'name' => $request['smoking_option1']['smoking_option1_' . $language->id],
                    'icon' => $request['smoking_option1_icon']['smoking_option1_icon_' . $language->id],
                ]);
            }
        }

        $smokingSetting2 = FeaturesSetting::whereSlug('indifferent_smoking')->first();
        if (!$smokingSetting2) {
            $smokingSetting2 = FeaturesSetting::create([
                'slug' => 'indifferent_smoking',
            ]);
        }
        foreach ($languages as $language) {
            $smokingOption2 = FeaturesSettingDetail::whereFeaturesSettingId($smokingSetting2->id)->whereLanguageId($language->id)->first();
            if ($smokingOption2) {
                $smokingOption2->update([
                    'name' => $request['smoking_option2']['smoking_option2_' . $language->id],
                    'icon' => $request['smoking_option2_icon']['smoking_option2_icon_' . $language->id],
                ]);
            } else {
                $smokingOption2 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $smokingSetting2->id,
                    'name' => $request['smoking_option2']['smoking_option2_' . $language->id],
                    'icon' => $request['smoking_option2_icon']['smoking_option2_icon_' . $language->id],
                ]);
            }
        }

        $animalsSetting1 = FeaturesSetting::whereSlug('no_animals')->first();
        if (!$animalsSetting1) {
            $animalsSetting1 = FeaturesSetting::create([
                'slug' => 'no_animals',
            ]);
        }
        foreach ($languages as $language) {
            $animalsOption1 = FeaturesSettingDetail::whereFeaturesSettingId($animalsSetting1->id)->whereLanguageId($language->id)->first();
            if ($animalsOption1) {
                $animalsOption1->update([
                    'name' => $request['animals_option1']['animals_option1_' . $language->id],
                    'icon' => $request['animals_option1_icon']['animals_option1_icon_' . $language->id],
                ]);
            } else {
                $animalsOption1 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $animalsSetting1->id,
                    'name' => $request['animals_option1']['animals_option1_' . $language->id],
                    'icon' => $request['animals_option1_icon']['animals_option1_icon_' . $language->id],
                ]);
            }
        }

        $animalsSetting2 = FeaturesSetting::whereSlug('yes_animals')->first();
        if (!$animalsSetting2) {
            $animalsSetting2 = FeaturesSetting::create([
                'slug' => 'yes_animals',
            ]);
        }
        foreach ($languages as $language) {
            $animalsOption2 = FeaturesSettingDetail::whereFeaturesSettingId($animalsSetting2->id)->whereLanguageId($language->id)->first();
            if ($animalsOption2) {
                $animalsOption2->update([
                    'name' => $request['animals_option2']['animals_option2_' . $language->id],
                    'icon' => $request['animals_option2_icon']['animals_option2_icon_' . $language->id],
                ]);
            } else {
                $animalsOption2 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $animalsSetting2->id,
                    'name' => $request['animals_option2']['animals_option2_' . $language->id],
                    'icon' => $request['animals_option2_icon']['animals_option2_icon_' . $language->id],
                ]);
            }
        }

        $animalsSetting3 = FeaturesSetting::whereSlug('caged_animals')->first();
        if (!$animalsSetting3) {
            $animalsSetting3 = FeaturesSetting::create([
                'slug' => 'caged_animals',
            ]);
        }
        foreach ($languages as $language) {
            $animalsOption3 = FeaturesSettingDetail::whereFeaturesSettingId($animalsSetting3->id)->whereLanguageId($language->id)->first();
            if ($animalsOption3) {
                $animalsOption3->update([
                    'name' => $request['animals_option3']['animals_option3_' . $language->id],
                    'icon' => $request['animals_option3_icon']['animals_option3_icon_' . $language->id],
                ]);
            } else {
                $animalsOption3 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $animalsSetting3->id,
                    'name' => $request['animals_option3']['animals_option3_' . $language->id],
                    'icon' => $request['animals_option3_icon']['animals_option3_icon_' . $language->id],
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
                    'smoking_option1' => $smokingSetting1->id,
                    'smoking_option1_tooltip' => $request['smoking_option1_tooltip']['smoking_option1_tooltip_' . $language->id],
                    'smoking_option2' => $smokingSetting2->id,
                    'smoking_option2_tooltip' => $request['smoking_option2_tooltip']['smoking_option2_tooltip_' . $language->id],
                    'animals_option1' => $animalsSetting1->id,
                    'animals_option1_tooltip' => $request['animals_option1_tooltip']['animals_option1_tooltip_' . $language->id],
                    'animals_option2' => $animalsSetting2->id,
                    'animals_option2_tooltip' => $request['animals_option2_tooltip']['animals_option2_tooltip_' . $language->id],
                    'animals_option3' => $animalsSetting3->id,
                    'animals_option3_tooltip' => $request['animals_option3_tooltip']['animals_option3_tooltip_' . $language->id],
                ]);
            } else {
                PostRidePageSettingDetail::whereLanguageId($language->id)->update([
                    'smoking_option1' => $smokingSetting1->id,
                    'smoking_option1_tooltip' => $request['smoking_option1_tooltip']['smoking_option1_tooltip_' . $language->id],
                    'smoking_option2' => $smokingSetting2->id,
                    'smoking_option2_tooltip' => $request['smoking_option2_tooltip']['smoking_option2_tooltip_' . $language->id],
                    'animals_option1' => $animalsSetting1->id,
                    'animals_option1_tooltip' => $request['animals_option1_tooltip']['animals_option1_tooltip_' . $language->id],
                    'animals_option2' => $animalsSetting2->id,
                    'animals_option2_tooltip' => $request['animals_option2_tooltip']['animals_option2_tooltip_' . $language->id],
                    'animals_option3' => $animalsSetting3->id,
                    'animals_option3_tooltip' => $request['animals_option3_tooltip']['animals_option3_tooltip_' . $language->id],
                ]);
            }
        }

        $findRidePageSetting = FindRidePageSetting::first();
        if (!$findRidePageSetting) {
            $findRidePageSetting = FindRidePageSetting::create([]);
        }
        foreach ($languages as $language) {
            FindRidePageSettingDetail::whereLanguageId($language->id)->update([
                'smoking_option1' => $smokingSetting1->id,
                'smoking_option2' => $smokingSetting2->id,
                'pets_allowed_option1' => $animalsSetting1->id,
                'pets_allowed_option2' => $animalsSetting2->id,
                'pets_allowed_option3' => $animalsSetting3->id,
            ]);
        }

        if ($postRidePageSetting && $findRidePageSetting) {
            return $this->successResponse([], "Preferences setting updated successfully.");
        }

        return $this->errorResponse();
    }
}
