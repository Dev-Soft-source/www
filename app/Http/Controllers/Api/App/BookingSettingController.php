<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\FeaturesSettingDetail;
use App\Models\Language;
use App\Models\PostRidePageSettingDetail;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class BookingSettingController extends Controller
{
    use StatusResponser;

    public function index(Request $request){
        $bookingLabels = [];
        if ($request->lang && $request->lang != 0) {
            $bookingOptions = PostRidePageSettingDetail::select('post_ride_page_setting_detail.booking_option1', 'post_ride_page_setting_detail.booking_option2')
                ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
                ->where('languages.id', $request->lang)
                ->first();

            if ($bookingOptions->booking_option1) {
                $name1 = FeaturesSettingDetail::whereFeaturesSettingId($bookingOptions->booking_option1)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name1) {
                    $bookingLabels[] = $name1 ?? null;
                }
            } else {
                $bookingLabels[] = null; // Add `null` if booking_option1 is null
            }
            
            if ($bookingOptions->booking_option2) {
                $name2 = FeaturesSettingDetail::whereFeaturesSettingId($bookingOptions->booking_option2)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name2) {
                    $bookingLabels[] = $name2 ?? null;
                }
            } else {
                $bookingLabels[] = null; // Add `null` if booking_option1 is null
            }

            $bookingTooltips = PostRidePageSettingDetail::select('post_ride_page_setting_detail.booking_option1_tooltip', 'post_ride_page_setting_detail.booking_option2_tooltip')
                ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
                ->where('languages.id', $request->lang)
                ->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $bookingOptions = PostRidePageSettingDetail::select('post_ride_page_setting_detail.booking_option1', 'post_ride_page_setting_detail.booking_option2')
                    ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
                    ->where('languages.id', $selectedLanguage->id)
                    ->first();

                if ($bookingOptions->booking_option1) {
                    $name1 = FeaturesSettingDetail::whereFeaturesSettingId($bookingOptions->booking_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name1) {
                        $bookingLabels[] = $name1 ?? null;
                    }
                } else {
                    $bookingLabels[] = null; // Add `null` if booking_option1 is null
                }
                
                if ($bookingOptions->booking_option2) {
                    $name2 = FeaturesSettingDetail::whereFeaturesSettingId($bookingOptions->booking_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name2) {
                        $bookingLabels[] = $name2 ?? null;
                    }
                } else {
                    $bookingLabels[] = null; // Add `null` if booking_option1 is null
                }

                $bookingTooltips = PostRidePageSettingDetail::select('post_ride_page_setting_detail.booking_option1_tooltip', 'post_ride_page_setting_detail.booking_option2_tooltip')
                    ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
                    ->where('languages.id', $selectedLanguage->id)
                    ->first();
            }
        }

        if ($bookingOptions && $bookingTooltips) {
            $data = ['bookingOptions' => array_values($bookingOptions->toArray()), 'bookingLabels' => $bookingLabels, 'bookingTooltips' => array_values($bookingTooltips->toArray())];
        } else {
            $data = ['bookingOptions' => $bookingOptions, 'bookingLabels' => $bookingLabels, 'bookingTooltips' => $bookingTooltips];
        }
        
        return $this->successResponse($data, 'Get booking options successfully');
    }
}
