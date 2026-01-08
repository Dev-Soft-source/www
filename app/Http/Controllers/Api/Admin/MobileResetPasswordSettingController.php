<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\MobileResetPasswordSettingResource;
use App\Models\MobileResetPasswordSetting;
use App\Services\MobileResetPasswordSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class MobileResetPasswordSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $resetPasswordPageSetting = MobileResetPasswordSetting::query();
        
        $resetPasswordPageSetting = $resetPasswordPageSetting->with(['mobileResetPasswordSettingDetail', 'mobileResetPasswordSettingDetail.language:id,name']);
        $resetPasswordPageSetting = $resetPasswordPageSetting->first();

        return $this->successResponse($resetPasswordPageSetting ? new MobileResetPasswordSettingResource($resetPasswordPageSetting) : [], 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new MobileResetPasswordSettingService();
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

        $resetPasswordPageSetting = MobileResetPasswordSetting::first();
        if (!$resetPasswordPageSetting) {
            $resetPasswordPageSetting = MobileResetPasswordSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($resetPasswordPageSetting, $language, $request);
        }

        if ($resetPasswordPageSetting) {
            return $this->successResponse([], "Reset password page setting updated successfully.");
        }

        return $this->errorResponse();
    }
}
