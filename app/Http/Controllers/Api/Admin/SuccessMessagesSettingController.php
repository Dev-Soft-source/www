<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\SuccessMessagesSettingResource;
use App\Models\SuccessMessagesSetting;
use App\Services\SuccessMessagesSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class SuccessMessagesSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $successMessagesSetting = SuccessMessagesSetting::query();
        
        $successMessagesSetting = $successMessagesSetting->with(['successMessagesSettingDetail', 'successMessagesSettingDetail.language:id,name']);
        $successMessagesSetting = $successMessagesSetting->first();

        return $this->successResponse($successMessagesSetting ? new SuccessMessagesSettingResource($successMessagesSetting) : [], 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new SuccessMessagesSettingService();
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

        $successMessagesSetting = SuccessMessagesSetting::first();
        if (!$successMessagesSetting) {
            $successMessagesSetting = SuccessMessagesSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($successMessagesSetting, $language, $request);
        }

        if ($successMessagesSetting) {
            return $this->successResponse([], "Success messages setting updated successfully.");
        }

        return $this->errorResponse();
    }
}
