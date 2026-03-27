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
        $bookingMethodOptions = collect($this->getRideFeatureOptionGroups($this->selectedLanguage->id)->get('booking_method', collect()))
            ->sortBy('id')
            ->values();

        $data = [
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
        
        return $this->successResponse($data, 'Get booking options successfully');
    }
}
