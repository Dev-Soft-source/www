<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\ProfileSettingDetail;
use Illuminate\Http\Request;
use App\Traits\StatusResponser;

class ProfileSettingController extends Controller
{
    use StatusResponser;
    public function ProfilePageSettingIndex(Request $request)
    {
        $profileSettingPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $profileSettingPage = ProfileSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $profileSettingPage = ProfileSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['profileSettingPage' => $profileSettingPage];
        return $this->successResponse($data, 'Profile setting page get successfully');
    }
}
