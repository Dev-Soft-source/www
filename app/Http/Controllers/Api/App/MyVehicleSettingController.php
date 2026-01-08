<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\MyVehicleSettingDetail;
use Illuminate\Http\Request;
use App\Traits\StatusResponser;

class MyVehicleSettingController extends Controller
{
    use StatusResponser;
    public function VehicleSettingIndex(Request $request)
    {
        $vehicleSettingPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $vehicleSettingPage = MyVehicleSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $vehicleSettingPage = MyVehicleSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['vehicleSettingPage' => $vehicleSettingPage];
        return $this->successResponse($data, 'Search my vehcile page get successfully');
    }
}
