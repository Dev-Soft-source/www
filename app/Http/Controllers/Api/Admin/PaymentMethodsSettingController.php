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
use App\Imports\PaymentMethodsSettingImport;
use App\Exports\PaymentMethodsSettingTemplateExport;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class PaymentMethodsSettingController extends Controller
{
    use StatusResponser;

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        foreach ($languages as $language) {
            if ($language->is_default == '1') {
                $validationRule = array_merge($validationRule, ['payment_methods_option1.payment_methods_option1_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['payment_methods_option1.payment_methods_option1_' . $language->id . '.required' => 'Payment option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['payment_methods_option2.payment_methods_option2_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['payment_methods_option2.payment_methods_option2_' . $language->id . '.required' => 'Payment option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['payment_methods_option3.payment_methods_option3_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['payment_methods_option3.payment_methods_option3_' . $language->id . '.required' => 'Payment option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_option1.booking_option1_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_option1.booking_option1_' . $language->id . '.required' => 'Booking option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['booking_option2.booking_option2_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['booking_option2.booking_option2_' . $language->id . '.required' => 'Booking option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancellation_policy_label1.cancellation_policy_label1_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancellation_policy_label1.cancellation_policy_label1_' . $language->id . '.required' => 'Cancellation option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancellation_policy_label1_tooltip.cancellation_policy_label1_tooltip_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancellation_policy_label1_tooltip.cancellation_policy_label1_tooltip_' . $language->id . '.required' => 'Cancellation option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancellation_policy_label2.cancellation_policy_label2_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancellation_policy_label2.cancellation_policy_label2_' . $language->id . '.required' => 'Cancellation option in ' . $language->name . ' is required.']);
                $validationRule = array_merge($validationRule, ['cancellation_policy_label2_tooltip.cancellation_policy_label2_tooltip_' . $language->id => ['required', 'string']]);
                $errorMessages = array_merge($errorMessages, ['cancellation_policy_label2_tooltip.cancellation_policy_label2_tooltip_' . $language->id . '.required' => 'Cancellation option in ' . $language->name . ' is required.']);
            }
        }

        $this->validate(
            $request,
            $validationRule,
            $errorMessages,
        );

        $cancellationSetting1 = FeaturesSetting::whereSlug('standard')->first();
        if (!$cancellationSetting1) {
            $cancellationSetting1 = FeaturesSetting::create([
                'slug' => 'standard',
            ]);
        }
        foreach ($languages as $language) {
            $cancellationOption1 = FeaturesSettingDetail::whereFeaturesSettingId($cancellationSetting1->id)->whereLanguageId($language->id)->first();
            if ($cancellationOption1) {
                $cancellationOption1->update([
                    'name' => $request['cancellation_policy_label1']['cancellation_policy_label1_' . $language->id],
                ]);
            } else {
                $cancellationOption1 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $cancellationSetting1->id,
                    'name' => $request['cancellation_policy_label1']['cancellation_policy_label1_' . $language->id],
                ]);
            }
        }

        $cancellationSetting2 = FeaturesSetting::whereSlug('firm')->first();
        if (!$cancellationSetting2) {
            $cancellationSetting2 = FeaturesSetting::create([
                'slug' => 'firm',
            ]);
        }
        foreach ($languages as $language) {
            $cancellationOption2 = FeaturesSettingDetail::whereFeaturesSettingId($cancellationSetting2->id)->whereLanguageId($language->id)->first();
            if ($cancellationOption2) {
                $cancellationOption2->update([
                    'name' => $request['cancellation_policy_label2']['cancellation_policy_label2_' . $language->id],
                ]);
            } else {
                $cancellationOption2 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $cancellationSetting2->id,
                    'name' => $request['cancellation_policy_label2']['cancellation_policy_label2_' . $language->id],
                ]);
            }
        }

        $bookingSetting1 = FeaturesSetting::whereSlug('instant')->first();
        if (!$bookingSetting1) {
            $bookingSetting1 = FeaturesSetting::create([
                'slug' => 'instant',
            ]);
        }
        foreach ($languages as $language) {
            $bookingOption1 = FeaturesSettingDetail::whereFeaturesSettingId($bookingSetting1->id)->whereLanguageId($language->id)->first();
            if ($bookingOption1) {
                $bookingOption1->update([
                    'name' => $request['booking_option1']['booking_option1_' . $language->id],
                    'icon' => $request['booking_option1_icon']['booking_option1_icon_' . $language->id],
                ]);
            } else {
                $bookingOption1 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $bookingSetting1->id,
                    'name' => $request['booking_option1']['booking_option1_' . $language->id],
                    'icon' => $request['booking_option1_icon']['booking_option1_icon_' . $language->id],
                ]);
            }
        }

        $bookingSetting2 = FeaturesSetting::whereSlug('manual')->first();
        if (!$bookingSetting2) {
            $bookingSetting2 = FeaturesSetting::create([
                'slug' => 'manual',
            ]);
        }
        foreach ($languages as $language) {
            $bookingOption2 = FeaturesSettingDetail::whereFeaturesSettingId($bookingSetting2->id)->whereLanguageId($language->id)->first();
            if ($bookingOption2) {
                $bookingOption2->update([
                    'name' => $request['booking_option2']['booking_option2_' . $language->id],
                    'icon' => $request['booking_option2_icon']['booking_option2_icon_' . $language->id],
                ]);
            } else {
                $bookingOption2 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $bookingSetting2->id,
                    'name' => $request['booking_option2']['booking_option2_' . $language->id],
                    'icon' => $request['booking_option2_icon']['booking_option2_icon_' . $language->id],
                ]);
            }
        }

        $paymentSetting1 = FeaturesSetting::whereSlug('cash')->first();
        if (!$paymentSetting1) {
            $paymentSetting1 = FeaturesSetting::create([
                'slug' => 'cash',
            ]);
        }
        foreach ($languages as $language) {
            $paymentOption1 = FeaturesSettingDetail::whereFeaturesSettingId($paymentSetting1->id)->whereLanguageId($language->id)->first();
            if ($paymentOption1) {
                $paymentOption1->update([
                    'name' => $request['payment_methods_option1']['payment_methods_option1_' . $language->id],
                    'icon' => $request['payment_methods_option1_icon']['payment_methods_option1_icon_' . $language->id],
                ]);
            } else {
                $paymentOption1 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $paymentSetting1->id,
                    'name' => $request['payment_methods_option1']['payment_methods_option1_' . $language->id],
                    'icon' => $request['payment_methods_option1_icon']['payment_methods_option1_icon_' . $language->id],
                ]);
            }
        }

        $paymentSetting2 = FeaturesSetting::whereSlug('online')->first();
        if (!$paymentSetting2) {
            $paymentSetting2 = FeaturesSetting::create([
                'slug' => 'online',
            ]);
        }
        foreach ($languages as $language) {
            $paymentOption2 = FeaturesSettingDetail::whereFeaturesSettingId($paymentSetting2->id)->whereLanguageId($language->id)->first();
            if ($paymentOption2) {
                $paymentOption2->update([
                    'name' => $request['payment_methods_option2']['payment_methods_option2_' . $language->id],
                    'icon' => $request['payment_methods_option2_icon']['payment_methods_option2_icon_' . $language->id],
                ]);
            } else {
                $paymentOption2 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $paymentSetting2->id,
                    'name' => $request['payment_methods_option2']['payment_methods_option2_' . $language->id],
                    'icon' => $request['payment_methods_option2_icon']['payment_methods_option2_icon_' . $language->id],
                ]);
            }
        }

        $paymentSetting3 = FeaturesSetting::whereSlug('secured')->first();
        if (!$paymentSetting3) {
            $paymentSetting3 = FeaturesSetting::create([
                'slug' => 'secured',
            ]);
        }
        foreach ($languages as $language) {
            $paymentOption3 = FeaturesSettingDetail::whereFeaturesSettingId($paymentSetting3->id)->whereLanguageId($language->id)->first();
            if ($paymentOption3) {
                $paymentOption3->update([
                    'name' => $request['payment_methods_option3']['payment_methods_option3_' . $language->id],
                    'icon' => $request['payment_methods_option3_icon']['payment_methods_option3_icon_' . $language->id],
                ]);
            } else {
                $paymentOption3 = FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $paymentSetting3->id,
                    'name' => $request['payment_methods_option3']['payment_methods_option3_' . $language->id],
                    'icon' => $request['payment_methods_option2_icon']['payment_methods_option2_icon_' . $language->id],
                ]);
            }
        }
        $vehicleSettingConvertible = FeaturesSetting::whereSlug('convertible')->first();
        if (!$vehicleSettingConvertible) {
            $vehicleSettingConvertible = FeaturesSetting::create([
                'slug' => 'convertible',
            ]);
        }
        foreach ($languages as $language) {
            $vehicleOptionConvertible = FeaturesSettingDetail::whereFeaturesSettingId($vehicleSettingConvertible->id)
                                                            ->whereLanguageId($language->id)
                                                            ->first();

            if ($vehicleOptionConvertible) {
                $vehicleOptionConvertible->update([
                    'name' => $request['vehicle_type_convertible_text']['vehicle_type_convertible_text_' . $language->id],
                ]);
            } else {
                FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $vehicleSettingConvertible->id,
                    'name' => $request['vehicle_type_convertible_text']['vehicle_type_convertible_text_' . $language->id],
                ]);
            }
        }

        $vehicleSettingHatchback = FeaturesSetting::whereSlug('hatchback')->first();
        if (!$vehicleSettingHatchback) {
            $vehicleSettingHatchback = FeaturesSetting::create([
                'slug' => 'hatchback',
            ]);
        }
        foreach ($languages as $language) {
            $vehicleOptionHatchback = FeaturesSettingDetail::whereFeaturesSettingId($vehicleSettingHatchback->id)
                                                            ->whereLanguageId($language->id)
                                                            ->first();

            if ($vehicleOptionHatchback) {
                $vehicleOptionHatchback->update([
                    'name' => $request['vehicle_type_hatchback_text']['vehicle_type_hatchback_text_' . $language->id],
                ]);
            } else {
                FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $vehicleSettingHatchback->id,
                    'name' => $request['vehicle_type_hatchback_text']['vehicle_type_hatchback_text_' . $language->id],
                ]);
            }
        }

        $vehicleSettingCoupe = FeaturesSetting::whereSlug('coupe')->first();
        if (!$vehicleSettingCoupe) {
            $vehicleSettingCoupe = FeaturesSetting::create([
                'slug' => 'coupe',
            ]);
        }
        foreach ($languages as $language) {
            $vehicleOptionCoupe = FeaturesSettingDetail::whereFeaturesSettingId($vehicleSettingCoupe->id)
                                                        ->whereLanguageId($language->id)
                                                        ->first();

            if ($vehicleOptionCoupe) {
                $vehicleOptionCoupe->update([
                    'name' => $request['vehicle_type_coupe_text']['vehicle_type_coupe_text_' . $language->id],
                ]);
            } else {
                FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $vehicleSettingCoupe->id,
                    'name' => $request['vehicle_type_coupe_text']['vehicle_type_coupe_text_' . $language->id],
                ]);
            }
        }

        $vehicleSettingMinivan = FeaturesSetting::whereSlug('minivan')->first();
        if (!$vehicleSettingMinivan) {
            $vehicleSettingMinivan = FeaturesSetting::create([
                'slug' => 'minivan',
            ]);
        }
        foreach ($languages as $language) {
            $vehicleOptionMinivan = FeaturesSettingDetail::whereFeaturesSettingId($vehicleSettingMinivan->id)
                                                        ->whereLanguageId($language->id)
                                                        ->first();

            if ($vehicleOptionMinivan) {
                $vehicleOptionMinivan->update([
                    'name' => $request['vehicle_type_minivan_text']['vehicle_type_minivan_text_' . $language->id],
                ]);
            } else {
                FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $vehicleSettingMinivan->id,
                    'name' => $request['vehicle_type_minivan_text']['vehicle_type_minivan_text_' . $language->id],
                ]);
            }
        }

        $vehicleSettingSedan = FeaturesSetting::whereSlug('sedan')->first();
        if (!$vehicleSettingSedan) {
            $vehicleSettingSedan = FeaturesSetting::create([
                'slug' => 'sedan',
            ]);
        }
        foreach ($languages as $language) {
            $vehicleOptionSedan = FeaturesSettingDetail::whereFeaturesSettingId($vehicleSettingSedan->id)
                                                        ->whereLanguageId($language->id)
                                                        ->first();

            if ($vehicleOptionSedan) {
                $vehicleOptionSedan->update([
                    'name' => $request['vehicle_type_sedan_text']['vehicle_type_sedan_text_' . $language->id],
                ]);
            } else {
                FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $vehicleSettingSedan->id,
                    'name' => $request['vehicle_type_sedan_text']['vehicle_type_sedan_text_' . $language->id],
                ]);
            }
        }

        $vehicleSettingStationWagon = FeaturesSetting::whereSlug('station_wagon')->first();
        if (!$vehicleSettingStationWagon) {
            $vehicleSettingStationWagon = FeaturesSetting::create([
                'slug' => 'station_wagon',
            ]);
        }
        foreach ($languages as $language) {
            $vehicleOptionStationWagon = FeaturesSettingDetail::whereFeaturesSettingId($vehicleSettingStationWagon->id)
                                                            ->whereLanguageId($language->id)
                                                            ->first();

            if ($vehicleOptionStationWagon) {
                $vehicleOptionStationWagon->update([
                    'name' => $request['vehicle_type_station_wagon_text']['vehicle_type_station_wagon_text_' . $language->id],
                ]);
            } else {
                FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $vehicleSettingStationWagon->id,
                    'name' => $request['vehicle_type_station_wagon_text']['vehicle_type_station_wagon_text_' . $language->id],
                ]);
            }
        }

        $vehicleSettingSuv = FeaturesSetting::whereSlug('suv')->first();
        if (!$vehicleSettingSuv) {
            $vehicleSettingSuv = FeaturesSetting::create([
                'slug' => 'suv',
            ]);
        }
        foreach ($languages as $language) {
            $vehicleOptionSuv = FeaturesSettingDetail::whereFeaturesSettingId($vehicleSettingSuv->id)
                                                    ->whereLanguageId($language->id)
                                                    ->first();

            if ($vehicleOptionSuv) {
                $vehicleOptionSuv->update([
                    'name' => $request['vehicle_type_suv_text']['vehicle_type_suv_text_' . $language->id],
                ]);
            } else {
                FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $vehicleSettingSuv->id,
                    'name' => $request['vehicle_type_suv_text']['vehicle_type_suv_text_' . $language->id],
                ]);
            }
        }

        $vehicleSettingTruck = FeaturesSetting::whereSlug('truck')->first();
        if (!$vehicleSettingTruck) {
            $vehicleSettingTruck = FeaturesSetting::create([
                'slug' => 'truck',
            ]);
        }
        foreach ($languages as $language) {
            $vehicleOptionTruck = FeaturesSettingDetail::whereFeaturesSettingId($vehicleSettingTruck->id)
                                                    ->whereLanguageId($language->id)
                                                    ->first();

            if ($vehicleOptionTruck) {
                $vehicleOptionTruck->update([
                    'name' => $request['vehicle_type_truck_text']['vehicle_type_truck_text_' . $language->id],
                ]);
            } else {
                FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $vehicleSettingTruck->id,
                    'name' => $request['vehicle_type_truck_text']['vehicle_type_truck_text_' . $language->id],
                ]);
            }
        }

        $vehicleSettingVan = FeaturesSetting::whereSlug('van')->first();
        if (!$vehicleSettingVan) {
            $vehicleSettingVan = FeaturesSetting::create([
                'slug' => 'van',
            ]);
        }
        foreach ($languages as $language) {
            $vehicleOptionVan = FeaturesSettingDetail::whereFeaturesSettingId($vehicleSettingVan->id)
                                                    ->whereLanguageId($language->id)
                                                    ->first();

            if ($vehicleOptionVan) {
                $vehicleOptionVan->update([
                    'name' => $request['vehicle_type_van_text']['vehicle_type_van_text_' . $language->id],
                ]);
            } else {
                FeaturesSettingDetail::create([
                    'language_id' => $language->id,
                    'features_setting_id' => $vehicleSettingVan->id,
                    'name' => $request['vehicle_type_van_text']['vehicle_type_van_text_' . $language->id],
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
                    'cancellation_policy_label1' => $cancellationSetting1->id,
                    'cancellation_policy_label1_tooltip' => $request['cancellation_policy_label1_tooltip']['cancellation_policy_label1_tooltip_' . $language->id],
                    'cancellation_policy_label2' => $cancellationSetting2->id,
                    'cancellation_policy_label2_tooltip' => $request['cancellation_policy_label2_tooltip']['cancellation_policy_label2_tooltip_' . $language->id],
                    'booking_option1' => $bookingSetting1->id,
                    'booking_option1_tooltip' => $request['booking_option1_tooltip']['booking_option1_tooltip_' . $language->id],
                    'booking_option2' => $bookingSetting2->id,
                    'booking_option2_tooltip' => $request['booking_option2_tooltip']['booking_option2_tooltip_' . $language->id],
                    'payment_methods_option1' => $paymentSetting1->id,
                    'payment_methods_option1_tooltip' => $request['payment_methods_option1_tooltip']['payment_methods_option1_tooltip_' . $language->id],
                    'payment_methods_option2' => $paymentSetting2->id,
                    'payment_methods_option2_tooltip' => $request['payment_methods_option2_tooltip']['payment_methods_option2_tooltip_' . $language->id],
                    'payment_methods_option3' => $paymentSetting3->id,
                    'payment_methods_option3_tooltip' => $request['payment_methods_option3_tooltip']['payment_methods_option3_tooltip_' . $language->id],
    
                    'vehicle_type_convertible_text' => $vehicleSettingConvertible->id,
                    'vehicle_type_hatchback_text' => $vehicleSettingHatchback->id,
                    'vehicle_type_coupe_text' => $vehicleSettingCoupe->id,
                    'vehicle_type_minivan_text' => $vehicleSettingMinivan->id,
                    'vehicle_type_sedan_text' => $vehicleSettingSedan->id,
                    'vehicle_type_station_wagon_text' => $vehicleSettingStationWagon->id,
                    'vehicle_type_suv_text' => $vehicleSettingSuv->id,
                    'vehicle_type_truck_text' => $vehicleSettingTruck->id,
                    'vehicle_type_van_text' => $vehicleSettingVan->id,
                ]);
            } else {
                PostRidePageSettingDetail::whereLanguageId($language->id)->update([
                    'cancellation_policy_label1' => $cancellationSetting1->id,
                    'cancellation_policy_label1_tooltip' => $request['cancellation_policy_label1_tooltip']['cancellation_policy_label1_tooltip_' . $language->id],
                    'cancellation_policy_label2' => $cancellationSetting2->id,
                    'cancellation_policy_label2_tooltip' => $request['cancellation_policy_label2_tooltip']['cancellation_policy_label2_tooltip_' . $language->id],
                    'booking_option1' => $bookingSetting1->id,
                    'booking_option1_tooltip' => $request['booking_option1_tooltip']['booking_option1_tooltip_' . $language->id],
                    'booking_option2' => $bookingSetting2->id,
                    'booking_option2_tooltip' => $request['booking_option2_tooltip']['booking_option2_tooltip_' . $language->id],
                    'payment_methods_option1' => $paymentSetting1->id,
                    'payment_methods_option1_tooltip' => $request['payment_methods_option1_tooltip']['payment_methods_option1_tooltip_' . $language->id],
                    'payment_methods_option2' => $paymentSetting2->id,
                    'payment_methods_option2_tooltip' => $request['payment_methods_option2_tooltip']['payment_methods_option2_tooltip_' . $language->id],
                    'payment_methods_option3' => $paymentSetting3->id,
                    'payment_methods_option3_tooltip' => $request['payment_methods_option3_tooltip']['payment_methods_option3_tooltip_' . $language->id],
    
                    'vehicle_type_convertible_text' => $vehicleSettingConvertible->id,
                    'vehicle_type_hatchback_text' => $vehicleSettingHatchback->id,
                    'vehicle_type_coupe_text' => $vehicleSettingCoupe->id,
                    'vehicle_type_minivan_text' => $vehicleSettingMinivan->id,
                    'vehicle_type_sedan_text' => $vehicleSettingSedan->id,
                    'vehicle_type_station_wagon_text' => $vehicleSettingStationWagon->id,
                    'vehicle_type_suv_text' => $vehicleSettingSuv->id,
                    'vehicle_type_truck_text' => $vehicleSettingTruck->id,
                    'vehicle_type_van_text' => $vehicleSettingVan->id,
                ]);
            }
        }

        $findRidePageSetting = FindRidePageSetting::first();
        if (!$findRidePageSetting) {
            $findRidePageSetting = FindRidePageSetting::create([]);
        }
        foreach ($languages as $language) {
            FindRidePageSettingDetail::whereLanguageId($language->id)->update([
                'payment_methods_option2' => $paymentSetting1->id,
                'payment_methods_option3' => $paymentSetting2->id,
                'payment_methods_option4' => $paymentSetting3->id,
            ]);
        }

        if ($postRidePageSetting && $findRidePageSetting) {
            return $this->successResponse([], "Payment methods setting updated successfully.");
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
                Excel::import(new PaymentMethodsSettingImport($request->language_id), $request->file('excel_file'));
                return $this->successResponse(['language' => $language->name], "Payment methods settings for {$language->name} uploaded successfully from Excel.");
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
            Log::error('Payment Methods Excel upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload Excel file'], 500);
        }
    }

    public function downloadTemplate(Request $request)
    {
        try {
            return Excel::download(new PaymentMethodsSettingTemplateExport($request->get('format', 'single_column')),
                'payment_methods_settings_template_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Payment Methods template download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download template'], 500);
        }
    }
}
