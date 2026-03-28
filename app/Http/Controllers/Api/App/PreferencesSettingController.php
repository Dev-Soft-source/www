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



    // for search ride
    public function getInitData(Request $request)
    {
        $groups = $this->getRideFeatureOptionGroups($this->selectedLanguage->id);
        
        $smokingOptions = collect($groups->get('smoking_allowed', collect()))
            ->sortBy('id')
            ->values();
        $petOptions = collect($groups->get('pets_allowed', collect()))
            ->sortBy('id')
            ->values();

        $data['preferencesOptions'] = [
                'smoking_option1' => $smokingOptions->get(0)?->features_setting_id,
                'smoking_option2' => $smokingOptions->get(1)?->features_setting_id,
                'smoking_option1_label' => $smokingOptions->get(0)?->name,
                'smoking_option2_label' => $smokingOptions->get(1)?->name,
                'animals_option1' => $petOptions->get(0)?->features_setting_id,
                'animals_option2' => $petOptions->get(1)?->features_setting_id,
                'animals_option3' => $petOptions->get(2)?->features_setting_id,
                'animals_option1_label' => $petOptions->get(0)?->name,
                'animals_option2_label' => $petOptions->get(1)?->name,
                'animals_option3_label' => $petOptions->get(2)?->name,
            ];

        $cancellationOptions = collect($groups->get('cancellation', collect()))
            ->sortBy('id')
            ->values();

        $data['cancellation'] = [
            'cancellationOptions' => $cancellationOptions
                ->pluck('features_setting_id')
                ->values()
                ->all(),
            'cancellationLabels' => $cancellationOptions
                ->pluck('name')
                ->values()
                ->all(),
            'cancellationTooltips' => $cancellationOptions
                ->pluck('tooltip')
                ->values()
                ->all(),
        ];

        $paymentOptions = collect($groups->get('payment_method', collect()))
            ->sortBy('id')
            ->values();

        $data['payment'] = [
            'paymentOptions' => $paymentOptions
                ->pluck('features_setting_id')
                ->values()
                ->all(),
            'paymentLabels' => $paymentOptions
                ->pluck('name')
                ->values()
                ->all(),
            'paymentTooltips' => $paymentOptions
                ->pluck('tooltip')
                ->values()
                ->all(),
        ];

        $luggageOptions = collect($groups->get('luggage_size', collect()))
            ->sortBy('id')
            ->values();

        $data['luggage'] = [
            'luggageOptions' => $luggageOptions
                ->pluck('features_setting_id')
                ->values()
                ->all(),
            'luggageLabels' => $luggageOptions
                ->pluck('name')
                ->values()
                ->all(),
            'luggageTooltips' => $luggageOptions
                ->pluck('tooltip')
                ->values()
                ->all(),
        ];

        $orderedFeatures = collect($groups->get('features', collect()))
            ->sortBy('id')
            ->filter(fn($feature) => ($feature->id >= 1 && $feature->id <= 12) || $feature->id == 47)
            ->values();
        
        $data['features'] = [
            'featuresOptions' => $orderedFeatures->pluck('features_setting_id')->values()->all(),
            'featuresLabels' => $orderedFeatures->pluck('name')->values()->all(),
        ];

        $orderedPassengers = collect($groups->get('features', collect()))
            ->sortBy('id')
            ->filter(fn($feature) => $feature->id >= 13 && $feature->id <= 16)
            ->values();

        $data['passengers'] = [
            'passengerRatingOptions' => $orderedPassengers->pluck('features_setting_id')->values()->all(),
            'passengerRatingLabels' => $orderedPassengers->pluck('name')->values()->all(),
        ];

        $bookingMethodOptions = collect($groups->get('booking_method', collect()))
            ->sortBy('id')
            ->values();

        $data['booking'] = [
            'bookingOptions' => $bookingMethodOptions
                ->pluck('features_setting_id')
                ->values()
                ->all(),
            'bookingLabels' => $bookingMethodOptions
                ->pluck('name')
                ->values()
                ->all(),
            'bookingTooltips' => $bookingMethodOptions
                ->pluck('tooltip')
                ->values()
                ->all(),
        ];


        return $this->successResponse($data, 'Get preferences options successfully');
    }

    public function preferencesOptions(Request $request)
    {

        $groups = $this->getRideFeatureOptionGroups($this->selectedLanguage->id);
        $smokingOptions = collect($groups->get('smoking_allowed', collect()))
            ->sortBy('id')
            ->values();
        $petOptions = collect($groups->get('pets_allowed', collect()))
            ->sortBy('id')
            ->values();

        return [
            'preferencesOptions' => [
                'smoking_option1' => $smokingOptions->get(0)?->features_setting_id,
                'smoking_option2' => $smokingOptions->get(1)?->features_setting_id,
                'smoking_option1_label' => $smokingOptions->get(0)?->name,
                'smoking_option2_label' => $smokingOptions->get(1)?->name,
                'animals_option1' => $petOptions->get(0)?->features_setting_id,
                'animals_option2' => $petOptions->get(1)?->features_setting_id,
                'animals_option3' => $petOptions->get(2)?->features_setting_id,
                'animals_option1_label' => $petOptions->get(0)?->name,
                'animals_option2_label' => $petOptions->get(1)?->name,
                'animals_option3_label' => $petOptions->get(2)?->name,
            ],
        ];

    }

    public function cancellationOptions(Request $request)
    {
        $cancellationOptions = collect($this->getRideFeatureOptionGroups($this->selectedLanguage->id)->get('cancellation', collect()))
            ->sortBy('id')
            ->values();

        $data = [
            'cancellationOptions' => $cancellationOptions
                ->pluck('features_setting_id')
                ->values()
                ->all(),
            'cancellationLabels' => $cancellationOptions
                ->pluck('name')
                ->values()
                ->all(),
            'cancellationTooltips' => $cancellationOptions
                ->pluck('tooltip')
                ->values()
                ->all(),
        ];

        return $this->successResponse($data, 'Get cancellation options successfully');
    }

    public function thankyouIndex(Request $request)
    {
        $thankYouPage = ThankyouPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $data = ['thankYouPage' => $thankYouPage];
        return $this->successResponse($data, 'Thankyou page get successfully');
    }
}
