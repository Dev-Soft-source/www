<?php

namespace App\Http\Controllers\Api\App;

use App\Models\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MyPassengerSettingDetail;
use App\Traits\StatusResponser;

class MyPassengerSettingController extends Controller
{
    use StatusResponser;
    public function myPassengerPage(Request $request)
    {
        $myPassengerPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $myPassengerPage = MyPassengerSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $myPassengerPage = MyPassengerSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['myPassengerPage' => $myPassengerPage];
        return $this->successResponse($data, 'Search my passenger setting page get successfully');
    }
}
