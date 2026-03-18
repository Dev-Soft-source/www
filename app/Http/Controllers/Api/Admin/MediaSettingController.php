<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaSetting;
use App\Services\MediaSettingService;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class MediaSettingController extends Controller
{
    use StatusResponser;

    public function show()
    {
        $mediaSetting = MediaSetting::with(['mediaSettingDetail', 'mediaSettingDetail.language:id,name'])->first();

        return $this->successResponse(
            $mediaSetting ? $mediaSetting : [],
            'Data Get Successfully!'
        );
    }

    public function update(Request $request)
    {
        $validationRule = [];
        $errorMessages  = [];
        $languages      = getAllLanguages();

        $service  = new MediaSettingService();
        $response = $service->validation($languages, $validationRule, $errorMessages);

        $this->validate(
            $request,
            $response['validation_rules'],
            $response['error_messages'],
            $response['nice_names']
        );

        $mediaSetting = MediaSetting::first();
        if (!$mediaSetting) {
            $mediaSetting = MediaSetting::create([]);
        }

        foreach ($languages as $language) {
            $service->update($mediaSetting, $language, $request);
        }

        if ($mediaSetting) {
            return $this->successResponse([], 'Media page setting updated successfully.');
        }

        return $this->errorResponse();
    }
}

