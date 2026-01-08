<?php

namespace App\Http\Controllers\Api\App;

use App\Models\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PasswordSettingDetail;
use App\Traits\StatusResponser;

class PasswordSettingController extends Controller
{
    use StatusResponser;
    public function PasswordPageSettingIndex(Request $request)
    {
        $passwordSettingPage = null;
        if ($request->lang_id && $request->lang_id != 0) {
            $passwordSettingPage = PasswordSettingDetail::where('language_id', $request->lang_id)->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $passwordSettingPage = PasswordSettingDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }

        $validationMessages = [
            'required' => trans('validation.required'),
            'string' => trans('validation.string'),
            'min' => trans('validation.min.string'),
            'regex' => trans('validation.regex'),
            'confirmed' => trans('validation.confirmed'),
        ];

        $data = ['passwordSettingPage' => $passwordSettingPage, 'validationMessages' => $validationMessages];
        return $this->successResponse($data, 'Search password page get successfully');
    }
}
