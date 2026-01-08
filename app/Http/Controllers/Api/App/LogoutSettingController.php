<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\LogoutSettingDetail;
use Illuminate\Http\Request;
use App\Traits\StatusResponser;

class LogoutSettingController extends Controller
{
    use StatusResponser;
    public function LogoutPageSettingIndex(Request $request)
    {
        $logoutPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $logoutPage = LogoutSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $logoutPage = LogoutSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['logoutPage' => $logoutPage];
        return $this->successResponse($data, 'Search logout get successfully');
    }
}
