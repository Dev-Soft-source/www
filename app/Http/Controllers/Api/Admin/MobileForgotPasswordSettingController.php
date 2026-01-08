<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\MobileForgotPasswordSettingResource;
use App\Models\MobileForgotPasswordSetting;
use App\Services\MobileForgotPasswordSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class MobileForgotPasswordSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $forgotPasswordPageSetting = MobileForgotPasswordSetting::query();
        
        $forgotPasswordPageSetting = $forgotPasswordPageSetting->with(['mobileForgotPasswordSettingDetail', 'mobileForgotPasswordSettingDetail.language:id,name']);
        $forgotPasswordPageSetting = $forgotPasswordPageSetting->first();

        return $this->successResponse($forgotPasswordPageSetting ? new MobileForgotPasswordSettingResource($forgotPasswordPageSetting) : [], 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new MobileForgotPasswordSettingService();
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

        $forgotPasswordPageSetting = MobileForgotPasswordSetting::first();
        if (!$forgotPasswordPageSetting) {
            $forgotPasswordPageSetting = MobileForgotPasswordSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($forgotPasswordPageSetting, $language, $request);
        }

        if ($forgotPasswordPageSetting) {
            return $this->successResponse([], "Forgot password page setting updated successfully.");
        }

        return $this->errorResponse();
    }
}
