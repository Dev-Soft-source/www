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
use App\Imports\FeaturesSettingImport;
use App\Exports\FeaturesSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class FeaturesSettingController extends Controller
{
    use StatusResponser;

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['features_option1.features_option1_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['features_option1.features_option1_' . $language->id . '.required' => 'Feature option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['features_option2.features_option2_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['features_option2.features_option2_' . $language->id . '.required' => 'Feature option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['features_option3.features_option3_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['features_option3.features_option3_' . $language->id . '.required' => 'Feature option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_features_option4.driver_features_option4_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_features_option4.driver_features_option4_' . $language->id . '.required' => 'Feature option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_features_option4.passenger_features_option4_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_features_option4.passenger_features_option4_' . $language->id . '.required' => 'Feature option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_features_option5.driver_features_option5_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_features_option5.driver_features_option5_' . $language->id . '.required' => 'Feature option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_features_option5.passenger_features_option5_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_features_option5.passenger_features_option5_' . $language->id . '.required' => 'Feature option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_features_option6.driver_features_option6_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_features_option6.driver_features_option6_' . $language->id . '.required' => 'Feature option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_features_option6.passenger_features_option6_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_features_option6.passenger_features_option6_' . $language->id . '.required' => 'Feature option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['driver_features_option7.driver_features_option7_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['driver_features_option7.driver_features_option7_' . $language->id . '.required' => 'Feature option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['passenger_features_option7.passenger_features_option7_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['passenger_features_option7.passenger_features_option7_' . $language->id . '.required' => 'Feature option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['features_option8.features_option8_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['features_option8.features_option8_' . $language->id . '.required' => 'Feature option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['features_option9.features_option9_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['features_option9.features_option9_' . $language->id . '.required' => 'Feature option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['features_option10.features_option10_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['features_option10.features_option10_' . $language->id . '.required' => 'Feature option in ' . $language->name . ' is required.']);
            }
        }

        $this->validate(
            $request,
            $validationRule,
            $errorMessages,
        );

        $featuresSetting1 = FeaturesSetting::whereSlug('pink_rides')->first();
        if (!$featuresSetting1) {
            $featuresSetting1 = FeaturesSetting::create([
                'slug' => 'pink_rides',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption1 = FeaturesSettingDetail::whereFeaturesSettingId($featuresSetting1->id)->whereLanguageId($language->id)->first();
            if ($featureOption1) {
                $featureOption1->update([
                    'name' => $request['features_option1']['features_option1_' . $language->id],
                    'icon' => $request['features_option1_icon']['features_option1_icon_' . $language->id],
                ]);
            } else {
                $featureOption1 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $featuresSetting1->id,
                    'name' => $request['features_option1']['features_option1_' . $language->id],
                    'icon' => $request['features_option1_icon']['features_option1_icon_' . $language->id],
                ]);
            }
        }

        $featuresSetting2 = FeaturesSetting::whereSlug('extra_care_rides')->first();
        if (!$featuresSetting2) {
            $featuresSetting2 = FeaturesSetting::create([
                'slug' => 'extra_care_rides',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption2 = FeaturesSettingDetail::whereFeaturesSettingId($featuresSetting2->id)->whereLanguageId($language->id)->first();
            if ($featureOption2) {
                $featureOption2->update([
                    'name' => $request['features_option2']['features_option2_' . $language->id],
                    'icon' => $request['features_option2_icon']['features_option2_icon_' . $language->id],
                ]);
            } else {
                $featureOption2 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $featuresSetting2->id,
                    'name' => $request['features_option2']['features_option2_' . $language->id],
                    'icon' => $request['features_option2_icon']['features_option2_icon_' . $language->id],
                ]);
            }
        }

        $featuresSetting3 = FeaturesSetting::whereSlug('wi_fi')->first();
        if (!$featuresSetting3) {
            $featuresSetting3 = FeaturesSetting::create([
                'slug' => 'wi_fi',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption3 = FeaturesSettingDetail::whereFeaturesSettingId($featuresSetting3->id)->whereLanguageId($language->id)->first();
            if ($featureOption3) {
                $featureOption3->update([
                    'name' => $request['features_option3']['features_option3_' . $language->id],
                    'icon' => $request['features_option3_icon']['features_option3_icon_' . $language->id],
                ]);
            } else {
                $featureOption3 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $featuresSetting3->id,
                    'name' => $request['features_option3']['features_option3_' . $language->id],
                    'icon' => $request['features_option3_icon']['features_option3_icon_' . $language->id],
                ]);
            }
        }

        $featuresSetting4 = FeaturesSetting::whereSlug('driver_features_option4')->first();
        if (!$featuresSetting4) {
            $featuresSetting4 = FeaturesSetting::create([
                'slug' => 'driver_features_option4',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption4 = FeaturesSettingDetail::whereFeaturesSettingId($featuresSetting4->id)->whereLanguageId($language->id)->first();
            if ($featureOption4) {
                $featureOption4->update([
                    'name' => $request['driver_features_option4']['driver_features_option4_' . $language->id],
                    'icon' => $request['driver_features_option4_icon']['driver_features_option4_icon_' . $language->id],
                ]);
            } else {
                $featureOption4 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $featuresSetting4->id,
                    'name' => $request['driver_features_option4']['driver_features_option4_' . $language->id],
                    'icon' => $request['driver_features_option4_icon']['driver_features_option4_icon_' . $language->id],
                ]);
            }
        }

        $featuresSetting5 = FeaturesSetting::whereSlug('driver_features_option5')->first();
        if (!$featuresSetting5) {
            $featuresSetting5 = FeaturesSetting::create([
                'slug' => 'driver_features_option5',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption5 = FeaturesSettingDetail::whereFeaturesSettingId($featuresSetting5->id)->whereLanguageId($language->id)->first();
            if ($featureOption5) {
                $featureOption5->update([
                    'name' => $request['driver_features_option5']['driver_features_option5_' . $language->id],
                    'icon' => $request['driver_features_option5_icon']['driver_features_option5_icon_' . $language->id],
                ]);
            } else {
                $featureOption5 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $featuresSetting5->id,
                    'name' => $request['driver_features_option5']['driver_features_option5_' . $language->id],
                    'icon' => $request['driver_features_option5_icon']['driver_features_option5_icon_' . $language->id],
                ]);
            }
        }

        $featuresSetting6 = FeaturesSetting::whereSlug('driver_features_option6')->first();
        if (!$featuresSetting6) {
            $featuresSetting6 = FeaturesSetting::create([
                'slug' => 'driver_features_option6',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption6 = FeaturesSettingDetail::whereFeaturesSettingId($featuresSetting6->id)->whereLanguageId($language->id)->first();
            if ($featureOption6) {
                $featureOption6->update([
                    'name' => $request['driver_features_option6']['driver_features_option6_' . $language->id],
                    'icon' => $request['driver_features_option6_icon']['driver_features_option6_icon_' . $language->id],
                ]);
            } else {
                $featureOption6 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $featuresSetting6->id,
                    'name' => $request['driver_features_option6']['driver_features_option6_' . $language->id],
                    'icon' => $request['driver_features_option6_icon']['driver_features_option6_icon_' . $language->id],
                ]);
            }
        }

        $featuresSetting7 = FeaturesSetting::whereSlug('driver_features_option7')->first();
        if (!$featuresSetting7) {
            $featuresSetting7 = FeaturesSetting::create([
                'slug' => 'driver_features_option7',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption7 = FeaturesSettingDetail::whereFeaturesSettingId($featuresSetting7->id)->whereLanguageId($language->id)->first();
            if ($featureOption7) {
                $featureOption7->update([
                    'name' => $request['driver_features_option7']['driver_features_option7_' . $language->id],
                    'icon' => $request['driver_features_option7_icon']['driver_features_option7_icon_' . $language->id],
                ]);
            } else {
                $featureOption7 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $featuresSetting7->id,
                    'name' => $request['driver_features_option7']['driver_features_option7_' . $language->id],
                    'icon' => $request['driver_features_option7_icon']['driver_features_option7_icon_' . $language->id],
                ]);
            }
        }

        $featuresSetting8 = FeaturesSetting::whereSlug('heating')->first();
        if (!$featuresSetting8) {
            $featuresSetting8 = FeaturesSetting::create([
                'slug' => 'heating',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption8 = FeaturesSettingDetail::whereFeaturesSettingId($featuresSetting8->id)->whereLanguageId($language->id)->first();
            if ($featureOption8) {
                $featureOption8->update([
                    'name' => $request['features_option8']['features_option8_' . $language->id],
                    'icon' => $request['features_option8_icon']['features_option8_icon_' . $language->id],
                ]);
            } else {
                $featureOption8 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $featuresSetting8->id,
                    'name' => $request['features_option8']['features_option8_' . $language->id],
                    'icon' => $request['features_option8_icon']['features_option8_icon_' . $language->id],
                ]);
            }
        }

        $featuresSetting9 = FeaturesSetting::whereSlug('ac')->first();
        if (!$featuresSetting9) {
            $featuresSetting9 = FeaturesSetting::create([
                'slug' => 'ac',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption9 = FeaturesSettingDetail::whereFeaturesSettingId($featuresSetting9->id)->whereLanguageId($language->id)->first();
            if ($featureOption9) {
                $featureOption9->update([
                    'name' => $request['features_option9']['features_option9_' . $language->id],
                    'icon' => $request['features_option9_icon']['features_option9_icon_' . $language->id],
                ]);
            } else {
                $featureOption9 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $featuresSetting9->id,
                    'name' => $request['features_option9']['features_option9_' . $language->id],
                    'icon' => $request['features_option9_icon']['features_option9_icon_' . $language->id],
                ]);
            }
        }

        $featuresSetting10 = FeaturesSetting::whereSlug('bike_rack')->first();
        if (!$featuresSetting10) {
            $featuresSetting10 = FeaturesSetting::create([
                'slug' => 'bike_rack',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption10 = FeaturesSettingDetail::whereFeaturesSettingId($featuresSetting10->id)->whereLanguageId($language->id)->first();
            if ($featureOption10) {
                $featureOption10->update([
                    'name' => $request['features_option10']['features_option10_' . $language->id],
                    'icon' => $request['features_option10_icon']['features_option10_icon_' . $language->id],
                ]);
            } else {
                $featureOption10 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $featuresSetting10->id,
                    'name' => $request['features_option10']['features_option10_' . $language->id],
                    'icon' => $request['features_option10_icon']['features_option10_icon_' . $language->id],
                ]);
            }
        }

        $featuresSetting11 = FeaturesSetting::whereSlug('ski_rack')->first();
        if (!$featuresSetting11) {
            $featuresSetting11 = FeaturesSetting::create([
                'slug' => 'ski_rack',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption11 = FeaturesSettingDetail::whereFeaturesSettingId($featuresSetting11->id)->whereLanguageId($language->id)->first();
            if ($featureOption11) {
                $featureOption11->update([
                    'name' => $request['features_option11']['features_option11_' . $language->id],
                    'icon' => $request['features_option11_icon']['features_option11_icon_' . $language->id],
                ]);
            } else {
                $featureOption11 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $featuresSetting11->id,
                    'name' => $request['features_option11']['features_option11_' . $language->id],
                    'icon' => $request['features_option11_icon']['features_option11_icon_' . $language->id],
                ]);
            }
        }

        $featuresSetting12 = FeaturesSetting::whereSlug('winter_tires')->first();
        if (!$featuresSetting12) {
            $featuresSetting12 = FeaturesSetting::create([
                'slug' => 'winter_tires',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption12 = FeaturesSettingDetail::whereFeaturesSettingId($featuresSetting12->id)->whereLanguageId($language->id)->first();
            if ($featureOption12) {
                $featureOption12->update([
                    'name' => $request['features_option12']['features_option12_' . $language->id],
                    'icon' => $request['features_option12_icon']['features_option12_icon_' . $language->id],
                ]);
            } else {
                $featureOption12 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $featuresSetting12->id,
                    'name' => $request['features_option12']['features_option12_' . $language->id],
                    'icon' => $request['features_option12_icon']['features_option12_icon_' . $language->id],
                ]);
            }
        }

        $featuresSetting13 = FeaturesSetting::whereSlug('star5_passenger')->first();
        if (!$featuresSetting13) {
            $featuresSetting13 = FeaturesSetting::create([
                'slug' => 'star5_passenger',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption13 = FeaturesSettingDetail::whereFeaturesSettingId($featuresSetting13->id)->whereLanguageId($language->id)->first();
            if ($featureOption13) {
                $featureOption13->update([
                    'name' => $request['features_option13']['features_option13_' . $language->id],
                    'icon' => $request['features_option13_icon']['features_option13_icon_' . $language->id],
                ]);
            } else {
                $featureOption13 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $featuresSetting13->id,
                    'name' => $request['features_option13']['features_option13_' . $language->id],
                    'icon' => $request['features_option13_icon']['features_option13_icon_' . $language->id],
                ]);
            }
        }

        $featuresSetting14 = FeaturesSetting::whereSlug('star4_passenger')->first();
        if (!$featuresSetting14) {
            $featuresSetting14 = FeaturesSetting::create([
                'slug' => 'star4_passenger',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption14 = FeaturesSettingDetail::whereFeaturesSettingId($featuresSetting14->id)->whereLanguageId($language->id)->first();
            if ($featureOption14) {
                $featureOption14->update([
                    'name' => $request['features_option14']['features_option14_' . $language->id],
                    'icon' => $request['features_option14_icon']['features_option14_icon_' . $language->id],
                ]);
            } else {
                $featureOption14 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $featuresSetting14->id,
                    'name' => $request['features_option14']['features_option14_' . $language->id],
                    'icon' => $request['features_option14_icon']['features_option14_icon_' . $language->id],
                ]);
            }
        }

        $featuresSetting15 = FeaturesSetting::whereSlug('star3_passenger')->first();
        if (!$featuresSetting15) {
            $featuresSetting15 = FeaturesSetting::create([
                'slug' => 'star3_passenger',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption15 = FeaturesSettingDetail::whereFeaturesSettingId($featuresSetting15->id)->whereLanguageId($language->id)->first();
            if ($featureOption15) {
                $featureOption15->update([
                    'name' => $request['features_option15']['features_option15_' . $language->id],
                    'icon' => $request['features_option15_icon']['features_option15_icon_' . $language->id],
                ]);
            } else {
                $featureOption15 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $featuresSetting15->id,
                    'name' => $request['features_option15']['features_option15_' . $language->id],
                    'icon' => $request['features_option15_icon']['features_option15_icon_' . $language->id],
                ]);
            }
        }

        $featuresSetting16 = FeaturesSetting::whereSlug('with_review_passenger')->first();
        if (!$featuresSetting16) {
            $featuresSetting16 = FeaturesSetting::create([
                'slug' => 'with_review_passenger',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption16 = FeaturesSettingDetail::whereFeaturesSettingId($featuresSetting16->id)->whereLanguageId($language->id)->first();
            if ($featureOption16) {
                $featureOption16->update([
                    'name' => $request['features_option16']['features_option16_' . $language->id],
                    'icon' => $request['features_option16_icon']['features_option16_icon_' . $language->id],
                ]);
            } else {
                $featureOption16 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $featuresSetting16->id,
                    'name' => $request['features_option16']['features_option16_' . $language->id],
                    'icon' => $request['features_option16_icon']['features_option16_icon_' . $language->id],
                ]);
            }
        }

        $PassengerFeaturesSetting4 = FeaturesSetting::whereSlug('passenger_features_option4')->first();
        if (!$PassengerFeaturesSetting4) {
            $PassengerFeaturesSetting4 = FeaturesSetting::create([
                'slug' => 'passenger_features_option4',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption16 = FeaturesSettingDetail::whereFeaturesSettingId($PassengerFeaturesSetting4->id)->whereLanguageId($language->id)->first();
            if ($featureOption16) {
                $featureOption16->update([
                    'name' => $request['passenger_features_option4']['passenger_features_option4_' . $language->id],
                ]);
            } else {
                $featureOption16 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $PassengerFeaturesSetting4->id,
                    'name' => $request['passenger_features_option4']['passenger_features_option4_' . $language->id],
                ]);
            }
        }

        $PassengerFeaturesSetting5 = FeaturesSetting::whereSlug('passenger_features_option5')->first();
        if (!$PassengerFeaturesSetting5) {
            $PassengerFeaturesSetting5 = FeaturesSetting::create([
                'slug' => 'passenger_features_option5',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption16 = FeaturesSettingDetail::whereFeaturesSettingId($PassengerFeaturesSetting5->id)->whereLanguageId($language->id)->first();
            if ($featureOption16) {
                $featureOption16->update([
                    'name' => $request['passenger_features_option5']['passenger_features_option5_' . $language->id],
                ]);
            } else {
                $featureOption16 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $PassengerFeaturesSetting5->id,
                    'name' => $request['passenger_features_option5']['passenger_features_option5_' . $language->id],
                ]);
            }
        }

        $PassengerFeaturesSetting6 = FeaturesSetting::whereSlug('passenger_features_option6')->first();
        if (!$PassengerFeaturesSetting6) {
            $PassengerFeaturesSetting6 = FeaturesSetting::create([
                'slug' => 'passenger_features_option6',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption16 = FeaturesSettingDetail::whereFeaturesSettingId($PassengerFeaturesSetting6->id)->whereLanguageId($language->id)->first();
            if ($featureOption16) {
                $featureOption16->update([
                    'name' => $request['passenger_features_option6']['passenger_features_option6_' . $language->id],
                ]);
            } else {
                $featureOption16 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $PassengerFeaturesSetting6->id,
                    'name' => $request['passenger_features_option6']['passenger_features_option6_' . $language->id],
                ]);
            }
        }

        $PassengerFeaturesSetting7 = FeaturesSetting::whereSlug('passenger_features_option7')->first();
        if (!$PassengerFeaturesSetting7) {
            $PassengerFeaturesSetting7 = FeaturesSetting::create([
                'slug' => 'passenger_features_option7',
            ]);
        }
        foreach ($languages as $language) {
            $featureOption16 = FeaturesSettingDetail::whereFeaturesSettingId($PassengerFeaturesSetting7->id)->whereLanguageId($language->id)->first();
            if ($featureOption16) {
                $featureOption16->update([
                    'name' => $request['passenger_features_option7']['passenger_features_option7_' . $language->id],
                ]);
            } else {
                $featureOption16 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $PassengerFeaturesSetting7->id,
                    'name' => $request['passenger_features_option7']['passenger_features_option7_' . $language->id],
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
                    'features_option1' => $featuresSetting1->id,
                    'features_option1_tooltip' => $request['features_option1_tooltip']['features_option1_tooltip_' . $language->id],
                    'features_option2' => $featuresSetting2->id,
                    'features_option2_tooltip' => $request['features_option2_tooltip']['features_option2_tooltip_' . $language->id],
                    'features_option3' => $featuresSetting3->id,
                    'features_option3_tooltip' => $request['features_option3_tooltip']['features_option3_tooltip_' . $language->id],
                    'features_option4' => $featuresSetting4->id,
                    'features_option4_tooltip' => $request['driver_features_option4_tooltip']['driver_features_option4_tooltip_' . $language->id],
                    'features_option5' => $featuresSetting5->id,
                    'features_option5_tooltip' => $request['driver_features_option5_tooltip']['driver_features_option5_tooltip_' . $language->id],
                    'features_option6' => $featuresSetting6->id,
                    'features_option6_tooltip' => $request['driver_features_option6_tooltip']['driver_features_option6_tooltip_' . $language->id],
                    'features_option7' => $featuresSetting7->id,
                    'features_option7_tooltip' => $request['driver_features_option7_tooltip']['driver_features_option7_tooltip_' . $language->id],
                    'features_option8' => $featuresSetting8->id,
                    'features_option8_tooltip' => $request['features_option8_tooltip']['features_option8_tooltip_' . $language->id],
                    'features_option9' => $featuresSetting9->id,
                    'features_option9_tooltip' => $request['features_option9_tooltip']['features_option9_tooltip_' . $language->id],
                    'features_option10' => $featuresSetting10->id,
                    'features_option10_tooltip' => $request['features_option10_tooltip']['features_option10_tooltip_' . $language->id],
                    'features_option11' => $featuresSetting11->id,
                    'features_option11_tooltip' => $request['features_option11_tooltip']['features_option11_tooltip_' . $language->id],
                    'features_option12' => $featuresSetting12->id,
                    'features_option12_tooltip' => $request['features_option12_tooltip']['features_option12_tooltip_' . $language->id],
                    'features_option13' => $featuresSetting13->id,
                    'features_option13_tooltip' => $request['features_option13_tooltip']['features_option13_tooltip_' . $language->id],
                    'features_option14' => $featuresSetting14->id,
                    'features_option14_tooltip' => $request['features_option14_tooltip']['features_option14_tooltip_' . $language->id],
                    'features_option15' => $featuresSetting15->id,
                    'features_option15_tooltip' => $request['features_option15_tooltip']['features_option15_tooltip_' . $language->id],
                    'features_option16' => $featuresSetting16->id,
                    'features_option16_tooltip' => $request['features_option16_tooltip']['features_option16_tooltip_' . $language->id],
                ]);
            } else {
                PostRidePageSettingDetail::whereLanguageId($language->id)->update([
                    'features_option1' => $featuresSetting1->id,
                    'features_option1_tooltip' => $request['features_option1_tooltip']['features_option1_tooltip_' . $language->id],
                    'features_option2' => $featuresSetting2->id,
                    'features_option2_tooltip' => $request['features_option2_tooltip']['features_option2_tooltip_' . $language->id],
                    'features_option3' => $featuresSetting3->id,
                    'features_option3_tooltip' => $request['features_option3_tooltip']['features_option3_tooltip_' . $language->id],
                    'features_option4' => $featuresSetting4->id,
                    'features_option4_tooltip' => $request['driver_features_option4_tooltip']['driver_features_option4_tooltip_' . $language->id],
                    'features_option5' => $featuresSetting5->id,
                    'features_option5_tooltip' => $request['driver_features_option5_tooltip']['driver_features_option5_tooltip_' . $language->id],
                    'features_option6' => $featuresSetting6->id,
                    'features_option6_tooltip' => $request['driver_features_option6_tooltip']['driver_features_option6_tooltip_' . $language->id],
                    'features_option7' => $featuresSetting7->id,
                    'features_option7_tooltip' => $request['driver_features_option7_tooltip']['driver_features_option7_tooltip_' . $language->id],
                    'features_option8' => $featuresSetting8->id,
                    'features_option8_tooltip' => $request['features_option8_tooltip']['features_option8_tooltip_' . $language->id],
                    'features_option9' => $featuresSetting9->id,
                    'features_option9_tooltip' => $request['features_option9_tooltip']['features_option9_tooltip_' . $language->id],
                    'features_option10' => $featuresSetting10->id,
                    'features_option10_tooltip' => $request['features_option10_tooltip']['features_option10_tooltip_' . $language->id],
                    'features_option11' => $featuresSetting11->id,
                    'features_option11_tooltip' => $request['features_option11_tooltip']['features_option11_tooltip_' . $language->id],
                    'features_option12' => $featuresSetting12->id,
                    'features_option12_tooltip' => $request['features_option12_tooltip']['features_option12_tooltip_' . $language->id],
                    'features_option13' => $featuresSetting13->id,
                    'features_option13_tooltip' => $request['features_option13_tooltip']['features_option13_tooltip_' . $language->id],
                    'features_option14' => $featuresSetting14->id,
                    'features_option14_tooltip' => $request['features_option14_tooltip']['features_option14_tooltip_' . $language->id],
                    'features_option15' => $featuresSetting15->id,
                    'features_option15_tooltip' => $request['features_option15_tooltip']['features_option15_tooltip_' . $language->id],
                    'features_option16' => $featuresSetting16->id,
                    'features_option16_tooltip' => $request['features_option16_tooltip']['features_option16_tooltip_' . $language->id],
                ]);
            }
        }

        $findRidePageSetting = FindRidePageSetting::first();
        if (!$findRidePageSetting) {
            $findRidePageSetting = FindRidePageSetting::create([]);
        }
        foreach ($languages as $language) {
            FindRidePageSettingDetail::whereLanguageId($language->id)->update([
                'ride_features_option1' => $featuresSetting1->id,
                'ride_features_option2' => $featuresSetting2->id,
                'ride_features_option3' => $featuresSetting3->id,
                'ride_features_option4' => $PassengerFeaturesSetting4->id,
                'ride_features_option5' => $PassengerFeaturesSetting5->id,
                'ride_features_option6' => $PassengerFeaturesSetting6->id,
                'ride_features_option7' => $PassengerFeaturesSetting7->id,
                'ride_features_option8' => $featuresSetting8->id,
                'ride_features_option9' => $featuresSetting9->id,
                'ride_features_option10' => $featuresSetting10->id,
                'ride_features_option11' => $featuresSetting11->id,
                'ride_features_option12' => $featuresSetting12->id,
                'ride_features_option13' => $featuresSetting13->id,
                'ride_features_option14' => $featuresSetting14->id,
                'ride_features_option15' => $featuresSetting15->id,
                'ride_features_option16' => $featuresSetting16->id,
            ]);
        }

        if ($postRidePageSetting && $findRidePageSetting) {
            return $this->successResponse([], "Features setting updated successfully.");
        }

        return $this->errorResponse();
    }

    public function show()
    {
        $features = FeaturesSetting::with(['featuresSettingDetail', 'featuresSettingDetail.language:id,name'])->get();
        return $this->successResponse($features, 'Data Get Successfully!');
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
                Excel::import(new FeaturesSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Features settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Features Setting Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new FeaturesSettingTemplateExport($request->get('format', 'single_column')),
                'features_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Features Setting template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
