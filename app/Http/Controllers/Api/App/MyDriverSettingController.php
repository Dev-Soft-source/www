<?php

namespace App\Http\Controllers\Api\App;

use App\Models\Language;
use Illuminate\Http\Request;
use App\Models\DriverSettingDetail;
use App\Http\Controllers\Controller;
use App\Traits\StatusResponser;

class MyDriverSettingController extends Controller
{
    use StatusResponser;
    public function DriverPageSettingIndex(Request $request)
    {
        $driverSettingPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $driverSettingPage = DriverSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $driverSettingPage = DriverSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['driverSettingPage' => $driverSettingPage];
        return $this->successResponse($data, 'Search my driver page get successfully');
    }
}
