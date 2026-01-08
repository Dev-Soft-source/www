<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ContactUsPageSettingResource;
use App\Models\ContactUsPageSetting;
use App\Services\ContactUsPageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class ContactUsPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $contactUsPageSetting = ContactUsPageSetting::query();

        // $defaultLang = getDefaultLanguage();
        // $contactUsPageSetting = $contactUsPageSetting->with(['contactUsPageSettingDetail' => function ($q) use ($defaultLang) {
        //     $q->where('language_id', $defaultLang->id);
        // }]);
        
        $contactUsPageSetting = $contactUsPageSetting->with(['contactUsPageSettingDetail', 'contactUsPageSettingDetail.language:id,name']);
        $contactUsPageSetting = $contactUsPageSetting->first();

        return $this->successResponse($contactUsPageSetting ? new ContactUsPageSettingResource($contactUsPageSetting) : [], 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new ContactUsPageSettingService();
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

        $contactUsPageSetting = ContactUsPageSetting::first();
        if (!$contactUsPageSetting) {
            $contactUsPageSetting = ContactUsPageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($contactUsPageSetting, $language, $request);
        }

        if ($contactUsPageSetting) {
            return $this->successResponse([], "Contact us page setting updated successfully.");
        }

        return $this->errorResponse();
    }
}
