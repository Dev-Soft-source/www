<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\MyPhoneSettingDetail;
use Illuminate\Http\Request;
use App\Traits\StatusResponser;

class MyPhoneSettingController extends Controller
{
    use StatusResponser;
    public function MyPhonePageSettingIndex(Request $request)
    {
        $phoneSettingPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $phoneSettingPage = MyPhoneSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $phoneSettingPage = MyPhoneSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['phoneSettingPage' => $phoneSettingPage];
        return $this->successResponse($data, 'Search my phone page get successfully');
    }
}
