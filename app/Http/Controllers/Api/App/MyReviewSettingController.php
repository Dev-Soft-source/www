<?php

namespace App\Http\Controllers\Api\App;

use App\Models\Language;
use Illuminate\Http\Request;
use App\Traits\StatusResponser;
use App\Models\ReviewSettingDetail;
use App\Http\Controllers\Controller;

class MyReviewSettingController extends Controller
{
    use StatusResponser;
    public function reviewSettingPage(Request $request)
    {
        $reviewSettingPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $reviewSettingPage = ReviewSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $reviewSettingPage = ReviewSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['reviewSettingPage' => $reviewSettingPage];
        return $this->successResponse($data, 'Search my phone page get successfully');
    }
}
