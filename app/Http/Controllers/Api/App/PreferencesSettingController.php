<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\FeaturesSettingDetail;
use App\Models\Language;
use App\Models\PostRidePageSettingDetail;
use App\Models\ThankyouPageSettingDetail;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class PreferencesSettingController extends Controller
{
    use StatusResponser;

    public function preferencesOptions(Request $request){
        $preferencesOptions = PostRidePageSettingDetail::select('post_ride_page_setting_detail.smoking_option1', 'post_ride_page_setting_detail.smoking_option2', 'post_ride_page_setting_detail.animals_option1', 'post_ride_page_setting_detail.animals_option2', 'post_ride_page_setting_detail.animals_option3')
            ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id');

        if($request->lang && $request->lang != 0){
            $preferencesOptions = $preferencesOptions->where('languages.id', $request->lang)->first();
            if ($preferencesOptions->smoking_option1) {
                $name1 = FeaturesSettingDetail::whereFeaturesSettingId($preferencesOptions->smoking_option1)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name1) {
                    $preferencesOptions->smoking_option1_label = $name1 ?? null;
                }
            } else {
                $preferencesOptions->smoking_option1_label = null; // Add `null` if smoking_option1 is null
            }

            if ($preferencesOptions->smoking_option2) {
                $name2 = FeaturesSettingDetail::whereFeaturesSettingId($preferencesOptions->smoking_option2)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name2) {
                    $preferencesOptions->smoking_option2_label = $name2 ?? null;
                }
            } else {
                $preferencesOptions->smoking_option2_label = null; // Add `null` if smoking_option2 is null
            }

            if ($preferencesOptions->animals_option1) {
                $name2 = FeaturesSettingDetail::whereFeaturesSettingId($preferencesOptions->animals_option1)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name2) {
                    $preferencesOptions->animals_option1_label = $name2 ?? null;
                }
            } else {
                $preferencesOptions->animals_option1_label = null; // Add `null` if animals_option1 is null
            }

            if ($preferencesOptions->animals_option2) {
                $name2 = FeaturesSettingDetail::whereFeaturesSettingId($preferencesOptions->animals_option2)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name2) {
                    $preferencesOptions->animals_option2_label = $name2 ?? null;
                }
            } else {
                $preferencesOptions->animals_option2_label = null; // Add `null` if animals_option2 is null
            }

            if ($preferencesOptions->animals_option3) {
                $name2 = FeaturesSettingDetail::whereFeaturesSettingId($preferencesOptions->animals_option3)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name2) {
                    $preferencesOptions->animals_option3_label = $name2 ?? null;
                }
            } else {
                $preferencesOptions->animals_option3_label = null; // Add `null` if animals_option3 is null
            }
        }else{
            $selectedLanguage = Language::where('is_default', 1)->first();
            $preferencesOptions = $preferencesOptions->where('languages.id', $selectedLanguage->id)->first();
            if ($preferencesOptions->smoking_option1) {
                $name1 = FeaturesSettingDetail::whereFeaturesSettingId($preferencesOptions->smoking_option1)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');
                if ($name1) {
                    $preferencesOptions->smoking_option1_label = $name1 ?? null;
                }
            } else {
                $preferencesOptions->smoking_option1_label = null; // Add `null` if smoking_option1 is null
            }

            if ($preferencesOptions->smoking_option2) {
                $name2 = FeaturesSettingDetail::whereFeaturesSettingId($preferencesOptions->smoking_option2)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');
                if ($name2) {
                    $preferencesOptions->smoking_option2_label = $name2 ?? null;
                }
            } else {
                $preferencesOptions->smoking_option2_label = null; // Add `null` if smoking_option2 is null
            }

            if ($preferencesOptions->animals_option1) {
                $name2 = FeaturesSettingDetail::whereFeaturesSettingId($preferencesOptions->animals_option1)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');
                if ($name2) {
                    $preferencesOptions->animals_option1_label = $name2 ?? null;
                }
            } else {
                $preferencesOptions->animals_option1_label = null; // Add `null` if animals_option1 is null
            }

            if ($preferencesOptions->animals_option2) {
                $name2 = FeaturesSettingDetail::whereFeaturesSettingId($preferencesOptions->animals_option2)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');
                if ($name2) {
                    $preferencesOptions->animals_option2_label = $name2 ?? null;
                }
            } else {
                $preferencesOptions->animals_option2_label = null; // Add `null` if animals_option2 is null
            }

            if ($preferencesOptions->animals_option3) {
                $name2 = FeaturesSettingDetail::whereFeaturesSettingId($preferencesOptions->animals_option3)
                    ->whereLanguageId($selectedLanguage->id)
                    ->value('name');
                if ($name2) {
                    $preferencesOptions->animals_option3_label = $name2 ?? null;
                }
            } else {
                $preferencesOptions->animals_option3_label = null; // Add `null` if animals_option3 is null
            }
        }

        $data = ['preferencesOptions' => $preferencesOptions];
        return $this->successResponse($data, 'Get preferences options successfully');
    }

    public function cancellationOptions(Request $request){
        $cancellationLabels = [];
        if ($request->lang && $request->lang != 0) {
            $cancellationOptions = PostRidePageSettingDetail::select('post_ride_page_setting_detail.cancellation_policy_label1', 'post_ride_page_setting_detail.cancellation_policy_label2')
            ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
            ->where('languages.id', $request->lang)
            ->first();

            if ($cancellationOptions->cancellation_policy_label1) {
                $name1 = FeaturesSettingDetail::whereFeaturesSettingId($cancellationOptions->cancellation_policy_label1)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name1) {
                    $cancellationLabels[] = $name1 ?? null;
                }
            } else {
                $cancellationLabels[] = null; // Add `null` if booking_option1 is null
            }
            
            if ($cancellationOptions->cancellation_policy_label2) {
                $name2 = FeaturesSettingDetail::whereFeaturesSettingId($cancellationOptions->cancellation_policy_label2)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name2) {
                    $cancellationLabels[] = $name2 ?? null;
                }
            } else {
                $cancellationLabels[] = null; // Add `null` if booking_option1 is null
            }

            $cancellationTooltips = PostRidePageSettingDetail::select('post_ride_page_setting_detail.cancellation_policy_label1_tooltip', 'post_ride_page_setting_detail.cancellation_policy_label2_tooltip')
                ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
                ->where('languages.id', $request->lang)
                ->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $cancellationOptions = PostRidePageSettingDetail::select('post_ride_page_setting_detail.cancellation_policy_label1', 'post_ride_page_setting_detail.cancellation_policy_label2')
                    ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
                    ->where('languages.id', $selectedLanguage->id)
                    ->first();

                if ($cancellationOptions->cancellation_policy_label1) {
                    $name1 = FeaturesSettingDetail::whereFeaturesSettingId($cancellationOptions->cancellation_policy_label1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name1) {
                        $cancellationLabels[] = $name1 ?? null;
                    }
                } else {
                    $cancellationLabels[] = null; // Add `null` if booking_option1 is null
                }
                
                if ($cancellationOptions->cancellation_policy_label2) {
                    $name2 = FeaturesSettingDetail::whereFeaturesSettingId($cancellationOptions->cancellation_policy_label2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name2) {
                        $cancellationLabels[] = $name2 ?? null;
                    }
                } else {
                    $cancellationLabels[] = null; // Add `null` if booking_option1 is null
                }

                $cancellationTooltips = PostRidePageSettingDetail::select('post_ride_page_setting_detail.cancellation_policy_label1_tooltip', 'post_ride_page_setting_detail.cancellation_policy_label2_tooltip')
                    ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
                    ->where('languages.id', $selectedLanguage->id)
                    ->first();
            }
        }

        if ($cancellationOptions && $cancellationTooltips) {
            $data = ['cancellationOptions' => array_values($cancellationOptions->toArray()), 'cancellationLabels' => $cancellationLabels, 'cancellationTooltips' => array_values($cancellationTooltips->toArray())];
        } else {
            $data = ['cancellationOptions' => $cancellationOptions, 'cancellationLabels' => $cancellationLabels, 'cancellationTooltips' => $cancellationTooltips];
        }
        
        return $this->successResponse($data, 'Get cancellation options successfully');
    }

    public function thankyouIndex(Request $request)
    {
        $thankYouPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            // Retrieve the thankyouPageSettingDetail associated with the selected language
            $thankYouPage = ThankyouPageSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $thankYouPage = ThankyouPageSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['thankYouPage' => $thankYouPage];
        return $this->successResponse($data, 'Thankyou page get successfully');
    }
}
