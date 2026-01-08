<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\MyEmailSettingDetail;
use Illuminate\Http\Request;
use App\Traits\StatusResponser;

class MyEmailSettingController extends Controller
{
    use StatusResponser;
    public function EmailSettingIndex(Request $request)
    {
        $emailSettingPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $emailSettingPage = MyEmailSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $emailSettingPage = MyEmailSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $validationMessages = [
            'required' => trans('validation.required'),
            'email' => trans('validation.email'),
            'string' => trans('validation.string'),
            'unique' => trans('validation.unique'),
            'confirmed' => trans('validation.confirmed'),
        ];

        $data = ['emailSettingPage' => $emailSettingPage, 'validationMessages' => $validationMessages];
        return $this->successResponse($data, 'Search my email page get successfully');
    }
}
