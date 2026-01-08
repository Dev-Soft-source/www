<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\FeaturesSettingDetail;
use App\Models\Language;
use App\Models\PostRidePageSettingDetail;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class LuggageSettingController extends Controller
{
    use StatusResponser;

    public function index(Request $request){
        $luggageLabels = [];
        if ($request->lang && $request->lang != 0) {
            $luggageOptions = PostRidePageSettingDetail::select('post_ride_page_setting_detail.luggage_option1', 'post_ride_page_setting_detail.luggage_option2', 'post_ride_page_setting_detail.luggage_option3', 'post_ride_page_setting_detail.luggage_option4', 'post_ride_page_setting_detail.luggage_option5')
                ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
                ->where('languages.id', $request->lang)
                ->first();

            if ($luggageOptions->luggage_option1) {
                $name1 = FeaturesSettingDetail::whereFeaturesSettingId($luggageOptions->luggage_option1)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name1) {
                    $luggageLabels[] = $name1 ?? null;
                }
            } else {
                $luggageLabels[] = null; // Add `null` if booking_option1 is null
            }

            if ($luggageOptions->luggage_option2) {
                $name1 = FeaturesSettingDetail::whereFeaturesSettingId($luggageOptions->luggage_option2)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name1) {
                    $luggageLabels[] = $name1 ?? null;
                }
            } else {
                $luggageLabels[] = null; // Add `null` if booking_option1 is null
            }

            if ($luggageOptions->luggage_option3) {
                $name1 = FeaturesSettingDetail::whereFeaturesSettingId($luggageOptions->luggage_option3)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name1) {
                    $luggageLabels[] = $name1 ?? null;
                }
            } else {
                $luggageLabels[] = null; // Add `null` if booking_option1 is null
            }

            if ($luggageOptions->luggage_option4) {
                $name1 = FeaturesSettingDetail::whereFeaturesSettingId($luggageOptions->luggage_option4)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name1) {
                    $luggageLabels[] = $name1 ?? null;
                }
            } else {
                $luggageLabels[] = null; // Add `null` if booking_option1 is null
            }

            if ($luggageOptions->luggage_option5) {
                $name1 = FeaturesSettingDetail::whereFeaturesSettingId($luggageOptions->luggage_option5)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name1) {
                    $luggageLabels[] = $name1 ?? null;
                }
            } else {
                $luggageLabels[] = null; // Add `null` if booking_option1 is null
            }

            $luggageTooltips = PostRidePageSettingDetail::select('post_ride_page_setting_detail.luggage_option1_tooltip', 'post_ride_page_setting_detail.luggage_option2_tooltip', 'post_ride_page_setting_detail.luggage_option3_tooltip', 'post_ride_page_setting_detail.luggage_option4_tooltip', 'post_ride_page_setting_detail.luggage_option5_tooltip')
                ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
                ->where('languages.id', $request->lang)
                ->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $luggageOptions = PostRidePageSettingDetail::select('post_ride_page_setting_detail.luggage_option1', 'post_ride_page_setting_detail.luggage_option2', 'post_ride_page_setting_detail.luggage_option3', 'post_ride_page_setting_detail.luggage_option4', 'post_ride_page_setting_detail.luggage_option5')
                    ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
                    ->where('languages.id', $selectedLanguage->id)
                    ->first();

                if ($luggageOptions->luggage_option1) {
                    $name1 = FeaturesSettingDetail::whereFeaturesSettingId($luggageOptions->luggage_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name1) {
                        $luggageLabels[] = $name1 ?? null;
                    }
                } else {
                    $luggageLabels[] = null; // Add `null` if booking_option1 is null
                }
        
                if ($luggageOptions->luggage_option2) {
                    $name1 = FeaturesSettingDetail::whereFeaturesSettingId($luggageOptions->luggage_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name1) {
                        $luggageLabels[] = $name1 ?? null;
                    }
                } else {
                    $luggageLabels[] = null; // Add `null` if booking_option1 is null
                }
        
                if ($luggageOptions->luggage_option3) {
                    $name1 = FeaturesSettingDetail::whereFeaturesSettingId($luggageOptions->luggage_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name1) {
                        $luggageLabels[] = $name1 ?? null;
                    }
                } else {
                    $luggageLabels[] = null; // Add `null` if booking_option1 is null
                }
        
                if ($luggageOptions->luggage_option4) {
                    $name1 = FeaturesSettingDetail::whereFeaturesSettingId($luggageOptions->luggage_option4)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name1) {
                        $luggageLabels[] = $name1 ?? null;
                    }
                } else {
                    $luggageLabels[] = null; // Add `null` if booking_option1 is null
                }
        
                if ($luggageOptions->luggage_option5) {
                    $name1 = FeaturesSettingDetail::whereFeaturesSettingId($luggageOptions->luggage_option5)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name1) {
                        $luggageLabels[] = $name1 ?? null;
                    }
                } else {
                    $luggageLabels[] = null; // Add `null` if booking_option1 is null
                }

                $luggageTooltips = PostRidePageSettingDetail::select('post_ride_page_setting_detail.luggage_option1_tooltip', 'post_ride_page_setting_detail.luggage_option2_tooltip', 'post_ride_page_setting_detail.luggage_option3_tooltip', 'post_ride_page_setting_detail.luggage_option4_tooltip', 'post_ride_page_setting_detail.luggage_option5_tooltip')
                    ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
                    ->where('languages.id', $selectedLanguage->id)
                    ->first();
            }
        }

        if ($luggageOptions && $luggageTooltips) {
            $data = ['luggageOptions' => array_values($luggageOptions->toArray()), 'luggageLabels' => $luggageLabels, 'luggageTooltips' => array_values($luggageTooltips->toArray())];
        } else {
            $data = ['luggageOptions' => $luggageOptions, 'luggageLabels' => $luggageLabels, 'luggageTooltips' => $luggageTooltips];
        }
        
        return $this->successResponse($data, 'Get luggage options successfully');
    }
}
