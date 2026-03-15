<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\NotificationsPageSettingResource;
use App\Models\NotificationsPageSetting;
use App\Services\NotificationsPageSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use App\Models\Language;

class NotificationsPageSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $notificationsPageSetting = NotificationsPageSetting::with(['notificationsPageSettingDetail', 'notificationsPageSettingDetail.language:id,name'])->first();
        if (!$notificationsPageSetting) {
            $notificationsPageSetting = NotificationsPageSetting::create([]);
            $notificationsPageSetting = NotificationsPageSetting::with(['notificationsPageSettingDetail', 'notificationsPageSettingDetail.language:id,name'])->find($notificationsPageSetting->id);
        }
        return $this->successResponse(new NotificationsPageSettingResource($notificationsPageSetting), 'Data Get Successfully!');
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $languages = getAllLanguages();

        $pageSettingService = new NotificationsPageSettingService();
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

        $notificationsPageSetting = NotificationsPageSetting::first();
        if (!$notificationsPageSetting) {
            $notificationsPageSetting = NotificationsPageSetting::create([]);
        }
        foreach ($languages as $language) {
            $pageSettingService->update($notificationsPageSetting, $language, $request);
        }

        if ($notificationsPageSetting) {
            return $this->successResponse([], "Notifications page settings updated successfully.");
        }

        return $this->errorResponse();
    }
}
