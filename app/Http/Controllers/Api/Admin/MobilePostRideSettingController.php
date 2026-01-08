<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\MobilePostRideSettingResource;
use App\Models\MobilePostRideSetting;
use App\Services\MobilePostRideSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class MobilePostRideSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $postRidePageSetting = MobilePostRideSetting::query();
        
        $postRidePageSetting = $postRidePageSetting->with(['mobilePostRideSettingDetail', 'mobilePostRideSettingDetail.language:id,name']);
        $postRidePageSetting = $postRidePageSetting->first();

        return $this->successResponse($postRidePageSetting ? new MobilePostRideSettingResource($postRidePageSetting) : [], 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new MobilePostRideSettingService();
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

        $postRidePageSetting = MobilePostRideSetting::first();
        if (!$postRidePageSetting) {
            $postRidePageSetting = MobilePostRideSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($postRidePageSetting, $language, $request);
        }

        if ($postRidePageSetting) {
            return $this->successResponse([], "Post ride page setting updated successfully.");
        }

        return $this->errorResponse();
    }
}
