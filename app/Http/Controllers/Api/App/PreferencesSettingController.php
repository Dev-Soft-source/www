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
        return $this->successResponse(
            $this->buildSearchRideInitPreferencePayload(),
            'Get preferences options successfully'
        );
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
