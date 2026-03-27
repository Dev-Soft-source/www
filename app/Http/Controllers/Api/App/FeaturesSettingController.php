<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\FeaturesSettingDetail;
use App\Models\FindRidePageSettingDetail;
use App\Models\Language;
use App\Models\PostRidePageSettingDetail;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class FeaturesSettingController extends Controller
{
    use StatusResponser;

    public function postRideFeaturesOptions(Request $request){


        $featureGroup = $this->getRideFeatureOptionGroups($this->selectedLanguage->id)->get('features', collect());

        $orderedFeatures = collect($featureGroup)
            ->sortBy('id')
            ->filter(fn($feature) => $feature->id >= 1 && $feature->id <= 16 || 47)
            ->values();
        
        $data = [
            'featuresOptions' => $orderedFeatures->pluck('features_setting_id')->values()->all(),
            'featuresLabels' => $orderedFeatures->pluck('name')->values()->all(),
        ];

        
        return $this->successResponse($data, 'Get features options successfully');
    }
    
    public function findRideFeaturesOptions(Request $request){

        $featureGroup = $this->getRideFeatureOptionGroups($this->selectedLanguage->id)->get('features', collect());

        $orderedFeatures = collect($featureGroup)
            ->sortBy('id')
            ->filter(fn($feature) => $feature->id >= 1 && $feature->id <= 12 || 47)
            ->values();
        
        $orderedPassengers = collect($featureGroup)
            ->sortBy('id')
            ->filter(fn($feature) => $feature->id >= 13 && $feature->id <= 16)
            ->values();

        $data = [
            'featuresOptions' => $orderedFeatures->pluck('features_setting_id')->values()->all(),
            'featuresLabels' => $orderedFeatures->pluck('name')->values()->all(),
            
            'passengerRatingOptions' => $orderedPassengers->pluck('features_setting_id')->values()->all(),
            'passengerRatingLabels' => $orderedPassengers->pluck('name')->values()->all(),
        ];
        
        return $this->successResponse($data, 'Get features options successfully');
    }
}
