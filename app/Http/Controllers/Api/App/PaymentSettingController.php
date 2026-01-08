<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\FeaturesSettingDetail;
use App\Models\Language;
use App\Models\PostRidePageSettingDetail;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    use StatusResponser;

    public function index(Request $request){
        $paymentLabels = [];
        if ($request->lang && $request->lang != 0) {
            $paymentOptions = PostRidePageSettingDetail::select('post_ride_page_setting_detail.payment_methods_option1', 'post_ride_page_setting_detail.payment_methods_option2', 'post_ride_page_setting_detail.payment_methods_option3')
                ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
                ->where('languages.id', $request->lang)
                ->first();

            if ($paymentOptions->payment_methods_option1) {
                $name1 = FeaturesSettingDetail::whereFeaturesSettingId($paymentOptions->payment_methods_option1)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name1) {
                    $paymentLabels[] = $name1 ?? null;
                }
            } else {
                $paymentLabels[] = null; // Add `null` if payment_methods_option1 is null
            }

            if ($paymentOptions->payment_methods_option2) {
                $name2 = FeaturesSettingDetail::whereFeaturesSettingId($paymentOptions->payment_methods_option2)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name2) {
                    $paymentLabels[] = $name2 ?? null;
                }
            } else {
                $paymentLabels[] = null; // Add `null` if payment_methods_option1 is null
            }

            if ($paymentOptions->payment_methods_option3) {
                $name3 = FeaturesSettingDetail::whereFeaturesSettingId($paymentOptions->payment_methods_option3)
                    ->whereLanguageId($request->lang)
                    ->value('name');
                if ($name3) {
                    $paymentLabels[] = $name3 ?? null;
                }
            } else {
                $paymentLabels[] = null; // Add `null` if payment_methods_option1 is null
            }

            $paymentTooltips = PostRidePageSettingDetail::select('post_ride_page_setting_detail.payment_methods_option1_tooltip', 'post_ride_page_setting_detail.payment_methods_option2_tooltip', 'post_ride_page_setting_detail.payment_methods_option3_tooltip')
                ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
                ->where('languages.id', $request->lang)
                ->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $paymentOptions = PostRidePageSettingDetail::select('post_ride_page_setting_detail.payment_methods_option1', 'post_ride_page_setting_detail.payment_methods_option2', 'post_ride_page_setting_detail.payment_methods_option3')
                    ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
                    ->where('languages.id', $selectedLanguage->id)
                    ->first();

                if ($paymentOptions->payment_methods_option1) {
                    $name1 = FeaturesSettingDetail::whereFeaturesSettingId($paymentOptions->payment_methods_option1)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name1) {
                        $paymentLabels[] = $name1 ?? null;
                    }
                } else {
                    $paymentLabels[] = null; // Add `null` if payment_methods_option1 is null
                }
        
                if ($paymentOptions->payment_methods_option2) {
                    $name2 = FeaturesSettingDetail::whereFeaturesSettingId($paymentOptions->payment_methods_option2)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name2) {
                        $paymentLabels[] = $name2 ?? null;
                    }
                } else {
                    $paymentLabels[] = null; // Add `null` if payment_methods_option1 is null
                }
        
                if ($paymentOptions->payment_methods_option3) {
                    $name3 = FeaturesSettingDetail::whereFeaturesSettingId($paymentOptions->payment_methods_option3)
                        ->whereLanguageId($selectedLanguage->id)
                        ->value('name');
                    if ($name3) {
                        $paymentLabels[] = $name3 ?? null;
                    }
                } else {
                    $paymentLabels[] = null; // Add `null` if payment_methods_option1 is null
                }

                $paymentTooltips = PostRidePageSettingDetail::select('post_ride_page_setting_detail.payment_methods_option1_tooltip', 'post_ride_page_setting_detail.payment_methods_option2_tooltip', 'post_ride_page_setting_detail.payment_methods_option3_tooltip')
                    ->join('languages', 'languages.id', '=', 'post_ride_page_setting_detail.language_id')
                    ->where('languages.id', $selectedLanguage->id)
                    ->first();
            }
        }

        if ($paymentOptions && $paymentTooltips) {
            $data = ['paymentOptions' => array_values($paymentOptions->toArray()), 'paymentLabels' => $paymentLabels, 'paymentTooltips' => array_values($paymentTooltips->toArray())];
        } else {
            $data = ['paymentOptions' => $paymentOptions, 'paymentLabels' => $paymentLabels, 'paymentTooltips' => $paymentTooltips];
        }
        
        return $this->successResponse($data, 'Get payment options successfully');
    }
}
