<?php

namespace App\Http\Controllers\Api\App;

use App\Models\Language;
use Illuminate\Http\Request;
use App\Traits\StatusResponser;
use App\Http\Controllers\Controller;
use App\Models\CloseAccountSettingDetail;
use App\Models\SuccessMessagesSettingDetail;

class CloseMyAccountSettingController extends Controller
{
    use StatusResponser;
    public function CloseAccountPageSettingIndex(Request $request)
    {
        $CloseAccountPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $CloseAccountPage = CloseAccountSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $CloseAccountPage = CloseAccountSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $messages = null;
        $selectedLanguage = app()->getLocale();
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                // Retrieve the HomePageSettingDetail associated with the selected language
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('select_reason', 'select_recommend', 'check_box')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $messages = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('select_reason', 'select_recommend', 'check_box')->first();
            }
        }

        $data = ['CloseAccountPage' => $CloseAccountPage, 'messages' => $messages];
        return $this->successResponse($data, 'Search close account get successfully');
    }
}
