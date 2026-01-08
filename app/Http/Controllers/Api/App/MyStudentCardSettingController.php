<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\MyStudentCardSettingDetail;
use Illuminate\Http\Request;
use App\Traits\StatusResponser;

class MyStudentCardSettingController extends Controller
{
    use StatusResponser;
    public function StudentPageSettingIndex(Request $request)
    {
        $studentCardPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $studentCardPage = MyStudentCardSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $studentCardPage = MyStudentCardSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $data = ['studentCardPage' => $studentCardPage];
        return $this->successResponse($data, 'Search my student card get successfully');
    }
}
