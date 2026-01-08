<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\MobileFindRideSettingResource;
use App\Models\MobileFindRideSetting;
use App\Services\MobileFindRideSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class MobileFindRideSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $findRidePageSetting = MobileFindRideSetting::query();
        
        $findRidePageSetting = $findRidePageSetting->with(['mobileFindRideSettingDetail', 'mobileFindRideSettingDetail.language:id,name']);
        $findRidePageSetting = $findRidePageSetting->first();

        return $this->successResponse($findRidePageSetting ? new MobileFindRideSettingResource($findRidePageSetting) : [], 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new MobileFindRideSettingService();
        $response = $pageSettingService->validation($languages, $validationRule, $errorMessages);
        $validationRule = $response['validation_rules'];
        $errorMessages = $response['error_messages'];
        $niceNames = $response['nice_names'];

        $this->validate(
            $request,
            $validationRule,
            $errorMessages,
            $niceNames
        );

        $findRidePageSetting = MobileFindRideSetting::first();
        if (!$findRidePageSetting) {
            $findRidePageSetting = MobileFindRideSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($findRidePageSetting, $language, $request);
        }

        if ($findRidePageSetting) {
            return $this->successResponse([], "Find ride page setting updated successfully.");
        }

        return $this->errorResponse();
    }
}
