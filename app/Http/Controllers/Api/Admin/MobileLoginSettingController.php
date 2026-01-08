<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\MobileLoginSettingResource;
use App\Models\MobileLoginSetting;
use App\Services\MobileLoginSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class MobileLoginSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $loginPageSetting = MobileLoginSetting::query();
        
        $loginPageSetting = $loginPageSetting->with(['mobileLoginSettingDetail', 'mobileLoginSettingDetail.language:id,name']);
        $loginPageSetting = $loginPageSetting->first();

        return $this->successResponse($loginPageSetting ? new MobileLoginSettingResource($loginPageSetting) : [], 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new MobileLoginSettingService();
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

        $loginPageSetting = MobileLoginSetting::first();
        if (!$loginPageSetting) {
            $loginPageSetting = MobileLoginSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($loginPageSetting, $language, $request);
        }

        if ($loginPageSetting) {
            return $this->successResponse([], "Login page setting updated successfully.");
        }

        return $this->errorResponse();
    }
}
