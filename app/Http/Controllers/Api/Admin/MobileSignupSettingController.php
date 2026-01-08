<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\MobileSignupSettingResource;
use App\Models\MobileSignupSetting;
use App\Services\MobileSignupSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class MobileSignupSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $signupPageSetting = MobileSignupSetting::query();
        
        $signupPageSetting = $signupPageSetting->with(['mobileSignupSettingDetail', 'mobileSignupSettingDetail.language:id,name']);
        $signupPageSetting = $signupPageSetting->first();

        return $this->successResponse($signupPageSetting ? new MobileSignupSettingResource($signupPageSetting) : [], 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new MobileSignupSettingService();
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

        $signupPageSetting = MobileSignupSetting::first();
        if (!$signupPageSetting) {
            $signupPageSetting = MobileSignupSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($signupPageSetting, $language, $request);
        }

        if ($signupPageSetting) {
            return $this->successResponse([], "Signup page setting updated successfully.");
        }

        return $this->errorResponse();
    }
}
