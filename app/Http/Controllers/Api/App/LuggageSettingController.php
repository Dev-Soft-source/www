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
        $luggageOptions = collect($this->getRideFeatureOptionGroups($this->selectedLanguage->id)->get('luggage_size', collect()))
            ->sortBy('id')
            ->values();

        $data = [
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
        
        return $this->successResponse($data, 'Get luggage options successfully');
    }
}
