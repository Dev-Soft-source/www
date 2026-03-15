<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ThankyouPageSettingResource;
use App\Models\ThankyouPageSetting;
use App\Services\ThankyouPageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Models\Language;

class ThankyouPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $thankyouPageSetting = ThankyouPageSetting::with(['thankyouPageSettingDetail', 'thankyouPageSettingDetail.language:id,name'])->first();
        if (!$thankyouPageSetting) {
            $thankyouPageSetting = ThankyouPageSetting::create([]);
            $thankyouPageSetting = ThankyouPageSetting::with(['thankyouPageSettingDetail', 'thankyouPageSettingDetail.language:id,name'])->find($thankyouPageSetting->id);
        }
        return $this->successResponse(new ThankyouPageSettingResource($thankyouPageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new ThankyouPageSettingService();
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

        $thankyouPageSetting = ThankyouPageSetting::first();
        if (!$thankyouPageSetting) {
            $thankyouPageSetting = ThankyouPageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($thankyouPageSetting, $language, $request);
        }

        if ($thankyouPageSetting) {
            return $this->successResponse([], "Thank you page settings updated successfully.");
        }

        return $this->errorResponse();
    }
}
