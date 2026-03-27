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
        
        $paymentOptions = collect($this->getRideFeatureOptionGroups($this->selectedLanguage->id)->get('payment_method', collect()))
            ->sortBy('id')
            ->values();

        $data = [
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
        
        return $this->successResponse($data, 'Get payment options successfully');
    }
}
